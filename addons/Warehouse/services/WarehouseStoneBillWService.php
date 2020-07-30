<?php

namespace addons\Warehouse\services;

use addons\Warehouse\common\enums\WarehouseIdEnum;
use Yii;
use common\components\Service;
use common\enums\AuditStatusEnum;
use addons\Warehouse\common\forms\WarehouseBillWForm;
use addons\Warehouse\common\forms\WarehouseStoneBillGoodsWForm;
use addons\Warehouse\common\models\WarehouseBillW;
use addons\Warehouse\common\models\WarehouseStone;
use addons\Warehouse\common\models\WarehouseStoneBill;
use addons\Warehouse\common\models\WarehouseStoneBillGoods;
use addons\Warehouse\common\models\WarehouseStoneBillGoodsW;
use addons\Warehouse\common\models\WarehouseStoneBillW;
use addons\Warehouse\common\enums\BillWStatusEnum;
use addons\Warehouse\common\enums\FinAuditStatusEnum;
use addons\Warehouse\common\enums\PandianAdjustEnum;
use addons\Warehouse\common\enums\PandianStatusEnum;
use addons\Warehouse\common\enums\StoneBillStatusEnum;
use addons\Warehouse\common\enums\StoneStatusEnum;
use common\enums\ConfirmEnum;
use yii\db\Exception;

/**
 * 盘点单
 * @package services\common
 * @author jianyan74 <751393839@qq.com>
 */
class WarehouseStoneBillWService extends Service
{
    /**
     * 创建盘点单
     * @param object $form
     */
    public function createBillW($form){
        if(false === $form->validate()) {
            throw new \Exception($this->getError($form));
        }
        $bill = new WarehouseStoneBill();
        $bill->attributes = $form->toArray();
        $bill->bill_status = StoneBillStatusEnum::SAVE;
        $bill->to_warehouse_id = WarehouseIdEnum::STONE;//石料库
        if(false === $bill->save() ) {
            throw new \Exception($this->getError($bill));
        }
        //批量创建单据明细
        $where = [
            //'warehouse_id'=>$bill->to_warehouse_id,
            'stone_type' => $form->stone_type,
        ];
        $goods_list = WarehouseStone::find()->where($where)->asArray()->all();
        $stock_weight = $should_grain = 0;
        $bill_goods_values = $bill_goods_keys = $ids = [];
        if(!empty($goods_list)) {
            foreach ($goods_list as $goods) {
                $ids[] = $goods['id'];
                $bill_goods = [
                    'bill_id'=>$bill->id,
                    'bill_type'=>$bill->bill_type,
                    'bill_no'=>$bill->bill_no,
                    'stone_sn'=>$goods['stone_sn'],
                    'stone_name'=>$goods['stone_name'],
                    'style_sn'=>$goods['style_sn'],
                    'stone_type'=>$goods['stone_type'],
                    'color' => $goods['stone_color'],
                    'clarity' => $goods['stone_clarity'],
                    'cut' => $goods['stone_cut'],
                    'polish' => $goods['stone_polish'],
                    'fluorescence' => $goods['stone_fluorescence'],
                    'symmetry' => $goods['stone_symmetry'],
                    'stone_num'=>$goods['stock_cnt'],
                    'stone_weight'=>$goods['stock_weight'],
                    'status'=> PandianStatusEnum::SAVE,
                ];
                $bill_goods_values[] = array_values($bill_goods);
                $should_grain = bcadd($should_grain, $goods['stock_cnt']);
                $stock_weight = bcadd($stock_weight, $goods['stock_weight'], 3);
                $bill_goods_keys = array_keys($bill_goods);
                if(count($bill_goods_values)>=10){
                    //导入明细
                    $result = Yii::$app->db->createCommand()->batchInsert(WarehouseStoneBillGoods::tableName(), $bill_goods_keys, $bill_goods_values)->execute();
                    if(!$result) {
                        throw new \Exception('导入单据明细失败1');
                    }
                    $bill_goods_values = [];
                }
            }
            if(!empty($bill_goods_values)){
                //导入明细
                $result = Yii::$app->db->createCommand()->batchInsert(WarehouseStoneBillGoods::tableName(), $bill_goods_keys, $bill_goods_values)->execute();
                if(!$result) {
                    throw new \Exception('导入单据明细失败2');
                }
            }
            //更新仓库所选材质货品 盘点中
            $execute_num = WarehouseStone::updateAll(['stone_status'=>StoneStatusEnum::IN_PANDIAN],['id'=>$ids,'stone_status'=>StoneStatusEnum::IN_STOCK]);
            if($execute_num <> count($ids)){
                throw new \Exception("货品改变状态数量与明细数量不一致");
            }
        }else{
            throw new \Exception('库存中未查到石料为['.\Yii::$app->attr->valueName($form->stone_type).']的盘点数据');
        }

        //同步盘点明细关系表
        $sql = "insert into ".WarehouseStoneBillGoodsW::tableName().'(id,adjust_status,status) select id,0,0 from '.WarehouseStoneBillGoods::tableName()." where bill_id=".$bill->id;
        $should_num = Yii::$app->db->createCommand($sql)->execute();
        if(false === $should_num) {
            throw new \Exception('导入单据明细失败2');
        }
        //盘点单附属表
        $billW = new WarehouseStoneBillW();
        $billW->id = $bill->id;
        $billW->stone_type = $form->stone_type;
        $billW->should_num = $should_num;
        $billW->should_grain = $should_grain;
        $billW->should_weight = $stock_weight;
        if(false === $billW->save()){
            throw new \Exception($this->getError($billW));
        }
        //更新应盘数量和总金额
        $this->billWSummary($bill->id);
        return $bill;
    }

    /**
     * 盘点商品操作
     * @param WarehouseBillWForm $form
     */
    public function pandianGoods($form)
    {
        //校验单据状态
        if ($form->bill_status != StoneBillStatusEnum::SAVE) {
            throw new \Exception("单据已盘点结束");
        }
        if(false === $form->validate()) {
            throw new \Exception($this->getError($form));
        }
        if(!$form->stone_sn) {
            throw new \Exception("石料编号不能为空");
        }
        if(!$form->stone_num) {
            throw new \Exception("石料粒数不能为空");
        }
        if(!$form->stone_weight) {
            throw new \Exception("石料重量不能为空");
        }
        $billGoods = WarehouseStoneBillGoods::find()->where(['stone_sn'=>$form->stone_sn,'bill_id'=>$form->id])->one();
        if($billGoods && $billGoods->status == PandianStatusEnum::NORMAL) {
            //已盘点且正常的忽略
            throw new \Exception("石料编号[{$form->stone_sn}]已盘点且正常");
        }
        $goods = WarehouseStone::find()->where(['stone_sn'=>$form->stone_sn])->one();
        if(empty($goods)) {
            throw new \Exception("[{$form->stone_sn}]石料编号不存在");
        }else{
            $modelW = WarehouseStoneBillW::findOne(['id'=>$form->id]);
            if($goods->stone_type != $modelW->stone_type){
                throw new \Exception("[{$form->stone_sn}]石料编号材质不对");
            }
        }
        if(!$billGoods) {
            $billGoods = new WarehouseStoneBillGoods();
            $billGoods->bill_id = $form->id;
            $billGoods->bill_no = $form->bill_no;
            $billGoods->bill_type = $form->bill_type;
            $billGoods->stone_sn = $goods->stone_sn;
            $billGoods->stone_name = $goods->stone_name;
            $billGoods->style_sn = $goods->style_sn;
            $billGoods->stone_type = $goods->stone_type;
            $billGoods->color = $goods->stone_color;
            $billGoods->clarity = $goods->stone_clarity;
            $billGoods->cut = $goods->stone_cut;
            $billGoods->polish = $goods->stone_polish;
            $billGoods->fluorescence = $goods->stone_fluorescence;
            $billGoods->symmetry = $goods->stone_symmetry;
            $billGoods->stone_num = $form->stone_num;
            $billGoods->stone_weight = $form->stone_weight;
            $billGoods->status = PandianStatusEnum::PROFIT;//盘盈
        }else {
            if($form->to_warehouse_id == $goods->warehouse_id
                && bccomp($billGoods->stone_num,$form->stone_num)==0
                && bccomp($billGoods->stone_weight,$form->stone_weight,3)==0) {
                $billGoods->status = PandianStatusEnum::NORMAL;//正常
            }elseif($form->to_warehouse_id != $goods->warehouse_id
                || bccomp($billGoods->stone_num,$form->stone_num)!=0
                || bccomp($billGoods->stone_weight,$form->stone_weight,3)!=0){
                $billGoods->status = PandianStatusEnum::LOSS;//盘亏
            }
        }
        //更多商品属性
        //............
        if(false === $billGoods->save()) {
            throw new \Exception($this->getError($billGoods));
        }
        $data = ['status'=>ConfirmEnum::YES,'actual_num'=>$form->stone_num,'actual_weight'=>$form->stone_weight,'fin_status'=>FinAuditStatusEnum::PENDING];
        WarehouseStoneBillGoodsW::updateAll($data,['id'=>$billGoods->id]);
        $this->billWSummary($form->id);
    }

    /**
     *  商品明细审核验证
     * @param object $form
     * @throws \Exception
     */
    public function auditGoodsValidate($form){
        $ids = $form->getIds();
        if(is_array($ids)){
            foreach ($ids as $id) {
                $goods = WarehouseStoneBillGoodsWForm::findOne(['id'=>$id]);
                if($goods->fin_status != FinAuditStatusEnum::PENDING){
                    $stone = WarehouseStoneBillGoods::findOne($id);
                    throw new \Exception("石料编号【{$stone->stone_sn}】不是待审核状态");
                }
            }
        }
    }

    /**
     * 财务盘点明细-审核
     * @param $form
     */
    public function auditFinW($form)
    {
        $ids = $form->getIds();
        if($ids && is_array($ids)){
            foreach ($ids as $id) {
                $goods = WarehouseStoneBillGoodsWForm::findOne($id);
                $goods->fin_status = $form->fin_status;
                $goods->adjust_status = $form->adjust_status;
                $goods->fin_remark = $form->fin_remark;
                $goods->fin_check_time = time();
                $goods->fin_checker = (string) \Yii::$app->user->identity->id;
                if(false === $goods->save()) {
                    throw new \Exception($this->getError($goods));
                }
            }
        }else {
            if (false === $form->validate()) {
                throw new \Exception($this->getError($form));
            }
            if ($form->fin_status != FinAuditStatusEnum::PASS) {
                $form->fin_status = FinAuditStatusEnum::UNPASS;
            }
            if (false === $form->save()) {
                throw new \Exception($this->getError($form));
            }
        }
    }

    /**
     * 盘点结束
     * @param WarehouseBillW $bill
     */
    public function finishBillW($bill_id)
    {
        $bill = WarehouseStoneBill::find()->where(['id'=>$bill_id])->one();
        if(!$bill || $bill->status == BillWStatusEnum::FINISHED) {
            throw new \Exception("盘点已结束");
        }
        $bill->status = BillWStatusEnum::FINISHED;
        $bill->bill_status = StoneBillStatusEnum::PENDING; //待审核
        if(false === $bill->save(false,['id','status', 'bill_status'])) {
            throw new \Exception($this->getError($bill));
        }
        //1.未盘点设为盘亏
        WarehouseStoneBillGoods::updateAll(['status'=>PandianStatusEnum::LOSS],['bill_id'=>$bill_id,'status'=>PandianStatusEnum::SAVE]);

        //2.解锁商品
        $subQuery = WarehouseStoneBillGoods::find()->select(['stone_sn'])->where(['bill_id'=>$bill->id]);
        WarehouseStone::updateAll(['stone_status'=>StoneStatusEnum::IN_STOCK],['stone_sn'=>$subQuery,'stone_status'=>StoneStatusEnum::IN_PANDIAN]);

        //3.解锁仓库
        \Yii::$app->warehouseService->warehouse->unlockWarehouse($bill->to_warehouse_id);

        //4.自动调整盘亏盘盈数据
        //$this->adjustGoods($bill_id);
        //5.盘点单汇总
        $this->billWSummary($bill_id);
    }

    /**
     * 盘点审核
     * @param WarehouseBillWForm $form
     */
    public function auditBillW($form)
    {
        if(false === $form->validate()) {
            throw new \Exception($this->getError($form));
        }
        /*$count = WarehouseStoneBillGoodsW::find()->where(['fin_status'=>FinAuditStatusEnum::PENDING])->count();
        if($count) {
            throw new \Exception("盘点明细还有财务未审核的商品，盘点不能审核");
        }*/
        $subQuery = WarehouseStoneBillGoods::find()->select(['stone_sn'])->where(['bill_id'=>$form->id]);
        if($form->audit_status == AuditStatusEnum::PASS) {
            $form->bill_status = StoneBillStatusEnum::CONFIRM;
            WarehouseStone::updateAll(['stone_status'=>StoneStatusEnum::IN_STOCK],['stone_sn'=>$subQuery,'stone_status'=>StoneStatusEnum::IN_PANDIAN]);
            //解锁仓库
            \Yii::$app->warehouseService->warehouse->unlockWarehouse($form->to_warehouse_id);
        }else {
            $form->bill_status = StoneBillStatusEnum::CANCEL;
            WarehouseStone::updateAll(['stone_status'=>StoneStatusEnum::IN_STOCK],['stone_sn'=>$subQuery,'stone_status'=>StoneStatusEnum::IN_PANDIAN]);
        }
        if(false === $form->save() ){
            throw new \Exception($this->getError($form));
        }
    }

    /**
     * 盘点单汇总
     * @param int $bill_id
     */
    public function billWSummary($bill_id)
    {
        $sum = WarehouseStoneBillGoods::find()->alias("g")->innerJoin(WarehouseStoneBillGoodsW::tableName().' gw','g.id=gw.id')
            ->select(['sum(if(gw.status='.ConfirmEnum::YES.',1,0)) as actual_num',
                'sum(if(gw.status='.ConfirmEnum::YES.',gw.actual_weight,0)) as actual_weight',
                'sum(if(gw.status='.ConfirmEnum::YES.',gw.actual_num,0)) as actual_grain',
                'sum(if(g.status='.PandianStatusEnum::PROFIT.',1,0)) as profit_num',
                'sum(if(g.status='.PandianStatusEnum::PROFIT.',g.stone_weight,0)) as profit_weight',
                'sum(if(g.status='.PandianStatusEnum::PROFIT.',g.stone_num,0)) as profit_grain',
                'sum(if(g.status='.PandianStatusEnum::LOSS.',1,0)) as loss_num',
                'sum(if(g.status='.PandianStatusEnum::LOSS.',g.stone_weight,0)) as loss_weight',
                'sum(if(g.status='.PandianStatusEnum::LOSS.',g.stone_num,0)) as loss_grain',
                'sum(if(g.status='.PandianStatusEnum::SAVE.',1,0)) as save_num',
                'sum(if(g.status='.PandianStatusEnum::SAVE.',g.stone_weight,0)) as save_weight',
                'sum(if(g.status='.PandianStatusEnum::SAVE.',g.stone_num,0)) as save_grain',
                'sum(if(g.status='.PandianStatusEnum::NORMAL.',1,0)) as normal_num',
                'sum(if(g.status='.PandianStatusEnum::NORMAL.',g.stone_weight,0)) as normal_weight',
                'sum(if(g.status='.PandianStatusEnum::NORMAL.',g.stone_num,0)) as normal_grain',
                'sum(if(gw.adjust_status>'.PandianAdjustEnum::SAVE.',1,0)) as adjust_num',
                'sum(if(gw.adjust_status>'.PandianAdjustEnum::SAVE.',g.stone_weight,0)) as adjust_weight',
                'sum(if(gw.adjust_status>'.PandianAdjustEnum::SAVE.',g.stone_num,0)) as adjust_grain',
                'sum(1) as goods_num',//明细总数量
                'sum(g.stone_weight) as goods_weight',//明细总重量
                'sum(g.stone_num) as goods_grain',//明细总粒数
                'sum(IFNULL(g.cost_price,0)) as total_cost',
            ])->where(['g.bill_id'=>$bill_id])->asArray()->one();
        if($sum) {
            $billUpdate = ['total_num'=>$sum['goods_num'], 'total_weight'=>$sum['goods_weight'], 'total_grain'=>$sum['goods_grain']];
            $billWUpdate = [
                'save_num'=>$sum['save_num'],'actual_num'=>$sum['actual_num'], 'loss_num'=>$sum['loss_num'], 'normal_num'=>$sum['normal_num'], 'adjust_num'=>$sum['adjust_num'],
                'save_weight'=>$sum['save_weight'],'actual_weight'=>$sum['actual_weight'], 'loss_weight'=>$sum['loss_weight'], 'normal_weight'=>$sum['normal_weight'], 'adjust_weight'=>$sum['adjust_weight'],
                'save_grain'=>$sum['save_grain'],'actual_grain'=>$sum['actual_grain'], 'loss_grain'=>$sum['loss_grain'], 'normal_grain'=>$sum['normal_grain'], 'adjust_grain'=>$sum['adjust_grain'],
            ];
            $res1 = WarehouseStoneBill::updateAll($billUpdate,['id'=>$bill_id]);
            $res2 = WarehouseStoneBillW::updateAll($billWUpdate,['id'=>$bill_id]);
            return $res1 && $res2;
        }
        return false;
    }

}
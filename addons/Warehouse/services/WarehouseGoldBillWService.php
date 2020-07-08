<?php

namespace addons\Warehouse\services;

use addons\Warehouse\common\enums\WarehouseIdEnum;
use addons\Warehouse\common\forms\WarehouseGoldBillGoodsWForm;
use addons\Warehouse\common\models\WarehouseStoneBillGoodsW;
use Yii;
use addons\Warehouse\common\forms\WarehouseBillWForm;
use addons\Warehouse\common\models\WarehouseGold;
use addons\Warehouse\common\models\WarehouseGoldBill;
use addons\Warehouse\common\models\WarehouseGoldBillW;
use addons\Warehouse\common\models\WarehouseGoldBillGoodsW;
use addons\Warehouse\common\models\WarehouseGoldBillGoods;
use addons\Warehouse\common\enums\BillStatusEnum;
use addons\Warehouse\common\enums\BillWStatusEnum;
use addons\Warehouse\common\enums\PandianAdjustEnum;
use addons\Warehouse\common\enums\PandianStatusEnum;
use addons\Warehouse\common\enums\GoldBillStatusEnum;
use addons\Warehouse\common\enums\FinAuditStatusEnum;
use addons\Warehouse\common\enums\GoldStatusEnum;
use common\enums\AuditStatusEnum;
use common\enums\ConfirmEnum;
use yii\db\Exception;

/**
 * 盘点单
 * @package services\common
 * @author jianyan74 <751393839@qq.com>
 */
class WarehouseGoldBillWService extends WarehouseBillService
{
    /**
     * 创建盘点单
     * @param object $form
     */
    public function createBillW($form){
        if(false === $form->validate()) {
            throw new \Exception($this->getError($form));
        }
        $bill = new WarehouseGoldBill();
        $bill->attributes = $form->toArray();
        $bill->bill_status = BillStatusEnum::SAVE;
        $bill->to_warehouse_id = WarehouseIdEnum::GOLD;//金料库
        if(false === $bill->save() ) {
            throw new \Exception($this->getError($bill));
        }
        //批量创建单据明细
        $where = [
            //'warehouse_id'=>$bill->to_warehouse_id,
            'gold_type' => $form->gold_type
        ];
        $goods_list = WarehouseGold::find()->where($where)->asArray()->all();
        $gold_weight = 0;
        $bill_goods_values = $bill_goods_keys = $ids = [];
        if(!empty($goods_list)) {
            foreach ($goods_list as $goods) {
                $ids[] = $goods['id'];
                $bill_goods = [
                    'bill_id'=>$bill->id,
                    'bill_type'=>$bill->bill_type,
                    'bill_no'=>$bill->bill_no,
                    'gold_sn'=>$goods['gold_sn'],
                    'gold_name'=>$goods['gold_name'],
                    'style_sn'=>$goods['style_sn'],
                    'gold_type'=>$goods['gold_type'],
                    'gold_num'=>$goods['gold_num'],
                    'gold_weight'=>$goods['gold_weight'],
                    'status'=> PandianStatusEnum::SAVE,
                ];
                $bill_goods_values[] = array_values($bill_goods);
                $gold_weight = bcadd($gold_weight, $goods['gold_weight'], 3);
                $bill_goods_keys = array_keys($bill_goods);
                if(count($bill_goods_values)>=10){
                    //导入明细
                    $result = Yii::$app->db->createCommand()->batchInsert(WarehouseGoldBillGoods::tableName(), $bill_goods_keys, $bill_goods_values)->execute();
                    if(!$result) {
                        throw new \Exception('导入单据明细失败1');
                    }
                    $bill_goods_values = [];
                }
            }
            if(!empty($bill_goods_values)){
                //导入明细
                $result = Yii::$app->db->createCommand()->batchInsert(WarehouseGoldBillGoods::tableName(), $bill_goods_keys, $bill_goods_values)->execute();
                if(!$result) {
                    throw new \Exception('导入单据明细失败2');
                }
            }
            //更新仓库所选材质货品 盘点中
            $execute_num = WarehouseGold::updateAll(['gold_status'=>GoldStatusEnum::IN_PANDIAN],['id'=>$ids,'gold_status'=>GoldStatusEnum::IN_STOCK]);
            if($execute_num <> count($ids)){
                throw new \Exception("货品改变状态数量与明细数量不一致");
            }
        }else{
            throw new \Exception('库存中未查到材质为['.\Yii::$app->attr->valueName($form->gold_type).']的盘点数据');
        }

        //同步盘点明细关系表
        $sql = "insert into ".WarehouseGoldBillGoodsW::tableName().'(id,adjust_status,status) select id,0,0 from '.WarehouseGoldBillGoods::tableName()." where bill_id=".$bill->id;
        $should_num = Yii::$app->db->createCommand($sql)->execute();
        if(false === $should_num) {
            throw new \Exception('导入单据明细失败2');
        }
        //盘点单附属表
        $billW = new WarehouseGoldBillW();
        $billW->id = $bill->id;
        $billW->gold_type = $form->gold_type;
        $billW->should_num = $should_num;
        $billW->should_weight = $gold_weight;
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
        if ($form->bill_status != GoldBillStatusEnum::SAVE) {
            throw new \Exception("单据已盘点结束");
        }
        if(false === $form->validate()) {
            throw new \Exception($this->getError($form));
        }
        if(!$form->gold_sn) {
            throw new \Exception("批次号不能为空");
        }
        if(!$form->gold_weight) {
            throw new \Exception("金料总重不能为空");
        }
        $billGoods = WarehouseGoldBillGoods::find()->where(['gold_sn'=>$form->gold_sn,'bill_id'=>$form->id])->one();
        if($billGoods && $billGoods->status == PandianStatusEnum::NORMAL) {
            //已盘点且正常的忽略
            throw new \Exception("批次号[{$form->gold_sn}]已盘点且正常");
        }
        $goods = WarehouseGold::find()->where(['gold_sn'=>$form->gold_sn])->one();
        if(empty($goods)) {
            throw new \Exception("[{$form->gold_sn}]批次号不存在");
        }else{
            $modelW = WarehouseGoldBillW::findOne(['id'=>$form->id]);
            if($goods->gold_type != $modelW->gold_type){
                throw new \Exception("[{$form->gold_sn}]批次号材质不对");
            }
        }
        if(!$billGoods) {
            $billGoods = new WarehouseGoldBillGoods();
            $billGoods->bill_id = $form->id;
            $billGoods->bill_no = $form->bill_no;
            $billGoods->bill_type = $form->bill_type;
            $billGoods->gold_sn = $goods->gold_sn;
            $billGoods->gold_name = $goods->gold_name;
            $billGoods->style_sn = $goods->style_sn;
            $billGoods->gold_type = $goods->gold_type;
            $billGoods->gold_weight = $form->gold_weight;
            $billGoods->status = PandianStatusEnum::PROFIT;//盘盈
        }else {
            if($form->to_warehouse_id == $goods->warehouse_id
                && bccomp($billGoods->gold_weight,$form->gold_weight,3)==0) {
                $billGoods->status = PandianStatusEnum::NORMAL;//正常
            }elseif($form->to_warehouse_id != $goods->warehouse_id
                || bccomp($billGoods->gold_weight,$form->gold_weight,3)!=0){
                $billGoods->status = PandianStatusEnum::LOSS;//盘亏
            }
        }
        //更多商品属性
        //............
        if(false === $billGoods->save()) {
            throw new \Exception($this->getError($billGoods));
        }
        $data = ['status'=>ConfirmEnum::YES,'actual_weight'=>$form->gold_weight,'fin_status'=>FinAuditStatusEnum::PENDING];
        WarehouseGoldBillGoodsW::updateAll($data,['id'=>$billGoods->id]);
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
                $goods = WarehouseGoldBillGoodsWForm::findOne(['id'=>$id]);
                if($goods->fin_status != FinAuditStatusEnum::PENDING){
                    $gold = WarehouseGoldBillGoods::findOne($id);
                    throw new \Exception("金料编号【{$gold->gold_sn}】不是待审核状态");
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
                $goods = WarehouseGoldBillGoodsWForm::findOne($id);
                $goods->fin_status = $form->fin_status;
                $goods->adjust_status = $form->adjust_status;
                $goods->fin_remark = $form->fin_remark;
                $goods->fin_check_time = time();
                $goods->fin_checker = (string) \Yii::$app->user->identity->id;
                if(false === $goods->save()) {
                    throw new \Exception($this->getError($goods));
                }
            }
        }else{
            if(false === $form->validate()) {
                throw new \Exception($this->getError($form));
            }

            if($form->fin_status != FinAuditStatusEnum::PASS){
                $form->fin_status = FinAuditStatusEnum::UNPASS;
            }
            if(false === $form->save()) {
                throw new \Exception($this->getError($form));
            }
        }
    }

    /**
     * 盘点结束
     * @param WarehouseGoldBill $bill
     */
    public function finishBillW($bill_id)
    {
        $bill = WarehouseGoldBill::find()->where(['id'=>$bill_id])->one();
        if(!$bill || $bill->status == BillWStatusEnum::FINISHED) {
            throw new \Exception("盘点已结束");
        }
        $bill->status = BillWStatusEnum::FINISHED;
        $bill->bill_status = BillStatusEnum::PENDING; //待审核
        if(false === $bill->save(false,['id','status', 'bill_status'])) {
            throw new \Exception($this->getError($bill));
        }
        //1.未盘点设为盘亏
        WarehouseGoldBillGoods::updateAll(['status'=>PandianStatusEnum::LOSS],['bill_id'=>$bill_id,'status'=>PandianStatusEnum::SAVE]);

        //2.解锁商品
        $subQuery = WarehouseGoldBillGoods::find()->select(['gold_sn'])->where(['bill_id'=>$bill->id]);
        WarehouseGold::updateAll(['gold_status'=>GoldStatusEnum::IN_STOCK],['gold_sn'=>$subQuery,'gold_status'=>GoldStatusEnum::IN_PANDIAN]);

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
        $subQuery = WarehouseGoldBillGoods::find()->select(['gold_sn'])->where(['bill_id'=>$form->id]);
        if($form->audit_status == AuditStatusEnum::PASS) {
            $form->bill_status = GoldBillStatusEnum::CONFIRM;
            WarehouseGold::updateAll(['gold_status'=>GoldStatusEnum::IN_STOCK],['gold_sn'=>$subQuery,'gold_status'=>GoldStatusEnum::IN_PANDIAN]);
            //解锁仓库
            \Yii::$app->warehouseService->warehouse->unlockWarehouse($form->to_warehouse_id);
        }else {
            $form->bill_status = GoldBillStatusEnum::CANCEL;
            WarehouseGold::updateAll(['gold_status'=>GoldStatusEnum::IN_STOCK],['gold_sn'=>$subQuery,'gold_status'=>GoldStatusEnum::IN_PANDIAN]);
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
        $sum = WarehouseGoldBillGoods::find()->alias("g")->innerJoin(WarehouseGoldBillGoodsW::tableName().' gw','g.id=gw.id')
            ->select(['sum(if(gw.status='.ConfirmEnum::YES.',1,0)) as actual_num',
                'sum(if(gw.status='.ConfirmEnum::YES.',gw.actual_weight,0)) as actual_weight',
                'sum(if(g.status='.PandianStatusEnum::PROFIT.',1,0)) as profit_num',
                'sum(if(g.status='.PandianStatusEnum::PROFIT.',g.gold_weight,0)) as profit_weight',
                'sum(if(g.status='.PandianStatusEnum::LOSS.',1,0)) as loss_num',
                'sum(if(g.status='.PandianStatusEnum::LOSS.',g.gold_weight,0)) as loss_weight',
                'sum(if(g.status='.PandianStatusEnum::SAVE.',1,0)) as save_num',
                'sum(if(g.status='.PandianStatusEnum::SAVE.',g.gold_weight,0)) as save_weight',
                'sum(if(g.status='.PandianStatusEnum::NORMAL.',1,0)) as normal_num',
                'sum(if(g.status='.PandianStatusEnum::NORMAL.',g.gold_weight,0)) as normal_weight',
                'sum(if(gw.adjust_status>'.PandianAdjustEnum::SAVE.',1,0)) as adjust_num',
                'sum(if(gw.adjust_status>'.PandianAdjustEnum::SAVE.',g.gold_weight,0)) as adjust_weight',
                'sum(1) as goods_num',//明细总数量
                'sum(IFNULL(g.cost_price,0)) as total_cost',
            ])->where(['g.bill_id'=>$bill_id])->asArray()->one();
        if($sum) {
            $billUpdate = ['total_num'=>$sum['goods_num']];
            $billWUpdate = [
                'save_num'=>$sum['save_num'],'actual_num'=>$sum['actual_num'], 'loss_num'=>$sum['loss_num'], 'normal_num'=>$sum['normal_num'], 'adjust_num'=>$sum['adjust_num'],
                'save_weight'=>$sum['save_weight'],'actual_weight'=>$sum['actual_weight'], 'loss_weight'=>$sum['loss_weight'], 'normal_weight'=>$sum['normal_weight'], 'adjust_weight'=>$sum['adjust_weight']
            ];
            $res1 = WarehouseGoldBill::updateAll($billUpdate,['id'=>$bill_id]);
            $res2 = WarehouseGoldBillW::updateAll($billWUpdate,['id'=>$bill_id]);
            return $res1 && $res2;
        }
        return false;
    }
}
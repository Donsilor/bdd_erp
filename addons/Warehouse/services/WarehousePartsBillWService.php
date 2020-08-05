<?php

namespace addons\Warehouse\services;

use Yii;
use common\components\Service;
use common\enums\AuditStatusEnum;
use addons\Warehouse\common\forms\WarehouseBillWForm;
use addons\Warehouse\common\forms\WarehousePartsBillGoodsWForm;
use addons\Warehouse\common\models\WarehouseBillW;
use addons\Warehouse\common\models\WarehouseParts;
use addons\Warehouse\common\models\WarehousePartsBill;
use addons\Warehouse\common\models\WarehousePartsBillGoods;
use addons\Warehouse\common\models\WarehousePartsBillGoodsW;
use addons\Warehouse\common\models\WarehousePartsBillW;
use addons\Warehouse\common\enums\BillWStatusEnum;
use addons\Warehouse\common\enums\FinAuditStatusEnum;
use addons\Warehouse\common\enums\PandianAdjustEnum;
use addons\Warehouse\common\enums\PandianStatusEnum;
use addons\Warehouse\common\enums\PartsBillStatusEnum;
use addons\Warehouse\common\enums\PartsStatusEnum;
use addons\Warehouse\common\enums\WarehouseIdEnum;
use common\enums\ConfirmEnum;
use yii\db\Exception;

/**
 * 盘点单
 * @package services\common
 * @author jianyan74 <751393839@qq.com>
 */
class WarehousePartsBillWService extends Service
{
    /**
     * 创建盘点单
     * @param object $form
     * @throws
     */
    public function createBillW($form){
        if(false === $form->validate()) {
            throw new \Exception($this->getError($form));
        }
        $bill = new WarehousePartsBill();
        $bill->attributes = $form->toArray();
        $bill->bill_status = PartsBillStatusEnum::SAVE;
        $bill->to_warehouse_id = WarehouseIdEnum::PARTS;//配件库
        if(false === $bill->save() ) {
            throw new \Exception($this->getError($bill));
        }
        //批量创建单据明细
        $where = [
            //'warehouse_id'=>$bill->to_warehouse_id,
            'parts_type' => $form->parts_type,
        ];
        $goods_list = WarehouseParts::find()->where($where)->asArray()->all();
        $stock_weight = $should_grain = 0;
        $bill_goods_values = $bill_goods_keys = $ids = [];
        if(!empty($goods_list)) {
            foreach ($goods_list as $goods) {
                $ids[] = $goods['id'];
                $bill_goods = [
                    'bill_id'=>$bill->id,
                    'bill_type'=>$bill->bill_type,
                    'bill_no'=>$bill->bill_no,
                    'parts_sn'=>$goods['parts_sn'],
                    'parts_name'=>$goods['parts_name'],
                    'style_sn'=>$goods['style_sn'],
                    'parts_type'=>$goods['parts_type'],
                    'color' => $goods['color'],
                    'shape' => $goods['shape'],
                    'size' => $goods['size'],
                    'chain_type' => $goods['chain_type'],
                    'cramp_ring' => $goods['cramp_ring'],
                    'parts_num'=>$goods['parts_num'],
                    'parts_weight'=>$goods['parts_weight'],
                    'status'=> PandianStatusEnum::SAVE,
                ];
                $bill_goods_values[] = array_values($bill_goods);
                $should_grain = bcadd($should_grain, $goods['parts_num']);
                $stock_weight = bcadd($stock_weight, $goods['parts_weight'], 3);
                $bill_goods_keys = array_keys($bill_goods);
                if(count($bill_goods_values)>=10){
                    //导入明细
                    $result = Yii::$app->db->createCommand()->batchInsert(WarehousePartsBillGoods::tableName(), $bill_goods_keys, $bill_goods_values)->execute();
                    if(!$result) {
                        throw new \Exception('导入单据明细失败1');
                    }
                    $bill_goods_values = [];
                }
            }
            if(!empty($bill_goods_values)){
                //导入明细
                $result = Yii::$app->db->createCommand()->batchInsert(WarehousePartsBillGoods::tableName(), $bill_goods_keys, $bill_goods_values)->execute();
                if(!$result) {
                    throw new \Exception('导入单据明细失败2');
                }
            }
            //更新仓库所选材质货品 盘点中
            $execute_num = WarehouseParts::updateAll(['parts_status'=>PartsStatusEnum::IN_PANDIAN],['id'=>$ids,'parts_status'=>PartsStatusEnum::IN_STOCK]);
            if($execute_num <> count($ids)){
                throw new \Exception("货品改变状态数量与明细数量不一致");
            }
        }else{
            throw new \Exception('库存中未查到配件为['.\Yii::$app->attr->valueName($form->parts_type).']的盘点数据');
        }
        //同步盘点明细关系表
        $sql = "insert into ".WarehousePartsBillGoodsW::tableName().'(id,adjust_status,status) select id,0,0 from '.WarehousePartsBillGoods::tableName()." where bill_id=".$bill->id;
        $should_num = Yii::$app->db->createCommand($sql)->execute();
        if(false === $should_num) {
            throw new \Exception('导入单据明细失败2');
        }
        //盘点单附属表
        $billW = new WarehousePartsBillW();
        $billW->id = $bill->id;
        $billW->parts_type = $form->parts_type;
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
     * @throws
     */
    public function pandianGoods($form)
    {
        //校验单据状态
        if ($form->bill_status != PartsBillStatusEnum::SAVE) {
            throw new \Exception("单据已盘点结束");
        }
        if(false === $form->validate()) {
            throw new \Exception($this->getError($form));
        }
        if(!$form->parts_sn) {
            throw new \Exception("配件编号不能为空");
        }
        if(!$form->parts_num) {
            throw new \Exception("配件数量不能为空");
        }
        if(!$form->parts_weight) {
            throw new \Exception("配件重量不能为空");
        }
        $billGoods = WarehousePartsBillGoods::find()->where(['parts_sn'=>$form->parts_sn,'bill_id'=>$form->id])->one();
        if($billGoods && $billGoods->status == PandianStatusEnum::NORMAL) {
            //已盘点且正常的忽略
            throw new \Exception("配件编号[{$form->parts_sn}]已盘点且正常");
        }
        $goods = WarehouseParts::find()->where(['parts_sn'=>$form->parts_sn])->one();
        if(empty($goods)) {
            throw new \Exception("[{$form->parts_sn}]配件编号不存在");
        }else{
            $modelW = WarehousePartsBillW::findOne(['id'=>$form->id]);
            if($goods->parts_type != $modelW->parts_type){
                throw new \Exception("[{$form->parts_sn}]配件编号材质不对");
            }
        }
        if(!$billGoods) {
            $billGoods = new WarehousePartsBillGoods();
            $billGoods->bill_id = $form->id;
            $billGoods->bill_no = $form->bill_no;
            $billGoods->bill_type = $form->bill_type;
            $billGoods->parts_sn = $goods->parts_sn;
            $billGoods->parts_name = $goods->parts_name;
            $billGoods->style_sn = $goods->style_sn;
            $billGoods->parts_type = $goods->parts_type;
            $billGoods->color = $goods->color;
            $billGoods->shape = $goods->shape;
            $billGoods->size = $goods->size;
            $billGoods->chain_type = $goods->chain_type;
            $billGoods->cramp_ring = $goods->cramp_ring;
            $billGoods->parts_num = $form->parts_num;
            $billGoods->parts_weight = $form->parts_weight;
            $billGoods->status = PandianStatusEnum::PROFIT;//盘盈
        }else {
            if($form->to_warehouse_id == $goods->warehouse_id
                && bccomp($billGoods->parts_num,$form->parts_num)==0
                && bccomp($billGoods->parts_weight,$form->parts_weight,3)==0) {
                $billGoods->status = PandianStatusEnum::NORMAL;//正常
            }elseif($form->to_warehouse_id != $goods->warehouse_id
                || bccomp($billGoods->parts_num,$form->parts_num)!=0
                || bccomp($billGoods->parts_weight,$form->parts_weight,3)!=0){
                $billGoods->status = PandianStatusEnum::LOSS;//盘亏
            }
        }
        //更多商品属性
        //............
        if(false === $billGoods->save()) {
            throw new \Exception($this->getError($billGoods));
        }
        $data = ['status'=>ConfirmEnum::YES,'actual_num'=>$form->parts_num,'actual_weight'=>$form->parts_weight,'fin_status'=>FinAuditStatusEnum::PENDING];
        WarehousePartsBillGoodsW::updateAll($data,['id'=>$billGoods->id]);
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
                $goods = WarehousePartsBillGoodsWForm::findOne(['id'=>$id]);
                if($goods->fin_status != FinAuditStatusEnum::PENDING){
                    $parts = WarehousePartsBillGoods::findOne($id);
                    throw new \Exception("配件编号【{$parts->parts_sn}】不是待审核状态");
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
                $goods = WarehousePartsBillGoodsWForm::findOne($id);
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
     * @throws
     */
    public function finishBillW($bill_id)
    {
        $bill = WarehousePartsBill::find()->where(['id'=>$bill_id])->one();
        if(!$bill || $bill->status == BillWStatusEnum::FINISHED) {
            throw new \Exception("盘点已结束");
        }
        $bill->status = BillWStatusEnum::FINISHED;
        $bill->bill_status = PartsBillStatusEnum::PENDING; //待审核
        if(false === $bill->save(false,['id','status', 'bill_status'])) {
            throw new \Exception($this->getError($bill));
        }
        //1.未盘点设为盘亏
        WarehousePartsBillGoods::updateAll(['status'=>PandianStatusEnum::LOSS],['bill_id'=>$bill_id,'status'=>PandianStatusEnum::SAVE]);

        //2.解锁商品
        $subQuery = WarehousePartsBillGoods::find()->select(['parts_sn'])->where(['bill_id'=>$bill->id]);
        WarehouseParts::updateAll(['parts_status'=>PartsStatusEnum::IN_STOCK],['parts_sn'=>$subQuery,'parts_status'=>PartsStatusEnum::IN_PANDIAN]);

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
     * @throws
     */
    public function auditBillW($form)
    {
        if(false === $form->validate()) {
            throw new \Exception($this->getError($form));
        }
        /*$count = WarehousePartsBillGoodsW::find()->where(['fin_status'=>FinAuditStatusEnum::PENDING])->count();
        if($count) {
            throw new \Exception("盘点明细还有财务未审核的商品，盘点不能审核");
        }*/
        $subQuery = WarehousePartsBillGoods::find()->select(['parts_sn'])->where(['bill_id'=>$form->id]);
        if($form->audit_status == AuditStatusEnum::PASS) {
            $form->bill_status = PartsBillStatusEnum::CONFIRM;
            WarehouseParts::updateAll(['parts_status'=>PartsStatusEnum::IN_STOCK],['parts_sn'=>$subQuery,'parts_status'=>PartsStatusEnum::IN_PANDIAN]);
            //解锁仓库
            \Yii::$app->warehouseService->warehouse->unlockWarehouse($form->to_warehouse_id);
        }else {
            $form->bill_status = PartsBillStatusEnum::CANCEL;
            WarehouseParts::updateAll(['parts_status'=>PartsStatusEnum::IN_STOCK],['parts_sn'=>$subQuery,'parts_status'=>PartsStatusEnum::IN_PANDIAN]);
        }
        if(false === $form->save() ){
            throw new \Exception($this->getError($form));
        }
    }

    /**
     * 盘点单汇总
     * @param int $bill_id
     * @throws
     * @return
     */
    public function billWSummary($bill_id)
    {
        $sum = WarehousePartsBillGoods::find()->alias("g")->innerJoin(WarehousePartsBillGoodsW::tableName().' gw','g.id=gw.id')
            ->select(['sum(if(gw.status='.ConfirmEnum::YES.',1,0)) as actual_num',
                'sum(if(gw.status='.ConfirmEnum::YES.',gw.actual_weight,0)) as actual_weight',
                'sum(if(gw.status='.ConfirmEnum::YES.',gw.actual_num,0)) as actual_grain',
                'sum(if(g.status='.PandianStatusEnum::PROFIT.',1,0)) as profit_num',
                'sum(if(g.status='.PandianStatusEnum::PROFIT.',g.parts_weight,0)) as profit_weight',
                'sum(if(g.status='.PandianStatusEnum::PROFIT.',g.parts_num,0)) as profit_grain',
                'sum(if(g.status='.PandianStatusEnum::LOSS.',1,0)) as loss_num',
                'sum(if(g.status='.PandianStatusEnum::LOSS.',g.parts_weight,0)) as loss_weight',
                'sum(if(g.status='.PandianStatusEnum::LOSS.',g.parts_num,0)) as loss_grain',
                'sum(if(g.status='.PandianStatusEnum::SAVE.',1,0)) as save_num',
                'sum(if(g.status='.PandianStatusEnum::SAVE.',g.parts_weight,0)) as save_weight',
                'sum(if(g.status='.PandianStatusEnum::SAVE.',g.parts_num,0)) as save_grain',
                'sum(if(g.status='.PandianStatusEnum::NORMAL.',1,0)) as normal_num',
                'sum(if(g.status='.PandianStatusEnum::NORMAL.',g.parts_weight,0)) as normal_weight',
                'sum(if(g.status='.PandianStatusEnum::NORMAL.',g.parts_num,0)) as normal_grain',
                'sum(if(gw.adjust_status>'.PandianAdjustEnum::SAVE.',1,0)) as adjust_num',
                'sum(if(gw.adjust_status>'.PandianAdjustEnum::SAVE.',g.parts_weight,0)) as adjust_weight',
                'sum(if(gw.adjust_status>'.PandianAdjustEnum::SAVE.',g.parts_num,0)) as adjust_grain',
                'sum(1) as goods_num',//明细总数量
                'sum(g.parts_weight) as goods_weight',//明细总重量
                'sum(g.parts_num) as goods_grain',//明细总粒数
                'sum(IFNULL(g.cost_price,0)) as total_cost',
            ])->where(['g.bill_id'=>$bill_id])->asArray()->one();
        if($sum) {
            $billUpdate = ['total_num'=>$sum['goods_num'], 'total_weight'=>$sum['goods_weight'], 'total_grain'=>$sum['goods_grain']];
            $billWUpdate = [
                'save_num'=>$sum['save_num'],'actual_num'=>$sum['actual_num'], 'loss_num'=>$sum['loss_num'], 'normal_num'=>$sum['normal_num'], 'adjust_num'=>$sum['adjust_num'],
                'save_weight'=>$sum['save_weight'],'actual_weight'=>$sum['actual_weight'], 'loss_weight'=>$sum['loss_weight'], 'normal_weight'=>$sum['normal_weight'], 'adjust_weight'=>$sum['adjust_weight'],
                'save_grain'=>$sum['save_grain'],'actual_grain'=>$sum['actual_grain'], 'loss_grain'=>$sum['loss_grain'], 'normal_grain'=>$sum['normal_grain'], 'adjust_grain'=>$sum['adjust_grain'],
            ];
            $res1 = WarehousePartsBill::updateAll($billUpdate,['id'=>$bill_id]);
            $res2 = WarehousePartsBillW::updateAll($billWUpdate,['id'=>$bill_id]);
            return $res1 && $res2;
        }
        return false;
    }

}
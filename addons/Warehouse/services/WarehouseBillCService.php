<?php

namespace addons\Warehouse\services;

use addons\Warehouse\common\enums\DeliveryTypeEnum;
use Yii;
use yii\db\Exception;
use addons\Warehouse\common\models\WarehouseGoods;
use addons\Warehouse\common\forms\WarehouseBillCForm;
use addons\Warehouse\common\models\WarehouseBillGoods;
use addons\Warehouse\common\enums\LendStatusEnum;
use addons\Warehouse\common\enums\BillStatusEnum;
use addons\Warehouse\common\enums\GoodsStatusEnum;
use common\enums\AuditStatusEnum;
use common\helpers\ArrayHelper;
use common\helpers\Url;

/**
 * 其他出库单
 * @package services\common
 * @author jianyan74 <751393839@qq.com>
 */
class WarehouseBillCService extends WarehouseBillService
{

    /**
     * 创建退货返厂单明细
     * @param WarehouseBillCForm $form
     */
    public function createBillGoodsC($form, $bill_goods)
    {
        if(false === $form->validate()) {
            throw new \Exception($this->getError($form));
        }

        //批量创建单据明细
        $goods_val = [];
        $goods_id_arr = [];
        foreach ($bill_goods as &$goods) {
            $goods_id = $goods['goods_id'];
            $goods_info = WarehouseGoods::find()->where(['goods_id' => $goods_id, 'goods_status'=>GoodsStatusEnum::IN_STOCK])->one();
            if(empty($goods_info)){
                throw new \yii\base\Exception("货号{$goods_id}不存在或者不是库存中");
            }
            $goods['bill_id'] = $form->id;
            $goods['bill_no'] = $form->bill_no;
            $goods['bill_type'] = $form->bill_type;
            $goods['warehouse_id'] = $goods_info->warehouse_id;
            $goods['put_in_type'] = $goods_info->put_in_type;
            $goods_val[] = array_values($goods);
            $goods_id_arr[] = $goods_id;
        }
        $goods_key = array_keys($bill_goods[0]);
        \Yii::$app->db->createCommand()->batchInsert(WarehouseBillGoods::tableName(), $goods_key, $goods_val)->execute();

        //更新商品库存状态
        if($form->delivery_type == DeliveryTypeEnum::BORROW_GOODS){
            $status = GoodsStatusEnum::IN_LEND;
        }elseif($form->delivery_type == DeliveryTypeEnum::QUICK_SALE){
            $status = GoodsStatusEnum::IN_SALE;
        }else{
            //其他出库类型
            $status = GoodsStatusEnum::IN_STOCK;//待定
        }
        $execute_num = WarehouseGoods::updateAll(['goods_status'=> $status],['goods_id'=>$goods_id_arr, 'goods_status' => GoodsStatusEnum::IN_STOCK]);
        if($execute_num <> count($bill_goods)){
            throw new Exception("货品改变状态数量与明细数量不一致");
        }

        //更新收货单汇总：总金额和总数量
        $res = \Yii::$app->warehouseService->bill->WarehouseBillSummary($form->id);
        if(false === $res){
            throw new Exception('更新单据汇总失败');
        }
    }

    /**
     * 其他出库单审核
     * @param WarehouseBillCForm $form
     */
    public function auditBillC($form)
    {
        if(false === $form->validate()) {
            throw new \Exception($this->getError($form));
        }
        if($form->audit_status == AuditStatusEnum::PASS){
            $form->bill_status = BillStatusEnum::CONFIRM;
        }else{
            $form->bill_status = BillStatusEnum::SAVE;
        }
        if(false === $form->save()) {
            throw new \Exception($this->getError($form));
        }
        $billGoods = WarehouseBillGoods::find()->select(['id', 'goods_id'])->where(['bill_id' => $form->id])->asArray()->all();
        if(empty($billGoods) && $form->audit_status == AuditStatusEnum::PASS){
            throw new \Exception("单据明细不能为空");
        }
        if($form->audit_status == AuditStatusEnum::PASS){
            $goods_ids = ArrayHelper::getColumn($billGoods, 'goods_id');
            //更新商品库存状态
            if($form->delivery_type == DeliveryTypeEnum::BORROW_GOODS){
                $status = GoodsStatusEnum::HAS_LEND;
                $conStatus = GoodsStatusEnum::IN_LEND;
            }elseif($form->delivery_type == DeliveryTypeEnum::QUICK_SALE){
                $status = GoodsStatusEnum::HAS_SOLD;
                $conStatus = GoodsStatusEnum::IN_SALE;
            }else{
                //其他出库类型
                $status = GoodsStatusEnum::IN_STOCK;//待定
                $conStatus = GoodsStatusEnum::IN_STOCK;//待定
            }
            $condition = ['goods_status' => $conStatus, 'goods_id' => $goods_ids];
            $res = WarehouseGoods::updateAll(['goods_status' => $status], $condition);
            if(false === $res){
                throw new \Exception("更新货品状态失败");
            }
        }
        if($form->delivery_type == DeliveryTypeEnum::BORROW_GOODS){
            $ids = ArrayHelper::getColumn($billGoods, 'id');
            $res = WarehouseBillGoods::updateAll(['status' => LendStatusEnum::LEND], ['id' => $ids]);
            if(false === $res){
                throw new \Exception("更新明细商品状态失败");
            }
        }
    }

    /**
     * 其他出库单关闭
     * @param WarehouseBillCForm $form
     */
    public function closeBillB($form)
    {
        if(false === $form->validate()) {
            throw new \Exception($this->getError($form));
        }
        //更新库存状态
        $billGoods = WarehouseBillGoods::find()->where(['bill_id' => $form->id])->select(['goods_id'])->all();
        foreach ($billGoods as $goods){
            $res = WarehouseGoods::updateAll(['goods_status' => GoodsStatusEnum::IN_STOCK],['goods_id' => $goods->goods_id, 'goods_status' => GoodsStatusEnum::IN_RETURN_FACTORY]);
            if(!$res){
                throw new Exception("商品{$goods->goods_id}不是返厂中或者不存在，请查看原因");
            }
        }
        if(false === $form->save()){
            throw new \Exception($this->getError($form));
        }
    }

    /**
     *  还货
     * @param object $form
     * @throws \Exception
     */
    public function returnGoods($form){

        $ids = $form->getIds();
        if(empty($ids)) {
            throw new \Exception("ID不能为空");
        }
        //更新库存状态
        $billGoods = WarehouseBillGoods::find()->where(['id' => $ids])->select(['goods_id'])->all();
        foreach ($billGoods as $goods){
            $res = WarehouseGoods::updateAll(['goods_status' => GoodsStatusEnum::IN_STOCK],['goods_id' => $goods->goods_id, 'goods_status' => GoodsStatusEnum::HAS_LEND]);
            if(!$res){
                throw new Exception("商品{$goods->goods_id}不是已借货状态或者不存在，请查看原因");
            }
        }
        $returned_time = empty($form->returned_time)?0:strtotime($form->returned_time);
        WarehouseBillGoods::updateAll(['status'=>$form->status, 'goods_remark'=>$form->goods_remark, 'returned_time'=>$returned_time], ['id'=>$ids]);

        //if(false === $form->save()) {
        //    throw new \Exception($this->getError($form));
        //}
        //$execute_num = WarehouseBillGoods::updateAll(['status'=>$form->status, 'goods_remark'=>$form->goods_remark], ['id'=>$ids]);
        //if($execute_num <> count($ids)){
        //    throw new Exception("货品改变状态数量与明细数量不一致");
        //}
    }

    /**
     *  还货验证
     * @param object $form
     * @throws \Exception
     */
    public function returnGoodsValidate($form){
        $ids = $form->getIds();
        if(is_array($ids)){
            foreach ($ids as $id) {
                $goods = WarehouseBillGoods::find()->where(['id'=>$id])->select(['status', 'bill_id', 'goods_id'])->one();
                if($goods->status != LendStatusEnum::LEND){
                    throw new Exception("货号【{$goods->goods_id}】不是借货状态");
                }
            }
        }
        return $goods->bill_id??"";
    }
}
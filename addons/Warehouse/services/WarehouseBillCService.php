<?php

namespace addons\Warehouse\services;

use addons\Warehouse\common\enums\DeliveryTypeEnum;
use addons\Warehouse\common\models\WarehouseBill;
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
     * 创建其他出库单明细
     * @param WarehouseBillCForm $form
     * @param array $bill_goods
     * @throws
     *
     */
    public function createBillGoodsC($form, $bill_goods)
    {
        if(false === $form->validate()) {
            throw new \Exception($this->getError($form));
        }
        //批量创建单据明细
        $goods_ids = $goods_val = [];
        foreach ($bill_goods as &$goods) {
            $goods_id = $goods['goods_id'];
            $goods_ids[] = $goods_id;
            $goods_info = WarehouseGoods::find()->where(['goods_id' => $goods_id, 'goods_status'=>GoodsStatusEnum::IN_STOCK])->one();
            if(empty($goods_info)){
                throw new Exception("货号{$goods_id}不存在或者不是库存中");
            }
            $goods['bill_id'] = $form->id;
            $goods['bill_no'] = $form->bill_no;
            $goods['bill_type'] = $form->bill_type;
            $goods['warehouse_id'] = $goods_info->warehouse_id;
            $goods['put_in_type'] = $goods_info->put_in_type;
            $goods_val[] = array_values($goods);
            $goods_key = array_keys($goods);
            if(count($goods_val)>=10){
                $res = \Yii::$app->db->createCommand()->batchInsert(WarehouseBillGoods::tableName(), $goods_key, $goods_val)->execute();
                if(false === $res){
                    throw new Exception('更新单据汇总失败1');
                }
                $goods_val = [];
            }
        }
        if(!empty($goods_val)){
            $res = \Yii::$app->db->createCommand()->batchInsert(WarehouseBillGoods::tableName(), $goods_key, $goods_val)->execute();
            if(false === $res){
                throw new Exception('更新单据汇总失败2');
            }
        }
        //更新商品库存状态
        if(in_array($form->delivery_type, [DeliveryTypeEnum::QUICK_SALE, DeliveryTypeEnum::PLATFORM]))
        {
            $status = GoodsStatusEnum::IN_SALE;
        }else{
            //其他出库类型
            $status = GoodsStatusEnum::IN_STOCK;//待定
        }
//        $execute_num = WarehouseGoods::updateAll(['goods_status'=> $status],['goods_id'=>$goods_ids, 'goods_status' => GoodsStatusEnum::IN_STOCK]);
//        if($execute_num <> count($bill_goods)){
//            throw new Exception("货品改变状态数量与明细数量不一致");
//        }

        foreach ($goods_ids as $goods_id){
            $outbound_cost = Yii::$app->warehouseService->warehouseGoods->getOutboundCost($goods_id);
            $res = WarehouseGoods::updateAll(['goods_status'=> $status,'outbound_cost'=>$outbound_cost],['goods_id'=>$goods_id, 'goods_status' => GoodsStatusEnum::IN_STOCK]);
            if(false === $res){
                throw new Exception('更新库存信息失败');
            }
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
     * @throws
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
        $billGoods = WarehouseBillGoods::find()->select(['id', 'goods_id'])->where(['bill_id' => $form->id])->asArray()->all();
        if(empty($billGoods)){
            throw new \Exception("单据明细不能为空");
        }
        if($form->audit_status == AuditStatusEnum::PASS){
            $goods_ids = ArrayHelper::getColumn($billGoods, 'goods_id');
            //更新商品库存状态
            if(in_array($form->delivery_type, [DeliveryTypeEnum::QUICK_SALE, DeliveryTypeEnum::PLATFORM])){
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
        if(false === $form->save()) {
            throw new \Exception($this->getError($form));
        }
    }

    /**
     * 其他出库单-关闭
     * @param WarehouseBill $form
     * @throws
     */
    public function cancelBillC($form)
    {
        //更新库存状态
        $billGoods = WarehouseBillGoods::find()->where(['bill_id' => $form->id])->select(['goods_id'])->all();
        if($billGoods){
            foreach ($billGoods as $goods){
                $res = WarehouseGoods::updateAll(['goods_status' => GoodsStatusEnum::IN_STOCK],['goods_id' => $goods->goods_id]);
                if(!$res){
                    throw new Exception("商品{$goods->goods_id}不存在，请查看原因");
                }
            }
        }
        $form->bill_status = BillStatusEnum::CANCEL;
        if(false === $form->save()){
            throw new \Exception($this->getError($form));
        }
    }

    /**
     * 其他出库单-删除
     * @param WarehouseBill $form
     * @throws
     */
    public function deleteBillC($form)
    {
        //删除明细
        $res = WarehouseBillGoods::deleteAll(['bill_id' => $form->id]);
        if(false === $res){
            throw new Exception("删除明细失败");
        }
        if(false === $form->delete()){
            throw new \Exception($this->getError($form));
        }
    }
}
<?php

namespace addons\Warehouse\services;

use addons\Warehouse\common\models\WarehouseStoneBill;
use addons\Warehouse\common\models\WarehouseStoneBillDetail;
use Yii;
use common\components\Service;
use common\helpers\SnHelper;
use addons\Warehouse\common\models\WarehouseBill;
use addons\Warehouse\common\models\WarehouseGoods;
use addons\Warehouse\common\models\WarehouseBillGoods;
use addons\Purchase\common\enums\ReceiptGoodsStatusEnum;
use addons\Purchase\common\models\PurchaseReceiptGoods;
use addons\Warehouse\common\enums\BillStatusEnum;
use addons\Warehouse\common\enums\GoodsStatusEnum;
use addons\Warehouse\common\enums\OrderTypeEnum;
use common\enums\AuditStatusEnum;
use common\enums\StatusEnum;
use common\helpers\ArrayHelper;

/**
 * 石包单据
 * @package services\common
 * @author jianyan74 <751393839@qq.com>
 */
class WarehouseStoneBillService extends Service
{

    /**
     * 创建买石单
     * @param array $bill
     * @param array $details
     */
    public function createBillMs($bill, $details){
        $billM = new WarehouseStoneBill();
        $billM->attributes = $bill;
        $billM->bill_no = SnHelper::createBillSn($billM->bill_type);
        if(false === $billM->save()){
            throw new \Exception($this->getError($billM));
        }
        $bill_id = $billM->attributes['id'];
        $goodsM = new WarehouseStoneBillDetail();
        foreach ($details as &$good){
            $good['bill_id'] = $bill_id;
            $good['bill_type'] = $billM->bill_type;
            $goodsM->setAttributes($good);
            if(!$goodsM->validate()){
                throw new \Exception($this->getError($goodsM));
            }
        }
        $details = ArrayHelper::toArray($details);
        $value = [];
        $key = array_keys($details[0]);
        foreach ($details as $detail) {
            $value[] = array_values($detail);
        }
        $res = Yii::$app->db->createCommand()->batchInsert(WarehouseStoneBillDetail::tableName(), $key, $value)->execute();
        if(false === $res){
            throw new \Exception("创建买石单明细失败");
        }
    }

    /**
     * 买石单-审核
     * @param $form
     */
    public function auditBillMs($form)
    {
        if(false === $form->validate()) {
            throw new \Exception($this->getError($form));
        }
        if($form->audit_status == AuditStatusEnum::PASS){
            //$form->status = StatusEnum::ENABLED;
            $form->bill_status = BillStatusEnum::CONFIRM;
        }else{
            //$form->status = StatusEnum::DISABLED;
            $form->bill_status = BillStatusEnum::SAVE;
        }
        $billGoods = WarehouseBillGoods::find()->select(['goods_id', 'source_detail_id'])->where(['bill_id' => $form->id])->asArray()->all();
        if(empty($billGoods)){
            throw new \Exception("单据明细不能为空");
        }
        $goods_ids = ArrayHelper::getColumn($billGoods, 'goods_id');
        $condition = ['goods_status' => GoodsStatusEnum::RECEIVING, 'goods_id' => $goods_ids];
        $goods_status = $form->audit_status == AuditStatusEnum::PASS ? GoodsStatusEnum::IN_STOCK : GoodsStatusEnum::RECEIVING;
        $res = WarehouseGoods::updateAll(['goods_status' => $goods_status, 'put_in_type' => $form->put_in_type, 'warehouse_id' => $form->to_warehouse_id], $condition);
        if(false === $res) {
            throw new \Exception("更新收货单货品状态失败");
        }
        if($form->order_type == OrderTypeEnum::ORDER_L && $form->audit_status == AuditStatusEnum::PASS){
            //同步采购收货单货品状态
            $ids = ArrayHelper::getColumn($billGoods, 'source_detail_id');
            $res = PurchaseReceiptGoods::updateAll(['goods_status'=>ReceiptGoodsStatusEnum::WAREHOUSE], ['id'=>$ids]);
            if(false === $res) {
                throw new \Exception("同步采购收货单货品状态失败");
            }
        }
        if(false === $form->save()) {
            throw new \Exception($this->getError($form));
        }
    }

}
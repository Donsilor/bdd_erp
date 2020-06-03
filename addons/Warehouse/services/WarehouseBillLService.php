<?php

namespace addons\Warehouse\services;

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
 * 收货单
 * @package services\common
 * @author jianyan74 <751393839@qq.com>
 */
class WarehouseBillLService extends Service
{

    /**
     * 创建收货单据
     * @param array $goods
     * @param array $bill
     * @param array $bill_goods
     */
    public function createBillL($goods, $bill, $bill_goods){
        $billM = new WarehouseBill();
        $billM->attributes = $bill;
        $billM->bill_no = SnHelper::createBillSn($billM->bill_type);
        if(false === $billM->save()){
            throw new \Exception($this->getError($billM));
        }
        $bill_id = $billM->attributes['id'];
        $goodsM = new WarehouseGoods();
        $billGoods = new WarehouseBillGoods();
        foreach ($goods as $k => &$good){
            $good['goods_id'] = SnHelper::createGoodsId();
            $goodsM->setAttributes($good);
            if(!$goodsM->validate()){
                throw new \Exception($this->getError($goodsM));
            }
            $bill_goods[$k]['bill_id'] = $bill_id;
            $bill_goods[$k]['bill_no'] = $billM->bill_no;
            $bill_goods[$k]['bill_type'] = $billM->bill_type;
            $bill_goods[$k]['goods_id'] = $goodsM->goods_id;
            $billGoods->setAttributes($bill_goods[$k]);
            if(!$billGoods->validate()){
                throw new \Exception($this->getError($billGoods));
            }
        }
        $value = [];
        $key = array_keys($goods[0]);
        foreach ($goods as $good) {
            $value[] = array_values($good);
        }
        $res = Yii::$app->db->createCommand()->batchInsert(WarehouseGoods::tableName(), $key, $value)->execute();
        if(false === $res){
            throw new \Exception("创建货品信息失败");
        }
        $value = [];
        $key = array_keys($bill_goods[0]);
        foreach ($bill_goods as $goods) {
            $value[] = array_values($goods);
        }
        $res = Yii::$app->db->createCommand()->batchInsert(WarehouseBillGoods::tableName(), $key, $value)->execute();
        if(false === $res){
            throw new \Exception("创建收货单据明细失败");
        }
    }

    /**
     * 收货单-审核
     * @param $form
     */
    public function auditBillL($form)
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
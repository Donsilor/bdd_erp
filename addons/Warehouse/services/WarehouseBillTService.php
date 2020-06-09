<?php

namespace addons\Warehouse\services;

use addons\Style\common\enums\JintuoTypeEnum;
use addons\Style\common\models\Style;
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
 * 其他收货单
 * @package services\common
 * @author jianyan74 <751393839@qq.com>
 */
class WarehouseBillTService extends Service
{

    /**
     * 添加明细
     * @param array $goods
     * @param array $bill
     * @param array $bill_goods
     */
    public function addBillTGoods($form){

        if(!$form->style_sn){
            throw new \Exception("款号不能为空");
        }
        if(!$form->goods_num){
            throw new \Exception("商品数量必填");
        }
        if(!is_numeric($form->goods_num)){
            throw new \Exception("商品数量不合法");
        }
        if($form->goods_num <= 0){
            throw new \Exception("商品数量必须大于0");
        }

        $style = Style::findOne(['style_sn'=>$form->style_sn]);
        if(!$style){
            throw new \Exception("款号不存在");
        }

        $bill = WarehouseBill::findOne(['id'=>$form->bill_id]);
        $model = new WarehouseGoods();
        $goodsM = new WarehouseBillGoods();

        $goods = [
            'goods_name' =>$style->style_name,
            'style_sn' => $form->style_sn,
            'product_type_id'=>$style->product_type_id,
            'style_cate_id'=>$style->style_cate_id,
            'style_sex' => $style->style_sex,
            'goods_status'=>GoodsStatusEnum::RECEIVING,
            'supplier_id'=>$bill->supplier_id,
            'put_in_type'=>$bill->put_in_type,
            'company_id'=> 1,//暂时为1
            'warehouse_id' => $bill->to_warehouse_id?:0,
            'goods_num' => 1,
            'jintuo_type' => JintuoTypeEnum::Chengpin,
            'cost_price' => $style->cost_price,
            //'market_price' => $style->market_price,
            'creator_id' => \Yii::$app->user->identity->getId(),
            'created_at' => time(),
        ];
        $bill_goods = [
            'goods_name' => $style->style_name,
            'style_sn' => $form->style_sn,
            'goods_num' => 1,
            'put_in_type' => $bill->put_in_type,
            'cost_price' => $style->cost_price,
            'sale_price' => $style->sale_price,
            'market_price' => $style->market_price,
            'status' => StatusEnum::ENABLED,
            'creator_id' => \Yii::$app->user->identity->getId(),
            'created_at' => time(),
        ];

        $goodsInfo = $bGoodsInfo = $goods_ids = [];
        for ($i=0; $i<$form->goods_num; $i++){
            $goods_id = SnHelper::createGoodsId();
            $goods_ids[] = $goods_id;
            $goodsInfo[$i] = $goods;
            $goodsInfo[$i]['goods_id'] = $goods_id;
            $model->setAttributes($goodsInfo[$i]);
            if(!$model->validate()){
                throw new \Exception($this->getError($model));
            }
            $bGoodsInfo[$i]= $bill_goods;
            $bGoodsInfo[$i]['bill_id'] = $form->bill_id;
            $bGoodsInfo[$i]['bill_no'] = $bill->bill_no;
            $bGoodsInfo[$i]['bill_type'] = $bill->bill_type;
            $bGoodsInfo[$i]['goods_id'] = $model->goods_id;
            $goodsM->setAttributes($bGoodsInfo[$i]);
            if(!$goodsM->validate()){
                throw new \Exception($this->getError($goodsM));
            }
        }
        $value = [];
        $key = array_keys($goodsInfo[0]);
        foreach ($goodsInfo as $item) {
            $value[] = array_values($item);
        }
        $res = Yii::$app->db->createCommand()->batchInsert(WarehouseGoods::tableName(), $key, $value)->execute();
        if(false === $res){
            throw new \Exception("创建货品信息失败");
        }
        $value = [];
        $key = array_keys($bGoodsInfo[0]);
        foreach ($bGoodsInfo as $item) {
            $value[] = array_values($item);
        }
        $res = Yii::$app->db->createCommand()->batchInsert(WarehouseBillGoods::tableName(), $key, $value)->execute();
        if(false === $res){
            throw new \Exception("创建收货单据明细失败");
        }
        //更新货号
        $ids = WarehouseGoods::find()->select(['id'])->where(['goods_id' => $goods_ids])->all();
        $ids = ArrayHelper::getColumn($ids,'id');
        if($ids){
            foreach ($ids as $id) {
                $goods = WarehouseGoods::findOne(['id'=>$id]);
                $old_goods_id = $goods->goods_id;
                $goods_id = \Yii::$app->warehouseService->warehouseGoods->createGoodsId($goods);
                $billGoods = WarehouseBillGoods::findOne(['goods_id'=>$old_goods_id]);
                $billGoods->goods_id = $goods_id;
                if(false === $billGoods->save()){
                    throw new \Exception($this->getError($billGoods));
                }
            }
        }
    }

    /**
     * 其他收货单-审核
     * @param $form
     */
    public function auditBillT($form)
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
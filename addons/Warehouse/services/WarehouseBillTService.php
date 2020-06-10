<?php

namespace addons\Warehouse\services;

use addons\Style\common\enums\JintuoTypeEnum;
use addons\Style\common\models\Style;
use addons\Warehouse\common\models\WarehouseBillGoodsT;
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
     * 单据汇总
     * @param unknown $bill_id
     */
    public function warehouseBillTSummary($bill_id)
    {
        $result = false;
        $sum = WarehouseBillGoodsT::find()
            ->select(['sum(1) as goods_num', 'sum(cost_price) as total_cost', 'sum(market_price) as total_market'])
            ->where(['bill_id'=>$bill_id])
            ->asArray()->one();
        if($sum) {
            $result = WarehouseBill::updateAll(['goods_num'=>$sum['goods_num']/1, 'total_cost'=>$sum['total_cost']/1, 'total_market'=>$sum['total_market']/1],['id'=>$bill_id]);
        }
        return $result;
    }

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
        if($form->goods_num > 100){
            throw new \Exception("一次最多只能添加100个商品，可分多次添加");
        }
        $style = Style::findOne(['style_sn'=>$form->style_sn]);
        if(!$style){
            throw new \Exception("款号不存在");
        }
        //$bill = WarehouseBill::findOne(['id'=>$form->bill_id]);
        $goodsM = new WarehouseBillGoodsT();
        $goods = [
            'goods_name' =>$style->style_name,
            'style_sn' => $form->style_sn,
            'product_type_id'=>$style->product_type_id,
            'style_cate_id'=>$style->style_cate_id,
            'style_sex' => $style->style_sex,
            'goods_num' => 1,
            'jintuo_type' => JintuoTypeEnum::Chengpin,
            'cost_price' => $style->cost_price,
            //'market_price' => $style->market_price,
            'creator_id' => \Yii::$app->user->identity->getId(),
            'created_at' => time(),
        ];
        $goodsInfo = [];
        for ($i=0; $i<$form->goods_num; $i++){
            $goodsInfo[$i]= $goods;
            $goodsInfo[$i]['bill_id'] = $form->bill_id;
            $goodsInfo[$i]['goods_id'] = SnHelper::createGoodsId();
            $goodsM->setAttributes($goodsInfo[$i]);
            if(!$goodsM->validate()){
                throw new \Exception($this->getError($goodsM));
            }
        }
        $value = [];
        $key = array_keys($goodsInfo[0]);
        foreach ($goodsInfo as $item) {
            $value[] = array_values($item);
        }
        $res = Yii::$app->db->createCommand()->batchInsert(WarehouseBillGoodsT::tableName(), $key, $value)->execute();
        if(false === $res){
            throw new \Exception("创建收货单据明细失败");
        }

        $this->warehouseBillTSummary($form->bill_id);
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
            $form->bill_status = BillStatusEnum::CONFIRM;
        }else{
            $form->bill_status = BillStatusEnum::SAVE;
        }
        $billGoods = WarehouseBillGoodsT::find()->where(['bill_id' => $form->id])->all();
        if(empty($billGoods)){
            throw new \Exception("单据明细不能为空");
        }
        $bill = WarehouseBill::findOne(['id'=>$form->id]);
        $goods = $bill_goods = $goods_ids = [];
        foreach ($billGoods as $good) {
            $goods_ids[] = $good->goods_id;
            $goods[] = [
                'goods_id' => $good->goods_id,
                'goods_name' =>$good->goods_name,
                'style_sn' => $good->style_sn,
                'product_type_id'=>$good->product_type_id,
                'style_cate_id'=>$good->style_cate_id,
                'style_sex' => $good->style_sex,
                'goods_status'=>GoodsStatusEnum::IN_STOCK,
                'supplier_id'=>$bill->supplier_id,
                'put_in_type'=>$bill->put_in_type,
                'company_id'=> 1,//暂时为1
                'warehouse_id' => $bill->to_warehouse_id?:0,
                'goods_num' => 1,
                'jintuo_type' => JintuoTypeEnum::Chengpin,
                'cost_price' => $good->cost_price,
                //'market_price' => $style->market_price,
                'creator_id' => \Yii::$app->user->identity->getId(),
                'created_at' => time(),
            ];
            $bill_goods[] = [
                'bill_id' => $good->bill_id,
                'bill_no' => $bill->bill_no,
                'goods_id' => $good->goods_id,
                'bill_type' => $bill->bill_type,
                'goods_name' => $good->goods_name,
                'style_sn' => $good->style_sn,
                'goods_num' => 1,
                'put_in_type' => $bill->put_in_type,
                'cost_price' => $good->cost_price,
                //'sale_price' => $good->sale_price,
                //'market_price' => $good->market_price,
                'status' => StatusEnum::ENABLED,
                'creator_id' => \Yii::$app->user->identity->getId(),
                'created_at' => time(),
            ];
        }
        $model = new WarehouseGoods();
        $goodsM = new WarehouseBillGoods();
        $value = [];
        $key = array_keys($goods[0]);
        foreach ($goods as $item) {
            $model->setAttributes($item);
            if(!$model->validate()){
                throw new \Exception($this->getError($model));
            }
            $value[] = array_values($item);
        }
        $res = Yii::$app->db->createCommand()->batchInsert(WarehouseGoods::tableName(), $key, $value)->execute();
        if(false === $res){
            throw new \Exception("创建货品信息失败");
        }
        $value = [];
        $key = array_keys($bill_goods[0]);
        foreach ($bill_goods as $item) {
            $goodsM->setAttributes($item);
            if(!$goodsM->validate()){
                throw new \Exception($this->getError($goodsM));
            }
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
        if(false === $form->save()) {
            throw new \Exception($this->getError($form));
        }
    }

}
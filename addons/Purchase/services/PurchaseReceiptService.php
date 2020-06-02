<?php

namespace addons\Purchase\services;


use addons\Purchase\common\enums\ReceiptGoodsStatusEnum;
use Yii;
use common\components\Service;
use common\helpers\Url;
use addons\Purchase\common\models\PurchaseReceipt;
use addons\Purchase\common\models\PurchaseReceiptGoods;
use addons\Warehouse\common\forms\WarehouseBillBForm;
use addons\Warehouse\common\models\WarehouseBillGoods;
use addons\Warehouse\common\models\WarehouseGoods;
use addons\Warehouse\common\enums\BillStatusEnum;
use addons\Warehouse\common\enums\BillTypeEnum;
use addons\Warehouse\common\enums\GoodsStatusEnum;
use addons\Warehouse\common\enums\OrderTypeEnum;
use addons\Supply\common\enums\QcTypeEnum;
use common\enums\AuditStatusEnum;
use common\enums\StatusEnum;

/**
 * Class TypeService
 * @package services\common
 * @author jianyan74 <751393839@qq.com>
 */
class PurchaseReceiptService extends Service
{
    
    /**
     * 采购收货单明细 tab
     * @param int $id 采购单ID
     * @return array
     */
    public function menuTabList($receipt_id,$returnUrl = null,$tag = null)
    {
        if($tag==3){
            $tablist = [
                1=>['name'=>'基础信息','url'=>Url::to(['receipt/view','id'=>$receipt_id,'tab'=>1,'returnUrl'=>$returnUrl])],
                3=>['name'=>'单据明细(编辑)','url'=>Url::to(['receipt-goods/edit-all','receipt_id'=>$receipt_id,'tab'=>3,'returnUrl'=>$returnUrl])],
                4=>['name'=>'日志信息','url'=>Url::to(['receipt-log/index','receipt_id'=>$receipt_id,'tab'=>4,'returnUrl'=>$returnUrl])]
            ];
        }else{
            $tablist = [
                1=>['name'=>'基础信息','url'=>Url::to(['receipt/view','id'=>$receipt_id,'tab'=>1,'returnUrl'=>$returnUrl])],
                2=>['name'=>'单据明细','url'=>Url::to(['receipt-goods/index','receipt_id'=>$receipt_id,'tab'=>2,'returnUrl'=>$returnUrl])],
                4=>['name'=>'日志信息','url'=>Url::to(['receipt-log/index','receipt_id'=>$receipt_id,'tab'=>4,'returnUrl'=>$returnUrl])]
            ];
        }
        return $tablist;
    }
    
    /**
     * 采购收货单汇总
     * @param unknown $receipt_id
     */
    public function purchaseReceiptSummary($receipt_id)
    {
        $result = false;
        $sum = PurchaseReceiptGoods::find()
                    ->select(['sum(1) as receipt_num','sum(cost_price) as total_cost'])
                    ->where(['receipt_id'=>$receipt_id, 'status'=>StatusEnum::ENABLED])
                    ->asArray()->one();
        if($sum) {
            $result = PurchaseReceipt::updateAll(['receipt_num'=>$sum['receipt_num']/1,'total_cost'=>$sum['total_cost']/1],['id'=>$receipt_id]);
        }
        return $result;
    }


    /**
     * 同步采购收货单生成L单
     * @param unknown $purchase_id
     * @param unknown $detail_ids
     * @throws \Exception
     */
    public function syncReceiptToBillInfoL($receipt_id, $detail_ids = null)
    {
        $receipt = PurchaseReceipt::find()->where(['id'=>$receipt_id])->one();
        if($receipt->receipt_num <= 0 ){
            throw new \Exception('采购收货单没有明细');
        }
        if($receipt->audit_status != AuditStatusEnum::PASS){
            throw new \Exception('采购收货单没有审核');
        }
        $query = PurchaseReceiptGoods::find()->where(['receipt_id'=>$receipt_id, 'goods_status' => ReceiptGoodsStatusEnum::IQC_PASS]);
        if(!empty($detail_ids)) {
            $query->andWhere(['id'=>$detail_ids]);
        }
        $models = $query->all();
        $total_cost = 0;
        $market_price = 0;
        $sale_price = 0;
        $goods = [];
        foreach ($models as $model){

            $goods[] = [
                'goods_name' =>$model->goods_name,
                'style_sn' => $model->style_sn,
                'product_type_id'=>$model->product_type_id,
                'style_cate_id'=>$model->style_cate_id,
                'goods_status'=>GoodsStatusEnum::RECEIVING,
                'supplier_id'=>$receipt->supplier_id,
                'put_in_type'=>$receipt->put_in_type,
                'company_id'=> 1,//暂时为1
                'warehouse_id' => $receipt->to_warehouse_id?:0,
                'gold_weight' => $model->gold_weight?:0,
                'gold_loss' => $model->gold_loss?:0,
                'gross_weight' => (String) $model->gross_weight,
                'finger' => (String) $model->finger?:'0',
                'produce_sn' => $model->produce_sn,
                'cert_id' => $model->cert_id,
                'goods_num' => $model->goods_num,
                'material' => (String) $model->material,
                'material_type' => '',
                'material_color' => '',
                'diamond_carat' => $model->main_stone_weight,
                'diamond_clarity' => (String) $model->main_stone_clarity,
                'jintuo_type' => $model->jintuo_type,
                'market_price' => $model->market_price,
                'xiangkou' => $model->xiangkou?:0,
                'parts_gold_weight' => $model->parts_weight,
                'parts_num' => 1,
                'main_stone_type' => $model->main_stone,
                'main_stone_num' => $model->main_stone_num,
                'second_stone_type1' => (String) $model->second_stone1,
                'second_stone_num1' => $model->second_stone_num1,
                'second_stone_price1' => $model->second_stone_price1,
                'second_stone_weight1' => $model->second_stone_weight1,
                'second_stone_type2' => (String) $model->second_stone2,
                'second_stone_num2' => $model->second_stone_num2,
                'second_stone_weight2' => $model->second_stone_weight2,
                'second_stone_price2' => $model->second_stone_price2
            ];

            $total_cost = bcadd($total_cost, $model->cost_price, 2);
            $market_price = bcadd($market_price, $model->market_price, 2);
            $sale_price = bcadd($sale_price, $model->sale_price, 2);
        }

        $bill = [
            'bill_type' =>  BillTypeEnum::BILL_TYPE_L,
            'bill_status' => BillStatusEnum::SAVE,
            'supplier_id' => $receipt->supplier_id,
            'put_in_type' => $receipt->put_in_type,
            'order_type' => OrderTypeEnum::ORDER_L,
            'goods_num' => count($goods),
            'total_cost' => $total_cost,
            'total_sale' => $sale_price,
            'total_market' => $market_price,
            'to_warehouse_id' => $receipt->to_warehouse_id,
            'to_company_id' => 0,
            'from_company_id' => 0,
            'from_warehouse_id' => 0,
            'send_goods_sn' => $receipt->receipt_no,
        ];
        Yii::$app->warehouseService->billL->createBillL($bill, $goods);
    }

    /**
     *  IQC质检
     * @param WarehouseBillBForm $form
     */
    public function qcIqc($form)
    {
        if(false === $form->validate()) {
            throw new \Exception($this->getError($form));
        }
        if($form->goods_status == QcTypeEnum::PASS){
            $form->goods_status = ReceiptGoodsStatusEnum::IQC_PASS;
        }else{
            $form->goods_status = ReceiptGoodsStatusEnum::IQC_NO_PASS;
        }
        if(false === $form->save()) {
            throw new \Exception($this->getError($form));
        }
    }

    /**
     *  批量生成不良返厂单
     * @param WarehouseBillBForm $form
     */
    public function batchDefective($form)
    {
        if(false === $form->validate()) {
            throw new \Exception($this->getError($form));
        }
        if($form->goods_status == QcTypeEnum::PASS){
            $form->goods_status = ReceiptGoodsStatusEnum::IQC_PASS;
        }else{
            $form->goods_status = ReceiptGoodsStatusEnum::IQC_NO_PASS;
        }
        if(false === $form->save()) {
            throw new \Exception($this->getError($form));
        }
    }
}
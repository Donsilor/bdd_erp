<?php

namespace addons\Purchase\services;

use addons\Purchase\common\enums\PurchaseTypeEnum;
use addons\Purchase\common\models\PurchaseReceiptGoods;
use addons\Purchase\common\models\PurchaseStoneReceiptGoods;
use addons\Warehouse\common\enums\AdjustTypeEnum;
use addons\Warehouse\common\enums\GoldBillTypeEnum;
use common\helpers\SnHelper;
use Yii;
use common\components\Service;
use common\helpers\Url;
use addons\Purchase\common\models\PurchaseReceipt;
use addons\Purchase\common\models\PurchaseGoldReceiptGoods;
use addons\Warehouse\common\forms\WarehouseBillBForm;
use addons\Purchase\common\enums\DefectiveStatusEnum;
use addons\Purchase\common\enums\ReceiptGoodsStatusEnum;
use addons\Purchase\common\models\PurchaseDefective;
use addons\Purchase\common\models\PurchaseDefectiveGoods;
use addons\Warehouse\common\enums\BillStatusEnum;
use addons\Warehouse\common\enums\BillTypeEnum;
use addons\Warehouse\common\enums\GoodsStatusEnum;
use addons\Warehouse\common\enums\OrderTypeEnum;
use addons\Supply\common\enums\QcTypeEnum;
use common\enums\AuditStatusEnum;
use common\enums\StatusEnum;
use yii\db\Exception;

/**
 * 金料采购收货单
 * @package services\common
 * @author jianyan74 <751393839@qq.com>
 */
class PurchaseGoldReceiptService extends Service
{
    
    /**
     * 采购收货单汇总
     * @param unknown $receipt_id
     */
    public function purchaseReceiptSummary($receipt_id)
    {
        $result = false;
        $sum = PurchaseGoldReceiptGoods::find()
                    ->select(['sum(1) as receipt_num','sum(cost_price) as total_cost'])
                    ->where(['receipt_id'=>$receipt_id, 'status'=>StatusEnum::ENABLED])
                    ->asArray()->one();
        if($sum) {
            $result = PurchaseReceipt::updateAll(['receipt_num'=>$sum['receipt_num']/1,'total_cost'=>$sum['total_cost']/1],['id'=>$receipt_id]);
        }
        return $result;
    }


    /**
     * 同步采购收货单生成金料L单
     * @param object $form
     * @param array $detail_ids
     * @throws \Exception
     */
    public function syncReceiptToGoldL($form, $detail_ids = null)
    {
        if($form->audit_status != AuditStatusEnum::PASS){
            throw new \Exception('采购收货单没有审核');
        }
        if($form->receipt_num <= 0 ){
            throw new \Exception('采购收货单没有明细');
        }
        $query = PurchaseGoldReceiptGoods::find()->where(['receipt_id'=>$form->id, 'goods_status' => ReceiptGoodsStatusEnum::IQC_PASS]);
        $detail_ids = $form->getIds();
        if(!empty($detail_ids)) {
            $query->andWhere(['id'=>$detail_ids]);
        }
        $models = $query->all();
        if(!$models){
            throw new \Exception('采购收货单没有待入库的货品');
        }
        $goods = $ids = [];
        $total_weight = $total_cost = $sale_price = 0;
        foreach ($models as $model){
            $ids[] = $model->id;
            $goods[] = [
                'gold_name' => $model->goods_name,
                'gold_type' => $model->material_type,
                'gold_num' => $model->goods_num,
                'gold_weight' => $model->goods_weight,
                'cost_price' => $model->cost_price,
                'sale_price' => $model->gold_price,
                'source_detail_id' =>$model->id,
                'status' => StatusEnum::ENABLED,
                'created_at' => time(),
            ];
            $total_cost = bcadd($total_cost, $model->cost_price, 2);
            $total_weight = bcadd($total_weight, bcmul($model->goods_num, $model->goods_weight, 2), 2);
            //$total_sale = bcadd($sale_price, $model->sale_price, 2);
        }
        //批量更新采购收货单货品状态
        $res = PurchaseGoldReceiptGoods::updateAll(['goods_status'=>ReceiptGoodsStatusEnum::WAREHOUSE_ING, 'put_in_type'=>$form->put_in_type, 'to_warehouse_id'=>$form->to_warehouse_id],['id'=>$ids]);
        if(false === $res){
            throw new \Exception('更新采购收货单货品状态失败');
        }
        $bill = [
            'bill_type' =>  GoldBillTypeEnum::GOLD_L,
            'bill_status' => BillStatusEnum::SAVE,
            'supplier_id' => $form->supplier_id,
            'put_in_type' => $form->put_in_type,
            'adjust_type' => AdjustTypeEnum::ADD,
            'goods_num' => count($goods),
            'total_weight' => $total_weight,
            'total_cost' => $form->total_cost,
            'pay_amount' => $form->total_cost,
            'delivery_no' => $form->receipt_no,
            'remark' => $form->remark,
            'status' => StatusEnum::ENABLED,
            'creator_id' => \Yii::$app->user->identity->getId(),
            'created_at' => time(),
        ];
        Yii::$app->warehouseService->goldBill->createGoldL($bill, $goods);
    }

    /**
     *  IQC质检
     * @param WarehouseBillBForm $form
     */
    public function qcIqc($form)
    {
        $this->iqcValidate($form);
        //if(false === $form->validate()) {
            //throw new \Exception($this->getError($form));
        //}
        $ids = $form->getIds();
        if($form->goods_status == QcTypeEnum::PASS){
            $goods = ['goods_status' =>ReceiptGoodsStatusEnum::IQC_PASS];
        }else{
            $goods = ['goods_status' =>ReceiptGoodsStatusEnum::IQC_NO_PASS, 'iqc_reason' => $form->iqc_reason, 'iqc_remark' => $form->iqc_remark];
        }
        $res = PurchaseGoldReceiptGoods::updateAll($goods, ['id'=>$ids]);
        if(false === $res) {
            throw new Exception("保存失败");
        }
    }

    /**
     *  IQC质检合法验证
     * @param $ids
     */
    public function iqcValidate($form){
        $ids = $form->getIds();
        if(is_array($ids)){
            foreach ($ids as $id) {
                $goods = PurchaseGoldReceiptGoods::findOne(['id'=>$id]);
                if($goods->goods_status != ReceiptGoodsStatusEnum::IQC_ING){
                    throw new Exception("流水号【{$id}】不是待质检状态，不能质检");
                }
            }
        }
    }

    /**
     *  批量生成不良返厂单
     * @param WarehouseBillBForm $form
     */
    public function batchDefective($form)
    {
        $ids = $form->getIds();
        if(!count($ids)>1){
            throw new Exception("至少选择一个货品");
        }
        if(!$form->checkDistinct('receipt_no', $ids)){
            throw new Exception("不是同一个出货单号不允许制单");
        }
        if(!$form->checkDistinct('supplier_id', $ids)){
            throw new Exception("不是同一个供应商不允许制单");
        }
        $total_cost = 0;
        $detail = [];
        $receipt = [];
        foreach($ids as $id)
        {
            $goods = PurchaseGoldReceiptGoods::find()->where(['id'=>$id])->one();
            $receipt_id = $goods->receipt_id;
            if(!$receipt){
                $receipt = PurchaseReceipt::find()->where(['id' => $receipt_id])->one();
                $defect = PurchaseDefective::find()->select(['id'])->where(['receipt_no'=>$receipt->receipt_no])->one();
            }
            if($goods->goods_status != ReceiptGoodsStatusEnum::IQC_NO_PASS)
            {
                throw new Exception("流水号【{$id}】不是IQC质检未过状态，不能生成不良品返厂单");
            }
            $check = PurchaseDefectiveGoods::find()->where(['defective_id'=>$defect->id, 'xuhao' => $goods->xuhao])->count(1);
            if($check){
                throw new Exception("流水号【{$id}】已存在保存状态的不良返厂单，不能多次生成不良品返厂单");
            }
            $detail[] = [
                'xuhao' => $goods->xuhao,
                'goods_name' => $goods->goods_name,
                'goods_num' => $goods->goods_num,
                'material_type' => $goods->material_type,
                'goods_weight' => $goods->goods_weight,
                'cost_price' => $goods->cost_price,
                'goods_price' => $goods->gold_price,
                'iqc_reason' => $goods->iqc_reason,
                'iqc_remark' => $goods->iqc_remark,
                'created_at' => time(),
            ];
            $total_cost = bcadd($total_cost, $goods->cost_price, 2);
        }
        $bill = [
            'supplier_id' => $receipt->supplier_id,
            'receipt_no' => $receipt->receipt_no,
            'purchase_type' => $receipt->purchase_type,
            'defective_num' => count($detail),
            'total_cost' => $total_cost,
            'audit_status' => AuditStatusEnum::PENDING,
            'defective_status' => DefectiveStatusEnum::PENDING,
            'creator_id' => \Yii::$app->user->identity->getId(),
            'created_at' => time(),
        ];

        \Yii::$app->purchaseService->defective->createDefactiveBill($bill, $detail);

        $res = PurchaseGoldReceiptGoods::updateAll(['goods_status' =>ReceiptGoodsStatusEnum::FACTORY_ING], ['id'=>$ids]);
        if(false === $res) {
            throw new Exception("更新货品状态失败");
        }
    }

    /**
     *  申请入库合法验证
     * @param $ids
     */
    public function warehouseValidate($form){
        $ids = $form->getIds();
        if(is_array($ids)){
            foreach ($ids as $id) {
                $goods = PurchaseGoldReceiptGoods::findOne(['id'=>$id]);
                if($goods->goods_status != ReceiptGoodsStatusEnum::IQC_PASS){
                    throw new Exception("序号【{$goods->xuhao}】不是IQC质检通过状态，不能入库");
                }
            }
        }
    }
}
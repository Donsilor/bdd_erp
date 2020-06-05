<?php

namespace addons\Purchase\services;

use addons\Warehouse\common\enums\AdjustTypeEnum;
use addons\Warehouse\common\enums\StoneBillTypeEnum;
use Yii;
use common\components\Service;
use common\helpers\Url;
use addons\Purchase\common\models\PurchaseReceipt;
use addons\Purchase\common\models\PurchaseStoneReceiptGoods;
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
 * 石料采购收货单
 * @package services\common
 * @author jianyan74 <751393839@qq.com>
 */
class PurchaseStoneReceiptService extends Service
{
    
    /**
     * 采购收货单汇总
     * @param unknown $receipt_id
     */
    public function purchaseReceiptSummary($receipt_id)
    {
        $result = false;
        $sum = PurchaseStoneReceiptGoods::find()
                    ->select(['sum(1) as receipt_num','sum(cost_price) as total_cost'])
                    ->where(['receipt_id'=>$receipt_id, 'status'=>StatusEnum::ENABLED])
                    ->asArray()->one();
        if($sum) {
            $result = PurchaseReceipt::updateAll(['receipt_num'=>$sum['receipt_num']/1,'total_cost'=>$sum['total_cost']/1],['id'=>$receipt_id]);
        }
        return $result;
    }


    /**
     * 同步石料采购收货单生成买石单
     * @param object $form
     * @param array $detail_ids
     * @throws \Exception
     */
    public function syncReceiptToStoneBillMs($form, $detail_ids = null)
    {
        if($form->audit_status != AuditStatusEnum::PASS){
            throw new \Exception('采购收货单没有审核');
        }
        if($form->receipt_num <= 0 ){
            throw new \Exception('采购收货单没有明细');
        }
        $query = PurchaseStoneReceiptGoods::find()->where(['receipt_id'=>$form->id, 'goods_status' => ReceiptGoodsStatusEnum::IQC_PASS]);
        $detail_ids = $form->getIds();
        if(!empty($detail_ids)) {
            $query->andWhere(['id'=>$detail_ids]);
        }
        $models = $query->all();
        if(!$models){
            throw new \Exception('采购收货单没有待入库的货品');
        }
        $goods = $ids = [];
        $total_weight= $market_price= $sale_price = 0;
        foreach ($models as $model){
            $ids[] = $model->id;
            $goods[] = [
                'shibao' => $model->goods_name,
                //'cert_id' => $model->cert_id,
                'carat' => $model->goods_weight,
                'color' => $model->goods_color,
                'clarity' => $model->goods_clarity,
                //'cut' => $model->cut,
                //'polish' => $model->polish,
                //'fluorescence' =>$model->fluorescence,
                //'symmetry' =>$model->symmetry,
                'stone_num' => $model->goods_num,
                'source_detail_id' => $model->id,
                'purchase_price' => $model->cost_price,
                'stone_weight' => bcmul($model->goods_num, $model->goods_weight, 2),
                //'sale_price' => $model->sale_price,
                'status' => StatusEnum::ENABLED,
                'created_at' => time()
            ];

            $total_weight = bcadd($total_weight, bcmul($model->goods_num, $model->goods_weight, 2), 2);
        }
        //批量更新采购收货单货品状态
        $res = PurchaseStoneReceiptGoods::updateAll(['goods_status'=>ReceiptGoodsStatusEnum::WAREHOUSE_ING, 'put_in_type'=>$form->put_in_type],['id'=>$ids]);
        if(false === $res){
            throw new \Exception('更新采购收货单货品状态失败');
        }
        $bill = [
            'bill_type' =>  StoneBillTypeEnum::STONE_MS,
            'bill_status' => BillStatusEnum::SAVE,
            'supplier_id' => $form->supplier_id,
            'put_in_type' => $form->put_in_type,
            'adjust_type' => AdjustTypeEnum::ADD,
            'goods_num' => count($goods),
            'goods_weight' => $total_weight,
            'goods_total' => $form->total_cost,
            'purchase_price' => $form->total_cost,
            'send_goods_sn' => $form->receipt_no,
            'remark' => $form->remark,
            'status' => StatusEnum::ENABLED,
            'creator_id' => \Yii::$app->user->identity->getId(),
            'created_at' => time(),
        ];
        Yii::$app->warehouseService->stoneBill->createBillMs($bill, $goods);
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
        $res = PurchaseStoneReceiptGoods::updateAll($goods, ['id'=>$ids]);
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
                $goods = PurchaseStoneReceiptGoods::findOne(['id'=>$id]);
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
            $goods = PurchaseStoneReceiptGoods::find()->where(['id'=>$id])->one();
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
                'material_type' => (String) $goods->material_type,
                'goods_weight' => $goods->goods_weight,
                'goods_color' => $goods->goods_color,
                'goods_clarity' => $goods->goods_clarity,
                'goods_norms' => $goods->goods_norms,
                'cost_price' => $goods->cost_price,
                'goods_price' => $goods->stone_price,
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

        $res = PurchaseStoneReceiptGoods::updateAll(['goods_status' =>ReceiptGoodsStatusEnum::FACTORY_ING], ['id'=>$ids]);
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
                $goods = PurchaseStoneReceiptGoods::findOne(['id'=>$id]);
                if($goods->goods_status != ReceiptGoodsStatusEnum::IQC_PASS){
                    throw new Exception("序号【{$goods->xuhao}】不是IQC质检通过状态，不能入库");
                }
            }
        }
    }
}
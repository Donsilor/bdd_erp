<?php

namespace addons\Purchase\services;

use addons\Purchase\common\enums\DefectiveStatusEnum;
use addons\Purchase\common\models\PurchasePartsReceiptGoods;
use Yii;
use common\components\Service;
use common\helpers\Url;
use addons\Purchase\common\models\PurchaseDefective;
use addons\Purchase\common\models\PurchaseDefectiveGoods;
use addons\Purchase\common\models\PurchaseReceipt;
use addons\Purchase\common\models\PurchaseReceiptGoods;
use addons\Purchase\common\models\PurchaseGoldReceiptGoods;
use addons\Purchase\common\models\PurchaseStoneReceiptGoods;
use addons\Purchase\common\enums\PurchaseTypeEnum;
use addons\Purchase\common\enums\ReceiptGoodsStatusEnum;
use addons\Warehouse\common\enums\BillStatusEnum;
use common\enums\AuditStatusEnum;
use common\helpers\ArrayHelper;
use common\enums\StatusEnum;
use common\helpers\SnHelper;
use yii\db\Exception;
use common\enums\LogTypeEnum;

/**
 * Class TypeService
 * @package services\common
 * @author jianyan74 <751393839@qq.com>
 */
class PurchaseDefectiveService extends Service
{
    /**
     * 不良返厂单明细 tab
     * @param int $defective_id 不良返厂单ID
     * @param int $purchase_type 采购类型
     * @param int $returnUrl
     * @param int $tag 页签ID
     * @return array
     */
    public function menuTabList($defective_id, $purchase_type, $returnUrl = null, $tag = null)
    {
        $tabList = $tab = [];
        switch ($purchase_type){
            case PurchaseTypeEnum::GOODS:
                {
                    $tabList = [
                        1=>['name'=>'基础信息','url'=>Url::to(['defective/view','id'=>$defective_id,'tab'=>1,'returnUrl'=>$returnUrl])],
                        4=>['name'=>'日志信息','url'=>Url::to(['defective-log/index','defective_id'=>$defective_id,'tab'=>4,'returnUrl'=>$returnUrl])]
                    ];
                    if($tag!=3){
                        $tab = [2=>['name'=>'单据明细','url'=>Url::to(['defective-goods/index','defective_id'=>$defective_id,'tab'=>2,'returnUrl'=>$returnUrl])]];
                    }else{
                        $tab = [3=>['name'=>'单据明细(编辑)','url'=>Url::to(['defective-goods/edit-all','defective_id'=>$defective_id,'tab'=>3,'returnUrl'=>$returnUrl])]];
                    }
                    break;
                }
            case PurchaseTypeEnum::MATERIAL_STONE:
                {
                    $tabList = [
                        1=>['name'=>'基础信息','url'=>Url::to(['stone-defective/view','id'=>$defective_id,'tab'=>1,'returnUrl'=>$returnUrl])],
                        4=>['name'=>'日志信息','url'=>Url::to(['defective-log/index','defective_id'=>$defective_id,'tab'=>4,'returnUrl'=>$returnUrl])]
                    ];
                    if($tag!=3){
                        $tab = [2=>['name'=>'单据明细','url'=>Url::to(['stone-defective-goods/index','defective_id'=>$defective_id,'tab'=>2,'returnUrl'=>$returnUrl])]];
                    }else{
                        $tab = [3=>['name'=>'单据明细(编辑)','url'=>Url::to(['stone-defective-goods/edit-all','defective_id'=>$defective_id,'tab'=>3,'returnUrl'=>$returnUrl])]];
                    }
                    break;
                }
            case PurchaseTypeEnum::MATERIAL_GOLD:
                {
                    $tabList = [
                        1=>['name'=>'基础信息','url'=>Url::to(['gold-defective/view','id'=>$defective_id,'tab'=>1,'returnUrl'=>$returnUrl])],
                        4=>['name'=>'日志信息','url'=>Url::to(['defective-log/index','defective_id'=>$defective_id,'tab'=>4,'returnUrl'=>$returnUrl])]
                    ];
                    if($tag!=3){
                        $tab = [2=>['name'=>'单据明细','url'=>Url::to(['gold-defective-goods/index','defective_id'=>$defective_id,'tab'=>2,'returnUrl'=>$returnUrl])]];
                    }else{
                        $tab = [3=>['name'=>'单据明细(编辑)','url'=>Url::to(['gold-defective-goods/edit-all','defective_id'=>$defective_id,'tab'=>3,'returnUrl'=>$returnUrl])]];
                    }
                    break;
                }
            case PurchaseTypeEnum::MATERIAL_PARTS:
                {
                    $tabList = [
                        1=>['name'=>'基础信息','url'=>Url::to(['parts-defective/view','id'=>$defective_id,'tab'=>1,'returnUrl'=>$returnUrl])],
                        4=>['name'=>'日志信息','url'=>Url::to(['defective-log/index','defective_id'=>$defective_id,'tab'=>4,'returnUrl'=>$returnUrl])]
                    ];
                    if($tag!=3){
                        $tab = [2=>['name'=>'单据明细','url'=>Url::to(['parts-defective-goods/index','defective_id'=>$defective_id,'tab'=>2,'returnUrl'=>$returnUrl])]];
                    }else{
                        $tab = [3=>['name'=>'单据明细(编辑)','url'=>Url::to(['parts-defective-goods/edit-all','defective_id'=>$defective_id,'tab'=>3,'returnUrl'=>$returnUrl])]];
                    }
                    break;
                }
        }
        $tabList = ArrayHelper::merge($tabList, $tab);
        ksort($tabList);
        return $tabList;
    }
    
    /**
     * 不良返厂单汇总
     * @param int $defective_id
     * @throws \Exception
     */
    public function purchaseDefectiveSummary($defective_id)
    {
        $result = false;
        $sum = PurchaseDefectiveGoods::find()
                    ->select(['sum(1) as defective_num','sum(cost_price) as total_cost'])
                    ->where(['defective_id'=>$defective_id, 'status'=>StatusEnum::ENABLED])
                    ->asArray()->one();
        if($sum) {
            $result = PurchaseDefective::updateAll(['defective_num'=>$sum['defective_num']/1,'total_cost'=>$sum['total_cost']/1],['id'=>$defective_id]);
        }
        return $result;
    }

    /**
     * 创建不良返厂单
     * @param array $bill 单据详情
     * @param array $detail 单据明细
     * @throws \Exception
     */
    public function createDefactiveBill($bill, $detail)
    {
        $billM = new PurchaseDefective();
        $billM->attributes = $bill;
        $billM->defective_no = SnHelper::createDefectiveSn();

        if(false === $billM->validate()) {
            throw new \Exception($this->getError($billM));
        }
        if(false === $billM->save()) {
            throw new \Exception($this->getError($billM));
        }

        $defective_id = $billM->attributes['id'];

        foreach ($detail as $good) {
            $goods = new PurchaseDefectiveGoods();
            $goods->attributes = $good;
            $goods->defective_id = $defective_id;
            if(false === $goods->validate()) {
                throw new \Exception($this->getError($goods));
            }
            if(false === $goods->save()) {
                throw new \Exception($this->getError($goods));
            }
        }
    }

    /**
     * 不良返厂单-申请审核
     * @param object $form
     * @throws \Exception
     */
    public function applyAudit($form)
    {
        if(false === $form->validate()) {
            throw new \Exception($this->getError($form));
        }
        if($form->defective_num<=0){
            throw new \Exception("单据明细不能为空");
        }
        //同步采购收货单商品状态
        $this->getReceiptGoodsIds($form, ReceiptGoodsStatusEnum::FACTORY_ING);
        if(false === $form->save()) {
            throw new \Exception($this->getError($form));
        }
        
        //日志
        $log = [
                'defective_id' => $form->id,
                'defective_no' => $form->defective_no,
                'log_type' => LogTypeEnum::ARTIFICIAL,
                'log_module' => "申请审核",
                'log_msg' => "不良返厂单-申请审核"
        ];
        Yii::$app->purchaseService->defectiveLog->createDefectiveLog($log);
    }

    /**
     * 不良返厂单-审核
     * @param object $form
     * @throws \Exception
     */
    public function auditDefect($form)
    {
        if(false === $form->validate()) {
            throw new \Exception($this->getError($form));
        }
        if($form->audit_status == AuditStatusEnum::PASS){
            $form->defective_status = BillStatusEnum::CONFIRM;
            $goods_status = ReceiptGoodsStatusEnum::FACTORY;
        }else{
            $form->defective_status = BillStatusEnum::SAVE;
            $goods_status = ReceiptGoodsStatusEnum::FACTORY_ING;
        }
        $this->getReceiptGoodsIds($form, $goods_status);
        if(false === $form->save()) {
            throw new \Exception($this->getError($form));
        }
        
        //日志
        $log = [
                'defective_id' => $form->id,
                'defective_no' => $form->defective_no,
                'log_type' => LogTypeEnum::ARTIFICIAL,
                'log_module' => "单据审核",
                'log_msg' => "不良返厂单审核, 审核状态：".AuditStatusEnum::getValue($form->audit_status).",审核备注：".$form->audit_remark
        ];
        Yii::$app->purchaseService->defectiveLog->createDefectiveLog($log);
    }

    /**
     * 不良返厂单-取消
     * @param object $form
     * @throws \Exception
     */
    public function cancelDefect($form)
    {
        if(false === $form->validate()) {
            throw new \Exception($this->getError($form));
        }
        //同步采购收货单商品状态
        $this->getReceiptGoodsIds($form, ReceiptGoodsStatusEnum::IQC_NO_PASS);
        $form->defective_status = DefectiveStatusEnum::CANCEL;
        if(false === $form->save()) {
            throw new \Exception($this->getError($form));
        }
        
        //日志
        $log = [
                'defective_id' => $form->id,
                'defective_no' => $form->defective_no,
                'log_type' => LogTypeEnum::ARTIFICIAL,
                'log_module' => "单据取消",
                'log_msg' => "不良返厂单取消"
        ];
        Yii::$app->purchaseService->defectiveLog->createDefectiveLog($log);
    }

    /**
     * 不良返厂单-删除
     * @param object $form
     * @throws \Exception
     */
    public function DeleteDefect($form)
    {
        if(false === $form->validate()) {
            throw new \Exception($this->getError($form));
        }
        $res = PurchaseDefectiveGoods::deleteAll(['defective_id'=>$form->id]);
        if(false === $res) {
            throw new \Exception("删除单据明细失败");
        }
        if(false === $form->delete()) {
            throw new \Exception($this->getError($form));
        }
    }

    /**
     * 获取采购收货单明细ID
     * @param object $form
     * @param int $goods_status
     * @throws \Exception
     */
    public function getReceiptGoodsIds($form, $goods_status){
        if($form->purchase_type == PurchaseTypeEnum::MATERIAL_STONE){
            $model = new PurchaseStoneReceiptGoods();
        }elseif($form->purchase_type == PurchaseTypeEnum::MATERIAL_GOLD){
            $model = new PurchaseGoldReceiptGoods();
        }elseif($form->purchase_type == PurchaseTypeEnum::MATERIAL_PARTS){
            $model = new PurchasePartsReceiptGoods();
        }else{
            $model = new PurchaseReceiptGoods();
        }
        $goods = PurchaseDefectiveGoods::find()->select(['receipt_detail_id'])->where(['defective_id'=>$form->id])->asArray()->all();
        $ids = ArrayHelper::getColumn($goods,'receipt_detail_id');
        $res = $model::updateAll(['goods_status'=>$goods_status], ['id'=>$ids]);
        if(false === $res) {
            throw new \Exception("同步采购收货单货品状态失败");
        }
    }
}
<?php

namespace addons\Purchase\services;

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


/**
 * Class TypeService
 * @package services\common
 * @author jianyan74 <751393839@qq.com>
 */
class PurchaseDefectiveService extends Service
{
    
    /**
     * 不良返厂单明细 tab
     * @param int $id 不良返厂单ID
     * @return array
     */
    public function menuTabList($defective_id, $purchase_type, $returnUrl = null, $tag = null)
    {
        switch ($purchase_type){

            case PurchaseTypeEnum::GOODS:
                {
                    if($tag==3){
                        $tablist = [
                            1=>['name'=>'基础信息','url'=>Url::to(['defective/view','id'=>$defective_id,'tab'=>1,'returnUrl'=>$returnUrl])],
                            3=>['name'=>'单据明细(编辑)','url'=>Url::to(['defective-goods/edit-all','defective_id'=>$defective_id,'tab'=>3,'returnUrl'=>$returnUrl])],
                            4=>['name'=>'日志信息','url'=>Url::to(['defective-log/index','defective_id'=>$defective_id,'tab'=>4,'returnUrl'=>$returnUrl])]
                        ];
                    }else{
                        $tablist = [
                            1=>['name'=>'基础信息','url'=>Url::to(['defective/view','id'=>$defective_id,'tab'=>1,'returnUrl'=>$returnUrl])],
                            2=>['name'=>'单据明细','url'=>Url::to(['defective-goods/index','defective_id'=>$defective_id,'tab'=>2,'returnUrl'=>$returnUrl])],
                            4=>['name'=>'日志信息','url'=>Url::to(['defective-log/index','defective_id'=>$defective_id,'tab'=>4,'returnUrl'=>$returnUrl])]
                        ];
                    }
                    break;
                }
            case PurchaseTypeEnum::MATERIAL_STONE:
                {
                    if($tag==3){
                        $tablist = [
                            1=>['name'=>'基础信息','url'=>Url::to(['stone-defective/view','id'=>$defective_id,'tab'=>1,'returnUrl'=>$returnUrl])],
                            3=>['name'=>'单据明细(编辑)','url'=>Url::to(['stone-defective-goods/edit-all','defective_id'=>$defective_id,'tab'=>3,'returnUrl'=>$returnUrl])],
                            4=>['name'=>'日志信息','url'=>Url::to(['defective-log/index','defective_id'=>$defective_id,'tab'=>4,'returnUrl'=>$returnUrl])]
                        ];
                    }else{
                        $tablist = [
                            1=>['name'=>'基础信息','url'=>Url::to(['stone-defective/view','id'=>$defective_id,'tab'=>1,'returnUrl'=>$returnUrl])],
                            2=>['name'=>'单据明细','url'=>Url::to(['stone-defective-goods/index','defective_id'=>$defective_id,'tab'=>2,'returnUrl'=>$returnUrl])],
                            4=>['name'=>'日志信息','url'=>Url::to(['defective-log/index','defective_id'=>$defective_id,'tab'=>4,'returnUrl'=>$returnUrl])]
                        ];
                    }
                    break;
                }
            case PurchaseTypeEnum::MATERIAL_GOLD:
                {
                    if($tag==3){
                        $tablist = [
                            1=>['name'=>'基础信息','url'=>Url::to(['gold-defective/view','id'=>$defective_id,'tab'=>1,'returnUrl'=>$returnUrl])],
                            3=>['name'=>'单据明细(编辑)','url'=>Url::to(['gold-defective-goods/edit-all','defective_id'=>$defective_id,'tab'=>3,'returnUrl'=>$returnUrl])],
                            4=>['name'=>'日志信息','url'=>Url::to(['defective-log/index','defective_id'=>$defective_id,'tab'=>4,'returnUrl'=>$returnUrl])]
                        ];
                    }else{
                        $tablist = [
                            1=>['name'=>'基础信息','url'=>Url::to(['gold-defective/view','id'=>$defective_id,'tab'=>1,'returnUrl'=>$returnUrl])],
                            2=>['name'=>'单据明细','url'=>Url::to(['gold-defective-goods/index','defective_id'=>$defective_id,'tab'=>2,'returnUrl'=>$returnUrl])],
                            4=>['name'=>'日志信息','url'=>Url::to(['defective-log/index','defective_id'=>$defective_id,'tab'=>4,'returnUrl'=>$returnUrl])]
                        ];
                    }
                    break;
                }
        }
        return $tablist;
    }
    
    /**
     * 不良返厂单汇总
     * @param int $defective_id
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
     * @param $form
     */
    public function applyAudit($form)
    {
        if(false === $form->validate()) {
            throw new \Exception($this->getError($form));
        }
        //同步采购收货单商品状态
        $ids = $this->getReceiptGoodsIds($form);
        $res = PurchaseGoldReceiptGoods::updateAll(['goods_status'=>ReceiptGoodsStatusEnum::FACTORY_ING], ['id'=>$ids]);
        if(false === $res) {
            throw new \Exception("同步采购收货单货品状态失败");
        }
        if(false === $form->save()) {
            throw new \Exception($this->getError($form));
        }
    }

    /**
     * 不良返厂单-审核
     * @param $form
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
            $goods_status = ReceiptGoodsStatusEnum::IQC_NO_PASS;
        }
        $ids = $this->getReceiptGoodsIds($form);
        if($form->purchase_type == PurchaseTypeEnum::MATERIAL_STONE){
            $model = new PurchaseStoneReceiptGoods();
        }elseif($form->purchase_type == PurchaseTypeEnum::MATERIAL_GOLD){
            $model = new PurchaseGoldReceiptGoods();
        }else{
            $model = new PurchaseReceiptGoods();
        }
        //同步采购收货单货品状态
        $res = $model::updateAll(['goods_status'=>$goods_status], ['id'=>$ids]);
        if(false === $res) {
            throw new \Exception("同步采购收货单货品状态失败");
        }
        if(false === $form->save()) {
            throw new \Exception($this->getError($form));
        }
    }

    /**
     * 不良返厂单-取消/删除
     * @param $form
     */
    public function cancelDefect($form)
    {
        if(false === $form->validate()) {
            throw new \Exception($this->getError($form));
        }
        if($form->purchase_type == PurchaseTypeEnum::MATERIAL_STONE){
            $model = new PurchaseStoneReceiptGoods();
        }elseif($form->purchase_type == PurchaseTypeEnum::MATERIAL_GOLD){
            $model = new PurchaseGoldReceiptGoods();
        }else{
            $model = new PurchaseReceiptGoods();
        }
        //同步采购收货单商品状态
        $ids = $this->getReceiptGoodsIds($form);
        $res = $model::updateAll(['goods_status'=>ReceiptGoodsStatusEnum::IQC_NO_PASS], ['id'=>$ids]);
        if(false === $res) {
            throw new \Exception("同步采购收货单货品状态失败");
        }
        $res = PurchaseDefectiveGoods::deleteAll(['id'=>$ids]);
        if(false === $res) {
            throw new \Exception("删除单据明细失败");
        }
        if(false === $form->delete()) {
            throw new \Exception($this->getError($form));
        }
    }

    /**
     * 获取采购收货单明细ID
     * @param $form
     */
    public function getReceiptGoodsIds($form){
        if($form->purchase_type == PurchaseTypeEnum::MATERIAL_STONE){
            $model = new PurchaseStoneReceiptGoods();
        }elseif($form->purchase_type == PurchaseTypeEnum::MATERIAL_GOLD){
            $model = new PurchaseGoldReceiptGoods();
        }else{
            $model = new PurchaseReceiptGoods();
        }
        $dfGoods = PurchaseDefectiveGoods::find()->select(['xuhao'])->where(['defective_id'=>$form->id])->asArray()->all();
        $receipt = PurchaseReceipt::find()->select(['id'])->where(['receipt_no'=>$form->receipt_no])->one();
        $ids = $model::find()->select(['id'])->where(['receipt_id'=> $receipt->id, 'xuhao'=> ArrayHelper::getColumn($dfGoods,'xuhao')])->asArray()->all();
        return ArrayHelper::getColumn($ids,'id')?:[];
    }
}
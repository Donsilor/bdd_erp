<?php

namespace addons\Warehouse\services;


use Yii;
use common\components\Service;
use addons\Warehouse\common\forms\WarehouseBillRepairForm;
use addons\Warehouse\common\models\WarehouseGoods;
use addons\Warehouse\common\enums\GoodsStatusEnum;
use addons\Warehouse\common\enums\WeixiuStatusEnum;
use addons\Warehouse\common\enums\QcStatusEnum;
use addons\Warehouse\common\enums\RepairStatusEnum;
use common\enums\AuditStatusEnum;
use yii\base\Exception;

/**
 * 维修单
 * @package services\common
 * @author jianyan74 <751393839@qq.com>
 */
class WarehouseBillRepairService extends Service
{
    /**
     * 创建维修单
     * @param WarehouseBillRepairForm $form
     */
    public function createRepairBill($form)
    {
        if(false === $form->validate()) {
            throw new \Exception($this->getError($form));
        }
        $goods = WarehouseGoods::find()->where(['goods_id'=>$form->goods_id])->one();
        if(!$goods){
            throw new Exception("货号不存在");
        }
        if(GoodsStatusEnum::IN_STOCK != $goods->goods_status){
            throw new Exception("货号不是库存状态");
        }
        $goods->weixiu_status = WeixiuStatusEnum::SAVE;
        if(false === $goods->save()){
            throw new Exception($this->getError($goods));
        }
        $form->repair_times = 1;
        $form->repair_status = RepairStatusEnum::SAVE;
        $form->qc_status = QcStatusEnum::SAVE;
        if(false === $form->save()){
            throw new Exception($this->getError($form));
        }
    }

    /**
     * 维修申请
     * @param WarehouseBillRepairForm $form
     */
    public function applyRepair($form)
    {
        if (false === $form->validate()) {
            throw new \Exception($this->getError($form));
        }
        if($form->repair_status != RepairStatusEnum::SAVE){
            throw new Exception("单据不是保存状态");
        }
        $goods = WarehouseGoods::find()->where(['goods_id'=>$form->goods_id])->one();
        if(!$goods){
            throw new Exception("货号不存在");
        }
        if(GoodsStatusEnum::IN_STOCK != $goods->goods_status){
            throw new Exception("货号不是库存状态");
        }
        $goods->weixiu_status = WeixiuStatusEnum::APPLY;
        if(false === $goods->save()){
            throw new Exception($this->getError($goods));
        }
        $form->repair_status = RepairStatusEnum::APPLY;
        $form->audit_status = AuditStatusEnum::PENDING;
        if(false === $form->save()) {
            throw new \Exception($this->getError($form));
        }
    }

    /**
     * 维修审核
     * @param WarehouseBillRepairForm $form
     */
    public function auditRepair($form)
    {
        if (false === $form->validate()) {
            throw new \Exception($this->getError($form));
        }
        $goods = WarehouseGoods::find()->where(['goods_id'=>$form->goods_id])->one();
        if($form->audit_status == AuditStatusEnum::PASS){
            $goods->weixiu_status = WeixiuStatusEnum::ACCEPT;
            $form->repair_status = RepairStatusEnum::AFFIRM;
        }else{
            $goods->weixiu_status = WeixiuStatusEnum::SAVE;
            $form->repair_status = RepairStatusEnum::SAVE;
        }
        if(false === $goods->save()){
            throw new Exception($this->getError($goods));
        }
        if(false === $form->save()) {
            throw new \Exception($this->getError($form));
        }
    }
}
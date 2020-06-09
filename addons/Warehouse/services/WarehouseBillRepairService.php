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
        $form->predict_time = $this->getEndDay(time(), 3);
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
            $form->audit_status = AuditStatusEnum::PASS;
        }else{
            $goods->weixiu_status = WeixiuStatusEnum::SAVE;
            $form->repair_status = RepairStatusEnum::SAVE;
            $form->audit_status = AuditStatusEnum::SAVE;
        }
        if(false === $goods->save()){
            throw new Exception($this->getError($goods));
        }
        if(false === $form->save()) {
            throw new \Exception($this->getError($form));
        }
    }

    /**
     * 下单申请
     * @param WarehouseBillRepairForm $form
     */
    public function ordersRepair($form)
    {
        if (false === $form->validate()) {
            throw new \Exception($this->getError($form));
        }
        if($form->repair_status != RepairStatusEnum::AFFIRM){
            throw new Exception("单据不是确认状态");
        }
        $form->repair_status = RepairStatusEnum::ORDERS;
        $form->orders_time = time();//下单时间
        if(false === $form->save()) {
            throw new \Exception($this->getError($form));
        }
    }

    /**
     * 维修完毕
     * @param WarehouseBillRepairForm $form
     */
    public function finishRepair($form)
    {
        if (false === $form->validate()) {
            throw new \Exception($this->getError($form));
        }
        if($form->repair_status != RepairStatusEnum::ORDERS){
            throw new Exception("单据不是下单状态");
        }
        $form->repair_status = RepairStatusEnum::FINISH;
        $form->end_time = time();//完成时间
        if(false === $form->save()) {
            throw new \Exception($this->getError($form));
        }
    }

    /**
     * 收货
     * @param WarehouseBillRepairForm $form
     */
    public function receivingRepair($form)
    {
        if (false === $form->validate()) {
            throw new \Exception($this->getError($form));
        }
        if($form->repair_status != RepairStatusEnum::FINISH){
            throw new Exception("单据不是完毕状态");
        }
        $form->repair_status = RepairStatusEnum::RECEIVING;
        $form->receiving_time = time();//收货时间
        if(false === $form->save()) {
            throw new \Exception($this->getError($form));
        }
    }

    /**
     * 求取从某日起经过一定天数后的日期,
     * 排除周日
     * @param $start       开始日期
     * @param $offset      经过天数
     * @return
     *  examples:输入(2010-06-25,5),得到2010-07-02
     */
    public function getEndDay( $start='now', $offset=0){
        $tmptime = $start + 24*3600;
        while( $offset > 0 ){
            $weekday = date('w', $tmptime);
            if($weekday != 0){//不是周末
                $offset--;
            }
            $tmptime += 24*3600;
        }
        return $tmptime;
    }
}
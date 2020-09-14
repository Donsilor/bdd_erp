<?php

namespace addons\Warehouse\services;

use Yii;
use common\components\Service;
use addons\Warehouse\common\forms\WarehouseBillRepairForm;
use addons\Warehouse\common\models\WarehouseGoods;
use addons\Warehouse\common\models\WarehouseBillRepairLog;
use addons\Warehouse\common\enums\GoodsStatusEnum;
use addons\Warehouse\common\enums\WeixiuStatusEnum;
use addons\Warehouse\common\enums\QcStatusEnum;
use addons\Warehouse\common\enums\RepairStatusEnum;
use addons\Style\common\enums\LogTypeEnum;
use common\enums\AuditStatusEnum;
use common\helpers\DateHelper;
use common\helpers\Url;
use yii\base\Exception;

/**
 * 维修单
 * @package services\common
 * @author jianyan74 <751393839@qq.com>
 */
class WarehouseBillRepairService extends Service
{

    /**
     * 维修单详情 tab
     * @param int $repair_id 维修
     * @param string $returnUrl
     * @return array
     */
    public function menuTabList($repair_id, $returnUrl = null)
    {
        return [
            1=>['name'=>'维修单详情','url'=>Url::to(['bill-repair/view','id'=>$repair_id,'tab'=>1,'returnUrl'=>$returnUrl])],
            2=>['name'=>'维修日志','url'=>Url::to(['bill-repair-log/index','repair_id'=>$repair_id,'tab'=>2,'returnUrl'=>$returnUrl])],
        ];
    }

    /**
     * 创建维修单
     * @param WarehouseBillRepairForm $form
     * @throws
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
        $form->predict_time = DateHelper::getEndDay(time(), 3);
        if(false === $form->save()){
            throw new Exception($this->getError($form));
        }
        $log_msg = "创建维修出库单";
        $log = [
            'repair_id' => $form->id,
            'log_type' => LogTypeEnum::ARTIFICIAL,
            'log_msg' => $log_msg,
        ];
        $this->createRepairLog($log);
    }

    /**
     * 维修单-取消
     * @param WarehouseBillRepairForm $form
     * @throws \Exception
     */
    public function cancelRepair($form)
    {
        $goods = WarehouseGoods::find()->where(['goods_id'=>$form->goods_id])->one() ?? new WarehouseGoods();
        $goods->weixiu_status = WeixiuStatusEnum::SAVE;
        if(false === $goods->save()){
            throw new Exception("更新货品维修状态失败");
        }
        $form->repair_status = RepairStatusEnum::CANCEL;
        if(false === $form->save()) {
            throw new \Exception($this->getError($form));
        }
    }

    /**
     * 维修申请
     * @param WarehouseBillRepairForm $form
     * @throws
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
        $log_msg = "维修申请";
        $log = [
            'repair_id' => $form->id,
            'log_type' => LogTypeEnum::ARTIFICIAL,
            'log_msg' => $log_msg,
        ];
        $this->createRepairLog($log);
    }

    /**
     * 维修审核
     * @param WarehouseBillRepairForm $form
     * @throws
     */
    public function auditRepair($form)
    {
        if (false === $form->validate()) {
            throw new \Exception($this->getError($form));
        }
        $goods = WarehouseGoods::find()->where(['goods_id'=>$form->goods_id])->one();
        if($form->audit_status == AuditStatusEnum::PASS){
            $goods->weixiu_status = WeixiuStatusEnum::ACCEPT;
            $form->repair_status = RepairStatusEnum::FINISHED;
            $form->audit_status = AuditStatusEnum::PASS;
        }else{
            $goods->weixiu_status = WeixiuStatusEnum::SAVE;
            $form->repair_status = RepairStatusEnum::SAVE;
            $form->audit_status = AuditStatusEnum::SAVE;
        }
        if(false === $goods->save()){
            throw new Exception($this->getError($goods));
        }

        //插入商品日志
        $log = [
            'goods_id' => $goods->id,
            'goods_status' => $goods->goods_status,
            'log_type' => LogTypeEnum::ARTIFICIAL,
            'log_msg' => '维修单：'.$form->repair_no.";维修状态:“".WeixiuStatusEnum::getValue(WeixiuStatusEnum::SAVE)."”变更为：“".WeixiuStatusEnum::getValue(WeixiuStatusEnum::ACCEPT)."”"
        ];
        Yii::$app->warehouseService->goodsLog->createGoodsLog($log);



        if(false === $form->save()) {
            throw new \Exception($this->getError($form));
        }
        $log_msg = "维修审核";
        $log = [
            'repair_id' => $form->id,
            'log_type' => LogTypeEnum::ARTIFICIAL,
            'log_msg' => $log_msg,
        ];
        $this->createRepairLog($log);
    }

    /**
     * 下单申请
     * @param WarehouseBillRepairForm $form
     * @throws
     */
    public function ordersRepair($form)
    {
        if (false === $form->validate()) {
            throw new \Exception($this->getError($form));
        }
        if($form->repair_status != RepairStatusEnum::FINISHED){
            throw new Exception("单据不是确认状态");
        }
        $form->repair_status = RepairStatusEnum::ORDERS;
        $form->orders_time = time();//下单时间
        if(false === $form->save()) {
            throw new \Exception($this->getError($form));
        }
        $log_msg = "下单申请";
        $log = [
            'repair_id' => $form->id,
            'log_type' => LogTypeEnum::ARTIFICIAL,
            'log_msg' => $log_msg,
        ];
        $this->createRepairLog($log);
    }

    /**
     * 维修完毕
     * @param WarehouseBillRepairForm $form
     * @throws
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
        $log_msg = "维修完毕";
        $log = [
            'repair_id' => $form->id,
            'log_type' => LogTypeEnum::ARTIFICIAL,
            'log_msg' => $log_msg,
        ];
        $this->createRepairLog($log);
    }

    /**
     * 收货
     * @param WarehouseBillRepairForm $form
     * @throws
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
        $log_msg = "维修收货";
        $log = [
            'repair_id' => $form->id,
            'log_type' => LogTypeEnum::ARTIFICIAL,
            'log_msg' => $log_msg,
        ];
        $this->createRepairLog($log);
    }

    /**
     * 维修日志
     * @param array $log
     * @throws \Exception
     * @return object $model
     */
    public function createRepairLog($log){

        $model = new WarehouseBillRepairLog();
        $model->attributes = $log;
        if(false === $model->save()){
            throw new \Exception($this->getError($model));
        }
        return $model;
    }

    /**
     * 货品是否维修中
     * @param WarehouseGoods $goods
     * @throws \Exception
     * @return object $model
     */
    public function checkRepairStatus($goods){

        if($goods->weixiu_status != WeixiuStatusEnum::SAVE){
            throw new \Exception($goods->goods_id."货号维修中");
        }

        return $goods;
    }
}
<?php

namespace addons\Warehouse\services;

use common\enums\AuditStatusEnum;
use Yii;
use common\components\Service;
use common\helpers\SnHelper;
use addons\Warehouse\common\models\WarehouseStoneBill;
use addons\Warehouse\common\models\WarehouseStoneBillGoods;
use addons\Warehouse\common\enums\BillStatusEnum;
use common\helpers\ArrayHelper;
use common\helpers\Url;

/**
 * 工厂退石单（退石单）
 * @package services\common
 * @author jianyan74 <751393839@qq.com>
 */
class WarehouseStoneBillTsService extends Service
{

    /**
     * 创建退石单（退石单）
     * @param array $bill
     * @param array $details
     */
    public function createBillTs($bill,$details)
    {
        $billM = new WarehouseStoneBill();
        $billM->attributes = $bill;
        $billM->bill_no = SnHelper::createBillSn($billM->bill_type);
        $billM->bill_status = BillStatusEnum::SAVE;
        if(false === $billM->save()){
            throw new \Exception($this->getError($billM));
        }
        $goodsM = new WarehouseStoneBillGoods();
        foreach ($details as &$good){
            $good['bill_id'] = $billM->id;
            $good['bill_type'] = $billM->bill_type;
            $good['bill_no'] = $billM->bill_no;
            $goodsM->setAttributes($good);
            if(!$goodsM->validate()){
                throw new \Exception($this->getError($goodsM));
            }
        }
        $details = ArrayHelper::toArray($details);
        $value = [];
        $key = array_keys($details[0]);
        foreach ($details as $detail) {
            $value[] = array_values($detail);
        }
        $res = Yii::$app->db->createCommand()->batchInsert(WarehouseStoneBillGoods::tableName(), $key, $value)->execute();
        if(false === $res){
            throw new \Exception("创建领石单明细失败");
        }
        Yii::$app->warehouseService->stoneBill->stoneBillSummary($billM->id);
        return $billM;
    }

    /**
     * 退石单-审核
     * @param $form
     */
    public function auditBillTs($form)
    {
        if(false === $form->validate()) {
            throw new \Exception($this->getError($form));
        }

        if($form->audit_status == AuditStatusEnum::PASS){
            $form->bill_status = BillStatusEnum::CONFIRM;
        }else{
            $form->bill_status = BillStatusEnum::SAVE;
        }

        if(false === $form->save()) {
            throw new \Exception($this->getError($form));
        }
    }
}
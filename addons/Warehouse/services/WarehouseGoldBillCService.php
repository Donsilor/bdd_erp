<?php

namespace addons\Warehouse\services;

use Yii;
use common\components\Service;
use common\helpers\SnHelper;
use addons\Warehouse\common\models\WarehouseGoldBill;
use addons\Warehouse\common\models\WarehouseGoldBillGoods;
use common\helpers\ArrayHelper;

/**
 * 领料单
 * @package services\common
 * @author jianyan74 <751393839@qq.com>
 */
class WarehouseGoldBillCService extends Service
{
    /**
     * 创建领料单
     * @param array $bill
     * @param array $details
     * @throws
     */
    public function createGoldC($bill, $details){
        $billM = new WarehouseGoldBill();
        $billM->attributes = $bill;
        $billM->bill_no = SnHelper::createBillSn($billM->bill_type);
        if(false === $billM->save()){
            throw new \Exception($this->getError($billM));
        }
        $goodsM = new WarehouseGoldBillGoods();
        foreach ($details as &$good){
            $good['bill_id'] = $billM->id;
            $good['bill_no'] = $billM->bill_no;
            $good['bill_type'] = $billM->bill_type;
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
        $res = \Yii::$app->db->createCommand()->batchInsert(WarehouseGoldBillGoods::tableName(), $key, $value)->execute();
        if(false === $res){
            throw new \Exception("创建领料单明细失败");
        }
        //单据汇总
        \Yii::$app->warehouseService->goldBill->goldBillSummary($billM->id);
        return $billM;
    }

}
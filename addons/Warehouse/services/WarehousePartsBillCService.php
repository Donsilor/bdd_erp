<?php

namespace addons\Warehouse\services;

use Yii;
use common\components\Service;
use common\helpers\SnHelper;
use addons\Warehouse\common\models\WarehousePartsBill;
use addons\Warehouse\common\models\WarehousePartsBillGoods;
use common\helpers\ArrayHelper;

/**
 * 领件单
 * @package services\common
 * @author jianyan74 <751393839@qq.com>
 */
class WarehousePartsBillCService extends Service
{
    /**
     * 创建领件单
     * @param array $bill
     * @param array $details
     * @throws
     */
    public function createPartsC($bill, $details){
        $billM = new WarehousePartsBill();
        $billM->attributes = $bill;
        $billM->bill_no = SnHelper::createBillSn($billM->bill_type);
        if(false === $billM->save()){
            throw new \Exception($this->getError($billM));
        }
        $goodsM = new WarehousePartsBillGoods();
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
        $res = \Yii::$app->db->createCommand()->batchInsert(WarehousePartsBillGoods::tableName(), $key, $value)->execute();
        if(false === $res){
            throw new \Exception("创建领件单明细失败");
        }
        //单据汇总
        \Yii::$app->warehouseService->partsBill->partsBillSummary($billM->id);
        return $billM;
    }

}
<?php

namespace addons\Warehouse\services;

use Yii;
use addons\Warehouse\common\models\WarehouseBill;
use addons\Warehouse\common\models\WarehouseBillGoods;
use common\helpers\SnHelper;

/**
 * 销售退货单
 * @package services\common
 * @author jianyan74 <751393839@qq.com>
 */
class WarehouseBillDService extends WarehouseBillService
{

    /**
     * 创建销售退货单
     *
     * @param array $bill
     * @param array $goods
     * @throws
     */
    public function createBillD($bill, $goods){
        $billM = new WarehouseBill();
        $billM->attributes = $bill;
        $billM->bill_no = SnHelper::createBillSn($billM->bill_type);
        if(false === $billM->save()){
            throw new \Exception($this->getError($billM));
        }
        $bill_id = $billM->attributes['id'];
        $goodsM = new WarehouseBillGoods();
        foreach ($goods as $k => &$good){
            $good['bill_id'] = $bill_id;
            $good['bill_no'] = $billM->bill_no;
            $good['bill_type'] = $billM->bill_type;
            $goodsM->setAttributes($good);
            if(!$goodsM->validate()){
                throw new \Exception($this->getError($goodsM));
            }
        }
        $value = [];
        $key = array_keys($goods[0]);
        foreach ($goods as $item) {
            $value[] = array_values($item);
            if(count($value)>10){
                $res = Yii::$app->db->createCommand()->batchInsert(WarehouseBillGoods::tableName(), $key, $value)->execute();
                if(false === $res){
                    throw new \Exception("创建销售退货单据明细失败1");
                }
                $value = [];
            }
        }
        if(!empty($value)){
            $res = Yii::$app->db->createCommand()->batchInsert(WarehouseBillGoods::tableName(), $key, $value)->execute();
            if(false === $res){
                throw new \Exception("创建销售退货单据明细失败2");
            }
        }
        return $billM;
    }
}
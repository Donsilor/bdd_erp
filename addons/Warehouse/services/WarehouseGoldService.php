<?php

namespace addons\Warehouse\services;

use addons\Warehouse\common\forms\WarehouseGoldBillLGoodsForm;
use addons\Warehouse\common\models\WarehouseGold;
use Yii;
use addons\Warehouse\common\models\WarehouseStone;
use addons\Warehouse\common\models\WarehouseStoneBillGoods;
use common\components\Service;

/**
 * Class TypeService
 * @package services\common
 * @author jianyan74 <751393839@qq.com>
 */
class WarehouseGoldService extends Service
{
    /**
     * 创建/编辑-金料信息
     * @param $form
     */
    public function editGold($form)
    {
        $gold = WarehouseGoldBillLGoodsForm::find()->where(['bill_id'=>$form->id])->all();
        foreach ($gold as $detail){
            $goods = WarehouseGold::findOne(['gold_name'=>$detail->gold_name]);
            if(!$goods){
                $goldM = new WarehouseGold();
                $good = [
                    'gold_name' => $detail->gold_name,
                    'gold_type' => $detail->gold_type,
                    'gold_num' => $detail->gold_num,
                    'gold_weight' => $detail->gold_weight,
                    'cost_price' => $detail->cost_price,
                    'sale_price' => $detail->sale_price
                ];
                $goldM->attributes = $good;
                if(false === $goldM->save()){
                    throw new \Exception($this->getError($goldM));
                }
            }else{
                $goods->gold_num = bcadd($goods->gold_num, $detail->gold_num);
                $goods->gold_weight = bcadd($goods->gold_weight, $detail->gold_weight, 2);
                //$goods->cost_price = bcadd($goods->cost_price, $detail->cost_price, 2);
                //$goods->sale_price = bcadd($goods->sale_price, $detail->sale_price, 2);
                if(false === $goods->save()){
                    throw new \Exception($this->getError($goods));
                }
            }
        }
    }
}
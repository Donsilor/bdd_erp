<?php
/**
 * Created by PhpStorm.
 * User: BDD
 * Date: 2019/12/7
 * Time: 13:53
 */

namespace addons\Supply\services;


use addons\Supply\common\models\ProduceGold;
use addons\Supply\common\models\ProduceGoldGoods;
use common\components\Service;
use addons\Warehouse\common\models\WarehouseGold;
use addons\Warehouse\common\enums\GoldStatusEnum;
use addons\Warehouse\common\enums\AdjustTypeEnum;

class ProduceGoldService extends Service
{
    public $switchQueue = false;
    
    public function queue($switchQueue = true)
    {
        $this->switchQueue = $switchQueue;
        return $this;
    }
    /**
     * 批量配料
     * @param string $data
     * [
     *   1=>[
     *     'remark'=>'配料备注',
     *     'ProduceGoldGoods'=>['gold_sn'=>'金料编号','gold_weight'=>'金料总重']
     *   ],
     *   2=>[
     *     'remark'=>'配料备注',
     *     'ProduceGoldGoods'=>['gold_sn'=>'金料编号','gold_weight'=>'金料总重']
     *   ]
     * ]
     */
    public function batchPeiliao($data)
    {
        foreach ($data as $id => $goldData) {
            
            $gold = ProduceGold::find()->where(['id'=>$id])->one();
            if(!$gold) {
                throw new \Exception("(ID={$id})配料单查询失败");
            }
            $gold->attributes = $goldData;
            if(false === $gold->save()) {
                throw new \Exception($this->getError($gold));
            }
            ProduceGoldGoods::deleteAll(['id'=>$id]);
            foreach ($goldData['ProduceGoldGoods'] as $goldGoodsData) {                
                $goldGoods = new ProduceGoldGoods();
                $goldGoods->attributes = $goldGoodsData;
                $goldGoods->id = $id;
                if(false === $goldGoods->save()) {
                    throw new \Exception("(ID={$id})".$this->getError($goldGoods));
                }
                //金料减库存
                Yii::$app->warehouseService->gold->adjustGoldStock($goldGoods->gold_sn, $goldGoods->gold_weight, AdjustTypeEnum::MINUS);                
            }
        }
    }
}
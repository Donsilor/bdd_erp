<?php
/**
 * Created by PhpStorm.
 * User: BDD
 * Date: 2019/12/7
 * Time: 13:53
 */

namespace addons\Supply\services;

use Yii;
use addons\Supply\common\models\ProduceGold;
use addons\Supply\common\models\ProduceGoldGoods;
use common\components\Service;
use addons\Warehouse\common\models\WarehouseGold;
use addons\Warehouse\common\enums\GoldStatusEnum;
use addons\Warehouse\common\enums\AdjustTypeEnum;
use addons\Supply\common\enums\PeiliaoStatusEnum;

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
            $gold->peiliao_time = time();
            $gold->peiliao_user = Yii::$app->user->identity->username;
            $gold->peiliao_status = PeiliaoStatusEnum::HAS_PEILIAO;
            if(false === $gold->save()) {
                throw new \Exception($this->getError($gold));
            }
            
            //金料校验 begin
            foreach ($goldData['ProduceGoldGoods'] as $goldGoodsData) {
                $goldGoods = new ProduceGoldGoods();
                $goldGoods->attributes = $goldGoodsData;
                $goldGoods->id = $id;
                if(false === $goldGoods->validate()) {
                    throw new \Exception("(ID={$id})".$this->getError($goldGoods));
                }elseif(!$goldGoods->gold) {
                    throw new \Exception("({$goldGoods->gold_sn})金料编号不存在");
                }elseif($goldGoods->gold->gold_status != GoldStatusEnum::IN_STOCK ) {
                    throw new \Exception("({$goldGoods->gold->gold_sn})金料编号不是库存状态");
                }elseif($goldGoods->gold_weight > $goldGoods->gold->gold_weight) {
                    throw new \Exception("(ID={$id})领取数量不能超过金料剩余重量({$goldGoods->gold->gold_weight})");
                }elseif($gold->gold_type != ($gold_type = Yii::$app->attr->valueName($goldGoods->gold->gold_type))) {
                    if(preg_match("/铂|PT/is", $gold->gold_type)) {
                        if (!preg_match("/铂|PT/is",$gold_type)){
                            throw new \Exception("(ID={$id})金料类型不匹配(需要配铂金)");
                        }
                    }elseif(preg_match("/黄金|足金/is", $gold->gold_type)) {
                        if (!preg_match("/黄金|足金/is",$gold_type)){
                            throw new \Exception("(ID={$id})金料类型不匹配(需要配黄金)");
                        }
                    }elseif(preg_match("/银/is", $gold->gold_type)) {
                        if (!preg_match("/银/is",$gold_type)){
                            throw new \Exception("(ID={$id})金料类型不匹配(需要配足银)");
                        }
                    }else{
                        throw new \Exception("(ID={$id})暂不支持当前金料类型");
                    }
                }
            }//金料校验 end

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
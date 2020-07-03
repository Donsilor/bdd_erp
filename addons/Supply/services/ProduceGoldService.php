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
        $produce_sns = [];
        foreach ($data as $id => $goldData) {
            //1.更新配料状态
            $gold = ProduceGold::find()->where(['id'=>$id])->one();
            if(!$gold) {
                throw new \Exception("(ID={$id})配料单查询失败");
            }
            $gold->attributes = $goldData;
            $gold->peiliao_time = time();
            $gold->peiliao_user = Yii::$app->user->identity->username;
            $gold->peiliao_status = PeiliaoStatusEnum::HAS_PEILIAO;
            //如果绑定了 领料单
            if($gold->delivery_no) {
                $gold->peiliao_status = PeiliaoStatusEnum::TO_LINGLIAO;
            }else{
                $gold->peiliao_status = PeiliaoStatusEnum::HAS_PEILIAO;
            }
            if(false === $gold->save()) {
                throw new \Exception($this->getError($gold));
            }
            $produce_sns[$gold->produce_sn] = $gold->produce_sn;
            //2.还原金料库存
            if($gold->goldGoods) {
                foreach ($gold->goldGoods as $goldGoods){
                    Yii::$app->warehouseService->gold->adjustGoldStock($goldGoods->gold_sn,$goldGoods->gold_weight, AdjustTypeEnum::ADD);
                }
            }
            //3.删除配料信息
            ProduceGoldGoods::deleteAll(['id'=>$id]);
            //4.金料校验 begin
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
                    throw new \Exception("(ID={$id})金料领取数量不能超过剩余总重({$goldGoods->gold->gold_weight}g)");
                }elseif($gold->gold_type != ($gold_type = Yii::$app->attr->valueName($goldGoods->gold->gold_type))) {
                    if(preg_match("/铂|PT/is", $gold->gold_type)) {
                        if (!preg_match("/铂|PT/is",$gold_type)){
                            throw new \Exception("(ID={$id})金料类型不一致(需要配铂金)");
                        }
                    }elseif(preg_match("/黄金|足金/is", $gold->gold_type)) {
                        if (!preg_match("/黄金|足金/is",$gold_type)){
                            throw new \Exception("(ID={$id})金料类型不一致(需要配黄金)");
                        }
                    }elseif(preg_match("/银|Ag/is", $gold->gold_type)) {
                        if (!preg_match("/银|Ag/is", $gold_type)){
                            throw new \Exception("(ID={$id})金料类型不一致(需要配足银)");
                        }
                    }else {
                        throw new \Exception("(ID={$id})金料类型不一致(暂不支持当前金料类型)");
                    }
                }
            }

            //5.新增金料配料信息
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
        
        //同步更新布产单配料状态
        if(!empty($produce_sns)) {            
            Yii::$app->supplyService->produce->autoPeiliaoStatus($produce_sns);  
        }
    }
}
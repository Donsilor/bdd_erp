<?php
/**
 * Created by PhpStorm.
 * User: BDD
 * Date: 2019/12/7
 * Time: 13:53
 */

namespace addons\Supply\services;

use Yii;
use addons\Supply\common\models\ProduceStone;
use addons\Supply\common\models\ProduceStoneGoods;
use common\components\Service;
use addons\Warehouse\common\enums\AdjustTypeEnum;
use addons\Supply\common\enums\PeishiStatusEnum;
use addons\Warehouse\common\enums\StoneStatusEnum;
use addons\Supply\common\models\Produce;

class ProduceStoneService extends Service
{
    public $switchQueue = false;
    
    public function queue($switchQueue = true)
    {
        $this->switchQueue = $switchQueue;
        return $this;
    }
    /**
     * 批量配石
     * @param string $data
     * [
     *   1=>[
     *     'remark'=>'配石备注',
     *     'ProduceStoneGoods'=>['stone_sn'=>'石包编号','stone_num'=>'配石数量','stone_weight'=>'配石总重']
     *   ],
     *   2=>[
     *     'remark'=>'配石备注',
     *     'ProduceStoneGoods'=>['stone_sn'=>'石包编号','stone_num'=>'配石数量','stone_weight'=>'配石总重']
     *   ]
     * ]
     */
    public function batchPeishi($data)
    {   
        $produce_sns = [];
        foreach ($data as $id => $stoneData) {
            //1更新配石状态
            $stone = ProduceStone::find()->where(['id'=>$id])->one();
            if(!$stone) {
                throw new \Exception("(ID={$id})配石单查询失败");
            }
            $stone->attributes = $stoneData;            
            $stone->peishi_time = time();
            $stone->peishi_user = Yii::$app->user->identity->username;            
            //如果绑定了 领石单
            if($stone->delivery_no) {
                $stone->peishi_status = PeishiStatusEnum::TO_LINGSHI;
            }else{
                $stone->peishi_status = PeishiStatusEnum::HAS_PEISHI;
            }
            if(false === $stone->save()) {
                 throw new \Exception($this->getError($stone));
            }
            $produce_sns[$stone->produce_sn] = $stone->produce_sn;            
            //2.还原石料库存
            if($stone->stoneGoods) {
                foreach ($stone->stoneGoods as $stoneGoods){                    
                    //石料库存还原
                    Yii::$app->warehouseService->stone->adjustStoneStock($stoneGoods->stone_sn, $stoneGoods->stone_num, $stoneGoods->stone_weight, AdjustTypeEnum::ADD);
                }
            }
            //3.删除配石信息
            ProduceStoneGoods::deleteAll(['id'=>$id]);
            
            //4.石包校验 begin
            $stone_num = 0;
            foreach ($stoneData['ProduceStoneGoods'] as $stoneGoodsData) {
                $stoneGoods = new ProduceStoneGoods();
                $stoneGoods->attributes = $stoneGoodsData;
                $stoneGoods->id = $id;
                if(false === $stoneGoods->validate()) {
                    throw new \Exception("(ID={$id})".$this->getError($stoneGoods));
                }elseif(!$stoneGoods->stone) {
                    throw new \Exception("({$stoneGoods->stone_sn})石包号不存在");
                }elseif($stoneGoods->stone->stone_status != StoneStatusEnum::IN_STOCK ) {
                    throw new \Exception("({$stoneGoods->stone->stone_sn})石包号不是库存状态");
                }elseif($stone->stone_type != ($stone_type = Yii::$app->attr->valueName($stoneGoods->stone->stone_type))) {
                    throw new \Exception("(ID={$id})石料类型不匹配(需要配{$stone->stone_type})");
                }elseif($stoneGoods->stone_num > $stoneGoods->stone->stock_cnt) {
                    throw new \Exception("(ID={$id})领取数量不能超过石包剩余数量({$stoneGoods->stone->stock_cnt})");
                }
                $stone_num += $stoneGoods->stone_num;
            }          
            if($stone_num < $stone->stone_num) {
                throw new \Exception("(ID={$id})石料领取总数量不能小于{$stone->stone_num}");
            }
            //5.新增配石信息
            foreach ($stoneData['ProduceStoneGoods'] as $stoneGoodsData) {
                 $stoneGoods = new ProduceStoneGoods();                     
                 $stoneGoods->attributes = $stoneGoodsData;
                 $stoneGoods->id = $id;                 
                 if(false === $stoneGoods->save()) {
                     throw new \Exception("(ID={$id})".$this->getError($stoneGoods));
                 }
                 //石料库存变动
                 Yii::$app->warehouseService->stone->adjustStoneStock($stoneGoods->stone_sn, $stoneGoods->stone_num, $stoneGoods->stone_weight, AdjustTypeEnum::MINUS);
            }
            
        }
        //同步更新布产单配石状态
        if(!empty($produce_sns)) {
            Yii::$app->supplyService->produce->autoPeishiStatus($produce_sns);
        }
    }
}
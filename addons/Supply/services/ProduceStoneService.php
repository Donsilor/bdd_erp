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
        foreach ($data as $id => $stoneData) {
            $stone = ProduceStone::find()->where(['id'=>$id])->one();
            if(!$stone) {
                throw new \Exception("(ID={$id})配石单查询失败");
            }
            $stone->attributes = $stoneData;
            $stone->peishi_time = time();
            $stone->peishi_user = Yii::$app->user->identity->username;
            $stone->peishi_status = PeishiStatusEnum::HAS_PEISHI;
            if(false === $stone->save()) {
                 throw new \Exception($this->getError($stone));
            }
            ProduceStoneGoods::deleteAll(['id'=>$id]);
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
    }
}
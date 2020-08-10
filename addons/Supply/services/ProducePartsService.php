<?php
/**
 * Created by PhpStorm.
 * User: BDD
 * Date: 2019/12/7
 * Time: 13:53
 */

namespace addons\Supply\services;

use Yii;
use common\components\Service;
use addons\Supply\common\models\ProduceParts;
use addons\Supply\common\models\ProducePartsGoods;
use addons\Supply\common\enums\PeijianStatusEnum;
use addons\Warehouse\common\enums\PartsStatusEnum;
use addons\Warehouse\common\enums\AdjustTypeEnum;

class ProducePartsService extends Service
{
    public $switchQueue = false;
    
    public function queue($switchQueue = true)
    {
        $this->switchQueue = $switchQueue;
        return $this;
    }
    /**
     * 批量配料
     * @param array $data
     * [
     *   1=>[
     *     'remark'=>'配件备注',
     *     'ProducePartsGoods'=>['parts_sn'=>'配件编号','parts_weight'=>'配件总重']
     *   ],
     *   2=>[
     *     'remark'=>'配料备注',
     *     'ProducePartsGoods'=>['parts_sn'=>'配件编号','parts_weight'=>'配件总重']
     *   ]
     * ]
     * @throws
     */
    public function batchPeijian($data)
    {
        $produce_sns = [];
        foreach ($data as $id => $partsData) {
            //1.更新配件状态
            $parts = ProduceParts::find()->where(['id'=>$id])->one();
            if(!$parts) {
                throw new \Exception("(ID={$id})配件单查询失败");
            }
            $parts->attributes = $partsData;
            $parts->peijian_time = time();
            $parts->peijian_user = Yii::$app->user->identity->username;
            $parts->peijian_status = PeijianStatusEnum::HAS_PEIJIAN;
            //如果绑定了 领件单
            if($parts->delivery_no) {
                $parts->peijian_status = PeijianStatusEnum::HAS_PEIJIAN;
            }else{
                $parts->peijian_status = PeijianStatusEnum::HAS_PEIJIAN;
            }
            if(false === $parts->save()) {
                throw new \Exception($this->getError($parts));
            }
            $produce_sns[$parts->produce_sn] = $parts->produce_sn;
            //2.还原配件库存
            if($parts->partsGoods) {
                foreach ($parts->partsGoods as $partsGoods){
                    Yii::$app->warehouseService->parts->adjustPartsStock($partsGoods->parts_sn, $partsGoods->parts_num, $partsGoods->parts_weight, AdjustTypeEnum::ADD);
                }
            }
            //3.删除配件信息
            ProducePartsGoods::deleteAll(['id'=>$id]);
            //4.配件校验 begin
            foreach ($partsData['ProducePartsGoods'] as $partsGoodsData) {
                $partsGoods = new ProducePartsGoods();
                $partsGoods->attributes = $partsGoodsData;
                $partsGoods->id = $id;
                if(false === $partsGoods->validate()) {
                    throw new \Exception("(ID={$id})".$this->getError($partsGoods));
                }elseif(!$partsGoods->parts) {
                    throw new \Exception("({$partsGoods->parts_sn})配件编号不存在");
                }elseif($partsGoods->parts->parts_status != PartsStatusEnum::IN_STOCK ) {
                    throw new \Exception("({$partsGoods->parts->parts_sn})配件编号不是库存状态");
                }elseif($partsGoods->parts_num > $partsGoods->parts->parts_num) {
                    throw new \Exception("(ID={$id})配件领取数量不能超过剩余总重({$partsGoods->parts->parts_num})");
                }elseif($parts->parts_type != ($parts_type = Yii::$app->attr->valueName($partsGoods->parts->parts_type))) {
                    /*if(preg_match("/铂|PT/is", $parts->parts_type)) {
                        if (!preg_match("/铂|PT/is",$gold_type)){
                            throw new \Exception("(ID={$id})金料类型不一致(需要配铂金)");
                        }
                    }elseif(preg_match("/黄金|足金/is", $parts->gold_type)) {
                        if (!preg_match("/黄金|足金/is",$gold_type)){
                            throw new \Exception("(ID={$id})金料类型不一致(需要配黄金)");
                        }
                    }elseif(preg_match("/银|Ag/is", $parts->gold_type)) {
                        if (!preg_match("/银|Ag/is", $gold_type)){
                            throw new \Exception("(ID={$id})金料类型不一致(需要配足银)");
                        }
                    }else {
                        throw new \Exception("(ID={$id})金料类型不一致(暂不支持当前金料类型)");
                    }*/
                }
            }

            //5.新增配件信息
            foreach ($partsData['ProducePartsGoods'] as $partsGoodsData) {
                $partsGoods = new ProducePartsGoods();
                $partsGoods->attributes = $partsGoodsData;
                $partsGoods->id = $id;
                if(false === $partsGoods->save()) {
                    throw new \Exception("(ID={$id})".$this->getError($partsGoods));
                }
                //配件减库存
                Yii::$app->warehouseService->parts->adjustPartsStock($partsGoods->parts_sn, $partsGoods->parts_num, $partsGoods->parts_weight, AdjustTypeEnum::MINUS);
            }
        }
        
        //同步更新布产单配件状态
        if(!empty($produce_sns)) {            
            Yii::$app->supplyService->produce->autoPeijianStatus($produce_sns);
        }
    }
}
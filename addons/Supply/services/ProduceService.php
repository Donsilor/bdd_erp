<?php
/**
 * Created by PhpStorm.
 * User: BDD
 * Date: 2019/12/7
 * Time: 13:53
 */

namespace addons\Supply\services;

use addons\Supply\common\models\Factory;
use addons\Supply\common\models\Produce;
use addons\Supply\common\models\ProduceAttribute;
use common\components\Service;
use common\enums\StatusEnum;
use common\helpers\ArrayHelper;
use common\helpers\SnHelper;
use common\helpers\Url;
use yii\base\Exception;
use addons\Purchase\common\models\PurchaseGoods;


class ProduceService extends Service
{
    public $switchQueue = false;
    
    public function queue($switchQueue = true)
    {
        $this->switchQueue = $switchQueue;
        return $this;
    }

    /**
     * 布产编辑 tab
     * @param int $id 款式ID
     * @return array
     */
    public function menuTabList($produce_id,$returnUrl = null)
    {
        return [
            1=>['name'=>'基础信息','url'=>Url::to(['produce/view','id'=>$produce_id,'tab'=>1,'returnUrl'=>$returnUrl])],
            5=>['name'=>'日志信息','url'=>Url::to(['produce-log/index','produce_id'=>$produce_id,'tab'=>5,'returnUrl'=>$returnUrl])]
        ];
    }
    /**
     * 创建布产单
     * @return array
     */
    public function createProduce($goods, $attr_list){
        
        $produce = new Produce();
        $produce->attributes = $goods;
        $produce->produce_sn = SnHelper::createProduceSn();
        if(false === $produce->save()){
            throw new \Exception($this->getError($produce));
        }        
        $produce_id = $produce->id;
        foreach ($attr_list as $attr){
            $produceAttr = new ProduceAttribute();
            $produceAttr->attributes = $attr;
            $produceAttr->produce_id = $produce_id;
            if(false === $produceAttr->save()){
                throw new \Exception($this->getError($produceAttr));
            }
        }        
        return $produce ;
    }
    


}
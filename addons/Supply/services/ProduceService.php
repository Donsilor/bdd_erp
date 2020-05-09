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
use yii\base\Exception;


class ProduceService extends Service
{
    /**
     *
     * @return array
     */
    public function createProduce($goods, $attrs ,$from_type ){
        $produce = new Produce();
        $produce->attributes = $goods;
        $produce->merchant_id = 1;
        $produce->produce_sn = SnHelper::createProduceSn();
        $produce->from_type = $from_type;
        if(!$produce->save()){
            throw new \Exception($this->getError($produce));
        }
        $produce_id = $produce->id;
        foreach ($attrs as $attr){
            $produce_attr = new ProduceAttribute();
            $produce_attr->attributes = $attr;
            $produce_attr->produce_id = $produce_id;
            if($produce_attr->validate() == false || $produce_attr->save() == false){
                throw new  \Exception($this->getError($produce_attr));
            }
        }


    }


}
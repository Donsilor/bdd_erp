<?php
/**
 * Created by PhpStorm.
 * User: BDD
 * Date: 2019/12/7
 * Time: 13:53
 */

namespace addons\Supply\services;

use addons\Style\common\enums\LogTypeEnum;
use addons\Supply\common\models\Factory;
use addons\Supply\common\models\Produce;
use addons\Supply\common\models\ProduceAttribute;
use addons\Supply\common\models\ProduceLog;
use addons\Supply\common\models\ProduceShipment;
use common\components\Service;
use common\enums\StatusEnum;
use common\helpers\ArrayHelper;
use common\helpers\SnHelper;
use common\helpers\Url;
use yii\base\Exception;
use addons\Purchase\common\models\PurchaseGoods;
use addons\Style\common\enums\AttrIdEnum;


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
            2=>['name'=>'出厂信息','url'=>Url::to(['produce-shipment/index','produce_id'=>$produce_id,'tab'=>2,'returnUrl'=>$returnUrl])],
            5=>['name'=>'日志信息','url'=>Url::to(['produce-log/index','produce_id'=>$produce_id,'tab'=>5,'returnUrl'=>$returnUrl])]
        ];
    }
    /**
     * 创建布产单
     * @return array
     */
    public function createProduce($goods, $attr_list){
       
        $produce_id = $goods['id'] ?? 0;
        $is_new = true;
        if($produce_id) {
            $is_new = false;
            $produce = Produce::findOne($produce_id);
            if(!$produce) {
                throw new \Exception("[{$produce_id}]布产单查询失败");
            }
        }else {        
            $produce = new Produce();
            $produce->produce_sn = SnHelper::createProduceSn();
        }
        $produce->attributes = $goods;   
        
        if(false === $produce->save()){            
            throw new \Exception($this->getError($produce));
        }
        
        ProduceAttribute::deleteAll(['produce_id'=>$produce_id]);
        foreach ($attr_list as $attr){
            $produceAttr = new ProduceAttribute();
            $produceAttr->attributes = $attr;
            $produceAttr->produce_id = $produce->id;
            if(false === $produceAttr->save()){
                throw new \Exception($this->getError($produceAttr));
            }
            if($produceAttr->attr_id == AttrIdEnum::INLAY_METHOD) {
                $produce->inlay_type = $produceAttr->attr_value_id;
            }
        }
        $produce->follower_name = $produce->follower->username;
        //更新布产单属性到布产单横向字段
        if(false === $produce->save(true)) {
            throw new \Exception($this->getError($produce));
        }
        
        if($is_new === true) {
            $follower_name = $produce->follower ? $produce->follower->username:'';
            $supplier_name = $produce->supplier ? $produce->supplier->supplier_name:'';
            $log = [
                'produce_id' => $produce->id,
                'produce_sn' => $produce->produce_sn,
                'log_type' => LogTypeEnum::SYSTEM,
                'bc_status' => $produce->bc_status,
                'log_module' => '布产单创建',
                'log_msg' => "采购单审核生成布产单{$produce->produce_sn},供应商:{$supplier_name},跟单人:{$follower_name}"
            ];
            \Yii::$app->supplyService->produce->createProduceLog($log);            
        }
        
        return $produce ;
    }

    /**
     * 统计布产单出货数量
     * @param $produce_id
     * @return mixed
     */
    public function getShippentNum($produce_id){
        return ProduceShipment::find()->where(['produce_id'=>$produce_id])->sum('shippent_num') ?? 0;
    }




    /**
     * 创建布产日志
     * @return array
     */
    public function createProduceLog($log){

        $produce_log = new ProduceLog();
        $produce_log->attributes = $log;
        $produce_log->log_time = time();
        $produce_log->creator_id = \Yii::$app->user->id;
        $produce_log->creator = \Yii::$app->user->identity->username;
        if(false === $produce_log->save()){
            throw new \Exception($this->getError($produce_log));
        }
        return $produce_log ;
    }


}
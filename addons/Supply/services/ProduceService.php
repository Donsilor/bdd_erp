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
use addons\Supply\common\enums\LogModuleEnum;
use addons\Supply\common\enums\BuChanEnum;
use addons\Supply\common\enums\PeiliaoStatusEnum;
use addons\Supply\common\enums\PeishiStatusEnum;
use addons\Supply\common\models\Peishi;
use addons\Style\common\enums\StonePositionEnum;
use addons\Supply\common\models\ProduceStone;
use addons\Supply\common\models\ProduceGold;


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
        
        $menus = [
            1=>['name'=>'基础信息','url'=>Url::to(['produce/view','id'=>$produce_id,'tab'=>1,'returnUrl'=>$returnUrl])],
            2=>['name'=>'金料信息','url'=>Url::to(['produce-gold/index','produce_id'=>$produce_id,'tab'=>2,'returnUrl'=>$returnUrl])],
            3=>['name'=>'石料信息','url'=>Url::to(['produce-stone/index','produce_id'=>$produce_id,'tab'=>3,'returnUrl'=>$returnUrl])],
            4=>['name'=>'出厂信息','url'=>Url::to(['produce-shipment/index','produce_id'=>$produce_id,'tab'=>4,'returnUrl'=>$returnUrl])],
            5=>['name'=>'日志信息','url'=>Url::to(['produce-log/index','produce_id'=>$produce_id,'tab'=>5,'returnUrl'=>$returnUrl])]
        ];
        
        $model = Produce::find()->where(['id'=>$produce_id])->one();
        if($model->peiliao_status == PeiliaoStatusEnum::NONE) {
            unset($menus[2]);
        }
        if($model->peishi_status == PeishiStatusEnum::NONE) {
            unset($menus[3]);
        }
        
        return $menus;
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
    public function getShippentNum($produce_id)
    {
        return ProduceShipment::find()->where(['produce_id'=>$produce_id])->sum('shippent_num') ?? 0;
    }
    /**
     * 创建配料单
     * @param Produce $form
     */
     
    public function toPeiliao($form)
    {
        if($form->bc_status != BuChanEnum::TO_PEILIAO){
            throw new \Exception('布产单不是'.BuChanEnum::getValue(BuChanEnum::TO_PEILIAO).'状态，不能操作');
        }
        $attrValues = ArrayHelper::map($form->attrs ?? [], 'attr_id', 'attr_value');
        $form->bc_status = BuChanEnum::IN_PEILIAO;

        if($form->peiliao_status == PeiliaoStatusEnum::PENDING) {
            $form->peiliao_status = PeiliaoStatusEnum::DOING;
            $this->createPeiliao($form,$attrValues);
        }
        if($form->peishi_status == PeishiStatusEnum::PENDING) {
            $form->peishi_status = PeishiStatusEnum::DOING;
            $this->createPeishi($form,$attrValues);
        }        
        if(false === $form->save()){
            throw new \Exception($this->getError($form));
        }
    }
    /**
     * 创建配料单
     * @param Produce $form
     */
    private function createPeiliao($form ,$attrValues)
    {        
        $gold = [
                'gold_type' =>  $attrValues[AttrIdEnum::MATERIAL]??'',
                'gold_weight' => $form->goods_num * ($attrValues[AttrIdEnum::JINZHONG]?? 0),
        ];
        $model = ProduceGold::find()->where(['produce_id'=>$form->id])->one();
        if(!$model) {
            $model = new ProduceGold();
            $model->attributes = $gold;
            $model->produce_id =  $form->id;
            $model->produce_sn =  $form->produce_sn;
        }else {
            $model->attributes = ArrayHelper::merge($model->attributes, $gold);
        }
        if(false === $model->save()) {
            throw new \Exception($this->getError($model));
        }          
        //日志
        $log = [
                'produce_id' => $form->id,
                'produce_sn' => $form->produce_sn,
                'log_type' => LogTypeEnum::ARTIFICIAL,
                'bc_status' => $form->bc_status,
                'log_module' => LogModuleEnum::getValue(LogModuleEnum::TO_PEILIAO),
                'log_msg' => "生成配料单:".$model->id,
        ];
        $this->createProduceLog($log);
    }
    /**
     * 创建配石单
     * @param Produce $form
     */
    private function createPeishi($form , $attrValues) 
    {
        $stone_list =[];
        //主石
        if(!empty($attrValues[AttrIdEnum::MAIN_STONE_TYPE])) {
            $stone_list[StonePositionEnum::MAIN_STONE] = [
                    'stone_type'=>$attrValues[AttrIdEnum::MAIN_STONE_TYPE]??'',
                    'stone_position'=>StonePositionEnum::MAIN_STONE,                    
                    'stone_num'=> $form->goods_num * ($attrValues[AttrIdEnum::MAIN_STONE_NUM]??0),
                    'stone_spec'=>$attrValues[AttrIdEnum::DIA_SPEC]??'',
                    'color' =>$attrValues[AttrIdEnum::DIA_COLOR]??'',
                    'clarity'=>$attrValues[AttrIdEnum::DIA_CLARITY]??'',
                    'shape'=>$attrValues[AttrIdEnum::DIA_SHAPE]??'',                    
                    'cert_type'=>$attrValues[AttrIdEnum::DIA_CERT_TYPE]??'',
                    'cert_no'=>$attrValues[AttrIdEnum::DIA_CERT_NO]??'',                  
            ];
        }
        //副石1
        if(!empty($attrValues[AttrIdEnum::SIDE_STONE1_TYPE])) {
            $stone_list[StonePositionEnum::SECOND_STONE1] = [
                    'stone_type'=>$attrValues[AttrIdEnum::SIDE_STONE1_TYPE]??'',
                    'stone_position'=>StonePositionEnum::SECOND_STONE1,                    
                    'stone_num'=>$form->goods_num * ($attrValues[AttrIdEnum::SIDE_STONE1_NUM]??0),
                    'stone_spec'=>$attrValues[AttrIdEnum::SIDE_STONE1_SPEC]??'',
                    'color' =>$attrValues[AttrIdEnum::SIDE_STONE1_COLOR]??'',
                    'clarity'=>$attrValues[AttrIdEnum::SIDE_STONE1_CLARITY]??'',
            ];
        }
        //副石2
        if(!empty($attrValues[AttrIdEnum::SIDE_STONE2_TYPE])) {
            $stone_list[StonePositionEnum::SECOND_STONE2] = [
                    'stone_type'=>$attrValues[AttrIdEnum::SIDE_STONE2_TYPE]??'',
                    'stone_position'=>StonePositionEnum::SECOND_STONE2,
                    'stone_num'=>$form->goods_num * ($attrValues[AttrIdEnum::SIDE_STONE2_NUM]??0),
                    'stone_spec'=>$attrValues[AttrIdEnum::SIDE_STONE2_SPEC]??'',
            ];
        }
        //副石3
        if(!empty($attrValues[AttrIdEnum::SIDE_STONE3_TYPE])) {
            $stone_list[StonePositionEnum::SECOND_STONE3] = [
                    'stone_type'=>$attrValues[AttrIdEnum::SIDE_STONE3_TYPE]??'',
                    'stone_position'=>StonePositionEnum::SECOND_STONE3,
                    'stone_num'=>$form->goods_num * ($attrValues[AttrIdEnum::SIDE_STONE3_NUM]??0),
                    'stone_spec'=>$attrValues[AttrIdEnum::SIDE_STONE3_SPEC]??'',
            ];
        }
        $log_list = [];
        foreach ($stone_list as $position => $stone) {
             
             $model = ProduceStone::find()->where(['produce_id'=>$form->id,'stone_position'=>$position])->one();
             if(!$model) {                 
                 $model = new ProduceStone();
                 $model->attributes = $stone;
                 $model->produce_id =  $form->id;
                 $model->produce_sn =  $form->produce_sn;
             }else {
                 $model->attributes = ArrayHelper::merge($model->attributes, $stone);
             }
             if(false === $model->save()) {
                 throw new \Exception($this->getError($model));
             }             
        }
        //日志
        $log = [
                'produce_id' => $form->id,
                'produce_sn' => $form->produce_sn,
                'log_type' => LogTypeEnum::ARTIFICIAL,
                'bc_status' => $form->bc_status,
                'log_module' => LogModuleEnum::getValue(LogModuleEnum::TO_PEILIAO),
                'log_msg' => "生成配石单:2222222,3333333"
        ];
        $this->createProduceLog($log);
        /* foreach ($log_list as $log) {
            $log = [
                    'produce_id' => $form->id,
                    'produce_sn' => $form->produce_sn,
                    'log_type' => LogTypeEnum::ARTIFICIAL,
                    'bc_status' => $form->bc_status,
                    'log_module' => LogModuleEnum::getValue(LogModuleEnum::TO_PEILIAO),
                    'log_msg' => "生成配石单:".$model->id
            ];
            $this->createProduceLog($log);
        } */
    }
    /**
     * 创建布产日志
     * @return array
     */
    public function createProduceLog($log){

        $model = new ProduceLog();
        $model->attributes = $log;
        $model->log_time = time();
        $model->creator_id = \Yii::$app->user->id;
        $model->creator = \Yii::$app->user->identity->username;
        if(false === $model->save()){
            throw new \Exception($this->getError($model));
        }
        return $model ;
    }


}
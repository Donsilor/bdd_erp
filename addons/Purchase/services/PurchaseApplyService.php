<?php

namespace addons\Purchase\services;


use common\enums\TargetTypeEnum;
use Yii;
use common\components\Service;
use common\helpers\Url;
use common\enums\StatusEnum;
use addons\Purchase\common\models\PurchaseApplyGoods;
use addons\Purchase\common\models\PurchaseApply;
use addons\Purchase\common\models\PurchaseApplyLog;
use addons\Purchase\common\enums\ApplyStatusEnum;
use addons\Purchase\common\models\PurchaseApplyGoodsAttribute;

/**
 * Class PurchaseApplyService
 * @package services\common
 * @author jianyan74 <751393839@qq.com>
 */
class PurchaseApplyService extends Service
{
    
    /**
     * 采购申请单菜单
     * @param int $id 款式ID
     * @return array
     */
    public function menuTabList($apply_id, $returnUrl = null)
    {

        return [
                1=>['name'=>'基础信息','url'=>Url::to(['purchase-apply/view','id'=>$apply_id,'tab'=>1,'returnUrl'=>$returnUrl])],
                2=>['name'=>'采购商品','url'=>Url::to(['purchase-apply-goods/index','apply_id'=>$apply_id,'tab'=>2,'returnUrl'=>$returnUrl])],
                3=>['name'=>'日志信息','url'=>Url::to(['purchase-apply-log/index','apply_id'=>$apply_id,'tab'=>3,'returnUrl'=>$returnUrl])]
        ];
        
    }
    
    /**
     * 采购单汇总
     * @param unknown $apply_id
     */
    public function applySummary($apply_id) 
    {
        $sum = PurchaseApplyGoods::find()
                    ->select(['sum(goods_num) as total_num','sum(cost_price*goods_num) as total_cost'])
                    ->where(['apply_id'=>$apply_id,'status'=>StatusEnum::ENABLED])
                    ->asArray()->one();
        if($sum) {
            PurchaseApply::updateAll(['total_num'=>$sum['total_num'],'total_cost'=>$sum['total_cost']],['id'=>$apply_id]);
        }
    }
    /**
     * 创建采购申请单-同步创建
     * @param array $applyInfo
     * @param array $applyGoodsList
     * @throws \Exception
     * @return \addons\Purchase\common\models\PurchaseApply $apply
     */
    public function createSyncApply($applyInfo, $applyGoodsList)
    {
         $isNewRecod = false;
         if(empty($applyInfo['order_sn'])) {
             throw new \Exception("参数 applyInfo->order_sn 不能为空");
         }
         $apply = PurchaseApply::find()->where(['order_sn'=>$applyInfo['order_sn']])->one();
         if(!$apply) {
             $apply = new PurchaseApply();
             $apply->attributes = $applyInfo;
             $apply->creator_id = Yii::$app->user->id;
             $isNewRecod = true;
         } else if($apply->apply_status != ApplyStatusEnum::SAVE){
             return $apply;
         }
         //采购申请商品
         foreach ($applyGoodsList as $goodsInfo) {
             if(empty($goodsInfo['id'])) {
                 throw new \Exception("参数 applyGoodsList->id 不能为空");
             }
             if($isNewRecod === false) {
                 $applyGoods = PurchaseApplyGoods::find()->where(['order_detail_id'=>$goodsInfo['id'],'apply_id'=>$apply->id])->one();
             }
             if(empty($applyGoods)) {
                 $applyGoods = new PurchaseApplyGoods();
             }
             $applyGoods->attributes = $goodsInfo;
             if(false === $applyGoods->save()) {
                 throw new \Exception($this->getError($applyGoods));
             }
             //商品属性
             if($isNewRecod === false) {
                 PurchaseApplyGoodsAttribute::deleteAll(['id'=>$applyGoods->id]);
             }
             foreach ($goodsInfo['goods_attrs'] ?? [] as $goods_attr) {
                 $goodsAttr = new PurchaseApplyGoodsAttribute();
                 $goodsAttr->attributes = $goods_attr;
                 if(false === $goodsAttr->save()) {
                     throw new \Exception($this->getError($goodsAttr));
                 }
             }
         }
         
         return $apply;
    }
    /**
     * 根据采购申请单生成采购单
     * @param array|int $apply_ids
     */
    public function createPurchase(array $apply_ids) 
    {
        $count = PurchaseApply::find()->where(['id'=>$apply_ids,'apply_status'=>ApplyStatusEnum::FINISHED])->count();
        if($count != count($apply_ids) ) {
            throw new \Exception("采购申请单未完成 申请流程");
        }
        $channel_ids = PurchaseApply::find()->distinct("channel_id")->where(['id'=>$apply_ids])->asArray()->all();
        foreach ($channel_ids as $channel_id){
            
        }
    }
    /**
     * 创建采购单日志
     * @return array
     */
    public function createApplyLog($log){

        $model = new PurchaseApplyLog();
        $model->attributes = $log;
        $model->log_time = time();
        $model->creator_id = \Yii::$app->user->id;
        $model->creator = \Yii::$app->user->identity->username;
        if(false === $model->save()){
            throw new \Exception($this->getError($model));
        }
        return $model ;
    }


    public function getTargetYType($channel_id){
        if(in_array($channel_id,[1,2,5,6,7,8,9,10])){
            return TargetTypeEnum::PURCHASE_APPLY_T_MENT;
        }elseif (in_array($channel_id,[3])){
            return TargetTypeEnum::PURCHASE_APPLY_F_MENT;
        }elseif (in_array($channel_id,[4])){
            return TargetTypeEnum::PURCHASE_APPLY_Z_MENT;
        }
    }




}
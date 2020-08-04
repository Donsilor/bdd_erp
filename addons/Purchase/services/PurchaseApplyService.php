<?php

namespace addons\Purchase\services;


use common\enums\TargetTypeEnum;
use common\helpers\SnHelper;
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
        try{
            $isNewRecod = false;
            if(empty($applyInfo['order_sn'])) {
                throw new \Exception("参数 applyInfo->order_sn 不能为空");
            }
            $apply = PurchaseApply::find()->where(['order_sn'=>$applyInfo['order_sn']])->one();
            if(!$apply) {
                $apply = new PurchaseApply();
                $apply->attributes = $applyInfo;
                $apply->creator_id = Yii::$app->user->id;
                $apply->apply_sn = SnHelper::createPurchaseApplySn();
                $apply->total_num = count($applyGoodsList);
                $isNewRecod = true;
            } else if($apply->apply_status != ApplyStatusEnum::SAVE){
                return $apply;
            }
            if(false === $apply->save()) {
                throw new \Exception($this->getError($apply));
            }
            //采购申请商品
            foreach ($applyGoodsList as $goodsInfo) {
                if(empty($goodsInfo['order_detail_id'])) {
                    throw new \Exception("参数 applyGoodsList->order_detail_id 不能为空");
                }
                if($isNewRecod === false) {
                    $applyGoods = PurchaseApplyGoods::find()->where(['order_detail_id'=>$goodsInfo['order_detail_id'],'apply_id'=>$apply->id])->one();
                }
                if(empty($applyGoods)) {
                    $applyGoods = new PurchaseApplyGoods();
                }
                $applyGoods->attributes = $goodsInfo;
                $applyGoods->apply_id = $apply->id;
                $applyGoods->goods_sn = $goodsInfo['qiban_sn'] ? $goodsInfo['qiban_sn'] : $goodsInfo['style_sn'];
                $applyGoods->created_at = time();
                $applyGoods->updated_at = time();
                $applyGoods->creator_id = Yii::$app->user->id;

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
                    $goodsAttr->id = $applyGoods->id;
                    if(false === $goodsAttr->save()) {
                        throw new \Exception($this->getError($goodsAttr));
                    }
                }
            }
            return $apply;
        }catch (\Exception $e){
            throw $e;
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
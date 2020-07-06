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
<?php

namespace addons\Purchase\services;

use addons\Purchase\common\models\PurchaseGoodsAttribute;
use common\enums\AuditStatusEnum;
use Yii;
use common\components\Service;
use addons\Purchase\common\models\Purchase;
use common\helpers\Url;
use addons\Purchase\common\models\PurchaseGoods;
use common\enums\StatusEnum;
use yii\db\Exception;

/**
 * Class TypeService
 * @package services\common
 * @author jianyan74 <751393839@qq.com>
 */
class PurchaseService extends Service
{
    
    /**
     * 款式编辑 tab
     * @param int $id 款式ID
     * @return array
     */
    public function menuTabList($purchase_id,$returnUrl = null)
    {
        return [
                1=>['name'=>'基础信息','url'=>Url::to(['purchase/view','id'=>$purchase_id,'tab'=>1,'returnUrl'=>$returnUrl])],
                2=>['name'=>'采购商品','url'=>Url::to(['purchase-goods/index','purchase_id'=>$purchase_id,'tab'=>2,'returnUrl'=>$returnUrl])],
                3=>['name'=>'日志信息','url'=>Url::to(['purchase-log/index','purchase_id'=>$purchase_id,'tab'=>3,'returnUrl'=>$returnUrl])]
        ];
    }
    
    /**
     * 采购单汇总
     * @param unknown $purchase_id
     */
    public function purchaseSummary($purchase_id) 
    {
        $sum = PurchaseGoods::find()
                    ->select(['sum(goods_num) as goods_count','sum(cost_price*goods_num) as cost_total'])
                    ->where(['purchase_id'=>$purchase_id,'status'=>StatusEnum::ENABLED])
                    ->asArray()->one();
        if($sum) {
            Purchase::updateAll(['goods_count'=>$sum['goods_count']/1,'cost_total'=>$sum['cost_total']/1],['id'=>$purchase_id]);
        }
    }


    /**
     * 审核后同步到布产
     */
    public function syncPurchaseBC($purchase_id){
        $purchase_goods = PurchaseGoods::find()
            ->where(['purchase_id'=>$purchase_id])
            ->asArray()
            ->all();
        foreach ($purchase_goods as &$goods){
            $goods_attr = PurchaseGoodsAttribute::find()->where(['id'=>$goods['id']])->asArray()->all();
            Yii::$app->supplyService->produce->createProduce($goods ,$goods_attr,2);
        }


    }






 
}
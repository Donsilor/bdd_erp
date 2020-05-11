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
     * 同步采购单生成布产单
     * @param unknown $purchase_id
     */
    public function syncPurchaseToProduce($purchase_id)
    {
        $purchase = Purchase::find()->where(['id'=>$purchase_id]);
        
        $models = PurchaseGoods::find()->where(['purchase_id'=>$purchase_id])->all();
        foreach ($models as $model){ 
            $goods = [
                    'goods_name' =>$model->goods_name,
                    'from_order_id'=>$model->purchase_id,
                    'from_detail_id' => $model->id, 
                    'from_order_sn'=>$purchase->purchase_sn,
                    'from_type' => 2,
                    'style_sn' => $model->style_sn,
                    'qiban_sn' => $model->qiban_sn,
                    'qiban_type'=>$model->qiban_type, 
                    'style_sex' =>$model->style_sex, 
                    'goods_num' =>$model->goods_num,
                    'jintuo_type'=>$model->jintuo_type,
                    'product_type_id'=>$model->product_type_id,
                    'style_cate_id'=>$model->style_cate_id,
            ];
            $goods_attrs = PurchaseGoodsAttribute::find()->where(['id'=>$model->id])->asArray()->all();
            Yii::$app->supplyService->produce->createProduce($goods ,$goods_attrs);
        }
    }






 
}
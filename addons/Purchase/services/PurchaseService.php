<?php

namespace addons\Purchase\services;

use addons\Purchase\common\models\PurchaseGoodsAttribute;
use addons\Purchase\common\models\PurchaseLog;
use addons\Supply\common\enums\BuChanEnum;
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
                    ->select(['sum(goods_num) as goods_count','sum(cost_price*goods_num) as total_cost'])
                    ->where(['purchase_id'=>$purchase_id,'status'=>StatusEnum::ENABLED])
                    ->asArray()->one();
        if($sum) {
            Purchase::updateAll(['goods_count'=>$sum['goods_count']/1,'total_cost'=>$sum['total_cost']/1],['id'=>$purchase_id]);
        }
    }
    
    /**
    * 同步采购单生成布产单
    * @param unknown $purchase_id
    * @param unknown $detail_ids
    * @throws \Exception
    */
    public function syncPurchaseToProduce($purchase_id, $detail_ids = null)
    {
        $purchase = Purchase::find()->where(['id'=>$purchase_id])->one();
        if($purchase->goods_count <= 0 ){
            throw new \Exception('采购单没有明细');
        }
        if($purchase->audit_status != AuditStatusEnum::PASS){
            throw new \Exception('采购单没有审核');
        }
        if($purchase->follower_id == ''){
            throw new \Exception('没有分配跟单人');
        }
        $query = PurchaseGoods::find()->where(['purchase_id'=>$purchase_id]);
        if(!empty($detail_ids)) {
            $query->andWhere(['id'=>$detail_ids]);
        }
        $models = $query->all();
        foreach ($models as $model){
            $goods = [
                    'goods_name' =>$model->goods_name,
                    'from_order_id'=>$model->purchase_id,
                    'from_detail_id' => $model->id,
                    'from_order_sn'=>$purchase->purchase_sn,
                    'from_type' => 2,
                    'style_sn' => $model->style_sn,
                    'bc_status' => BuChanEnum::ASSIGNED,
                    'qiban_sn' => $model->qiban_sn,
                    'qiban_type'=>$model->qiban_type,
                    'style_sex' =>$model->style_sex,
                    'goods_num' =>$model->goods_num,
                    'jintuo_type'=>$model->jintuo_type,
                    'is_inlay' =>$model->is_inlay,
                    'product_type_id'=>$model->product_type_id,
                    'style_cate_id'=>$model->style_cate_id,
                    'supplier_id'=>$purchase->supplier_id,
                    'follower_id'=>$purchase->follower_id,
                    'factory_distribute_time' => time()
            ];            
            if($model->produce_id && $model->produce){
                if($model->produce->bc_status > BuChanEnum::IN_PRODUCTION) {
                    //生产中之后的流程，禁止同步
                    continue;
                }else {
                    $goods['id'] = $model->produce->id;
                    $goods['bc_status'] = $model->produce->bc_status;
                    $goods['factory_distribute_time'] = $model->produce->factory_distribute_time;
                }
            }
            $goods_attrs = PurchaseGoodsAttribute::find()->where(['id'=>$model->id])->asArray()->all();
            $produce = Yii::$app->supplyService->produce->createProduce($goods ,$goods_attrs);
            if($produce) {
                $model->produce_id = $produce->id;
            }            
            if(false === $model->save()) {
                throw new \Exception($this->getError($model),422);
            }
        }
    }
    /**
     * 创建采购单日志
     * @return array
     */
    public function createPurchaseLog($log){

        $purchase_log = new PurchaseLog();
        $purchase_log->attributes = $log;
        $purchase_log->log_time = time();
        $purchase_log->creator_id = \Yii::$app->user->id;
        $purchase_log->creator = \Yii::$app->user->identity->username;
        if(false === $purchase_log->save()){
            throw new \Exception($this->getError($purchase_log));
        }
        return $purchase_log ;
    }


 
}
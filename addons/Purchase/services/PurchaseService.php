<?php

namespace addons\Purchase\services;

use addons\Purchase\common\enums\ReceiptGoodsStatusEnum;
use addons\Purchase\common\models\PurchaseGold;
use addons\Purchase\common\models\PurchaseGoldGoods;
use addons\Purchase\common\models\PurchaseGoldReceiptGoods;
use addons\Purchase\common\models\PurchaseReceiptGoods;
use addons\Purchase\common\models\PurchaseStone;
use addons\Purchase\common\models\PurchaseStoneGoods;
use addons\Purchase\common\models\PurchaseStoneReceiptGoods;
use addons\Warehouse\common\enums\BillStatusEnum;
use addons\Warehouse\common\enums\PutInTypeEnum;
use common\enums\ConfirmEnum;
use Yii;
use common\components\Service;
use common\enums\AuditStatusEnum;
use common\helpers\Url;
use common\enums\StatusEnum;

use addons\Purchase\common\models\Purchase;
use addons\Purchase\common\models\PurchaseGoodsAttribute;
use addons\Purchase\common\models\PurchaseLog;
use addons\Supply\common\enums\BuChanEnum;
use addons\Purchase\common\models\PurchaseGoods;
use addons\Purchase\common\enums\PurchaseTypeEnum;
use yii\db\Exception;
use addons\Supply\common\enums\FromTypeEnum;
use addons\Supply\common\enums\PeiliaoTypeEnum;
use addons\Supply\common\enums\PeishiStatusEnum;
use addons\Supply\common\enums\PeiliaoStatusEnum;

/**
 * Class PurchaseService
 * @package services\common
 * @author jianyan74 <751393839@qq.com>
 */
class PurchaseService extends Service
{
    
    /**
     * 采购单菜单
     * @param int $id 款式ID
     * @return array
     */
    public function menuTabList($purchase_id, $purchase_type = 1, $returnUrl = null)
    {
        if($purchase_type == PurchaseTypeEnum::MATERIAL_GOLD) {
            return [
                    1=>['name'=>'基础信息','url'=>Url::to(['purchase-gold/view','id'=>$purchase_id,'tab'=>1,'returnUrl'=>$returnUrl])],
                    2=>['name'=>'采购商品','url'=>Url::to(['purchase-gold-goods/index','purchase_id'=>$purchase_id,'tab'=>2,'returnUrl'=>$returnUrl])],
                    //3=>['name'=>'日志信息','url'=>Url::to(['purchase-log/index','purchase_id'=>$purchase_id,'tab'=>3,'returnUrl'=>$returnUrl])]
            ];
        }else if($purchase_type == PurchaseTypeEnum::MATERIAL_STONE) {
            return [
                    1=>['name'=>'基础信息','url'=>Url::to(['purchase-stone/view','id'=>$purchase_id,'tab'=>1,'returnUrl'=>$returnUrl])],
                    2=>['name'=>'采购商品','url'=>Url::to(['purchase-stone-goods/index','purchase_id'=>$purchase_id,'tab'=>2,'returnUrl'=>$returnUrl])],
                    //3=>['name'=>'日志信息','url'=>Url::to(['material-log/index','purchase_id'=>$purchase_id,'tab'=>3,'returnUrl'=>$returnUrl])]
            ];
        }
        else {
            return [
                    1=>['name'=>'基础信息','url'=>Url::to(['purchase/view','id'=>$purchase_id,'tab'=>1,'returnUrl'=>$returnUrl])],
                    2=>['name'=>'采购商品','url'=>Url::to(['purchase-goods/index','purchase_id'=>$purchase_id,'tab'=>2,'returnUrl'=>$returnUrl])],
                    3=>['name'=>'日志信息','url'=>Url::to(['purchase-log/index','purchase_id'=>$purchase_id,'tab'=>3,'returnUrl'=>$returnUrl])]
            ];
        }
    }
    
    /**
     * 采购单汇总
     * @param unknown $purchase_id
     */
    public function purchaseSummary($purchase_id) 
    {
        $sum = PurchaseGoods::find()
                    ->select(['sum(goods_num) as total_num','sum(cost_price*goods_num) as total_cost'])
                    ->where(['purchase_id'=>$purchase_id,'status'=>StatusEnum::ENABLED])
                    ->asArray()->one();
        if($sum) {
            Purchase::updateAll(['total_num'=>$sum['total_num']/1,'total_cost'=>$sum['total_cost']/1],['id'=>$purchase_id]);
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
        if($purchase->total_num <= 0 ){
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
            list($peishi_status,$peiliao_status) = PeiliaoTypeEnum::getValue($model->peiliao_type,"getStatusMap") ?? [PeishiStatusEnum::PENDING,PeiliaoStatusEnum::PENDING];
            $goods = [
                    'goods_name' =>$model->goods_name,
                    'goods_num' =>$model->goods_num,                   
                    'from_order_id'=>$model->purchase_id,
                    'from_detail_id' => $model->id,
                    'from_order_sn'=>$purchase->purchase_sn,
                    'from_type' => FromTypeEnum::PURCHASE,
                    'style_sn' => $model->style_sn,
                    'peishi_status'=>$peishi_status,
                    'peiliao_status'=>$peiliao_status,
                    'bc_status' => BuChanEnum::ASSIGNED,
                    'qiban_sn' => $model->qiban_sn,
                    'qiban_type'=>$model->qiban_type,
                    'jintuo_type'=>$model->jintuo_type,
                    'peiliao_type'=>$model->peiliao_type,
                    'style_sex' =>$model->style_sex,                    
                    'is_inlay' =>$model->is_inlay,
                    'product_type_id'=>$model->product_type_id,
                    'style_cate_id'=>$model->style_cate_id,
                    'supplier_id'=>$purchase->supplier_id,
                    'follower_id'=>$purchase->follower_id,
                    'factory_mo'=>$model->factory_mo,
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
     * 采购收货验证
     * @param object $form
     * @param int $purchase_type
     */
    public function receiptValidate($form, $purchase_type)
    {
        $ids = $form->getIds();
        if(is_array($ids)){
            if($purchase_type == PurchaseTypeEnum::MATERIAL_STONE){
                $model = new PurchaseStoneGoods();
            }else{
                $model = new PurchaseGoldGoods();
            }
            foreach ($ids as $id) {
                $goods = $model::findOne(['id'=>$id]);
                if($goods->is_receipt){
                    throw new Exception("【".$goods->goods_name."】已收货，不能重复收货");
                }
            }
        }
    }

    /**
     * 同步采购单生成采购收货单
     * @param object $form
     * @param int $purchase_type
     * @param array $detail_ids
     * @throws \Exception
     */
    public function syncPurchaseToReceipt($form, $purchase_type, $detail_ids = null)
    {
        if(!$form->put_in_type){
            throw new \Exception('请选择入库方式');
        }
        $put_in_type = $form->put_in_type;
        if($purchase_type == PurchaseTypeEnum::MATERIAL_STONE){
            $model = new PurchaseStoneGoods();
            $PurchaseModel = new PurchaseStone();
        }else{
            $model = new PurchaseGoldGoods();
            $PurchaseModel = new PurchaseGold();
        }
        if(!empty($detail_ids)) {
            $goods = $model::find()->select('purchase_id')->where(['id'=>$detail_ids[0]])->one();
            $form = $PurchaseModel::find()->where(['id'=>$goods->purchase_id])->one();
        }
        if($form->total_num <= 0 ){
            throw new \Exception('采购单没有明细');
        }
        if($form->audit_status != AuditStatusEnum::PASS){
            throw new \Exception('采购单没有审核');
        }
        $query = $model::find()->where(['purchase_id'=>$form->id]);
        if(!empty($detail_ids)) {
            $query->andWhere(['id'=>$detail_ids]);
        }
        $models = $query->all();
        $goods = $bill = [];
        $total_cost =0;
        $i=1;
        foreach ($models as $k => $model){
            if($model->is_receipt){
                throw new \Exception("【".$model->goods_name."】已收货，不能重复收货");
            }
            $goods[$k] = [
                'purchase_sn' =>$form->purchase_sn,
                'xuhao'=>$i++,
                'put_in_type' => $put_in_type,
                'purchase_detail_id' => $model->id,
                'goods_status' => ReceiptGoodsStatusEnum::SAVE,
                'goods_name'=>$model->goods_name,
                'goods_num' => $model->goods_num,
                'goods_weight'=>$model->goods_weight,
                'cost_price' =>$model->cost_price,
                'goods_remark'=>$model->remark,
                'status'=>StatusEnum::ENABLED,
                'created_at' => time(),
            ];
            if($purchase_type == PurchaseTypeEnum::MATERIAL_GOLD){
                $goods[$k]['material_type'] = $model->material_type;
                $goods[$k]['gold_price'] = $model->gold_price;
            }else{
                $goods[$k]['material_type'] = $model->stone_type;
                $goods[$k]['goods_color'] = $model->stone_color;
                $goods[$k]['goods_clarity'] = $model->stone_clarity;
                //$goods[$k]['goods_norms'] =  $model->goods_norms;
                $goods[$k]['stone_price'] = $model->stone_price;
            }
            $total_cost = bcadd($total_cost, $model->cost_price, 2);
        }
        $bill = [
            'supplier_id' => $form->supplier_id,
            'purchase_sn' => $form->purchase_sn,
            'purchase_type' => $purchase_type,
            'to_warehouse_id' => 0,
            'put_in_type' => PutInTypeEnum::PURCHASE,
            'receipt_status' => BillStatusEnum::PENDING,
            'receipt_num' => count($goods),
            'total_cost' => $total_cost,
            'audit_status' => AuditStatusEnum::PENDING,
            'creator_id' => \Yii::$app->user->identity->getId(),
            'created_at' => time(),
        ];
        Yii::$app->purchaseService->receipt->createReceipt($bill ,$goods);
        if(!empty($detail_ids)){
            $res = $model::updateAll(['is_receipt'=>ConfirmEnum::YES], ['id'=>$detail_ids]);
            if(false === $res){
                throw new \Exception('更新货品状态失败');
            }
        }
    }

    /**
     * 创建采购单日志
     * @return array
     */
    public function createPurchaseLog($log){

        $model = new PurchaseLog();
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
<?php

namespace addons\Purchase\services;

use addons\Purchase\common\enums\ReceiveStatusEnum;
use addons\Sales\common\models\OrderGoods;
use addons\Style\common\enums\LogTypeEnum;
use addons\Supply\common\enums\PeijianTypeEnum;
use common\helpers\SnHelper;
use Yii;
use common\helpers\Url;
use common\components\Service;
use addons\Purchase\common\models\Purchase;
use addons\Purchase\common\models\PurchaseGoodsAttribute;
use addons\Purchase\common\enums\ReceiptGoodsStatusEnum;
use addons\Purchase\common\models\PurchaseGift;
use addons\Purchase\common\models\PurchaseGiftGoods;
use addons\Purchase\common\models\PurchaseGold;
use addons\Purchase\common\models\PurchaseGoldGoods;
use addons\Purchase\common\models\PurchaseParts;
use addons\Purchase\common\models\PurchasePartsGoods;
use addons\Purchase\common\models\PurchaseStone;
use addons\Purchase\common\models\PurchaseStoneGoods;
use addons\Purchase\common\models\PurchaseLog;
use addons\Supply\common\enums\BuChanEnum;
use addons\Purchase\common\models\PurchaseGoods;
use addons\Purchase\common\enums\PurchaseTypeEnum;
use addons\Warehouse\common\enums\BillStatusEnum;
use addons\Warehouse\common\enums\PutInTypeEnum;
use addons\Supply\common\enums\FromTypeEnum;
use addons\Supply\common\enums\PeiliaoTypeEnum;
use addons\Supply\common\enums\PeishiTypeEnum;
use common\enums\AuditStatusEnum;
use common\enums\StatusEnum;
use common\enums\ConfirmEnum;
use common\helpers\ArrayHelper;
use yii\db\Exception;

/**
 * Class PurchaseService
 * @package services\common
 * @author jianyan74 <751393839@qq.com>
 */
class PurchaseService extends Service
{
    /**
     * 采购单菜单
     * @param int $purchase_id 采购ID
     * @return array $returnUrl
     */
    public function menuTabList($purchase_id, $returnUrl = null)
    {
        return [
                1=>['name'=>'基础信息','url'=>Url::to(['purchase/view','id'=>$purchase_id,'tab'=>1,'returnUrl'=>$returnUrl])],
                2=>['name'=>'采购商品','url'=>Url::to(['purchase-goods/index','purchase_id'=>$purchase_id,'tab'=>2,'returnUrl'=>$returnUrl])],
                3=>['name'=>'日志信息','url'=>Url::to(['purchase-log/index','purchase_id'=>$purchase_id,'tab'=>3,'returnUrl'=>$returnUrl])]
        ];
    }
    
    /**
     * 采购单汇总
     * @param int $purchase_id
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
     * 创建采购单
     * @param array $info 采购单-单头
     * @param array $goods_list 采购单-明细列表
     * @throws \Exception
     * @return \addons\Purchase\common\models\Purchase
     */
    public function createPurchase($info, $goods_list)
    {
            $purchase = new Purchase();
            $purchase->attributes = $info;
            $purchase->purchase_sn = SnHelper::createPurchaseSn();
            $purchase->creator_id  = \Yii::$app->user->identity->id ?? 0;
            $purchase->created_at  = time();

            if(false === $purchase->save()){
                throw new \Exception($this->getError($purchase));
            }

            //日志
            $log = [
                'purchase_id' => $purchase->id,
                'purchase_sn' => $purchase->purchase_sn,
                'log_type' => LogTypeEnum::ARTIFICIAL,
                'log_module' => "采购单",
                'log_msg' => "采购申请单同步创建采购单"
            ];
            Yii::$app->purchaseService->purchaseLog->createPurchaseLog($log);

            foreach ($goods_list as $goods) {
                $purchaseGoods = new PurchaseGoods();
                $purchaseGoods->attributes = $goods;
                $purchaseGoods->purchase_id = $purchase->id;
                if(false === $purchaseGoods->save()) {
                    throw new \Exception($this->getError($purchaseGoods));
                }
                foreach ($goods['goods_attrs'] ?? [] as $attr) {
                    $goodsAttr = new PurchaseGoodsAttribute();
                    $goodsAttr->attributes = $attr;
                    $goodsAttr->id = $purchaseGoods->id;
                    if(false === $goodsAttr->save()) {
                        throw new \Exception($this->getError($goodsAttr));
                    }
                }
            }
            return $purchase;


    }
    
    /**
    * 同步采购单生成布产单
    * @param int $purchase_id
    * @param array $detail_ids
    * @throws \Exception
    */
    public function syncProduce($purchase_id, $detail_ids = null)
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
            $order_goods = OrderGoods::find()->where(['id'=>$model->order_detail_id])->one();
            $peishi_status = PeishiTypeEnum::getPeishiStatus($model->peishi_type);
            $peiliao_status = PeiliaoTypeEnum::getPeiliaoStatus($model->peiliao_type);
            $peijian_status = PeijianTypeEnum::getPeijianStatus($model->peijian_type);
            if(PeishiTypeEnum::isPeishi($model->peishi_type) || PeiliaoTypeEnum::isPeiliao($model->peiliao_type) || PeijianTypeEnum::isPeijian($model->peijian_type)) {
                $buchan_status = BuChanEnum::TO_PEILIAO;
            } else {
                $buchan_status = BuChanEnum::TO_PRODUCTION;
            }
            //$model = new PurchaseGoods();
            $goods = [
                    'goods_name' =>$model->goods_name,
                    'goods_num' =>$model->goods_num,
                    'order_detail_id' =>$model->order_detail_id,
                    'purchase_detail_id' => $model->id,
                    'purchase_sn'=>$purchase->purchase_sn,
                    'order_sn'=>$order_goods->order->order_sn ?? '',
                    'from_type' => empty($model->order_detail_id) ? FromTypeEnum::PURCHASE : FromTypeEnum::ORDER,
                    'style_sn' => $model->style_sn,
                    'peiliao_type'=>$model->peiliao_type,
                    'peishi_type'=>$model->peishi_type,
                    'peijian_type'=>$model->peijian_type,
                    'templet_type'=>$model->templet_type,
                    'peishi_status'=>$peishi_status,
                    'peiliao_status'=>$peiliao_status,
                    'peijian_status'=>$peijian_status,
                    'bc_status' => $buchan_status,
                    'qiban_sn' => $model->qiban_sn,
                    'qiban_type'=>$model->qiban_type,
                    'jintuo_type'=>$model->jintuo_type,                    
                    'style_sex' =>$model->style_sex,                    
                    'is_inlay' =>$model->is_inlay,
                    'product_type_id'=>$model->product_type_id,
                    'style_cate_id'=>$model->style_cate_id,
                    'supplier_id'=>$purchase->supplier_id,
                    'follower_id'=>$purchase->follower_id,
                    'factory_mo'=>$model->factory_mo,
                    'parts_info'=>$model->parts_info,
                    'factory_distribute_time' => time()
            ];

            if($model->produce_id && $model->produce){
                if($model->produce->bc_status > BuChanEnum::IN_PRODUCTION) {
                    //生产中之后的流程，禁止同步
                    continue;
                }else {
//                    unset($goods['bc_status']);
                    $goods['id'] = $model->produce->id;                    
                    //如果是配料中的，不同步配料类型和配料状态
                    if($model->produce->bc_status == BuChanEnum::IN_PEILIAO) {
                        unset($goods['peiliao_type']);
                        unset($goods['peishi_status']);
                        unset($goods['peiliao_status']);
                        unset($goods['peijian_status']);
                    }
                }
            }
            $goods_attrs = PurchaseGoodsAttribute::find()->where(['id'=>$model->id])->asArray()->all();
            $produce = Yii::$app->supplyService->produce->createSyncProduce($goods ,$goods_attrs);
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
     * @throws
     */
    public function receiptValidate($form, $purchase_type)
    {
        $ids = $form->getIds();
        if(is_array($ids)){
            if($purchase_type == PurchaseTypeEnum::MATERIAL_STONE){
                $model = new PurchaseStoneGoods();
            }elseif($purchase_type == PurchaseTypeEnum::MATERIAL_GOLD){
                $model = new PurchaseGoldGoods();
            }elseif($purchase_type == PurchaseTypeEnum::MATERIAL_PARTS){
                $model = new PurchasePartsGoods();
            }elseif($purchase_type == PurchaseTypeEnum::MATERIAL_GIFT){
                $model = new PurchaseGiftGoods();
            }else{
                $model = new PurchaseGoods();
            }
            foreach ($ids as $id) {
                $goods = $model::findOne(['id'=>$id]);
                if($goods->is_receipt){
                    throw new Exception("[ID={$goods->id}]已收货，不能重复收货");
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
        }elseif($purchase_type == PurchaseTypeEnum::MATERIAL_GOLD){
            $model = new PurchaseGoldGoods();
            $PurchaseModel = new PurchaseGold();
        }elseif($purchase_type == PurchaseTypeEnum::MATERIAL_PARTS){
            $model = new PurchasePartsGoods();
            $PurchaseModel = new PurchaseParts();
        }elseif($purchase_type == PurchaseTypeEnum::MATERIAL_GIFT){
            $model = new PurchaseGiftGoods();
            $PurchaseModel = new PurchaseGift();
        }else{
            $model = new PurchaseGoods();
            $PurchaseModel = new Purchase();
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
        $total_cost =$total_weight= $total_stone_num =0;
        $i=1;
        foreach ($models as $k => $model){
            if($model->is_receipt){
                throw new \Exception("[ID={$model->id}]已收货，不能重复收货");
            }
            $goods[$k] = [
                'xuhao'=>$i++,
                'purchase_sn' =>$form->purchase_sn,
                'put_in_type' => $put_in_type,
                'purchase_detail_id' => $model->id,
                'goods_status' => ReceiptGoodsStatusEnum::SAVE,
                'goods_name'=>$model->goods_name,
                'goods_sn'=>$model->goods_sn,
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
                $goods[$k]['incl_tax_price'] = $model->incl_tax_price;
            }elseif($purchase_type == PurchaseTypeEnum::MATERIAL_STONE) {
                $goods[$k]['material_type'] = $model->stone_type;
                $goods[$k]['goods_shape'] = $model->stone_shape;
                $goods[$k]['goods_color'] = $model->stone_color;
                $goods[$k]['goods_clarity'] = $model->stone_clarity;
                $goods[$k]['goods_cut'] = $model->stone_cut;
                $goods[$k]['goods_symmetry'] = $model->stone_symmetry;
                $goods[$k]['goods_polish'] = $model->stone_polish;
                $goods[$k]['goods_fluorescence'] = $model->stone_fluorescence;
                $goods[$k]['goods_colour'] = $model->stone_colour;
                $goods[$k]['cert_type'] = $model->cert_type;
                $goods[$k]['cert_id'] = $model->cert_id;
                $goods[$k]['goods_norms'] = $model->spec_remark;
                $goods[$k]['goods_size'] = $model->stone_size;
                $goods[$k]['stone_num'] = $model->stone_num;
                $goods[$k]['stone_weight'] = $model->stone_weight;
                $goods[$k]['stone_price'] = $model->stone_price;
                $goods[$k]['channel_id'] = $model->channel_id;
                $total_stone_num = bcadd($total_stone_num, $model->stone_num);
            }elseif($purchase_type == PurchaseTypeEnum::MATERIAL_PARTS){
                $goods[$k]['parts_type'] = $model->parts_type;
                $goods[$k]['material_type'] = $model->material_type;
                $goods[$k]['goods_shape'] = $model->goods_shape;
                $goods[$k]['goods_color'] = $model->goods_color;
                $goods[$k]['goods_size'] =  $model->goods_size;
                $goods[$k]['chain_type'] = $model->chain_type;
                $goods[$k]['cramp_ring'] =  $model->cramp_ring;
                $goods[$k]['parts_price'] = $model->gold_price;
            }elseif($purchase_type == PurchaseTypeEnum::MATERIAL_GIFT){
                $goods[$k]['product_type_id'] = $model->product_type_id;
                $goods[$k]['style_cate_id'] = $model->style_cate_id;
                $goods[$k]['style_sex'] = $model->style_sex;
                $goods[$k]['material_type'] = $model->material_type;
                $goods[$k]['material_color'] = $model->material_color;
                $goods[$k]['finger'] = $model->finger;
                $goods[$k]['finger_hk'] = $model->finger_hk;
                $goods[$k]['chain_length'] = $model->chain_length;
                $goods[$k]['main_stone_type'] = $model->main_stone_type;
                $goods[$k]['main_stone_num'] = $model->main_stone_num;
                $goods[$k]['goods_size'] =  $model->goods_size;
                $goods[$k]['gold_price'] = $model->gold_price;
            }else{
                //成品采购
            }
            $total_weight = bcadd($total_weight, $model->goods_weight, 3);
            $total_cost = bcadd($total_cost, $model->cost_price, 2);
        }
        $bill = [
            'supplier_id' => $form->supplier_id,
            'purchase_sn' => $form->purchase_sn,
            'purchase_type' => $purchase_type,
            'to_warehouse_id' => 0,
            'put_in_type' => PutInTypeEnum::PURCHASE,
            'receipt_status' => BillStatusEnum::SAVE,
            'receipt_num' => count($goods),
            'total_weight' => $total_weight,
            'total_cost' => $total_cost,
            'audit_status' => AuditStatusEnum::SAVE,
            'creator_id' => \Yii::$app->user->identity->getId(),
            'created_at' => time(),
        ];
        if($purchase_type == PurchaseTypeEnum::MATERIAL_STONE){
            $bill = ArrayHelper::merge($bill, ['total_stone_num' => $total_stone_num]);
        }
        \Yii::$app->purchaseService->receipt->createReceipt($bill ,$goods);
        if(!empty($detail_ids)){
            $res = $model::updateAll(['is_receipt'=>ConfirmEnum::YES], ['id'=>$detail_ids]);
            if(false === $res){
                throw new \Exception('更新货品状态失败');
            }
        }
    }

    /**
     * 收货统计
     * @param int $id
     * @param int $purchase_type
     * @throws
     */
    public function receiveSummary($id, $purchase_type)
    {
        if (empty($id)) {
            throw new \Exception('ID不能为空');
        }
        if ($purchase_type == PurchaseTypeEnum::MATERIAL_STONE) {
            $model = new PurchaseStone();
            $gModel = new PurchaseStoneGoods();
        } elseif ($purchase_type == PurchaseTypeEnum::MATERIAL_GOLD) {
            $model = new PurchaseGold();
            $gModel = new PurchaseGoldGoods();
        } elseif ($purchase_type == PurchaseTypeEnum::MATERIAL_PARTS) {
            $model = new PurchaseParts();
            $gModel = new PurchasePartsGoods();
        } elseif ($purchase_type == PurchaseTypeEnum::MATERIAL_GIFT) {
            $model = new PurchaseGift();
            $gModel = new PurchaseGiftGoods();
        } else {
            $model = new Purchase();
            $gModel = new PurchaseGoods();
        }
        $purchase = $model::findOne($id);
        $count = $gModel::find()->where(['purchase_id'=>$purchase->id, 'is_receipt' => ConfirmEnum::YES])->count();
        if ($count < $purchase->total_num) {
            $purchase->receive_status = ReceiveStatusEnum::IN_RECEIVE;
        } else {
            $purchase->receive_status = ReceiveStatusEnum::HAS_RECEIVE;
        }
        $purchase->receive_num = $count;
        if (false === $purchase->save()) {
            throw new \Exception($this->getError($purchase));
        }
    }

    /**
     * 创建采购单日志
     * @return $model
     * @throws
     */
    public function createPurchaseLog($log)
    {
        $model = new PurchaseLog();
        $model->attributes = $log;
        $model->log_time = time();
        $model->creator_id = \Yii::$app->user->id;
        $model->creator = \Yii::$app->user->identity->username;
        if(false === $model->save()){
            throw new \Exception($this->getError($model));
        }
        return $model;
    }


 
}
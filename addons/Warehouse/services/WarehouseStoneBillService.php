<?php

namespace addons\Warehouse\services;

use addons\Purchase\common\enums\PurchaseTypeEnum;
use addons\Purchase\common\models\PurchaseGoldReceiptGoods;
use addons\Purchase\common\models\PurchaseReceipt;
use addons\Purchase\common\models\PurchaseStoneReceiptGoods;
use addons\Warehouse\common\enums\BillTypeEnum;
use addons\Warehouse\common\enums\BillWStatusEnum;
use addons\Warehouse\common\enums\FinAuditStatusEnum;
use addons\Warehouse\common\enums\GoldBillStatusEnum;
use addons\Warehouse\common\enums\GoldBillTypeEnum;
use addons\Warehouse\common\enums\GoldStatusEnum;
use addons\Warehouse\common\enums\PandianAdjustEnum;
use addons\Warehouse\common\enums\PandianStatusEnum;
use addons\Warehouse\common\enums\StoneBillStatusEnum;
use addons\Warehouse\common\enums\StoneBillTypeEnum;
use addons\Warehouse\common\enums\StoneStatusEnum;
use addons\Warehouse\common\forms\WarehouseBillWForm;
use addons\Warehouse\common\models\WarehouseBillW;
use addons\Warehouse\common\models\WarehouseGold;
use addons\Warehouse\common\models\WarehouseGoldBill;
use addons\Warehouse\common\models\WarehouseGoldBillGoods;
use addons\Warehouse\common\models\WarehouseGoldBillGoodsW;
use addons\Warehouse\common\models\WarehouseGoldBillW;
use addons\Warehouse\common\models\WarehouseStone;
use addons\Warehouse\common\models\WarehouseStoneBill;
use addons\Warehouse\common\models\WarehouseStoneBillGoods;
use addons\Warehouse\common\models\WarehouseStoneBillGoodsW;
use addons\Warehouse\common\models\WarehouseStoneBillW;
use common\enums\ConfirmEnum;
use common\helpers\Url;
use Yii;
use common\components\Service;
use common\helpers\SnHelper;
use addons\Warehouse\common\models\WarehouseBill;
use addons\Warehouse\common\models\WarehouseGoods;
use addons\Warehouse\common\models\WarehouseBillGoods;
use addons\Purchase\common\enums\ReceiptGoodsStatusEnum;
use addons\Purchase\common\models\PurchaseReceiptGoods;
use addons\Warehouse\common\enums\BillStatusEnum;
use addons\Warehouse\common\enums\GoodsStatusEnum;
use addons\Warehouse\common\enums\OrderTypeEnum;
use common\enums\AuditStatusEnum;
use common\enums\StatusEnum;
use common\helpers\ArrayHelper;
use addons\Supply\common\models\ProduceStone;

/**
 * 石包单据
 * @package services\common
 * @author jianyan74 <751393839@qq.com>
 */
class WarehouseStoneBillService extends Service
{

    /**
     * 石包单据明细 tab
     * @param int $bill_id 单据ID
     * @param $returnUrl URL
     * @return array
     */
    public function menuTabList($bill_id, $bill_type, $returnUrl = null, $tag = null)
    {
        $tabList = [];
        switch ($bill_type){

            case StoneBillTypeEnum::STONE_MS:
                {
                    if(!$tag){
                        $tabList = [
                            1=>['name'=>'单据详情','url'=>Url::to(['stone-bill-ms/view','id'=>$bill_id,'tab'=>1,'returnUrl'=>$returnUrl])],
                            2=>['name'=>'单据明细','url'=>Url::to(['stone-bill-ms-goods/index','bill_id'=>$bill_id,'tab'=>2,'returnUrl'=>$returnUrl])],
                            4=>['name'=>'日志列表','url'=>Url::to(['stone-bill-log/index','bill_id'=>$bill_id,'tab'=>4,'returnUrl'=>$returnUrl])]
                        ];
                    }else{
                        $tabList = [
                            1=>['name'=>'单据详情','url'=>Url::to(['stone-bill-ms/view','id'=>$bill_id,'tab'=>1,'returnUrl'=>$returnUrl])],
                            3=>['name'=>'单据明细(编辑)','url'=>Url::to(['stone-bill-ms-goods/edit-all','bill_id'=>$bill_id,'tab'=>3,'returnUrl'=>$returnUrl])],
                            4=>['name'=>'日志列表','url'=>Url::to(['stone-bill-log/index','bill_id'=>$bill_id,'tab'=>4,'returnUrl'=>$returnUrl])]
                        ];
                    }
                    break;
                }
            case StoneBillTypeEnum::STONE_SS:
                {
                    if(!$tag){
                        $tabList = [
                            1=>['name'=>'单据详情','url'=>Url::to(['stone-bill-ss/view','id'=>$bill_id,'tab'=>1,'returnUrl'=>$returnUrl])],
                            2=>['name'=>'单据明细','url'=>Url::to(['stone-bill-ss-goods/index','bill_id'=>$bill_id,'tab'=>2,'returnUrl'=>$returnUrl])],
                            4=>['name'=>'日志列表','url'=>Url::to(['stone-bill-log/index','bill_id'=>$bill_id,'tab'=>4,'returnUrl'=>$returnUrl])]
                        ];
                    }else{
                        $tabList = [
                            1=>['name'=>'单据详情','url'=>Url::to(['stone-bill-ss/view','id'=>$bill_id,'tab'=>1,'returnUrl'=>$returnUrl])],
                            3=>['name'=>'单据明细(编辑)','url'=>Url::to(['stone-bill-ss-goods/edit-all','bill_id'=>$bill_id,'tab'=>3,'returnUrl'=>$returnUrl])],
                            4=>['name'=>'日志列表','url'=>Url::to(['stone-bill-log/index','bill_id'=>$bill_id,'tab'=>4,'returnUrl'=>$returnUrl])]
                        ];
                    }
                    break;
                }
            case StoneBillTypeEnum::STONE_TS:
                {
                    if(!$tag){
                        $tabList = [
                            1=>['name'=>'单据详情','url'=>Url::to(['stone-bill-ts/view','id'=>$bill_id,'tab'=>1,'returnUrl'=>$returnUrl])],
                            2=>['name'=>'单据明细','url'=>Url::to(['stone-bill-ts-goods/index','bill_id'=>$bill_id,'tab'=>2,'returnUrl'=>$returnUrl])],
                            4=>['name'=>'日志列表','url'=>Url::to(['stone-bill-log/index','bill_id'=>$bill_id,'tab'=>3,'returnUrl'=>$returnUrl])]
                        ];
                    }else{
                        $tabList = [
                            1=>['name'=>'单据详情','url'=>Url::to(['stone-bill-ts/view','id'=>$bill_id,'tab'=>1,'returnUrl'=>$returnUrl])],
                            3=>['name'=>'单据明细(编辑)','url'=>Url::to(['stone-bill-ts-goods/edit-all','bill_id'=>$bill_id,'tab'=>3,'returnUrl'=>$returnUrl])],
                            4=>['name'=>'日志列表','url'=>Url::to(['stone-bill-log/index','bill_id'=>$bill_id,'tab'=>4,'returnUrl'=>$returnUrl])]
                        ];
                    }
                    break;
                }
            case StoneBillTypeEnum::STONE_W:
                {
                    if(!$tag){
                        $tabList = [
                            1=>['name'=>'单据详情','url'=>Url::to(['stone-bill-w/view','id'=>$bill_id,'tab'=>1,'returnUrl'=>$returnUrl])],
                            2=>['name'=>'单据明细','url'=>Url::to(['stone-bill-w-goods/index','bill_id'=>$bill_id,'tab'=>2,'returnUrl'=>$returnUrl])],
                            4=>['name'=>'日志列表','url'=>Url::to(['stone-bill-log/index','bill_id'=>$bill_id,'tab'=>4,'returnUrl'=>$returnUrl])]
                        ];
                    }else{
                        $tabList = [
                            1=>['name'=>'单据详情','url'=>Url::to(['stone-bill-w/view','id'=>$bill_id,'tab'=>1,'returnUrl'=>$returnUrl])],
                            3=>['name'=>'单据明细(编辑)','url'=>Url::to(['stone-bill-w-goods/edit-all','bill_id'=>$bill_id,'tab'=>3,'returnUrl'=>$returnUrl])],
                            4=>['name'=>'日志列表','url'=>Url::to(['stone-bill-log/index','bill_id'=>$bill_id,'tab'=>4,'returnUrl'=>$returnUrl])]
                        ];
                    }
                    break;
                }
        }
        return $tabList;
    }

    /**
     * 单据汇总
     * @param integer $bill_id
     * @throws \Exception
     */
    public function stoneBillSummary($bill_id)
    {
        $sum = WarehouseStoneBillGoods::find()
            ->select(['sum(1) as total_num','sum(stone_weight) as total_weight','sum(cost_price) as total_cost'])
            ->where(['bill_id'=>$bill_id, 'status'=>StatusEnum::ENABLED])
            ->asArray()->one();
        if($sum) {
            $result = WarehouseStoneBill::updateAll(['total_num'=>$sum['total_num']/1,'total_weight'=>$sum['total_weight']/1,'total_cost'=>$sum['total_cost']/1],['id'=>$bill_id]);
        }
        return $result?:null;
    }
    /**
     * 领石单（送石单）
     * @param array $bill
     * @param array $details
     */
    public function createBillSs($bill,$details) 
    {
        $billM = new WarehouseStoneBill();
        $billM->attributes = $bill;
        $billM->bill_no = SnHelper::createBillSn($billM->bill_type);
        $billM->bill_status = BillStatusEnum::SAVE;
        if(false === $billM->save()){
            throw new \Exception($this->getError($billM));
        }
        $goodsM = new WarehouseStoneBillGoods();
        foreach ($details as &$good){
            $good['bill_id'] = $billM->id;
            $good['bill_type'] = $billM->bill_type;
            $good['bill_no'] = $billM->bill_no;
            $goodsM->setAttributes($good);
            if(!$goodsM->validate()){
                throw new \Exception($this->getError($goodsM));
            }
        }
        $details = ArrayHelper::toArray($details);
        $value = [];
        $key = array_keys($details[0]);
        foreach ($details as $detail) {
            $value[] = array_values($detail);
        }
        $res = Yii::$app->db->createCommand()->batchInsert(WarehouseStoneBillGoods::tableName(), $key, $value)->execute();
        if(false === $res){
            throw new \Exception("创建领石单单明细失败");
        } 
        
        $this->stoneBillSummary($billM->id);
        return $billM;
    }
    /**
     * 创建买石单
     * @param array $bill
     * @param array $details
     */
    public function createBillMs($bill, $details){
        $billM = new WarehouseStoneBill();
        $billM->attributes = $bill;
        $billM->bill_no = SnHelper::createBillSn($billM->bill_type);
        if(false === $billM->save()){
            throw new \Exception($this->getError($billM));
        }
        $goodsM = new WarehouseStoneBillGoods();
        foreach ($details as &$good){
            $good['bill_id'] = $billM->id;
            $good['bill_type'] = $billM->bill_type;
            $good['bill_no'] = $billM->bill_no;
            $goodsM->setAttributes($good);
            if(!$goodsM->validate()){
                throw new \Exception($this->getError($goodsM));
            }
        }
        $details = ArrayHelper::toArray($details);
        $value = [];
        $key = array_keys($details[0]);
        foreach ($details as $detail) {
            $value[] = array_values($detail);
        }
        $res = Yii::$app->db->createCommand()->batchInsert(WarehouseStoneBillGoods::tableName(), $key, $value)->execute();
        if(false === $res){
            throw new \Exception("创建买石单明细失败");
        }
        
        $this->stoneBillSummary($billM->id);
        
        return $billM;
    }
    /**
     * 买石单-审核
     * @param $form
     */
    public function auditBillMs($form)
    {
        if(false === $form->validate()) {
            throw new \Exception($this->getError($form));
        }
        if($form->audit_status == AuditStatusEnum::PASS){
            $form->bill_status = BillStatusEnum::CONFIRM;

            $billGoodsList = WarehouseStoneBillGoods::find()->where(['bill_id' => $form->id])->all();
            if(empty($billGoodsList)){
                throw new \Exception("单据明细不能为空");
            }
            //石包入库
            foreach ($billGoodsList as $billGoods) {
                $stoneM = new WarehouseStone();
                $stoneData = [
                        'stone_sn' => rand(10000000000,99999999999),//临时
                        'stone_name' => $billGoods->stone_name,
                        'stone_status' => StoneStatusEnum::IN_STOCK,
                        'style_sn' => $billGoods->style_sn,
                        'stone_type' => $billGoods->stone_type,
                        'supplier_id' => $billGoods->supplier_id,
                        'stone_color' => $billGoods->color,
                        'stone_clarity' => $billGoods->clarity,
                        'stock_cnt' => $billGoods->stone_num,
                        'ms_cnt' => $billGoods->stone_num,
                        'stock_weight' => $billGoods->stone_weight,
                        'ms_weight' => $billGoods->stone_weight,
                        'cost_price' => $billGoods->cost_price,
                        'sale_price' => $billGoods->sale_price,
                ];
                $stoneM->attributes = $stoneData;
                if(false === $stoneM->save()){
                    throw new \Exception($this->getError($stoneM));
                }
                \Yii::$app->warehouseService->stone->createStoneSn($stoneM);
                //同步更新石料编号到单据明细
                $billGoods->stone_sn = $stoneM->stone_sn;
                if(false === $billGoods->save(true,['id','stone_sn'])) {
                    throw new \Exception($this->getError($billGoods));
                }
            }
            //同步石料采购收货单货品状态
            $queryId = WarehouseStoneBillGoods::find()->select(['source_detail_id']);
            $res = PurchaseStoneReceiptGoods::updateAll(['goods_status'=>ReceiptGoodsStatusEnum::WAREHOUSE], ['id'=>$queryId]);
            if(false === $res) {
                throw new \Exception("同步石料采购收货单货品状态失败");
            }
            
        }else{
            $form->bill_status = BillStatusEnum::SAVE;
        }
        if(false === $form->save()) {
            throw new \Exception($this->getError($form));
        }
    }
    /**
     * 添加单据明细
     * @param $form
     */
    public function createBillGoods($form)
    {
        $stone = WarehouseStone::findOne(['stone_name'=>$form->stone_name]);
        $bill = WarehouseStoneBill::findOne(['id'=>$form->bill_id]);
        $goods = [
            'bill_id' => $form->bill_id,
            'bill_type' => $bill->bill_type,
            'stone_name' => $stone->stone_name,
            'stone_type' => $stone->stone_type,
            'stone_num' => $form->stone_num,
            'stone_weight' => $form->stone_weight,
            'color' => $stone->stone_color,
            'clarity' => $stone->stone_clarity,
            'cost_price' => $stone->cost_price,
            'sale_price' => $stone->sale_price,
            'status' => StatusEnum::ENABLED,
            'created_at' => time()
        ];
        $billGoods = new WarehouseStoneBillGoods();
        $billGoods->attributes = $goods;
        if(false === $billGoods->save()) {
            throw new \Exception($this->getError($billGoods));
        }
        if(false === $form->save()) {
            throw new \Exception($this->getError($form));
        }
    }

}
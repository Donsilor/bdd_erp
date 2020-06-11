<?php

namespace addons\Purchase\services;


use common\helpers\ArrayHelper;
use Yii;
use common\components\Service;
use common\helpers\Url;
use addons\Purchase\common\models\PurchaseReceipt;
use addons\Purchase\common\models\PurchaseReceiptGoods;
use addons\Purchase\common\enums\DefectiveStatusEnum;
use addons\Purchase\common\enums\PurchaseTypeEnum;
use addons\Purchase\common\enums\ReceiptGoodsStatusEnum;
use addons\Purchase\common\models\PurchaseDefective;
use addons\Purchase\common\models\PurchaseDefectiveGoods;
use addons\Purchase\common\forms\PurchaseReceiptGoodsForm;
use addons\Purchase\common\models\PurchaseGoldReceiptGoods;
use addons\Purchase\common\models\PurchaseStoneReceiptGoods;
use addons\Warehouse\common\forms\WarehouseBillBForm;
use addons\Warehouse\common\enums\BillStatusEnum;
use addons\Warehouse\common\enums\BillTypeEnum;
use addons\Warehouse\common\enums\GoodsStatusEnum;
use addons\Warehouse\common\enums\OrderTypeEnum;
use addons\Supply\common\enums\QcTypeEnum;
use common\enums\AuditStatusEnum;
use common\enums\StatusEnum;
use addons\Style\common\enums\AttrIdEnum;
use addons\Supply\common\models\Produce;
use addons\Supply\common\models\ProduceAttribute;
use addons\Supply\common\models\ProduceShipment;
use common\helpers\SnHelper;
use yii\db\Exception;

/**
 * Class TypeService
 * @package services\common
 * @author jianyan74 <751393839@qq.com>
 */
class PurchaseReceiptService extends Service
{
    
    /**
     * 采购收货单明细 tab
     * @param int $id 采购单ID
     * @return array
     */
    public function menuTabList($receipt_id, $purchase_type, $returnUrl = null, $tag = null)
    {
        $tabList = [];
        switch ($purchase_type){

            case PurchaseTypeEnum::GOODS:
                {
                    if($tag==3){
                        $tabList = [
                            1=>['name'=>'基础信息','url'=>Url::to(['receipt/view','id'=>$receipt_id,'tab'=>1,'returnUrl'=>$returnUrl])],
                            3=>['name'=>'单据明细(编辑)','url'=>Url::to(['receipt-goods/edit-all','receipt_id'=>$receipt_id,'tab'=>3,'returnUrl'=>$returnUrl])],
                            4=>['name'=>'日志信息','url'=>Url::to(['receipt-log/index','receipt_id'=>$receipt_id,'tab'=>4,'returnUrl'=>$returnUrl])]
                        ];
                    }else{
                        $tabList = [
                            1=>['name'=>'基础信息','url'=>Url::to(['receipt/view','id'=>$receipt_id,'tab'=>1,'returnUrl'=>$returnUrl])],
                            2=>['name'=>'单据明细','url'=>Url::to(['receipt-goods/index','receipt_id'=>$receipt_id,'tab'=>2,'returnUrl'=>$returnUrl])],
                            4=>['name'=>'日志信息','url'=>Url::to(['receipt-log/index','receipt_id'=>$receipt_id,'tab'=>4,'returnUrl'=>$returnUrl])]
                        ];
                    }
                    break;
                }
            case PurchaseTypeEnum::MATERIAL_STONE:
                {
                    if($tag==3){
                        $tabList = [
                            1=>['name'=>'基础信息','url'=>Url::to(['stone-receipt/view','id'=>$receipt_id,'tab'=>1,'returnUrl'=>$returnUrl])],
                            3=>['name'=>'单据明细(编辑)','url'=>Url::to(['stone-receipt-goods/edit-all','receipt_id'=>$receipt_id,'tab'=>3,'returnUrl'=>$returnUrl])],
                            4=>['name'=>'日志信息','url'=>Url::to(['receipt-log/index','receipt_id'=>$receipt_id,'tab'=>4,'returnUrl'=>$returnUrl])]
                        ];
                    }else{
                        $tabList = [
                            1=>['name'=>'基础信息','url'=>Url::to(['stone-receipt/view','id'=>$receipt_id,'tab'=>1,'returnUrl'=>$returnUrl])],
                            2=>['name'=>'单据明细','url'=>Url::to(['stone-receipt-goods/index','receipt_id'=>$receipt_id,'tab'=>2,'returnUrl'=>$returnUrl])],
                            4=>['name'=>'日志信息','url'=>Url::to(['receipt-log/index','receipt_id'=>$receipt_id,'tab'=>4,'returnUrl'=>$returnUrl])]
                        ];
                    }
                    break;
                }
            case PurchaseTypeEnum::MATERIAL_GOLD:
                {
                    if($tag==3){
                        $tabList = [
                            1=>['name'=>'基础信息','url'=>Url::to(['gold-receipt/view','id'=>$receipt_id,'tab'=>1,'returnUrl'=>$returnUrl])],
                            3=>['name'=>'单据明细(编辑)','url'=>Url::to(['gold-receipt-goods/edit-all','receipt_id'=>$receipt_id,'tab'=>3,'returnUrl'=>$returnUrl])],
                            4=>['name'=>'日志信息','url'=>Url::to(['receipt-log/index','receipt_id'=>$receipt_id,'tab'=>4,'returnUrl'=>$returnUrl])]
                        ];
                    }else{
                        $tabList = [
                            1=>['name'=>'基础信息','url'=>Url::to(['gold-receipt/view','id'=>$receipt_id,'tab'=>1,'returnUrl'=>$returnUrl])],
                            2=>['name'=>'单据明细','url'=>Url::to(['gold-receipt-goods/index','receipt_id'=>$receipt_id,'tab'=>2,'returnUrl'=>$returnUrl])],
                            4=>['name'=>'日志信息','url'=>Url::to(['receipt-log/index','receipt_id'=>$receipt_id,'tab'=>4,'returnUrl'=>$returnUrl])]
                        ];
                    }
                    break;
                }
        }
        return $tabList;
    }

    /**
     * 创建采购收货单
     * @param array $bill
     * @param array $detail
     */
    public function createReceipt($bill, $detail)
    {
        $billM = new PurchaseReceipt();
        $billM->attributes = $bill;
        $billM->receipt_no = SnHelper::createReceiptSn();

        if(false === $billM->validate()) {
            throw new \Exception($this->getError($billM));
        }
        if(false === $billM->save()) {
            throw new \Exception($this->getError($billM));
        }

        $receipt_id = $billM->attributes['id'];
        foreach ($detail as $good) {
            if($billM->purchase_type == PurchaseTypeEnum::MATERIAL_STONE){
                $goods = new PurchaseStoneReceiptGoods();
            }elseif($billM->purchase_type == PurchaseTypeEnum::MATERIAL_GOLD){
                $goods = new PurchaseGoldReceiptGoods();
            }else{
                $goods = new PurchaseReceiptGoods();
            }
            $purchase_detail_id = $good['purchase_detail_id']??"";
            if($purchase_detail_id){
                $count = $goods::find()->where(['purchase_detail_id'=>$purchase_detail_id])->count(1);
                if($count >= 1){
                    throw new \Exception("采购单已收货，不能重复收货");
                }
            }
            $goods->attributes = $good;
            $goods->receipt_id = $receipt_id;
            if(false === $goods->validate()) {
                throw new \Exception($this->getError($goods));
            }
            if(false === $goods->save()) {
                throw new \Exception($this->getError($goods));
            }
        }
    }
    
    /**
     * 采购收货单汇总
     * @param unknown $receipt_id
     */
    public function purchaseReceiptSummary($receipt_id)
    {
        $result = false;
        $sum = PurchaseReceiptGoods::find()
                    ->select(['sum(1) as receipt_num','sum(cost_price) as total_cost'])
                    ->where(['receipt_id'=>$receipt_id, 'status'=>StatusEnum::ENABLED])
                    ->asArray()->one();
        if($sum) {
            $result = PurchaseReceipt::updateAll(['receipt_num'=>$sum['receipt_num']/1,'total_cost'=>$sum['total_cost']/1],['id'=>$receipt_id]);
        }
        return $result;
    }

    /**
     * 布产单号批量查询可出货商品
     * @param object $form
     */
    public function getGoodsByProduceSn($form)
    {
        $produce_sns = $form->getProduceSns();
        if(!$produce_sns){
            throw new \Exception("布产单号不能为空");
        }
        $receipt_goods = [];
        foreach ($produce_sns as $produce_sn) {
            $produce = Produce::findOne(['produce_sn' => $produce_sn]);
            $message="布产单".$produce_sn;
            if (!$produce) {
                throw new \Exception($message."单号不对");
            }
            if ($form->supplier_id != $produce->supplier_id) {
                throw new \Exception($message."供应商与收货单供应商不一致");
            }
            $shippent_num = ProduceShipment::find()->where(['produce_id' => $produce->id])->sum('shippent_num');
            if (!$shippent_num) {
                throw new \Exception($message."未出货");
            }
            $receipt_num = PurchaseReceiptGoods::find()->where(['produce_sn' => $produce_sn])->count();
            $the_num = bcsub($shippent_num, $receipt_num);
            if (!$the_num) {
                throw new \Exception($message."没有可出货数量");
            }
            $produce_attr = ProduceAttribute::find()->where(['produce_id' => $produce->id])->asArray()->all();
            $attr_arr = [];
            foreach ($produce_attr as $attr) {
                $attr_name = Yii::$app->styleService->attribute->getAttrNameByAttrId($attr['attr_id']);
                $attr_arr[$attr['attr_id']]['attr_name'] = $attr_name;
                $attr_arr[$attr['attr_id']]['attr_value'] = $attr['attr_value'];
                $attr_arr[$attr['attr_id']]['attr_value_id'] = $attr['attr_value_id'];
            }
            $goodsM = new PurchaseReceiptGoods();
            for ($i = 1; $i <= $the_num; $i++) {
                $goods = [
                    'receipt_id' => $form->id,
                    'produce_sn' => $produce_sn,
                    'purchase_sn' => $produce->from_order_sn,
                    'goods_name' => $produce->goods_name,
                    'goods_num' => 1,
                    'style_sn' => $produce->qiban_sn ?: $produce->style_sn,
                    'style_cate_id' => $produce->style_cate_id,
                    'product_type_id' => $produce->product_type_id,
                    'finger' => $attr_arr[AttrIdEnum::FINGER]['attr_value'] ?? '',
                    'xiangkou' => $attr_arr[AttrIdEnum::XIANGKOU]['attr_value'] ?? '',
                    'material' => $attr_arr[AttrIdEnum::MATERIAL]['attr_value_id'] ?? '',
                    'jintuo_type' => $produce->jintuo_type,
                ];
                $receipt_goods[] = ArrayHelper::merge($goodsM->getAttributes(),$goods);
            }
        }
        return $receipt_goods??[];
    }

    /**
     * 添加采购收货单商品明细
     * @param PurchaseReceiptGoodsForm $form
     */
    public function addReceiptGoods($form)
    {
        if(!empty($form->goods)){
            $value = [];
            $key = array_keys($form->goods[0]);
            array_push($key,'receipt_id', 'xuhao');
            $xuhaoMax = PurchaseReceiptGoods::find()->where(['receipt_id' => $form->id])->select(['xuhao'])->orderBy(['xuhao' => SORT_DESC])->one();
            $xuhao = $xuhaoMax->xuhao?:0;
            foreach ($form->goods as $good) {
                $xuhao++;
                array_push($good, $form->id, $xuhao);
                $value[] = array_values($good);
            }
            $res= \Yii::$app->db->createCommand()->batchInsert(PurchaseReceiptGoods::tableName(), $key, $value)->execute();
            if(false === $res){
                throw new \yii\base\Exception("保存失败");
            }
            //更新采购收货单汇总：总金额和总数量
            $this->purchaseReceiptSummary($form->id);
        }
    }


    /**
     * 同步采购收货单生成L单
     * @param object $form
     * @param array $detail_ids
     * @throws \Exception
     */
    public function syncReceiptToBillInfoL($form, $detail_ids = null)
    {
        if($form->audit_status != AuditStatusEnum::PASS){
            throw new \Exception('采购收货单没有审核');
        }
        if($form->receipt_num <= 0 ){
            throw new \Exception('采购收货单没有明细');
        }
        $query = PurchaseReceiptGoods::find()->where(['receipt_id'=>$form->id, 'goods_status' => ReceiptGoodsStatusEnum::IQC_PASS]);
        $detail_ids = $form->getIds();
        if(!empty($detail_ids)) {
            $query->andWhere(['id'=>$detail_ids]);
        }
        $models = $query->all();
        if(!$models){
            throw new \Exception('采购收货单没有待入库的货品');
        }
        $goods = $ids = [];
        $total_cost= $market_price= $sale_price = 0;
        foreach ($models as $model){
            $ids[] = $model->id;
            $goods[] = [
                'goods_name' =>$model->goods_name,
                'style_sn' => $model->style_sn,
                'product_type_id'=>$model->product_type_id,
                'style_cate_id'=>$model->style_cate_id,
                'goods_status'=>GoodsStatusEnum::RECEIVING,
                'supplier_id'=>$form->supplier_id,
                'put_in_type'=>$form->put_in_type,
                'company_id'=> 1,//暂时为1
                'warehouse_id' => $form->to_warehouse_id?:0,
                'gold_weight' => $model->gold_weight?:0,
                'gold_loss' => $model->gold_loss?:0,
                'gross_weight' => (String) $model->gross_weight,
                'finger' => (String) $model->finger?:'0',
                'produce_sn' => $model->produce_sn,
                'cert_id' => $model->cert_id,
                'goods_num' => $model->goods_num,
                'material' => (String) $model->material,
                'material_type' => '',
                'material_color' => '',
                'diamond_carat' => $model->main_stone_weight,
                'diamond_clarity' => (String) $model->main_stone_clarity,
                'jintuo_type' => $model->jintuo_type,
                'market_price' => $model->market_price,
                'xiangkou' => $model->xiangkou?:0,
                'parts_gold_weight' => $model->parts_weight,
                'parts_num' => 1,
                'main_stone_type' => $model->main_stone,
                'main_stone_num' => $model->main_stone_num,
                'second_stone_type1' => (String) $model->second_stone1,
                'second_stone_num1' => $model->second_stone_num1,
                'second_stone_price1' => $model->second_stone_price1,
                'second_stone_weight1' => $model->second_stone_weight1,
                'second_stone_type2' => (String) $model->second_stone2,
                'second_stone_num2' => $model->second_stone_num2,
                'second_stone_weight2' => $model->second_stone_weight2,
                'second_stone_price2' => $model->second_stone_price2,
                'creator_id' => \Yii::$app->user->identity->getId(),
                'created_at' => time(),
            ];
            $bill_goods[] = [
                'goods_name' => $model->goods_name,
                'style_sn' => $model->style_sn,
                'goods_num' => $model->goods_num,
                'put_in_type' => $model->put_in_type,
                'material' => $model->material,
                'gold_weight' => $model->gold_weight,
                'gold_loss' => $model->gold_loss,
                'diamond_carat' =>$model->main_stone_weight,
                'diamond_color' =>$model->main_stone_color,
                'diamond_clarity' => $model->main_stone_clarity,
                'diamond_cert_id' => $model->cert_id,
                'source_detail_id' => $model->id,
                'cost_price' => $model->cost_price,
                'sale_price' => $model->sale_price,
                'market_price' => $model->market_price,
                'markup_rate' => $model->markup_rate,
                'status' => StatusEnum::ENABLED,
                'creator_id' => \Yii::$app->user->identity->getId(),
                'created_at' => time(),
            ];
            $total_cost = bcadd($total_cost, $model->cost_price, 2);
            $market_price = bcadd($market_price, $model->market_price, 2);
            $sale_price = bcadd($sale_price, $model->sale_price, 2);
        }
        //批量更新采购收货单货品状态
        $res = PurchaseReceiptGoods::updateAll(['goods_status'=>ReceiptGoodsStatusEnum::WAREHOUSE_ING, 'put_in_type'=>$form->put_in_type, 'to_warehouse_id'=>$form->to_warehouse_id],['id'=>$ids]);
        if(false === $res){
            throw new \Exception('更新采购收货单货品状态失败');
        }
        $bill = [
            'bill_type' =>  BillTypeEnum::BILL_TYPE_L,
            'bill_status' => BillStatusEnum::SAVE,
            'supplier_id' => $form->supplier_id,
            'put_in_type' => $form->put_in_type,
            'order_type' => OrderTypeEnum::ORDER_L,
            'goods_num' => count($goods),
            'total_cost' => $total_cost,
            'total_sale' => $sale_price,
            'total_market' => $market_price,
            'to_warehouse_id' => $form->to_warehouse_id,
            'to_company_id' => 0,
            'from_company_id' => 0,
            'from_warehouse_id' => 0,
            'send_goods_sn' => $form->receipt_no,
            'creator_id' => \Yii::$app->user->identity->getId(),
            'created_at' => time(),
        ];
        Yii::$app->warehouseService->billL->createBillL($goods, $bill, $bill_goods);
    }

    /**
     *  IQC质检合法验证
     * @param $ids
     */
    public function iqcValidate($form){
        $ids = $form->getIds();
        if(is_array($ids)){
            foreach ($ids as $id) {
                $goods = PurchaseReceiptGoods::findOne(['id'=>$id]);
                if($goods->goods_status != ReceiptGoodsStatusEnum::IQC_ING){
                    throw new Exception("流水号【{$id}】不是待质检状态，不能质检");
                }
            }
        }
    }

    /**
     *  IQC质检
     * @param WarehouseBillBForm $form
     */
    public function qcIqc($form)
    {
        $this->iqcValidate($form);
        //if(false === $form->validate()) {
            //throw new \Exception($this->getError($form));
        //}
        $ids = $form->getIds();
        if($form->goods_status == QcTypeEnum::PASS){
            $goods = ['goods_status' =>ReceiptGoodsStatusEnum::IQC_PASS];
        }else{
            $goods = ['goods_status' =>ReceiptGoodsStatusEnum::IQC_NO_PASS, 'iqc_reason' => $form->iqc_reason, 'iqc_remark' => $form->iqc_remark];
        }
        $res = PurchaseReceiptGoods::updateAll($goods, ['id'=>$ids]);
        if(false === $res) {
            throw new Exception("保存失败");
        }
    }

    /**
     *  申请入库合法验证
     * @param $ids
     */
    public function warehouseValidate($form){
        $ids = $form->getIds();
        if(is_array($ids)){
            foreach ($ids as $id) {
                $goods = PurchaseReceiptGoods::findOne(['id'=>$id]);
                if($goods->goods_status != ReceiptGoodsStatusEnum::IQC_PASS){
                    throw new Exception("序号【{$goods->xuhao}】不是IQC质检通过状态，不能入库");
                }
            }
        }
    }

    /**
     *  批量生成不良返厂单
     * @param WarehouseBillBForm $form
     */
    public function batchDefective($form)
    {
        $ids = $form->getIds();
        if(!count($ids)>1){
            throw new Exception("至少选择一个货品");
        }
        if(!$form->checkDistinct('receipt_no', $ids)){
            throw new Exception("不是同一个出货单号不允许制单");
        }
        if(!$form->checkDistinct('supplier_id', $ids)){
            throw new Exception("不是同一个供应商不允许制单");
        }
        $total_cost = 0;
        $receipt = $defect = $detail = [];
        foreach($ids as $id)
        {
            $goods = PurchaseReceiptGoods::find()->where(['id'=>$id])->one();
            $receipt_id = $goods->receipt_id;
            if(!$receipt){
                $receipt = PurchaseReceipt::find()->where(['id' => $receipt_id])->one();
                $defect = PurchaseDefective::find()->select(['id'])->where(['receipt_no'=>$receipt->receipt_no])->one();
            }
            if($goods->goods_status != ReceiptGoodsStatusEnum::IQC_NO_PASS)
            {
                throw new Exception("流水号【{$id}】不是IQC质检未过状态，不能生成不良品返厂单");
            }
            if($defect){
                $check = PurchaseDefectiveGoods::find()->where(['defective_id'=>$defect->id, 'xuhao' => $goods->xuhao])->count(1);
                if($check){
                    throw new Exception("流水号【{$id}】已存在保存状态的不良返厂单，不能多次生成不良品返厂单");
                }
            }
            $detail[] = [
                'xuhao' => $goods->xuhao,
                'style_sn' => $goods->style_sn,
                'factory_mo' => $goods->factory_mo,
                'produce_sn' => $goods->produce_sn,
                'style_cate_id' => $goods->style_cate_id,
                'product_type_id' => $goods->product_type_id,
                'cost_price' => $goods->cost_price,
                'iqc_reason' => $goods->iqc_reason,
                'iqc_remark' => $goods->iqc_remark,
                'created_at' => time(),
            ];
            $total_cost = bcadd($total_cost, $goods->cost_price, 2);
        }
        $bill = [
            'supplier_id' => $receipt->supplier_id,
            'receipt_no' => $receipt->receipt_no,
            'defective_num' => count($detail),
            'total_cost' => $total_cost,
            'audit_status' => AuditStatusEnum::PENDING,
            'defective_status' => DefectiveStatusEnum::PENDING,
            'creator_id' => \Yii::$app->user->identity->getId(),
            'created_at' => time(),
        ];

        \Yii::$app->purchaseService->defective->createDefactiveBill($bill, $detail);

        $res = PurchaseReceiptGoods::updateAll(['goods_status' =>ReceiptGoodsStatusEnum::FACTORY_ING], ['id'=>$ids]);
        if(false === $res) {
            throw new Exception("更新货品状态失败");
        }
    }


}
<?php

namespace addons\Purchase\services;

use addons\Warehouse\common\enums\WarehouseIdEnum;
use Yii;
use common\components\Service;
use addons\Purchase\common\models\Purchase;
use addons\Purchase\common\models\PurchaseGoods;
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
use addons\Purchase\common\enums\ReceiptStatusEnum;
use addons\Purchase\common\forms\PurchaseReceiptForm;
use addons\Supply\common\enums\PeiliaoStatusEnum;
use addons\Supply\common\models\ProduceGold;
use addons\Supply\common\models\ProduceGoldGoods;
use addons\Supply\common\models\ProduceStone;
use addons\Supply\common\models\ProduceStoneGoods;
use addons\Warehouse\common\models\WarehouseStone;
use addons\Supply\common\models\Produce;
use addons\Supply\common\models\ProduceAttribute;
use addons\Supply\common\models\ProduceShipment;
use addons\Warehouse\common\enums\AdjustTypeEnum;
use addons\Warehouse\common\enums\GoldBillTypeEnum;
use addons\Warehouse\common\enums\StoneBillTypeEnum;
use addons\Warehouse\common\enums\GoldBillStatusEnum;
use addons\Warehouse\common\enums\BillStatusEnum;
use addons\Warehouse\common\enums\BillTypeEnum;
use addons\Warehouse\common\enums\OrderTypeEnum;
use addons\Supply\common\enums\QcTypeEnum;
use addons\Style\common\enums\AttrIdEnum;
use common\enums\AuditStatusEnum;
use common\enums\StatusEnum;
use common\helpers\ArrayHelper;
use common\helpers\SnHelper;
use common\helpers\Url;
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
     * @param int $receipt_id 采购收货单ID
     * @param int $purchase_type 采购类型
     * @param string $returnUrl
     * @param int $tag 页签ID
     * @return array
     */
    public function menuTabList($receipt_id, $purchase_type, $returnUrl = null, $tag = null)
    {
        $tabList = $tab = [];
        switch ($purchase_type){
            case PurchaseTypeEnum::GOODS:
                {
                    $tabList = [
                        1=>['name'=>'基础信息','url'=>Url::to(['receipt/view','id'=>$receipt_id,'tab'=>1,'returnUrl'=>$returnUrl])],
                        4=>['name'=>'日志信息','url'=>Url::to(['receipt-log/index','receipt_id'=>$receipt_id,'tab'=>4,'returnUrl'=>$returnUrl])]
                    ];
                    if($tag!=3){
                        $tab = [2=>['name'=>'单据明细','url'=>Url::to(['receipt-goods/index','receipt_id'=>$receipt_id,'tab'=>2,'returnUrl'=>$returnUrl])]];
                    }else{
                        $tab = [3=>['name'=>'单据明细(编辑)','url'=>Url::to(['receipt-goods/edit-all','receipt_id'=>$receipt_id,'tab'=>3,'returnUrl'=>$returnUrl])]];
                    }
                    break;
                }
            case PurchaseTypeEnum::MATERIAL_STONE:
                {
                    $tabList = [
                        1=>['name'=>'基础信息','url'=>Url::to(['stone-receipt/view','id'=>$receipt_id,'tab'=>1,'returnUrl'=>$returnUrl])],
                        4=>['name'=>'日志信息','url'=>Url::to(['receipt-log/index','receipt_id'=>$receipt_id,'tab'=>4,'returnUrl'=>$returnUrl])]
                    ];
                    if($tag!=3){
                        $tab = [2=>['name'=>'单据明细','url'=>Url::to(['stone-receipt-goods/index','receipt_id'=>$receipt_id,'tab'=>2,'returnUrl'=>$returnUrl])]];
                    }else{
                        $tab = [3=>['name'=>'单据明细(编辑)','url'=>Url::to(['stone-receipt-goods/edit-all','receipt_id'=>$receipt_id,'tab'=>3,'returnUrl'=>$returnUrl])]];
                    }
                    break;
                }
            case PurchaseTypeEnum::MATERIAL_GOLD:
                {
                    $tabList = [
                        1=>['name'=>'基础信息','url'=>Url::to(['gold-receipt/view','id'=>$receipt_id,'tab'=>1,'returnUrl'=>$returnUrl])],
                        4=>['name'=>'日志信息','url'=>Url::to(['receipt-log/index','receipt_id'=>$receipt_id,'tab'=>4,'returnUrl'=>$returnUrl])]
                    ];
                    if($tag!=3){
                        $tab = [2=>['name'=>'单据明细','url'=>Url::to(['gold-receipt-goods/index','receipt_id'=>$receipt_id,'tab'=>2,'returnUrl'=>$returnUrl])]];
                    }else{
                        $tab = [3=>['name'=>'单据明细(编辑)','url'=>Url::to(['gold-receipt-goods/edit-all','receipt_id'=>$receipt_id,'tab'=>3,'returnUrl'=>$returnUrl])]];
                    }
                    break;
                }
        }
        $tabList = ArrayHelper::merge($tabList, $tab);
        ksort($tabList);
        return $tabList;
    }

    /**
     * 采购收货单汇总
     * @param integer $receipt_id
     * @param integer $purchase_type
     * @throws \Exception
     */
    public function purchaseReceiptSummary($receipt_id, $purchase_type)
    {
        $select = [
            'sum(1) as receipt_num',
            'sum(goods_weight) as total_weight',
            'sum(cost_price) as total_cost',
        ];
        if($purchase_type == PurchaseTypeEnum::MATERIAL_GOLD){
            $model = new PurchaseGoldReceiptGoods();
        }elseif($purchase_type == PurchaseTypeEnum::MATERIAL_STONE){
            $model = new PurchaseStoneReceiptGoods();
            $select[] = "sum(stone_num) as total_stone_num";
        }else{
            $model = new PurchaseReceiptGoods();
        }
        $sum = $model::find()
            ->select($select)
            ->where(['receipt_id'=>$receipt_id, 'status'=>StatusEnum::ENABLED])
            ->asArray()->one();
        if($sum) {
            $data = [
                'receipt_num'=>$sum['receipt_num']/1,
                'total_weight'=>$sum['total_weight']/1,
                'total_cost'=>$sum['total_cost']/1,
            ];
            if($purchase_type == PurchaseTypeEnum::MATERIAL_STONE){
                $data['total_stone_num'] = $sum['total_stone_num']/1;
            }
            $result = PurchaseReceipt::updateAll($data, ['id'=>$receipt_id]);
        }
        return $result??"";
    }

    /**
     * 创建采购收货单
     * @param array $bill
     * @param array $detail
     * @throws \Exception
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
                if($count){
                    throw new \Exception("【".$good['goods_name']."】采购单已收货，不能重复收货");
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
     * 布产单号批量查询可出货商品
     * @param object $form
     * @throws \Exception
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
            $select = [
                'rg.produce_sn'=>$produce_sn,
                'r.receipt_status'=>[
                    ReceiptStatusEnum::SAVE,
                    ReceiptStatusEnum::PENDING,
                    ReceiptStatusEnum::CONFIRM
                ],
                'r.status'=>StatusEnum::ENABLED,
                'rg.status'=>StatusEnum::ENABLED
            ];
            $receipt_num = PurchaseReceiptGoods::find()->alias('rg')
                ->leftJoin(PurchaseReceipt::tableName().' r','r.id=rg.receipt_id')
                ->where($select)
                ->select(['r.id'])
                ->count()??"0";
            $the_num = bcsub($shippent_num, $receipt_num);
            if ($the_num<=0) {
                throw new \Exception($message."没有可出货数量");
            }
            $purchase = Purchase::findOne(['id' => $produce->from_order_id]);
            if(!$purchase){
                throw new \Exception($message."对应的采购单号不对");
            }
            $purchaseGoods = PurchaseGoods::findOne(['produce_id' => $produce->id]);
            if(!$purchaseGoods){
                throw new \Exception($message."未绑定采购单明细");
            }
            //属性
            $produce_attr = ProduceAttribute::find()->where(['produce_id' => $produce->id])->asArray()->all();
            $attr_arr = [];
            foreach ($produce_attr as $attr) {
                $attr_name = Yii::$app->styleService->attribute->getAttrNameByAttrId($attr['attr_id']);
                $attr_arr[$attr['attr_id']]['attr_name'] = $attr_name;
                $attr_arr[$attr['attr_id']]['attr_value'] = $attr['attr_value'];
                $attr_arr[$attr['attr_id']]['attr_value_id'] = $attr['attr_value_id'];
            }
            $gold_weight = $attr_arr[AttrIdEnum::JINZHONG]['attr_value']??0;
            //主石
            $main_stone = $attr_arr[AttrIdEnum::MAIN_STONE_TYPE]['attr_value_id'] ?? '';
            $main_stone_num = $attr_arr[AttrIdEnum::MAIN_STONE_NUM]['attr_value'] ?? '0';
            $main_stone_weight = $attr_arr[AttrIdEnum::MAIN_STONE_NUM]['attr_value'] ?? '0';
            $main_stone_color = $attr_arr[AttrIdEnum::MAIN_STONE_COLOR]['attr_value_id'] ?? '';
            $main_stone_clarity = $attr_arr[AttrIdEnum::MAIN_STONE_CLARITY]['attr_value_id'] ?? '';
            //副石1
            $second_stone1 = $attr_arr[AttrIdEnum::SIDE_STONE1_TYPE]['attr_value_id'] ?? '';
            $second_stone_num1 = $attr_arr[AttrIdEnum::SIDE_STONE1_NUM]['attr_value'] ?? '0';
            $second_stone_weight1 = $attr_arr[AttrIdEnum::SIDE_STONE1_WEIGHT]['attr_value'] ?? '0';
            //副石2
            $second_stone2 = $attr_arr[AttrIdEnum::SIDE_STONE2_TYPE]['attr_value_id'] ?? '';
            $second_stone_num2 = $attr_arr[AttrIdEnum::SIDE_STONE2_NUM]['attr_value'] ?? '0';
            $second_stone_weight2 = $attr_arr[AttrIdEnum::SIDE_STONE2_WEIGHT]['attr_value'] ?? '0';
            if($produce->peiliao_status == PeiliaoStatusEnum::HAS_LINGLIAO){
                //金料信息
                $gold_weight = ProduceGold::find()->alias('pg')
                    ->leftJoin(ProduceGoldGoods::tableName().' gg', 'pg.id=gg.id')
                    ->where(['pg.produce_id'=>$produce->id])
                    ->sum('gg.gold_weight');
                $gold_weight = $gold_weight??0;
                //石料信息
                $select = [
                    'ps.stone_position',
                    'ws.stone_type',
                    'sum(sg.stone_num) as stone_num',
                    'sum(sg.stone_weight) as stone_weight',
                    'ws.stone_color',
                    'ws.stone_clarity',
                    'ws.stone_cut',
                    'ws.stone_symmetry',
                    'ws.stone_polish',
                    'ws.stone_fluorescence'
                ];
                $stone = ProduceStone::find()->alias('ps')
                    ->leftJoin(ProduceStoneGoods::tableName().' sg','ps.id=sg.id')
                    ->leftJoin(WarehouseStone::tableName().' ws', 'sg.stone_sn=ws.stone_sn')
                    ->select($select)
                    ->where(['ps.produce_id'=>$produce->id])
                    ->groupBy(['ps.stone_position'])
                    ->asArray()
                    ->all();
                if($stone){
                    $stone = ArrayHelper::index($stone, 'stone_position');
                    //主石
                    if(isset($stone[1])){
                        $stone1 = $stone[1];
                        //$main_stone = $stone1['stone_type'] ?? $main_stone;
                        $main_stone_num = $stone1['stone_num'] ?? $main_stone_num;
                        $main_stone_weight = $stone1['stone_weight'] ?? $main_stone_weight;
                        $main_stone_color = $stone1['stone_color'] ?? $main_stone_color;
                        $main_stone_clarity = $stone1['stone_clarity'] ?? $main_stone_clarity;
                    }
                    //副石1
                    if(isset($stone[2])){
                        $stone2 = $stone[2];
                        //$second_stone1 = $stone2['stone_type'] ?? $second_stone1;
                        $second_stone_num1 = $stone2['stone_num'] ?? $second_stone_num1;
                        $second_stone_weight1 = $stone2['stone_weight'] ?? $second_stone_weight1;
                    }
                    //副石2
                    if(isset($stone[3])){
                        $stone3 = $stone[3];
                        //$second_stone2 = $stone3['stone_type'] ?? $second_stone2;
                        $second_stone_num2 = $stone3['stone_num'] ?? $second_stone_num2;
                        $second_stone_weight2 = $stone3['stone_weight'] ?? $second_stone_weight2;
                    }
                }
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
                    'finger' => $attr_arr[AttrIdEnum::FINGER]['attr_value_id'] ?? '',
                    'xiangkou' => $attr_arr[AttrIdEnum::XIANGKOU]['attr_value_id'] ?? '',
                    'material' => $attr_arr[AttrIdEnum::MATERIAL]['attr_value_id'] ?? '',
                    'jintuo_type' => $produce->jintuo_type,
                    'style_sex' => $produce->style_sex,
                    'style_channel_id' => $purchaseGoods->style_channel_id,
                    'factory_mo' => "",
                    'gold_weight' => $gold_weight,
                    'gold_price' => $purchaseGoods->gold_price,
                    'gold_loss' => $purchaseGoods->gold_loss,
                    'gold_amount' => $purchaseGoods->gold_amount,
                    'gross_weight' => $purchaseGoods->single_stone_weight,
                    'suttle_weight' => $purchaseGoods->gold_price,
                    'cost_price' => $purchaseGoods->factory_cost_price,
                    'cert_id' => $attr_arr[AttrIdEnum::DIA_CERT_NO]['attr_value'] ?? '',
                    'product_size' => $purchaseGoods->product_size,
                    'put_in_type' =>$purchase->put_in_type,
                    //主石
                    'main_stone' => $main_stone,
                    'main_stone_num' => $main_stone_num,
                    'main_stone_weight' => $main_stone_weight,
                    'main_stone_color' => $main_stone_color,
                    'main_stone_clarity' => $main_stone_clarity,
                    'main_stone_price' => 0,
                    //副石1
                    'second_stone1' => $second_stone1,
                    'second_stone_num1' => $second_stone_num1,
                    'second_stone_weight1' => $second_stone_weight1,
                    'second_stone_price1' => 0,
                    //副石2
                    'second_stone2' => $second_stone2,
                    'second_stone_num2' => $second_stone_num2,
                    'second_stone_weight2' => $second_stone_weight2,
                    'second_stone_price2' => 0,
                    //工费
                    'gong_fee' => $purchaseGoods->gong_fee,
                    'xianqian_fee' => $purchaseGoods->xiangqian_fee,
                    'biaomiangongyi' => $attr_arr[AttrIdEnum::FACEWORK]['attr_value_id'] ?? '',
                    'biaomiangongyi_fee' => $purchaseGoods->biaomiangongyi_fee,
                    'fense_fee' => $purchaseGoods->fense_fee,
                    'bukou_fee' => $purchaseGoods->bukou_fee,
                    'cert_fee' => $purchaseGoods->cert_fee,
                    'extra_stone_fee' => 0,
                ];
                $receipt_goods[] = ArrayHelper::merge($goodsM->getAttributes(),$goods);
            }
        }
        return $receipt_goods??[];
    }

    /**
     * 添加采购收货单商品明细
     * @param PurchaseReceiptForm $form
     * @throws \Exception
     */
    public function addReceiptGoods($form)
    {
        $goods = $form->goods;
        if(!empty($goods)){
            $value = [];
            $key = array_keys($goods[0]);
            $max = PurchaseReceiptGoods::find()->where(['receipt_id' => $form->id])->select(['xuhao'])->orderBy(['xuhao' => SORT_DESC])->one();
            $xuhao = $max->xuhao??1;
            foreach ($goods as $good) {
                $good['receipt_id'] = $form->id;
                $good['xuhao'] = $xuhao++;
                $good['goods_status'] =ReceiptGoodsStatusEnum::SAVE;
                $good['status'] = StatusEnum::ENABLED;
                $good['created_at'] = time();
                $value[] = array_values($good);
                if(count($value) >= 10){
                    $res= \Yii::$app->db->createCommand()->batchInsert(PurchaseReceiptGoods::tableName(), $key, $value)->execute();
                    if(false === $res){
                        throw new \yii\base\Exception("保存失败");
                    }
                    $value = [];
                }
            }
            if(!empty($value)){
                $res= \Yii::$app->db->createCommand()->batchInsert(PurchaseReceiptGoods::tableName(), $key, $value)->execute();
                if(false === $res){
                    throw new \yii\base\Exception("保存失败");
                }
            }
            //更新采购收货单汇总：总金额和总数量
            $this->purchaseReceiptSummary($form->id, PurchaseTypeEnum::GOODS);
        }
    }

    /**
     * 同步采购收货单生成L单
     * @param object $form
     * @param array $detail_ids
     * @throws \Exception
     */
    public function syncReceiptToBillL($form, $detail_ids = null)
    {
        if($form->audit_status != AuditStatusEnum::PASS){
            throw new \Exception('采购收货单没有审核');
        }
        if($form->receipt_num <= 0 ){
            throw new \Exception('采购收货单没有明细');
        }
        if(!$form->to_warehouse_id){
            throw new \Exception('入库仓库不能为空');
        }
        if(!$detail_ids){
            $detail_ids = $form->getIds();
        }
        $query = PurchaseReceiptGoods::find()->where(['receipt_id'=>$form->id, 'goods_status' => ReceiptGoodsStatusEnum::IQC_PASS]);
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
                'gold_weight' => $model->gold_weight?:0,
                'gold_loss' => $model->gold_loss?:0,
                'gross_weight' => (string)$model->gross_weight,
                'finger' => (string)$model->finger?:'0',
                'produce_sn' => $model->produce_sn,
                'cert_id' => $model->cert_id,
                'goods_num' => $model->goods_num,
                'material' => (string)$model->material,
                'material_type' => '',
                'material_color' => '',
                'diamond_carat' => $model->main_stone_weight,
                'diamond_clarity' => (string)$model->main_stone_clarity,
                'jintuo_type' => $model->jintuo_type,
                'market_price' => $model->market_price,
                'xiangkou' => (string)$model->xiangkou?:'0',
                'parts_gold_weight' => $model->parts_weight,
                'parts_num' => 1,
                'main_stone_type' => $model->main_stone,
                'main_stone_num' => $model->main_stone_num,
                'second_stone_type1' => (string)$model->second_stone1,
                'second_stone_num1' => $model->second_stone_num1,
                'second_stone_price1' => $model->second_stone_price1,
                'second_stone_weight1' => $model->second_stone_weight1,
                'second_stone_type2' => (string)$model->second_stone2,
                'second_stone_num2' => $model->second_stone_num2,
                'second_stone_weight2' => $model->second_stone_weight2,
                'second_stone_price2' => $model->second_stone_price2,
                'creator_id' => \Yii::$app->user->identity->getId(),
                'created_at' => time(),
            ];
            $total_cost = bcadd($total_cost, $model->cost_price, 2);
            $market_price = bcadd($market_price, $model->market_price, 2);
            $sale_price = bcadd($sale_price, $model->sale_price, 2);
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
        Yii::$app->warehouseService->billL->createBillL($bill, $goods);

        //批量更新采购收货单货品状态
        $data = [
            'goods_status'=>ReceiptGoodsStatusEnum::WAREHOUSE_ING,
            'put_in_type'=>$form->put_in_type,
            'to_warehouse_id'=>$form->to_warehouse_id
        ];
        $res = PurchaseReceiptGoods::updateAll($data, ['id'=>$ids]);
        if(false === $res){
            throw new \Exception('更新采购收货单货品状态失败');
        }
    }

    /**
     *  IQC批量质检验证
     * @param object $form
     * @param integer $purchase_type
     * @throws \Exception
     */
    public function iqcValidate($form, $purchase_type){
        $ids = $form->getIds();
        if(is_array($ids)){
            if($purchase_type == PurchaseTypeEnum::MATERIAL_GOLD){
                $model = new PurchaseGoldReceiptGoods();
            }elseif($purchase_type == PurchaseTypeEnum::MATERIAL_STONE){
                $model = new PurchaseStoneReceiptGoods();
            }else{
                $model = new PurchaseReceiptGoods();
            }
            foreach ($ids as $id) {
                $goods = $model::findOne(['id'=>$id]);
                if($goods->goods_status != ReceiptGoodsStatusEnum::IQC_ING){
                    throw new Exception("流水号【{$id}】不是待质检状态，不能质检");
                }
            }
        }
    }

    /**
     *  IQC质检
     * @param object $form
     * @param integer $purchase_type
     * @throws \Exception
     */
    public function qcIqc($form, $purchase_type)
    {
        if($form->goods_status === ""){
            throw new Exception("请选择是否质检通过");
        }
        $this->iqcValidate($form, $purchase_type);
        $ids = $form->getIds();
        if($form->goods_status == QcTypeEnum::PASS){
            $goods = ['goods_status' =>ReceiptGoodsStatusEnum::IQC_PASS];
        }else{
            $goods = ['goods_status' =>ReceiptGoodsStatusEnum::IQC_NO_PASS, 'iqc_reason' => $form->iqc_reason, 'iqc_remark' => $form->iqc_remark];
        }
        if($purchase_type == PurchaseTypeEnum::MATERIAL_GOLD){
            $model = new PurchaseGoldReceiptGoods();
        }elseif($purchase_type == PurchaseTypeEnum::MATERIAL_STONE){
            $model = new PurchaseStoneReceiptGoods();
        }else{
            $model = new PurchaseReceiptGoods();
        }
        $res = $model::updateAll($goods, ['id'=>$ids]);
        if(false === $res) {
            throw new Exception("更新货品状态失败");
        }
    }

    /**
     *  批量申请入库验证
     * @param object $form
     * @param integer $purchase_type
     * @throws \Exception
     */
    public function warehouseValidate($form, $purchase_type){
        $ids = $form->getIds();
        if(is_array($ids)){
            if($purchase_type == PurchaseTypeEnum::MATERIAL_GOLD){
                $model = new PurchaseGoldReceiptGoods();
            }elseif($purchase_type == PurchaseTypeEnum::MATERIAL_STONE){
                $model = new PurchaseStoneReceiptGoods();
            }else{
                $model = new PurchaseReceiptGoods();
            }
            foreach ($ids as $id) {
                $goods = $model::find()->where(['id'=>$id])->select(['receipt_id', 'goods_status', 'xuhao'])->one();
                if($goods->goods_status != ReceiptGoodsStatusEnum::IQC_PASS){
                    throw new Exception("序号【{$goods->xuhao}】不是IQC质检通过状态，不能入库");
                }
            }
        }
        return $goods->receipt_id??"";
    }

    /**
     * {@inheritdoc}
     */
    public function checkDistinct($model, $col, $ids){
        $num = $model::find()->alias('rg')
            ->leftJoin(['r' => PurchaseReceipt::tableName()], 'r.id = rg.receipt_id')
            ->where(['rg.id' => $ids])
            ->select($col)
            ->distinct($col)
            ->count(1);
        return $num==1?:false;
    }

    /**
     *  批量生成不良返厂单
     * @param object $form
     * @param integer $purchase_type
     * @throws \Exception
     */
    public function batchDefective($form, $purchase_type)
    {
        if($purchase_type == PurchaseTypeEnum::MATERIAL_GOLD){
            $model = new PurchaseGoldReceiptGoods();
        }elseif($purchase_type == PurchaseTypeEnum::MATERIAL_STONE){
            $model = new PurchaseStoneReceiptGoods();
        }else{
            $model = new PurchaseReceiptGoods();
        }
        $ids = $form->ids;
        if(!count($ids)){
            throw new Exception("至少选择一个货品");
        }
        if(!$this->checkDistinct($model, 'receipt_no', $ids)){
            throw new Exception("不是同一个出货单号不允许制单");
        }
        if(!$this->checkDistinct($model, 'supplier_id', $ids)){
            throw new Exception("不是同一个供应商不允许制单");
        }
        $total_cost = 0;
        $receipt = $defect = $detail = [];
        foreach($ids as $id)
        {
            $goods = $model::find()->where(['id'=>$id])->one();
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
            if($purchase_type == PurchaseTypeEnum::MATERIAL_GOLD){
                $detail[] = [
                    'xuhao' => $goods->xuhao,
                    'receipt_detail_id' => $goods->id,
                    'goods_name' => $goods->goods_name,
                    'goods_num' => $goods->goods_num,
                    'material_type' => $goods->material_type,
                    'goods_weight' => $goods->goods_weight,
                    'cost_price' => $goods->cost_price,
                    'goods_price' => $goods->gold_price,
                    'iqc_reason' => $goods->iqc_reason,
                    'iqc_remark' => $goods->iqc_remark,
                    'created_at' => time(),
                ];
            }elseif($purchase_type == PurchaseTypeEnum::MATERIAL_STONE){
                $detail[] = [
                    'xuhao' => $goods->xuhao,
                    'receipt_detail_id' => $goods->id,
                    'goods_name' => $goods->goods_name,
                    'goods_num' => $goods->goods_num,
                    'material_type' => (String) $goods->material_type,
                    'goods_weight' => $goods->goods_weight,
                    'goods_color' => $goods->goods_color,
                    'goods_clarity' => $goods->goods_clarity,
                    'goods_norms' => $goods->goods_norms,
                    'cost_price' => $goods->cost_price,
                    'goods_price' => $goods->stone_price,
                    'iqc_reason' => $goods->iqc_reason,
                    'iqc_remark' => $goods->iqc_remark,
                    'created_at' => time(),
                ];
            }else{
                $detail[] = [
                    'xuhao' => $goods->xuhao,
                    'receipt_detail_id' => $goods->id,
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
            }
            $total_cost = bcadd($total_cost, $goods->cost_price, 2);
        }
        $bill = [
            'supplier_id' => $receipt->supplier_id??'',
            'receipt_no' => $receipt->receipt_no??'',
            'purchase_sn' => $receipt->purchase_sn??'',
            'purchase_type' => $purchase_type,
            'defective_num' => count($detail),
            'total_cost' => $total_cost,
            'audit_status' => AuditStatusEnum::PENDING,
            'defective_status' => DefectiveStatusEnum::PENDING,
            'creator_id' => \Yii::$app->user->identity->getId(),
            'created_at' => time(),
        ];
        \Yii::$app->purchaseService->defective->createDefactiveBill($bill, $detail);
        $res = $model::updateAll(['goods_status' =>ReceiptGoodsStatusEnum::FACTORY_ING], ['id'=>$ids]);
        if(false === $res) {
            throw new Exception("更新货品状态失败");
        }
    }

    /**
     * 金料收货单同步创建入库单
     * @param object $form
     * @param array $detail_ids
     * @throws \Exception
     */
    public function syncReceiptToGoldL($form, $detail_ids = null)
    {
        if($form->audit_status != AuditStatusEnum::PASS){
            throw new \Exception('采购收货单没有审核');
        }
        if($form->receipt_num <= 0 ){
            throw new \Exception('采购收货单没有明细');
        }
        if(!$detail_ids){
            $detail_ids = $form->getIds();
        }
        $query = PurchaseGoldReceiptGoods::find()->where(['receipt_id'=>$form->id, 'goods_status' => ReceiptGoodsStatusEnum::IQC_PASS]);
        if(!empty($detail_ids)) {
            $query->andWhere(['id'=>$detail_ids]);
        }
        $models = $query->all();
        if(!$models){
            throw new \Exception('采购收货单没有待入库的货品');
        }
        $form->to_warehouse_id = WarehouseIdEnum::GOLD;
        $goods = $ids = [];
        $total_weight = $total_cost = $sale_price = 0;
        foreach ($models as $model){
            $ids[] = $model->id;
            $goods[] = [
                'gold_name' => $model->goods_name,
                'gold_type' => $model->material_type,
                'style_sn' => $model->goods_sn,
                'gold_num' => $model->goods_num,
                'gold_weight' => $model->goods_weight,
                'cost_price' => $model->cost_price,
                'gold_price' => $model->gold_price,
                'source_detail_id' =>$model->id,
                'status' => StatusEnum::ENABLED,
                'created_at' => time(),
            ];
            $total_cost = bcadd($total_cost, $model->cost_price, 2);
            $total_weight = bcadd($total_weight, bcmul($model->goods_num, $model->goods_weight, 2), 2);
        }
        $bill = [
            'bill_type' =>  GoldBillTypeEnum::GOLD_L,
            'bill_status' => GoldBillStatusEnum::SAVE,
            'audit_status' => AuditStatusEnum::SAVE,
            'supplier_id' => $form->supplier_id,
            'put_in_type' => $form->put_in_type,
            'to_warehouse_id' => $form->to_warehouse_id,
            'adjust_type' => AdjustTypeEnum::ADD,
            'total_num' => count($goods),
            'total_weight' => $total_weight,
            'total_cost' => $total_cost,
            'delivery_no' => $form->receipt_no,
            'remark' => $form->remark,
            'status' => StatusEnum::ENABLED,
            'creator_id' => \Yii::$app->user->identity->getId(),
            'created_at' => time(),
        ];
        Yii::$app->warehouseService->goldL->createGoldL($bill, $goods);
        //批量更新采购收货单货品状态
        $data = [
            'goods_status'=>ReceiptGoodsStatusEnum::WAREHOUSE_ING,
            'put_in_type'=>$form->put_in_type,
            'to_warehouse_id'=>$form->to_warehouse_id
        ];
        $res = PurchaseGoldReceiptGoods::updateAll($data, ['id'=>$ids]);
        if(false === $res){
            throw new \Exception('更新采购收货单货品状态失败');
        }
    }

    /**
     * 石料收货单同步创建入库单
     * @param object $form
     * @param array $detail_ids
     * @throws \Exception
     */
    public function syncReceiptToStoneBillMs($form, $detail_ids = null)
    {
        if($form->audit_status != AuditStatusEnum::PASS){
            throw new \Exception('采购收货单没有审核');
        }
        if($form->receipt_num <= 0 ){
            throw new \Exception('采购收货单没有明细');
        }
        if(!$detail_ids){
            $detail_ids = $form->getIds();
        }
        $query = PurchaseStoneReceiptGoods::find()
            ->where(['receipt_id'=>$form->id, 'goods_status' => ReceiptGoodsStatusEnum::IQC_PASS]);
        if(!empty($detail_ids)) {
            $query->andWhere(['id'=>$detail_ids]);
        }
        $models = $query->all();
        if(!$models){
            throw new \Exception('采购收货单没有待入库的货品');
        }
        $form->to_warehouse_id = WarehouseIdEnum::STONE;
        $goods = $ids = [];
        $total_stone_num = $total_weight= $market_price= $sale_price = 0;
        foreach ($models as $model){
            $ids[] = $model->id;
            $goods[] = [
                'stone_name' => $model->goods_name,
                'stone_type' => $model->material_type,
                'style_sn' => $model->goods_sn,
                'cert_type' => $model->cert_type,
                'cert_id' => $model->cert_id,
                'carat' => $model->goods_weight,
                'color' => $model->goods_color,
                'clarity' => $model->goods_clarity,
                'cut' => $model->goods_cut,
                'polish' => $model->goods_polish,
                'fluorescence' =>$model->goods_fluorescence,
                'symmetry' =>$model->goods_symmetry,
                'stone_num' => $model->stone_num,
                'source_detail_id' => $model->id,
                'stone_price' => $model->stone_price,
                'cost_price' => $model->cost_price,
                'stone_weight' => $model->goods_weight,
                'stone_norms' => $model->goods_norms,
                'sale_price' => $model->stone_price,
                'status' => StatusEnum::ENABLED,
                'created_at' => time()
            ];
            $total_stone_num = bcadd($total_stone_num, $model->stone_num);
            $total_weight = bcadd($total_weight, $model->goods_weight, 2);
        }
        $bill = [
            'bill_type' =>  StoneBillTypeEnum::STONE_MS,
            'bill_status' => BillStatusEnum::SAVE,
            'supplier_id' => $form->supplier_id,
            'put_in_type' => $form->put_in_type,
            'to_warehouse_id' => $form->to_warehouse_id,
            'adjust_type' => AdjustTypeEnum::ADD,
            'total_num' => count($goods),
            'total_stone_num' => $total_stone_num,
            'total_weight' => $total_weight,
            'total_cost' => $form->total_cost,
            'pay_amount' => $form->total_cost,
            'delivery_no' => $form->receipt_no,
            'remark' => $form->remark,
            'status' => StatusEnum::ENABLED,
            'creator_id' => \Yii::$app->user->identity->getId(),
            'created_at' => time(),
        ];
        \Yii::$app->warehouseService->stoneMs->createBillMs($bill, $goods);
        //批量更新采购收货单货品状态
        $data = [
            'goods_status'=>ReceiptGoodsStatusEnum::WAREHOUSE_ING,
            'put_in_type'=>$form->put_in_type,
        ];
        $res = PurchaseStoneReceiptGoods::updateAll($data, ['id'=>$ids]);
        if(false === $res){
            throw new \Exception('更新采购收货单货品状态失败');
        }
    }
}
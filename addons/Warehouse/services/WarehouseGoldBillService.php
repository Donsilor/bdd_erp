<?php

namespace addons\Warehouse\services;

use addons\Warehouse\common\enums\FinAuditStatusEnum;
use addons\Warehouse\common\enums\GoldBillStatusEnum;
use addons\Warehouse\common\enums\GoodsStatusEnum;
use addons\Warehouse\common\enums\PandianAdjustEnum;
use addons\Warehouse\common\enums\PandianStatusEnum;
use addons\Warehouse\common\forms\WarehouseBillWForm;
use addons\Warehouse\common\forms\WarehouseGoldBillWForm;
use addons\Warehouse\common\models\WarehouseBill;
use addons\Warehouse\common\models\WarehouseBillGoods;
use addons\Warehouse\common\models\WarehouseBillGoodsW;
use addons\Warehouse\common\models\WarehouseBillW;
use addons\Warehouse\common\models\WarehouseGold;
use addons\Warehouse\common\models\WarehouseGoldBillGoodsW;
use addons\Warehouse\common\models\WarehouseGoldBillW;
use addons\Warehouse\common\models\WarehouseGoods;
use addons\Warehouse\common\models\WarehouseMaterialBillW;
use common\enums\ConfirmEnum;
use Yii;
use common\components\Service;
use common\helpers\SnHelper;
use addons\Purchase\common\models\PurchaseGoldReceiptGoods;
use addons\Warehouse\common\enums\GoldBillTypeEnum;
use addons\Warehouse\common\models\WarehouseGoldBill;
use addons\Warehouse\common\models\WarehouseGoldBillGoods;
use addons\Warehouse\common\models\WarehouseStone;
use addons\Warehouse\common\models\WarehouseStoneBill;
use addons\Warehouse\common\models\WarehouseStoneBillGoods;
use addons\Purchase\common\enums\ReceiptGoodsStatusEnum;
use addons\Warehouse\common\enums\BillStatusEnum;
use common\enums\AuditStatusEnum;
use common\enums\StatusEnum;
use common\helpers\Url;
use common\helpers\ArrayHelper;

/**
 * 金料单据
 * @package services\common
 * @author jianyan74 <751393839@qq.com>
 */
class WarehouseGoldBillService extends Service
{

    /**
     * 金料单据明细 tab
     * @param int $bill_id 单据ID
     * @param $returnUrl URL
     * @return array
     */
    public function menuTabList($bill_id, $bill_type, $returnUrl = null, $tag = null)
    {
        $tabList = [];
        switch ($bill_type){

            case GoldBillTypeEnum::GOLD_L:
                {
                    if(!$tag){
                        $tabList = [
                            1=>['name'=>'单据详情','url'=>Url::to(['gold-bill-l/view','id'=>$bill_id,'tab'=>1,'returnUrl'=>$returnUrl])],
                            2=>['name'=>'单据明细','url'=>Url::to(['gold-bill-l-goods/index','bill_id'=>$bill_id,'tab'=>2,'returnUrl'=>$returnUrl])],
                            4=>['name'=>'日志列表','url'=>Url::to(['gold-bill-log/index','bill_id'=>$bill_id,'tab'=>4,'returnUrl'=>$returnUrl])]
                        ];
                    }else{
                        $tabList = [
                            1=>['name'=>'单据详情','url'=>Url::to(['gold-bill-l/view','id'=>$bill_id,'tab'=>1,'returnUrl'=>$returnUrl])],
                            3=>['name'=>'单据明细(编辑)','url'=>Url::to(['gold-bill-l-goods/edit-all','bill_id'=>$bill_id,'tab'=>3,'returnUrl'=>$returnUrl])],
                            4=>['name'=>'日志列表','url'=>Url::to(['gold-bill-log/index','bill_id'=>$bill_id,'tab'=>4,'returnUrl'=>$returnUrl])]
                        ];
                    }
                    break;
                }
            case GoldBillTypeEnum::GOLD_C:
                {
                    if(!$tag){
                        $tabList = [
                            1=>['name'=>'单据详情','url'=>Url::to(['gold-bill-c/view','id'=>$bill_id,'tab'=>1,'returnUrl'=>$returnUrl])],
                            2=>['name'=>'单据明细','url'=>Url::to(['gold-bill-c-goods/index','bill_id'=>$bill_id,'tab'=>2,'returnUrl'=>$returnUrl])],
                            4=>['name'=>'日志列表','url'=>Url::to(['gold-bill-log/index','bill_id'=>$bill_id,'tab'=>4,'returnUrl'=>$returnUrl])]
                        ];
                    }else{
                        $tabList = [
                            1=>['name'=>'单据详情','url'=>Url::to(['gold-bill-c/view','id'=>$bill_id,'tab'=>1,'returnUrl'=>$returnUrl])],
                            3=>['name'=>'单据明细(编辑)','url'=>Url::to(['gold-bill-c-goods/edit-all','bill_id'=>$bill_id,'tab'=>3,'returnUrl'=>$returnUrl])],
                            4=>['name'=>'日志列表','url'=>Url::to(['gold-bill-log/index','bill_id'=>$bill_id,'tab'=>4,'returnUrl'=>$returnUrl])]
                        ];
                    }
                    break;
                }
            case GoldBillTypeEnum::GOLD_W:
                {
                    if(!$tag){
                        $tabList = [
                            1=>['name'=>'单据详情','url'=>Url::to(['gold-bill-w/view','id'=>$bill_id,'tab'=>1,'returnUrl'=>$returnUrl])],
                            2=>['name'=>'单据明细','url'=>Url::to(['gold-bill-w-goods/index','bill_id'=>$bill_id,'tab'=>2,'returnUrl'=>$returnUrl])],
                            4=>['name'=>'日志列表','url'=>Url::to(['gold-bill-log/index','bill_id'=>$bill_id,'tab'=>4,'returnUrl'=>$returnUrl])]
                        ];
                    }else{
                        $tabList = [
                            1=>['name'=>'单据详情','url'=>Url::to(['gold-bill-w/view','id'=>$bill_id,'tab'=>1,'returnUrl'=>$returnUrl])],
                            3=>['name'=>'单据明细(编辑)','url'=>Url::to(['gold-bill-w-goods/edit-all','bill_id'=>$bill_id,'tab'=>3,'returnUrl'=>$returnUrl])],
                            4=>['name'=>'日志列表','url'=>Url::to(['gold-bill-log/index','bill_id'=>$bill_id,'tab'=>4,'returnUrl'=>$returnUrl])]
                        ];
                    }
                    break;
                }
        }
        return $tabList;
    }

    /**
     * 创建金料收货单
     * @param array $bill
     * @param array $details
     */
    public function createGoldL($bill, $details){
        $billM = new WarehouseGoldBill();
        $billM->attributes = $bill;
        $billM->bill_no = SnHelper::createBillSn($billM->bill_type);
        if(false === $billM->save()){
            throw new \Exception($this->getError($billM));
        }
        $bill_id = $billM->attributes['id'];
        $goodsM = new WarehouseGoldBillGoods();
        foreach ($details as &$good){
            $good['bill_id'] = $bill_id;
            $good['bill_no'] = $billM->bill_no;
            $good['bill_type'] = $billM->bill_type;
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
        $res = Yii::$app->db->createCommand()->batchInsert(WarehouseGoldBillGoods::tableName(), $key, $value)->execute();
        if(false === $res){
            throw new \Exception("创建收货单明细失败");
        }
    }

    /**
     * 金料收货单-审核
     * @param $form
     */
    public function auditGoldL($form)
    {
        if(false === $form->validate()) {
            throw new \Exception($this->getError($form));
        }
        if($form->audit_status == AuditStatusEnum::PASS){
            $form->bill_status = BillStatusEnum::CONFIRM;
            $billGoods = WarehouseGoldBillGoods::find()->select(['gold_name', 'source_detail_id'])->where(['bill_id' => $form->id])->asArray()->all();
            if(empty($billGoods)){
                throw new \Exception("单据明细不能为空");
            }
            //金料入库
            \Yii::$app->warehouseService->gold->editGold($form);
            if($form->audit_status == AuditStatusEnum::PASS){
                //同步金料采购收货单货品状态
                $ids = ArrayHelper::getColumn($billGoods, 'source_detail_id');
                $res = PurchaseGoldReceiptGoods::updateAll(['goods_status'=>ReceiptGoodsStatusEnum::WAREHOUSE], ['id'=>$ids]);
                if(false === $res) {
                    throw new \Exception("同步金料采购收货单货品状态失败");
                }
            }
        }else{
            $form->bill_status = BillStatusEnum::SAVE;
        }
        if(false === $form->save()) {
            throw new \Exception($this->getError($form));
        }
    }

    /**
     * 创建盘点单
     * @param object $form
     */
    public function createBillW($form){
        if(false === $form->validate()) {
            throw new \Exception($this->getError($form));
        }
        $bill = new WarehouseGoldBill();
        $bill->attributes = $form->toArray();
        $bill->bill_status = BillStatusEnum::SAVE;
        if(false === $bill->save() ) {
            throw new \Exception($this->getError($bill));
        }
        //批量创建单据明细
        $goods_list = WarehouseGold::find()->where(['warehouse_id'=>$bill->to_warehouse_id, 'gold_type' => $form->gold_type])->asArray()->all();
        $gold_weight = 0;
        $bill_goods_values = [];
        if(!empty($goods_list)) {
            $bill_goods= [];
            foreach ($goods_list as $goods) {
                $bill_goods = [
                    'bill_id'=>$bill->id,
                    'bill_type'=>$bill->bill_type,
                    'bill_no'=>$bill->bill_no,
                    'gold_sn'=>$goods['gold_sn'],
                    'gold_name'=>$goods['gold_name'],
                    'style_sn'=>$goods['style_sn'],
                    'gold_type'=>$goods['gold_type'],
                    'gold_num'=>$goods['gold_num'],
                    'gold_weight'=>$goods['gold_weight'],
                    'status'=> PandianStatusEnum::SAVE,
                ];
                $bill_goods_values[] = array_values($bill_goods);
                $gold_weight = bcadd($gold_weight, $goods['gold_weight'], 3);
            }
            if(empty($bill_goods_keys)) {
                $bill_goods_keys = array_keys($bill_goods);
            }
            //导入明细
            $result = Yii::$app->db->createCommand()->batchInsert(WarehouseGoldBillGoods::tableName(), $bill_goods_keys, $bill_goods_values)->execute();
            if(!$result) {
                throw new \Exception('导入单据明细失败');
            }
        }

        //同步盘点明细关系表
        $sql = "insert into ".WarehouseGoldBillGoodsW::tableName().'(id,adjust_status,status) select id,0,0 from '.WarehouseGoldBillGoods::tableName()." where bill_id=".$bill->id;
        $should_num = Yii::$app->db->createCommand($sql)->execute();
        if(false === $should_num) {
            throw new \Exception('导入单据明细失败2');
        }
        //盘点单附属表
        $billW = new WarehouseGoldBillW();
        $billW->id = $bill->id;
        $billW->gold_type = $form->gold_type;
        $billW->should_num = $should_num;
        $billW->should_weight = $gold_weight;
        if(false === $billW->save()){
            throw new \Exception($this->getError($billW));
        }
        //更新应盘数量和总金额
        $this->billWSummary($bill->id);
        return $bill;
    }

    /**
     * 盘点商品操作
     * @param WarehouseBillWForm $form
     */
    public function pandianGoods($form)
    {
        //校验单据状态
        if ($form->bill_status != GoldBillStatusEnum::SAVE) {
            throw new \Exception("单据已盘点结束");
        }
        if(false === $form->validate()) {
            throw new \Exception($this->getError($form));
        }
        $bill_detail_ids = [];
        $billGoods = WarehouseGoldBillGoods::find()->where(['gold_sn'=>$form->gold_sn,'bill_id'=>$form->id])->one();
        if($billGoods && $billGoods->status == PandianStatusEnum::NORMAL) {
            //已盘点且正常的忽略
            throw new \Exception("批次号[{$form->gold_sn}]已盘点且正常");
        }
        $goods = WarehouseGold::find()->where(['gold_sn'=>$form->gold_sn])->one();
        if(empty($goods)) {
            throw new \Exception("[{$form->gold_sn}]批次号不存在");
        }
        if(!$billGoods) {
            $billGoods = new WarehouseGoldBillGoods();
            $billGoods->bill_id = $form->id;
            $billGoods->bill_no = $form->bill_no;
            $billGoods->bill_type = $form->bill_type;
            $billGoods->gold_sn = $goods->gold_sn;
            $billGoods->gold_name = $goods->gold_name;
            $billGoods->style_sn = $goods->style_sn;
            $billGoods->gold_type = $goods->gold_type;
            $billGoods->gold_weight = $form->gold_weight;
            $billGoods->status = PandianStatusEnum::PROFIT;//盘盈
        }else {
            if($form->to_warehouse_id == $goods->warehouse_id
                && bccomp($billGoods->gold_weight,$form->gold_weight,2)==0) {
                $billGoods->status = PandianStatusEnum::NORMAL;//正常
            }elseif($form->to_warehouse_id != $goods->warehouse_id
                || bccomp($billGoods->gold_weight,$form->gold_weight,2)!=0){
                $billGoods->status = PandianStatusEnum::LOSS;//盘亏
            }
        }
        //更多商品属性
        //............
        if(false === $billGoods->save()) {
            throw new \Exception($this->getError($billGoods));
        }
        $data = ['status'=>ConfirmEnum::YES,'actual_weight'=>$form->gold_weight,'fin_status'=>FinAuditStatusEnum::PENDING];
        WarehouseGoldBillGoodsW::updateAll($data,['id'=>$billGoods->id]);
        $this->billWSummary($form->id);
    }

    /**
     * 财务盘点明细-审核
     * @param $form
     */
    public function auditFinW($form)
    {
        if(false === $form->validate()) {
            throw new \Exception($this->getError($form));
        }
        if($form->fin_status != FinAuditStatusEnum::PASS){
            $form->fin_status = FinAuditStatusEnum::UNPASS;
        }
        if(false === $form->save()) {
            throw new \Exception($this->getError($form));
        }
    }

    /**
     * 盘点单汇总
     * @param int $bill_id
     */
    public function billWSummary($bill_id)
    {
        $sum = WarehouseGoldBillGoods::find()->alias("g")->innerJoin(WarehouseGoldBillGoodsW::tableName().' gw','g.id=gw.id')
            ->select(['sum(if(gw.status='.ConfirmEnum::YES.',1,0)) as actual_num',
                'sum(if(gw.status='.ConfirmEnum::YES.',g.gold_weight,0)) as actual_weight',
                'sum(if(g.status='.PandianStatusEnum::PROFIT.',1,0)) as profit_num',
                'sum(if(g.status='.PandianStatusEnum::PROFIT.',g.gold_weight,0)) as profit_weight',
                'sum(if(g.status='.PandianStatusEnum::LOSS.',1,0)) as loss_num',
                'sum(if(g.status='.PandianStatusEnum::LOSS.',g.gold_weight,0)) as loss_weight',
                'sum(if(g.status='.PandianStatusEnum::SAVE.',1,0)) as save_num',
                'sum(if(g.status='.PandianStatusEnum::SAVE.',g.gold_weight,0)) as save_weight',
                'sum(if(g.status='.PandianStatusEnum::NORMAL.',1,0)) as normal_num',
                'sum(if(g.status='.PandianStatusEnum::NORMAL.',g.gold_weight,0)) as normal_weight',
                'sum(if(gw.adjust_status>'.PandianAdjustEnum::SAVE.',1,0)) as adjust_num',
                'sum(if(gw.adjust_status>'.PandianAdjustEnum::SAVE.',g.gold_weight,0)) as adjust_weight',
                'sum(1) as goods_num',//明细总数量
                'sum(IFNULL(g.cost_price,0)) as total_cost',
            ])->where(['g.bill_id'=>$bill_id])->asArray()->one();
        if($sum) {
            $billUpdate = ['total_num'=>$sum['goods_num']];
            $billWUpdate = [
                'save_num'=>$sum['save_num'],'actual_num'=>$sum['actual_num'], 'loss_num'=>$sum['loss_num'], 'normal_num'=>$sum['normal_num'], 'adjust_num'=>$sum['adjust_num'],
                'save_weight'=>$sum['save_weight'],'actual_weight'=>$sum['actual_weight'], 'loss_weight'=>$sum['loss_weight'], 'normal_weight'=>$sum['normal_weight'], 'adjust_weight'=>$sum['adjust_weight']
            ];
            $res1 = WarehouseGoldBill::updateAll($billUpdate,['id'=>$bill_id]);
            $res2 = WarehouseGoldBillW::updateAll($billWUpdate,['id'=>$bill_id]);
            return $res1 && $res2;
        }
        return false;
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
            'bill_no' => $form->bill_no,
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
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
    public function purchaseStoneBillSummary($bill_id)
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
        $bill_id = $billM->attributes['id'];
        $goodsM = new WarehouseStoneBillGoods();
        foreach ($details as &$good){
            $good['bill_id'] = $bill_id;
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
        $res = Yii::$app->db->createCommand()->batchInsert(WarehouseStoneBillGoods::tableName(), $key, $value)->execute();
        if(false === $res){
            throw new \Exception("创建买石单明细失败");
        }
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

            $billGoods = WarehouseStoneBillGoods::find()->select(['stone_name', 'source_detail_id'])->where(['bill_id' => $form->id])->asArray()->all();
            if(empty($billGoods)){
                throw new \Exception("单据明细不能为空");
            }
            //石包入库
            \Yii::$app->warehouseService->stone->editStone($form);
            if($form->audit_status == AuditStatusEnum::PASS){
                //同步石料采购收货单货品状态
                $ids = ArrayHelper::getColumn($billGoods, 'source_detail_id');
                $res = PurchaseStoneReceiptGoods::updateAll(['goods_status'=>ReceiptGoodsStatusEnum::WAREHOUSE], ['id'=>$ids]);
                if(false === $res) {
                    throw new \Exception("同步石料采购收货单货品状态失败");
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
        $bill = new WarehouseStoneBill();
        $bill->attributes = $form->toArray();
        $bill->bill_status = StoneBillStatusEnum::SAVE;
        if(false === $bill->save() ) {
            throw new \Exception($this->getError($bill));
        }
        //批量创建单据明细
        $goods_list = WarehouseStone::find()->where(['warehouse_id'=>$bill->to_warehouse_id, 'stone_type' => $form->stone_type])->asArray()->all();
        $stock_weight = 0;
        $bill_goods_values = [];
        if(!empty($goods_list)) {
            $bill_goods= [];
            foreach ($goods_list as $goods) {
                $bill_goods = [
                    'bill_id'=>$bill->id,
                    'bill_type'=>$bill->bill_type,
                    'bill_no'=>$bill->bill_no,
                    'stone_sn'=>$goods['stone_sn'],
                    'stone_name'=>$goods['stone_name'],
                    'style_sn'=>$goods['style_sn'],
                    'stone_type'=>$goods['stone_type'],
                    'color' => $goods['stone_color'],
                    'clarity' => $goods['stone_clarity'],
                    'cut' => $goods['stone_cut'],
                    'polish' => $goods['stone_polish'],
                    'fluorescence' => $goods['stone_fluorescence'],
                    'symmetry' => $goods['stone_symmetry'],
                    'stone_num'=>$goods['stock_cnt'],
                    'stone_weight'=>$goods['stock_weight'],
                    'status'=> PandianStatusEnum::SAVE,
                ];
                $bill_goods_values[] = array_values($bill_goods);
                $stock_weight = bcadd($stock_weight, $goods['stock_weight'], 3);
            }
            if(empty($bill_goods_keys)) {
                $bill_goods_keys = array_keys($bill_goods);
            }
            //导入明细
            $result = Yii::$app->db->createCommand()->batchInsert(WarehouseStoneBillGoods::tableName(), $bill_goods_keys, $bill_goods_values)->execute();
            if(!$result) {
                throw new \Exception('导入单据明细失败');
            }
        }
        //同步盘点明细关系表
        $sql = "insert into ".WarehouseStoneBillGoodsW::tableName().'(id,adjust_status,status) select id,0,0 from '.WarehouseStoneBillGoods::tableName()." where bill_id=".$bill->id;
        $should_num = Yii::$app->db->createCommand($sql)->execute();
        if(false === $should_num) {
            throw new \Exception('导入单据明细失败2');
        }
        //盘点单附属表
        $billW = new WarehouseStoneBillW();
        $billW->id = $bill->id;
        $billW->stone_type = $form->stone_type;
        $billW->should_num = $should_num;
        $billW->should_weight = $stock_weight;
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
     * 盘点结束
     * @param WarehouseBillW $bill
     */
    public function finishBillW($bill_id)
    {
        $bill = WarehouseGoldBill::find()->where(['id'=>$bill_id])->one();
        if(!$bill || $bill->status == BillWStatusEnum::FINISHED) {
            throw new \Exception("盘点已结束");
        }
        $bill->status = BillWStatusEnum::FINISHED;
        $bill->bill_status = BillStatusEnum::PENDING; //待审核
        if(false === $bill->save(false,['id','status', 'bill_status'])) {
            throw new \Exception($this->getError($bill));
        }
        //1.未盘点设为盘亏
        WarehouseGoldBillGoods::updateAll(['status'=>PandianStatusEnum::LOSS],['bill_id'=>$bill_id,'status'=>PandianStatusEnum::SAVE]);

        //2.解锁商品
        $subQuery = WarehouseGoldBillGoods::find()->select(['gold_sn'])->where(['bill_id'=>$bill->id]);
        WarehouseGold::updateAll(['gold_status'=>GoldStatusEnum::IN_STOCK],['gold_sn'=>$subQuery,'gold_status'=>GoldStatusEnum::IN_PANDIAN]);

        //3.解锁仓库
        \Yii::$app->warehouseService->warehouse->unlockWarehouse($bill->to_warehouse_id);

        //4.自动调整盘亏盘盈数据
        //$this->adjustGoods($bill_id);
        //5.盘点单汇总
        $this->billWSummary($bill_id);
    }

    /**
     * 盘点审核
     * @param WarehouseBillWForm $form
     */
    public function auditBillW($form)
    {
        if(false === $form->validate()) {
            throw new \Exception($this->getError($form));
        }
        $subQuery = WarehouseGoldBillGoods::find()->select(['gold_sn'])->where(['bill_id'=>$form->id]);
        if($form->audit_status == AuditStatusEnum::PASS) {
            $form->bill_status = GoldBillStatusEnum::CONFIRM;
            WarehouseGold::updateAll(['gold_status'=>GoldStatusEnum::IN_STOCK],['gold_sn'=>$subQuery,'gold_status'=>GoldStatusEnum::IN_PANDIAN]);
            //解锁仓库
            \Yii::$app->warehouseService->warehouse->unlockWarehouse($form->to_warehouse_id);
        }else {
            $form->bill_status = GoldBillStatusEnum::CANCEL;
            WarehouseGoods::updateAll(['gold_status'=>GoldStatusEnum::IN_STOCK],['gold_sn'=>$subQuery,'gold_status'=>GoldStatusEnum::IN_PANDIAN]);
        }
        if(false === $form->save() ){
            throw new \Exception($this->getError($form));
        }
    }

    /**
     * 盘点单汇总
     * @param int $bill_id
     */
    public function billWSummary($bill_id)
    {
        $sum = WarehouseStoneBillGoods::find()->alias("g")->innerJoin(WarehouseStoneBillGoodsW::tableName().' gw','g.id=gw.id')
            ->select(['sum(if(gw.status='.ConfirmEnum::YES.',1,0)) as actual_num',
                'sum(if(gw.status='.ConfirmEnum::YES.',g.stone_weight,0)) as actual_weight',
                'sum(if(g.status='.PandianStatusEnum::PROFIT.',1,0)) as profit_num',
                'sum(if(g.status='.PandianStatusEnum::PROFIT.',g.stone_weight,0)) as profit_weight',
                'sum(if(g.status='.PandianStatusEnum::LOSS.',1,0)) as loss_num',
                'sum(if(g.status='.PandianStatusEnum::LOSS.',g.stone_weight,0)) as loss_weight',
                'sum(if(g.status='.PandianStatusEnum::SAVE.',1,0)) as save_num',
                'sum(if(g.status='.PandianStatusEnum::SAVE.',g.stone_weight,0)) as save_weight',
                'sum(if(g.status='.PandianStatusEnum::NORMAL.',1,0)) as normal_num',
                'sum(if(g.status='.PandianStatusEnum::NORMAL.',g.stone_weight,0)) as normal_weight',
                'sum(if(gw.adjust_status>'.PandianAdjustEnum::SAVE.',1,0)) as adjust_num',
                'sum(if(gw.adjust_status>'.PandianAdjustEnum::SAVE.',g.stone_weight,0)) as adjust_weight',
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
<?php

namespace addons\Warehouse\services;

use addons\Warehouse\common\forms\WarehouseStoneBillMsForm;
use Yii;
use common\components\Service;
use common\helpers\SnHelper;
use addons\Warehouse\common\models\WarehouseStone;
use addons\Warehouse\common\models\WarehouseStoneBill;
use addons\Warehouse\common\models\WarehouseStoneBillGoods;
use addons\Purchase\common\models\PurchaseStoneReceiptGoods;
use addons\Warehouse\common\enums\BillStatusEnum;
use addons\Warehouse\common\enums\StoneStatusEnum;
use addons\Purchase\common\enums\ReceiptGoodsStatusEnum;
use common\enums\AuditStatusEnum;
use common\helpers\ArrayHelper;
use common\helpers\Url;

/**
 * 石料入库单（买石单）
 * @package services\common
 * @author jianyan74 <751393839@qq.com>
 */
class WarehouseStoneBillMsService extends Service
{

    /**
     * 创建石料入库单（买石单）
     * @param array $bill
     * @param array $details
     * @throws
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
        $res = \Yii::$app->db->createCommand()->batchInsert(WarehouseStoneBillGoods::tableName(), $key, $value)->execute();
        if(false === $res){
            throw new \Exception("创建买石单明细失败");
        }
        \Yii::$app->warehouseService->stoneBill->stoneBillSummary($billM->id);
        return $billM;
    }
    /**
     * 买石单-审核
     * @param WarehouseStoneBillMsForm $form
     * @throws
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
                //$billGoods = new WarehouseStoneBillGoods();
                $stoneM = new WarehouseStone();
                $stoneData = [
                    'stone_sn' => (string) rand(10000000000,99999999999),//临时
                    'stone_name' => $billGoods->stone_name,
                    'stone_status' => StoneStatusEnum::IN_STOCK,
                    'style_sn' => $billGoods->style_sn,
                    'stone_type' => $billGoods->stone_type,
                    'supplier_id' => $form->supplier_id,
                    'put_in_type' => $form->put_in_type,
                    'warehouse_id' => $form->to_warehouse_id,
                    'channel_id' => $billGoods->channel_id,
                    'stone_shape' => $billGoods->shape,
                    'stone_color' => $billGoods->color,
                    'stone_clarity' => $billGoods->clarity,
                    'stone_cut' => $billGoods->cut,
                    'stone_symmetry' => $billGoods->symmetry,
                    'stone_polish' => $billGoods->polish,
                    'stone_fluorescence' => $billGoods->fluorescence,
                    'cert_id' => $billGoods->cert_id,
                    'cert_type' => (string) $billGoods->cert_type,
                    'stone_norms' => $billGoods->stone_norms,
                    'stone_size' => $billGoods->stone_size,
                    'stone_colour' => $billGoods->stone_colour,
                    'stock_cnt' => $billGoods->stone_num,
                    'ms_cnt' => $billGoods->stone_num,
                    'stock_weight' => $billGoods->stone_weight,
                    'ms_weight' => $billGoods->stone_weight,
                    'stone_price' => $billGoods->stone_price,
                    'cost_price' => $billGoods->cost_price,
                    'sale_price' => $billGoods->sale_price,
                    'remark' => $billGoods->remark,
                    'creator_id'=>\Yii::$app->user->identity->getId(),
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

}
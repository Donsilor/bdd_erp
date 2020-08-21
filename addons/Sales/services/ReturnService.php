<?php
/**
 * Created by PhpStorm.
 * User: BDD
 * Date: 2019/12/7
 * Time: 13:53
 */

namespace addons\Sales\services;

use addons\Sales\common\enums\DeliveryStatusEnum;
use addons\Sales\common\enums\RefundStatusEnum;
use addons\Sales\common\enums\ReturnByEnum;
use addons\Sales\common\enums\CheckStatusEnum;
use addons\Sales\common\enums\ReturnTypeEnum;
use addons\Sales\common\models\OrderGoods;
use addons\Warehouse\common\enums\BillStatusEnum;
use addons\Warehouse\common\enums\BillTypeEnum;
use addons\Warehouse\common\enums\GoodsStatusEnum;
use addons\Warehouse\common\enums\OrderTypeEnum;
use addons\Warehouse\common\models\WarehouseGoods;
use common\enums\AuditStatusEnum;
use common\enums\StatusEnum;
use common\helpers\SnHelper;
use common\helpers\Url;
use addons\Sales\common\forms\ReturnForm;
use addons\Sales\common\models\Order;

class ReturnService
{

    /**
     * 退款单 tab
     * @param int $return_id 快递公司ID
     * @param string $returnUrl
     * @return array
     */
    public function menuTabList($return_id, $returnUrl = null)
    {
        return [
            1 => ['name' => '退款单详情', 'url' => Url::to(['return/view', 'id' => $return_id, 'tab' => 1, 'returnUrl' => $returnUrl])],
            //2=>['name'=>'快递配送区域','url'=>Url::to(['express-area/index','express_id'=>$return_id,'tab'=>2,'returnUrl'=>$returnUrl])],
        ];
    }

    /**
     * @param ReturnForm $form
     * @param Order $order
     * @throws \Exception
     * @return object $form
     * 退款
     */
    public function salesReturn($form, $order)
    {
        if(empty($form->ids) && !is_array($form->ids)){
            throw new \Exception("请选择需要退款的商品");
        }
        foreach ($form->ids as $id) {
            $goods = OrderGoods::findOne($id);
            if($order->delivery_status == DeliveryStatusEnum::HAS_SEND){
                $form->return_by = ReturnByEnum::GOODS;
            }else{
                $form->return_by = ReturnByEnum::NO_GOODS;
            }
            $return = [
                'return_no' => SnHelper::createReturnSn(),
                'order_id' => $order->id,
                'order_sn' => $order->order_sn,
                'order_detail_id' => $goods->id,
                'channel_id' => $order->sale_channel_id,
                'goods_num' => $goods->goods_num,
                'should_amount' => $goods->goods_pay_price,
                'apply_amount' => $goods->goods_pay_price,
                'return_reason' => $form->return_reason,
                'return_by' => $form->return_by,
                'return_type' => $form->return_type,
                'customer_id' => $order->customer_id,
                'customer_name' => $order->customer_name,
                'customer_mobile' => $order->customer_mobile,
                'customer_email' => $order->customer_email,
                'currency' => $order->currency,
                //'bank_name' => '',
                'bank_card' => $order->customer_account,
                'is_quick_refund' => $form->is_quick_refund,
                'check_status' => CheckStatusEnum::SAVE,
                'remark' => $form->remark,
                'creator_id' => \Yii::$app->user->identity->getId(),
                'created_at' => time(),
            ];
            $form->attributes = $return;
            if(false === $form->save()){
                throw new \Exception($this->getError($form));
            }
        }

        //同步订单信息
        $order->refund_status = RefundStatusEnum::APPLY;
        if(false === $order->save()){
            throw new \Exception($this->getError($order));
        }

        return $form;
    }

    /**
     * @param ReturnForm $form
     * @throws \Exception
     * @return object $form
     * 退款-审核
     */
    public function auditReturn($form)
    {
        $check_status = $form->check_status;
        if($check_status == CheckStatusEnum::SAVE){
            $form->leader_id = \Yii::$app->user->getId();
            $form->leader_time = time();
            if($form->leader_status == AuditStatusEnum::PASS){
                $form->check_status = CheckStatusEnum::LEADER;
            }
        }elseif($check_status == CheckStatusEnum::LEADER){
            $form->storekeeper_id = \Yii::$app->user->getId();
            $form->storekeeper_time = time();
            if($form->storekeeper_status == AuditStatusEnum::PASS){
                $form->check_status = CheckStatusEnum::STOREKEEPER;
                $this->createBillD($form);
            }else{
                //$form->check_status = CheckStatusEnum::SAVE;
            }
        }elseif($check_status == CheckStatusEnum::STOREKEEPER){
            $form->finance_id = \Yii::$app->user->getId();
            $form->finance_time = time();
            if($form->finance_status == AuditStatusEnum::PASS){
                $form->check_status = CheckStatusEnum::FINANCE;
            }else{
                //$form->check_status = CheckStatusEnum::LEADER;
            }
        }else{
            throw new \Exception("审核失败");
        }
        if(false === $form->save()) {
            throw new \Exception($this->getError($form));
        }
        return $form;
    }

    /**
     * @param ReturnForm $form
     * @throws \Exception
     * @return object $form
     * 创建销售退货单
     */
    public function createBillD($form)
    {
        $goods_ids[$form->order_detail_id] = $form->goods_id;
        if(empty($goods_ids)){
            throw new \Exception("货号[条码号]不能为空");
        }
        $bill_goods = [];
        $total_cost = $total_sale = $total_market = 0;
        foreach ($goods_ids as $id => $goods_id) {
            if(!$goods_id){
                throw new \Exception("货号不能为空");
            }
            $goods = WarehouseGoods::find()->where(['goods_id'=>$goods_id])->one();
            if(!$goods){
                throw new \Exception("货号".$goods_id."不存在");
            }
            if($goods->goods_status != GoodsStatusEnum::HAS_SOLD){
                throw new \Exception("货号".$goods_id."不是已销售状态");
            }
            $orderGoods = OrderGoods::findOne($id);
            //$goodsAccount = OrderAccount::findOne($id);
            //$goods = new WarehouseGoods();
            $bill_goods[] = [
                'goods_id' => $goods_id,
                'goods_name' => $goods->goods_name,
                'style_sn' => $goods->style_sn,
                'goods_num' => $goods->goods_num,
                'order_detail_id' => $id,
                'source_detail_id' => $id,
                'put_in_type' => $goods->put_in_type,
                'warehouse_id' => $goods->warehouse_id,
                'material' => $goods->material,
                'material_type' => $goods->material_type,
                'material_color' => $goods->material_color,
                'gold_weight' => $goods->gold_weight,
                'gold_loss' => $goods->gold_loss,
                'diamond_carat' => $goods->diamond_carat,
                'diamond_color' => $goods->diamond_color,
                'diamond_clarity' => $goods->diamond_clarity,
                'diamond_cert_id' => $goods->diamond_cert_id,
                'diamond_cert_type' => $goods->diamond_cert_type,
                'cost_price' => $goods->cost_price,
                'sale_price' => $form->real_amount,
                'market_price' => $goods->market_price,
                'markup_rate' => 1,
                'status' => StatusEnum::ENABLED,
                'creator_id' =>\Yii::$app->user->identity->getId(),
                'created_at' => time(),
            ];

            $total_cost = bcadd($total_cost, $goods->cost_price, 2);
            $total_market = bcadd($total_market, $goods->market_price, 2);
            $total_sale = bcadd($total_sale, $form->real_amount, 2);
        }
        $bill = [
            'bill_type' => BillTypeEnum::BILL_TYPE_D,
            'bill_status' => BillStatusEnum::PENDING,
            'channel_id' => $form->sale_channel_id,
            'order_sn' => $form->order_sn,
            'order_type' => OrderTypeEnum::ORDER_K,
            'goods_num' => count($bill_goods),
            'total_cost' => $total_cost,
            'total_market' => $total_market,
            'total_sale' => $total_sale,
            'auditor_id' => \Yii::$app->user->identity->getId(),
            'audit_status' => AuditStatusEnum::PENDING,
            'audit_time' => time(),
            'creator_id' => \Yii::$app->user->identity->getId(),
            'created_at' => time(),
        ];

        //1.创建销售退货单
        \Yii::$app->warehouseService->billD->createBillD($bill, $bill_goods);

        //2.更新商品库存状态
        $condition = ['goods_id'=>$goods_ids, 'goods_status' => GoodsStatusEnum::HAS_SOLD];
        $execute_num = WarehouseGoods::updateAll(['goods_status'=> GoodsStatusEnum::IN_REFUND], $condition);
        if($execute_num <> count($bill_goods)){
            throw new \Exception("货品改变状态数量与明细数量不一致");
        }
        return $form;
    }
}
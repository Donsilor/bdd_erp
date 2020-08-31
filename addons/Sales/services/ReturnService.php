<?php
/**
 * Created by PhpStorm.
 * User: BDD
 * Date: 2019/12/7
 * Time: 13:53
 */

namespace addons\Sales\services;

use addons\Sales\common\enums\DistributeStatusEnum;
use addons\Sales\common\enums\ReturnTypeEnum;
use addons\Sales\common\models\OrderAccount;
use addons\Sales\common\models\OrderAddress;
use addons\Sales\common\models\OrderGoods;
use addons\Sales\common\forms\ReturnGoodsForm;
use addons\Sales\common\models\SalesReturn;
use addons\Warehouse\common\forms\WarehouseBillDForm;
use addons\Warehouse\common\models\WarehouseBillGoods;
use addons\Warehouse\common\models\WarehouseGoods;
use addons\Sales\common\models\Order;
use addons\Sales\common\forms\ReturnForm;
use addons\Sales\common\enums\DeliveryStatusEnum;
use addons\Sales\common\enums\IsReturnEnum;
use addons\Sales\common\enums\RefundStatusEnum;
use addons\Sales\common\enums\ReturnByEnum;
use addons\Sales\common\enums\CheckStatusEnum;
use addons\Sales\common\enums\ReturnStatusEnum;
use addons\Warehouse\common\enums\BillStatusEnum;
use addons\Warehouse\common\enums\BillTypeEnum;
use addons\Warehouse\common\enums\GoodsStatusEnum;
use addons\Warehouse\common\enums\OrderTypeEnum;
use common\components\Service;
use common\enums\AuditStatusEnum;
use common\enums\LogTypeEnum;
use common\enums\StatusEnum;
use common\helpers\ArrayHelper;
use common\helpers\SnHelper;
use common\helpers\Url;

class ReturnService extends Service
{

    /**
     *
     * 退款单 tab
     * @param int $return_id 退款ID
     * @param string $returnUrl
     * @return array
     */
    public function menuTabList($return_id, $returnUrl = null)
    {
        return [
            1 => ['name' => '退款单详情', 'url' => Url::to(['return/view', 'id' => $return_id, 'tab' => 1, 'returnUrl' => $returnUrl])],
            2 => ['name' => '退款单明细', 'url' => Url::to(['return-goods/index', 'return_id' => $return_id, 'tab' => 2, 'returnUrl' => $returnUrl])],
            3 => ['name' => '退款单日志', 'url' => Url::to(['return-log/index', 'return_id' => $return_id, 'tab' => 3, 'returnUrl' => $returnUrl])],
        ];
    }

    /**
     *
     *  创建退款单
     * @param ReturnForm $form
     * @param Order $order
     * @return object $form
     * @throws \Exception
     */
    public function createReturn($form, $order)
    {
        if (empty($form->ids) && !is_array($form->ids)) {
            throw new \Exception("请选择需要退款的商品");
        }
        $newOrder = null;
        if ($form->new_order_sn) {
            $newOrder = Order::findOne(['order_sn' => $form->new_order_sn]);
            if (empty($newOrder)) {
                throw new \Exception("新订单号不存在");
            }
        }
        if ($order->delivery_status == DeliveryStatusEnum::HAS_SEND) {
            $form->return_by = ReturnByEnum::GOODS;
        } else {
            $form->return_by = ReturnByEnum::NO_GOODS;
        }
        $goods_num = $should_amount = $apply_amount = 0;
        $rGoods = [];
        $return_no =  SnHelper::createReturnSn();
        foreach ($form->ids as $id) {
            $goods = OrderGoods::findOne($id);
            $rGoods[] = [
                'return_id' => rand(10000, 99999),
                'return_no' => $return_no,
                'goods_id' => $goods->goods_id,
                'goods_name' => $goods->goods_name,
                'order_detail_id' => $goods->id,
                'goods_num' => $goods->goods_num,
                'should_amount' => $goods->goods_pay_price,
                'apply_amount' => $goods->goods_pay_price,
                'status' => StatusEnum::ENABLED,
                'creator_id' => \Yii::$app->user->identity->getId(),
                'created_at' => time(),
            ];
            $goods_num = bcadd($goods_num, $goods->goods_num);
            $should_amount = bcadd($should_amount, $goods->goods_pay_price, 3);
            $apply_amount = bcadd($apply_amount, $goods->goods_pay_price, 3);

            //同步订单明细信息
            $goods->is_return = IsReturnEnum::APPLY;
            $goods->return_no = $return_no;
            if (false === $goods->save()) {
                throw new \Exception($this->getError($goods));
            }
        }
        $return = [
            'return_no' => $return_no,
            'order_id' => $order->id,
            'order_sn' => $order->order_sn,
            'new_order_id' => $newOrder ? $newOrder->id : "",
            'new_order_sn' => $newOrder ? $newOrder->order_sn : "",
            'channel_id' => $order->sale_channel_id,
            'goods_num' => $goods_num,
            'should_amount' => $should_amount,
            'apply_amount' => $apply_amount,
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
        if (false === $form->save()) {
            throw new \Exception($this->getError($form));
        }
        foreach ($rGoods as $good) {
            $goodsM = new ReturnGoodsForm();
            $goodsM->attributes = $good;
            $goodsM->return_id = $form->id;
            //$goodsM->return_no = $form->return_no;
            if (false === $goodsM->save()) {
                throw new \Exception($this->getError($goodsM));
            }

            $goods = OrderGoods::findOne($goodsM->order_detail_id);
            $goods->return_id = $form->id;
            if (false === $goods->save(true, ['return_id'])) {
                throw new \Exception($this->getError($goods));
            }
        }
        //同步订单信息
        $order->refund_status = RefundStatusEnum::APPLY;
        if (false === $order->save()) {
            throw new \Exception($this->getError($order));
        }

        return $form;
    }

    /**
     * 退款-审核
     * @param ReturnForm $form
     * @return object $form
     * @throws
     */
    public function auditReturn($form)
    {
        $check_status = $form->check_status;
        if ($check_status == CheckStatusEnum::SAVE) {//主管审核
            $form->leader_id = \Yii::$app->user->getId();
            $form->leader_time = time();
            if ($form->leader_status == AuditStatusEnum::PASS) {
                $form->check_status = CheckStatusEnum::LEADER;
                $form->storekeeper_status = AuditStatusEnum::PENDING;
            } else {
                $form->return_status = ReturnStatusEnum::SAVE;
                $form->audit_status = AuditStatusEnum::SAVE;
            }
        } elseif ($check_status == CheckStatusEnum::LEADER) {//商品部审核
            $form->storekeeper_id = \Yii::$app->user->getId();
            $form->storekeeper_time = time();
            if ($form->storekeeper_status == AuditStatusEnum::PASS) {
                $form->check_status = CheckStatusEnum::STOREKEEPER;
                $form->finance_status = AuditStatusEnum::PENDING;
                if ($form->return_by == ReturnByEnum::GOODS) {//退商品
                    $billM = $this->createBillD($form);
                    $form->bill_no = $billM->bill_no;
                }
            } else {
                $form->check_status = CheckStatusEnum::SAVE;
                $form->leader_status = AuditStatusEnum::PENDING;
            }
        } elseif ($check_status == CheckStatusEnum::STOREKEEPER) {//财务审核
            $form->finance_id = \Yii::$app->user->getId();
            $form->finance_time = time();
            if ($form->finance_status == AuditStatusEnum::PASS) {
                $form->check_status = CheckStatusEnum::FINANCE;
                $order = Order::findOne($form->order_id);
                $rGoods = ReturnGoodsForm::findAll(['return_id' => $form->id]);
                $goods_id = [];
                foreach ($rGoods as $good) {
                    $goods = OrderGoods::findOne($good->order_detail_id);
                    $goods_id[] = $good->goods_id;
                    $goods->is_return = IsReturnEnum::HAS_RETURN;
                    if (false === $goods->save()) {
                        throw new \Exception($this->getError($goods));
                    }
                }
                $count = OrderGoods::find()->where(['order_id'=>$order->id, 'is_return'=>[IsReturnEnum::SAVE, IsReturnEnum::APPLY]])->count();
                if($count>0){
                    $order->refund_status = RefundStatusEnum::PART_RETURN;
                }else{
                    $order->refund_status = RefundStatusEnum::HAS_RETURN;
                }
                if (false === $order->save()) {
                    throw new \Exception($this->getError($order));
                }
                if ($form->return_type == ReturnTypeEnum::TRANSFER) {//转单
                    $newOrder = $this->zhuandan($form);
                    $form->new_order_id = $newOrder ? $newOrder->id : "";
                    $form->new_order_sn = $newOrder ? $newOrder->order_sn : "";
                }
                if ($form->return_by == ReturnByEnum::GOODS) {
                    //1.审核销售退货单
                    $bill = WarehouseBillDForm::findOne(['bill_no' => $form->bill_no]);
                    if (!empty($bill)) {
                        $bill->bill_status = BillStatusEnum::CONFIRM;
                        $bill->audit_status = AuditStatusEnum::PASS;
                        if (false === $bill->save()) {
                            throw new \Exception($this->getError($bill));
                        }
                    } else {
                        throw new \Exception("销售退货单不存在[code=1]");
                    }
                    //2.更新商品库存状态
                    $condition = ['goods_id' => $goods_id, 'goods_status' => GoodsStatusEnum::IN_REFUND];
                    if ($form->return_type == ReturnTypeEnum::TRANSFER) {
                        $goods_status = GoodsStatusEnum::IN_SALE;
                    } else {
                        $goods_status = GoodsStatusEnum::IN_STOCK;
                    }
                    WarehouseGoods::updateAll(['goods_status' => $goods_status], $condition);
                }
                //3.更新订单金额
                $account = OrderAccount::findOne($form->order_id);
                //$account->order_amount = bcsub($account->order_amount, $form->real_amount, 2);
                $account->refund_amount = bcadd($account->refund_amount, $form->real_amount, 2);
                if (false === $account->save()) {
                    throw new \Exception($this->getError($account));
                }

                $form->return_status = ReturnStatusEnum::CONFIRM;
                $form->audit_status = AuditStatusEnum::PASS;
            } else {
                $form->check_status = CheckStatusEnum::LEADER;
                $form->storekeeper_status = AuditStatusEnum::PENDING;
                if ($form->return_by == ReturnByEnum::GOODS) {
                    //1.取消销售退货单
                    $bill = WarehouseBillDForm::findOne(['bill_no' => $form->bill_no]);
                    if (!empty($bill)) {
                        $bill->bill_status = BillStatusEnum::CANCEL;
                        $bill->audit_status = AuditStatusEnum::UNPASS;
                        if (false === $bill->save()) {
                            throw new \Exception($this->getError($bill));
                        }
                    } else {
                        throw new \Exception("销售退货单不存在[code=2]");
                    }
                    //2.更新商品库存状态
                    $bGoods = WarehouseBillGoods::findAll(['bill_id' => $bill->id]);
                    $goods_ids = ArrayHelper::getColumn($bGoods, 'goods_id');
                    $condition = ['goods_id' => $goods_ids, 'goods_status' => GoodsStatusEnum::IN_REFUND];
                    WarehouseGoods::updateAll(['goods_status' => GoodsStatusEnum::HAS_SOLD], $condition);
                }
            }
        } else {
            throw new \Exception("审核失败");
        }
        if (false === $form->save()) {
            throw new \Exception($this->getError($form));
        }
        return $form;
    }

    /**
     * 退款-取消
     * @param ReturnForm $form
     * @return object $form
     * @throws
     */
    public function cancelReturn($form)
    {
        //1.还原订单状态
        $order = Order::findOne(['id' => $form->order_id]);
        $order->refund_status = RefundStatusEnum::SAVE;
        if (false === $order->save()) {
            throw new \Exception($this->getError($order));
        }
        //2.还原商品状态
        $goods = ReturnGoodsForm::findAll(['return_id' => $form->id]);
        $ids = ArrayHelper::getColumn($goods, 'order_detail_id');
        OrderGoods::updateAll(['is_return' => IsReturnEnum::SAVE, 'return_id'=> "", 'return_no'=> ""], ['id' => $ids]);
        //3.取消退款单状态
        $form->return_status = ReturnStatusEnum::CANCEL;
        $form->audit_status = AuditStatusEnum::UNPASS;
        if (false === $form->save()) {
            throw new \Exception($this->getError($form));
        }
        return $form;
    }

    /**
     *
     * 退款-转单
     * @param SalesReturn $form
     * @return object $form
     * @throws
     */
    public function zhuandan($form)
    {
        $order = Order::findOne(['id' => $form->order_id]);
        if (empty($order)) {
            throw new \Exception("未查到相关订单");
        }
        if (!empty($form->new_order_id)) {
            $newOrder = Order::findOne(['id' => $form->new_order_id]);
        } else {
            //1.创建新订单
            $newOrder = new Order();
            $newOrder->attributes = $order->toArray();
            $newOrder->id = null;
            $newOrder->order_sn = (string)rand(10000000000, 99999999999);
            $newOrder->order_time = time();
            $newOrder->express_id = "";
            $newOrder->express_no = "";
            //$newOrder->pay_status = PayStatusEnum::NO_PAY;
            $newOrder->distribute_status = DistributeStatusEnum::SAVE;
            $newOrder->delivery_status = DeliveryStatusEnum::SAVE;
            $newOrder->refund_status = RefundStatusEnum::SAVE;
            $newOrder->delivery_time = "";
            $newOrder->finished_time = "";
            $newOrder->pay_time = time();
            if (false == $newOrder->save()) {
                throw new \Exception($this->getError($newOrder));
            }
            \Yii::$app->salesService->order->createOrderSn($newOrder, true);
        }

        //2.添加商品
        $rGoods = ReturnGoodsForm::findAll(['return_id' => $form->id]);
        $ids = ArrayHelper::getColumn($rGoods, 'order_detail_id');
        if (empty($ids)) {
            throw new \Exception("订单明细ID不能为空");
        }
        $order_amount = $goods_amount = $discount_amount = 0;
        foreach ($ids as $id) {
            $goods = OrderGoods::findOne($id);
            $newGoods = new OrderGoods();
            $newGoods->attributes = $goods->toArray();
            $newGoods->id = null;
            $newGoods->order_id = $newOrder->id;
            $newGoods->is_return = IsReturnEnum::SAVE;
            if (false == $newGoods->save()) {
                throw new \Exception($this->getError($newGoods));
            }
            //3.绑定货号
            if ($form->return_by == ReturnByEnum::GOODS) {
                $wGoods = WarehouseGoods::findOne(['goods_id' => $newGoods->goods_id]);
                if (!empty($wGoods)) {
                    $wGoods->order_sn = $newOrder->order_sn;
                    $wGoods->order_detail_id = (string)$newGoods->id;
                    if (false == $wGoods->save()) {
                        throw new \Exception($this->getError($wGoods));
                    }
                }
            }
            $order_amount = bcadd($order_amount, $newGoods->goods_pay_price, 3);
            $goods_amount = bcadd($goods_amount, $newGoods->goods_price, 3);
            $discount_amount = bcadd($discount_amount, $newGoods->goods_discount, 3);
        }

        if (!empty($form->new_order_id)) {

            $oldAccount = OrderAccount::findOne(['order_id' => $form->new_order_id]);

            $order_amount = bcadd($order_amount, $oldAccount->order_amount, 3);
            $goods_amount = bcadd($goods_amount, $oldAccount->goods_amount, 3);
            $discount_amount = bcadd($discount_amount, $oldAccount->discount_amount, 3);

            $oldAccount->order_amount = $order_amount;
            $oldAccount->goods_amount = $goods_amount;
            $oldAccount->discount_amount = $discount_amount;
            $oldAccount->pay_amount = $order_amount;
            $oldAccount->paid_amount = $order_amount;
            //$account->pay_amount = $order_amount;
            if (false == $oldAccount->save()) {
                throw new \Exception($this->getError($oldAccount));
            }
        } else {
            //4.创建订单金额
            $account = new OrderAccount();
            $account->order_id = $newOrder->id;
            $account->order_amount = $order_amount;
            $account->goods_amount = $goods_amount;
            $account->discount_amount = $discount_amount;
            $account->pay_amount = $order_amount;
            $account->paid_amount = $order_amount;
            //$account->pay_amount = $order_amount;
            if (false == $account->save()) {
                throw new \Exception($this->getError($account));
            }

            //5.创建订单地址信息
            $address = OrderAddress::find()->where(['order_id' => $order->id])->one();
            if (!empty($address)) {
                $newAddress = new OrderAddress();
                $newAddress->attributes = $address->toArray();
                $newAddress->order_id = $newOrder->id;
                if (false == $newAddress->save()) {
                    throw new \Exception($this->getError($newAddress));
                }
            }

            //6.创建订单日志
            $log = [
                'order_id' => $newOrder->id,
                'order_sn' => $newOrder->order_sn,
                'order_status' => $newOrder->order_status,
                'log_type' => LogTypeEnum::ARTIFICIAL,
                'log_time' => time(),
                'log_module' => '创建订单',
                'log_msg' => "创建订单, 订单号:" . $newOrder->order_sn,
            ];
            \Yii::$app->salesService->orderLog->createOrderLog($log);
        }
        return $newOrder;
    }

    /**
     * @param ReturnForm $form
     * @return object $billM
     * 创建销售退货单
     * @throws \Exception
     */
    public function createBillD($form)
    {
        $goods_ids = ReturnGoodsForm::findAll(['return_id' => $form->id]);
        if (empty($goods_ids)) {
            throw new \Exception("货号[条码号]不能为空[code=1]");
        }
        $bill_goods = [];
        $total_cost = $total_sale = $total_market = 0;
        foreach ($goods_ids as $good) {
            $goods_id = $good['goods_id'] ?? "";
            if (!$goods_id) {
                throw new \Exception("货号[条码号]不能为空[code=2]");
            }
            $order_detail_id = $good['order_detail_id'] ?? "";
            if(empty($order_detail_id)){
                throw new \Exception("订单明细ID不能为空");
            }
            $orderGoods = OrderGoods::findOne($order_detail_id);
            if($orderGoods->is_gift){//赠品
                continue;
            }
            $goods = WarehouseGoods::find()->where(['goods_id' => $goods_id])->one();
            if (empty($goods)) {
                throw new \Exception("货号[条码号]" . $goods_id . "不存在");
            }
            if ($goods->goods_status != GoodsStatusEnum::HAS_SOLD) {
                throw new \Exception("货号[条码号]" . $goods_id . "不是已销售状态");
            }
            //$goodsAccount = OrderAccount::findOne($id);
            //$goods = new WarehouseGoods();
            $bill_goods[] = [
                'goods_id' => $goods_id,
                'goods_name' => $goods->goods_name,
                'style_sn' => $goods->style_sn,
                'goods_num' => $goods->goods_num,
                'order_detail_id' => $good['order_detail_id'] ?? "",
                'source_detail_id' => $good['id'] ?? "",
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
                'sale_price' => $form->should_amount,
                'market_price' => $goods->market_price,
                'markup_rate' => 1,
                'status' => StatusEnum::ENABLED,
                'creator_id' => \Yii::$app->user->identity->getId(),
                'created_at' => time(),
            ];

            $total_cost = bcadd($total_cost, $goods->cost_price, 2);
            $total_market = bcadd($total_market, $goods->market_price, 2);
            $total_sale = bcadd($total_sale, $form->real_amount, 2);
        }
        $bill = [
            'bill_type' => BillTypeEnum::BILL_TYPE_D,
            'bill_status' => BillStatusEnum::PENDING,
            'channel_id' => $form->channel_id,
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
        $billM = \Yii::$app->warehouseService->billD->createBillD($bill, $bill_goods);

        //2.更新商品库存状态
        $condition = ['goods_id' => $goods_ids, 'goods_status' => GoodsStatusEnum::HAS_SOLD];
        $execute_num = WarehouseGoods::updateAll(['goods_status' => GoodsStatusEnum::IN_REFUND], $condition);
        if ($execute_num <> count($bill_goods)) {
            throw new \Exception("货品[条码号]改变状态数量与明细数量不一致");
        }
        return $billM;
    }
}
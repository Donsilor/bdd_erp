<?php

namespace addons\Sales\services;

use addons\Sales\common\enums\ProblemTypeEnum;
use common\helpers\Url;
use Yii;
use common\components\Service;
use addons\Sales\common\models\Order;
use addons\Sales\common\forms\OrderFqcForm;
use addons\Warehouse\common\models\WarehouseGoods;
use addons\Warehouse\common\models\WarehouseBill;
use addons\Warehouse\common\models\WarehouseBillGoods;
use addons\Sales\common\enums\DeliveryStatusEnum;
use addons\Sales\common\enums\DistributeStatusEnum;
use addons\Warehouse\common\enums\BillStatusEnum;
use addons\Warehouse\common\enums\GoodsStatusEnum;
use common\helpers\ArrayHelper;
use yii\db\Exception;
use common\enums\LogTypeEnum;

/**
 * Class OrderFqcService
 * @package services\common
 */
class OrderFqcService extends Service
{

    /**
     * tab
     * @param int $order_id
     * @param string $returnUrl
     * @return array
     */
    public function menuTabList($order_id, $returnUrl = null)
    {
        return [
            1 => ['name' => '质检详情', 'url' => Url::to(['order/order-fqc', 'id' => $order_id, 'tab' => 1, 'returnUrl' => $returnUrl])],
            //2=>['name'=>'日志信息','url'=>Url::to(['order-log/index','order_id'=>$order_id,'tab'=>2,'returnUrl'=>$returnUrl])],
        ];
    }

    /**
     * FQC质检
     * @param OrderFqcForm $form
     * @return object
     * @throws
     */
    public function orderFqc($form)
    {
        $order = Order::findOne($form->order_id);
        if (!$order) {
            throw new \Exception('订单号：' . $form->order_sn . '未查到订单信息');
        }
        if ($order->distribute_status != DistributeStatusEnum::HAS_PEIHUO) {
            throw new \Exception('订单号：' . $form->order_sn . '不是已配货状态不能质检');
        }
        if (!$form->is_pass && $form->problem_type != ProblemTypeEnum::ORDER) {
            $order->distribute_status = DistributeStatusEnum::ALLOWED;
            //质检不通过，取消S单
            $bill = WarehouseBill::find()->where(['order_sn' => $form->order_sn])->one();
            if (!$bill) {
                throw new \Exception('订单号：' . $form->order_sn . '未查到S销售单');
            }
            $bill->bill_status = BillStatusEnum::CANCEL;
            if (false === $bill->save()) {
                throw new \Exception($this->getError($bill));
            }
            $billG = WarehouseBillGoods::find()->select(['goods_id'])->where(['bill_id' => $bill->id])->all();
            if (!$billG) {
                throw new \Exception('订单号：' . $form->order_sn . '对应销售单明细不能为空');
            }
            $goods_ids = ArrayHelper::getColumn($billG, 'goods_id');
            //还原库存信息
            $execute_num = WarehouseGoods::updateAll(['goods_status' => GoodsStatusEnum::IN_STOCK], ['goods_id' => $goods_ids, 'goods_status' => GoodsStatusEnum::IN_SALE]);
            if ($execute_num <> count($goods_ids)) {
                throw new \Exception("货品改变状态数量与明细数量不一致");
            }
        } else {
            if($form->problem_type == ProblemTypeEnum::ORDER){
                //$order->delivery_status = DeliveryStatusEnum::SAVE;
            }else{
                $order->delivery_status = DeliveryStatusEnum::TO_SEND;
            }
        }
        //更新订单信息
        if (false === $order->save()) {
            throw new \Exception($this->getError($order));
        }
        //创建质检日志
        $form->creator_id = \Yii::$app->user->identity->getId();
        $form->created_at = time();
        if (false === $form->save()) {
            throw new \Exception($this->getError($form));
        }
        //创建订单日志
        $log = [
            'order_id' => $order->id,
            'order_sn' => $order->order_sn,
            'order_status' => $order->order_status,
            'log_type' => LogTypeEnum::ARTIFICIAL,
            'log_time' => time(),
            'log_module' => 'FQC质检',
            'log_msg' => $form->is_pass ? "FQC质检通过" : "FQC质检不通过",
        ];
        \Yii::$app->salesService->orderLog->createOrderLog($log);

        return $order;
    }
}
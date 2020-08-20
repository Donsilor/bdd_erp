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


}
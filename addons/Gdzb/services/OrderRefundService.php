<?php

namespace addons\Gdzb\services;

use addons\Gdzb\common\models\Customer;

use addons\Gdzb\common\models\Goods;
use addons\Gdzb\common\models\OrderGoods;
use addons\Gdzb\common\models\OrderRefund;
use addons\Gdzb\common\models\RefundGoods;
use addons\Warehouse\common\enums\GoodsStatusEnum;
use common\enums\ConfirmEnum;
use common\enums\LogTypeEnum;
use common\helpers\SnHelper;

use Yii;
use common\components\Service;
use common\helpers\Url;



/**
 * Class SaleChannelService
 * @package services\common
 */
class OrderRefundService extends Service
{
    /**
     * 顾客订单菜单
     * @param int $order_id
     * @return array
     */
    public function menuTabList($refund_id, $returnUrl = null)
    {
        return [
            1=>['name'=>'退货单信息','url'=>Url::to(['order-refund/view','id'=>$refund_id,'tab'=>1,'returnUrl'=>$returnUrl])],
            2=>['name'=>'日志信息','url'=>Url::to(['refund-log/index','refund_id'=>$refund_id,'tab'=>2,'returnUrl'=>$returnUrl])],
        ];
    }

    /****
     * @param $model
     * @return Customer|array|null|void|\yii\db\ActiveRecord
     * @throws \Exception
     * 同步退货单
     */
    public function createSyncRefund($refund,$refund_goods){

        $refund_model = new OrderRefund();
        $refund_model->attributes = $refund;
        $refund_model->refund_sn = SnHelper::createReturnSn('T');
        $refund_model->created_at = time();
        $refund_model->creator_id = Yii::$app->user->identity->getId();
        if($refund_model->save() === false){
            throw new \Exception($this->getError($refund_model));
        }

        foreach ($refund_goods as $good){
            $refund_goods_model = new RefundGoods();
            $refund_goods_model->attributes = $good;
            $refund_goods_model->refund_id = $refund_model->id;
            if($refund_goods_model->save() === false){
                throw new \Exception($this->getError($refund_goods_model));
            }
        }
        return $refund_model->toArray();
    }

    public function syncAuditPass($model){
        //订单日志
        $log = [
            'order_id' => $model->order_id,
            'order_sn' => $model->order->order_sn ?? '',
            'order_status' => $model->order->order_status ?? '',
            'log_type' => LogTypeEnum::ARTIFICIAL,
            'log_time' => time(),
            'log_module' => '退货单审核',
            'log_msg' => "退货单审核通过",
        ];
        \Yii::$app->gdzbService->orderLog->createOrderLog($log);

        //更改商品为库存状态
        $refund_goods = RefundGoods::find()->where(['refund_id'=>$model->id])->select(['goods_sn'])->asArray()->all();
        $goods_sns = array_values(array_column($refund_goods,'goods_sn'));
        Goods::updateAll(['goods_status' => GoodsStatusEnum::IN_STOCK],['goods_sn'=>$goods_sns]);

        //重新统计
        Yii::$app->gdzbService->order->orderSummary($model->order_id);

    }

    public function syncAuditNoPass($model){
        //订单日志
        $log = [
            'order_id' => $model->order_id,
            'order_sn' => $model->order->order_sn ?? '',
            'order_status' => $model->order->order_status ?? '',
            'log_type' => LogTypeEnum::ARTIFICIAL,
            'log_time' => time(),
            'log_module' => '退货单审核',
            'log_msg' => "退货单{$model->refund_sn}审核不通过",
        ];
        \Yii::$app->gdzbService->orderLog->createOrderLog($log);

        //更改订单明细
        $refund_goods = RefundGoods::find()->where(['refund_id'=>$model->id])->select(['goods_sn'])->asArray()->all();
        $goods_sns = array_values(array_column($refund_goods,'goods_sn'));
        OrderGoods::updateAll(['is_return' => ConfirmEnum::NO, 'refund_price' => 0],['order_id'=>$model->order_id,'goods_sn'=>$goods_sns]);
        //重新统计
        Yii::$app->gdzbService->order->orderSummary($model->order_id);
    }




}
<?php

namespace addons\Sales\services;

use common\components\Service;

/**
 * Class Application
 *
 * @package addons\Sales\services
 * @property \addons\Sales\services\SaleChannelService $saleChannel 销售渠道
 * @property \addons\Sales\services\CustomerSourcesService $sources 客户来源
 * @property \addons\Sales\services\ExpressService $express 快递
 * @property \addons\Sales\services\CustomerService $customer 客户
 * @property \addons\Sales\services\PaymentService $payment 支付方式
 * @property \addons\Sales\services\OrderService $order 订单
 * @property \addons\Sales\services\DistributionOrderService $distribution 订单配货
 * @property \addons\Sales\services\OrderGoodsService $orderGoods 订单明细
 * @property \addons\Sales\services\OrderLogService $orderLog 订单日志
 * @property \addons\Sales\services\FqcConfigService $fqc FQC配置
 * @property \addons\Sales\services\ShippingService $shipping 订单发货
 * @property \addons\Sales\services\OrderFqcService $orderFqc 订单质检
 * @property \addons\Sales\services\ReturnService $return 订单退款
 */
class Application extends Service
{
    /**
     * @var array
     */
    public $childService = [
        'saleChannel' => 'addons\Sales\services\SaleChannelService',
        'sources' => 'addons\Sales\services\CustomerSourcesService',
        'express' => 'addons\Sales\services\ExpressService',
        'payment' => 'addons\Sales\services\PaymentService',
        'customer' => 'addons\Sales\services\CustomerService',
        'order' => 'addons\Sales\services\OrderService',
        'orderGoods' => 'addons\Sales\services\OrderGoodsService',
        'orderLog' => 'addons\Sales\services\OrderLogService',
        'distribution' => 'addons\Sales\services\DistributionOrderService',
        'fqc' => 'addons\Sales\services\FqcConfigService',
        'orderFqc' => 'addons\Sales\services\OrderFqcService',
        'shipping' => 'addons\Sales\services\ShippingService',
        'return' => 'addons\Sales\services\ReturnService',
    ];
}
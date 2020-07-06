<?php

namespace addons\Sales\services;

use common\components\Service;

/**
 * Class Application
 *
 * @package addons\Sales\services
 * @property \addons\Sales\services\SaleChannelService $saleChannel 销售渠道
 * @property \addons\Sales\services\ExpressService $express 快递
 * @property \addons\Sales\services\CustomerService $customer 客户
 * @property \addons\Sales\services\OrderService $order 订单
 * @property \addons\Sales\services\OrderGoodsService $orderGoods 订单明细
 * @property \addons\Sales\services\OrderLogService $orderLog 订单日志
 */
class Application extends Service
{
    /**
     * @var array
     */
    public $childService = [
            'saleChannel' => 'addons\Sales\services\SaleChannelService',
            'express' => 'addons\Sales\services\ExpressService',
            'customer' => 'addons\Sales\services\CustomerService',
            'order' => 'addons\Sales\services\OrderService',
            'orderGoods' => 'addons\Sales\services\OrderGoodsService',
            'orderLog' => 'addons\Sales\services\OrderLogService',        
            
            
    ];
}
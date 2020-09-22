<?php

namespace addons\Gdzb\services;

use common\components\Service;

/**
 * Class Application
 *
 * @package addons\Gdzb\services
 * @property \addons\Gdzb\services\OrderService $order 订单
 * @property \addons\Gdzb\services\OrderGoodsService $orderGoods 订单
 * @property \addons\Gdzb\services\OrderRefundService $orderRefund 退货单
 * @property \addons\Gdzb\services\SupplierService $supplier 供应商
 * @property \addons\Gdzb\services\CustomerService $customer 客户
 * @property \addons\Gdzb\services\OrderLogService $orderLog 订单日志
 * @property \addons\Gdzb\services\RefundLogService $refundLog 订单日志
 * @var array
 */
class Application extends Service
{
    
    public $childService = [
            /*********订单相关*********/
            'order' => 'addons\Gdzb\services\OrderService',            
            'orderGoods' => 'addons\Gdzb\services\OrderGoodsService',
            'orderRefund' => 'addons\Gdzb\services\OrderRefundService',
            'supplier' => 'addons\Gdzb\services\SupplierService',
            'customer' => 'addons\Gdzb\services\CustomerService',
            'orderLog' => 'addons\Gdzb\services\OrderLogService',
            'refundLog' => 'addons\Gdzb\services\RefundLogService',
    ];
}
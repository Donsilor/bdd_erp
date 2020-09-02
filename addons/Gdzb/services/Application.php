<?php

namespace addons\Gdzb\services;

use common\components\Service;

/**
 * Class Application
 *
 * @package addons\Gdzb\services
 * @property \addons\Gdzb\services\OrderService $order 订单
 * @property \addons\Gdzb\services\SupplierService $supplier 供应商
 * @property \addons\Gdzb\services\CustomerService $customer 客户
 * @var array
 */
class Application extends Service
{
    
    public $childService = [
            /*********订单相关*********/
            'order' => 'addons\Gdzb\services\OrderService',            
            'supplier' => 'addons\Gdzb\services\SupplierService',
            'customer' => 'addons\Gdzb\services\CustomerService',
    ];
}
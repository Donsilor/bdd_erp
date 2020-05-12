<?php

namespace addons\Supply\services;

use common\components\Service;

/**
 * Class Application
 *
 * @package addons\Supply\services
 * @property \addons\Supply\services\FactoryService $factory 工厂
 * @property \addons\Supply\services\ProduceService $produce 布产
 * @property \addons\Supply\services\ProduceLogService $produce_log 布产日志
 * @property \addons\Supply\services\SupplierService $supplier 供应商
 *
 */
class Application extends Service
{
    /**
     * @var array
     */
    public $childService = [       
        /*********供应商相关*********/
        'factory' => 'addons\Supply\services\FactoryService',
        'produce' => 'addons\Supply\services\ProduceService',
        'produce_log' => 'addons\Supply\services\ProduceLogService',
        'supplier' => 'addons\Supply\services\SupplierService',
    ];
}
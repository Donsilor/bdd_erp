<?php

namespace addons\Supply\services;

use common\components\Service;

/**
 * Class Application
 *
 * @package addons\Supply\services
 * @property \addons\Supply\services\FactoryService $factory 工厂
 * @property \addons\Supply\services\SupplierService $supplier 供应商
 * @property \addons\Supply\services\ProduceService $produce 布产单
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
        'supplier' => 'addons\Supply\services\SupplierService',
        'produce' => 'addons\Supply\services\ProduceService',
    ];
}
<?php

namespace addons\Supply\services;

use common\components\Service;

/**
 * Class Application
 *
 * @package addons\Supply\services
 * @property \addons\Supply\services\FactoryService $factory 工厂
 * @property \addons\Supply\services\ProduceService $produce 布产单
 * @property \addons\Supply\services\SupplierService $supplier 供应商
 * @property \addons\Supply\services\ProduceStoneService $produceStone 配石
 * @property \addons\Supply\services\ProduceGoldService $produceGold 配金
 * @property \addons\Supply\services\ProducePartsService $produceParts 配金
 */
class Application extends Service
{
    /**
     * @var array
     */
    public $childService = [       
        'supplier' => 'addons\Supply\services\SupplierService',
        'produce' => 'addons\Supply\services\ProduceService',
        'produceStone' => 'addons\Supply\services\ProduceStoneService',
        'produceGold' => 'addons\Supply\services\ProduceGoldService',
        'produceParts' => 'addons\Supply\services\ProducePartsService',
    ];
}
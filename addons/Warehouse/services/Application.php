<?php

namespace addons\Warehouse\services;

use common\components\Service;

/**
 * Class Application
 *
 * @package addons\Warehouse\services
 * @property \addons\Warehouse\services\WarehouseService $warehouse 仓库
 * @property \addons\Warehouse\services\WarehouseGoodsService $warehouseGoods 库存
 * @property \addons\Warehouse\services\WarehouseBillService $warehouseBill 单据（作废）
 * @property \addons\Warehouse\services\WarehouseBillService $bill   基础单据
 * @property \addons\Warehouse\services\WarehouseBillMService $billM 盘点单据
 * @property \addons\Warehouse\services\WarehouseBillWService $billW 调拨单据
 * 
 */
class Application extends Service
{
    /**
     * @var array
     */
    public $childService = [       
        /*********仓储相关*********/
		'warehouse' => 'addons\Warehouse\services\WarehouseService',
		'warehouseGoods' => 'addons\Warehouse\services\WarehouseGoodsService',
        'warehouseBill' => 'addons\Warehouse\services\WarehouseBillService',
        'bill' => 'addons\Warehouse\services\WarehouseBillService',
        'billW' => 'addons\Warehouse\services\WarehouseBillWService',
        'billM' => 'addons\Warehouse\services\WarehouseBillMService',
    ];
}
<?php

namespace addons\Warehouse\services;

use common\components\Service;

/**
 * Class Application
 *
 * @package addons\Warehouse\services
 * @property \addons\Warehouse\services\WarehouseService $warehouse 仓库
 * @property \addons\Warehouse\services\WarehouseGoodsService $warehouseGoods 商品列表
 * @property \addons\Warehouse\services\WarehouseBillService $warehouseBill 单据列表
 * @property \addons\Warehouse\services\WarehouseBillLService $warehouseLBill 收货单
 * @property \addons\Warehouse\services\WarehouseBillMService $warehouseMBill 调拨单
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
        'warehouseLBill' => 'addons\Warehouse\services\WarehouseBillLService',
        'warehouseMBill' => 'addons\Warehouse\services\WarehouseBillMService',
    ];
}
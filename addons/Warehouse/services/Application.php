<?php

namespace addons\Warehouse\services;

use common\components\Service;

/**
 * Class Application
 *
 * @package addons\Warehouse\services
 * @property \addons\Warehouse\services\WarehouseService $warehouse 仓库
 * @property \addons\Warehouse\services\WarehouseGoodsService $warehouseGoods 库存
 * @property \addons\Warehouse\services\WarehouseBillService $bill   基础单据
 * @property \addons\Warehouse\services\WarehouseBillLService $billL 收货单据
 * @property \addons\Warehouse\services\WarehouseBillMService $billM 盘点单据
 * @property \addons\Warehouse\services\WarehouseBillWService $billW 调拨单据
 * @property \addons\Warehouse\services\WarehouseBillBService $billB 退货返厂单据
 * @property \addons\Warehouse\services\WarehouseBillLogService $billLog 单据日志
 * @property \addons\Warehouse\services\WarehouseBillRepairService $repair 维修单据
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
        'bill' => 'addons\Warehouse\services\WarehouseBillService',
        'billL' => 'addons\Warehouse\services\WarehouseBillLService',
        'billW' => 'addons\Warehouse\services\WarehouseBillWService',
        'billM' => 'addons\Warehouse\services\WarehouseBillMService',
        'billB' => 'addons\Warehouse\services\WarehouseBillBService',
        'billLog' => 'addons\Warehouse\services\WarehouseBillLogService',
        'repair' => 'addons\Warehouse\services\WarehouseBillRepairService',
    ];
}
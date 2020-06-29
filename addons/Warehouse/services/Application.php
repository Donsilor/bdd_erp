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
 * @property \addons\Warehouse\services\WarehouseBillAService $billA 调拨单据
 * @property \addons\Warehouse\services\WarehouseBillTService $billT 其他收货单据
 * @property \addons\Warehouse\services\WarehouseBillCService $billC 其他出库单据
 * @property \addons\Warehouse\services\WarehouseBillBService $billB 退货返厂单据
 * @property \addons\Warehouse\services\WarehouseBillLogService $billLog 单据日志
 * @property \addons\Warehouse\services\WarehouseBillRepairService $repair 维修单据
 *
 * @property \addons\Warehouse\services\WarehouseGoldService $gold 金料库存
 * @property \addons\Warehouse\services\WarehouseGoldBillService $goldBill 金料单据
 * @property \addons\Warehouse\services\WarehouseGoldBillWService $goldBillW 金料盘点单
 *
 * @property \addons\Warehouse\services\WarehouseStoneService $stone 石包库存
 * @property \addons\Warehouse\services\WarehouseStoneBillService $stoneBill 石包单据
 * @property \addons\Warehouse\services\WarehouseStoneBillMsService $stoneMs 入库单(买石单)
 * @property \addons\Warehouse\services\WarehouseStoneBillSsService $stoneSs 领石单(送石单)
 * @property \addons\Warehouse\services\WarehouseStoneBillWService $stoneBillW 石料盘点单
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
        'billA' => 'addons\Warehouse\services\WarehouseBillAService',
        'billB' => 'addons\Warehouse\services\WarehouseBillBService',
        'billT' => 'addons\Warehouse\services\WarehouseBillTService',
        'billC' => 'addons\Warehouse\services\WarehouseBillCService',
        'billLog' => 'addons\Warehouse\services\WarehouseBillLogService',
        'repair' => 'addons\Warehouse\services\WarehouseBillRepairService',

        'gold' => 'addons\Warehouse\services\WarehouseGoldService',
        'goldBill' => 'addons\Warehouse\services\WarehouseGoldBillService',
        'goldBillW' => 'addons\Warehouse\services\WarehouseGoldBillWService',

        'stone' => 'addons\Warehouse\services\WarehouseStoneService',
        'stoneBill' => 'addons\Warehouse\services\WarehouseStoneBillService',
        'stoneSs' => 'addons\Warehouse\services\WarehouseStoneBillSsService',
        'stoneMs' => 'addons\Warehouse\services\WarehouseStoneBillMsService',
        'stoneBillW' => 'addons\Warehouse\services\WarehouseStoneBillWService',
    ];
}
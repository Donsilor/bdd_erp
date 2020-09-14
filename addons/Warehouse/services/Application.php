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
 * @property \addons\Warehouse\services\WarehouseBillAService $billA 调整单据
 * @property \addons\Warehouse\services\WarehouseBillJService $billJ 借货单据
 * @property \addons\Warehouse\services\WarehouseBillSService $billS 销售单据
 * @property \addons\Warehouse\services\WarehouseBillDService $billD 销售退货单据
 * @property \addons\Warehouse\services\WarehouseBillTService $billT 其它收货单据
 * @property \addons\Warehouse\services\WarehouseBillCService $billC 其它出库单据
 * @property \addons\Warehouse\services\WarehouseBillBService $billB 退货返厂单据
 * @property \addons\Warehouse\services\WarehouseBillLogService $billLog 单据日志
 * @property \addons\Warehouse\services\WarehouseGoodsLogService $goodsLog 单据日志
 * @property \addons\Warehouse\services\WarehouseBillRepairService $repair 维修单据
 * @property \addons\Warehouse\services\WarehouseGiftService $gift 赠品库存
 *
 * @property \addons\Warehouse\services\WarehouseGoldService $gold 金料库存
 * @property \addons\Warehouse\services\WarehouseGoldBillService $goldBill 金料单据
 * @property \addons\Warehouse\services\WarehouseGoldBillLService $goldL 入库单
 * @property \addons\Warehouse\services\WarehouseGoldBillCService $goldC 领料单
 * @property \addons\Warehouse\services\WarehouseGoldBillDService $goldD 退料单
 * @property \addons\Warehouse\services\WarehouseGoldBillWService $goldW 盘点单
 * *@property \addons\Warehouse\services\WarehouseGoldLogService $goldLog 单据日志
 *
 * @property \addons\Warehouse\services\WarehouseStoneService $stone 石包库存
 * @property \addons\Warehouse\services\WarehouseStoneBillService $stoneBill 石包单据
 * @property \addons\Warehouse\services\WarehouseStoneBillMsService $stoneMs 入库单(买石单)
 * @property \addons\Warehouse\services\WarehouseStoneBillSsService $stoneSs 领石单(送石单)
 * @property \addons\Warehouse\services\WarehouseStoneBillTsService $stoneTs 工厂退石单(退石单)
 * @property \addons\Warehouse\services\WarehouseStoneBillWService $stoneW 石料盘点单
 *
 * @property \addons\Warehouse\services\WarehousePartsService $parts 配件库存
 * @property \addons\Warehouse\services\WarehousePartsBillService $partsBill 配件单据
 * @property \addons\Warehouse\services\WarehousePartsBillLService $partsL 入库单
 * @property \addons\Warehouse\services\WarehousePartsBillCService $partsC 领件单
 * @property \addons\Warehouse\services\WarehousePartsBillDService $partsD 退件单
 * @property \addons\Warehouse\services\WarehousePartsBillWService $partsW 盘点单
 *
 * @property \addons\Warehouse\services\WarehouseTempletService $templet 样板库存
 * @property \addons\Warehouse\services\WarehouseTempletBillService $templetBill 样板单据
 * @property \addons\Warehouse\services\WarehouseTempletBillLService $templetL 入库单
 * @property \addons\Warehouse\services\WarehouseTempletBillCService $templetC 出库单
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
        'billJ' => 'addons\Warehouse\services\WarehouseBillJService',
        'billS' => 'addons\Warehouse\services\WarehouseBillSService',
        'billD' => 'addons\Warehouse\services\WarehouseBillDService',
        'billLog' => 'addons\Warehouse\services\WarehouseBillLogService',
        'goodsLog' => 'addons\Warehouse\services\WarehouseGoodsLogService',
        'repair' => 'addons\Warehouse\services\WarehouseBillRepairService',
        'gift' => 'addons\Warehouse\services\WarehouseGiftService',

        'gold' => 'addons\Warehouse\services\WarehouseGoldService',
        'goldBill' => 'addons\Warehouse\services\WarehouseGoldBillService',
        'goldL' => 'addons\Warehouse\services\WarehouseGoldBillLService',
        'goldC' => 'addons\Warehouse\services\WarehouseGoldBillCService',
        'goldD' => 'addons\Warehouse\services\WarehouseGoldBillDService',
        'goldW' => 'addons\Warehouse\services\WarehouseGoldBillWService',
        'goldLog' => 'addons\Warehouse\services\WarehouseGoldLogService',

        'stone' => 'addons\Warehouse\services\WarehouseStoneService',
        'stoneBill' => 'addons\Warehouse\services\WarehouseStoneBillService',
        'stoneSs' => 'addons\Warehouse\services\WarehouseStoneBillSsService',
        'stoneMs' => 'addons\Warehouse\services\WarehouseStoneBillMsService',
        'stoneTs' => 'addons\Warehouse\services\WarehouseStoneBillTsService',
        'stoneW' => 'addons\Warehouse\services\WarehouseStoneBillWService',

        'parts' => 'addons\Warehouse\services\WarehousePartsService',
        'partsBill' => 'addons\Warehouse\services\WarehousePartsBillService',
        'partsL' => 'addons\Warehouse\services\WarehousePartsBillLService',
        'partsC' => 'addons\Warehouse\services\WarehousePartsBillCService',
        'partsD' => 'addons\Warehouse\services\WarehousePartsBillDService',
        'partsW' => 'addons\Warehouse\services\WarehousePartsBillWService',

        'templet' => 'addons\Warehouse\services\WarehouseTempletService',
        'templetBill' => 'addons\Warehouse\services\WarehouseTempletBillService',
        'templetL' => 'addons\Warehouse\services\WarehouseTempletBillLService',
        'templetC' => 'addons\Warehouse\services\WarehouseTempletBillCService',
    ];
}
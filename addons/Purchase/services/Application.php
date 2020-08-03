<?php

namespace addons\Purchase\services;

use common\components\Service;

/**
 * Class Application
 *
 * @package addons\Purchase\services
 * @property \addons\Purchase\services\PurchaseService $purchase 采购订单
 * @property \addons\Purchase\services\PurchaseGoodsService $purchaseGoods 采购明细
 * @property \addons\Purchase\services\PurchaseLogService $purchaseLog 采购订单日志
 * @property \addons\Purchase\services\PurchaseApplyService $apply 采购申请单
 * @property \addons\Purchase\services\ApplyLogService $applyLog 采购申请单日志
 * @property \addons\Purchase\services\PurchaseApplyGoodsService $applyGoods 采购申请单
 * @property \addons\Purchase\services\PurchaseGoldService $gold 金料采购订单
 * @property \addons\Purchase\services\PurchaseStoneService $stone 石料采购订单
 * @property \addons\Purchase\services\PurchasePartsService $parts 配件采购订单
 * @property \addons\Purchase\services\PurchaseGiftService $gift 赠品采购订单
 * @property \addons\Purchase\services\PurchaseReceiptService $receipt 采购收货单
 * @property \addons\Purchase\services\ReceiptLogService $receiptLog 采购收货单日志
 * @property \addons\Purchase\services\PurchaseDefectiveService $defective 不良返厂单
 * @property \addons\Purchase\services\DefectiveLogService $defectiveLog 不良返厂单日志
 * @property \addons\Purchase\services\PurchaseFqcService $fqc 质检未过原因
 * @var array
 */
class Application extends Service
{

    public $childService = [
        /*********采购单相关*********/
        'purchase' => 'addons\Purchase\services\PurchaseService',
        'purchaseGoods' => 'addons\Purchase\services\PurchaseGoodsService',
        'purchaseLog'=>'addons\Purchase\services\PurchaseLogService',
        'apply' => 'addons\Purchase\services\PurchaseApplyService',
        'applyGoods' => 'addons\Purchase\services\PurchaseApplyGoodsService',
        'applyLog' => 'addons\Purchase\services\ApplyLogService',
        'gold' => 'addons\Purchase\services\PurchaseGoldService',
        'stone' => 'addons\Purchase\services\PurchaseStoneService',
        'parts' => 'addons\Purchase\services\PurchasePartsService',
        'gift' => 'addons\Purchase\services\PurchaseGiftService',

        'receipt' => 'addons\Purchase\services\PurchaseReceiptService',
        'receiptLog' => 'addons\Purchase\services\ReceiptLogService',

        'defective' => 'addons\Purchase\services\PurchaseDefectiveService',
        'defectiveLog' => 'addons\Purchase\services\DefectiveLogService',

        'fqc' => 'addons\Purchase\services\PurchaseFqcService',
    ];
}
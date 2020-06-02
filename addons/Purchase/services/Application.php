<?php

namespace addons\Purchase\services;

use common\components\Service;

/**
 * Class Application
 * 
 * @package addons\Purchase\services
 * @property \addons\Purchase\services\PurchaseService $purchase 采购订单
 * @property \addons\Purchase\services\PurchaseGoodsService $purchaseGoods 采购明细
 * @property \addons\Purchase\services\PurchaseGoldService $gold 金料采购订单
 * @property \addons\Purchase\services\PurchaseGoldGoodsService $goldGoods 金料采购明细
 * @property \addons\Purchase\services\PurchaseStoneService $stone 石料采购订单
 * @property \addons\Purchase\services\PurchaseStoneGoodsService $stoneGoods 石料采购明细
 * @property \addons\Purchase\services\PurchaseReceiptService $receipt 采购收货单
 * @property \addons\Purchase\services\PurchaseDefectiveService $fefective 不良返厂单
 * @property \addons\Purchase\services\PurchaseFqcConfigService $fqc 质检未过原因
 * 
 * @property \addons\Purchase\services\PurchaseReceiptService $purchaseReceipt 采购收货单（作废）
 * @property \addons\Purchase\services\PurchaseDefectiveService $purchaseDefective 不良返厂单（作废）
 * @var array
 */
class Application extends Service
{

    public $childService = [
            /*********采购单相关*********/
            'purchase' => 'addons\Purchase\services\PurchaseService',
            'purchaseGoods' => 'addons\Purchase\services\PurchaseGoodsService',
            'gold' => 'addons\Purchase\services\PurchaseGoldService',
            'goldGoods' => 'addons\Purchase\services\PurchaseGoldGoodsService',
            'stone' => 'addons\Purchase\services\PurchaseStoneService',
            'stoneGoods' => 'addons\Purchase\services\PurchaseStoneGoodsService',
            'receipt' => 'addons\Purchase\services\PurchaseReceiptService',
            'defective' => 'addons\Purchase\services\PurchaseDefectiveService',
            
            'purchaseReceipt' => 'addons\Purchase\services\PurchaseReceiptService',//（作废）
            'purchaseDefective' => 'addons\Purchase\services\PurchaseDefectiveService',//（作废）
            'fqc' => 'addons\Purchase\services\PurchaseFqcConfigService',
    ];
}
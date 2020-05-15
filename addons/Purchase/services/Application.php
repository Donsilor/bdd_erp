<?php

namespace addons\Purchase\services;

use common\components\Service;

/**
 * Class Application
 * 
 * @package addons\Purchase\services
 * @property \addons\Purchase\services\PurchaseService $purchase 采购订单
 * @property \addons\Purchase\services\PurchaseGoodsService $purchaseGoods 采购明细
 * @property \addons\Purchase\services\purchaseReceiptService $purchaseReceipt 采购收货单
 * @property \addons\Purchase\services\purchaseDefectiveService $purchaseDefective 采购收货单
 * @property \addons\Purchase\services\purchaseFqcConfigService $purchaseFqcConfig 质检未过原因
 * @var array
 */
class Application extends Service
{

    public $childService = [
            /*********采购单相关*********/
            'purchase' => 'addons\Purchase\services\PurchaseService',
            'purchaseGoods' => 'addons\Purchase\services\PurchaseGoodsService',
            'purchaseReceipt' => 'addons\Purchase\services\PurchaseReceiptService',
            'purchaseDefective' => 'addons\Purchase\services\purchaseDefectiveService',
            'purchaseFqcConfig' => 'addons\Purchase\services\purchaseFqcConfigService',
    ];
}
<?php

namespace addons\Purchase\services;

use common\components\Service;

/**
 * Class Application
 * 
 * @package addons\Purchase\services
 * @property \addons\Purchase\services\PurchaseService $purchase 采购订单
 * @property \addons\Purchase\services\PurchaseApplyService $apply 采购申请单
 * @property \addons\Purchase\services\PurchaseApplyGoodsService $applyGoods 采购申请单
 * @property \addons\Purchase\services\PurchaseGoodsService $purchaseGoods 采购明细
 * @property \addons\Purchase\services\PurchaseGoldService $gold 金料采购订单
 * @property \addons\Purchase\services\PurchaseGoldGoodsService $goldGoods 金料采购明细
 * @property \addons\Purchase\services\PurchaseStoneService $stone 石料采购订单
 * @property \addons\Purchase\services\PurchaseStoneGoodsService $stoneGoods 石料采购明细
 * @property \addons\Purchase\services\PurchaseReceiptService $receipt 采购收货单
 * @property \addons\Purchase\services\PurchaseDefectiveService $defective 不良返厂单
 * @property \addons\Purchase\services\PurchaseFqcService $fqc 质检未过原因
 * @var array
 */
class Application extends Service
{

    public $childService = [
            /*********采购单相关*********/
            'purchase' => 'addons\Purchase\services\PurchaseService',
            'apply' => 'addons\Purchase\services\PurchaseApplyService',
            'applyGoods' => 'addons\Purchase\services\PurchaseApplyGoodsService',
            'purchaseGoods' => 'addons\Purchase\services\PurchaseGoodsService',
            'gold' => 'addons\Purchase\services\PurchaseGoldService',
            'goldGoods' => 'addons\Purchase\services\PurchaseGoldGoodsService',
            'stone' => 'addons\Purchase\services\PurchaseStoneService',
            'stoneGoods' => 'addons\Purchase\services\PurchaseStoneGoodsService',
            'receipt' => 'addons\Purchase\services\PurchaseReceiptService',
            'defective' => 'addons\Purchase\services\PurchaseDefectiveService',
            'fqc' => 'addons\Purchase\services\PurchaseFqcService',
    ];
}
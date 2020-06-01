<?php

namespace addons\Purchase\backend\controllers;

use Yii;
use addons\Purchase\common\enums\PurchaseTypeEnum;
use addons\Purchase\common\forms\PurchaseGoldGoodsForm;
/**
 * 
 * 物料商品
 * Class MaterialGoodsController
 * @package backend\modules\goods\controllers
 */
class PurchaseGoldGoodsController extends PurchaseGoodsController
{
    /**
     * @var PurchaseGoodsForm
     */
    public $modelClass = PurchaseGoldGoodsForm::class;
    
   /**
     * @var int
     */
    public $purchaseType = PurchaseTypeEnum::MATERIAL_GOLD;
    
    
}

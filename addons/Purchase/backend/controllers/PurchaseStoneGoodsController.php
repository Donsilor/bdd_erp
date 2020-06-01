<?php

namespace addons\Purchase\backend\controllers;

use Yii;
use addons\Purchase\common\enums\PurchaseTypeEnum;
use addons\Purchase\common\forms\PurchaseStoneGoodsForm;
/**
 * 
 * 物料商品
 * Class MaterialGoodsController
 * @package backend\modules\goods\controllers
 */
class PurchaseStoneGoodsController extends PurchaseGoodsController
{
    /**
     * @var PurchaseGoodsForm
     */
    public $modelClass = PurchaseStoneGoodsForm::class;
    
   /**
     * @var int
     */
    public $purchaseType = PurchaseTypeEnum::MATERIAL_STONE;
    
    
}

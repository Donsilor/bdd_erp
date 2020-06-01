<?php

namespace addons\Purchase\backend\controllers;

use Yii;
use addons\Purchase\common\enums\PurchaseTypeEnum;
/**
 * 
 * 物料商品
 * Class MaterialGoodsController
 * @package backend\modules\goods\controllers
 */
class PurchaseStoneGoodsController extends PurchaseGoodsController
{
   /**
     * @var int
     */
    public $purchaseType = PurchaseTypeEnum::MATERIAL_GOLD;
    
    
}

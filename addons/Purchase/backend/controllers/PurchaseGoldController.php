<?php

namespace addons\Purchase\backend\controllers;

use Yii;
use addons\Purchase\common\enums\PurchaseTypeEnum;
/**
 *
 *
 * Class MaterialController
 * @package backend\modules\goods\controllers
 */
class PurchaseGoldController extends PurchaseController
{  
    /**
     * @var int
     */
    public $purchaseType = PurchaseTypeEnum::MATERIAL_GOLD;
    
        
}

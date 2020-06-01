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
class MaterialStoneController extends PurchaseController
{  
    /**
     * @var int
     */
    public $purchaseType = PurchaseTypeEnum::MATERIAL_STONE;
    
        
}

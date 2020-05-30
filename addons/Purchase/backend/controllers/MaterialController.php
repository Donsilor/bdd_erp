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
class MaterialController extends PurchaseController
{  
    /**
     * @var int
     */
    public $purchaseType = PurchaseTypeEnum::MATERIAL;
    
        
}

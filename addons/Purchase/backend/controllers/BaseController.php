<?php

namespace addons\Purchase\backend\controllers;

use Yii;
use common\controllers\AddonsController;

/**
 * 默认控制器
 *
 * Class DefaultController
 * @package addons\Purchase\backend\controllers
 */
class BaseController extends AddonsController
{
    /**
    * @var string
    */
    // public $layout = "@addons/Purchase/backend/views/layouts/main";
    /**
     * @var string
     */
    public $layout = "@backend/views/layouts/main";
    
    /**
     * 视图文件前缀
     *
     * @var string
     */
    protected $viewPrefix = '@backend/modules/common/views/';
    
    
}
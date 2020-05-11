<?php

namespace addons\Supply\backend\controllers;

use Yii;
use common\controllers\AddonsController;

/**
 * 默认控制器
 *
 * Class DefaultController
 * @package addons\Supply\backend\controllers
 */
class BaseController extends AddonsController
{
    /**
    * @var string
    */
    // public $layout = "@addons/Supply/backend/views/layouts/main";
    public $layout = "@backend/views/layouts/main";
}
<?php

namespace addons\Finance\backend\controllers;

use Yii;
use common\controllers\AddonsController;

/**
 * 默认控制器
 *
 * Class DefaultController
 * @package addons\Finance\backend\controllers
 */
class BaseController extends AddonsController
{
    /**
    * @var string
    */
    // public $layout = "@addons/Finance/backend/views/layouts/main";
    public $layout = "@backend/views/layouts/main";

    /**
     * 视图文件前缀
     *
     * @var string
     */
    protected $viewPrefix = '@backend/modules/common/views/';
}
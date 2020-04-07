<?php

namespace addons\Style\backend\controllers;

use Yii;
use addons\Style\common\models\Attribute;

/**
 * 默认控制器
 *
 * Class DefaultController
 * @package addons\Style\backend\controllers
 */
class DefaultController extends BaseController
{
    /**
    * 首页
    *
    * @return string
    */
    public function actionIndex()
    {        
        return $this->render('index',[

        ]);
    }
}
<?php

namespace addons\Style\backend\controllers;

use Yii;
use addons\style\common\models\Attribute;

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
        echo '1111111111';
        echo Attribute::fullTableName();
        
        return $this->render('index',[

        ]);
    }
}
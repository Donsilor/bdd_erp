<?php

namespace addons\Gdzb\api\modules\v1\controllers;

use addons\Gdzb\common\models\Special;
use api\controllers\OnAuthController;
use Yii;

/**
 * 默认控制器
 *
 * Class DefaultController
 * @package addons\Gdzb\frontend\controllers
 */
class SpecialController extends OnAuthController
{
    protected $authOptional = ['details'];

    public $modelClass = Special::class;

    /**
    * 首页
    *
    * @return string
    */
    public function actionDetails()
    {
        $url = Yii::$app->request->get('url', null);

        $where = [];
        $where['url'] = $url;
        $where['status'] = 1;

        $result = Special::findOne($where);

        return $result;
    }
}
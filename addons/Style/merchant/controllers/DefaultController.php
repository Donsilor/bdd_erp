<?php

namespace addons\Style\merchant\controllers;

use Yii;
use common\controllers\AddonsController;
use addons\style\common\models\Attribute;
use addons\style\common\models\AttributeLang;

/**
 * 默认控制器
 *
 * Class DefaultController
 * @package addons\Style\merchant\controllers
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
        //echo AttributeLang::fullTableName();exit;
        $res = Attribute::find()->alias('a')->innerJoin(AttributeLang::tableName().' lang','a.id=lang.master_id2')->all();
        print_r($res);
        return $this->render('index',[

        ]);
    }
}
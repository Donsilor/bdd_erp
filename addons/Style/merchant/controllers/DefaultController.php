<?php

namespace addons\Style\merchant\controllers;

use Yii;
use common\controllers\AddonsController;
use addons\Style\common\models\Attribute;
use addons\Style\common\models\AttributeLang;
use common\models\common\SmsLog;
use yii\db\Transaction;
use common\helpers\TransactionHelper;

/**
 * 默认控制器
 *
 * Class DefaultController
 * @package addons\Style\merchant\controllers
 */
class DefaultController extends BaseController
{

    public function init()
    {
        $this->noAuthOptional = ['index'];
    }
    /**
    * 首页
    *
    * @return string
    */
    public function actionIndex()
    {
        
        $trans = \Yii::$app->transaction->beginTransaction();
        $smsLog = SmsLog::find()->where(['id'=>1])->one();
        $smsLog->mobile = time();
        $res = $smsLog->save(false);
        var_dump($res);
        $attribute = Attribute::find()->where(['id'=>2])->one();
        $attribute->code = rand(10000,20000);
        $res = $attribute->save(false);
        var_dump($res);
        echo $smsLog->mobile.'---'.$attribute->code.'<br/>';
        $trans->commit();
        echo 'merchant:'.\Yii::$app->controller->route;
        exit;
        
    }
}
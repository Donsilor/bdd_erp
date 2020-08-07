<?php

namespace console\controllers;

use yii\console\Controller;
use yii\helpers\Console;
use common\models\common\Goldprice;
use common\enums\StatusEnum;


/**
 * 金价/汇率处理
 * Class StyleController
 * @package console\controllers
 */
class GoldToolController extends Controller
{
    /**
     * 更新金价
     * @param string $params
     */
    public function actionUpdateGoldPrice()
    {
        console::output("UpdateGoldPrice BEGIN");
        $models = GoldPrice::find()->where(['status'=>StatusEnum::ENABLED])->all();
        foreach ($models as $model) {
            $model->usd_price = \Yii::$app->goldTool->getGoldUsdPrice($model->code);
            $model->price = \Yii::$app->goldTool->getGoldRmbPrice($model->code,-15.2);
            $model->rmb_rate = \Yii::$app->goldTool->getExchangeRate('USDCNY');
            $model->api_time = time();
            if($model->usd_price && $model->price) {
                 if(false === $model->save()) {
                     console::output("[ERROR] Code: {$model->code} updated failed");
                     continue;
                 }
                 console::output("[SUCCESS] CODE:".$model->code .' , PRICE:'.$model->price.' , USD PRICE:'.$model->usd_price.' , RMB RATE:'.$model->rmb_rate);
            }else{
                 console::output("[ERROR] CODE:".$model->code .' , PRICE:'.$model->price.' , USD PRICE:'.$model->usd_price.' , RMB RATE:'.$model->rmb_rate);
            }
            
        }
        console::output("UpdateGoldPrice END");
    }
    
    /**
     * 更新货币汇率
     * @param string $params
     */
    public function actionUpdateCurrency()
    {
        
    }
}
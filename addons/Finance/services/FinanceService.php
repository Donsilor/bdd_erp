<?php

namespace addons\Finance\services;

use common\components\Service;

/**
 * Class FinanceService
 * @package services\common
 * @author jianyan74 <751393839@qq.com>
 */
class FinanceService extends Service
{

    /**
     * 创建采购单日志
     * @return array
     */
    public function createFinanceLog($log){

        $model = new FinanceLog();
        $model->attributes = $log;
        $model->log_time = time();
        $model->creator_id = \Yii::$app->user->id;
        $model->creator = \Yii::$app->user->identity->username;
        if(false === $model->save()){
            throw new \Exception($this->getError($model));
        }
        return $model ;
    }


 
}
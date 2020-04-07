<?php
/**
 * Created by PhpStorm.
 * User: BDD
 * Date: 2019/12/7
 * Time: 13:53
 */

namespace addons\Style\services;


use common\enums\StatusEnum;
use common\helpers\ArrayHelper;
use addons\Style\common\models\DiamondSource;

class DiamondSourceService
{
    public function getDropDown(){
        $model = DiamondSource::find()
            ->where(['status' => StatusEnum::ENABLED])
            ->select(['id','name'])
            ->asArray()
            ->all();

        return ArrayHelper::map($model,'id', 'name');
    }


}
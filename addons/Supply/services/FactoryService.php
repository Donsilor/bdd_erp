<?php
/**
 * Created by PhpStorm.
 * User: BDD
 * Date: 2019/12/7
 * Time: 13:53
 */

namespace addons\Supply\services;

use addons\Supply\common\models\Factory;
use common\enums\StatusEnum;
use common\helpers\ArrayHelper;


class FactoryService
{
    /**
     * 下拉
     * @return array
     */
    public function getDropDown(){

        $model = Factory::find()
            ->where(['status' => StatusEnum::ENABLED])
            ->select(['id','factory_name'])
            ->asArray()
            ->all();

        return ArrayHelper::map($model,'id', 'factory_name');
    }


}
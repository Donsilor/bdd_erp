<?php
/**
 * Created by PhpStorm.
 * User: BDD
 * Date: 2019/12/7
 * Time: 13:53
 */

namespace addons\Sales\services;

use common\enums\StatusEnum;
use common\helpers\ArrayHelper;
use addons\Sales\common\models\Express;

class ExpressService
{
    /**
     * 下拉
     * @return array
     */
    public function getDropDown(){
        $model = Express::find()
            ->where(['status' => StatusEnum::ENABLED])
            ->select(['id','name'])
            ->orderBy('sort asc')
            ->asArray()
            ->all();

        return ArrayHelper::map($model,'id', 'name');
    }


}
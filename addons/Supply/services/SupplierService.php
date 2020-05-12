<?php
/**
 * Created by PhpStorm.
 * User: BDD
 * Date: 2019/12/7
 * Time: 13:53
 */

namespace addons\Supply\services;

use addons\Supply\common\models\Factory;
use addons\Supply\common\models\Supplier;
use addons\Supply\common\models\SupplierFollower;
use common\enums\StatusEnum;
use common\helpers\ArrayHelper;


class SupplierService
{
    /**
     * 下拉
     * @return array
     */
    public function getDropDown(){

        $model = Supplier::find()
            ->where(['status' => StatusEnum::ENABLED])
            ->select(['id','supplier_name'])
            ->asArray()
            ->all();

        return ArrayHelper::map($model,'id', 'supplier_name');
    }




}
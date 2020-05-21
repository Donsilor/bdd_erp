<?php

namespace addons\Warehouse\services;

use addons\Warehouse\common\models\Warehouse;
use common\components\Service;
use common\enums\StatusEnum;
use common\helpers\ArrayHelper;


/**
 * Class TypeService
 * @package services\common
 * @author jianyan74 <751393839@qq.com>
 */
class WarehouseService extends Service
{


    /**
     * 编辑获取下拉
     *
     * @param string $id
     * @return array
     */
    public static function getDropDownForEdit(){
        $data = self::getDropDown();
        return ArrayHelper::merge([0 => '顶级分类'], $data);

    }
    /**
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getDropDown()
    {
        $models = Warehouse::find()
            ->where(['=', 'status', StatusEnum::ENABLED])
            ->select(['id', 'name'])
            ->orderBy('sort asc')
            ->asArray()
            ->all();
        return ArrayHelper::map($models,'id','name');
    }

}
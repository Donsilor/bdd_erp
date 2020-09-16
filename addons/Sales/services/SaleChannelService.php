<?php

namespace addons\Sales\services;

use Yii;
use common\components\Service;
use addons\Sales\common\models\SaleChannel;
use common\enums\StatusEnum;
use common\helpers\ArrayHelper;

/**
 * Class SaleChannelService
 * @package services\common
 */
class SaleChannelService extends Service
{
    /**
     *  销售渠道下拉
     * @return array
     */
    public static function getDropDown()
    {        
        $models = SaleChannel::find()
            ->where(['=', 'status', StatusEnum::ENABLED])
            ->select(['id', 'name'])
            ->orderBy('sort asc')
            ->asArray()
            ->all();
        
        return ArrayHelper::map($models, 'id', 'name');        
    }
    /**
     * 外部平台销售渠道
     * @return array
     */
    public static function getDropDownForExternalOrder()
    {
        $models = SaleChannel::find()
            ->where(['id'=>[7,8,13]])
            ->select(['id', 'name'])
            ->orderBy('sort asc')
            ->asArray()
            ->all();        
        return ArrayHelper::map($models, 'id', 'name'); 
    }
    
}
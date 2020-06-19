<?php

namespace addons\Style\services;

use Yii;
use common\components\Service;
use addons\Style\common\models\StyleStoneStyle;
use common\enums\StatusEnum;
use common\helpers\ArrayHelper;

/**
 * Class StoneStyleService
 * @package addons\Style\services
 * @author jianyan74 <751393839@qq.com>
 */
class StoneStyleService extends Service
{
    /**
     * @param int $stone_type;
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getDropDown($stone_type = null)
    {
        $model = StyleStoneStyle::find()
            ->where(['=', 'status', StatusEnum::ENABLED])
            ->andFilterWhere(['=', 'stone_type', $stone_type])
            ->select(['id', 'style_sn'])
            ->orderBy('sort asc')
            ->asArray()
            ->all();
        return ArrayHelper::map($model,'style_sn', 'style_sn');

    }
}
<?php

namespace addons\Style\services;

use Yii;
use common\components\Service;
use addons\Style\common\models\StyleGoldStyle;
use common\enums\StatusEnum;
use common\helpers\ArrayHelper;

/**
 * Class GoldStyleService
 * @package addons\Style\services
 * @author jianyan74 <751393839@qq.com>
 */
class GoldStyleService extends Service
{
    /**
     * @param int $gold_type;
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getDropDown($gold_type = null)
    {
        $model = StyleGoldStyle::find()
            ->where(['=', 'status', StatusEnum::ENABLED])
            ->andFilterWhere(['=', 'gold_type', $gold_type])
            ->select(['id', 'style_sn'])
            ->orderBy('sort asc')
            ->asArray()
            ->all();
        return ArrayHelper::map($model,'style_sn', 'style_sn');

    }
}
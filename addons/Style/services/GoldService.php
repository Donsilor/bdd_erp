<?php

namespace addons\Style\services;

use common\helpers\Url;
use Yii;
use common\components\Service;
use addons\Style\common\models\GoldStyle;
use common\enums\StatusEnum;
use common\helpers\ArrayHelper;

/**
 * Class GoldService
 * @package addons\Style\services
 * @author jianyan74 <751393839@qq.com>
 */
class GoldService extends Service
{
    /**
     * 金料款式 tab
     * @param int $id 款式ID
     * @param string $returnUrl
     * @return array
     */
    public function menuTabList($id, $returnUrl = null)
    {
        return [
            1=>['name'=>'金料款式详情','url'=>Url::to(['view','id'=>$id,'tab'=>1,'returnUrl'=>$returnUrl])],
        ];
    }

    /**
     * @param int $gold_type;
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getDropDown($gold_type = null)
    {
        $model = GoldStyle::find()
            ->where(['=', 'status', StatusEnum::ENABLED])
            ->andFilterWhere(['=', 'gold_type', $gold_type])
            ->select(['id', 'style_sn'])
            ->orderBy('sort asc')
            ->asArray()
            ->all();
        return ArrayHelper::map($model,'style_sn', 'style_sn');

    }
}
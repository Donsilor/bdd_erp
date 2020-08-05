<?php

namespace addons\Style\services;

use common\helpers\Url;
use Yii;
use common\components\Service;
use addons\Style\common\models\StoneStyle;
use common\enums\StatusEnum;
use common\helpers\ArrayHelper;

/**
 * Class StoneService
 * @package addons\Style\services
 * @author jianyan74 <751393839@qq.com>
 */
class StoneService extends Service
{

    /**
     * 石料款式 tab
     * @param int $id 款式ID
     * @param string $returnUrl
     * @return array
     */
    public function menuTabList($id, $returnUrl = null)
    {
        return [
            1=>['name'=>'石料款式详情','url'=>Url::to(['view','id'=>$id,'tab'=>1,'returnUrl'=>$returnUrl])],
        ];
    }

    /**
     * @param int $stone_type;
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getDropDown($stone_type = null)
    {
        $model = StoneStyle::find()
            ->where(['=', 'status', StatusEnum::ENABLED])
            ->andFilterWhere(['=', 'stone_type', $stone_type])
            ->select(['id', 'style_sn'])
            ->orderBy('sort asc')
            ->asArray()
            ->all();
        return ArrayHelper::map($model,'style_sn', 'style_sn');

    }
}
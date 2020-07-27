<?php

namespace addons\Style\services;

use Yii;
use common\helpers\Url;
use common\components\Service;
use addons\Style\common\models\PartsStyle;
use common\enums\StatusEnum;
use common\helpers\ArrayHelper;

/**
 * Class PartsService
 * @package addons\Style\services
 * @author jianyan74 <751393839@qq.com>
 */
class PartsService extends Service
{
    /**
     * 配件款式 tab
     * @param int $id 款式ID
     * @param string $returnUrl
     * @return array
     */
    public function menuTabList($id, $returnUrl = null)
    {
        return [
            1=>['name'=>'配件款式详情','url'=>Url::to(['view','id'=>$id,'tab'=>1,'returnUrl'=>$returnUrl])],
        ];
    }

    /**
     * @param int $parts_type;
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getDropDown($parts_type = null)
    {
        $model = PartsStyle::find()
            ->where(['=', 'status', StatusEnum::ENABLED])
            ->andFilterWhere(['=', 'parts_type', $parts_type])
            ->select(['id', 'style_sn'])
            ->orderBy('sort asc')
            ->asArray()
            ->all();
        return ArrayHelper::map($model,'style_sn', 'style_sn');

    }
}
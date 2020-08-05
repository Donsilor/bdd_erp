<?php

namespace addons\Style\services;

use Yii;
use common\components\Service;
use addons\Style\common\models\StyleGift;
use common\enums\StatusEnum;
use common\helpers\ArrayHelper;
use common\helpers\Url;

/**
 * Class GoldService
 * @package addons\Style\services
 * @author jianyan74 <751393839@qq.com>
 */
class GiftService extends Service
{
    /**
     * 赠品列表 tab
     * @param int $id 款式ID
     * @param string $returnUrl
     * @return array
     */
    public function menuTabList($id, $returnUrl = null)
    {
        return [
            1=>['name'=>'赠品详情','url'=>Url::to(['view','id'=>$id,'tab'=>1,'returnUrl'=>$returnUrl])],
        ];
    }

    /**
     * @param int $channel_id;
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getDropDown($channel_id = null)
    {
        $model = StyleGift::find()
            ->where(['=', 'status', StatusEnum::ENABLED])
            ->andFilterWhere(['=', 'channel_id', $channel_id])
            ->select(['id', 'style_sn'])
            ->orderBy('sort asc')
            ->asArray()
            ->all();
        return ArrayHelper::map($model,'style_sn', 'style_sn');

    }
}
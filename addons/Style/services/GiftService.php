<?php

namespace addons\Style\services;

use addons\Style\common\enums\StyleSexEnum;
use addons\Style\common\models\Style;
use common\enums\AutoSnEnum;
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

    /**
     * 创建赠品款式编号
     * @param StyleGift $model
     * @param bool $save
     * @throws
     * @return string
     */
    public static function createStyleSn($model, $save = true)
    {
        if(!$model->id) {
            throw new \Exception("编款失败：ID不能为空");
        }
        $channel_tag = '';
//        $channel_tag = $model->channel->tag ?? '';
//        if(empty($channel_tag)) {
//            throw new \Exception("编款失败：渠道未配置编码规则");
//        }
        //1.渠道部门代号
        $prefix   = $channel_tag;
        //2.款式分类
        $cate_tag = $model->cate->tag ?? '00';
        $cate_tag_list = explode("-", $cate_tag);
        if(count($cate_tag_list) < 2 ) {
            throw new \Exception("编款失败：款式分类未配置编码规则");
        }
        list($cate_m, $cate_w) = $cate_tag_list;
        if($model->style_sex == StyleSexEnum::MAN) {
            $prefix .= $cate_m;
        }else {
            $prefix .= $cate_w;
        }
        //3.中间部分
        $middle = str_pad($model->id,6,'0',STR_PAD_LEFT);
        //4.结尾部分-金属材质
        $last = '';
//        $last = $model->material_type;
        $model->style_sn = $prefix.$middle.$last;
        if($save === true) {
            $result = $model->save(true,['id','style_sn']);
            if($result === false){
                throw new \Exception("编款失败：保存款号失败");
            }
        }
        return $model->style_sn;
    }
}
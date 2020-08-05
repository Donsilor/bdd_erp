<?php

namespace addons\Style\services;

use addons\Style\common\models\StyleImages;
use common\enums\StatusEnum;
use Yii;
use common\components\Service;
use addons\Style\common\models\Style;
use common\helpers\Url;
use addons\Style\common\models\StyleAttribute;
use common\helpers\SnHelper;
use addons\Style\common\enums\StyleSexEnum;
use addons\Style\common\enums\StyleMaterialEnum;
use common\enums\AutoSnEnum;

/**
 * Class TypeService
 * @package services\common
 * @author jianyan74 <751393839@qq.com>
 */
class StyleService extends Service
{
    
    /**
     * 款式编辑 tab
     * @param int $id 款式ID
     * @return array
     */
    public function menuTabList($style_id,$returnUrl = null)
    {
        $menus = [
                1=>['name'=>'基础信息','url'=>Url::to(['style/view','id'=>$style_id,'tab'=>1,'returnUrl'=>$returnUrl])],
                2=>['name'=>'款式属性','url'=>Url::to(['style-attribute/index','style_id'=>$style_id,'tab'=>2,'returnUrl'=>$returnUrl])],
                3=>['name'=>'商品列表','url'=>Url::to(['style-goods/edit-all','style_id'=>$style_id,'tab'=>3,'returnUrl'=>$returnUrl])],
                4=>['name'=>'石头信息','url'=>Url::to(['style-stone/index','style_id'=>$style_id,'tab'=>4,'returnUrl'=>$returnUrl])],
                5=>['name'=>'工厂信息','url'=>Url::to(['style-factory/index','style_id'=>$style_id,'tab'=>5,'returnUrl'=>$returnUrl])],
                6=>['name'=>'工费信息','url'=>Url::to(['style-factory-fee/index','style_id'=>$style_id,'tab'=>6,'returnUrl'=>$returnUrl])],
                7=>['name'=>'款式图片','url'=>Url::to(['style-image/index','style_id'=>$style_id,'tab'=>7,'returnUrl'=>$returnUrl])],
                8=>['name'=>'日志信息','url'=>Url::to(['style-log/index','style_id'=>$style_id,'tab'=>8,'returnUrl'=>$returnUrl])]
        ];
        
        $model = Style::find()->select(['id','is_inlay'])->where(['id'=>$style_id])->one();        
        if($model && $model->is_inlay==0) {
            unset($menus[4]);
        }
        return $menus;
    }
    
    /**
     * 获取款式属性列表
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getStyleAttrList($style_id)
    {
        return StyleAttribute::find()->where(['style_id'=>$style_id])->asArray()->all();
    }
    
    /**
     * 创建款式编号
     * @param Style $model
     */
    public static function createStyleSn($model,$save = true)
    {   
        if(!$model->id) {
            throw new \Exception("编款失败：款式ID不能为空");
        }
        $channel_tag = $model->channel->tag ?? null;
        if(empty($channel_tag)) {
            throw new \Exception("编款失败：款式渠道未配置编码规则");
        }
        //1.渠道部门代号
        $prefix   = $channel_tag;
        //2.款式分类
        $cate_tag = $model->cate->tag ?? '';    
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
        $last = $model->style_material;
        $model->style_sn = $prefix.$middle.$last;
        if($save === true) {
            $model->is_autosn = AutoSnEnum::YES;
            $result = $model->save(true,['id','style_sn','is_autosn']);
            if($result === false){
                throw new \Exception("编款失败：保存款号失败");
            }
        }
        return $model->style_sn;
    }

    public function getStyleImages($style_sn){
        $list = StyleImages::find()->alias('a')
            ->innerJoin(Style::tableName().' s','s.id=a.style_id')
            ->where(['s.style_sn'=>$style_sn,'a.status'=>StatusEnum::ENABLED])
            ->select(['a.image'])
            ->asArray()
            ->all();
        if(empty($list)) return [];
        return array_column($list,'image');
    }

    public function isExist($style_sn=null){
        if($style_sn == null) return false;
        $style = Style::find()->where(['style_sn'=>$style_sn])->select(['id'])->one();
        return $style;
    }
}
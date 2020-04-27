<?php

namespace addons\Style\services;

use Yii;
use common\components\Service;
use addons\Style\common\models\Style;
use common\helpers\Url;
use addons\Style\common\models\StyleAttribute;

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
        return [
                1=>['name'=>'基础信息','url'=>Url::to(['style/view','id'=>$style_id,'tab'=>1,'returnUrl'=>$returnUrl])],
                2=>['name'=>'款式属性','url'=>Url::to(['style-attribute/index','style_id'=>$style_id,'tab'=>2,'returnUrl'=>$returnUrl])],
                3=>['name'=>'商品属性','url'=>Url::to(['style-goods/edit-all','style_id'=>$style_id,'tab'=>3,'returnUrl'=>$returnUrl])],
                4=>['name'=>'石头信息','url'=>Url::to(['style-stone/index','style_id'=>$style_id,'tab'=>4,'returnUrl'=>$returnUrl])],
                5=>['name'=>'工厂信息','url'=>Url::to(['style-factory/index','style_id'=>$style_id,'tab'=>5,'returnUrl'=>$returnUrl])],
                6=>['name'=>'工费信息','url'=>Url::to(['style-factory-fee/index','style_id'=>$style_id,'tab'=>6,'returnUrl'=>$returnUrl])],
                7=>['name'=>'款式图片','url'=>Url::to(['style-image/index','style_id'=>$style_id,'tab'=>7,'returnUrl'=>$returnUrl])],
                8=>['name'=>'日志信息','url'=>Url::to(['style-log/index','style_id'=>$style_id,'tab'=>8,'returnUrl'=>$returnUrl])]
        ];
    }
    
    /**
     * 获取款式属性列表
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getStyleAttrList($style_id)
    {
        return StyleAttribute::find()->where(['style_id'=>$style_id])->asArray()->all();
    }

}
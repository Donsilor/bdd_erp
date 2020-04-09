<?php

namespace addons\Style\services;

use Yii;
use common\components\Service;
use addons\Style\common\models\Style;
use common\helpers\Url;

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
    public function editTabList($id,$returnUrl = null)
    {
        $tab_list = [
                1=>['name'=>'基础信息','url'=>Url::to(['style/view','id'=>$id,'tab'=>1,'returnUrl'=>$returnUrl])],
                2=>['name'=>'款式属性','url'=>Url::to(['style-attribute/index','style_id'=>$id,'tab'=>2,'returnUrl'=>$returnUrl])],
                3=>['name'=>'商品属性','url'=>Url::to(['style/edit-goods','id'=>$id,'tab'=>3,'returnUrl'=>$returnUrl])],
                4=>['name'=>'石头信息','url'=>Url::to(['style-stone/index','style_id'=>$id,'tab'=>4,'returnUrl'=>$returnUrl])],
                5=>['name'=>'工厂信息','url'=>Url::to(['style-factory/index','style_id'=>$id,'tab'=>5,'returnUrl'=>$returnUrl])],
                6=>['name'=>'工费信息','url'=>Url::to(['style-factory-fee/index','style_id'=>$id,'tab'=>6,'returnUrl'=>$returnUrl])],
                7=>['name'=>'款式图片','url'=>Url::to(['style-image/index','style_id'=>$id,'tab'=>7,'returnUrl'=>$returnUrl])],
                8=>['name'=>'日志信息','url'=>Url::to(['style-log/index','style_id'=>$id,'tab'=>8,'returnUrl'=>$returnUrl])]
        ];
        
        return $tab_list;
    }
    

}
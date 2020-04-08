<?php

namespace addons\Style\services;

use Yii;
use common\components\Service;
use addons\Style\common\models\Style;
use common\helpers\Url;
use addons\Style\common\models\AttributeSpec;
use addons\Style\common\models\StyleAttribute;
use common\enums\StatusEnum;


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
    public function editTabList($id)
    {
        $tab_list = [
                1=>['name'=>'基础信息','url'=>Url::to(['style/edit-info','id'=>$id,'tab'=>1])],
                2=>['name'=>'款式属性','url'=>Url::to(['style/edit-attr','id'=>$id,'tab'=>2])],
                3=>['name'=>'款式规格','url'=>Url::to(['style/edit-goods','id'=>$id,'tab'=>3])],
                4=>['name'=>'石头信息','url'=>Url::to(['style-stone/index','id'=>$id,'tab'=>4])],
                5=>['name'=>'工厂信息','url'=>Url::to(['style-factory/index','id'=>$id,'tab'=>5])],
                6=>['name'=>'工费信息','url'=>Url::to(['style-factory-fee/index','id'=>$id,'tab'=>6])],
                7=>['name'=>'款式图片','url'=>Url::to(['style-image/index','id'=>$id,'tab'=>7])],
                8=>['name'=>'日志信息','url'=>Url::to(['style-log/index','id'=>$id,'tab'=>8])]
        ];
        
        return $tab_list;
    }
    

}
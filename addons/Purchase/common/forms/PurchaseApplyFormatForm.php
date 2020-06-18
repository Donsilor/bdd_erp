<?php

namespace addons\Purchase\common\forms;

use addons\Purchase\common\models\PurchaseApplyGoods;

/**
 * 采购申请-版式 Form
 *
 *
 */
class PurchaseApplyFormatForm extends PurchaseApplyGoods
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = [
            [['format_sn', 'format_images'], 'required'],
        ];
        return array_merge(parent::rules() , $rules);
    }



    public function beforeValidate()
    {

        //版式图片
        $format_images = $this->format_images;
        if(is_array($format_images)){
            $this->format_images = implode(',',$format_images);
        }
        //版式视频
        $format_video = $this->format_video;
        if(is_array($format_video)){
            $this->format_video = implode(',',$format_video);
        }


        return parent::beforeValidate(); // TODO: Change the autogenerated stub
    }

}

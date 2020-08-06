<?php

namespace addons\Style\services;

use Yii;
use common\components\Service;
use addons\Style\common\models\Style;

/**
 * Class StyleImageService
 * @package services\common
 * @author jianyan74 <751393839@qq.com>
 */
class StyleImageService extends Service
{
    public function createSyncImages($style_sn, $images) 
    {
        
         $style = Style::find()->where(['style_sn'=>$style_sn])->one();
         if(!$style) {
             throw new \Exception("[{$style_sn}]款号不存在");
         }
         foreach ($images as $image) {
             
         }

    }
}
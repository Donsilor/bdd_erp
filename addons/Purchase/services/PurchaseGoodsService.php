<?php

namespace addons\Purchase\services;

use addons\Style\common\models\Qiban;
use addons\Style\common\models\Style;
use Yii;
use common\components\Service;
use addons\Purchase\common\models\Purchase;
use addons\Purchase\common\models\PurchaseGoodsAttribute;

/**
 * Class PurchaseGoodsService
 * @package services\common
 * @author jianyan74 <751393839@qq.com>
 */
class PurchaseGoodsService extends Service
{    
   
    /**
     * 列表自定义字段
     * @param unknown $id
     * @return boolean[][]|string[][]|NULL[][]
     */
    public function listColmuns($id)
    {
        $models = PurchaseGoodsAttribute::find()->select(['attr_id','input_type','GROUP_CONCAT(attr_value order by sort asc) as attr_values'])->where(['id'=>$id])->groupBy(['attr_id'])->asArray()->all();
        $columns = [];
        if($models) {
            foreach ($models as $model) {
                $columns[$model['attr_id']] =  \Yii::$app->attr->attrName($model['attr_id']);
            }
        }
        return $columns;
    }

    public function getStyleImage($model){
        if($model->qiban_sn){
            $qiban = Qiban::find()->where(['qiban_sn'=>$model->qiban_sn])->one();
            $image = !empty($qiban->style_image)?explode(',', $qiban->style_image):[];
            $image = $image ? $image[0] :'' ;

        }else{
            $style = Style::find()->where(['style_sn'=>$model->style_sn])->one();
            $image = $style->style_image ?? '';
        }
        return $image;

    }
    
}
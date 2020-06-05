<?php

namespace addons\Purchase\services;

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
    
}
<?php

namespace addons\Style\services;

use Yii;
use common\components\Service;
use common\enums\StatusEnum;
use common\helpers\ArrayHelper;
use addons\style\common\models\StyleCate;


/**
 * Class StyleCateService
 * @package addons\Style\services
 * @author jianyan74 <751393839@qq.com>
 */
class StyleCateService extends Service
{
    
    /**
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getDropDown($pid = null)
    {

        $query = StyleCate::find()
                    ->where(['status' => StatusEnum::ENABLED]);
        
        if($pid !== null){
            $query->andWhere(['pid'=>$pid]);
        }
        
        $models = $query->select(['id','level','pid', 'name'])->orderBy('sort asc,created_at asc')->asArray()->all();

        $models = ArrayHelper::itemsMerge($models);
        
        return ArrayHelper::map(ArrayHelper::itemsMergeDropDown($models,'id','name'), 'id', 'name');
    }
}
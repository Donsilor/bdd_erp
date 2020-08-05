<?php

namespace addons\Sales\services;

use Yii;
use common\components\Service;
use common\enums\StatusEnum;
use common\helpers\ArrayHelper;
use addons\Sales\common\models\FqcConfig;

/**
 * Class PurchaseFqcConfigService
 * @package addons\Style\services
 * @author jianyan74 <751393839@qq.com>
 */
class FqcConfigService extends Service
{

    /**
     * 编辑获取下拉
     *
     * @param string $pid
     * @return array
     */
    public static function getDropDownForEdit($pid = ''){
        $data = self::getDropDown($pid);
        return ArrayHelper::merge([0 => '顶级分类'], $data);
    }

    /**
     * @param integer $pid
     * @return array
     */
    public static function getDropDown($pid = null)
    {
        $list = FqcConfig::find()
            ->where(['=', 'status', StatusEnum::ENABLED])
            ->andFilterWhere(['<>', 'id', $pid])
            ->select(['id', 'name', 'pid', 'level'])
            ->orderBy('sort asc')
            ->asArray()
            ->all();
        $models = ArrayHelper::itemsMerge($list);
        return ArrayHelper::map(ArrayHelper::itemsMergeDropDown($models,'id', 'name'), 'id', 'name');

    }

    /**
     * 分组下拉框
     * @param integer $pid
     * @param integer $treeStat
     * @return array
     */
    public static function getGrpDropDown($pid = null, $treeStat = 1)
    {
        $query = FqcConfig::find()->alias('a')
            ->where(['status' => StatusEnum::ENABLED]) ;
        if($pid !== null){
            if($pid ==0){
                $query->andWhere(['a.pid'=>$pid]);
            }else{
                $query->andWhere(['or',['a.pid'=>$pid],['a.id'=>$pid]]);
            }
        }
        $models = $query
            ->select(['id', 'name', 'pid', 'level'])
            ->orderBy('sort asc,created_at asc')
            ->asArray()
            ->all();
        return  ArrayHelper::itemsMergeGrpDropDown($models,0,'id','name','pid', $treeStat);
    }
}
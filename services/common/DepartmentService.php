<?php

namespace services\common;

use common\models\common\Department;
use Yii;
use common\components\Service;
use common\enums\StatusEnum;
use common\helpers\ArrayHelper;


/**
 * Class StyleCateService
 * @package addons\Style\services
 * @author jianyan74 <751393839@qq.com>
 */
class DepartmentService extends Service
{


    /**
     * 编辑获取下拉
     *
     * @param string $id
     * @return array
     */
    public static function getDropDownForEdit($pid = ''){
        $data = self::getDropDown($pid);
        return ArrayHelper::merge([0 => '顶级分类'], $data);

    }

    /**
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getDropDown($pid = null)
    {

        $list = Department::find()
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
     * @param unknown $pid
     * @param unknown $language
     * @return array
     */
    public static function getGrpDropDown($pid = null,$treeStat = 1)
    {

        $query = Department::find()->alias('a')
            ->where(['status' => StatusEnum::ENABLED]) ;
        if($pid !== null){
            if($pid ==0){
                $query->andWhere(['a.pid'=>$pid]);
            }
            else{
                $query->andWhere(['or',['a.pid'=>$pid],['a.id'=>$pid]]);
            }
        }

        $models =$query
            ->select(['id', 'name', 'pid', 'level'])
            ->orderBy('sort asc,created_at asc')
            ->asArray()
            ->all();

        return  ArrayHelper::itemsMergeGrpDropDown($models,0,'id','name','pid',$treeStat);
    }
}
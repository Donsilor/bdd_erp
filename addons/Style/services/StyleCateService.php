<?php

namespace addons\Style\services;

use common\helpers\TreeHelper;
use Yii;
use common\components\Service;
use common\enums\StatusEnum;
use common\helpers\ArrayHelper;
use addons\Style\common\models\StyleCate;


/**
 * Class StyleCateService
 * @package addons\Style\services
 * @author jianyan74 <751393839@qq.com>
 */
class StyleCateService extends Service
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

        $list = StyleCate::find()
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

        $query = StyleCate::find()->alias('a')
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

    /**
     * 产品分类列表
     */
    public static function getList($pid = null, $key = 'id', $value = 'name')
    {
        $list = StyleCate::find()
            ->where(['=', 'status', StatusEnum::ENABLED])
            ->andFilterWhere(['<>', 'id', $pid])
            ->select(['id', 'name'])
            ->orderBy('sort asc')
            ->asArray()
            ->all();
        return array_column($list, $value, $key);
    }

    /**
     * 获取所有下级
     *
     * @param $tree
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getChilds($tree)
    {
        return StyleCate::find()
            ->where(['like', 'tree', $tree . '%', false])
            ->andWhere(['>=', 'status', StatusEnum::DISABLED])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->asArray()
            ->all();
    }


    /**
     * @param $id
     * @return array|\yii\db\ActiveRecord|null|Cate
     */
    public function findById($id)
    {
        return StyleCate::find()
            ->where(['id' => $id])
            ->andWhere(['>=', 'status', StatusEnum::DISABLED])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->asArray()
            ->one();
    }

    /**
     * 获取所有下级id
     *
     * @param $id
     * @return array
     */
    public function findChildIdsById($id)
    {
        if ($model = $this->findById($id)) {
            $tree = $model['tree'] .  TreeHelper::prefixTreeKey($id);
            $list = $this->getChilds($tree);
            return ArrayHelper::merge([$id], array_column($list, 'id'));
        }

        return [];
    }
}
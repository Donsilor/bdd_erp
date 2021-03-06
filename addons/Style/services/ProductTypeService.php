<?php

namespace addons\Style\services;

use addons\Style\common\models\ProductType;
use common\helpers\TreeHelper;
use Yii;
use common\components\Service;
use common\enums\StatusEnum;
use common\helpers\ArrayHelper;


/**
 * Class TypeService
 * @package services\common
 * @author jianyan74 <751393839@qq.com>
 */
class ProductTypeService extends Service
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
        $list = ProductType::find()
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
        $query = ProductType::find()
            ->where(['status' => StatusEnum::ENABLED]);
        
        if($pid !== null){
            if($pid ==0){
                $query->andWhere(['pid'=>$pid]);
            }
            else{
                $query->andWhere(['or',['pid'=>$pid],['id'=>$pid]]);
            }            
        }
        
        $models =$query->select(['id' ,'level', 'pid', 'name'])->orderBy('sort asc,created_at asc')->asArray()->all();
        
       return  ArrayHelper::itemsMergeGrpDropDown($models,0,'id','name','pid',$treeStat);
    }
    /**
     * 查询指定ID下所有产品线
     * @param unknown $id
     * @param number $status
     * @param unknown $language
     * @return mixed
     */
    public static function getAllTypesById($id,$status = 1)
    {

        
        $query = ProductType::find()->alias('a');
        if($status !== null){
            $query->andWhere(['=','a.status',$status]);
        }
        $query->andWhere(['or',['a.id'=>$id],['a.pid'=>$id]]);      
        $models =$query->select(['id' ,'level', 'pid', 'name'])->orderBy('sort asc,created_at asc')->asArray()->all();
        $models = ArrayHelper::itemsMerge($models,0,'id','pid','chidren');
        if(!empty($models)) {
            $models = $models[0];
            $models['ids'] = [$id];
            if(!empty($models['chidren'])){
                $models['ids'] = array_merge($models['ids'],array_column($models['chidren'], 'id'));
            }
        }
        
        return $models;        
    }

    public static function getNameById($id ){
        $model = ProductType::find()
            ->Where(['id'=>$id])
            ->select(['name'])
            ->asArray()
            ->one();
        return $model['name'];
    }

    /**
     * 产品线列表
     */
    public static function getList($pid = null, $key = 'id', $value = 'name')
    {
        $list = ProductType::find()
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
        return ProductType::find()
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
        return ProductType::find()
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
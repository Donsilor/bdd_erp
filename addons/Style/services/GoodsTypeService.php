<?php

namespace services\goods;

use Yii;
use common\components\Service;
use common\enums\StatusEnum;
use common\helpers\ArrayHelper;
use addons\style\common\models\Type;
use addons\style\common\models\TypeLang;
use addons\style\common\models\GoodsTypeLang;


/**
 * Class TypeService
 * @package services\common
 * @author jianyan74 <751393839@qq.com>
 */
class TypeService extends Service
{
    
    /**
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getDropDown($pid = null,$treeStat = 1,$language = null)
    {
        if(empty($language)){
            $language = Yii::$app->params['language'];
        }
        $query = GoodsTY::find()->alias('a')
                    ->where(['status' => StatusEnum::ENABLED])
                    ->andWhere(['merchant_id' => Yii::$app->services->merchant->getId()]);

        if($pid !== null){
            $query->andWhere(['a.pid'=>$pid]);
        }else{
            $pid = 0;
        }
        
        $models = $query->leftJoin(GoodsTypeLang::tableName().' b', 'b.master_id = a.id and b.language = "'.$language.'"')
            ->select(['a.*', 'b.type_name'])
            ->orderBy('sort asc,created_at asc')
            ->asArray()
            ->all();

        $models = ArrayHelper::itemsMerge($models,$pid);
        return ArrayHelper::map(ArrayHelper::itemsMergeDropDown($models,'id','type_name',$treeStat), 'id', 'type_name');
    }
    /**
     * 分组下拉框
     * @param unknown $pid
     * @param unknown $language
     * @return array
     */
    public static function getGrpDropDown($pid = null,$treeStat = 1,$language = null)
    {
        if(empty($language)){
            $language = Yii::$app->params['language'];
        }
        $query = Type::find()->alias('a')
            ->where(['status' => StatusEnum::ENABLED])
            ->andWhere(['merchant_id' => Yii::$app->services->merchant->getId()]);
        
        if($pid !== null){
            if($pid ==0){
                $query->andWhere(['a.pid'=>$pid]);
            }
            else{
                $query->andWhere(['or',['a.pid'=>$pid],['a.id'=>$pid]]);
            }            
        }
        
        $models =$query->leftJoin('{{%goods_type_lang}} b', 'b.master_id = a.id and b.language = "'.$language.'"')
            ->select(['a.*', 'b.type_name'])
            ->orderBy('sort asc,created_at asc')
            ->asArray()
            ->all();
        
       return  ArrayHelper::itemsMergeGrpDropDown($models,0,'id','type_name','pid',$treeStat);
    }
    /**
     * 查询指定ID下所有产品线
     * @param unknown $id
     * @param number $status
     * @param unknown $language
     * @return mixed
     */
    public static function getAllTypesById($id,$status = 1,$language = null) 
    {
        if(empty($language)){
            $language = Yii::$app->params['language'];
        }
        
        $query = Type::find()->alias('a');        
        if($status !== null){
            $query->andWhere(['=','a.status',$status]);
        }
        $query->andWhere(['or',['a.id'=>$id],['a.pid'=>$id]]);      
        $models =$query->leftJoin(TypeLang::tableName().' b', 'b.master_id = a.id and b.language = "'.$language.'"')
                        ->select(['a.id' , 'a.pid', 'b.type_name'])
                        ->orderBy('sort asc,created_at asc')
                        ->asArray()
                        ->all();
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

    public static function getTypeNameById($id ,$language=null){
        if(empty($language)){
            $language = Yii::$app->params['language'];
        }
        $query = Type::find()->alias('a');
        $query->andWhere(['a.id'=>$id]);
        $model =$query->leftJoin('{{%goods_type_lang}} b', 'b.master_id = a.id and b.language = "'.$language.'"')
            ->select([ 'b.type_name'])
            ->asArray()
            ->one();

        return $model['type_name'];
    }
}
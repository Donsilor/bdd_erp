<?php

namespace addons\Style\backend\controllers;

use Yii;
use addons\style\common\models\Goods;
use common\traits\Curd;
use common\models\base\SearchModel;
use backend\controllers\BaseController;


/**
* Goods
*
* Class GoodsController
* @package backend\modules\goods\controllers
*/
class GoodsController extends BaseController
{
    use Curd;

    /**
    * @var Goods
    */
    public $modelClass = Goods::class;


    /**
    * 首页
    *
    * @return string
    * @throws \yii\web\NotFoundHttpException
    */
    public function actionIndex()
    {
        $type_id = Yii::$app->request->get('type_id',0);
        $searchModel = new SearchModel([
            'model' => $this->modelClass,
            'scenario' => 'default',
            'partialMatchAttributes' => [], // 模糊查询
            'defaultOrder' => [
                'id' => SORT_DESC
            ],
            'pageSize' => $this->pageSize,
            'relations' => [
                'style' => ['style_sn','sale_price'],
                'styleLang' => ['style_name'],
                'markup' => ['sale_price','area_id','status'],
            ]
        ]);

        $typeModel= Yii::$app->services->goodsType->getAllTypesById($type_id,null);
         
        $dataProvider = $searchModel
            ->search(Yii::$app->request->queryParams);
        
        $params = Yii::$app->request->queryParams;
        //切换默认地区11
        if(!empty($params['SearchModel']['markup.area_id'])) {
            $area_id = Yii::$app->request->queryParams['SearchModel']['markup.area_id'];
            $this->setLocalAreaId($area_id);
        }

        if($typeModel){
            $dataProvider->query->andFilterWhere(['in', 'goods.type_id',$typeModel['ids']]);
        }

//        $dataProvider->query->andFilterWhere(['IS','goods_markup.area_id',new \yii\db\Expression('NULL')]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'typeModel'  =>$typeModel,
        ]);
    }
}

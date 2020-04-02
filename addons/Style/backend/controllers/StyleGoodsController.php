<?php

namespace addons\Style\backend\controllers;

use Yii;
use common\traits\Curd;
use common\models\base\SearchModel;
use addons\style\common\models\StyleGoods;
/**
* Goods
*
* Class GoodsController
* @package backend\modules\goods\controllers
*/
class StyleGoodsController extends BaseController
{
    use Curd;

    /**
    * @var Goods
    */
    public $modelClass = StyleGoods::class;


    /**
    * 首页
    *
    * @return string
    * @throws \yii\web\NotFoundHttpException
    */
    public function actionIndex()
    {
        $searchModel = new SearchModel([
            'model' => $this->modelClass,
            'scenario' => 'default',
            'partialMatchAttributes' => ['goods_name'], // 模糊查询
            'defaultOrder' => [
                'id' => SORT_DESC
            ],
            'pageSize' => $this->pageSize,
            'relations' => [
                 
            ]
        ]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }
}

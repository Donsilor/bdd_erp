<?php

namespace addons\Style\backend\controllers;

use Yii;
use addons\Style\common\models\DiamondSource;
use common\traits\Curd;
use common\models\base\SearchModel;
use backend\controllers\BaseController;

/**
* DiamondSource
*
* Class DiamondSourceController
* @package backend\modules\goods\controllers
*/
class DiamondSourceController extends BaseController
{
    use Curd;

    /**
    * @var DiamondSource
    */
    public $modelClass = DiamondSource::class;


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
            'partialMatchAttributes' => [], // 模糊查询
            'defaultOrder' => [
                'id' => SORT_DESC
            ],
            'pageSize' => $this->pageSize
        ]);

        $dataProvider = $searchModel
            ->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }
}

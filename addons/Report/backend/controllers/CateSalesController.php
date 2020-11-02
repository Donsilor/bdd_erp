<?php

namespace addons\Report\backend\controllers;

use Yii;
use common\traits\Curd;
use common\models\base\SearchModel;
use addons\report\common\forms\CateSalesForm;

/**
 * Default controller for the `CateSalesForm` module
 */
class CateSalesController extends BaseController
{
    use Curd;
    
    /**
     * @var CateSalesForm
     */
    public $modelClass = CateSalesForm::class;
    
    /**
     * Renders the index view for the module
     * @return string
     * @throws
     */
    public function actionIndex()
    {

        $searchModel = new SearchModel([
                'model' => $this->modelClass,
                'scenario' => 'default',
                'partialMatchAttributes' => [], // 模糊查询
                'defaultOrder' => [
                        'id' => SORT_DESC,
                ],
                'pageSize' => $this->pageSize,
                'relations' => [

                ]
        ]);
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams, []);
        return $this->render($this->action->id, [
                'dataProvider' => $dataProvider,
                'searchModel' => $searchModel,
                'cateSales' => \Yii::$app->reportService->cateSales->getCateSalesReport(),
        ]);
    }

}


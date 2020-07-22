<?php

namespace addons\Sales\backend\controllers;

use Yii;
use common\traits\Curd;
use addons\Sales\common\models\ExpressArea;
use addons\Sales\common\forms\ExpressAreaForm;
use common\models\base\SearchModel;

/**
 * 物流配送区域
 *
 * Class ExpressController
 * @package addons\Sales\backend\controllers
 */
class ExpressAreaController extends BaseController
{
    use Curd;

    /**
     * @var ExpressArea
     */
    public $modelClass = ExpressAreaForm::class;
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
            'pageSize' => $this->pageSize,
            'relations' => [
                'member' => ['username'],
            ]
        ]);

        $dataProvider = $searchModel
            ->search(Yii::$app->request->queryParams,['created_at']);

        $created_at = $searchModel->created_at;
        if (!empty($created_at)) {
            $dataProvider->query->andFilterWhere(['>=',ExpressAreaForm::tableName().'.created_at', strtotime(explode('/', $created_at)[0])]);//起始时间
            $dataProvider->query->andFilterWhere(['<',ExpressAreaForm::tableName().'.created_at', (strtotime(explode('/', $created_at)[1]) + 86400)] );//结束时间
        }

        //$dataProvider->query->andWhere(['>',ExpressAreaForm::tableName().'.status',-1]);

        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }
}
<?php

namespace addons\Sales\backend\controllers;

use Yii;
use common\traits\Curd;
use common\enums\StatusEnum;
use common\models\base\SearchModel;
use addons\Sales\common\models\Currency;
use yii\web\NotFoundHttpException;

class CurrencyController extends BaseController
{
    use Curd;

    /**
     * @var \yii\db\ActiveRecord
     */
    public $modelClass = Currency::class;

    /**
     * 首页
     *
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionIndex()
    {
        $searchModel = new SearchModel([
            'model' => $this->modelClass,
            'scenario' => 'default',
            'partialMatchAttributes' => ['sign', 'code', 'name'], // 模糊查询
            'defaultOrder' => [
                'id' => SORT_ASC,
            ],
            'pageSize' => $this->pageSize
        ]);

        $dataProvider = $searchModel
            ->search(Yii::$app->request->queryParams);
        $dataProvider->query
            ->andWhere(['>=', 'status', StatusEnum::DISABLED]);

        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }
}
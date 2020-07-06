<?php

namespace addons\Sales\backend\controllers;

use addons\Sales\common\models\Customer;
use common\models\base\SearchModel;
use Yii;

/**
 * 销售渠道
 *
 * Class SaleChannelController
 * @package addons\Sales\backend\controllers
 */
class SaleChannelController extends BaseController
{
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
            ->search(Yii::$app->request->queryParams,['created_at']);

        $created_at = $searchModel->created_at;
        if (!empty($created_at)) {
            $dataProvider->query->andFilterWhere(['>=',Customer::tableName().'.created_at', strtotime(explode('/', $created_at)[0])]);//起始时间
            $dataProvider->query->andFilterWhere(['<',Customer::tableName().'.created_at', (strtotime(explode('/', $created_at)[1]) + 86400)] );//结束时间
        }

        //$dataProvider->query->andWhere(['>',Customer::tableName().'.status',-1]);

        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }
}
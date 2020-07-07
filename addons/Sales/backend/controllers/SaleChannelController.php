<?php

namespace addons\Sales\backend\controllers;

use Yii;
use common\traits\Curd;
use addons\Sales\common\models\SaleChannel;
use addons\Sales\common\forms\SaleChannelForm;
use common\models\base\SearchModel;

/**
 * 销售渠道
 *
 * Class SaleChannelController
 * @package addons\Sales\backend\controllers
 */
class SaleChannelController extends BaseController
{
    use Curd;

    /**
     * @var SaleChannel
     */
    public $modelClass = SaleChannelForm::class;
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
            $dataProvider->query->andFilterWhere(['>=',SaleChannel::tableName().'.created_at', strtotime(explode('/', $created_at)[0])]);//起始时间
            $dataProvider->query->andFilterWhere(['<',SaleChannel::tableName().'.created_at', (strtotime(explode('/', $created_at)[1]) + 86400)] );//结束时间
        }

        //$dataProvider->query->andWhere(['>',SaleChannel::tableName().'.status',-1]);

        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }
}
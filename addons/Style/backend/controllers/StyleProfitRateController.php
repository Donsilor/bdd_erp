<?php

namespace addons\Style\backend\controllers;

use addons\style\common\models\StyleProfitRate;
use Yii;
use common\traits\Curd;
use common\models\base\SearchModel;



/**
 * StyleChannelController implements the CRUD actions for StyleChannel model.
 */
class StyleProfitRateController extends BaseController
{
    use Curd;
    public $modelClass = StyleProfitRate::class;
    /**
     * Lists all StyleChannel models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SearchModel([
            'model' => $this->modelClass,
            'scenario' => 'default',
            'partialMatchAttributes' => ['cate.name','product.name'], // 模糊查询
            'defaultOrder' => [
                'id' => SORT_DESC
            ],
            'pageSize' => $this->pageSize,
            'relations' => [
                'member' => ['username'],
                'cate' => ['name'],
                'product' => ['name'],
            ]
        ]);

        $dataProvider = $searchModel
            ->search(Yii::$app->request->queryParams,['updated_at']);

        $updated_at = $searchModel->updated_at;
        if (!empty($updated_at)) {
            $dataProvider->query->andFilterWhere(['>=',StyleProfitRate::tableName().'.updated_at', strtotime(explode('/', $updated_at)[0])]);//起始时间
            $dataProvider->query->andFilterWhere(['<',StyleProfitRate::tableName().'.updated_at', (strtotime(explode('/', $updated_at)[1]) + 86400)] );//结束时间
        }

        $dataProvider->query->andWhere(['>',StyleProfitRate::tableName().'.status',-1]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }


}

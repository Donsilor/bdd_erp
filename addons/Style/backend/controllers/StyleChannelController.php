<?php

namespace addons\Style\backend\controllers;

use Yii;
use common\traits\Curd;
use common\models\base\SearchModel;
use addons\style\common\models\StyleChannel;


/**
 * StyleChannelController implements the CRUD actions for StyleChannel model.
 */
class StyleChannelController extends BaseController
{
    use Curd;
    public $modelClass = StyleChannel::class;
    /**
     * Lists all StyleChannel models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SearchModel([
            'model' => $this->modelClass,
            'scenario' => 'default',
            'partialMatchAttributes' => ['name'], // 模糊查询
            'defaultOrder' => [
                'id' => SORT_DESC
            ],
            'pageSize' => $this->pageSize,
            'relations' => [
                'member' => ['realname'],
            ]
        ]);

        $dataProvider = $searchModel
            ->search(Yii::$app->request->queryParams);

        $dataProvider->query->andWhere(['>',StyleChannel::tableName().'.status',-1]);


        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }


}

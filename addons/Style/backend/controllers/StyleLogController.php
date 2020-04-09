<?php

namespace addons\Style\backend\controllers;

use addons\Style\common\models\StyleLog;
use Yii;
use common\traits\Curd;
use common\models\base\SearchModel;



/**
 * StyleChannelController implements the CRUD actions for StyleChannel model.
 */
class StyleLogController extends BaseController
{
    use Curd;
    public $modelClass = StyleLog::class;
    /**
     * Lists all StyleChannel models.
     * @return mixed
     */
    public function actionIndex()
    {
        $style_id = Yii::$app->request->get('id');
        $tab = Yii::$app->request->get('tab');
        $returnUrl = Yii::$app->request->get('returnUrl');
        $searchModel = new SearchModel([
            'model' => $this->modelClass,
            'scenario' => 'default',
            'partialMatchAttributes' => ['factory.factory_name'], // 模糊查询
            'defaultOrder' => [
                'id' => SORT_DESC
            ],
            'pageSize' => $this->pageSize,

        ]);

        $dataProvider = $searchModel
            ->search(Yii::$app->request->queryParams);

        $dataProvider->query->andWhere(['=',StyleLog::tableName().'.style_id',$style_id]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'tab'=>$tab,
            'style_id' => $style_id,
            'tabList'=>\Yii::$app->styleService->style->editTabList($style_id,$returnUrl),
        ]);
    }


}

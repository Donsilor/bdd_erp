<?php

namespace addons\Supply\backend\controllers;

use addons\Supply\common\models\Produce;
use common\helpers\Url;
use common\models\base\SearchModel;
use common\traits\Curd;
use Yii;
use common\controllers\AddonsController;

/**
 * 默认控制器
 *
 * Class DefaultController
 * @package addons\Supply\backend\controllers
 */
class ProduceController extends BaseController
{
    use Curd;

    /**
     * @var Attribute
     */
    public $modelClass = Produce::class;
    /**
    * 首页
    *
    * @return string
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
                'purchaseGoods' => ['goods_name'],
                'follower' => ['username']
            ]
        ]);

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }


    /**
     * 详情展示页
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView()
    {
        $id = Yii::$app->request->get('id');
        $tab = Yii::$app->request->get('tab',1);
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['purchase/index']));

        $model = $this->findModel($id);
        return $this->render($this->action->id, [
            'model' => $model,
            'tab'=>$tab,
            'tabList'=>\Yii::$app->supplyService->produce->menuTabList($id,$returnUrl),
            'returnUrl'=>$returnUrl,
        ]);
    }
}
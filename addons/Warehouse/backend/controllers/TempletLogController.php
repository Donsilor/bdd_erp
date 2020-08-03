<?php

namespace addons\Warehouse\backend\controllers;


use common\helpers\Url;
use common\models\base\SearchModel;
use addons\Warehouse\common\models\WarehouseTemplet;
use addons\Warehouse\common\models\WarehouseTempletLog;
use common\traits\Curd;
use Yii;

/**
 * 样板库存日志
 *
 * Class DefaultController
 * @package addons\Supply\backend\controllers
 */
class TempletLogController extends BaseController
{
    use Curd;

    /**
     * @var WarehouseTempletLog
     */
    public $modelClass = WarehouseTempletLog::class;
    /**
    * 首页
    *
    * @return string
    */
    public function actionIndex()
    {
        $id = Yii::$app->request->get('id');
        $tab = Yii::$app->request->get('tab', 2);
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['templet-log/index', 'id'=>$id]));
        $searchModel = new SearchModel([
            'model' => $this->modelClass,
            'scenario' => 'default',
            'partialMatchAttributes' => [], // 模糊查询
            'defaultOrder' => [
                'id' => SORT_DESC
            ],
            'pageSize' => $this->pageSize,
            'relations' => [
                'member' => ['username']
            ]

        ]);
        $dataProvider = $searchModel
            ->search(Yii::$app->request->queryParams,['created_at']);
        $created_at = $searchModel->created_at;
        if (!empty($created_at)) {
            $dataProvider->query->andFilterWhere(['>=',WarehouseTempletLog::tableName().'.created_at', strtotime(explode('/', $created_at)[0])]);//起始时间
            $dataProvider->query->andFilterWhere(['<',WarehouseTempletLog::tableName().'.created_at', (strtotime(explode('/', $created_at)[1]) + 86400)] );//结束时间
        }
        $dataProvider->query->andWhere(['=','templet_id', $id]);
        $templet = WarehouseTemplet::find()->where(['id'=>$id])->one();
        $dataProvider->query->andWhere(['>',WarehouseTempletLog::tableName().'.status',-1]);
        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'templet' => $templet,
            'tab'=>$tab,
            'tabList'=>\Yii::$app->warehouseService->templet->menuTabList($id, $returnUrl),
        ]);
    }



}
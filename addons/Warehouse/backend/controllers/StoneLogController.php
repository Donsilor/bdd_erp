<?php

namespace addons\Warehouse\backend\controllers;

use common\helpers\Url;
use common\models\base\SearchModel;
use addons\Warehouse\common\models\WarehouseStone;
use addons\Warehouse\common\models\WarehouseStoneLog;
use common\traits\Curd;
use Yii;

/**
 * 石料库存日志
 *
 * Class DefaultController
 * @package addons\Supply\backend\controllers
 */
class StoneLogController extends BaseController
{
    use Curd;

    /**
     * @var Attribute
     */
    public $modelClass = WarehouseStoneLog::class;
    /**
    * 首页
    *
    * @return string
    */
    public function actionIndex()
    {
        $tab = Yii::$app->request->get('tab', 2);
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['stone/index']));
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
            $dataProvider->query->andFilterWhere(['>=',WarehouseStoneLog::tableName().'.created_at', strtotime(explode('/', $created_at)[0])]);//起始时间
            $dataProvider->query->andFilterWhere(['<',WarehouseStoneLog::tableName().'.created_at', (strtotime(explode('/', $created_at)[1]) + 86400)] );//结束时间
        }
        $id = Yii::$app->request->get('id');
        $dataProvider->query->andWhere(['=','stone_id', $id]);
        $stone = WarehouseStone::find()->where(['id'=>$id])->one();
        $dataProvider->query->andWhere(['>',WarehouseStoneLog::tableName().'.status',-1]);
        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'stone' => $stone,
            'tab'=>$tab,
            'tabList'=>\Yii::$app->warehouseService->stone->menuTabList($id, $returnUrl),
        ]);
    }



}
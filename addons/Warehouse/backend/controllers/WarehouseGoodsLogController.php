<?php

namespace addons\Warehouse\backend\controllers;

use addons\Warehouse\common\models\WarehouseGoods;
use addons\Warehouse\common\models\WarehouseGoodsLog;
use common\helpers\Url;
use common\models\base\SearchModel;
use common\traits\Curd;
use Yii;

/**
 * 默认控制器
 *
 * Class DefaultController
 * @package addons\Supply\backend\controllers
 */
class WarehouseGoodsLogController extends BaseController
{
    use Curd;

    /**
     * @var Attribute
     */
    public $modelClass = WarehouseGoodsLog::class;
    /**
    * 首页
    *
    * @return string
    */
    public function actionIndex()
    {
        $goods_id = Yii::$app->request->get('goods_id');
        $goods = WarehouseGoods::find()->where(['id'=>$goods_id])->one();
        $tab = Yii::$app->request->get('tab');
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['warehouse-goods/index']));
        $searchModel = new SearchModel([
            'model' => $this->modelClass,
            'scenario' => 'default',
            'partialMatchAttributes' => ['log_msg'], // 模糊查询
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
            $dataProvider->query->andFilterWhere(['>=',WarehouseGoodsLog::tableName().'.created_at', strtotime(explode('/', $created_at)[0])]);//起始时间
            $dataProvider->query->andFilterWhere(['<',WarehouseGoodsLog::tableName().'.created_at', (strtotime(explode('/', $created_at)[1]) + 86400)] );//结束时间
        }

        $dataProvider->query->andWhere(['=','goods_id',$goods_id]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'goods_id' => $goods_id,
            'goods' => $goods,
            'tab'=>$tab,
            'tabList'=>\Yii::$app->warehouseService->warehouseGoods->menuTabList($goods_id,$returnUrl),
        ]);
    }



}
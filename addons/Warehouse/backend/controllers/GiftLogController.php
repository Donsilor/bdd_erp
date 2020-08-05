<?php

namespace addons\Warehouse\backend\controllers;

use common\helpers\Url;
use common\models\base\SearchModel;
use addons\Warehouse\common\models\WarehouseGift;
use addons\Warehouse\common\models\WarehouseGiftLog;
use common\traits\Curd;
use Yii;

/**
 * 赠品库存日志
 *
 * Class DefaultController
 * @package addons\Supply\backend\controllers
 */
class GiftLogController extends BaseController
{
    use Curd;

    /**
     * @var WarehouseGiftLog
     */
    public $modelClass = WarehouseGiftLog::class;
    /**
    * 首页
    *
    * @return string
    */
    public function actionIndex()
    {
        $id = Yii::$app->request->get('id');
        $tab = Yii::$app->request->get('tab', 2);
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['gift-log/index', 'id'=>$id]));
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
            $dataProvider->query->andFilterWhere(['>=',WarehouseGiftLog::tableName().'.created_at', strtotime(explode('/', $created_at)[0])]);//起始时间
            $dataProvider->query->andFilterWhere(['<',WarehouseGiftLog::tableName().'.created_at', (strtotime(explode('/', $created_at)[1]) + 86400)] );//结束时间
        }
        $dataProvider->query->andWhere(['=','gift_id', $id]);
        $gift = WarehouseGift::find()->where(['id'=>$id])->one();
        $dataProvider->query->andWhere(['>',WarehouseGiftLog::tableName().'.status',-1]);
        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'gift' => $gift,
            'tab'=>$tab,
            'tabList'=>\Yii::$app->warehouseService->gift->menuTabList($id, $returnUrl),
        ]);
    }



}
<?php

namespace addons\Warehouse\backend\controllers;

use addons\Warehouse\common\models\WarehousePartsBill;
use addons\Warehouse\common\models\WarehousePartsBillLog;
use Yii;
use common\traits\Curd;
use common\helpers\Url;
use common\models\base\SearchModel;
use addons\Warehouse\common\models\WarehouseGoldBill;
use addons\Warehouse\common\models\WarehouseGoldBillLog;

/**
 * 默认控制器
 *
 * Class DefaultController
 * @package addons\Supply\backend\controllers
 */
class PartsBillLogController extends BaseController
{
    use Curd;

    /**
     * @var WarehousePartsBillLog
     */
    public $modelClass = WarehousePartsBillLog::class;
    /**
    * 首页
    *
    * @return string
    */
    public function actionIndex()
    {
        $bill_id = Yii::$app->request->get('bill_id');
        $tab = Yii::$app->request->get('tab');
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['bill/index', 'bill_id'=>$bill_id]));
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
            $dataProvider->query->andFilterWhere(['>=',WarehousePartsBillLog::tableName().'.created_at', strtotime(explode('/', $created_at)[0])]);//起始时间
            $dataProvider->query->andFilterWhere(['<',WarehousePartsBillLog::tableName().'.created_at', (strtotime(explode('/', $created_at)[1]) + 86400)] );//结束时间
        }
        $dataProvider->query->andWhere(['=','bill_id', $bill_id]);
        $billInfo = WarehousePartsBill::find()->where(['id'=>$bill_id])->one();
        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'bill_id' => $bill_id,
            'billInfo' => $billInfo,
            'tab'=>$tab,
            'tabList'=>\Yii::$app->warehouseService->partsBill->menuTabList($bill_id,$billInfo->bill_type,$returnUrl),
        ]);
    }



}
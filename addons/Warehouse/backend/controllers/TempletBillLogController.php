<?php

namespace addons\Warehouse\backend\controllers;

use Yii;
use common\traits\Curd;
use common\helpers\Url;
use addons\Warehouse\common\models\WarehouseTempletBill;
use addons\Warehouse\common\models\WarehouseTempletBillLog;
use common\models\base\SearchModel;

/**
 * 默认控制器
 *
 * Class DefaultController
 * @package addons\Supply\backend\controllers
 */
class TempletBillLogController extends BaseController
{
    use Curd;

    /**
     * @var WarehouseTempletBillLog
     */
    public $modelClass = WarehouseTempletBillLog::class;
    /**
    * 首页
    *
    * @return string
    */
    public function actionIndex()
    {
        $bill_id = Yii::$app->request->get('bill_id');
        $billInfo = WarehouseTempletBill::find()->where(['id'=>$bill_id])->one();
        $tab = Yii::$app->request->get('tab');
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['bill/index']));
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
            $dataProvider->query->andFilterWhere(['>=',WarehouseTempletBillLog::tableName().'.created_at', strtotime(explode('/', $created_at)[0])]);//起始时间
            $dataProvider->query->andFilterWhere(['<',WarehouseTempletBillLog::tableName().'.created_at', (strtotime(explode('/', $created_at)[1]) + 86400)] );//结束时间
        }

        $dataProvider->query->andWhere(['=','bill_id',$bill_id]);

        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'bill_id' => $bill_id,
            'billInfo' => $billInfo,
            'tab'=>$tab,
            'tabList'=>\Yii::$app->warehouseService->templetBill->menuTabList($bill_id,$billInfo->bill_type,$returnUrl),
        ]);
    }



}
<?php

namespace addons\Warehouse\backend\controllers;

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
class GoldBillLogController extends BaseController
{
    use Curd;

    /**
     * @var Attribute
     */
    public $modelClass = WarehouseGoldBillLog::class;
    /**
    * 首页
    *
    * @return string
    */
    public function actionIndex()
    {
        $bill_id = Yii::$app->request->get('bill_id');
        $billInfo = WarehouseGoldBill::find()->where(['id'=>$bill_id])->one();
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
            $dataProvider->query->andFilterWhere(['>=',WarehouseGoldBillLog::tableName().'.created_at', strtotime(explode('/', $created_at)[0])]);//起始时间
            $dataProvider->query->andFilterWhere(['<',WarehouseGoldBillLog::tableName().'.created_at', (strtotime(explode('/', $created_at)[1]) + 86400)] );//结束时间
        }

        $dataProvider->query->andWhere(['=','bill_id',$bill_id]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'bill_id' => $bill_id,
            'billInfo' => $billInfo,
            'tab'=>$tab,
            'tabList'=>\Yii::$app->warehouseService->goldBill->menuTabList($bill_id,$billInfo->bill_type,$returnUrl),
        ]);
    }



}
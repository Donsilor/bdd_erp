<?php

namespace addons\Warehouse\backend\controllers;

use common\traits\Curd;
use common\models\base\SearchModel;
use addons\Warehouse\common\models\WarehouseTempletBill;
use addons\Warehouse\common\forms\WarehouseTempletBillForm;
use addons\Warehouse\common\forms\WarehouseTempletBillGoodsForm;
use common\helpers\Url;
use Yii;

/**
 * StyleChannelController implements the CRUD actions for StyleChannel model.
 */
class TempletBillController extends BaseController
{
    use Curd;
    public $modelClass = WarehouseTempletBillForm::class;
    /**
     * 列表
     * @return mixed
     */
    public function actionIndex()
    {
        $model = new $this->modelClass;
        $searchParams = Yii::$app->request->get('SearchModel');
        $model->batch_sn = $searchParams['batch_sn']??"";
        if(empty($model->batch_sn)){
            $relations = [
                'creator' => ['username'],
                'auditor' => ['username'],
            ];
        }else{
            $this->modelClass = WarehouseTempletBillGoodsForm::class;
            $relations = [
                'bill' => [
                    'id',
                    'bill_status',
                    'created_at',
                    'audit_status',
                    'audit_time',
                    'status',
                ],
            ];
        }
        $searchModel = new SearchModel([
            'model' => $this->modelClass,
            'scenario' => 'default',
            'partialMatchAttributes' => [], // 模糊查询
            'defaultOrder' => [
                'id' => SORT_DESC
            ],
            'pageSize' => $this->pageSize,
            'relations' => $relations,
        ]);
        if(empty($model->batch_sn)) {
            $dataProvider = $searchModel
                ->search(Yii::$app->request->queryParams, ['created_at']);
            $created_at = $searchModel->created_at;
            if (!empty($created_at)) {
                $dataProvider->query->andFilterWhere(['>=', WarehouseTempletBill::tableName() . '.created_at', strtotime(explode('/', $created_at)[0])]);//起始时间
                $dataProvider->query->andFilterWhere(['<', WarehouseTempletBill::tableName() . '.created_at', (strtotime(explode('/', $created_at)[1]) + 86400)]);//结束时间
            }
            $dataProvider->query->andWhere(['>', WarehouseTempletBill::tableName() . '.status', -1]);
        }else{
            $dataProvider = $searchModel
                ->search(Yii::$app->request->queryParams, ['supplier_id']);
            $supplier_id = $searchModel->supplier_id;
            if($model->batch_sn){
                $dataProvider->query->andWhere(['=','batch_sn', $model->batch_sn]);
            }
            if($supplier_id){
                $dataProvider->query->andWhere(['=','bill.supplier_id', $supplier_id]);
            }
            $dataProvider->query->andWhere(['>', 'bill.status', -1]);
        }
        return $this->render($this->action->id, [
            'model' => $model,
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * 详情展示页
     * @return string
     * @throws
     */
    public function actionView()
    {
        $bill_id = Yii::$app->request->get('id');
        $tab = Yii::$app->request->get('tab',1);
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['templet-bill/index']));
        $model = $this->findModel($bill_id);
        $model = $model ?? new WarehouseTempletBill();
        return $this->render($this->action->id, [
            'model' => $model,
            'tab'=>$tab,
            'tabList'=>\Yii::$app->warehouseService->templetBill->menuTabList($bill_id, $model->bill_type, $returnUrl),
            'returnUrl'=>$returnUrl,
        ]);
    }
}

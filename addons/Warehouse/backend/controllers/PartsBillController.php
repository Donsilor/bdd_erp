<?php

namespace addons\Warehouse\backend\controllers;

use addons\Warehouse\common\forms\WarehouseGoldBillGoodsForm;
use addons\Warehouse\common\forms\WarehouseGoldBillLGoodsForm;
use addons\Warehouse\common\forms\WarehousePartsBillForm;
use addons\Warehouse\common\forms\WarehousePartsBillGoodsForm;
use addons\Warehouse\common\models\WarehousePartsBill;
use common\traits\Curd;
use common\models\base\SearchModel;
use addons\Warehouse\common\models\WarehouseGoldBill;
use addons\Warehouse\common\forms\WarehouseGoldBillForm;
use addons\Warehouse\common\models\WarehouseGoldBillGoods;
use common\helpers\Url;
use Yii;

/**
 * StyleChannelController implements the CRUD actions for StyleChannel model.
 */
class PartsBillController extends BaseController
{
    use Curd;
    public $modelClass = WarehousePartsBillForm::class;
    /**
     * 列表
     * @return mixed
     */
    public function actionIndex()
    {
        $model = new $this->modelClass;
        $searchParams = Yii::$app->request->get('SearchModel');
        $model->parts_sn = $searchParams['parts_sn']??"";
        if(empty($model->parts_sn)){
            $relations = [
                'creator' => ['username'],
                'auditor' => ['username'],
            ];
        }else{
            $this->modelClass = WarehousePartsBillGoodsForm::class;
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
        if(empty($model->parts_sn)) {
            $dataProvider = $searchModel
                ->search(Yii::$app->request->queryParams, ['created_at']);
            $created_at = $searchModel->created_at;
            if (!empty($created_at)) {
                $dataProvider->query->andFilterWhere(['>=', WarehousePartsBill::tableName() . '.created_at', strtotime(explode('/', $created_at)[0])]);//起始时间
                $dataProvider->query->andFilterWhere(['<', WarehousePartsBill::tableName() . '.created_at', (strtotime(explode('/', $created_at)[1]) + 86400)]);//结束时间
            }
            $dataProvider->query->andWhere(['>', WarehousePartsBill::tableName() . '.status', -1]);
        }else{
            $dataProvider = $searchModel
                ->search(Yii::$app->request->queryParams, ['supplier_id']);
            $supplier_id = $searchModel->supplier_id;
            if($model->parts_sn){
                $dataProvider->query->andWhere(['=','parts_sn', $model->parts_sn]);
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
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['parts-bill/index']));
        $model = $this->findModel($bill_id);
        $model = $model ?? new WarehousePartsBill();
        return $this->render($this->action->id, [
            'model' => $model,
            'tab'=>$tab,
            'tabList'=>\Yii::$app->warehouseService->partsBill->menuTabList($bill_id, $model->bill_type, $returnUrl),
            'returnUrl'=>$returnUrl,
        ]);
    }
}

<?php

namespace addons\Warehouse\backend\controllers;

use Yii;
use common\traits\Curd;
use addons\Warehouse\common\models\WarehouseBill;
use addons\Warehouse\common\forms\WarehouseBillForm;
use addons\Warehouse\common\forms\WarehouseBillGoodsForm;
use common\models\base\SearchModel;
use common\helpers\ExcelHelper;
use common\helpers\Url;

/**
 * BillController implements the CRUD actions for BillController model.
 */
class BillController extends BaseController
{
    use Curd;
    public $modelClass = WarehouseBillForm::class;
    /**
     * 列表
     * @return mixed
     */
    public function actionIndex()
    {
        $model = new $this->modelClass;
        $searchParams = Yii::$app->request->get('SearchModel');
        $model->goods_id = $searchParams['goods_id']??"";
        if(empty($model->goods_id)){
            $relations = [
                'creator' => ['username'],
                'auditor' => ['username'],
            ];
        }else{
            $this->modelClass = WarehouseBillGoodsForm::class;
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
        if(empty($model->goods_id)){
            $dataProvider = $searchModel
                ->search(\Yii::$app->request->queryParams,['created_at']);
            $created_at = $searchModel->created_at;
            if (!empty($created_at)) {
                $dataProvider->query->andFilterWhere(['>=',WarehouseBill::tableName().'.created_at', strtotime(explode('/', $created_at)[0])]);//起始时间
                $dataProvider->query->andFilterWhere(['<',WarehouseBill::tableName().'.created_at', (strtotime(explode('/', $created_at)[1]) + 86400)] );//结束时间
            }
            $dataProvider->query->andWhere(['>',WarehouseBill::tableName().'.status',-1]);
        }else{
            $dataProvider = $searchModel
                ->search(\Yii::$app->request->queryParams,['supplier_id','to_warehouse_id','from_warehouse_id']);
            $dataProvider->query->andWhere(['=','goods_id', $model->goods_id]);
            $supplier_id = $searchModel->supplier_id;
            if($supplier_id){
                $dataProvider->query->andWhere(['=','bill.supplier_id', $supplier_id]);
            }
            $to_warehouse_id = $searchModel->to_warehouse_id;
            if($to_warehouse_id){
                $dataProvider->query->andWhere(['=','bill.to_warehouse_id', $to_warehouse_id]);
            }
            $from_warehouse_id = $searchModel->from_warehouse_id;
            if($from_warehouse_id){
                $dataProvider->query->andWhere(['=','bill.from_warehouse_id', $from_warehouse_id]);
            }
            $dataProvider->query->andWhere(['>','bill.status',-1]);
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
        $id = Yii::$app->request->get('id');
        $tab = Yii::$app->request->get('tab',1);
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['bill/index']));
        $model = $this->findModel($id);
        return $this->render($this->action->id, [
            'model' => $model,
            'tab'=>$tab,
            'tabList'=>\Yii::$app->warehouseService->bill->menuTabList($id, $model->bill_type, $returnUrl),
            'returnUrl'=>$returnUrl,
        ]);
    }

    /**
     * 导出
     *
     * @return mixed|string|\yii\web\Response
     * @throws \yii\base\ExitException
     */
    public function getExport($dataProvider)
    {
        $list = $dataProvider->models;
        $header = [
            ['ID', 'id'],
            ['渠道名称', 'name', 'text'],
        ];
        return ExcelHelper::exportData($list, $header, '数据导出_' . time());

    }


}

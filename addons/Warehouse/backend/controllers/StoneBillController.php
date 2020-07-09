<?php

namespace addons\Warehouse\backend\controllers;

use addons\Warehouse\common\forms\WarehouseStoneBillForm;
use addons\Warehouse\common\forms\WarehouseStoneBillGoodsForm;
use Yii;
use common\traits\Curd;
use common\models\base\SearchModel;
use addons\Warehouse\common\models\WarehouseStoneBill;
use common\helpers\Url;

/**
 * StyleChannelController implements the CRUD actions for StyleChannel model.
 */
class StoneBillController extends BaseController
{
    use Curd;
    public $modelClass = WarehouseStoneBillForm::class;

    /**
     * 列表
     * @return mixed
     */
    public function actionIndex()
    {
        $model = new $this->modelClass;
        $searchParams = Yii::$app->request->get('SearchModel');
        $model->stone_sn = $searchParams['stone_sn']??"";
        if(empty($model->stone_sn)){
            $relations = [
                'creator' => ['username'],
                'auditor' => ['username'],
            ];
        }else{
            $this->modelClass = WarehouseStoneBillGoodsForm::class;
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

        if(empty($model->stone_sn)) {
            $dataProvider = $searchModel
                ->search(Yii::$app->request->queryParams,['updated_at']);
            $updated_at = $searchModel->updated_at;
            if (!empty($updated_at)) {
                $dataProvider->query->andFilterWhere(['>=',WarehouseStoneBill::tableName().'.updated_at', strtotime(explode('/', $updated_at)[0])]);//起始时间
                $dataProvider->query->andFilterWhere(['<',WarehouseStoneBill::tableName().'.updated_at', (strtotime(explode('/', $updated_at)[1]) + 86400)] );//结束时间
            }
            $dataProvider->query->andWhere(['>', WarehouseStoneBill::tableName().'.status', -1]);
        }else{
            $dataProvider = $searchModel
                ->search(Yii::$app->request->queryParams, ['supplier_id']);
            $supplier_id = $searchModel->supplier_id;
            if($model->stone_sn){
                $dataProvider->query->andWhere(['=','stone_sn', $model->stone_sn]);
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
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['stone-bill/index']));
        $model = $this->findModel($bill_id);
        $model = $model ?? new WarehouseStoneBill();
        return $this->render($this->action->id, [
            'model' => $model,
            'tab'=>$tab,
            'tabList'=>\Yii::$app->warehouseService->stoneBill->menuTabList($bill_id, $model->bill_type, $returnUrl),
            'returnUrl'=>$returnUrl,
        ]);
    }
}

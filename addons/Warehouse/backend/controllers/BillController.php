<?php

namespace addons\Warehouse\backend\controllers;


use common\helpers\Url;
use Yii;
use common\traits\Curd;
use common\models\base\SearchModel;
use common\helpers\ExcelHelper;
use addons\Warehouse\common\models\WarehouseBill;


/**
 * WarehouseBillController implements the CRUD actions for WarehouseBillController model.
 */
class BillController extends BaseController
{
    use Curd;
    public $modelClass = WarehouseBill::class;
    /**
     * Lists all StyleChannel models.
     * @return mixed
     */
    public function actionIndex()
    {

        $searchModel = new SearchModel([
            'model' => $this->modelClass,
            'scenario' => 'default',
            'partialMatchAttributes' => ['name'], // 模糊查询
            'defaultOrder' => [
                'id' => SORT_DESC
            ],
            'pageSize' => $this->pageSize,
            'relations' => [
//                'supplier' => ['supplier_name'],
//                'member' => ['username'],
                'creator' => ['username'],
                'auditor' => ['username'],

            ]
        ]);

        $dataProvider = $searchModel
            ->search(\Yii::$app->request->queryParams,['updated_at']);

        $created_at = $searchModel->created_at;
        if (!empty($created_at)) {
            $dataProvider->query->andFilterWhere(['>=',Warehousebill::tableName().'.created_at', strtotime(explode('/', $created_at)[0])]);//起始时间
            $dataProvider->query->andFilterWhere(['<',Warehousebill::tableName().'.created_at', (strtotime(explode('/', $created_at)[1]) + 86400)] );//结束时间
        }

        $audit_time = $searchModel->audit_time;
        if (!empty($audit_time)) {
            $dataProvider->query->andFilterWhere(['>=',Warehousebill::tableName().'.audit_time', strtotime(explode('/', $audit_time)[0])]);//起始时间
            $dataProvider->query->andFilterWhere(['<',Warehousebill::tableName().'.audit_time', (strtotime(explode('/', $audit_time)[1]) + 86400)] );//结束时间
        }

        $dataProvider->query->andWhere(['>',Warehousebill::tableName().'.status',-1]);

        //导出
        if(Yii::$app->request->get('action') === 'export'){
            $this->getExport($dataProvider);
        }
        //echo $this->action->id;
        if($searchModel->bill_type) {
            $this->action->id = '../bill-'.strtolower($searchModel->bill_type).'/index';
        }       
        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);


    }

    /**
     * 详情展示页
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView()
    {
        $id = Yii::$app->request->get('id');
        $tab = Yii::$app->request->get('tab',1);
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['warehouser-bill/index']));
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

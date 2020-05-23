<?php

namespace addons\Warehouse\backend\controllers;


use addons\Warehouse\common\models\WarehouseBillGoods;
use Yii;
use common\traits\Curd;
use common\models\base\SearchModel;
use common\helpers\ExcelHelper;
use addons\Warehouse\common\models\WarehouseBill;


/**
 * WarehouseBillController implements the CRUD actions for WarehouseBillController model.
 */
class WarehouseBillGoodsController extends BaseController
{
    use Curd;
    public $modelClass = WarehouseBillGoods::class;
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
                'member' => ['username'],

            ]
        ]);

        $dataProvider = $searchModel
            ->search(Yii::$app->request->queryParams,['updated_at']);

        $updated_at = $searchModel->updated_at;
        if (!empty($updated_at)) {
            $dataProvider->query->andFilterWhere(['>=',WarehouseBillGoods::tableName().'.updated_at', strtotime(explode('/', $updated_at)[0])]);//起始时间
            $dataProvider->query->andFilterWhere(['<',WarehouseBillGoods::tableName().'.updated_at', (strtotime(explode('/', $updated_at)[1]) + 86400)] );//结束时间
        }

        $dataProvider->query->andWhere(['>',WarehouseBillGoods::tableName().'.status',-1]);

        //导出
        if(Yii::$app->request->get('action') === 'export'){
            $this->getExport($dataProvider);
        }


        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);


    }


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

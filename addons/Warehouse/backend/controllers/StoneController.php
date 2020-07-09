<?php

namespace addons\Warehouse\backend\controllers;

use addons\Warehouse\common\forms\WarehouseStoneBillGoodsForm;
use common\helpers\Url;
use Yii;
use common\traits\Curd;
use common\models\base\SearchModel;
use addons\Warehouse\common\forms\WarehouseStoneForm;
use addons\Warehouse\common\models\WarehouseStone;
use common\helpers\ExcelHelper;
use common\helpers\StringHelper;


/**
 * StyleChannelController implements the CRUD actions for StyleChannel model.
 */
class StoneController extends BaseController
{
    use Curd;
    public $modelClass = WarehouseStoneForm::class;
    /**
     * Lists all StyleChannel models.
     * @return mixed
     */
    public function actionIndex()
    {

        $searchModel = new SearchModel([
            'model' => $this->modelClass,
            'scenario' => 'default',
            'partialMatchAttributes' => [], // 模糊查询
            'defaultOrder' => [
                'id' => SORT_DESC
            ],
            'pageSize' => $this->pageSize,
            'relations' => [
                'creator' => ['username'],
            ]
        ]);

        $dataProvider = $searchModel
            ->search(Yii::$app->request->queryParams,['created_at']);

        $created_at = $searchModel->created_at;
        if (!empty($created_at)) {
            $dataProvider->query->andFilterWhere(['>=',WarehouseStone::tableName().'.created_at', strtotime(explode('/', $created_at)[0])]);//起始时间
            $dataProvider->query->andFilterWhere(['<',WarehouseStone::tableName().'.created_at', (strtotime(explode('/', $created_at)[1]) + 86400)] );//结束时间
        }

        $dataProvider->query->andWhere(['>',WarehouseStone::tableName().'.status',-1]);

        //导出
        if(Yii::$app->request->get('action') === 'export'){
            $this->getExport($dataProvider);
        }

        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);

    }

    /**
     * 领石信息
     * @return mixed
     */
    public function actionLingshi()
    {
        $this->modelClass = new WarehouseStoneBillGoodsForm();
        $tab = \Yii::$app->request->get('tab',2);
        $returnUrl = \Yii::$app->request->get('returnUrl',Url::to(['stone/index']));
        $searchModel = new SearchModel([
            'model' => $this->modelClass,
            'scenario' => 'default',
            'partialMatchAttributes' => [], // 模糊查询
            'defaultOrder' => [
                'id' => SORT_DESC
            ],
            'pageSize' => $this->pageSize,
            'relations' => [

            ]
        ]);
        $dataProvider = $searchModel
            ->search(Yii::$app->request->queryParams,['created_at']);
        $created_at = $searchModel->created_at;
        if (!empty($created_at)) {
            $dataProvider->query->andFilterWhere(['>=',WarehouseStoneBillGoodsForm::tableName().'.created_at', strtotime(explode('/', $created_at)[0])]);//起始时间
            $dataProvider->query->andFilterWhere(['<',WarehouseStoneBillGoodsForm::tableName().'.created_at', (strtotime(explode('/', $created_at)[1]) + 86400)] );//结束时间
        }
        $id = \Yii::$app->request->get('id');
        $stone = WarehouseStone::findOne(['id'=>$id]);
        $dataProvider->query->andWhere(['=', 'stone_sn', $stone->stone_sn]);
        $dataProvider->query->andWhere(['>',WarehouseStoneBillGoodsForm::tableName().'.status',-1]);

        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'stone' => $stone,
            'tab' => $tab,
            'tabList'=>\Yii::$app->warehouseService->stone->menuTabList($id, $returnUrl),
        ]);
    }

    /**
     * 详情展示页
     * @return string
     * @throws
     */
    public function actionView()
    {
        $id = \Yii::$app->request->get('id');
        $tab = \Yii::$app->request->get('tab',1);
        $returnUrl = \Yii::$app->request->get('returnUrl',Url::to(['stone/index']));
        $model = $this->findModel($id);
        $model = $model ?? new WarehouseStoneForm();
        $bill = $model->getBillInfo();
        return $this->render($this->action->id, [
            'model' => $model,
            'tab'=>$tab,
            'tabList'=>\Yii::$app->warehouseService->stone->menuTabList($id, $returnUrl),
            'returnUrl'=>$returnUrl,
            'bill'=>$bill,
        ]);
    }

    /**
     * 导出
     * @return string
     * @throws
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

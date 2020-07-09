<?php

namespace addons\Warehouse\backend\controllers;

use addons\Warehouse\common\enums\GoldBillTypeEnum;
use addons\Warehouse\common\forms\WarehouseGoldBillGoodsForm;
use addons\Warehouse\common\forms\WarehouseGoldBillLForm;
use addons\Warehouse\common\models\WarehouseGold;
use addons\Warehouse\common\models\WarehouseGoldBill;
use addons\Warehouse\common\models\WarehouseGoldBillGoods;
use common\helpers\Url;
use Yii;
use common\traits\Curd;
use common\models\base\SearchModel;
use addons\Warehouse\common\models\WarehouseStone;
use addons\Warehouse\common\forms\WarehouseGoldForm;
use common\helpers\ExcelHelper;


/**
 * StyleChannelController implements the CRUD actions for StyleChannel model.
 */
class GoldController extends BaseController
{
    use Curd;
    public $modelClass = WarehouseGoldForm::class;
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
        if (!empty($updated_at)) {
            $dataProvider->query->andFilterWhere(['>=',WarehouseGold::tableName().'.created_at', strtotime(explode('/', $created_at)[0])]);//起始时间
            $dataProvider->query->andFilterWhere(['<',WarehouseGold::tableName().'.created_at', (strtotime(explode('/', $created_at)[1]) + 86400)] );//结束时间
        }

        $dataProvider->query->andWhere(['>',WarehouseGold::tableName().'.status',-1]);

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
     * 详情展示页
     * @return string
     * @throws
     */
    public function actionView()
    {
        $id = Yii::$app->request->get('id');
        $tab = Yii::$app->request->get('tab',1);
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['gold/index']));
        $model = $this->findModel($id);
        $model = $model ?? new WarehouseGoldForm();
        $bill = $model->getBillInfo();
        return $this->render($this->action->id, [
            'model' => $model,
            'tab'=>$tab,
            'tabList'=>\Yii::$app->warehouseService->gold->menuTabList($id, $returnUrl),
            'returnUrl'=>$returnUrl,
            'bill'=>$bill,
        ]);
    }

    /**
     * 领料信息
     * @return mixed
     */
    public function actionLingliao()
    {
        $this->modelClass = new WarehouseGoldBillGoodsForm();
        $tab = \Yii::$app->request->get('tab',2);
        $returnUrl = \Yii::$app->request->get('returnUrl',Url::to(['gold/index']));
        $searchModel = new SearchModel([
            'model' => $this->modelClass,
            'scenario' => 'default',
            'partialMatchAttributes' => [], // 模糊查询
            'defaultOrder' => [
                'id' => SORT_DESC
            ],
            'pageSize' => $this->pageSize,
            'relations' => [
                'bill' => ['audit_time'],
            ]
        ]);
        $dataProvider = $searchModel
            ->search(Yii::$app->request->queryParams,['created_at']);
        $created_at = $searchModel->created_at;
        if (!empty($created_at)) {
            $dataProvider->query->andFilterWhere(['>=',WarehouseGoldBillGoodsForm::tableName().'.created_at', strtotime(explode('/', $created_at)[0])]);//起始时间
            $dataProvider->query->andFilterWhere(['<',WarehouseGoldBillGoodsForm::tableName().'.created_at', (strtotime(explode('/', $created_at)[1]) + 86400)] );//结束时间
        }
        $id = \Yii::$app->request->get('id');
        $gold = WarehouseGold::findOne(['id'=>$id]);
        $dataProvider->query->andWhere(['=', 'gold_sn', $gold->gold_sn]);
        $dataProvider->query->andWhere(['>',WarehouseGoldBillGoodsForm::tableName().'.status',-1]);

        $dataProvider->query->andWhere(['=', 'bill.bill_type', GoldBillTypeEnum::GOLD_C]);

        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'gold' => $gold,
            'tab' => $tab,
            'tabList'=>\Yii::$app->warehouseService->gold->menuTabList($id, $returnUrl),
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

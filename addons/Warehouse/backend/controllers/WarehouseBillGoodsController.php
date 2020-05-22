<?php

namespace addons\Warehouse\backend\controllers;

use addons\Warehouse\common\models\WarehouseBill;
use Yii;
use common\traits\Curd;
use common\helpers\Url;
use common\models\base\SearchModel;
use addons\Warehouse\common\models\WarehouseBillGoods;


/**
 * WarehouseBillGoodsController implements the CRUD actions for WarehouseBillGoodsController model.
 */
class WarehouseBillGoodsController extends BaseController
{
    use Curd;
    public $modelClass = WarehouseBillGoods::class;
    /**
     * Lists all WarehouseBillGoods models.
     * @return mixed
     */
    public function actionIndex()
    {

        $bill_id = Yii::$app->request->get('bill_id');
        $tab = Yii::$app->request->get('tab',2);
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['warehouser-bill/index']));
        $searchModel = new SearchModel([
            'model' => $this->modelClass,
            'scenario' => 'default',
            'partialMatchAttributes' => [], // 模糊查询
            'defaultOrder' => [
                'id' => SORT_DESC
            ],
            'pageSize' => $this->pageSize,
            'relations' => []
        ]);

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['=', 'bill_id', $bill_id]);
        $dataProvider->query->andWhere(['>',WarehousebillGoods::tableName().'.status',-1]);
        $billInfo = WarehouseBill::find()->where(['id'=>$bill_id])->one();
        $billGoods = $dataProvider->getModels();
        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'billInfo' => $billInfo,
            'billGoods' => $billGoods,
            'tabList'=>\Yii::$app->warehouseService->warehouseBill->menuTabList($bill_id,$returnUrl),
            'tab' => $tab,
        ]);
    }

}

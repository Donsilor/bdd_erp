<?php

namespace addons\Warehouse\backend\controllers;

use addons\Warehouse\common\models\WarehouseBill;
use common\models\base\SearchModel;
use common\traits\Curd;


/**
 * Attribute
 *
 * Class AttributeController
 * @package backend\modules\goods\controllers
 */
class WarehouseBillMController extends BaseController
{
    use Curd;
    public $modelClass = WarehouseBill::class;

    /**
     * 调拨单
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
                'member' => ['username'],

            ]
        ]);

        $dataProvider = $searchModel
            ->search(\Yii::$app->request->queryParams,['updated_at']);

        $updated_at = $searchModel->updated_at;
        if (!empty($updated_at)) {
            $dataProvider->query->andFilterWhere(['>=',Warehousebill::tableName().'.updated_at', strtotime(explode('/', $updated_at)[0])]);//起始时间
            $dataProvider->query->andFilterWhere(['<',Warehousebill::tableName().'.updated_at', (strtotime(explode('/', $updated_at)[1]) + 86400)] );//结束时间
        }

        $dataProvider->query->andWhere(['>',Warehousebill::tableName().'.status',-1]);

        //导出
        if(\Yii::$app->request->get('action') === 'export'){
            $this->getError($dataProvider);
        }
        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }


}

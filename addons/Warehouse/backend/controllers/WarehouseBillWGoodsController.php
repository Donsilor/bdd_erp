<?php

namespace addons\Warehouse\backend\controllers;


use Yii;
use common\traits\Curd;
use common\helpers\Url;
use common\models\base\SearchModel;
use addons\Warehouse\common\enums\BillTypeEnum;
use addons\Warehouse\common\models\WarehouseBill;
use addons\Warehouse\common\forms\WarehouseBillWForm;
use addons\Warehouse\common\models\WarehouseBillGoods;


/**
 * WarehouseBillController implements the CRUD actions for WarehouseBillController model.
 */
class WarehouseBillWGoodsController extends BaseController
{
    use Curd;
    public $modelClass = WarehouseBillGoods::class;
    public $billType = BillTypeEnum::BILL_TYPE_W;
    /**
     * Lists all StyleChannel models.
     * @return mixed
     */
    public function actionIndex()
    {
        $bill_id = Yii::$app->request->get('bill_id');
        $tab = Yii::$app->request->get('tab',2);
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['warehouse-bill-w/index']));
        
        $bill = WarehouseBill::find()->where(['id'=>$bill_id])->one();
        
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
            ->search(Yii::$app->request->queryParams);
        
        $dataProvider->query->andWhere(['=',WarehousebillGoods::tableName().'.bill_id',$bill_id]);
        //$dataProvider->query->andWhere(['>',Warehousebill::tableName().'.status',-1]);
                
        
        return $this->render($this->action->id, [
                'dataProvider' => $dataProvider,
                'searchModel' => $searchModel,
                'bill'=>$bill,
                'tab' =>$tab,
                'tabList'=>\Yii::$app->warehouseService->billW->menuTabList($bill_id,$returnUrl),
                'returnUrl'=>$returnUrl
        ]);        
        
    } 
    
}

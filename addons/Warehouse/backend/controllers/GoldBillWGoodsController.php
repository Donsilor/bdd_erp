<?php

namespace addons\Warehouse\backend\controllers;

use addons\Warehouse\common\forms\WarehouseGoldBillWForm;
use Yii;
use common\traits\Curd;
use common\helpers\Url;
use common\models\base\SearchModel;
use addons\Warehouse\common\enums\GoldBillTypeEnum;
use addons\Warehouse\common\models\WarehouseGoldBillGoods;
use addons\Warehouse\common\forms\WarehouseBillWForm;
use addons\Warehouse\common\enums\PandianStatusEnum;

/**
 * WarehouseBillController implements the CRUD actions for WarehouseBillController model.
 */
class GoldBillWGoodsController extends BaseController
{
    use Curd;
    public $modelClass = WarehouseGoldBillGoods::class;
    public $billType = GoldBillTypeEnum::GOLD_W;
    /**
     * Lists all StyleChannel models.
     * @return mixed
     */
    public function actionIndex()
    {
        $bill_id = Yii::$app->request->get('bill_id');
        $tab = Yii::$app->request->get('tab',2);
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['gold-bill-w/index']));
        $bill = WarehouseGoldBillWForm::find()->where(['id'=>$bill_id])->one();
        
        $searchModel = new SearchModel([
                'model' => $this->modelClass,
                'scenario' => 'default',
                'partialMatchAttributes' => [], // 模糊查询
                'defaultOrder' => [
                        'id' => SORT_DESC
                ],
                'pageSize' =>  $this->getPageSize(15),
                'relations' => [

                ]
        ]);
        
        $dataProvider = $searchModel
            ->search(Yii::$app->request->queryParams);
        
        $dataProvider->query->andWhere(['=',WarehouseGoldBillGoods::tableName().'.bill_id',$bill_id]);
        $dataProvider->query->andWhere(['>',WarehouseGoldBillGoods::tableName().'.status',PandianStatusEnum::SAVE]);
        
        return $this->render($this->action->id, [
                'dataProvider' => $dataProvider,
                'searchModel' => $searchModel,
                'bill'=>$bill,
                'tab' =>$tab,
                'tabList'=>\Yii::$app->warehouseService->goldBill->menuTabList($bill_id,$this->billType,$returnUrl),
                'returnUrl'=>$returnUrl
        ]);        
        
    } 
    
}

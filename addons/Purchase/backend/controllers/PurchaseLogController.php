<?php

namespace addons\Purchase\backend\controllers;

use addons\Purchase\common\models\Purchase;
use addons\Purchase\common\models\PurchaseLog;
use common\helpers\Url;
use Yii;
use common\traits\Curd;
use common\models\base\SearchModel;



/**
 * PurchaseChannelController implements the CRUD actions for PurchaseChannel model.
 */
class PurchaseLogController extends BaseController
{
    use Curd;
    
    public $modelClass = PurchaseLog::class;
    /**
     * Lists all PurchaseChannel models.
     * @return mixed
     */
    public function actionIndex()
    {
        $purchase_id = Yii::$app->request->get('purchase_id');
        $tab = Yii::$app->request->get('tab',3);
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['purchase/index']));
        
        $purchase = Purchase::find()->where(['id'=>$purchase_id])->one();
        $searchModel = new SearchModel([
                'model' => $this->modelClass,
                'scenario' => 'default',
                'partialMatchAttributes' => ['log_msg'], // 模糊查询
                'defaultOrder' => [
                        'id' => SORT_DESC
                ],
                'pageSize' => $this->pageSize,
                
        ]);
        
        $dataProvider = $searchModel
        ->search(Yii::$app->request->queryParams);
        
        $dataProvider->query->andWhere(['=',PurchaseLog::tableName().'.purchase_id',$purchase_id]);
        
        return $this->render('index', [
                'dataProvider' => $dataProvider,
                'searchModel' => $searchModel,
                'purchase' => $purchase,
                'tab'=>$tab,
                'tabList'=>\Yii::$app->purchaseService->purchase->menuTabList($purchase_id,$returnUrl),                
        ]);
    }
    
    
}

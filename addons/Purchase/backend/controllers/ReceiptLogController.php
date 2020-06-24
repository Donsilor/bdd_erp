<?php

namespace addons\Purchase\backend\controllers;

use addons\Purchase\common\models\PurchaseReceipt;
use addons\Purchase\common\models\PurchaseReceiptLog;
use common\helpers\Url;
use Yii;
use common\traits\Curd;
use common\models\base\SearchModel;



/**
 * ReceiptLogController implements the CRUD actions for PurchaseReceiptLog model.
 */
class ReceiptLogController extends BaseController
{
    use Curd;
    
    public $modelClass = PurchaseReceiptLog::class;
    /**
     * Lists all PurchaseReceiptLog models.
     * @return mixed
     */
    public function actionIndex()
    {
        $receipt_id = Yii::$app->request->get('receipt_id');
        $tab = Yii::$app->request->get('tab',3);
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['receipt/index']));
        
        $receipt = PurchaseReceipt::find()->where(['id'=>$receipt_id])->one();
        $searchModel = new SearchModel([
                'model' => $this->modelClass,
                'scenario' => 'default',
                'partialMatchAttributes' => ['log_msg'], // 模糊查询
                'defaultOrder' => [
                        'id' => SORT_DESC
                ],
                'pageSize' => $this->pageSize,
                
        ]);
        
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['=', PurchaseReceiptLog::tableName().'.receipt_id', $receipt_id]);
        
        return $this->render('index', [
                'dataProvider' => $dataProvider,
                'searchModel' => $searchModel,
                'receipt' => $receipt,
                'tab'=>$tab,
                'tabList'=>\Yii::$app->purchaseService->receipt->menuTabList($receipt_id, $receipt->purchase_type, $returnUrl),
        ]);
    }
    
    
}

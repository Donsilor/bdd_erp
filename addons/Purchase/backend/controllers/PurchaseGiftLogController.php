<?php

namespace addons\Purchase\backend\controllers;

use Yii;
use common\traits\Curd;
use common\models\base\SearchModel;
use addons\Purchase\common\models\PurchaseGift;
use addons\Purchase\common\models\PurchaseGiftLog;

/**
 * 采购日志
 * 
 * Class PurchaseLogController
 * @package addons\Purchase\backend\controllers
 */
class PurchaseGiftLogController extends BaseController
{
    use Curd;
    /**
     * @var PurchaseGiftLog
     */
    public $modelClass = PurchaseGiftLog::class;

    /**
     * Lists all PurchaseChannel models.
     * @return mixed
     */
    public function actionIndex()
    {
        $purchase_id = Yii::$app->request->get('purchase_id');     
        
        $purchase = PurchaseGift::find()->where(['id'=>$purchase_id])->one();
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
        
        $dataProvider->query->andWhere(['=',PurchaseGiftLog::tableName().'.purchase_id',$purchase_id]);
        
        return $this->render('index', [
                'dataProvider' => $dataProvider,
                'searchModel' => $searchModel,
                'purchase' => $purchase,
                'tab'=>Yii::$app->request->get('tab',3),
                'tabList'=>\Yii::$app->purchaseService->gift->menuTabList($purchase_id,$this->returnUrl),
        ]);
    }
    
    
}

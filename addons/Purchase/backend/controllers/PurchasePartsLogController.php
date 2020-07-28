<?php

namespace addons\Purchase\backend\controllers;

use Yii;
use common\traits\Curd;
use common\models\base\SearchModel;
use addons\Purchase\common\models\PurchaseParts;
use addons\Purchase\common\models\PurchasePartsLog;

/**
 * 采购日志
 * 
 * Class PurchaseLogController
 * @package addons\Purchase\backend\controllers
 */
class PurchasePartsLogController extends BaseController
{
    use Curd;
    /**
     * @var PurchaseLog
     */
    public $modelClass = PurchasePartsLog::class;

    /**
     * Lists all PurchaseChannel models.
     * @return mixed
     */
    public function actionIndex()
    {
        $purchase_id = Yii::$app->request->get('purchase_id');     
        
        $purchase = PurchaseParts::find()->where(['id'=>$purchase_id])->one();
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
        
        $dataProvider->query->andWhere(['=',PurchasePartsLog::tableName().'.purchase_id',$purchase_id]);
        
        return $this->render('index', [
                'dataProvider' => $dataProvider,
                'searchModel' => $searchModel,
                'purchase' => $purchase,
                'tab'=>Yii::$app->request->get('tab',3),
                'tabList'=>\Yii::$app->purchaseService->parts->menuTabList($purchase_id,$this->returnUrl),
        ]);
    }
    
    
}

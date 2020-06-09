<?php

namespace addons\Purchase\backend\controllers;

use Yii;
use common\traits\Curd;
use common\models\base\SearchModel;
use addons\Purchase\common\models\PurchaseApply;
use addons\Purchase\common\models\PurchaseApplyLog;

/**
 * 采购申请日志
 * 
 * Class PurchaseLogController
 * @package addons\Purchase\backend\controllers
 */
class PurchaseApplyLogController extends BaseController
{
    use Curd;
    /**
     * @var PurchaseLog
     */
    public $modelClass = PurchaseApplyLog::class;

    /**
     * Lists all PurchaseChannel models.
     * @return mixed
     */
    public function actionIndex()
    {
        $apply_id = Yii::$app->request->get('apply_id');     
        
        $apply = PurchaseApply::find()->where(['id'=>$apply_id])->one();
        $searchModel = new SearchModel([
                'model' => $this->modelClass,
                'scenario' => 'default',
                'partialMatchAttributes' => ['log_msg'], // 模糊查询
                'defaultOrder' => [
                        'id' => SORT_DESC
                ],
                'pageSize' => $this->getPageSize(),
                
        ]);
        
        $dataProvider = $searchModel
            ->search(Yii::$app->request->queryParams);
        
        $dataProvider->query->andWhere(['=',PurchaseApplyLog::tableName().'.apply_id',$apply_id]);
        
        return $this->render('index', [
                'dataProvider' => $dataProvider,
                'searchModel' => $searchModel,
                'apply' => $apply,
                'tab'=>Yii::$app->request->get('tab',3),
                'tabList'=>\Yii::$app->purchaseService->apply->menuTabList($apply_id,$this->returnUrl),                
        ]);
    }
    
    
}

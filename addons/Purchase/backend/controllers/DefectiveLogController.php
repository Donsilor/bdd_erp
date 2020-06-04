<?php

namespace addons\Purchase\backend\controllers;


use addons\Purchase\common\enums\PurchaseTypeEnum;
use Yii;
use common\traits\Curd;
use common\models\base\SearchModel;
use addons\Purchase\common\models\PurchaseDefective;
use addons\Purchase\common\models\PurchaseDefectiveLog;
use common\helpers\Url;



/**
 * DefectiveLogController implements the CRUD actions for PurchaseDefectiveLog model.
 */
class DefectiveLogController extends BaseController
{
    use Curd;
    
    public $modelClass = PurchaseDefectiveLog::class;

    /**
     * Lists all PurchaseDefectiveLog models.
     * @return mixed
     */
    public function actionIndex()
    {
        $defective_id = Yii::$app->request->get('defective_id');
        $tab = Yii::$app->request->get('tab',3);
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['purchase-defective/index']));
        
        $defective = PurchaseDefective::find()->where(['id'=>$defective_id])->one();
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
        $dataProvider->query->andWhere(['=', PurchaseDefectiveLog::tableName().'.defective_id', $defective_id]);
        
        return $this->render('index', [
                'dataProvider' => $dataProvider,
                'searchModel' => $searchModel,
                'purchase_defective' => $defective,
                'tab'=>$tab,
                'tabList'=>\Yii::$app->purchaseService->defective->menuTabList($defective_id, $defective->purchase_type, $returnUrl),
        ]);
    }
    
    
}

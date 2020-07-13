<?php

namespace addons\Sales\backend\controllers;

use Yii;
use common\traits\Curd;
use common\models\base\SearchModel;
use addons\Sales\common\forms\DistributionOrderForm;

/**
 * 待配货订单
 *
 * Class OrderLogController
 * @package addons\Order\backend\controllers
 */
class DistributionOrderController extends BaseController
{
    use Curd;
    /**
     * @var DistributionOrderForm
     */
    public $modelClass = DistributionOrderForm::class;
    
    /**
     * Lists all Order models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SearchModel([
                'model' => $this->modelClass,
                'scenario' => 'default',
                'partialMatchAttributes' => ['log_msg'], // 模糊查询
                'defaultOrder' => [
                     'id' => SORT_DESC
                ],
                'pageSize' => $this->getPageSize(),
                'relations' => [
                    'account' => ['order_amount'],
                    'address' => [],
                ]
                
        ]);
        
        $dataProvider = $searchModel
            ->search(Yii::$app->request->queryParams);
        
        //$dataProvider->query->andWhere(['=',DistributionOrderForm::tableName().'.order_id',$order_id]);
        
        return $this->render('index', [
                'dataProvider' => $dataProvider,
                'searchModel' => $searchModel,
                //'order' => $order,
                'tab'=>Yii::$app->request->get('tab',2),
                //'tabList'=>\Yii::$app->salesService->order->menuTabList($order_id,$this->returnUrl),
        ]);
    }
    
    
}

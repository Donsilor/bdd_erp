<?php

namespace addons\Sales\backend\controllers;

use Yii;
use common\traits\Curd;
use common\models\base\SearchModel;
use addons\Sales\common\models\Order;
use addons\Sales\common\models\OrderLog;

/**
 * 订单日志
 *
 * Class OrderLogController
 * @package addons\Order\backend\controllers
 */
class OrderLogController extends BaseController
{
    use Curd;
    /**
     * @var OrderLog
     */
    public $modelClass = OrderLog::class;
    
    /**
     * Lists all OrderChannel models.
     * @return mixed
     */
    public function actionIndex()
    {
        $order_id = Yii::$app->request->get('order_id');
        
        $order = Order::find()->where(['id'=>$order_id])->one();
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
        
        $dataProvider->query->andWhere(['=',OrderLog::tableName().'.order_id',$order_id]);
        
        return $this->render('index', [
                'dataProvider' => $dataProvider,
                'searchModel' => $searchModel,
                'order' => $order,
                'tab'=>Yii::$app->request->get('tab',2),
                'tabList'=>\Yii::$app->salesService->order->menuTabList($order_id,$this->returnUrl),
        ]);
    }
    
    
}

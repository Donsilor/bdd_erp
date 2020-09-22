<?php

namespace addons\Gdzb\backend\controllers;

use addons\Gdzb\common\models\OrderRefund;
use addons\Gdzb\common\models\RefundLog;
use Yii;
use common\traits\Curd;
use common\models\base\SearchModel;
use addons\Gdzb\common\models\Order;
use addons\Gdzb\common\models\OrderLog;

/**
 * 订单日志
 *
 * Class OrderLogController
 * @package addons\Order\backend\controllers
 */
class RefundLogController extends BaseController
{
    use Curd;
    /**
     * @var OrderLog
     */
    public $modelClass = RefundLog::class;
    
    /**
     * Lists all OrderChannel models.
     * @return mixed
     */
    public function actionIndex()
    {
        $refund_id = Yii::$app->request->get('refund_id');
        
        $refund = OrderRefund::find()->where(['id'=>$refund_id])->one();
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
        
        $dataProvider->query->andWhere(['=',RefundLog::tableName().'.refund_id',$refund_id]);
        
        return $this->render('index', [
                'dataProvider' => $dataProvider,
                'searchModel' => $searchModel,
                'refund' => $refund,
                'tab'=>Yii::$app->request->get('tab',2),
                'tabList'=>\Yii::$app->gdzbService->orderRefund->menuTabList($refund_id,$this->returnUrl),
        ]);
    }
    
    
}

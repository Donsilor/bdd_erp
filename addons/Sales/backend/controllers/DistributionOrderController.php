<?php

namespace addons\Sales\backend\controllers;

use addons\Sales\common\models\OrderGoods;
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

    /**
     * 详情展示页
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView()
    {
        $id = Yii::$app->request->get('id');
        $tab = Yii::$app->request->get('tab',1);
        $model = $this->findModel($id);

        $dataProvider = null;
        if (!is_null($id)) {
            $searchModel = new SearchModel([
                'model' => OrderGoods::class,
                'scenario' => 'default',
                'partialMatchAttributes' => [], // 模糊查询
                'defaultOrder' => [
                    'id' => SORT_DESC
                ],
                'pageSize' => 1000,
            ]);

            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

            $dataProvider->query->andWhere(['=', 'order_id', $id]);

            $dataProvider->setSort(false);
        }
        return $this->render($this->action->id, [
            'model' => $model,
            'dataProvider' => $dataProvider,
            'returnUrl'=>$this->returnUrl,
            'tab'=>$tab,
        ]);
    }
}

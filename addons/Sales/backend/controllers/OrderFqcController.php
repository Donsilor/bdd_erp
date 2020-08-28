<?php

namespace addons\Sales\backend\controllers;

use Yii;
use common\traits\Curd;
use common\models\base\SearchModel;
use addons\Sales\common\models\Order;
use addons\Sales\common\models\OrderGoods;
use addons\Sales\common\forms\OrderFqcForm;
use addons\Sales\common\enums\DeliveryStatusEnum;
use addons\Sales\common\enums\DistributeStatusEnum;

/**
 * Default controller for the `orderFqc` module
 */
class OrderFqcController extends BaseController
{
    use Curd;
    
    /**
     * @var Order
     */
    public $modelClass = Order::class;
    
    /**
     * Renders the index view for the module
     * @return string
     * @throws
     */
    public function actionIndex()
    {
        $order_status = Yii::$app->request->get('order_status', -1);        
        $searchModel = new SearchModel([
                'model' => $this->modelClass,
                'scenario' => 'default',
                'partialMatchAttributes' => [], // 模糊查询
                'defaultOrder' => [
                        'id' => SORT_DESC,
                ],
                'pageSize' => $this->pageSize,
                'relations' => [
                        'account' => ['order_amount'],
                        'address' => [],
                ]
        ]);
        
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, ['created_at', 'customer_mobile', 'customer_email', 'order_status']);
        $searchParams = Yii::$app->request->queryParams['SearchModel'] ?? [];
        if($order_status != -1) {
            $dataProvider->query->andWhere(['=', 'order_status', $order_status]);
        }
        // 联系人搜索
        if(!empty($searchParams['customer_mobile'])) {
            $where = [ 'or',
                    ['like', Order::tableName().'.customer_mobile', $searchParams['customer_mobile']],
                    ['like', Order::tableName().'.customer_email', $searchParams['customer_mobile']]
            ];            
            $dataProvider->query->andWhere($where);
        }        
        //创建时间过滤
        if (!empty($searchParams['created_at'])) {
            list($start_date, $end_date) = explode('/', $searchParams['created_at']);
            $dataProvider->query->andFilterWhere(['between', Order::tableName().'.created_at', strtotime($start_date), strtotime($end_date) + 86400]);
        }
        $dataProvider->query->andWhere(['=',Order::tableName().'.distribute_status', DistributeStatusEnum::HAS_PEIHUO]);
        $dataProvider->query->andWhere(['>=',Order::tableName().'.delivery_status', DeliveryStatusEnum::SAVE]);
        return $this->render($this->action->id, [
                'dataProvider' => $dataProvider,
                'searchModel' => $searchModel,
        ]);
    }

    /**
     * 详情展示页
     * @return string
     * @throws
     */
    public function actionView()
    {
        $id = Yii::$app->request->get('id');
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
                'tab'=>\Yii::$app->request->get('tab',1),
                'tabList'=>\Yii::$app->salesService->orderFqc->menuTabList($id,$this->returnUrl),
                'returnUrl'=>$this->returnUrl,
        ]);
    }

    /**
     * ajax FQC质检
     *
     * @return mixed|string|\yii\web\Response
     * @throws \yii\base\ExitException
     */
    public function actionAjaxFqc()
    {
        $id = \Yii::$app->request->get('id');
        $order = $this->findModel($id) ?? new Order();
        $model = new OrderFqcForm();
        $model->order_id = $order->id;
        $model->order_sn = $order->order_sn;
        $model->is_pass = \Yii::$app->request->get('is_pass');
        if($model->is_pass){
            try{
                $trans = \Yii::$app->db->beginTransaction();

                \Yii::$app->salesService->orderFqc->orderFqc($model);
                $trans->commit();
                return $this->message('操作成功', $this->redirect(['index']), 'success');
            }catch (\Exception $e){
                $trans->rollBack();
                return $this->message($e->getMessage(), $this->redirect(\Yii::$app->request->referrer), 'error');
            }
        }
        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(\Yii::$app->request->post())) {
            $isNewRecord = $model->isNewRecord;
            try{
                $trans = \Yii::$app->db->beginTransaction();

                \Yii::$app->salesService->orderFqc->orderFqc($model);

                $trans->commit();
                if($isNewRecord) {
                    return $this->message("保存成功", $this->redirect(['view', 'id' => $model->id]), 'success');
                }else {
                    return $this->message('保存成功', $this->redirect(Yii::$app->request->referrer), 'success');
                }
            }catch (\Exception $e){
                $trans->rollBack();
                return $this->message($e->getMessage(), $this->redirect(\Yii::$app->request->referrer), 'error');
            }
        }
        return $this->renderAjax($this->action->id, [
            'model' => $model,
        ]);
    }
}


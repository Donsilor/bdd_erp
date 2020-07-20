<?php

namespace addons\Sales\backend\controllers;

use Yii;
use common\traits\Curd;
use common\models\base\SearchModel;
use addons\Sales\common\models\Order;
use addons\Sales\common\models\OrderGoods;
use addons\Sales\common\models\OrderAddress;
use addons\Sales\common\forms\ShippingForm;
use addons\Sales\common\forms\OrderFqcForm;
use addons\Sales\common\enums\DeliveryStatusEnum;

/**
 * Default controller for the `Order` module
 */
class ShippingController extends BaseController
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
        $dataProvider->query->andWhere(['=',Order::tableName().'.delivery_status', DeliveryStatusEnum::TO_SEND]);
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
                'tabList'=>\Yii::$app->salesService->shipping->menuTabList($id,$this->returnUrl),
                'returnUrl'=>$this->returnUrl,
        ]);
    }

    /**
     * ajax 发货
     *
     * @return mixed|string|\yii\web\Response
     * @throws \yii\base\ExitException
     */
    public function actionAjaxShipping()
    {
        $id = \Yii::$app->request->get('id');
        $order = $this->findModel($id) ?? new Order();
        $address = OrderAddress::findOne($id) ?? new OrderAddress();
        $model = new ShippingForm();
        $model->order_sn = $order->order_sn;
        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(\Yii::$app->request->post())) {
            $isNewRecord = $model->isNewRecord;
            try{
                $trans = \Yii::$app->db->beginTransaction();

                \Yii::$app->salesService->shipping->orderShipping($model);

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

        $model->consignee = $address->firstname;
        $model->consignee_mobile = $address->mobile;
        $address->address_details = $address->country_name.$address->province_name.$address->city_name.$address->address_details;
        $model->consignee_address = $address->address_details;
        $model->express_id = $order->express_id;
        $model->consigner = \Yii::$app->user->identity->username??"";

        return $this->renderAjax($this->action->id, [
            'model' => $model,
        ]);
    }
}


<?php

namespace addons\Sales\backend\controllers;

use addons\Sales\common\enums\OrderStatusEnum;
use addons\Sales\common\forms\OrderForm;
use addons\Sales\common\forms\OrderGoodsForm;
use common\enums\AuditStatusEnum;
use common\enums\FlowStatusEnum;
use common\helpers\ArrayHelper;
use Yii;
use common\traits\Curd;
use addons\Sales\common\models\Order;
use common\models\base\SearchModel;
use addons\Sales\common\models\OrderGoods;
use common\helpers\ResultHelper;
use addons\Sales\common\models\OrderInvoice;
use addons\Sales\common\models\OrderAddress;
use addons\Sales\common\models\Customer;

/**
 * Default controller for the `order` module
 */
class OrderController extends BaseController
{
    use Curd;
    
    /**
     * @var Order
     */
    public $modelClass = OrderForm::class;

    public function actionTest()
    {
        Yii::$app->shopService->orderSync->syncOrder(1405);
        exit;
    }
    /**
     * Renders the index view for the module
     * @return string
     * @throws NotFoundHttpException
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
                        'creator' =>['username'],
                ]
        ]);
        
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, ['created_at', 'customer_mobile', 'customer_email']);
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
        if (!empty($searchParams['order_time'])) {
            list($start_date, $end_date) = explode('/', $searchParams['order_time']);
            $dataProvider->query->andFilterWhere(['between', Order::tableName().'.order_time', strtotime($start_date), strtotime($end_date) + 86400]);
        }        
        return $this->render($this->action->id, [
                'dataProvider' => $dataProvider,
                'searchModel' => $searchModel,
        ]);
    }
    /**
     * 创建订单
     * @return array|mixed
     */
    public function actionAjaxEdit()
    {
        $id = Yii::$app->request->get('id');
        $model = $this->findModel($id);
        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {            
            $isNewRecord = $model->isNewRecord;
            try{
                $trans = Yii::$app->trans->beginTransaction();
                $model = Yii::$app->salesService->order->createOrder($model);                
                $trans->commit();
                return $isNewRecord  
                    ? $this->message("创建成功", $this->redirect(['view', 'id' => $model->id]), 'success')
                    : $this->message("保存成功", $this->redirect(Yii::$app->request->referrer), 'success');
                
            }catch (\Exception $e) {
                $trans->rollback();
                return $this->message($e->getMessage(), $this->redirect(Yii::$app->request->referrer), 'error');
            }
        }
        
        return $this->renderAjax($this->action->id, [
                'model' => $model,
        ]);
        
    }   
    /**
     * 查询客户信息
     * @return array|\yii\db\ActiveRecord|NULL
     */
    public function actionAjaxGetCustomer()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        $mobile = Yii::$app->request->get('mobile');
        $email = Yii::$app->request->get('email');
        $channel_id = Yii::$app->request->get('channel_id');
        
        
        $model = Customer::find()->select(['id','realname','mobile','email','level','source_id'])
            ->where(['channel_id'=>$channel_id])
            ->andFilterWhere(['=','mobile',$mobile])
            ->andFilterWhere(['=','email',$email])
            ->asArray()->one();
        return ResultHelper::json(200,'查询成功',$model);
    }
    /**
     * 详情展示页
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView()
    {
        $id = Yii::$app->request->get('id');        
        $model = $this->findModel($id); 
        $model->getTargetType();
        $dataProvider = null;
        if (!is_null($id)) {
            $searchModel = new SearchModel([
                    'model' => OrderGoodsForm::class,
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
            //商品属性
            $models = $dataProvider->models;
            foreach ($models as & $goods){
                $attrs = $goods->attrs ?? [];
                $goods['attr'] = ArrayHelper::map($attrs,'attr_id','attr_value');
            }
        }
        return $this->render($this->action->id, [
                'model' => $model,
                'dataProvider' => $dataProvider,
                'tab'=>Yii::$app->request->get('tab',1),
                'tabList'=>Yii::$app->salesService->order->menuTabList($id,$this->returnUrl),
                'returnUrl'=>$this->returnUrl,
        ]);
    }
    
    /**
     * 取消订单
     * @throws Exception
     * @return array|mixed
     */
    public function actionDelete()
    {
        $ids = Yii::$app->request->post("ids", []);
        if(empty($ids) || !is_array($ids)) {
            return ResultHelper::json(422, '提交数据异常');
        } 
        
        try {
            $trans = Yii::$app->db->beginTransaction();                      
            foreach ($ids as $id) {
                
            }
            $trans->commit();
            return ResultHelper::json(200, '操作成功');   
        } catch (\Exception $e) {
            $trans->rollBack();
            return ResultHelper::json(422, '取消失败！'.$e->getMessage());
        }        
              
    }    
    /**
     * 分配跟单人
     * @return mixed|string|\yii\web\Response
     * @throws \yii\base\ExitException
     */
    public function actionAjaxFollower()
    {
        
    }


    /**
     * @return mixed
     * 申请审核
     */
    public function actionAjaxApply(){
        $id = \Yii::$app->request->get('id');
        $order_goods_count = OrderGoods::find()->where(['order_id'=>$id])->count();
        if($order_goods_count == 0){
            return $this->message('订单没有明细', $this->redirect(\Yii::$app->request->referrer), 'error');
        }
        $model = $this->findModel($id);
        $model = $model ?? new OrderForm();
        if($model->order_status != OrderStatusEnum::SAVE){
            return $this->message('订单不是保存状态', $this->redirect(\Yii::$app->request->referrer), 'error');
        }
        $model->getTargetType();
        try{
            $trans = Yii::$app->db->beginTransaction();
            if($model->targetType){
                //审批流程
                Yii::$app->services->flowType->createFlow($model->targetType,$id,$model->order_sn);
            }

            $model->order_status = OrderStatusEnum::PENDING;
            $model->audit_status = AuditStatusEnum::PENDING;
            if(false === $model->save()){
                return $this->message($this->getError($model), $this->redirect(\Yii::$app->request->referrer), 'error');
            }

            $trans->commit();
            return $this->message('操作成功', $this->redirect(\Yii::$app->request->referrer), 'success');
        }catch (\Exception $e){
            $trans->rollBack();
            return $this->message($e->getMessage(), $this->redirect(Yii::$app->request->referrer), 'error');
        }

    }

    /**
     * 订单审核
     * @return array
     * @throws \yii\db\Exception
     */
    public function actionAjaxAudit()
    {
        $id = Yii::$app->request->get('id');
        $model = $this->findModel($id);
        $model = $model ?? new OrderForm();
        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {
            try{
                $trans = Yii::$app->trans->beginTransaction();

                $model->getTargetType();
                if($model->targetType){
                    $audit = [
                        'audit_status' =>  $model->audit_status ,
                        'audit_time' => time(),
                        'audit_remark' => $model->audit_remark
                    ];
                    $res = \Yii::$app->services->flowType->flowAudit($model->targetType,$id,$audit);
                    //审批完结或者审批不通过才会走下面
					if($res->flow_status == FlowStatusEnum::COMPLETE || $res->flow_status == FlowStatusEnum::CANCEL){
                        $model->auditor_id = \Yii::$app->user->id;
                        $model->audit_time = time();
                        if($model->audit_status == AuditStatusEnum::PASS){
                            $model->order_status = OrderStatusEnum::CONFORMED;
                        }
                        if(false === $model->save()){
                            return $this->message($this->getError($model), $this->redirect(\Yii::$app->request->referrer), 'error');
                        }
                    }
                }else{
                    $model->auditor_id = \Yii::$app->user->id;
                    $model->audit_time = time();
                    if($model->audit_status == AuditStatusEnum::PASS){
                        $model->order_status = OrderStatusEnum::CONFORMED;
                    }
                    if(false === $model->save()){
                        return $this->message($this->getError($model), $this->redirect(\Yii::$app->request->referrer), 'error');
                    }
                }
                $trans->commit();
            }catch (\Exception $e){
                $trans->rollBack();
                return $this->message("审核失败:". $e->getMessage(),  $this->redirect(Yii::$app->request->referrer), 'error');
            }
            return $this->message("保存成功", $this->redirect(Yii::$app->request->referrer), 'success');
        }
        $model->audit_status = AuditStatusEnum::PASS;
        return $this->renderAjax($this->action->id, [
            'model' => $model,
        ]);
    }




    /**
     * 修改收货地址
     * @return \yii\web\Response|mixed|string|string
     */
    public function actionAjaxEditAddress()
    {
        $id = Yii::$app->request->get('id');
        $this->modelClass = OrderAddress::class;
        $model = $this->findModel($id);
        if($model->isNewRecord) {
            $model->order_id = $id;     
        }        
        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {
            try{
                $trans = Yii::$app->trans->beginTransaction();
                if(false === $model->save()) {
                    throw new \Exception($this->getError($model));
                }                
                $trans->commit();
                
                return $this->message("保存成功", $this->redirect(Yii::$app->request->referrer), 'success');                
            }catch (\Exception $e) {
                $trans->rollback();
                return $this->message($e->getMessage(), $this->redirect(Yii::$app->request->referrer), 'error');
            }
        }
        if(!$model->realname) {
            $model->realname = $model->order->customer_name ?? null;
        }
        if(!$model->mobile) {
            $model->mobile = $model->order->customer_mobile ?? null;
        }
        if(!$model->email) {
            $model->email = $model->order->customer_email ?? null;
        }
        if(!$model->country_id) {
            $model->country_id = $model->customer->country_id ?? null;
            $model->province_id = $model->customer->province_id ?? null;
            $model->city_id = $model->customer->city_id ?? null;
            $model->address_details = $model->customer->address_details ?? null;
        }
        return $this->renderAjax($this->action->id, [
                'model' => $model,
        ]);
    }
    
    /**
     * 修改收货地址
     * @return \yii\web\Response|mixed|string|string
     */
    public function actionAjaxEditInvoice()
    {
        $id = Yii::$app->request->get('id');
        $this->modelClass = OrderInvoice::class;
        $model = $this->findModel($id);
        if($model->isNewRecord) {
            $model->order_id = $id;
        }
        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {
            try{
                $trans = Yii::$app->trans->beginTransaction();
                if(false === $model->save()) {
                    throw new \Exception($this->getError($model));
                }                
                $trans->commit();
                
                return $this->message("保存成功", $this->redirect(Yii::$app->request->referrer), 'success');
                
            }catch (\Exception $e) {
                $trans->rollback();
                return $this->message($e->getMessage(), $this->redirect(Yii::$app->request->referrer), 'error');
            }
        }
        
        return $this->renderAjax($this->action->id, [
                'model' => $model,
        ]);
    }
        
    
}


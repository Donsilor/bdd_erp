<?php

namespace addons\Sales\backend\controllers;

use addons\Sales\common\enums\IsReturnEnum;
use common\helpers\ArrayHelper;
use Yii;
use common\traits\Curd;
use addons\Sales\common\models\Order;
use common\models\base\SearchModel;
use common\helpers\ResultHelper;
use addons\Sales\common\enums\OrderFromEnum;
use yii\web\UploadedFile;
use common\helpers\ExcelHelper;
use addons\Sales\common\forms\OrderImportForm;
use addons\Sales\common\forms\ExternalOrderForm;
use addons\Sales\common\forms\OrderGoodsForm;
use addons\Sales\common\models\Platform;

/**
 * Default controller for the `order` module
 */
class ExternalOrderController extends BaseController
{
    use Curd;
    
    /**
     * @var Order
     */
    public $modelClass = ExternalOrderForm::class;    
    
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
        $dataProvider->query->andWhere(['=', 'order_from', OrderFromEnum::FROM_EXTERNAL]);
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
    public function actionEdit()
    {
        $this->layout = '@backend/views/layouts/iframe';
        
        $id = Yii::$app->request->get('id');
        $platform_id = Yii::$app->request->get('platform_id');
        $model = $this->findModel($id);
        $isNewRecord = $model->isNewRecord;
        if($platform_id) {
            $model->platform_id = $platform_id; 
            $platform = Platform::find()->where(['id'=>$platform_id])->one();
            if($platform) {
                $model->language = $platform->language;
                $model->currency = $platform->currency;
                $model->sale_channel_id = $platform->channel_id;
                $model->pay_type = $platform->payment_id;
                $model->_platform = $platform;
            }else{
                return $this->message("系统异常", $this->redirect(Yii::$app->request->referrer), 'error');
            }            
        }  
        if($isNewRecord === false) {
            $model->other_fee = $model->account->other_fee;
            $model->arrive_amount = $model->account->arrive_amount;
        }
        // ajax 校验
        //$this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {           
            try{                
                $trans = Yii::$app->trans->beginTransaction();                
                $model = Yii::$app->salesService->order->createExternalOrder($model);
                $trans->commit();
                return $isNewRecord
                    ? $this->message("创建成功", $this->redirect(['view', 'id' => $model->id]), 'success')
                    : $this->message("保存成功", $this->redirect(Yii::$app->request->referrer), 'success');
                
            }catch (\Exception $e) {
                $trans->rollback();
                return ResultHelper::json(424, $e->getMessage());
            }
        }
        
        return $this->render($this->action->id, [
                'model' => $model,
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
                
                if ($goods->is_return == IsReturnEnum::HAS_RETURN){
                    $return[] = $goods->id;
                }
            }
        }
        return $this->render($this->action->id, [
                'model' => $model,
                'dataProvider' => $dataProvider,
                'tab'=>Yii::$app->request->get('tab',1),
                'tabList'=>Yii::$app->salesService->order->menuTabList($id,$this->returnUrl),
                'returnUrl'=>$this->returnUrl,
                'return'=>!empty($return)?json_encode($return):"",
        ]);
    } 
    /**
     * 导入订单
     * @return array|mixed
     */
    public function actionAjaxImport()
    {
        if(Yii::$app->request->get('download')) {
            $file = dirname(dirname(__FILE__)).'/resources/excel/港台订单模板导入.xlsx';
            $content = file_get_contents($file);
            if (!empty($content)) {
                header("Content-type:application/vnd.ms-excel");
                header("Content-Disposition: attachment;filename=港台订单模板导入".date("Ymd").".xlsx");
                header("Content-Transfer-Encoding: binary");
                exit($content);
            }
        }
        $model =  new OrderImportForm();
        // ajax 校验
        $this->activeFormValidate($model);
        
        if ($model->load(\Yii::$app->request->post())) {
            
            try{
                $trans = \Yii::$app->trans->beginTransaction();
                
                $model->file = UploadedFile::getInstance($model, 'file');
                Yii::$app->salesService->order->importExternalOrder($model);
                $trans->commit();
                return $this->message('导入成功', $this->redirect(Yii::$app->request->referrer), 'success');
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


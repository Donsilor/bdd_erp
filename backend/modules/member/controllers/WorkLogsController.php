<?php

namespace backend\modules\member\controllers;

use addons\Finance\common\models\OrderPay;
use addons\Sales\common\models\OrderAccount;
use addons\Sales\common\models\Payment;
use addons\Sales\common\models\SaleChannel;
use backend\controllers\BaseController;
use common\enums\PayTypeEnum;
use common\helpers\AmountHelper;
use common\helpers\ArrayHelper;
use common\helpers\ExcelHelper;
use common\helpers\PageHelper;
use common\helpers\StringHelper;
use common\models\member\WorkLogs;
use Yii;
use common\models\base\SearchModel;
use common\traits\Curd;
use addons\Finance\common\forms\OrderPayForm;
use addons\Sales\common\models\Order;
use addons\Sales\common\enums\OrderStatusEnum;
use addons\Sales\common\enums\PayStatusEnum;

/**
 *
 * 财务订单点款
 * Class OrderPayController
 * @package backend\modules\goods\controllers
 */
class OrderPayController extends BaseController
{
    use Curd;
    
    /**
     * @var BankPay
     */
    public $modelClass = WorkLogs::class;
    /**
     * @var int
     */
    
    
    
    /**
     * 首页
     *
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionIndex()
    {
        $searchModel = new SearchModel([
                'model' => $this->modelClass,
                'scenario' => 'default',
                'partialMatchAttributes' => [], // 模糊查询
                'defaultOrder' => [
                        'id' => SORT_DESC
                ],
                'pageSize' => $this->getPageSize(),
                'relations' => [
                    'account'=>["order_amount","pay_amount","paid_amount","currency"] ,
                    'payLogs'=>["pay_sn"]
                ]
        ]);
        
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        //导出
        if(Yii::$app->request->get('action') === 'export'){
            $queryIds = $dataProvider->query->select(WorkLogs::tableName().'.id');
            $this->actionExport($queryIds);
        }
        return $this->render('index', [
                'dataProvider' => $dataProvider,
                'searchModel' => $searchModel,
        ]);
    }
    /**
     * ajax编辑/创建
     *
     * @return mixed|string|\yii\web\Response
     * @throws \yii\base\ExitException
     */
    public function actionAjaxEdit()
    {
        $id = Yii::$app->request->get('id');
        $model = $this->findModel($id);
        $model = $model ?? new OrderPayForm();
        
        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {
            try{
                $trans = Yii::$app->db->beginTransaction();                
                $orderPay = \Yii::$app->financeService->orderPay->pay($model);                
                $trans->commit();
                return $this->message('点款成功,交易号：'.$orderPay->pay_sn, $this->redirect(['index']), 'success');
            }catch (\Exception $e){
                $trans->rollBack();
                return $this->message($e->getMessage(), $this->redirect(Yii::$app->request->referrer), 'error');
            }
            
        }
        
        return $this->renderAjax($this->action->id, [
                'model' => $model,
        ]);
    }


    /***
     * 导出Excel
     */
    public function actionExport($ids=null){
        if(!is_array($ids)){
            $ids = StringHelper::explodeIds($ids);
        }
        if(!$ids){
            return $this->message('ID不为空', $this->redirect(['index']), 'warning');
        }
        $list = $this->getData($ids);
        // [名称, 字段名, 类型, 类型规则]
        $header = [
            ['订单时间', 'order_time', 'date','Y-m-d H:i:s'],
            ['订单编号', 'order_sn', 'text'],
            ['客户姓名', 'customer_name', 'text'],
            ['销售渠道', 'channel_name', 'text'],
            ['订单货币', 'currency', 'text'],
            ['应付金额', 'pay_amount' , 'text'],
            ['实际支付金额', 'paid_amount' , 'text'],
            ['剩余尾款', 'unpay_amount' , 'text'],
            ['订单状态', 'order_status' , 'text'],
            ['支付方式', 'payment_name' , 'text'],
            ['支付状态', 'pay_status' , 'text'],
            ['支付单号', 'pay_sn' , 'text'],
            ['点款人', 'pay_user' , 'text'],
            ['点款时间', 'pay_time' , 'date','Y-m-d H:i:s'],
        ];

        return ExcelHelper::exportData($list, $header, '订单点款_' . date('YmdHis',time()));

    }

    /**
     *
     * @return bool
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function getData($ids)
    {
        $select = ['o.*','p.name as payment_name','a.pay_amount','a.paid_amount','a.currency','c.name as channel_name','pay.creator as pay_user'];
        $query = Order::find()->alias('o')
            ->innerJoin(OrderPay::tableName().' pay','pay.order_id = o.id')
            ->leftJoin(OrderAccount::tableName().' a','o.id = a.order_id')
            ->leftJoin(Payment::tableName().' p','o.pay_type = p.id')
            ->leftJoin(SaleChannel::tableName().' c','o.sale_channel_id=c.id')
            ->where(['o.id' => $ids])
            ->select($select);
        $lists = PageHelper::findAll($query, 100);

        foreach ($lists as &$list){
            $unpay_amount = $list['pay_amount'] - $list['paid_amount'];
            $list['unpay_amount'] = AmountHelper::outputAmount($unpay_amount,2,$list['currency']);
            $list['pay_amount'] = AmountHelper::outputAmount($list['pay_amount'],2,$list['currency']);
            $list['paid_amount'] = AmountHelper::outputAmount($list['paid_amount'],2,$list['currency']);
            $list['order_status'] = OrderStatusEnum::getValue($list['order_status']);
            $list['pay_status'] = PayStatusEnum::getValue($list['pay_status']);
        }
        return $lists;

    }
    
    
    
}

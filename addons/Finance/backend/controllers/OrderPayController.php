<?php

namespace addons\Finance\backend\controllers;

use addons\Finance\common\models\OrderPay;
use common\helpers\ExcelHelper;
use common\helpers\PageHelper;
use common\helpers\StringHelper;
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
    public $modelClass = OrderPayForm::class;
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
        $this->modelClass = Order::class;
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
        $dataProvider->query->andWhere(['or',['=',Order::tableName().".pay_status",PayStatusEnum::HAS_PAY],['=',Order::tableName().".order_status",OrderStatusEnum::CONFORMED]]);
        //$dataProvider->query->andWhere(['=',Order::tableName().".pay_status",PayStatusEnum::NO_PAY]);
        
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
        list($list,) = $this->getData($ids);
        // [名称, 字段名, 类型, 类型规则]
        $header = [
            ['订单时间', 'add_time', 'date',''],
            ['订单编号', 'goods_id', 'text'],
            ['客户姓名', 'style_sn', 'text'],
            ['应付金额', 'product_type_name' , 'text'],
            ['实际支付金额', 'style_cate_name' , 'text'],
            ['剩余尾款', 'warehouse_name' , 'text'],
            ['订单状态', 'material' , 'text'],
            ['支付方式', 'gold_weight' , 'text'],
            ['支付状态', 'main_stone_type' , 'text'],
            ['支付单号', 'diamond_carat' , 'text'],
            ['点款人', 'main_stone_num' , 'text'],
        ];

        return ExcelHelper::exportData($list, $header, '退货返厂单_' . date('YmdHis',time()));

    }

    /**
     *
     * @return bool
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function getData($ids)
    {
        $select = ['p.*','o.order_sn','o.created_at as add_time'];
        $query = OrderPay::find()->alias('p')
            ->leftJoin(Order::tableName().' o','o.id=p.order_id')
            ->select($select);
        $lists = PageHelper::findAll($query, 100);

        foreach ($lists as &$list){
            $list['bill_status'] = BillStatusEnum::getValue($list['bill_status']);
            $list['material'] = \Yii::$app->attr->valueName($list['material']);
            $list['main_stone_type'] = \Yii::$app->attr->valueName($list['main_stone_type']);
            $diamond_color = $list['diamond_color'] ? \Yii::$app->attr->valueName($list['diamond_color']): '无';
            $diamond_clarity = $list['diamond_clarity'] ?\Yii::$app->attr->valueName($list['diamond_clarity']): '无';
            $diamond_cut = $list['diamond_cut'] ?\Yii::$app->attr->valueName($list['diamond_cut']): '无';
            $diamond_polish = $list['diamond_polish'] ?\Yii::$app->attr->valueName($list['diamond_polish']): '无';
            $diamond_symmetry = $list['diamond_symmetry'] ?\Yii::$app->attr->valueName($list['diamond_symmetry']): '无';
            $diamond_fluorescence = $list['diamond_fluorescence'] ?\Yii::$app->attr->valueName($list['diamond_fluorescence']): '无';
            $list['main_stone_info'] = $diamond_color . '/' . $diamond_clarity . '/' . $diamond_cut . '/'
                . $diamond_polish . '/' . $diamond_symmetry . '/' . $diamond_fluorescence;


        }
        return $list;



    }
    
    
    
}

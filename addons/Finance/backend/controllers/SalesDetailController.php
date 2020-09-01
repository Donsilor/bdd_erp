<?php

namespace addons\Finance\backend\controllers;


use addons\Finance\common\models\BankPay;
use addons\Finance\common\models\SalesDetail;
use addons\Sales\common\enums\ReturnByEnum;
use addons\Sales\common\models\SaleChannel;
use addons\Style\common\models\ProductType;
use common\enums\TargetType;
use common\helpers\ExcelHelper;
use common\helpers\PageHelper;
use common\helpers\StringHelper;
use common\models\common\Department;

use Yii;
use common\models\base\SearchModel;
use common\traits\Curd;



/**
 *
 *
 * Class PurchaseController
 * @package backend\modules\goods\controllers
 */
class SalesDetailController extends BaseController
{
    use Curd;

    /**
     * @var BankPay
     */
    public $modelClass = SalesDetail::class;

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
            ]
        ]);

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams,['pay_time','delivery_time','refund_time','return_time']);
        //过滤平台收款日期
        if (!empty($searchParams['pay_time'])) {
            list($start_date, $end_date) = explode('/', $searchParams['pay_time']);
            $dataProvider->query->andFilterWhere(['between', SalesDetail::tableName().'.pay_time', strtotime($start_date), strtotime($end_date) + 86400]);
        }

        //过滤发货时间
        if (!empty($searchParams['delivery_time'])) {
            list($start_date, $end_date) = explode('/', $searchParams['delivery_time']);
            $dataProvider->query->andFilterWhere(['between', SalesDetail::tableName().'.delivery_time', strtotime($start_date), strtotime($end_date) + 86400]);
        }

        //过滤退款时间
        if (!empty($searchParams['refund_time'])) {
            list($start_date, $end_date) = explode('/', $searchParams['refund_time']);
            $dataProvider->query->andFilterWhere(['between', SalesDetail::tableName().'.refund_time', strtotime($start_date), strtotime($end_date) + 86400]);
        }

        //过滤退货时间
        if (!empty($searchParams['return_time'])) {
            list($start_date, $end_date) = explode('/', $searchParams['return_time']);
            $dataProvider->query->andFilterWhere(['between', SalesDetail::tableName().'.return_time', strtotime($start_date), strtotime($end_date) + 86400]);
        }

        //导出
        if(\Yii::$app->request->get('action') === 'export'){
            $queryIds = $dataProvider->query->select(SalesDetail::tableName().'.id');
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
    public function actionEdit()
    {
        $id = Yii::$app->request->get('id');
        $model = $this->findModel($id);
        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {
            try{
                $trans = Yii::$app->db->beginTransaction();
                if(false === $model->save()){
                    throw new \Exception($this->getError($model));
                    return $this->message($this->getError($model), $this->redirect(\Yii::$app->request->referrer), 'error');
                }
                $trans->commit();
                return $this->message('操作成功', $this->redirect(['index']), 'success');
            }catch (\Exception $e){
                $trans->rollBack();
                return $this->message($e->getMessage(), $this->redirect(Yii::$app->request->referrer), 'error');
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
        return $this->render($this->action->id, [
            'model' => $model,
            'tab'=>Yii::$app->request->get('tab',1),
            'tabList'=> Yii::$app->financeService->bankPay->menuTabList($id),
            'returnUrl'=>$this->returnUrl,
        ]);
    }



    /**
     * @param null $ids
     * @return bool|mixed
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function actionExport($ids=null){
        $name = '财务销售明细单';
        if(!is_object($ids)) {
            $ids = StringHelper::explodeIds($ids);
        }
        if(!$ids){
            return $this->message('单据ID不为空', $this->redirect(['index']), 'warning');
        }
        list($list,) = $this->getData($ids);
        $header = [
            ['订单号', 'orde_sn' , 'text'],
            ['部门', 'depart_name' , 'text'],
            ['销售渠道', 'channel_name' , 'text'],
            ['商品名称', 'goods_name' , 'text'],
            ['产品线', 'product_type_name' , 'text'],
            ['货号', 'goods_sn' , 'text'],
            ['数量', 'goods_num' ,  'text'],
            ['单价', 'goods_price' , 'text'],
            ['平台收款日期', 'pay_time', 'date', 'Y-m-d'],
            ['销售金额', 'sale_price' , 'text'],
            ['发货时间', 'delivery_time', 'date', 'Y-m-d'],       
            ['退款时间', 'refund_time', 'date', 'Y-m-d'],
            ['退款金额', 'refund_price' , 'text'],
            ['退货时间', 'return_time', 'date', 'Y-m-d'],
            ['退款方式', 'return_by' , 'text'],

        ];
		if(\common\helpers\Auth::verify(\common\enums\SpecialAuthEnum::VIEW_CAIGOU_PRICE)){
			array_splice($header,11,0,['成本价', 'cost_price' , 'text']);
		}
        return ExcelHelper::exportData($list, $header, $name.'数据导出_' . date('YmdHis',time()));
    }


    private function getData($ids){
        $select = ['s.*','type.name as product_type_name','channel.name as channel_name','depart.name as depart_name'];
        $query = SalesDetail::find()->alias('s')
            ->leftJoin(ProductType::tableName().' type','type.id=s.product_type_id')
            ->leftJoin(SaleChannel::tableName().' channel','channel.id=s.sale_channel_id')
            ->leftJoin(Department::tableName().' depart','depart.id=s.dept_id')
            ->where(['s.id' => $ids])
            ->select($select);
        $lists = PageHelper::findAll($query, 100);
        //统计
        $total = [
        ];
        foreach ($lists as &$list){
            //退款方式
            $list['return_by'] = ReturnByEnum::getValue($list['return_by']);
            //统计


        }
        return [$lists,$total];
    }





}

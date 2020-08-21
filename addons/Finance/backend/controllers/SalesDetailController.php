<?php

namespace addons\Finance\backend\controllers;


use addons\Finance\common\enums\FinanceStatusEnum;
use addons\Finance\common\forms\BankPayForm;
use addons\Finance\common\models\BankPay;
use addons\Finance\common\models\SalesDetail;
use common\enums\CurrencyEnum;
use common\enums\FlowStatusEnum;
use common\enums\TargetType;
use common\helpers\ResultHelper;
use common\models\common\Flow;
use common\models\common\FlowDetails;
use Yii;
use common\enums\AuditStatusEnum;
use common\models\base\SearchModel;
use common\traits\Curd;
use common\helpers\SnHelper;


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









}

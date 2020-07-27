<?php

namespace addons\Finance\backend\controllers;


use addons\Finance\common\enums\FinanceStatusEnum;
use addons\Finance\common\forms\BankPayForm;
use addons\Finance\common\models\BankPay;
use common\enums\CurrencyEnum;
use common\enums\FlowStatusEnum;
use common\enums\TargetType;
use common\enums\TargetTypeEnum;
use common\helpers\PageHelper;
use common\helpers\ResultHelper;
use common\models\common\Flow;
use common\models\common\FlowDetails;
use Yii;
use common\helpers\ArrayHelper;
use common\helpers\ExcelHelper;
use common\helpers\StringHelper;
use common\models\backend\Member;
use common\enums\AuditStatusEnum;
use common\enums\LogTypeEnum;
use common\models\base\SearchModel;
use common\traits\Curd;
use common\helpers\SnHelper;
use addons\Purchase\common\forms\PurchaseFollowerForm;
use addons\Purchase\common\models\Purchase;
use addons\Purchase\common\enums\PurchaseTypeEnum;
use addons\Purchase\common\enums\PurchaseStatusEnum;
use addons\Purchase\common\models\PurchaseGoods;
use addons\Purchase\common\models\PurchaseGoodsAttribute;
use addons\Style\common\models\ProductType;
use addons\Style\common\models\StyleCate;
use addons\Style\common\enums\AttrIdEnum;
use addons\Supply\common\models\Supplier;

/**
 *
 *
 * Class PurchaseController
 * @package backend\modules\goods\controllers
 */
class BankPayController extends BaseController
{
    use Curd;

    /**
     * @var BankPay
     */
    public $modelClass = BankPayForm::class;
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
                'auditor' => ['username'],
                'creator' => ['username'],
            ]
        ]);

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        //导出
        if(\Yii::$app->request->get('action') === 'export'){
            $queryIds = $dataProvider->query->select(BankPay::tableName().'.id');
            $this->actionExport($queryIds);
        }

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
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
//            'tabList'=> Yii::$app->financeService->bankPay->menuTabList($id),
            'tabList'=> [],
            'returnUrl'=>$this->returnUrl,
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
        $model = $model ?? new BankPayForm();

        $model->creator_id = Yii::$app->user->identity->getId();
        $model->apply_user = $model->creator->username;
        $model->dept_id = $model->creator->dept_id;
        $model->currency = CurrencyEnum::CNY;

        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {
            try{
                $trans = Yii::$app->db->beginTransaction();
                $isNewRecord = $model->isNewRecord;
                if($isNewRecord){
                    $model->getTargetType();
                    if($model->targetType) {
                        /**
                         * 审批流程
                         * 根据流程ID生成单号，并把单号反写到流程中
                        */
                        $flow = Yii::$app->services->flowType->createFlow($model->targetType, $id);
                        if(!$flow){
                            throw new \Exception('创建审批流程错误');
                        }
                        $model->finance_no = SnHelper::createFinanceSn($flow->id);
                        $model->flow_id = $flow->id;
                        $flow->target_no = $model->finance_no;
                    }
                    $model->creator_id  = \Yii::$app->user->identity->id;
                }
                if(false === $model->save()){
                    throw new \Exception($this->getError($model));
                    return $this->message($this->getError($model), $this->redirect(\Yii::$app->request->referrer), 'error');
                }

                /**
                 * 把单据ID反写到流程表中
                 */
                if($isNewRecord){
                    if($model->targetType){
                        $flow->target_id = $model->id;
                        if(false === $flow->save()){
                            throw new \Exception($this->getError($flow));
                            return $this->message($this->getError($flow), $this->redirect(\Yii::$app->request->referrer), 'error');
                        }
                    }
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
     * 申请审核
     * @return mixed
     */
    public function actionAjaxApply(){

        $id = \Yii::$app->request->get('id');
        $model = $this->findModel($id);
        $this->returnUrl = \Yii::$app->request->referrer;

        if($model->finance_status != FinanceStatusEnum::SAVE){
            return $this->message('申请单不是保存状态', $this->redirect($this->returnUrl), 'error');
        }
        try{
            $trans = Yii::$app->db->beginTransaction();


            $model->finance_status = FinanceStatusEnum::PENDING;
            $model->audit_status = AuditStatusEnum::PENDING;
            if(false === $model->save()){
                return $this->message($this->getError($model), $this->redirect($this->returnUrl), 'error');
            }
            //日志
//            $log = [
//                'purchase_id' => $id,
//                'purchase_sn' => $model->purchase_sn,
//                'log_type' => LogTypeEnum::ARTIFICIAL,
//                'log_module' => "申请审核",
//                'log_msg' => "申请审核"
//            ];
//            Yii::$app->financeService->bankPay->createP0Log($log);
            $trans->commit();
            return $this->message('操作成功', $this->redirect($this->returnUrl), 'success');
        }catch (\Exception $e){
            $trans->rollBack();
            return $this->message($e->getMessage(), $this->redirect(Yii::$app->request->referrer), 'error');
        }
    }



    /**
     * 关闭
     * @return mixed
     */
    public function actionClose(){

        $id = \Yii::$app->request->get('id');
        $model = $this->findModel($id);
        if($model->purchase_status != PurchaseStatusEnum::SAVE){
            return $this->message('单据不是保存状态', $this->redirect(Yii::$app->request->referrer), 'error');
        }
        $model->purchase_status = PurchaseStatusEnum::CANCEL;
        if(false === $model->save()){
            return $this->message($this->getError($model), $this->redirect(Yii::$app->request->referrer), 'error');
        }
        //日志
        $log = [
            'purchase_id' => $id,
            'purchase_sn' => $model->purchase_sn,
            'log_type' => LogTypeEnum::ARTIFICIAL,
            'log_module' => "关闭单据",
            'log_msg' => "关闭单据"
        ];
        Yii::$app->purchaseService->purchase->createPurchaseLog($log);
        return $this->message('操作成功', $this->redirect(Yii::$app->request->referrer), 'success');

    }


    public function actionAudit(){
        $id = Yii::$app->request->get('id');
        $model = $this->findModel($id);
        return $this->renderAjax($this->action->id, [
            'model' => $model,
        ]);
    }


    /**
     * ajax 审核
     *
     * @return mixed|string|\yii\web\Response
     * @throws \yii\base\ExitException
     */
    public function actionAjaxAudit()
    {
        $id = Yii::$app->request->get('id');
        $model = $this->findModel($id);
        $model->getTargetType();
        $model->audit_status = AuditStatusEnum::PASS;
        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {
            try{
                $trans = Yii::$app->db->beginTransaction();
                $audit = [
                    'audit_status' =>  $model->audit_status ,
                    'audit_time' => time(),
                    'audit_remark' => $model->audit_remark
                ];
                $res = \Yii::$app->services->flowType->flowAudit($model->targetType,$id,$audit);
                //审批完结才会走下面
                if($res->flow_status == FlowStatusEnum::COMPLETE){
                    $model->audit_time = time();
                    $model->auditor_id = \Yii::$app->user->identity->id;
                    if($model->audit_status == AuditStatusEnum::PASS){
                        $model->finance_status = FinanceStatusEnum::FINISH;
                    }else{
                        $model->finance_status = FinanceStatusEnum::SAVE;
                    }
                    if(false === $model->save()){
                        throw new \Exception($this->getError($model));
                    }
                }

//                //日志
//                $log = [
//                    'purchase_id' => $id,
//                    'purchase_sn' => $model->purchase_sn,
//                    'log_type' => LogTypeEnum::ARTIFICIAL,
//                    'log_module' => "单据审核",
//                    'log_msg' => "审核状态：".AuditStatusEnum::getValue($model->audit_status).",审核备注：".$model->audit_remark
//                ];
//                Yii::$app->purchaseService->purchase->createPurchaseLog($log);


                $trans->commit();
                Yii::$app->getSession()->setFlash('success','保存成功');
                return $this->redirect(Yii::$app->request->referrer);
            }catch (\Exception $e){
                $trans->rollBack();
                return $this->message($e->getMessage(), $this->redirect(Yii::$app->request->referrer), 'error');
            }

        }
        try {
            $current_detail_id = Yii::$app->services->flowType->getCurrentDetailId($model->targetType, $id);
            list($current_users_arr, $flow_detail) = \Yii::$app->services->flowType->getFlowDetals($model->targetType, $id);
        }catch (\Exception $e){
            return $this->message($e->getMessage(), $this->redirect(Yii::$app->request->referrer), 'error');
        }
        return $this->renderAjax('audit', [
            'model' => $model,
            'current_users_arr' => $current_users_arr,
            'flow_detail' => $flow_detail,
            'current_detail_id'=> $current_detail_id
        ]);
    }


    /**
     * 添加商品时查询戒指数据
     * @return string[]|array[]|string
     */
    public function actionSelectFlow()
    {
        $id = Yii::$app->request->get('id');
        $model = $this->findModel($id);
        $request = Yii::$app->request;
        if($request->isPost)
        {
            $flow_id = Yii::$app->request->post('flow_id');
            if($id == null){
                return ResultHelper::json(422, '参数错误');
            }
            if($flow_id){
                $flow_ids = join('|',$flow_id);
            }else{
                $flow_ids = '';
            }

            $model->flow_ids = $flow_ids;
            if(false === $model->save()){
                return ResultHelper::json(422, $this->getError($model));
            }
            return $this->message('操作成功', $this->redirect(Yii::$app->request->referrer), 'success');
        }

        $searchModel = new SearchModel([
            'model' => Flow::class,
            'scenario' => 'default',
            'partialMatchAttributes' => ['flow_name'], // 模糊查询
            'defaultOrder' => [
                'id' => SORT_DESC
            ],
            'pageSize' => 5
        ]);

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams,['sid']);
        $dataProvider->query->andFilterWhere(['=', 'creator_id',\Yii::$app->user->identity->id]);
        return $this->render('select-flow', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'model' => $model,
        ]);
    }



    /**
     * 单据打印
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionPrint()
    {
        $this->layout = '@backend/views/layouts/print';
        $id = Yii::$app->request->get('id');
        $model = $this->findModel($id);
        $flow_list = FlowDetails::find()->where(['flow_id'=>$model->flow_id])->all();
        return $this->render($this->action->id, [
            'model' => $model,
            'flow_list' => $flow_list

        ]);
    }




}

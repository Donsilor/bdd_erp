<?php

namespace addons\Finance\backend\controllers;

use addons\Finance\common\enums\FinanceStatusEnum;
use addons\Finance\common\forms\ContractPayForm;
use addons\Finance\common\models\BankPay;
use addons\Finance\common\models\ContractPay;
use common\enums\CurrencyEnum;
use common\enums\FlowStatusEnum;
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
class ContractPayController extends BaseController
{
    use Curd;

    /**
     * @var BankPay
     */
    public $modelClass = ContractPayForm::class;
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
            'tabList'=> Yii::$app->financeService->contractPay->menuTabList($id),
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
        $model = $model ?? new ContractPayForm();

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
            if($model->audit_status == AuditStatusEnum::UNPASS){
                $model->getTargetType();
                if($model->targetType) {
                    /**
                     * 审批不通过，重新生成审批流程
                     */
                    $flow = Yii::$app->services->flowType->createFlow($model->targetType, $id,$model->finance_no);
                    if(!$flow){
                        throw new \Exception('创建审批流程错误');
                    }
                    $model->flow_id = $flow->id;
                }
            }

            $model->finance_status = FinanceStatusEnum::PENDING;
            $model->audit_status = AuditStatusEnum::PENDING;
            if(false === $model->save()){
                return $this->message($this->getError($model), $this->redirect($this->returnUrl), 'error');
            }
            $trans->commit();
            return $this->message('操作成功', $this->redirect($this->returnUrl), 'success');
        }catch (\Exception $e){
            $trans->rollBack();
            return $this->message($e->getMessage(), $this->redirect(Yii::$app->request->referrer), 'error');
        }
    }




    public function actionLog(){
        $id = Yii::$app->request->get('id');
        $model = $this->findModel($id);
        $model->getTargetType();
        $flow_type_id = $model->targetType;
        $flow_detail_arr = Yii::$app->services->flow->getFlowDetalsAll($flow_type_id,$id);
        return $this->render($this->action->id, [
            'flow_detail_arr' => $flow_detail_arr,
            'model' => $model,
            'tab'=>Yii::$app->request->get('tab',3),
            'tabList'=> Yii::$app->financeService->contractPay->menuTabList($id),
            'returnUrl'=>$this->returnUrl,
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
                //审批完结或者审批不通过才会走下面
                if($res->flow_status == FlowStatusEnum::COMPLETE || $res->flow_status == FlowStatusEnum::CANCEL){
                    $model->audit_time = time();
                    $model->auditor_id = \Yii::$app->user->identity->id;
                    if($model->audit_status == AuditStatusEnum::PASS){
                        $model->finance_status = FinanceStatusEnum::CONFORMED;
                    }else{
                        $model->finance_status = FinanceStatusEnum::SAVE;
                    }
                    if(false === $model->save()){
                        throw new \Exception($this->getError($model));
                    }
                }

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
     * 关闭
     * @return mixed
     */
    public function actionClose(){

        $id = \Yii::$app->request->get('id');
        $model = $this->findModel($id);
        if($model->finance_status != FinanceStatusEnum::SAVE){
            return $this->message('单据不是保存状态', $this->redirect(Yii::$app->request->referrer), 'error');
        }
        $model->finance_status = FinanceStatusEnum::CANCAEL;
        if(false === $model->save()){
            return $this->message($this->getError($model), $this->redirect(Yii::$app->request->referrer), 'error');
        }
        return $this->message('操作成功', $this->redirect(Yii::$app->request->referrer), 'success');

    }


    /**
     * 确认
     * @return mixed
     */
    public function actionConfirm(){

        $id = \Yii::$app->request->get('id');
        $model = $this->findModel($id);
        if($model->finance_status != FinanceStatusEnum::CONFORMED){
            return $this->message('单据不是待确认状态', $this->redirect(Yii::$app->request->referrer), 'error');
        }
        try {
            $trans = Yii::$app->db->beginTransaction();
            $model->finance_status = FinanceStatusEnum::FINISH;
            if (false === $model->save()) {
                return $this->message($this->getError($model), $this->redirect(Yii::$app->request->referrer), 'error');
            }
            $trans->commit();
            return $this->message('操作成功', $this->redirect(Yii::$app->request->referrer), 'success');
        }catch (\Exception $e){
            $trans->rollBack();
            return $this->message($e->getMessage(), $this->redirect(Yii::$app->request->referrer), 'error');
        }

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

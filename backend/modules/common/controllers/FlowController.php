<?php

namespace backend\modules\common\controllers;

use addons\Purchase\common\models\Purchase;
use common\enums\AuditStatusEnum;
use common\enums\FlowStatus;
use common\enums\FlowStatusEnum;
use common\enums\TargetTypeEnum;
use common\models\base\SearchModel;
use common\models\common\Flow;
use common\models\common\FlowDetails;
use Yii;
use common\traits\Curd;
use common\models\common\ConfigCate;
use backend\controllers\BaseController;

/**
 * Class ConfigCateController
 * @package backend\modules\common\controllers
 * @author jianyan74 <751393839@qq.com>
 */
class FlowController extends BaseController
{
    use Curd;

    /**
     * @var ConfigCate
     */
    public $modelClass = Flow::class;

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
            'pageSize' => $this->pageSize
        ]);

        $dataProvider = $searchModel
            ->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
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
        $current_user_id = \Yii::$app->user->identity->id;
        $model = FlowDetails::find()->where(['flow_id'=>$id,'user_id'=>$current_user_id])->one();
        $model->audit_status = AuditStatusEnum::PASS;
        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {
            try{
                $trans = Yii::$app->db->beginTransaction();
                $flow_model = $this->findModel($id);

                $model->audit_time = time();



                if($model->audit_status == AuditStatusEnum::UNPASS){
                    //不通过，审批流程结束
                    $flow_model->flow_status = FlowStatusEnum::COMPLETE;

                    //单据审核状态变更为不通过
                    $target_model = TargetTypeEnum::getValue($flow_model->flow_type, 'getModel');
                    $target_model = $target_model->where(['id'=>$flow_model->target_id])->one();
                    $target_model->audit_status = AuditStatusEnum::UNPASS;
                    if(false === $target_model->save()){
                        throw new \Exception($this->getError($target_model));
                    }

                }else{
                    $flow_num = $flow_model->flow_num + 1;
                    $flow_model->flow_num = $flow_num;

                    //当最后一个人审批通过后，审批流程结束
                    if($flow_num === $flow_model->flow_total){
                        $flow_model->flow_status = FlowStatusEnum::COMPLETE;

                        //单据审核状态变更为已审核
                        $target_model = TargetTypeEnum::getValue($flow_model->flow_type, 'getModel');
                        $target_model = $target_model->where(['id'=>$flow_model->target_id])->one();
                        $target_model->audit_status = AuditStatusEnum::PASS;
                        if(false === $target_model->save()){
                            throw new \Exception($this->getError($target_model));
                        }
                    }
                }

                if(false === $flow_model->save()){
                    throw new \Exception($this->getError($flow_model));
                }

                if(false === $model->save()){
                    throw new \Exception($this->getError($model));
                }

                $trans->commit();
                Yii::$app->getSession()->setFlash('success','保存成功');
                return $this->redirect(Yii::$app->request->referrer);
            }catch (\Exception $e){
                $trans->rollBack();
                return $this->message($e->getMessage(), $this->redirect(Yii::$app->request->referrer), 'error');
            }

        }

        return $this->renderAjax($this->action->id, [
            'model' => $model,
        ]);
    }


}
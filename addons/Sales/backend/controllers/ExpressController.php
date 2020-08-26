<?php

namespace addons\Sales\backend\controllers;

use Yii;
use common\helpers\Url;
use common\traits\Curd;
use common\models\base\SearchModel;
use addons\Sales\common\models\Express;
use addons\Sales\common\forms\ExpressForm;
use common\enums\AuditStatusEnum;
use common\enums\StatusEnum;

/**
 * 物流快递
 *
 * Class ExpressController
 * @package addons\Sales\backend\controllers
 */
class ExpressController extends BaseController
{
    use Curd;

    /**
     * @var Express
     */
    public $modelClass = ExpressForm::class;
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
            'pageSize' => $this->pageSize,
            'relations' => [
                'member' => ['username'],
                'creator' => ['username'],
                'auditor' => ['username'],
            ]
        ]);

        $dataProvider = $searchModel
            ->search(Yii::$app->request->queryParams,['created_at']);

        $created_at = $searchModel->created_at;
        if (!empty($created_at)) {
            $dataProvider->query->andFilterWhere(['>=',Express::tableName().'.created_at', strtotime(explode('/', $created_at)[0])]);//起始时间
            $dataProvider->query->andFilterWhere(['<',Express::tableName().'.created_at', (strtotime(explode('/', $created_at)[1]) + 86400)] );//结束时间
        }

        //$dataProvider->query->andWhere(['>',Express::tableName().'.status',-1]);

        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * Ajax 编辑/创建
     * @throws
     * @return mixed
     */
    public function actionAjaxEdit()
    {
        $id = \Yii::$app->request->get('id');
        $model = $this->findModel($id);
        $model = $model ?? new ExpressForm();
        $this->activeFormValidate($model);
        if ($model->load(\Yii::$app->request->post())) {
            try{
                $trans = \Yii::$app->db->beginTransaction();
                if($model->isNewRecord){
                    $model->status = StatusEnum::DISABLED;
                }
                if(false === $model->save()){
                    throw new \Exception($this->getError($model));
                }
                $trans->commit();
            }catch (\Exception $e){
                $trans->rollBack();
                return $this->message($this->getError($model), $this->redirect(\Yii::$app->request->referrer), 'error');
            }
            \Yii::$app->getSession()->setFlash('success','保存成功');
            return $this->redirect(\Yii::$app->request->referrer);
        }
        return $this->renderAjax($this->action->id, [
            'model' => $model,
        ]);
    }

    /**
     * @return mixed
     * 提交审核
     */
    public function actionAjaxApply(){
        $id = \Yii::$app->request->get('id');
        $model = $this->findModel($id);
        $model = $model ?? new ExpressForm();
        if($model->audit_status != AuditStatusEnum::SAVE){
            return $this->message('快递公司不是保存状态', $this->redirect(\Yii::$app->request->referrer), 'error');
        }
        $model->audit_status = AuditStatusEnum::PENDING;
        if(false === $model->save()){
            return $this->message($this->getError($model), $this->redirect(\Yii::$app->request->referrer), 'error');
        }
        return $this->message('操作成功', $this->redirect(\Yii::$app->request->referrer), 'success');
    }

    /**
     * 快递公司-审核
     * @throws
     * @return mixed
     */
    public function actionAjaxAudit()
    {
        $id = Yii::$app->request->get('id');
        $model = $this->findModel($id);
        $model = $model ?? new ExpressForm();
        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {
            try{
                $trans = Yii::$app->trans->beginTransaction();
                if($model->audit_status == AuditStatusEnum::PASS){
                    $model->auditor_id = \Yii::$app->user->id;
                    $model->audit_time = time();
                    $model->status = StatusEnum::ENABLED;
                }else{
                    $model->status = StatusEnum::DISABLED;
                    $model->audit_status = AuditStatusEnum::SAVE;
                }
                if(false === $model->save()) {
                    throw new \Exception($this->getError($model));
                }
                $trans->commit();
            }catch (\Exception $e){
                $trans->rollBack();
                return $this->message("保存失败:". $e->getMessage(),  $this->redirect(Yii::$app->request->referrer), 'error');
            }
            return $this->message("保存成功", $this->redirect(Yii::$app->request->referrer), 'success');
        }
        $model->audit_status  = AuditStatusEnum::PASS;
        return $this->renderAjax($this->action->id, [
            'model' => $model,
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
        $tab = Yii::$app->request->get('tab',1);
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['index', 'id'=>$id]));
        $model = $this->findModel($id);
        $model = $model ?? new ExpressForm();
        if($model->settlement_way){
            $arr = explode(',', $model->settlement_way);
            $arr = array_filter($arr);
            $str = '';
            foreach ($arr as $val){
                $str .= ','. \addons\Sales\common\enums\SettlementWayEnum::getValue($val);
            }
            $model->settlement_way = trim( $str,',' );
        }
        if($model->settlement_period){
            $arr = explode(',', $model->settlement_period);
            $arr = array_filter($arr);
            $str = '';
            foreach ($arr as $val){
                $str .= ','. \addons\Sales\common\enums\SettlementPeriodEnum::getValue($val);
            }
            $model->settlement_period = trim( $str,',' );
        }
        if($model->delivery_scope){
            $arr = explode(',', $model->delivery_scope);
            $arr = array_filter($arr);
            $str = '';
            foreach ($arr as $val){
                $str .= ','. \addons\Sales\common\enums\DeliveryScopeEnum::getValue($val);
            }
            $model->delivery_scope = trim( $str,',' );
        }
        return $this->render($this->action->id, [
            'model' => $model,
            'tab'=>$tab,
            'tabList'=>\Yii::$app->salesService->express->menuTabList($id, $returnUrl),
            'returnUrl'=>$returnUrl,
        ]);
    }
}
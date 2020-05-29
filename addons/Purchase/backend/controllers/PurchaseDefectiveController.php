<?php

namespace addons\Purchase\backend\controllers;


use addons\Purchase\common\models\PurchaseReceipt;
use addons\Warehouse\common\enums\BillStatusEnum;
use common\helpers\SnHelper;
use Yii;
use common\helpers\Url;
use common\models\base\SearchModel;
use addons\Purchase\common\models\PurchaseDefective;
use addons\Purchase\common\forms\PurchaseDefectiveForm;
use common\enums\AuditStatusEnum;
use common\enums\StatusEnum;
use common\traits\Curd;
/**
* PurchaseDefective
*
* Class PurchaseDefectiveController
* @package addons\Purchase\Backend\controllers
*/
class PurchaseDefectiveController extends BaseController
{
    use Curd;

    /**
    * @var PurchaseDefective
    */
    public $modelClass = PurchaseDefective::class;


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
            ]
        ]);
        $dataProvider = $searchModel
            ->search(Yii::$app->request->queryParams, ['created_at']);

        $created_at = $searchModel->created_at;
        if (!empty($created_at)) {
            $dataProvider->query->andFilterWhere(['>=',PurchaseDefective::tableName().'.created_at', strtotime(explode('/', $created_at)[0])]);//起始时间
            $dataProvider->query->andFilterWhere(['<',PurchaseDefective::tableName().'.created_at', (strtotime(explode('/', $created_at)[1]) + 86400)] );//结束时间
        }

        $dataProvider->query->andWhere(['>',PurchaseDefective::tableName().'.status',-1]);

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

        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {
            if($model->isNewRecord){
                $model->defective_no = SnHelper::createDefectiveSn();
            }
            $model->creator_id  = \Yii::$app->user->identity->id;
            return $model->save()
                ? $this->redirect(Yii::$app->request->referrer)
                : $this->message($this->getError($model), $this->redirect(['index']), 'error');
        }

        return $this->renderAjax($this->action->id, [
            'model' => $model,
        ]);
    }


    /**
     * @return mixed
     * 申请审核
     */
    public function actionAjaxApply(){
        $id = \Yii::$app->request->get('id');
        $model = $this->findModel($id);
        if($model->defective_status != BillStatusEnum::SAVE){
            return $this->message('单据不是保存状态', $this->redirect(\Yii::$app->request->referrer), 'error');
        }
        $model->defective_status = BillStatusEnum::PENDING;
        if(false === $model->save()){
            return $this->message($this->getError($model), $this->redirect(\Yii::$app->request->referrer), 'error');
        }
        return $this->message('操作成功', $this->redirect(\Yii::$app->request->referrer), 'success');

    }

    /**
     * 审核-不良返厂单
     *
     * @return mixed
     */
    public function actionAjaxAudit()
    {
        $id = Yii::$app->request->get('id');

        $this->modelClass = PurchaseDefectiveForm::class;
        $model = $this->findModel($id);

        if($model->audit_status == AuditStatusEnum::PENDING) {
            $model->audit_status = AuditStatusEnum::PASS;
        }

        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {
            try{
                $trans = Yii::$app->trans->beginTransaction();
                if($model->audit_status == AuditStatusEnum::PASS){
                    $model->auditor_id = \Yii::$app->user->id;
                    $model->audit_time = time();
                    $model->defective_status = BillStatusEnum::CONFIRM;
                }else{
                    $model->defective_status = BillStatusEnum::SAVE;
                }
                if(false === $model->save()) {
                    throw new \Exception($this->getError($model));
                }
                $trans->commit();
            }catch (\Exception $e){
                $trans->rollBack();
                return $this->message("审核失败:". $e->getMessage(),  $this->redirect(Yii::$app->request->referrer), 'error');
            }
            return $this->message("保存成功", $this->redirect(Yii::$app->request->referrer), 'success');
        }

        return $this->renderAjax($this->action->id, [
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
        $tab = Yii::$app->request->get('tab',1);
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['purchase-defective/index']));

        $model = $this->findModel($id);
        return $this->render($this->action->id, [
            'model' => $model,
            'tab'=>$tab,
            'tabList'=>\Yii::$app->purchaseService->purchaseDefective->menuTabList($id,$returnUrl),
            'returnUrl'=>$returnUrl,
        ]);
    }
}

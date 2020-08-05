<?php

namespace addons\Style\backend\controllers;

use Yii;
use common\traits\Curd;
use common\models\base\SearchModel;
use addons\Style\common\models\StoneStyle;
use addons\Style\common\enums\AttrIdEnum;
use common\enums\AuditStatusEnum;
use common\enums\StatusEnum;
use common\helpers\Url;

/**
 * StoneStyleController implements the CRUD actions for StoneStyle model.
 */
class StoneStyleController extends BaseController
{
    use Curd;
    public $modelClass = StoneStyle::class;
    /**
     * Lists all StoneStyle models.
     * @return mixed
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
                'creator' => ['username'],
                'auditor' => ['username'],
            ]
        ]);

        $dataProvider = $searchModel
            ->search(Yii::$app->request->queryParams,['created_at', 'updated_at', 'audit_time', 'stone_weight_min', 'stone_weight_max']);

        $created_at = $searchModel->created_at;
        if (!empty($created_at)) {
            $dataProvider->query->andFilterWhere(['>=',StoneStyle::tableName().'.created_at', strtotime(explode('/', $created_at)[0])]);//起始时间
            $dataProvider->query->andFilterWhere(['<',StoneStyle::tableName().'.created_at', (strtotime(explode('/', $created_at)[1]) + 86400)] );//结束时间
        }
        $audit_time = $searchModel->audit_time;
        if (!empty($audit_time)) {
            $dataProvider->query->andFilterWhere(['>=',StoneStyle::tableName().'.audit_time', strtotime(explode('/', $audit_time)[0])]);//起始时间
            $dataProvider->query->andFilterWhere(['<',StoneStyle::tableName().'.audit_time', (strtotime(explode('/', $audit_time)[1]) + 86400)] );//结束时间
        }
        $updated_at = $searchModel->updated_at;
        if (!empty($updated_at)) {
            $dataProvider->query->andFilterWhere(['>=',StoneStyle::tableName().'.updated_at', strtotime(explode('/', $updated_at)[0])]);//起始时间
            $dataProvider->query->andFilterWhere(['<',StoneStyle::tableName().'.updated_at', (strtotime(explode('/', $updated_at)[1]) + 86400)] );//结束时间
        }
        $stone_weight_min = $searchModel->stone_weight_min;
        if (!empty($stone_weight_min)) {
            $dataProvider->query->andFilterWhere(['>=',StoneStyle::tableName().'.stone_weight_min', $stone_weight_min] );//结束时间
        }
        $stone_weight_max = $searchModel->stone_weight_max;
        if (!empty($stone_weight_max)) {
            $dataProvider->query->andFilterWhere(['<=',StoneStyle::tableName().'.stone_weight_max', $stone_weight_max] );//结束时间
        }

        $dataProvider->query->andWhere(['>',StoneStyle::tableName().'.status',-1]);

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
            if($model->stone_weight_min>$model->stone_weight_max){
                return $this->message('石重范围小不能大于石重范围大', $this->redirect(\Yii::$app->request->referrer), 'error');
            }
            if($model->stone_type != AttrIdEnum::STONE_TYPE_MO && empty($model->stone_weight_min)){
                return $this->message('石重范围小必填', $this->redirect(\Yii::$app->request->referrer), 'error');
            }
            if($model->stone_type != AttrIdEnum::STONE_TYPE_MO && empty($model->stone_weight_max)){
                return $this->message('石重范围大必填', $this->redirect(\Yii::$app->request->referrer), 'error');
            }
            if($model->stone_type == AttrIdEnum::STONE_TYPE_MO && empty($model->product_size_min)){
                return $this->message('尺寸范围小必填', $this->redirect(\Yii::$app->request->referrer), 'error');
            }
            if($model->stone_type == AttrIdEnum::STONE_TYPE_MO && empty($model->product_size_max)){
                return $this->message('尺寸范围大必填', $this->redirect(\Yii::$app->request->referrer), 'error');
            }
            $model->status = StatusEnum::DISABLED;
            return $model->save()
                ? $this->redirect(Yii::$app->request->referrer)
                : $this->message($this->getError($model), $this->redirect(Yii::$app->request->referrer), 'error');
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
        $model = $model ?? new StoneStyle();
        if($model->audit_status != AuditStatusEnum::SAVE){
            return $this->message('不是保存状态', $this->redirect(\Yii::$app->request->referrer), 'error');
        }
        $model->audit_status = AuditStatusEnum::PENDING;
        if(false === $model->save()){
            return $this->message($this->getError($model), $this->redirect(\Yii::$app->request->referrer), 'error');
        }
        return $this->message('操作成功', $this->redirect(\Yii::$app->request->referrer), 'success');
    }

    /**
     * 审核
     *
     * @return mixed
     */
    public function actionAjaxAudit()
    {
        $id = Yii::$app->request->get('id');
        $model = $this->findModel($id);
        $model = $model ?? new StoneStyle();
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
                return $this->message("审核失败:". $e->getMessage(),  $this->redirect(Yii::$app->request->referrer), 'error');
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
        $returnUrl = Yii::$app->request->get('returnUrl', Url::to(['index']));
        $model = $this->findModel($id);
        $model = $model ?? new StoneStyle();
        return $this->render($this->action->id, [
            'model' => $model,
            'tab'=>$tab,
            'tabList'=>\Yii::$app->styleService->stone->menuTabList($id, $returnUrl),
            'returnUrl'=>$returnUrl,
        ]);
    }
}

<?php

namespace addons\Style\backend\controllers;

use addons\Style\common\forms\QibanFormatForm;
use addons\Style\common\enums\IsApply;
use addons\Style\common\forms\QibanAttrForm;
use addons\Style\common\forms\QibanAuditForm;
use addons\Style\common\models\Style;
use common\enums\FlowStatusEnum;
use common\enums\OperTypeEnum;
use common\enums\TargetTypeEnum;
use common\helpers\ResultHelper;
use Yii;
use common\models\base\SearchModel;
use common\traits\Curd;

use addons\Style\common\models\Qiban;
use common\helpers\Url;
use common\enums\AuditStatusEnum;
use common\enums\StatusEnum;
use common\helpers\SnHelper;
use addons\Style\common\enums\QibanTypeEnum;
use addons\Style\common\models\QibanAttribute;


/**
* Style
*
* Class StyleController
* @package backend\modules\goods\controllers
*/
class QibanController extends BaseController
{
    use Curd;

    /**
    * @var Qiban
    */
    public $modelClass = Qiban::class;

    //审批流程
    public $targetType = TargetTypeEnum::STYLE_QIBAN;

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
            'partialMatchAttributes' => ['qiban_name'], // 模糊查询
            'defaultOrder' => [
                'sort' => SORT_DESC
            ],
            'pageSize' => $this->pageSize,
            'relations' => [
                 
            ]
        ]);

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andFilterWhere(['<','is_apply',IsApply::Wait]);
        if ($searchModel->created_at) {
            $created_ats = explode('/', $searchModel->created_at);
            $dataProvider->query->andFilterWhere(['>=',Qiban::tableName().'.created_at', strtotime($created_ats[0])]);//起始时间
            $dataProvider->query->andFilterWhere(['<',Qiban::tableName().'.created_at', strtotime($created_ats[1]) + 86400]);//结束时间
        }
        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel, 
        ]);
    }

    /**
     * 待起版列表
     * @return string
     */
    public function actionApply()
    {
        $searchModel = new SearchModel([
            'model' => $this->modelClass,
            'scenario' => 'default',
            'partialMatchAttributes' => ['qiban_name'], // 模糊查询
            'defaultOrder' => [
                'id' => SORT_DESC
            ],
            'pageSize' => $this->pageSize,
            'relations' => [

            ]
        ]);

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andFilterWhere(['>','is_apply',IsApply::No]);
        
        if ($searchModel->created_at) {
            $created_ats = explode('/', $searchModel->created_at);
            $dataProvider->query->andFilterWhere(['>=',Qiban::tableName().'.created_at', strtotime($created_ats[0])]);//起始时间
            $dataProvider->query->andFilterWhere(['<',Qiban::tableName().'.created_at', strtotime($created_ats[1]) + 86400] );//结束时间
        }

        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }


    /**
     * 版式编辑
     * @var PurchaseApplyGoodsForm $model
     * @return mixed
     */
    public function actionFormatEdit()
    {
        $this->layout = '@backend/views/layouts/iframe';

        $id = Yii::$app->request->get('id');
        $this->modelClass = QibanFormatForm::className();
        $model = $this->findModel($id);
        $model = $model ?? new QibanFormatForm();

        if ($model->load(Yii::$app->request->post())) {
            if(!$model->validate()) {
                return ResultHelper::json(422, $this->getError($model));
            }
            $format_info = Yii::$app->request->post('format_info');
            $model->format_info = json_encode($format_info);
            if(false === $model->save()){
                throw new \Exception($this->getError($model));
            }
            //前端提示
            Yii::$app->getSession()->setFlash('success','保存成功');
            return ResultHelper::json(200, '保存成功');
        }

        return $this->render($this->action->id, [
            'model' => $model,
        ]);
    }


    /**
     * 编辑/创建
     * @property PurchaseGoodsForm $model
     * @return mixed
     */
    public function actionEdit()
    {
        $this->layout = '@backend/views/layouts/iframe';
        $id = Yii::$app->request->get('id');
        
        $style_sn = Yii::$app->request->get('style_sn');
        $search = Yii::$app->request->get('search');
        $jintuo_type = Yii::$app->request->get('jintuo_type');
        
        $this->modelClass = QibanAttrForm::class;
        $model = $this->findModel($id);
        $model = $model ?? new QibanAttrForm();
        $isNewRecord = $model->isNewRecord;
        
        if($jintuo_type) {
            $model->jintuo_type = $jintuo_type;
        }

        if($style_sn && $search) {
            $skiUrl = Url::buildUrl(\Yii::$app->request->url,[],['search']);
            $style  = Style::find()->where(['style_sn'=>$style_sn])->one();
            if(!$style) {
                return $this->message("无效的款号", $this->redirect($skiUrl), 'error');
            }elseif($style->status != StatusEnum::ENABLED) {
                return $this->message("款号不可用", $this->redirect($skiUrl), 'error');
            }
            if($isNewRecord) {
                $model->qiban_type = QibanTypeEnum::HAVE_STYLE;
                $model->qiban_name = $style->style_name;
                $model->style_id = $style->id;
                $model->style_sn = $style->style_sn;
                $model->style_cate_id = $style->style_cate_id;
                $model->product_type_id = $style->product_type_id;                
                $model->style_sex = $style->style_sex;                
                $model->is_inlay  = $style->is_inlay;
            }
        }
        if ($model->load(Yii::$app->request->post())) {
            //重新编辑后，审核状态改为未审核
            if($isNewRecord){
                $model->status = StatusEnum::DISABLED;                
                $model->creator_id = \Yii::$app->user->id;
                $model->sort = time();
            }
            $model->audit_status = AuditStatusEnum::SAVE;
            try{
                $trans = Yii::$app->trans->beginTransaction();
                if(false === $model->save()){
                    throw new \Exception($this->getError($model));
                }
                if($isNewRecord) {
                    \Yii::$app->styleService->qiban->createQibanSn($model);
                } 
                //创建属性关系表数据
                $model->createAttrs();
                $trans->commit();
                if($isNewRecord) {
                    return $this->message("保存成功", $this->redirect(['view', 'id' => $model->id]), 'success');
                }else{
                    //前端提示
                    Yii::$app->getSession()->setFlash('success','保存成功');
                    return ResultHelper::json(200, '保存成功');
                }
            }catch (\Exception $e){
                $trans->rollBack();
                return ResultHelper::json(422, $e->getMessage());
            }
        }

        $model->initAttrs();
        return $this->render($this->action->id, [
            'model' => $model,
        ]);
    }

    /**
     * 无款起版编辑/创建
     * @property PurchaseGoodsForm $model
     * @return mixed
     */
    public function actionEditNoStyle()
    {
        $this->layout = '@backend/views/layouts/iframe';
        $id = Yii::$app->request->get('id');
        
        $style_cate_id = Yii::$app->request->get('style_cate_id');
        $product_type_id = Yii::$app->request->get('product_type_id');
        $jintuo_type = Yii::$app->request->get('jintuo_type');
        
        $this->modelClass = QibanAttrForm::class;
        $model = $this->findModel($id);
        $model = $model ?? new QibanAttrForm();
        $isNewRecord = $model->isNewRecord;
        
        $model->style_cate_id = $style_cate_id ?? $model->style_cate_id;
        $model->product_type_id = $product_type_id ?? $model->product_type_id;
        $model->jintuo_type = $jintuo_type ?? $model->jintuo_type;
        
        //无款起版
        if($isNewRecord) {
            $model->qiban_type = QibanTypeEnum::NO_STYLE;   
            $model->style_sn = 'QIBAN';
            $model->is_inlay = $model->type->is_inlay ?? 0;
        }
        
        if ($model->load(Yii::$app->request->post())) {
            //重新编辑后，审核状态改为未审核 
            if($isNewRecord){
                $model->status = StatusEnum::DISABLED;                
                $model->creator_id = \Yii::$app->user->id;
                $model->sort = time();
            }
            $model->audit_status = AuditStatusEnum::SAVE;
            try{
                $trans = Yii::$app->trans->beginTransaction();
                if(false === $model->save()){
                    throw new \Exception($this->getError($model));
                }
                if($isNewRecord) {
                    \Yii::$app->styleService->qiban->createQibanSn($model);
                }                
                //创建属性关系表数据
                $model->createAttrs();
              
                $trans->commit();
                if($isNewRecord) {
                    return $this->message("保存成功", $this->redirect(['view', 'id' => $model->id]), 'success');
                }else{
                    //前端提示
                    Yii::$app->getSession()->setFlash('success','保存成功');
                    return ResultHelper::json(200, '保存成功');
                }

            }catch (\Exception $e){
                $trans->rollBack();
                return ResultHelper::json(422, $e->getMessage());
            }
        }
        $model->initAttrs();
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
        $this->modelClass = QibanAttrForm::class;
        $model = $this->findModel($id);
        $model = $model ?? new QibanAttrForm();
        $model->initAttrs();
        return $this->render($this->action->id, [
            'model' => $model,
        ]);
    }


    /**
     *  申请审核
     *  
     * @return mixed  
     */
    public function actionAjaxApply(){
        $id = \Yii::$app->request->get('id');
        $model = $this->findModel($id);
        if($model->audit_status != AuditStatusEnum::SAVE){
            return $this->message('单据不是保存状态', $this->redirect(\Yii::$app->request->referrer), 'error');
        }
        try{
            $trans = Yii::$app->db->beginTransaction();
            $model->audit_status = AuditStatusEnum::PENDING;                        
            if(false === $model->save()){
                throw new \Exception($this->getError($model));
            }
            
            //审批流程
            Yii::$app->services->flowType->createFlow($this->targetType,$id,$model->qiban_sn,OperTypeEnum::QIBAN);
            
            //创建款号
            Yii::$app->styleService->qiban->createStyleSn($model);
            $trans->commit();
            return $this->message('操作成功', $this->redirect(\Yii::$app->request->referrer), 'success');
        }catch (\Exception $e){
            $trans->rollBack();
            return $this->message($e->getMessage(), $this->redirect(Yii::$app->request->referrer), 'error');
        }
    }

    /**
     * 审核-起版
     *
     * @return mixed
     */
    public function actionAjaxAudit()
    {
        $id = Yii::$app->request->get('id');
        
        $this->modelClass = QibanAuditForm::class;
        $model = $this->findModel($id);

        if($model->audit_status == AuditStatusEnum::PENDING) {
            $model->audit_status = AuditStatusEnum::PASS;
        }
        // ajax 校验
        $this->activeFormValidate($model);        
        if ($model->load(Yii::$app->request->post())) {
            try{
                $trans = Yii::$app->trans->beginTransaction();

                /* $audit = [
                    'audit_status' =>  $model->audit_status ,
                    'audit_time' => time(),
                    'audit_remark' => $model->audit_remark
                ]; */
                //$flow = \Yii::$app->services->flowType->flowAudit($this->targetType,$id,$audit);
                //审批完结或者审批不通过才会走下面
                //if($flow->flow_status == FlowStatusEnum::COMPLETE || $flow->flow_status == FlowStatusEnum::CANCEL){
                    
                    $model->auditor_id = \Yii::$app->user->identity->getId();
                    $model->audit_time = time();
                    if ($model->audit_status == AuditStatusEnum::PASS) {
                        if ($model->is_apply == IsApply::Wait) {
                            $model->is_apply = IsApply::Yes;
                            $model->sort = time();
                        }
                        $model->status = StatusEnum::ENABLED;
                        
                    } else {
                        $model->status = StatusEnum::DISABLED;
                    }
                    if (false === $model->save()) {
                        throw new \Exception($this->getError($model));
                    }
                //}
                $trans->commit();
            }catch (\Exception $e){
                $trans->rollBack();
                return $this->message($e->getMessage(),  $this->redirect(Yii::$app->request->referrer), 'error');
            }
            return $this->message("审核成功", $this->redirect(Yii::$app->request->referrer), 'success');
        }
        if($model->audit_status == 0){
            $model->audit_status = 1;
        }

        return $this->renderAjax($this->action->id, [
                'model' => $model,
        ]);
    }
    
    /**
     * 删除
     *
     * @param $id
     * @return mixed
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id)
    {
        try{
            $trans = Yii::$app->trans->beginTransaction();
            if ($this->findModel($id)->delete()) {
                //属性删除
                QibanAttribute::deleteAll(['qiban_id' => $id]);                
            }
            
            $trans->commit();
            return $this->message("操作成功",  $this->redirect(Yii::$app->request->referrer), 'success');
        }catch (\Exception $e){
            $trans->rollBack();
            return $this->message($e->getMessage(),  $this->redirect(Yii::$app->request->referrer), 'error');
        }
    }
    /**
     * 作废
     * @param $id
     * @return mixed
     * @throws \Throwable
     */
    public function actionDestroy($id)
    {
        $model = $this->findModel($id);
        try{
            $trans = Yii::$app->trans->beginTransaction();
            $model->status = StatusEnum::DELETE;
            $model->audit_status = AuditStatusEnum::DESTORY;
            $model->audit_time = time();
            $model->auditor_id = \Yii::$app->user->identity->getId();
            if(false === $model->save(true,['status','audit_status','audit_time','updated_at'])){
                throw new \Exception($this->getError($model));
            }
            $trans->commit();
            return $this->message("操作成功",  $this->redirect(Yii::$app->request->referrer), 'success');
        }catch (\Exception $e){
            $trans->rollBack();
            return $this->message($e->getMessage(),  $this->redirect(Yii::$app->request->referrer), 'error');
        }
    }
    
}

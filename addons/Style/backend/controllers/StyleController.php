<?php

namespace addons\Style\backend\controllers;

use addons\Style\common\forms\StyleForm;
use addons\Warehouse\common\forms\WarehouseBillTGoodsForm;
use addons\Warehouse\common\models\WarehouseBill;
use common\enums\FlowStatusEnum;
use common\enums\TargetTypeEnum;
use Yii;
use common\models\base\SearchModel;
use common\traits\Curd;

use addons\Style\backend\controllers\BaseController;
use addons\Style\common\models\Style;
use addons\Style\common\forms\StyleAttrForm;
use addons\Style\common\forms\StyleGoodsForm;
use common\helpers\Url;
use common\enums\AuditStatusEnum;
use addons\Style\common\forms\StyleAuditForm;
use common\enums\StatusEnum;
use yii\behaviors\AttributeTypecastBehavior;
use addons\Style\common\enums\AttrTypeEnum;
use common\helpers\SnHelper;
use common\enums\AutoSnEnum;
use yii\web\UploadedFile;

/**
* Style
*
* Class StyleController
* @package backend\modules\goods\controllers
*/
class StyleController extends BaseController
{
    use Curd;

    /**
    * @var Style
    */
    public $modelClass = Style::class;

    public $targetType = TargetTypeEnum::STYLE_STYLE;


    /**
    * 首页
    *
    * @return string
    * @throws \yii\web\NotFoundHttpException
    */
    public function actionIndex()
    {
       // $cate_id = Yii::$app->request->get('cate_id');
        $searchModel = new SearchModel([
            'model' => $this->modelClass,
            'scenario' => 'default',
            'partialMatchAttributes' => ['style_name'], // 模糊查询
            'defaultOrder' => [
                'id' => SORT_DESC
            ],
            'pageSize' => $this->pageSize,
            'relations' => [
                 
            ]
        ]);

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams,['created_at','updated_at']);
        $created_at = $searchModel->created_at;
        if (count($created_ats = explode('/', $created_at)) == 2) {
            $dataProvider->query->andFilterWhere(['>=',Style::tableName().'.created_at', strtotime($created_ats[0])]);//起始时间
            $dataProvider->query->andFilterWhere(['<',Style::tableName().'.created_at', (strtotime($created_ats[1]) + 86400)] );//结束时间
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
    public function actionAjaxEdit()
    {
        $id = Yii::$app->request->get('id');
        $model = $this->findModel($id);
        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {
            //重新编辑后，审核状态改为未审核
            $model->audit_status = AuditStatusEnum::SAVE;

            if($model->isNewRecord){                
                $model->creator_id = \Yii::$app->user->id;
            }
            if($model->type) {
                $model->is_inlay = $model->type->is_inlay;
            }
            $isNewRecord = $model->isNewRecord;
            try{                
                $trans = Yii::$app->trans->beginTransaction();
                if(false === $model->save()) {
                    throw new \Exception($this->getError($model));
                }
                //自动创建款号
                if($isNewRecord && trim($model->style_sn) == "") {
                    Yii::$app->styleService->style->createStyleSn($model);                    
                }
                $trans->commit();
                if($isNewRecord) {
                    return $this->message("保存成功", $this->redirect(['view', 'id' => $model->id]), 'success');
                }else{
                    return $this->message("保存成功", $this->redirect(Yii::$app->request->referrer), 'success');
                }
            }catch (\Exception $e) {
                $trans->rollback();
                return $this->message($e->getMessage(), $this->redirect(Yii::$app->request->referrer), 'error');
            }
        }
        
        return $this->renderAjax($this->action->id, [
                'model' => $model,
        ]);
    }
    /**
     *
     * 文件格式导出
     * @return mixed|string|\yii\web\Response
     * @throws
     */
    public function actionDownload()
    {
        $model = new StyleForm();
        list($values, $fields) = $model->getTitleList();
        header("Content-Disposition: attachment;filename=款式数据".time().").csv");
        $content = implode($values, ",") . "\n" . implode($fields, ",") . "\n";
        echo iconv("utf-8", "gbk", $content);
        exit();
    }
    /**
     *
     * ajax批量导入
     * @return mixed|string|\yii\web\Response
     * @throws
     */
    public function actionAjaxUpload()
    {
        $model = new StyleForm();
        // ajax 校验
        $this->activeFormValidate($model);
        if (Yii::$app->request->isPost) {
            try {
                $trans = \Yii::$app->db->beginTransaction();
                $model->file = UploadedFile::getInstance($model, 'file');
                Yii::$app->styleService->style->uploadGoods($model);
                $trans->commit();
                \Yii::$app->getSession()->setFlash('success', '保存成功');
                return $this->redirect(\Yii::$app->request->referrer);
            } catch (\Exception $e) {
                $trans->rollBack();
                //var_dump($e->getTraceAsString());die;
                return $this->message($e->getMessage(), $this->redirect(\Yii::$app->request->referrer), 'error');
            }
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
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['style/index']));
        
        $model = $this->findModel($id);
        
        $dataProvider = null;      
        
        return $this->render($this->action->id, [
                'model' => $model,
                'dataProvider' => $dataProvider,
                'tab'=>$tab,
                'tabList'=>\Yii::$app->styleService->style->menuTabList($id,$returnUrl),
                'returnUrl'=>$returnUrl,
        ]);
    }


    /**
     * @return mixed
     * 申请审核
     */
    public function actionAjaxApply(){
        $id = \Yii::$app->request->get('id');
        $model = $this->findModel($id);
        if($model->audit_status != AuditStatusEnum::SAVE && $model->audit_status != AuditStatusEnum::UNPASS ){
            return $this->message('单据不是保存状态', $this->redirect(\Yii::$app->request->referrer), 'error');
        }
        //审批流程
        Yii::$app->services->flowType->createFlow($this->targetType,$id,$model->style_sn);

        $model->audit_status = AuditStatusEnum::PENDING;
        if(false === $model->save()){
            return $this->message($this->getError($model), $this->redirect(\Yii::$app->request->referrer), 'error');
        }
        return $this->message('操作成功', $this->redirect(\Yii::$app->request->referrer), 'success');

    }
    /**
     * 审核-款号
     *
     * @return mixed
     */
    public function actionAjaxAudit()
    {
        $id = Yii::$app->request->get('id');
        
        $this->modelClass = StyleAuditForm::class;
        $model = $this->findModel($id);

        if($model->audit_status == AuditStatusEnum::PENDING) {
            $model->audit_status = AuditStatusEnum::PASS;
        }
        // ajax 校验
        $this->activeFormValidate($model);        
        if ($model->load(Yii::$app->request->post())) {
            try{
                $trans = Yii::$app->trans->beginTransaction();
                $audit = [
                    'audit_status' =>  $model->audit_status ,
                    'audit_time' => time(),
                    'audit_remark' => $model->audit_remark
                ];
                $res = \Yii::$app->services->flowType->flowAudit($this->targetType,$id,$audit);
                //审批完结或者审批不通过才会走下面
                if($res->flow_status == FlowStatusEnum::COMPLETE || $res->flow_status == FlowStatusEnum::CANCEL){
                    if ($model->audit_status == AuditStatusEnum::PASS) {
                        $model->auditor_id = \Yii::$app->user->id;
                        $model->audit_time = time();
                        $model->status = StatusEnum::ENABLED;
                        //\Yii::$app->styleService->style->createGiftStyle($model);
                    } else {
                        $model->status = StatusEnum::DISABLED;
                    }
                    if (false === $model->save()) {
                        throw new \Exception($this->getError($model));
                    }
                }
                $trans->commit();
            }catch (\Exception $e){
                $trans->rollBack();
                return $this->message("审核失败:". $e->getMessage(),  $this->redirect(Yii::$app->request->referrer), 'error');
            }
            return $this->message("保存成功", $this->redirect(Yii::$app->request->referrer), 'success');
        }
        if ($model->audit_status == 0) $model->audit_status = AuditStatusEnum::PASS;


        try {
            $current_detail_id = Yii::$app->services->flowType->getCurrentDetailId($this->targetType, $id);
            list($current_users_arr, $flow_detail) = \Yii::$app->services->flowType->getFlowDetals($this->targetType, $id);
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
    
}

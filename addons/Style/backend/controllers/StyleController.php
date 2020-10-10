<?php

namespace addons\Style\backend\controllers;

use addons\Style\common\enums\StonePositionEnum;
use addons\Style\common\models\StoneStyle;
use addons\Style\common\models\StyleAttribute;
use addons\Style\common\models\StyleFactory;
use addons\Style\common\models\StyleFactoryFee;
use addons\Style\common\models\StyleGoods;
use addons\Style\common\models\StyleGoodsAttribute;
use addons\Style\common\models\StyleImages;
use addons\Style\common\models\StyleLog;
use addons\Style\common\models\StyleStone;
use common\helpers\ArrayHelper;
use common\helpers\StringHelper;
use Yii;
use common\helpers\Url;
use common\traits\Curd;
use common\models\base\SearchModel;
use addons\Style\common\models\Style;
use addons\Style\common\forms\StyleForm;
use addons\Style\common\forms\StyleAuditForm;
use common\enums\AuditStatusEnum;
use common\enums\FlowStatusEnum;
use common\enums\TargetTypeEnum;
use common\enums\StatusEnum;
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
            'partialMatchAttributes' => [], // 模糊查询
            'defaultOrder' => [
                'id' => SORT_DESC
            ],
            'pageSize' => $this->pageSize,
            'relations' => [
                 'creator' => 'username',
            ]
        ]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams,['created_at','updated_at','style_sn','style_name']);


        $created_at = $searchModel->created_at;
        if (count($created_ats = explode('/', $created_at)) == 2) {
            $dataProvider->query->andFilterWhere(['>=',Style::tableName().'.created_at', strtotime($created_ats[0])]);//起始时间
            $dataProvider->query->andFilterWhere(['<',Style::tableName().'.created_at', (strtotime($created_ats[1]) + 86400)] );//结束时间
        }

        $dataProvider->query->andFilterWhere(['like',Style::tableName().'.style_sn', trim($searchModel->style_sn)]);
        $dataProvider->query->andFilterWhere(['like',Style::tableName().'.style_name', trim($searchModel->style_name)]);
        $dataProvider->query->andFilterWhere(['>',Style::tableName().'.status',-2]);
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
        $model = $model ?? new Style();
        $isNewRecord = $model->isNewRecord;
        // ajax 校验
        $this->activeFormValidate($model);
        $oldinfo = $model->toArray();
        if ($model->load(Yii::$app->request->post())) {
            //重新编辑后，审核状态改为待审核
            if($isNewRecord){ 
                $model->audit_status = AuditStatusEnum::SAVE;
                $model->creator_id = \Yii::$app->user->id;
            }else{
                $model->audit_status = AuditStatusEnum::PENDING;
            }
            $model->is_inlay = $model->type->is_inlay ?? 0;         
            try{                
                $trans = Yii::$app->trans->beginTransaction();
                if(false === $model->save()) {
                    throw new \Exception($this->getError($model));
                }
                //创建款号
                if($isNewRecord === true) {
                    if($model->style_sn != '') {
                        Yii::$app->styleService->style->createStyleSort($model);
                    }else {
                        Yii::$app->styleService->style->createStyleSn($model);
                    }                    
                }else if($model->audit_status != AuditStatusEnum::PASS){
                    if($model->is_autosn == 1) {
                        /* if($oldinfo['style_channel_id'] != $model->style_channel_id || $oldinfo['style_sex'] != $model->style_sex || $oldinfo['style_cate_id'] != $model->style_cate_id || $oldinfo['style_material'] != $model->style_material) {
                            Yii::$app->styleService->style->createStyleSn($model);
                        } */
                    }else {
                       Yii::$app->styleService->style->createStyleSort($model);
                    }
                }
                if($isNewRecord === true) { 
                    //创建自定义属性值
                    $command = \Yii::$app->db->createCommand("call sp_create_style_attributes(" . $model->id . ");");
                    $command->execute();
                    //镶嵌类(创建一条主石信息)
                    if ($model->is_inlay) {
                        $stoneM = new StyleStone();
                        $stone = [
                            'style_id' => $model->id,
                            'position' => StonePositionEnum::MAIN_STONE,
                            'stone_type' => StyleForm::getStoneTypeByProduct($model),
                            'creator_id' => \Yii::$app->user->identity->getId(),
                            'created_at' => time(),
                        ];
                        $stoneM->attributes = $stone;
                        if (false === $stoneM->save()) {
                            throw new \Exception($this->getError($stoneM));
                        }
                    }
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
     * ajax批量导入
     * @return mixed|string|\yii\web\Response
     * @throws
     */
    public function actionAjaxUpload()
    {
        $model = new StyleForm();
        $download = \Yii::$app->request->get('download',0);
        if($download){
            list($values, $fields) = $model->getTitleList();
            header("Content-Disposition: attachment;filename=【" . rand(000, 999) . "】款式数据导入(" . date('Ymd', time()) . ").csv");
            $content = implode($values, ",") . "\n" . implode($fields, ",") . "\n";
            echo iconv("utf-8", "gbk", $content);
            exit();
        }
        // ajax 校验
        $this->activeFormValidate($model);
        if (Yii::$app->request->isPost) {
            try {
                $trans = \Yii::$app->db->beginTransaction();
                $model->file = UploadedFile::getInstance($model, 'file');
                \Yii::$app->styleService->style->uploadStyles($model);
                $trans->commit();
                return $this->message("保存成功", $this->redirect(\Yii::$app->request->referrer), 'success');
            } catch (\Exception $e) {
                $trans->rollBack();
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
                StyleAttribute::deleteAll(['style_id' => $id]);
                //商品删除
                $styleGoods = StyleGoods::find()->where(['style_id' => $id])->select(['id'])->asArray()->all();
                $styleGoodsIds = array_column($styleGoods,'id');
                StyleGoodsAttribute::deleteAll(['goods_id' => $styleGoodsIds]);
                $a = StyleGoods::deleteAll(['style_id' => $id]);
                // 石头信息删除
                StyleStone::deleteAll(['style_id' => $id]);
                //工厂信息删除
                StyleFactory::deleteAll(['style_id' => $id]);
                // 工费信息删除
                StyleFactoryFee::deleteAll(['style_id' => $id]);
                //图片信息
                StyleImages::deleteAll(['style_id' => $id]);
                //日志信息
                StyleLog::deleteAll(['style_id' => $id]);
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

<?php

namespace addons\Style\backend\controllers;

use addons\Style\common\forms\QibanAttrForm;
use addons\Style\common\forms\QibanAuditForm;
use addons\Style\common\forms\StyleAttrForm;
use addons\Style\common\models\Style;
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


    /**
    * 首页
    *
    * @return string
    * @throws \yii\web\NotFoundHttpException
    */
    public function actionIndex()
    {
        $cate_id = Yii::$app->request->get('cate_id');
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

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $created_at = $searchModel->created_at;
        if (!empty($created_at)) {
            $dataProvider->query->andFilterWhere(['>=',Qiban::tableName().'.created_at', strtotime(explode('/', $created_at)[0])]);//起始时间
            $dataProvider->query->andFilterWhere(['<',Qiban::tableName().'.created_at', (strtotime(explode('/', $created_at)[1]) + 86400)] );//结束时间
        }

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel, 
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

        $search = Yii::$app->request->get('search');
        $jintuo_type = Yii::$app->request->get('jintuo_type');
        $this->modelClass = QibanAttrForm::class;
        $model = $this->findModel($id);
        $model = $model ?? new QibanAttrForm();
        $model->initAttrs();
        if($model->isNewRecord) {
            $style_sn = Yii::$app->request->get('style_sn');
            $model->style_sn = $style_sn;
        }else{
            $style_sn = $model->style_sn;
        }
        if($jintuo_type) {
            $model->jintuo_type = $jintuo_type;
        }

        if($style_sn && $search) {
            $skiUrl = Url::buildUrl(\Yii::$app->request->url,[],['search']);
            $style  = Style::find()->where(['style_sn'=>$style_sn])->one();
            if(!$style) {
                return $this->message("无效的款号", $this->redirect($skiUrl), 'error');
            }elseif($style->status != 1) {
                return $this->message("款号不可用", $this->redirect($skiUrl), 'error');
            }
            if($model->isNewRecord) {
                $model->style_cate_id = $style->style_cate_id;
                $model->product_type_id = $style->product_type_id;
                $model->qiban_type = QibanTypeEnum::HAVE_STYLE;
                $model->style_sex = $style->style_sex;
                $model->qiban_name = $style->style_name;
                $style_images = Yii::$app->styleService->style->getStyleImages($style_sn);
                $model->style_image = join(',',$style_images);
                $model->is_inlay  = $style->is_inlay;
            }else{
                $model->style_image = !empty($model->style_image)?explode(',', $model->style_image):[];
            }

            //根据款号获取属性值
            $style_model = new StyleAttrForm();
            $style_model->style_id = $style->id;
            $style_model->initAttrs();
            $model->style_id = $style->id;
        }
        if ($model->load(Yii::$app->request->post())) {
            //重新编辑后，审核状态改为未审核
            $model->audit_status = AuditStatusEnum::SAVE;
            $isNewRecord = $model->isNewRecord;
            if($isNewRecord) {
                $model->qiban_sn = SnHelper::createQibanSn();
            }            
            try{
                $trans = Yii::$app->trans->beginTransaction();
                if(false === $model->save()){
                    throw new \Exception($this->getError($model));
                }
                //创建属性关系表数据
                $model->createAttrs();
                $trans->commit();
                //前端提示
//                Yii::$app->getSession()->setFlash('success','保存成功');
                if($isNewRecord) {
                    $this->message("保存成功", $this->redirect(['view', 'id' => $model->id]), 'success');
                }else{
                    $this->message("保存成功", $this->redirect(Yii::$app->request->referrer), 'success');
                }
//                return ResultHelper::json(200, '保存成功');
            }catch (\Exception $e){
                $trans->rollBack();
                return ResultHelper::json(422, $e->getMessage());
            }
        }



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
        $model->initAttrs();
        
        //无款起版
        $model->qiban_type = QibanTypeEnum::NO_STYLE;        
        $model->style_cate_id = $style_cate_id ?? $model->style_cate_id;
        $model->jintuo_type = $jintuo_type ?? $model->jintuo_type;
        $model->product_type_id = $product_type_id ?? $model->product_type_id;
        $model->style_sn = 'QIBAN';
        $model->is_inlay = $model->type ? $model->type->is_inlay : 0;
        $model->style_image = !empty($model->style_image)?explode(',', $model->style_image):null;
        
        
        if ($model->load(Yii::$app->request->post())) {
            //重新编辑后，审核状态改为未审核
            $model->audit_status = AuditStatusEnum::SAVE;
            $isNewRecord = $model->isNewRecord;
            if($isNewRecord) {
                $model->qiban_sn = SnHelper::createQibanSn();
            }
            try{
                $trans = Yii::$app->trans->beginTransaction();
                if(false === $model->save()){
                    throw new \Exception($this->getError($model));
                }
                //创建属性关系表数据
                $model->createAttrs();

                $trans->commit();
                if($isNewRecord) {
                    $this->message("保存成功", $this->redirect(['view', 'id' => $model->id]), 'success');
                }else{
                    $this->message("保存成功", $this->redirect(Yii::$app->request->referrer), 'success');
                }
                //前端提示
//                Yii::$app->getSession()->setFlash('success','保存成功');
//                return ResultHelper::json(200, '保存成功');
            }catch (\Exception $e){
                $trans->rollBack();
                return ResultHelper::json(422, $e->getMessage());
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
        $this->modelClass = QibanAttrForm::class;
        $model = $this->findModel($id);
        $model = $model ?? new QibanAttrForm();
        $model->initAttrs();
        return $this->render($this->action->id, [
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
        if($model->audit_status != AuditStatusEnum::SAVE){
            return $this->message('单据不是保存状态', $this->redirect(\Yii::$app->request->referrer), 'error');
        }
        $model->audit_status = AuditStatusEnum::PENDING;
        if(false === $model->save()){
            return $this->message($this->getError($model), $this->redirect(\Yii::$app->request->referrer), 'error');
        }
        return $this->message('操作成功', $this->redirect(\Yii::$app->request->referrer), 'success');

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
                if($model->audit_status == AuditStatusEnum::PASS){
                    $model->auditor_id = \Yii::$app->user->id;
                    $model->audit_time = time();
                    $model->status = StatusEnum::ENABLED;
                }else{
                    $model->status = StatusEnum::DISABLED;
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
        if($model->audit_status == 0){
            $model->audit_status = 1;
        }

        return $this->renderAjax($this->action->id, [
                'model' => $model,
        ]);
    }
    
}

<?php

namespace addons\Purchase\backend\controllers;

use addons\Purchase\common\enums\ApplyConfirmEnum;
use addons\Purchase\common\forms\PurchaseApplyDiamondForm;
use addons\Purchase\common\forms\PurchaseApplyFormatForm;
use addons\Purchase\common\forms\PurchaseApplyGoodsConfimForm;
use addons\Shop\common\enums\AttrIdEnum;
use addons\Style\common\enums\JintuoTypeEnum;
use addons\Style\common\enums\StyleSexEnum;
use addons\Style\common\forms\DiamondAttrForm;
use addons\Style\common\forms\StyleAttrForm;
use addons\Style\common\models\Diamond;
use common\enums\ConfirmEnum;
use common\helpers\ArrayHelper;
use Yii;
use addons\Style\common\models\Attribute;
use common\models\base\SearchModel;
use common\traits\Curd;
use addons\Purchase\common\models\Purchase;
use common\helpers\Url;
use addons\Purchase\common\models\PurchaseGoods;
use common\helpers\ResultHelper;
use addons\Style\common\models\Style;
use addons\Purchase\common\forms\PurchaseApplyGoodsForm;
use addons\Style\common\forms\QibanAttrForm;
use addons\Style\common\models\Qiban;
use common\enums\StatusEnum;
use common\enums\AuditStatusEnum;
use addons\Purchase\common\forms\PurchaseGoodsAuditForm;
use addons\Style\common\enums\QibanTypeEnum;
use addons\Purchase\common\models\PurchaseApplyGoodsAttribute;
use addons\Purchase\common\models\PurchaseApply;
use common\helpers\SnHelper;
use addons\Purchase\common\enums\ApplyGoodsTypeEnum;
use addons\Purchase\common\enums\PurchaseGoodsTypeEnum;
/**
 * Attribute
 *
 * Class AttributeController
 * @property PurchaseApplyGoodsForm $modelClass
 * @package backend\modules\goods\controllers
 */
class PurchaseApplyGoodsController extends BaseController
{
    use Curd;
    
    /**
     * @var PurchaseApplyGoodsForm
     */
    public $modelClass = PurchaseApplyGoodsForm::class;
    /**
     * 首页
     *
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionIndex()
    {
        $apply_id = Yii::$app->request->get('apply_id');
        
        $searchModel = new SearchModel([
                'model' => $this->modelClass,
                'scenario' => 'default',
                'partialMatchAttributes' => ['goods_name'], // 模糊查询
                'defaultOrder' => [
                     'id' => SORT_DESC
                ],
                'pageSize' => $this->pageSize,
                'relations' => [
                    'auditor' => ['username'],
                    'designMember' => ['username'],
                    'goodsMember' => ['username'],
                ]
        ]);
        
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['=','apply_id',$apply_id]);
        $models = $dataProvider->models;
        foreach ($models as & $model){
            $attrs = $model->attrs ?? [];
            $model['attr'] = ArrayHelper::map($attrs,'attr_id','attr_value');
        }
        
        $apply = PurchaseApply::find()->where(['id'=>$apply_id])->one();
        return $this->render('index', [
                'dataProvider' => $dataProvider,
                'searchModel' => $searchModel,
                'apply'=> $apply,
                'tab'=>Yii::$app->request->get('tab',2),
                'tabList'=>Yii::$app->purchaseService->apply->menuTabList($apply_id,$this->returnUrl),
                'returnUrl'=>$this->returnUrl,
        ]);
    }
    /**
     * 有款添加/编辑
     * @var PurchaseApplyGoodsForm $model
     * @return mixed
     */
    public function actionEdit()
    {
        $this->layout = '@backend/views/layouts/iframe';

        $id = Yii::$app->request->get('id');
        $model = $this->findModel($id);        
        $model = $model ?? new PurchaseApplyGoodsForm();
        if($model->isNewRecord && ($return = $this->checkGoods($model)) !== true) {
            return $return;
        }

        if ($model->load(Yii::$app->request->post())) {
            if(!$model->validate()) {
                return ResultHelper::json(422, $this->getError($model));
            }
            try{                
                $trans = Yii::$app->trans->beginTransaction();
                $model->audit_status = AuditStatusEnum::SAVE;
                $model->confirm_status = ApplyConfirmEnum::GOODS;

                if(false === $model->save()){
                    throw new \Exception($this->getError($model));
                }

                //创建属性关系表数据
                $model->createAttrs();
                //更新采购汇总：总金额和总数量
                Yii::$app->purchaseService->apply->applySummary($model->apply_id);
                $trans->commit();
                //前端提示
                Yii::$app->getSession()->setFlash('success','保存成功');
                return ResultHelper::json(200, '保存成功');
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


    /***
     * @return array|mixed|string
     * @throws \yii\db\Exception
     * 添加裸钻
     */
    public function actionEditDiamond(){

        $this->layout = '@backend/views/layouts/iframe';
        $id = Yii::$app->request->get('id');
        $this->modelClass = PurchaseApplyDiamondForm::class;
        $model = $this->findModel($id);
        $model = $model ?? new PurchaseApplyDiamondForm();
        if($model->isNewRecord){
            $cert_id = Yii::$app->request->get('cert_id');
            $search = Yii::$app->request->get('search');
            $apply_id = Yii::$app->request->get('apply_id');
            if($search && $cert_id) {
                $diamond_goods = Diamond::find()->where(['cert_id'=>$cert_id, 'audit_status'=>AuditStatusEnum::PASS,'status'=>StatusEnum::ENABLED])->one();
                if(empty($diamond_goods)){
                    $skiUrl = Url::buildUrl(\Yii::$app->request->url,[],['search']);
                    return $this->message('此裸钻不存在或者审核没通过或者已下架', $this->redirect($skiUrl), 'error');
                }

                $model->jintuo_type = JintuoTypeEnum::Chengpin;
                $model->qiban_type = QibanTypeEnum::NON_VERSION;
                $model->style_sex = StyleSexEnum::COMMON;
                $model->style_cate_id = 15; //裸钻
                $model->product_type_id = 1; //钻石
                $model->goods_num = 1;
                $model->goods_name = $diamond_goods->goods_name;
                $model->style_sn = '';
                $model->qiban_sn = '';
                $model->goods_image = $diamond_goods->goods_image;
                $model->cert_id = $cert_id;
                $model->goods_sn = $cert_id;
                $model->apply_id = $apply_id;
                $model->cost_price = $diamond_goods->cost_price;
                $diamondForm = new DiamondAttrForm();
                $diamondForm->cert_id = $cert_id;
                $diamondForm->initAttrs();

                $model->attr_custom = $diamondForm->attr_custom;
                $model->attr_require = $diamondForm->attr_require;
            }

        }else{
            $order_goods_attr = PurchaseApplyGoodsAttribute::find()->where(['id'=>$model->id,'attr_id'=>AttrIdEnum::DIA_CERT_NO])->one();
            $model->cert_id = $order_goods_attr->attr_value;
        }

        if ($model->load(Yii::$app->request->post())) {
            if(!$model->validate()) {
                return ResultHelper::json(422, $this->getError($model));
            }
            try{
                $trans = Yii::$app->trans->beginTransaction();
                $model->audit_status = AuditStatusEnum::SAVE;
                $model->confirm_status = ApplyConfirmEnum::GOODS;

                if(false === $model->save()){
                    throw new \Exception($this->getError($model));
                }

                //创建属性关系表数据
                $model->createAttrs();
                //更新采购汇总：总金额和总数量
                Yii::$app->purchaseService->apply->applySummary($model->apply_id);
                $trans->commit();
                //前端提示
                Yii::$app->getSession()->setFlash('success','保存成功');
                return ResultHelper::json(200, '保存成功');
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
     * 版式编辑
     * @var PurchaseApplyGoodsForm $model
     * @return mixed
     */
    public function actionFormatEdit()
    {
        $this->layout = '@backend/views/layouts/iframe';

        $id = Yii::$app->request->get('id');
        $this->modelClass = PurchaseApplyFormatForm::className();
        $model = $this->findModel($id);
        $model = $model ?? new PurchaseApplyFormatForm();
        if ($model->load(Yii::$app->request->post())) {
            if(!$model->validate()) {
                return ResultHelper::json(422, $this->getError($model));
            }
            $format_info = Yii::$app->request->post('format_info');
            if($model->format_creator_id == ''){
                $model->format_creator_id = Yii::$app->user->identity->getId();
                $model->format_created_at = time();
            }
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
     * 无款添加/编辑
     * @property PurchaseGoodsForm $model
     * @return mixed
     */
    public function actionEditNoStyle()
    {
        $this->layout = '@backend/views/layouts/iframe';
        
        $id = Yii::$app->request->get('id');
        
        $apply_id = Yii::$app->request->get('apply_id');
        $style_cate_id = Yii::$app->request->get('style_cate_id');
        $product_type_id = Yii::$app->request->get('product_type_id');
        $jintuo_type = Yii::$app->request->get('jintuo_type');
        
        $model = $this->findModel($id);
        $model = $model ?? new PurchaseApplyGoodsForm();
        $isNewRecord = $model->isNewRecord;
        if($isNewRecord == true) {
            $model->apply_id = $apply_id;
            $model->goods_type = PurchaseGoodsTypeEnum::OTHER;
            $model->qiban_type = QibanTypeEnum::NON_VERSION;
            $model->style_channel_id = $model->apply->channel_id;
        }  
        $model->style_cate_id = $style_cate_id ?? $model->style_cate_id;
        $model->product_type_id = $product_type_id ?? $model->product_type_id;
        $model->jintuo_type = $jintuo_type ?? $model->jintuo_type;
        $model->is_inlay = $model->type ? $model->type->is_inlay : 0;
        
        if ($model->load(Yii::$app->request->post())) {
              
            try{
                $trans = Yii::$app->trans->beginTransaction();
                $model->audit_status = AuditStatusEnum::SAVE;
                $model->confirm_status = ApplyConfirmEnum::DESIGN;
                //申请单里无款默认为起版
                $model->qiban_type = QibanTypeEnum::NO_STYLE;

                if($isNewRecord == true) {
                    $model->goods_sn = SnHelper::createQibanSn('QBA');
                }
                if(false === $model->save()){
                    throw new \Exception($this->getError($model));
                }
                //创建属性关系表数据
                $model->createAttrs();
                //更新采购汇总
                Yii::$app->purchaseService->apply->applySummary($model->apply_id);
                
                $trans->commit();
                if($isNewRecord) {
                    return $this->message("保存成功", $this->redirect(\Yii::$app->request->referrer), 'success');
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
        $model = $this->findModel($id);
        return $this->render($this->action->id, [
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
            $model = $this->findModel($id);            
            if(!$model->apply) {
                throw new \Exception("无效采购单",422);
            }            
            if($model->apply->audit_status == AuditStatusEnum::PASS) {
                throw new \Exception("采购申请单已审核,不允许删除商品",422);
            }
            if (!$model->delete()) {
                throw new \Exception("删除失败",422);
            }
            //删除商品属性
            PurchaseApplyGoodsAttribute::deleteAll(['id'=>$id]);
            //更新单据汇总
            Yii::$app->purchaseService->apply->applySummary($model->apply->id);
            $trans->commit();
            
            return $this->message("删除成功", $this->redirect($this->returnUrl));
        }catch (\Exception $e) {
            
            $trans->rollback();
            return $this->message($e->getMessage(), $this->redirect($this->returnUrl), 'error');
        }
    }
    /**
     * 申请编辑
     * @property PurchaseApplyGoodsForm $model
     * @return mixed
     */
    public function actionApplyEdit()
    {
        $this->layout = '@backend/views/layouts/iframe';
        
        $id = Yii::$app->request->get('id');
        
        $this->modelClass = PurchaseApplyGoodsForm::class;
        $model = $this->findModel($id);
        $model = $model ?? new PurchaseApplyGoodsForm();
        
        if ($model->load(Yii::$app->request->post())) {
            if(!$model->validate()) {
                return ResultHelper::json(422, $this->getError($model));
            }
            try{
                $trans = Yii::$app->trans->beginTransaction();
                $model->createApply();
                $trans->commit();
                //前端提示
                Yii::$app->getSession()->setFlash('success','申请提交成功！审批通过后生效');
                return ResultHelper::json(200, '保存成功');
            }catch (\Exception $e){
                $trans->rollBack();
                return ResultHelper::json(422, $e->getMessage());
            }
        }
        $model->initApplyEdit();
        $render = $model->goods_type == PurchaseGoodsTypeEnum::STYLE ? 'edit': 'edit-no-style';
        return $this->render($render, [
                'model' => $model,
        ]);
    }





    /**
     * 查看审批
     * @property PurchaseApplyGoodsForm $model
     * @return mixed
     */
    public function actionApplyView()
    {
        
        $id = Yii::$app->request->get('id');
        //$this->modelClass = PurchaseApplyGoodsForm::class;
        $model = $this->findModel($id);
        $model = $model ?? new PurchaseApplyGoodsForm();
        $model->initApplyView();
        
        return $this->render($this->action->id, [
                'model' => $model,
                'returnUrl'=>$this->returnUrl
        ]);
    }
    /**
     * 申请编辑-审核(ajax)
     * @property PurchaseApplyGoodsForm $model
     * @return mixed
     */
    public function actionApplyAudit()
    {
        
        $returnUrl = Yii::$app->request->get('returnUrl',Yii::$app->request->referrer);
        
        $id = Yii::$app->request->get('id');        
        
        $this->modelClass = PurchaseApplyGoodsForm::class;
        $model = $this->findModel($id);
        $model = $model ?? new PurchaseApplyGoodsForm();
        
        $form  = new PurchaseGoodsAuditForm();   
        $form->id = $id;
        $form->audit_status = AuditStatusEnum::PASS;        
        // ajax 校验
        $this->activeFormValidate($form);
        if ($form->load(Yii::$app->request->post())) {
            try {
                $trans = Yii::$app->trans->beginTransaction();                
                if($form->audit_status == AuditStatusEnum::PASS){
                     $model->initApplyEdit();
                     $model->createAttrs();
                     $model->apply_info = json_encode($model->apply_info);
                }
                $model->is_apply = 0;
                $model->save(false);  
                //金额汇总
                Yii::$app->purchaseService->apply->applySummary($model->apply_id);
               
                $trans->commit();
                return $this->message("保存成功", $this->redirect($returnUrl), 'success');
            }catch (\Exception $e){
                $trans->rollback();
                return $this->message($e->getMessage(), $this->redirect($returnUrl), 'error');
            }
            
        }
        return $this->renderAjax($this->action->id, [
                'model' => $form,
        ]);
    }
    
    /**
     * 查询商品
     * @param unknown $model
     * @param unknown $style_sn
     * @return mixed|string
     */
    private function checkGoods(& $model) 
    {
        
        $apply_id = Yii::$app->request->get('apply_id');        
        $goods_sn = Yii::$app->request->get('goods_sn');
        $search = Yii::$app->request->get('search');
        $jintuo_type = Yii::$app->request->get('jintuo_type');
        
        if($jintuo_type) {
            $model->jintuo_type = $jintuo_type;
        }
        if($model->isNewRecord) {
            $model->apply_id = $apply_id;
        }
        $apply = PurchaseApply::find()->where(['id'=>$apply_id])->one();
        if($model->isNewRecord && $search && $goods_sn) {
            
            $skiUrl = Url::buildUrl(\Yii::$app->request->url,[],['search']);
            $style  = Style::find()->where(['style_sn'=>$goods_sn])->one();
            if(!$style) {
                $qiban = Qiban::find()->where(['qiban_sn'=>$goods_sn])->one();
                if(!$qiban) {
                    return $this->message("[款号/起版号]不存在", $this->redirect($skiUrl), 'error');
                }elseif($qiban->status != StatusEnum::ENABLED) {
                    return $this->message("起版号不可用", $this->redirect($skiUrl), 'error');
                }else{
                    $exist = PurchaseGoods::find()->where(['qiban_sn'=>$goods_sn,'status'=>StatusEnum::ENABLED])->count();
                    if($exist) {
                        return $this->message("起版号已添加过", $this->redirect($skiUrl), 'error');
                    }
                    $model->style_id = $qiban->id;
                    $model->goods_sn = $goods_sn;
                    $model->goods_image = $qiban->style_image;
                    $model->qiban_sn = $goods_sn;
                    $model->goods_type = PurchaseGoodsTypeEnum::STYLE;
                    $model->qiban_type = $qiban->qiban_type;
                    $model->style_sn = $qiban->style_sn;
                    $model->style_cate_id = $qiban->style_cate_id;
                    $model->product_type_id = $qiban->product_type_id;
                    $model->style_channel_id = $apply->channel_id;
                    $model->style_sex = $qiban->style_sex;
                    $model->goods_name = $qiban->qiban_name;
                    $model->cost_price  = $qiban->cost_price;
                    $model->jintuo_type = $qiban->jintuo_type;
                    $model->is_inlay = $qiban->is_inlay;
                    $model->stone_info = $qiban->stone_info;
                    $model->remark = $qiban->remark;
                    $model->goods_images = $qiban->style_images;

                    $qibanForm = new QibanAttrForm();
                    $qibanForm->id = $qiban->id;
                    $qibanForm->initAttrs();
                    
                    $model->attr_custom = $qibanForm->attr_custom;
                    $model->attr_require = $qibanForm->attr_require;
                }
            }elseif($style->status != StatusEnum::ENABLED) {
                return $this->message("款号不可用", $this->redirect($skiUrl), 'error');
            }else{
                $model->style_id = $style->id;
                $model->goods_sn = $goods_sn;
                $model->style_sn = $goods_sn;
                $model->goods_image = $style->style_image;
                $model->goods_type = PurchaseGoodsTypeEnum::STYLE;
                $model->qiban_type = QibanTypeEnum::NON_VERSION;
                $model->style_cate_id = $style->style_cate_id;
                $model->product_type_id = $style->product_type_id;
                $model->style_channel_id = $apply->channel_id;
                $model->style_sex = $style->style_sex;
                $model->goods_name = $style->style_name;
                $model->cost_price = $style->cost_price;
                $model->is_inlay = $style->is_inlay;
                $images = Yii::$app->styleService->style->getStyleImages($goods_sn);
                $model->goods_images = join(',',$images);


                $styleForm = new StyleAttrForm();
                $styleForm->style_id = $style->id;
                $styleForm->initAttrs();

                $model->attr_custom = $styleForm->attr_custom;
                $model->attr_require = [];
            }
        }
        
        return true;
    }

    /**
     * ajax 设计确认
     *
     * @return mixed|string|\yii\web\Response
     * @throws \yii\base\ExitException
     */
    public function actionDesignConfirm()
    {
        $id = Yii::$app->request->get('id');
        $model = $this->findModel($id);
        $model->confirm_status = ApplyConfirmEnum::CONFIRM;
        $model->confirm_design_time = time();
        $model->confirm_design_id = \Yii::$app->user->identity->id;
        if(false === $model->save()){
            return $this->message($this->getError($model), $this->redirect(\Yii::$app->request->referrer), 'error');
        }

        return $this->message('操作成功', $this->redirect(\Yii::$app->request->referrer), 'success');

    }

    /**
     * ajax 商品确认
     *
     * @return mixed|string|\yii\web\Response
     * @throws \yii\base\ExitException
     */
    public function actionGoodsConfirm()
    {
        $id = Yii::$app->request->get('id');
        $this->modelClass = PurchaseApplyGoodsConfimForm::class;
        $model = $this->findModel($id);
        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {
            try{
                $trans = Yii::$app->db->beginTransaction();
                if($model->is_design_qiban == ConfirmEnum::YES){
                    $model->confirm_status = ApplyConfirmEnum::DESIGN;
                }else{
                    $model->confirm_status = ApplyConfirmEnum::CONFIRM;
                }
                $model->confirm_goods_time = time();
                $model->confirm_goods_id = \Yii::$app->user->identity->id;
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


    /**
     * ajax 商品审核（旧）
     *
     * @return mixed|string|\yii\web\Response
     * @throws \yii\base\ExitException
     */
    public function actionGoodsAudit()
    {
        $id = Yii::$app->request->get('id');
        $model = $this->findModel($id);
        if($model->audit_status == AuditStatusEnum::SAVE) {
            $model->audit_status = AuditStatusEnum::PASS;
        }
        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {
            try{
                $trans = Yii::$app->db->beginTransaction();
                $model->audit_time = time();
                $model->auditor_id = \Yii::$app->user->identity->id;
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


    /**
     * ajax 设计审核（旧）
     *
     * @return mixed|string|\yii\web\Response
     * @throws \yii\base\ExitException
     */
    public function actionDesignAudit()
    {
        $id = Yii::$app->request->get('id');
        $model = $this->findModel($id);
        if($model->audit_status == AuditStatusEnum::SAVE) {
            $model->audit_status = AuditStatusEnum::PASS;
        }
        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {
            try{
                $trans = Yii::$app->db->beginTransaction();
                $model->audit_time = time();
                $model->auditor_id = \Yii::$app->user->identity->id;
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

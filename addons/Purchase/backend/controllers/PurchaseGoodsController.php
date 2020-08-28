<?php

namespace addons\Purchase\backend\controllers;

use \addons\Style\common\enums\AttrIdEnum;
use addons\Style\common\enums\InlayEnum;
use addons\Style\common\forms\StyleAttrForm;
use addons\Style\common\models\PartsStyle;
use addons\Supply\common\enums\PeishiTypeEnum;
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
use addons\Purchase\common\forms\PurchaseGoodsForm;
use addons\Style\common\forms\QibanAttrForm;
use addons\Style\common\models\Qiban;
use common\enums\StatusEnum;
use addons\Purchase\common\models\PurchaseGoodsAttribute;
use common\enums\AuditStatusEnum;
use addons\Purchase\common\forms\PurchaseGoodsAuditForm;
use addons\Purchase\common\enums\PurchaseTypeEnum;
use addons\Style\common\enums\QibanTypeEnum;
use addons\Supply\common\models\ProduceStone;
use addons\Supply\common\enums\PeishiStatusEnum;
use addons\Purchase\common\forms\PurchaseStoneIncreaseForm;
use common\enums\ConfirmEnum;
use addons\Supply\common\enums\PeiliaoStatusEnum;
use addons\Supply\common\models\ProduceGold;
use addons\Purchase\common\forms\PurchaseGoldIncreaseForm;
use addons\Supply\common\models\Produce;
/**
 * Attribute
 *
 * Class AttributeController
 * @property PurchaseGoodsForm $modelClass
 * @package backend\modules\goods\controllers
 */
class PurchaseGoodsController extends BaseController
{
    use Curd;
    
    /**
     * @var PurchaseGoodsForm
     */
    public $modelClass = PurchaseGoodsForm::class;
    /**
     * @var int
     */
    public $purchaseType = PurchaseTypeEnum::GOODS;
    /**
     * 首页
     *
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionIndex()
    {
        $purchase_id = Yii::$app->request->get('purchase_id');
        
        $searchModel = new SearchModel([
                'model' => $this->modelClass,
                'scenario' => 'default',
                'partialMatchAttributes' => ['goods_name'], // 模糊查询
                'defaultOrder' => [
                     'id' => SORT_DESC
                ],
                'pageSize' => $this->pageSize,
                'relations' => [
                     
                ]
        ]);
        
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['=','purchase_id',$purchase_id]);
        $models = $dataProvider->models;
        foreach ($models as & $model){
            $attrs = $model->attrs ?? [];
            $model['attr'] = ArrayHelper::map($attrs,'attr_id','attr_value');
        }
        
        $purchase = Purchase::find()->where(['id'=>$purchase_id])->one();
        return $this->render('index', [
                'dataProvider' => $dataProvider,
                'searchModel' => $searchModel,
                'purchase'=> $purchase,
                'tab'=>Yii::$app->request->get('tab',2),
                'tabList'=>Yii::$app->purchaseService->purchase->menuTabList($purchase_id,$this->returnUrl),
                'returnUrl'=>$this->returnUrl,
        ]);
    }
    /**
     * 编辑/创建
     * @var PurchaseGoodsForm $model
     * @return mixed
     */
    public function actionEdit()
    {
        $this->layout = '@backend/views/layouts/iframe';

        $id = Yii::$app->request->get('id');
        $model = $this->findModel($id);        
        $model = $model ?? new PurchaseGoodsForm();
        if($model->isNewRecord && ($return = $this->checkGoods($model)) !== true) {   
            return $return;
        } 

        if ($model->load(Yii::$app->request->post())) {  
            if(!$model->validate()) {
                return ResultHelper::json(422, $this->getError($model));
            }
            try{                
                $trans = Yii::$app->trans->beginTransaction();

                //【镶石费=镶石单价*总副石数量】
                $second_stone_num = 0;
                $atts = $model->getPostAttrs();
                if(isset($atts[AttrIdEnum::SIDE_STONE1_NUM]) && !empty($atts[AttrIdEnum::SIDE_STONE1_NUM])){
                    $second_stone_num += $atts[AttrIdEnum::SIDE_STONE1_NUM];
                }
                if(isset($atts[AttrIdEnum::SIDE_STONE2_NUM]) && !empty($atts[AttrIdEnum::SIDE_STONE2_NUM])){
                    $second_stone_num += $atts[AttrIdEnum::SIDE_STONE2_NUM];
                }
                $model->xiangqian_fee = $model->xianqian_price * $second_stone_num;


                if(false === $model->save()){
                    throw new \Exception($this->getError($model));
                }     
                //创建属性关系表数据
                $model->createAttrs();
                //更新采购汇总：总金额和总数量
                Yii::$app->purchaseService->purchase->purchaseSummary($model->purchase_id);
                $trans->commit();
                //前端提示
                Yii::$app->getSession()->setFlash('success','保存成功');
                return ResultHelper::json(200, '保存成功');
            }catch (\Exception $e){
                $trans->rollBack();
                return ResultHelper::json(422, $e->getMessage());
            }
        }
        //var_dump(1);die;
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
        $purchase_id = Yii::$app->request->get('purchase_id');
        $this->modelClass = PurchaseGoodsForm::class;
        $model = $this->findModel($id);
        $model = $model ?? new PurchaseGoodsForm();
        $model->initAttrs();
        $purchase = Purchase::find()->where(['id'=>$purchase_id])->one();
        return $this->render($this->action->id, [
            'model' => $model,
            'purchase' => $purchase
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
        $purchase_id = Yii::$app->request->get('purchase_id');        
        
        try{   
            
            $trans = Yii::$app->trans->beginTransaction();  
            
            $purchase = Purchase::find()->where(['id'=>$purchase_id])->one();
            if($purchase->audit_status == AuditStatusEnum::PASS) {
                throw new \Exception("采购单已审核,不允许删除",422);
            }
            
            $model = $this->findModel($id);            
            if($model->produce_id) {
                throw new \Exception("该商品已布产,不允许删除",422);
            }
            if (!$model->delete()) {
                throw new \Exception("删除失败",422);
            }
            
            //删除商品属性
            PurchaseGoodsAttribute::deleteAll(['id'=>$id]);
            //更新单据汇总
            Yii::$app->purchaseService->purchase->purchaseSummary($purchase_id);
            $trans->commit();
            
            return $this->message("删除成功", $this->redirect($this->returnUrl));
        }catch (\Exception $e) {
            
            $trans->rollback();
            return $this->message($e->getMessage(), $this->redirect($this->returnUrl), 'error');
        }
    }
    /**
     * 申请编辑
     * @property PurchaseGoodsForm $model
     * @return mixed
     */
    public function actionApplyEdit()
    {
        $this->layout = '@backend/views/layouts/iframe';
        
        $id = Yii::$app->request->get('id');
        
        $this->modelClass = PurchaseGoodsForm::class;
        $model = $this->findModel($id);
        $model = $model ?? new PurchaseGoodsForm();
        //非镶切 配石类型默认不配石
        $model->peishi_type = $model->is_inlay == InlayEnum::No ? PeishiTypeEnum::None : $model->peishi_type;
        
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

        return $this->render($this->action->id, [
                'model' => $model,
        ]);
    }
    /**
     * 查看审批
     * @property PurchaseGoodsForm $model
     * @return mixed
     */
    public function actionApplyView()
    {
        
        $id = Yii::$app->request->get('id');
        $this->modelClass = PurchaseGoodsForm::class;
        $model = $this->findModel($id);
        $model = $model ?? new PurchaseGoodsForm();
        $model->initApplyView();
        
        return $this->render($this->action->id, [
                'model' => $model,
                'returnUrl'=>$this->returnUrl
        ]);
    }
    /**
     * 申请编辑-审核(ajax)
     * @property PurchaseGoodsForm $model
     * @return mixed
     */
    public function actionApplyAudit()
    {
        
        $returnUrl = Yii::$app->request->get('returnUrl',Yii::$app->request->referrer);
        
        $id = Yii::$app->request->get('id');        
        
        $this->modelClass = PurchaseGoodsForm::class;
        $model = $this->findModel($id);
        $model = $model ?? new PurchaseGoodsForm();
        
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


                    //【镶石费=镶石单价*总副石数量】
                    $second_stone_num = 0;
                    $atts = $model->getPostAttrs();
                    if(isset($atts[AttrIdEnum::SIDE_STONE1_NUM]) && !empty($atts[AttrIdEnum::SIDE_STONE1_NUM])){
                        $second_stone_num += $atts[AttrIdEnum::SIDE_STONE1_NUM];
                    }
                    if(isset($atts[AttrIdEnum::SIDE_STONE2_NUM]) && !empty($atts[AttrIdEnum::SIDE_STONE2_NUM])){
                        $second_stone_num += $atts[AttrIdEnum::SIDE_STONE2_NUM];
                    }
                    $model->xiangqian_fee = $model->xianqian_price * $second_stone_num;
                }
                $model->is_apply = 0;
                $model->save(false);
                //金额汇总
                Yii::$app->purchaseService->purchase->purchaseSummary($model->purchase_id);
                //同步布产单
                Yii::$app->purchaseService->purchase->syncProduce($model->purchase_id,$model->id);
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
        
        $purchase_id = Yii::$app->request->get('purchase_id');        
        $goods_sn = Yii::$app->request->get('goods_sn');
        $search = Yii::$app->request->get('search');
        $jintuo_type = Yii::$app->request->get('jintuo_type');
        
        if($jintuo_type) {
            $model->jintuo_type = $jintuo_type;
        }
        if($model->isNewRecord) {
            $model->purchase_id = $purchase_id;
        }
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
                    $exist = PurchaseGoods::find()->where(['purchase_id'=>$purchase_id,'qiban_sn'=>$goods_sn,'status'=>StatusEnum::ENABLED])->count();
                    if($exist) {
                        return $this->message("起版号已添加过", $this->redirect($skiUrl), 'error');
                    }
                    $model->style_id = $qiban->id;
                    $model->goods_sn = $goods_sn;
                    $model->qiban_sn = $goods_sn;
                    $model->qiban_type = $qiban->qiban_type;
                    $model->style_sn = $qiban->style_sn;
                    $model->style_cate_id = $qiban->style_cate_id;
                    $model->product_type_id = $qiban->product_type_id;
                    $model->style_channel_id = $qiban->style_channel_id;
                    $model->style_sex = $qiban->style_sex;
                    $model->goods_name = $qiban->qiban_name;
                    $model->cost_price  = $qiban->cost_price;
                    $model->jintuo_type = $qiban->jintuo_type;
                    $model->is_inlay = $qiban->is_inlay;
                    $model->stone_info = $qiban->stone_info;
                    $model->remark = $qiban->remark;
                    $model->goods_image = $qiban->style_image;

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
                $model->qiban_type = QibanTypeEnum::NON_VERSION;
                $model->style_cate_id = $style->style_cate_id;
                $model->product_type_id = $style->product_type_id;
                $model->style_channel_id = $style->style_channel_id;
                $model->style_sex = $style->style_sex;
                $model->goods_name = $style->style_name;
                $model->cost_price = $style->cost_price;
                $model->is_inlay = $style->is_inlay;                
                $model->goods_image = $style->style_image;
                $styleForm = new StyleAttrForm();
                $styleForm->style_id = $style->id;
                $styleForm->initAttrs();
                $model->attr_custom = $styleForm->attr_custom;
                $model->attr_require = [];
            }
            //非镶切 配石类型默认不配石
            $model->peishi_type = $model->is_inlay == InlayEnum::No ? PeishiTypeEnum::None : "";
        }
        
        return true;
    }

    /**
     * ajax编辑/创建(配件)
     *
     * @return mixed|string|\yii\web\Response
     * @throws \yii\base\ExitException
     */
    public function actionAjaxParts()
    {
        $id = Yii::$app->request->get('id');
        $model = $this->findModel($id) ?? new PurchaseGoodsForm();
        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {
            $model->parts_info = !empty($model->parts_info) ? serialize($model->parts_info) : '';
            if (false == $model->save(true, ['parts_info'])) {
                return $this->message($this->getError($model), $this->redirect(\Yii::$app->request->referrer), 'error');
            }
            Yii::$app->getSession()->setFlash('success', '保存成功');
            return $this->redirect(\Yii::$app->request->referrer);
        }
        if (!empty($model->parts_info)) {
            $parts_list = unserialize($model->parts_info);
        }
        return $this->renderAjax($this->action->id, [
            'model' => $model,
            'parts_list' => $parts_list ?? [],
        ]);
    }

    /**
     * 查询配件款式信息
     * @return array
     */
    public function actionAjaxGetParts()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $style_sn = \Yii::$app->request->get('style_sn');
        $style = PartsStyle::find()->select(['parts_name','parts_type','metal_type'])->where(['style_sn'=>$style_sn])->one();
        if(empty($style)){
            return ResultHelper::json(422,$style_sn.'款号不存在', []);
        }
        $data = [
            'parts_name' => $style->parts_name??"",
            'parts_type' => $style->parts_type??"",
            'metal_type' => $style->metal_type??"",
        ];
        return ResultHelper::json(200,'查询成功', $data);
    }
    
    /**
     * ajax补石创建
     *
     * @return mixed|string|\yii\web\Response
     * @throws \yii\base\ExitException
     */
    public function actionAjaxStoneIncrease()
    {
        $id = Yii::$app->request->get('id');
        $this->modelClass = PurchaseStoneIncreaseForm::class;
        $form = $this->findModel($id); 
        // ajax 校验
        $this->activeFormValidate($form);
        if ($form->load(Yii::$app->request->post())) {
            try {
                $trans = Yii::$app->trans->beginTransaction();
                $model = new ProduceStone();
                //复制石头信息
                $model->attributes = $form->toArray(['produce_id','produce_sn','supplier_id','from_order_sn','from_type','stone_type','stone_position','stone_weight','stone_spec','shape','secai','carat','color','clarity','cert_type','cert_no']);             
                $model->stone_num = $form->increase_num;
                $model->stone_weight = round($model->stone_num * $model->carat,2); 
                $model->remark = $form->increase_remark;
                $model->peishi_status = PeishiStatusEnum::PENDING;
                $model->is_increase = ConfirmEnum::YES;
                if(false === $model->save()) {
                     throw new \Exception($this->getError($model));
                }
                Produce::updateAll(['peishi_status'=>PeishiStatusEnum::PENDING],['id'=>$form->produce_id]);
                $trans->commit();
                return $this->message("保存成功", $this->redirect(\Yii::$app->request->referrer), 'success');
            }catch (\Exception $e){
                $trans->rollback();
                return $this->message($e->getMessage(), $this->redirect(\Yii::$app->request->referrer), 'error');
            }
        }       
        return $this->renderAjax($this->action->id, [
                'model' => $form,
        ]);
    }
    
    /**
     * ajax 编辑补石
     *
     * @return mixed|string|\yii\web\Response
     * @throws \yii\base\ExitException
     */
    public function actionAjaxStoneEdit()
    {
        $id = Yii::$app->request->get('id');
        $this->modelClass = ProduceStone::class;
        $model = $this->findModel($id);
        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {
            try {
                $trans = Yii::$app->trans->beginTransaction();                
                if(false === $model->save()) {
                    throw new \Exception($this->getError($model));
                }
                $trans->commit();
                return $this->message("保存成功", $this->redirect(\Yii::$app->request->referrer), 'success');
            }catch (\Exception $e){
                $trans->rollback();
                return $this->message($e->getMessage(), $this->redirect(\Yii::$app->request->referrer), 'error');
            }
        }
        return $this->renderAjax($this->action->id, [
                'model' => $model,
        ]);
    }
    
    /**
     * ajax补石创建
     *
     * @return mixed|string|\yii\web\Response
     * @throws \yii\base\ExitException
     */
    public function actionAjaxGoldIncrease()
    {
        $id = Yii::$app->request->get('id');
        $this->modelClass = PurchaseGoldIncreaseForm::class;
        $form = $this->findModel($id);
        // ajax 校验
        $this->activeFormValidate($form);
        if ($form->load(Yii::$app->request->post())) {
            try {
                $trans = Yii::$app->trans->beginTransaction();
                $model = new ProduceGold();
                //复制石头信息
                $model->attributes = $form->toArray(['produce_id','produce_sn','supplier_id','from_order_sn','from_type','gold_type','gold_weight','gold_spec']);
                $model->gold_weight = $form->increase_weight;
                $model->remark = $form->increase_remark;
                $model->peiliao_status = PeiliaoStatusEnum::PENDING;
                $model->is_increase = ConfirmEnum::YES;
                if(false === $model->save()) {
                    throw new \Exception($this->getError($model));
                }
                Produce::updateAll(['peiliao_status'=>PeiliaoStatusEnum::PENDING],['id'=>$form->produce_id]);
                $trans->commit();
                return $this->message("保存成功", $this->redirect(\Yii::$app->request->referrer."#box-gold"), 'success');
            }catch (\Exception $e){
                $trans->rollback();
                return $this->message($e->getMessage(), $this->redirect(\Yii::$app->request->referrer), 'error');
            }
        }
        return $this->renderAjax($this->action->id, [
                'model' => $form,
        ]);
    }
    
    /**
     * ajax 编辑补料
     *
     * @return mixed|string|\yii\web\Response
     * @throws \yii\base\ExitException
     */
    public function actionAjaxGoldEdit()
    {
        $id = Yii::$app->request->get('id');
        $this->modelClass = ProduceGold::class;
        $model = $this->findModel($id);
        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {
            try {
                $trans = Yii::$app->trans->beginTransaction();
                if(false === $model->save()) {
                    throw new \Exception($this->getError($model));
                }
                $trans->commit();
                return $this->message("保存成功", $this->redirect(\Yii::$app->request->referrer."#box-gold"), 'success');
            }catch (\Exception $e){
                $trans->rollback();
                return $this->message($e->getMessage(), $this->redirect(\Yii::$app->request->referrer), 'error');
            }
        }
        return $this->renderAjax($this->action->id, [
                'model' => $model,
        ]);
    }
}

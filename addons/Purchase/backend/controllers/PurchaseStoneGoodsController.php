<?php

namespace addons\Purchase\backend\controllers;

use addons\Purchase\common\enums\PurchaseTypeEnum;
use addons\Purchase\common\forms\PurchaseReceiptForm;
use addons\Purchase\common\forms\PurchaseStoneReceiptGoodsForm;
use addons\Style\common\models\StoneStyle;
use common\helpers\Url;
use Yii;
use addons\Style\common\models\Attribute;
use common\models\base\SearchModel;
use common\traits\Curd;
use common\helpers\ResultHelper;
use addons\Purchase\common\forms\PurchaseGoodsForm;
use common\enums\AuditStatusEnum;
use addons\Purchase\common\forms\PurchaseGoodsAuditForm;
use addons\Purchase\common\forms\PurchaseStoneGoodsForm;
use addons\Purchase\common\models\PurchaseStone;
/**
 * Attribute
 *
 * Class AttributeController
 * @property PurchaseGoodsForm $modelClass
 * @package backend\modules\goods\controllers
 */
class PurchaseStoneGoodsController extends BaseController
{
    use Curd;
    
    /**
     * @var PurchaseStoneGoodsForm
     */
    public $modelClass = PurchaseStoneGoodsForm::class;
    public $purchase_type = PurchaseTypeEnum::MATERIAL_STONE;
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
        $dataProvider->query->andWhere(['>','status',-1]);
        
        $purchase = PurchaseStone::find()->where(['id'=>$purchase_id])->one();
        return $this->render('index', [
                'dataProvider' => $dataProvider,
                'searchModel' => $searchModel,
                'purchase'=> $purchase,
                'tab'=>Yii::$app->request->get('tab',2),
                'tabList'=>Yii::$app->purchaseService->stone->menuTabList($purchase_id,$this->returnUrl),
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
        $purchase_id = Yii::$app->request->get('purchase_id');
        
        $model = $this->findModel($id) ?? new PurchaseStoneGoodsForm();
        if ($model->load(Yii::$app->request->post())) {
            if($model->isNewRecord && !empty($purchase_id)) {
                $model->purchase_id = $purchase_id;
            }
            
            if(!$model->validate()) {
                return ResultHelper::json(422, $this->getError($model));
            }
            try{
                $trans = Yii::$app->trans->beginTransaction();

                $stoneStyle = StoneStyle::find()->select(['stone_type','stone_shape'])->where(['style_sn'=>$model->goods_sn])->one();

                $model->cost_price = bcmul($model->stone_price, $model->goods_weight, 3);
                $model->stone_type = $stoneStyle->stone_type;
                $model->stone_shape = $stoneStyle->stone_shape;
                $model->goods_weight = bcmul($model->stone_weight, $model->goods_num, 2);

                if(false === $model->save()){
                    throw new \Exception($this->getError($model));
                }
                //更新采购汇总：总金额和总数量
                Yii::$app->purchaseService->stone->summary($model->purchase_id);
                $trans->commit();
                //前端提示
                Yii::$app->getSession()->setFlash('success','保存成功');
                return ResultHelper::json(200, '保存成功');
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
        $this->modelClass = PurchaseGoodsForm::class;
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
        $purchase_id = Yii::$app->request->get('purchase_id');
        
        try{
            
            $trans = Yii::$app->trans->beginTransaction();
            
            $purchase = PurchaseStone::find()->where(['id'=>$purchase_id])->one();
            if($purchase->audit_status != AuditStatusEnum::SAVE) {
                throw new \Exception("采购单不是保存状态,不允许删除",422);
            }
            
            $model = $this->findModel($id) ?? new PurchaseStoneGoodsForm();
            if (!$model->delete()) {
                throw new \Exception("删除失败",422);
            }
            //更新单据汇总
            Yii::$app->purchaseService->stone->summary($purchase_id);
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
        
        $model = $this->findModel($id);
        $model = $model ?? new PurchaseStoneGoodsForm();
        
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
        $model = $model ?? new PurchaseStoneGoodsForm();
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
        $model = $model ?? new PurchaseStoneGoodsForm();
        
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
                Yii::$app->purchaseService->stone->purchaseSummary($model->purchase_id);                
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
     * 分批收货
     *
     * @return mixed
     * @throws
     */
    public function actionWarehouse()
    {
        $ids = Yii::$app->request->get('ids');
        $check = Yii::$app->request->get('check', null);
        $model = new PurchaseStoneGoodsForm();
        $model->ids = $ids;
        if($check){
            try{
                \Yii::$app->purchaseService->purchase->receiptValidate($model, $this->purchase_type);
                return ResultHelper::json(200, '', ['url'=>Url::to([$this->action->id, 'ids'=>$ids])]);
            }catch (\Exception $e){
                return ResultHelper::json(422, $e->getMessage());
            }
        }
        if ($model->load(Yii::$app->request->post())) {
            try{
                $trans = Yii::$app->trans->beginTransaction();
                //同步采购单至采购收货单
                Yii::$app->purchaseService->purchase->syncPurchaseToReceipt($model, $this->purchase_type, $model->getIds());
                $trans->commit();
                Yii::$app->getSession()->setFlash('success','操作成功');
                return ResultHelper::json(200, '操作成功');
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
     * 查询石料款号信息
     * @return array
     */
    public function actionAjaxGetStone()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $goods_sn = \Yii::$app->request->get('goods_sn');
        $model = StoneStyle::find()->select(['stone_type','stone_shape','product_size_min','product_size_max'])->where(['style_sn'=>$goods_sn])->one();
        $stone_type = \Yii::$app->attr->valueName($model->stone_type)??"";
        $stone_shape = \Yii::$app->attr->valueName($model->stone_shape)??"";
        $data = [
            'stone_type' => $model->stone_type,
            'stone_shape' => $model->stone_shape,
            'goods_name' => $stone_type.$stone_shape.$model->product_size_min.$model->product_size_max,
        ];
        return ResultHelper::json(200,'查询成功', $data);
    }
}

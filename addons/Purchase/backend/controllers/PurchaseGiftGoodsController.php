<?php

namespace addons\Purchase\backend\controllers;

use addons\Style\common\models\StyleGift;
use Yii;
use common\traits\Curd;
use common\helpers\ResultHelper;
use common\models\base\SearchModel;
use addons\Purchase\common\models\PurchaseGift;
use addons\Purchase\common\models\PurchaseGiftGoods;
use addons\Purchase\common\forms\PurchaseGiftGoodsForm;
use addons\Purchase\common\enums\PurchaseTypeEnum;
use addons\Style\common\models\Style;
use common\enums\AuditStatusEnum;
use common\helpers\Url;

/**
 * Attribute
 *
 * Class AttributeController
 * @property PurchaseGiftGoodsForm $modelClass
 * @package backend\modules\goods\controllers
 */
class PurchaseGiftGoodsController extends BaseController
{
    use Curd;

    /**
     * @var PurchaseGiftGoodsForm
     */
    public $modelClass = PurchaseGiftGoodsForm::class;

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
        $dataProvider->query->andWhere(['=', 'purchase_id', $purchase_id]);
        $dataProvider->query->andWhere(['>', 'status', -1]);

        $purchase = PurchaseGift::find()->where(['id' => $purchase_id])->one();
        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'purchase' => $purchase,
            'tab' => Yii::$app->request->get('tab', 2),
            'tabList' => Yii::$app->purchaseService->gift->menuTabList($purchase_id, $this->returnUrl),
            'returnUrl' => $this->returnUrl,
        ]);
    }

    /**
     * 编辑/创建
     * @return mixed
     * @throws
     * @var PurchaseGiftGoodsForm $model
     */
    public function actionEdit()
    {
        $this->layout = '@backend/views/layouts/iframe';

        $id = Yii::$app->request->get('id');
        $purchase_id = Yii::$app->request->get('purchase_id');
        $model = $this->findModel($id) ?? new PurchaseGiftGoods();
        if ($model->load(Yii::$app->request->post())) {
            if ($model->isNewRecord && !empty($purchase_id)) {
                $model->purchase_id = $purchase_id;
            }
            if (!$model->validate()) {
                return ResultHelper::json(422, $this->getError($model));
            }
            try {
                $trans = Yii::$app->trans->beginTransaction();
                $style = Style::findOne(['style_sn' => $model->goods_sn]);
                if (!$style) {
                    return ResultHelper::json(422, "款号不存在");
                }
                $model->product_type_id = $style->product_type_id ?? "";
                $model->style_cate_id = $style->style_cate_id ?? "";
                $model->style_sex = $style->style_sex ?? "";
                $model->goods_image = $style->style_image ?? "";
                //$model->cost_price = bcmul($model->gold_price, $model->goods_weight, 3);
                if (false === $model->save()) {
                    throw new \Exception($this->getError($model));
                }
                //更新采购汇总：总金额和总数量
                Yii::$app->purchaseService->gift->summary($model->purchase_id);
                $trans->commit();
                //前端提示
                Yii::$app->getSession()->setFlash('success', '保存成功');
                return ResultHelper::json(200, '保存成功');
            } catch (\Exception $e) {
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
     * @throws
     */
    public function actionView()
    {
        $id = Yii::$app->request->get('id');
        $this->modelClass = PurchaseGiftGoodsForm::class;
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
        try {
            $trans = Yii::$app->trans->beginTransaction();
            $purchase = PurchaseGiftGoodsForm::find()->where(['id' => $purchase_id])->one();
            if ($purchase->audit_status == AuditStatusEnum::PASS) {
                throw new \Exception("采购单已审核,不允许删除", 422);
            }
            $model = $this->findModel($id) ?? new PurchaseGiftGoods();
            if (!$model->delete()) {
                throw new \Exception("删除失败", 422);
            }
            //更新单据汇总
            Yii::$app->purchaseService->gift->summary($purchase_id);
            $trans->commit();
            return $this->message("删除成功", $this->redirect($this->returnUrl));
        } catch (\Exception $e) {

            $trans->rollback();
            return $this->message($e->getMessage(), $this->redirect($this->returnUrl), 'error');
        }
    }

    /**
     * 申请编辑
     * @return mixed
     * @throws
     * @property PurchaseGiftGoodsForm $model
     */
    public function actionApplyEdit()
    {
        $this->layout = '@backend/views/layouts/iframe';

        $id = Yii::$app->request->get('id');

        $model = $this->findModel($id);
        $model = $model ?? new PurchaseGiftGoodsForm();

        if ($model->load(Yii::$app->request->post())) {
            if (!$model->validate()) {
                return ResultHelper::json(422, $this->getError($model));
            }
            try {
                $trans = Yii::$app->trans->beginTransaction();
                $model->createApply();
                $trans->commit();
                //前端提示
                Yii::$app->getSession()->setFlash('success', '申请提交成功！审批通过后生效');
                return ResultHelper::json(200, '保存成功');
            } catch (\Exception $e) {
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
     * @return mixed
     * @property PurchaseGiftGoodsForm $model
     */
    public function actionApplyView()
    {

        $id = Yii::$app->request->get('id');
        $this->modelClass = PurchaseGiftGoodsForm::class;
        $model = $this->findModel($id);
        $model = $model ?? new PurchaseGiftGoodsForm();
        $model->initApplyView();

        return $this->render($this->action->id, [
            'model' => $model,
            'returnUrl' => $this->returnUrl
        ]);
    }

    /**
     * 申请编辑-审核(ajax)
     * @return mixed
     * @property PurchaseGiftGoodsForm $model
     */
    public function actionApplyAudit()
    {

        $returnUrl = Yii::$app->request->get('returnUrl', Yii::$app->request->referrer);

        $id = Yii::$app->request->get('id');

        $this->modelClass = PurchaseGiftGoodsForm::class;
        $model = $this->findModel($id);
        $model = $model ?? new PurchaseGiftGoodsForm();

        $form = new PurchaseGoodsAuditForm();
        $form->id = $id;
        $form->audit_status = AuditStatusEnum::PASS;
        // ajax 校验
        $this->activeFormValidate($form);
        if ($form->load(Yii::$app->request->post())) {

            try {

                $trans = Yii::$app->trans->beginTransaction();
                if ($form->audit_status == AuditStatusEnum::PASS) {
                    $model->initApplyEdit();
                    $model->createAttrs();
                    $model->apply_info = json_encode($model->apply_info);
                }
                $model->is_apply = 0;
                $model->save(false);
                //金额汇总
                Yii::$app->purchaseService->gift->purchaseSummary($model->purchase_id);
                $trans->commit();
                return $this->message("保存成功", $this->redirect($returnUrl), 'success');
            } catch (\Exception $e) {
                $trans->rollback();
                return $this->message($e->getMessage(), $this->redirect($returnUrl), 'error');
            }

        }
        return $this->renderAjax($this->action->id, [
            'model' => $form,
        ]);
    }

    /**
     *
     * 分批收货
     * @return mixed
     * @throws
     */
    public function actionWarehouse()
    {
        $id = Yii::$app->request->get('id');
        $ids = Yii::$app->request->get('ids');
        $check = Yii::$app->request->get('check');
        $model = new PurchaseGiftGoodsForm();
        $model->ids = $ids;
        if ($check) {
            try {
                \Yii::$app->purchaseService->purchase->receiptValidate($model, PurchaseTypeEnum::MATERIAL_GIFT);
                return ResultHelper::json(200, '', ['url' => Url::to([$this->action->id, 'id'=>$id, 'ids' => $ids])]);
            } catch (\Exception $e) {
                return ResultHelper::json(422, $e->getMessage());
            }
        }
        if ($model->load(Yii::$app->request->post())) {
            try {
                $trans = Yii::$app->trans->beginTransaction();
                //同步采购单至采购收货单
                \Yii::$app->purchaseService->purchase->syncPurchaseToReceipt($model, PurchaseTypeEnum::MATERIAL_GIFT, $model->getIds());
                //同步收货信息
                \Yii::$app->purchaseService->purchase->receiveSummary($id, PurchaseTypeEnum::MATERIAL_GIFT);
                $trans->commit();
                \Yii::$app->getSession()->setFlash('success', '操作成功');
                return ResultHelper::json(200, '操作成功');
            } catch (\Exception $e) {
                $trans->rollBack();
                return ResultHelper::json(422, $e->getMessage());
            }
        }
        return $this->render($this->action->id, [
            'model' => $model,
        ]);
    }

    /**
     * 查询赠品款号信息
     * @return array
     */
    public function actionAjaxGetGift()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $goods_sn = \Yii::$app->request->get('goods_sn');
        $model = StyleGift::find()->where(['style_sn' => $goods_sn])->one();
        //$model = new StyleGift();
        //$style = Style::findOne(['style_sn' => $goods_sn]);
        $data = [
            'gift_name' => $model->gift_name,
            'style_cate_id' => $model->style_cate_id,
            //'product_type_id' => $model->product_type_id,
            'style_sex' => $model->style_sex,
            'material_type' => $model->material_type,
            'material_color' => $model->material_color,
            'finger_hk' => $model->finger_hk,
            'finger' => $model->finger,
            'chain_length' => $model->chain_length,
            'goods_size' => $model->goods_size,
            'cost_price' => $model->cost_price,
        ];
        return ResultHelper::json(200, '查询成功', $data);
    }
}

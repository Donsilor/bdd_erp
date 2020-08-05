<?php

namespace addons\Purchase\backend\controllers;

use Yii;
use common\models\base\SearchModel;
use addons\Purchase\common\models\PurchaseReceipt;
use addons\Purchase\common\forms\PurchaseStoneReceiptGoodsForm;
use addons\Purchase\common\models\PurchaseReceiptGoods;
use addons\Purchase\common\models\PurchaseStoneReceiptGoods;
use addons\Purchase\common\forms\PurchaseReceiptForm;
use addons\Warehouse\common\enums\BillStatusEnum;
use addons\Supply\common\enums\QcTypeEnum;
use addons\Purchase\common\enums\PurchaseTypeEnum;
use common\helpers\ResultHelper;
use common\helpers\Url;
use common\traits\Curd;

/**
 * ReceiptGoods
 *
 * Class ReceiptGoodsController
 * @property PurchaseStoneReceiptGoodsForm $modelClass
 * @package backend\modules\goods\controllers
 */
class StoneReceiptGoodsController extends BaseController
{
    use Curd;
    
    /**
     * @var $modelClass PurchaseStoneReceiptGoodsForm
     */
    public $modelClass = PurchaseStoneReceiptGoodsForm::class;
    public $purchaseType = PurchaseTypeEnum::MATERIAL_STONE;
    
    /**
     * 首页
     *
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionIndex()
    {
        $receipt_id = Yii::$app->request->get('receipt_id');
        $tab = Yii::$app->request->get('tab',2);
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['stone-receipt/index']));
        $searchModel = new SearchModel([
                'model' => $this->modelClass,
                'scenario' => 'default',
                'partialMatchAttributes' => ['purchase_sn'], // 模糊查询
                'defaultOrder' => [
                     'id' => SORT_DESC
                ],
                'pageSize' => $this->pageSize,
                'relations' => [

                ]
        ]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['=','receipt_id',$receipt_id]);
        $dataProvider->query->andWhere(['>','status',-1]);
        $receipt = PurchaseReceipt::find()->where(['id'=>$receipt_id])->one();
        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'tabList' => \Yii::$app->purchaseService->receipt->menuTabList($receipt_id, $this->purchaseType, $returnUrl),
            'returnUrl' => $returnUrl,
            'tab'=>$tab,
            'receipt' => $receipt,
        ]);
    }

    /**
     * 质检列表
     *
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionIqcIndex()
    {
        $tab = Yii::$app->request->get('tab',2);
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['stone-receipt/index']));
        $searchModel = new SearchModel([
            'model' => $this->modelClass,
            'scenario' => 'default',
            'partialMatchAttributes' => ['purchase_sn'], // 模糊查询
            'defaultOrder' => [
                'id' => SORT_DESC
            ],
            'pageSize' => $this->pageSize,
            'relations' => [
                'receipt' => ['supplier_id','receipt_no','receipt_status']
            ]
        ]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, ['supplier_id', 'receipt_no']);
        $dataProvider->query->andWhere(['>',PurchaseStoneReceiptGoods::tableName().'.status',-1]);
        $dataProvider->query->andWhere(['=','receipt.receipt_status', BillStatusEnum::CONFIRM]);
        $supplier_id = $searchModel->supplier_id;
        if($supplier_id){
            $dataProvider->query->andWhere(['=','receipt.supplier_id', $supplier_id]);
        }
        $receipt_no = $searchModel->receipt_no;
        if($receipt_no){
            $dataProvider->query->andWhere(['=','receipt.receipt_no', $receipt_no]);
        }
        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'returnUrl' => $returnUrl,
            'tab'=>$tab,
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
        $model = $this->findModel($id) ?? new PurchaseStoneReceiptGoodsForm();

        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {

            $model->cost_price = bcmul($model->stone_price, $model->goods_weight, 2);
            $model->goods_weight = bcmul($model->stone_weight, $model->goods_num, 3);

            if(false === $model->save()){
                return $this->message($this->getError($model), $this->redirect(['index']), 'error');
            }
            \Yii::$app->purchaseService->receipt->purchaseReceiptSummary($model->receipt_id, $this->purchaseType);
            \Yii::$app->getSession()->setFlash('success','保存成功');
            return $this->redirect(Yii::$app->request->referrer);
        }

        return $this->renderAjax($this->action->id, [
            'model' => $model,
        ]);
    }

    /**
     * 编辑
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionEditAll()
    {
        $receipt_id = Yii::$app->request->get('receipt_id');
        $tab = Yii::$app->request->get('tab',3);
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['stone-receipt/index']));
        $searchModel = new SearchModel([
            'model' => $this->modelClass,
            'scenario' => 'default',
            'partialMatchAttributes' => ['purchase_sn'], // 模糊查询
            'defaultOrder' => [
                'id' => SORT_DESC
            ],
            'pageSize' => $this->pageSize,
            'relations' => [

            ]
        ]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['=','receipt_id',$receipt_id]);
        $dataProvider->query->andWhere(['>','status',-1]);
        $receipt = PurchaseReceipt::find()->where(['id'=>$receipt_id])->one();
        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'tabList' => \Yii::$app->purchaseService->receipt->menuTabList($receipt_id, $this->purchaseType, $returnUrl, $tab),
            'returnUrl' => $returnUrl,
            'tab'=>$tab,
            'receipt' => $receipt,
        ]);
    }

    /**
     * IQC批量质检
     *
     * @return mixed
     */
    public function actionIqc()
    {
        $this->layout = '@backend/views/layouts/iframe';

        $ids = Yii::$app->request->get('ids');
        $check = Yii::$app->request->get('check',null);
        $model = new PurchaseStoneReceiptGoodsForm();
        $model->ids = $ids;
        if($check){
            try{
                \Yii::$app->purchaseService->receipt->iqcValidate($model, $this->purchaseType);
                return ResultHelper::json(200, '', ['url'=>Url::to(['iqc', 'ids'=>$ids])]);
            }catch (\Exception $e){
                return ResultHelper::json(422, $e->getMessage());
            }
        }
        if ($model->load(Yii::$app->request->post())) {
            try{
                $trans = Yii::$app->trans->beginTransaction();

                \Yii::$app->purchaseService->receipt->qcIqc($model, $this->purchaseType);

                $trans->commit();
                Yii::$app->getSession()->setFlash('success','保存成功');
                return ResultHelper::json(200, '保存成功');
            }catch (\Exception $e){
                $trans->rollBack();
                return ResultHelper::json(422, $e->getMessage());
            }
        }
        $model->goods_status = QcTypeEnum::PASS;
        return $this->render($this->action->id, [
            'model' => $model,
        ]);
    }

    /**
     * 批量生成不良返厂单
     *
     * @return mixed
     */
    public function actionAjaxDefective()
    {
        $ids = Yii::$app->request->post('ids');
        $model = new PurchaseStoneReceiptGoodsForm();
        $model->ids = $ids;
        try{
            $trans = Yii::$app->trans->beginTransaction();

            \Yii::$app->purchaseService->receipt->batchDefective($model, $this->purchaseType);

            $trans->commit();
            return $this->message("保存成功", $this->redirect(Yii::$app->request->referrer), 'success');
        }catch (\Exception $e){
            $trans->rollBack();
            return $this->message("保存失败:". $e->getMessage(),  $this->redirect(Yii::$app->request->referrer), 'error');
        }
    }

    /**
     *
     * 批量生成不良返厂单2
     * @return mixed
     * @throws
     */
    public function actionDefective()
    {
        $this->layout = '@backend/views/layouts/iframe';

        $ids = Yii::$app->request->get('ids');
        $check = Yii::$app->request->get('check', null);
        $model = new PurchaseStoneReceiptGoodsForm();
        $model->ids = $ids;
        if($check){
            try{
                \Yii::$app->purchaseService->receipt->DefectiveValidate($model, $this->purchaseType);
                return ResultHelper::json(200, '', ['url'=>Url::to([$this->action->id, 'ids'=>$ids])]);
            }catch (\Exception $e){
                return ResultHelper::json(422, $e->getMessage());
            }
        }
        if ($model->load(Yii::$app->request->post())) {
            try{
                $trans = Yii::$app->trans->beginTransaction();

                \Yii::$app->purchaseService->receipt->batchDefective($model, $this->purchaseType);

                $trans->commit();
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
     * 批量申请入库-采购收货单
     *
     * @return mixed
     */
    public function actionWarehouse()
    {
        $ids = Yii::$app->request->get('ids');
        $check = Yii::$app->request->get('check', null);
        $model = new PurchaseStoneReceiptGoodsForm();
        $model->ids = $ids;
        if($check){
            try{
                $receipt_id = \Yii::$app->purchaseService->receipt->warehouseValidate($model, $this->purchaseType);
                return ResultHelper::json(200, '', ['url'=>Url::to([$this->action->id, 'id'=>$receipt_id, 'ids'=>$ids])]);
            }catch (\Exception $e){
                return ResultHelper::json(422, $e->getMessage());
            }
        }
        $id = Yii::$app->request->get('id');
        $model = PurchaseReceiptForm::findOne(['id'=>$id]);
        $model = $model ?? new PurchaseReceiptForm();
        if ($model->load(Yii::$app->request->post())) {
            try{
                $trans = Yii::$app->trans->beginTransaction();
                if(false === $model->save()) {
                    throw new \Exception($this->getError($model));
                }
                //同步石包采购收货单至买石单
                Yii::$app->purchaseService->receipt->syncReceiptToStoneBillMs($model);
                $trans->commit();
                Yii::$app->getSession()->setFlash('success','申请入库成功');
                return ResultHelper::json(200, '申请入库成功');
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
     * 删除/关闭
     *
     * @param $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        if (!($model = $this->modelClass::findOne($id))) {
            return $this->message("找不到数据", $this->redirect(['index']), 'error');
        }

        try{
            $trans = \Yii::$app->db->beginTransaction();

            $model = PurchaseReceiptGoods::find()->where(['id'=>$id])->one();

            if(false === $model->delete()){
                throw new \Exception($this->getError($model));
            }

            //更新收货单汇总：总金额和总数量
            $res = \Yii::$app->purchaseService->receipt->purchaseReceiptSummary($model->receipt_id, $this->purchaseType);
            if(false === $res){
                throw new \yii\db\Exception('更新单据汇总失败');
            }

            \Yii::$app->getSession()->setFlash('success','删除成功');
            $trans->commit();
            return $this->redirect(\Yii::$app->request->referrer);
        }catch (\Exception $e){
            $trans->rollBack();
            return $this->message($e->getMessage(), $this->redirect(\Yii::$app->request->referrer), 'error');
        }
    }
}

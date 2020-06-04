<?php

namespace addons\Purchase\backend\controllers;

use Yii;
use common\models\base\SearchModel;
use addons\Purchase\common\models\PurchaseReceipt;
use addons\Purchase\common\forms\PurchaseGoldReceiptGoodsForm;
use addons\Purchase\common\models\PurchaseReceiptGoods;
use addons\Purchase\common\models\PurchaseGoldReceiptGoods;
use addons\Purchase\common\forms\PurchaseReceiptForm;
use addons\Style\common\enums\AttrIdEnum;
use addons\Supply\common\enums\QcTypeEnum;
use addons\Supply\common\models\Produce;
use addons\Supply\common\models\ProduceAttribute;
use addons\Supply\common\models\ProduceShipment;
use addons\Purchase\common\enums\PurchaseTypeEnum;
use common\helpers\ResultHelper;
use common\helpers\Url;
use common\traits\Curd;
use yii\base\Exception;

/**
 * ReceiptGoods
 *
 * Class ReceiptGoodsController
 * @property ReceiptGoodsForm $modelClass
 * @package backend\modules\goods\controllers
 */
class GoldReceiptGoodsController extends BaseController
{
    use Curd;
    
    /**
     * @var $modelClass PurchaseReceiptGoodsForm
     */
    public $modelClass = PurchaseGoldReceiptGoodsForm::class;
    public $purchaseType = PurchaseTypeEnum::MATERIAL_GOLD;
    
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
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['gold-receipt-goods/index']));
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
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['gold-receipt-goods/index']));
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
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, ['supplier_id', 'receipt_no']);
        $dataProvider->query->andWhere(['>',PurchaseGoldReceiptGoods::tableName().'.status',-1]);
        $supplier_id = $searchModel->supplier_id;
        if($supplier_id){
            $dataProvider->query->andWhere(['=',PurchaseReceipt::tableName().'.supplier_id', $supplier_id]);
        }
        $receipt_no = $searchModel->receipt_no;
        if($receipt_no){
            $dataProvider->query->andWhere(['=',PurchaseReceipt::tableName().'.receipt_no', $receipt_no]);
        }
        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'returnUrl' => $returnUrl,
            'tab'=>$tab,
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
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['gold-receipt-goods/index']));
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
        $ids = Yii::$app->request->get('ids');
        $model = new PurchaseGoldReceiptGoodsForm();
        $model->ids = $ids;
        try{
            \Yii::$app->purchaseService->goldReceipt->iqcValidate($model);
            return ResultHelper::json(200, '', ['url'=>'/purchase/gold-receipt-goods/ajax-iqc?ids='.$ids]);
        }catch (\Exception $e){
            return ResultHelper::json(422, $e->getMessage());
        }
    }

    /**
     * IQC批量质检
     *
     * @return mixed
     */
    public function actionAjaxIqc()
    {
        $this->layout = '@backend/views/layouts/iframe';

        $ids = Yii::$app->request->get('ids');
        $model = new PurchaseGoldReceiptGoodsForm();
        $model->ids = $ids;
        if ($model->load(Yii::$app->request->post())) {
            try{
                $trans = Yii::$app->trans->beginTransaction();

                \Yii::$app->purchaseService->goldReceipt->qcIqc($model);

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
        $model = new PurchaseGoldReceiptGoodsForm();
        $model->ids = $ids;
        try{
            $trans = Yii::$app->trans->beginTransaction();

            \Yii::$app->purchaseService->goldReceipt->batchDefective($model);

            $trans->commit();
            return $this->message("保存成功", $this->redirect(Yii::$app->request->referrer), 'success');
        }catch (\Exception $e){
            $trans->rollBack();
            return $this->message("保存失败:". $e->getMessage(),  $this->redirect(Yii::$app->request->referrer), 'error');
        }
    }

    /**
     * IQC批量质检
     *
     * @return mixed
     */
    public function actionWarehouse()
    {
        $receipt_id = Yii::$app->request->get('receipt_id');
        $ids = Yii::$app->request->get('ids');
        $model = new PurchaseGoldReceiptGoodsForm();
        $model->ids = $ids;
        try{
            \Yii::$app->purchaseService->goldReceipt->warehouseValidate($model);
            return ResultHelper::json(200, '', ['url'=>'/purchase/gold-receipt-goods/ajax-warehouse?id='.$receipt_id.'&ids='.$ids]);
        }catch (\Exception $e){
            return ResultHelper::json(422, $e->getMessage());
        }
    }

    /**
     * 批量申请入库-采购收货单
     *
     * @return mixed
     */
    public function actionAjaxWarehouse()
    {
        $id = Yii::$app->request->get('id');
        $ids = Yii::$app->request->get('ids');
        $model = PurchaseReceiptForm::findOne(['id'=>$id]);
        $model->ids = $ids;
        if ($model->load(Yii::$app->request->post())) {
            try{
                $trans = Yii::$app->trans->beginTransaction();
                //$model->is_to_warehouse = WhetherEnum::ENABLED;
                if(false === $model->save()) {
                    throw new \Exception($this->getError($model));
                }
                //同步采购收货单至L单
                Yii::$app->purchaseService->goldReceipt->syncReceiptToBillInfoL($model);
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
            $res = \Yii::$app->purchaseService->goldReceipt->purchaseReceiptSummary($model->receipt_id);
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

<?php

namespace addons\Purchase\backend\controllers;

use Yii;
use common\models\base\SearchModel;
use addons\Purchase\common\models\PurchaseReceipt;
use addons\Purchase\common\models\PurchaseReceiptGoods;
use addons\Purchase\common\forms\PurchaseReceiptForm;
use addons\Purchase\common\forms\PurchaseReceiptGoodsForm;
use addons\Purchase\common\enums\PurchaseTypeEnum;
use addons\Purchase\common\enums\ReceiptStatusEnum;
use addons\Supply\common\models\Produce;
use addons\Supply\common\enums\QcTypeEnum;
use common\helpers\ResultHelper;
use common\helpers\Url;
use common\traits\Curd;
use yii\base\Exception;

/**
 * ReceiptGoods
 *
 * Class ReceiptGoodsController
 * @property PurchaseReceiptGoodsForm $modelClass
 * @package backend\modules\goods\controllers
 */
class ReceiptGoodsController extends BaseController
{
    use Curd;
    
    /**
     * @var $modelClass PurchaseReceiptGoodsForm
     */
    public $modelClass = PurchaseReceiptGoodsForm::class;
    public $purchaseType = PurchaseTypeEnum::GOODS;
    
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
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['receipt-goods/index', 'receipt_id'=>$receipt_id]));
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
        $dataProvider->key = 'id';
        $dataProvider->query->andWhere(['=', 'receipt_id', $receipt_id]);
        $dataProvider->query->andWhere(['>', PurchaseReceiptGoods::tableName().'.status', -1]);

        $receipt = PurchaseReceipt::find()->where(['id'=>$receipt_id])->one();
        return $this->render($this->action->id, [
            'model' => new PurchaseReceiptGoodsForm(),
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
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['receipt-goods/index']));
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
        $dataProvider->query->andWhere(['>',PurchaseReceiptGoods::tableName().'.status',-1]);
        $dataProvider->query->andWhere(['=','receipt.receipt_status', ReceiptStatusEnum::CONFIRM]);
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
     * 创建
     * @property PurchaseReceiptGoodsForm $model
     * @return mixed
     */
    public function actionAdd()
    {
        $this->layout = '@backend/views/layouts/iframe';
        $id = \Yii::$app->request->get('receipt_id');
        $model = PurchaseReceiptForm::findOne(['id'=>$id]);
        $modelG = new PurchaseReceiptGoodsForm();
        $model->produce_sns = \Yii::$app->request->get('produce_sns');
        $goods_list = [];
        if(\Yii::$app->request->get('search') && $model->produce_sns){
            $skiUrl = Url::buildUrl(\Yii::$app->request->url,[],['search']);
            try{
                $goods_list = \Yii::$app->purchaseService->receipt->getGoodsByProduceSn($model);
            }catch (\Exception $e){
                return $this->message($e->getMessage(), $this->redirect($skiUrl), 'error');
            }
        }
        if($model->load(\Yii::$app->request->post()) && !empty($goods_list)){
            try{
                $trans = Yii::$app->db->beginTransaction();
                $model->goods = $goods_list;
                \Yii::$app->purchaseService->receipt->addReceiptGoods($model);
                $trans->commit();
                \Yii::$app->getSession()->setFlash('success', '保存成功');
                return $this->redirect(Yii::$app->request->referrer);
            }catch (\Exception $e){
                $trans->rollBack();
                return ResultHelper::json(422, '保存失败'.$e->getMessage());
            }
        }
        return $this->render($this->action->id, [
            'model' => $model,
            'modelG' => $modelG,
            'goods_list' => $goods_list,
            'num' => count($goods_list),
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
        $model = $this->findModel($id);
        return $this->render($this->action->id, [
            'model' => $model,
        ]);
    }

    /**
     * 质检详情
     * @return mixed
     */
    public function actionIqcView()
    {
        $id = Yii::$app->request->get('id');
        $model = $this->findModel($id);
        $model = $model ?? new PurchaseReceiptGoodsForm();
        $bill = PurchaseReceipt::find()->select(['receipt_no','receipt_status'])->where(['id'=>$model->receipt_id])->one();
        $produce = Produce::find()->select(['id'])->where(['produce_sn'=>$model->produce_sn])->one();
        //$goods = $model->getGoodsView();
        return $this->render($this->action->id, [
            'model' => $model,
            'bill' => $bill,
            //'goods' => $goods,
            'produce' => $produce,
            'returnUrl'=>$this->returnUrl
        ]);
    }

    /**
     * ajax编辑
     *
     * @return mixed|string|\yii\web\Response
     * @throws \yii\base\ExitException
     */
    public function actionEdit()
    {
        $this->layout = '@backend/views/layouts/iframe';

        $id = \Yii::$app->request->get('id');
        $model = $this->findModel($id);
        $model = $model ?? new PurchaseReceiptGoods();
        // ajax 校验
        if ($model->load(\Yii::$app->request->post())) {
            try{
                if(false === $model->save()) {
                    throw new \Exception($this->getError($model));
                }
                Yii::$app->getSession()->setFlash('success','保存成功');
                return ResultHelper::json(200, '保存成功');
            }catch (\Exception $e){
                return ResultHelper::json(422, $e->getMessage());
            }
        }
        return $this->render($this->action->id, [
            'model' => $model,
        ]);
    }

    /**
     * ajax批量编辑
     *
     * @return mixed|string|\yii\web\Response
     * @throws \yii\base\ExitException
     */
    public function actionBatchEdit()
    {
        $this->layout = '@backend/views/layouts/iframe';

        $ids = Yii::$app->request->post('ids');
        $ids = $ids ?? Yii::$app->request->get('ids');
        $model = new PurchaseReceiptGoodsForm();
        $model->ids = $ids;
        $id_arr = $model->getIds();
        if(!$id_arr){
            return ResultHelper::json(422, "ID不能为空");
        }
        $name = Yii::$app->request->post('name');
        $name = $name ?? Yii::$app->request->get('name');
        if(!$name){
            return ResultHelper::json(422, "字段错误");
        }
        if(Yii::$app->request->isPost){
            $value = Yii::$app->request->post('value');
            if(!$value){
                return ResultHelper::json(422, "输入值不能为空");
            }
            try{
                $trans = Yii::$app->trans->beginTransaction();
                foreach ($id_arr as $id) {
                    $goods = PurchaseReceiptGoods::findOne(['id'=>$id]);
                    $goods->$name = $value;
                    if(false === $goods->validate()) {
                        throw new \Exception($this->getError($goods));
                    }
                    if(false === $goods->save()) {
                        throw new \Exception($this->getError($goods));
                    }
                }
                $trans->commit();
                Yii::$app->getSession()->setFlash('success','保存成功');
                return ResultHelper::json(200, '保存成功');
            }catch (\Exception $e){
                $trans->rollBack();
                return ResultHelper::json(422, $e->getMessage());
            }
        }
        $attr_id = Yii::$app->request->get('attr_id',0);
        if(!$attr_id){
            return ResultHelper::json(422, '参数错误');
        }
        $check = Yii::$app->request->get('check',null);
        if($check){
            return ResultHelper::json(200, '', ['url'=>Url::to([$this->action->id, 'ids' => $ids, 'name' => $name, 'attr_id' => $attr_id])]);
        }
        $style_arr = $model::find()->where(['id'=>$id_arr])->select(['style_sn'])->asArray()->distinct('style_sn')->all();
        if(count($style_arr) != 1){
            return ResultHelper::json(422, '请选择同款的商品进行操作');
        }
        $style_sn = $style_arr[0]['style_sn']??"";
        $attr_arr = Yii::$app->styleService->styleAttribute->getAttrValueListByStyle($style_sn, $attr_id);
        return $this->render($this->action->id, [
            'model' => $model,
            'ids' => $ids,
            'name'=> $name,
            'attr_arr' =>$attr_arr
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
        $tab = \Yii::$app->request->get('tab',3);
        $returnUrl = \Yii::$app->request->get('returnUrl',Url::to(['receipt-goods/index', 'receipt_id' => $receipt_id]));
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
        return $this->render('edit-all', [
            'model' => new PurchaseReceiptGoodsForm(),
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
        $check = Yii::$app->request->get('check', null);
        $model = new PurchaseReceiptGoodsForm();
        $model->ids = $ids;
        if($check){
            try{
                \Yii::$app->purchaseService->receipt->iqcValidate($model, $this->purchaseType);
                return ResultHelper::json(200, '', ['url'=>Url::to([$this->action->id, 'ids'=>$ids])]);
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
     *
     * 批量生成不良返厂单
     * @return mixed
     * @throws
     */
    public function actionAjaxDefective()
    {
        $ids = Yii::$app->request->post('ids');
        $model = new PurchaseReceiptGoodsForm();
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
        $model = new PurchaseReceiptGoodsForm();
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
        $model = new PurchaseReceiptGoodsForm();
        $model->ids = $ids;
        if($check){
            try{
                $receipt_id = \Yii::$app->purchaseService->receipt->warehouseValidate($model, $this->purchaseType);
                return ResultHelper::json(200, '', ['url'=>Url::to([$this->action->id,'id'=>$receipt_id, 'ids'=>$ids])]);
            }catch (\Exception $e){
                return ResultHelper::json(422, $e->getMessage());
            }
        }
        $id = Yii::$app->request->get('id');
        $model = PurchaseReceiptForm::findOne($id);
        $model = $model ?? new PurchaseReceiptForm();
        if ($model->load(Yii::$app->request->post())) {
            try{
                $trans = Yii::$app->trans->beginTransaction();
                if(false === $model->save()) {
                    throw new \Exception($this->getError($model));
                }
                //同步采购收货单至L单
                Yii::$app->purchaseService->receipt->syncReceiptToBillL($model);
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

    /**
     *
     * 批量删除
     * @return mixed
     */
    public function actionBatchDelete()
    {
        $ids = Yii::$app->request->post('ids');
        if (empty($ids)) {
            return $this->message("ID不能为空", $this->redirect(['index']), 'error');
        }
        foreach ($ids as $id) {
            if (!($model = $this->modelClass::findOne($id))) {
                return $this->message("找不到数据", $this->redirect(['index']), 'error');
            }
        }
        try {
            $trans = \Yii::$app->db->beginTransaction();
            PurchaseReceiptGoodsForm::deleteAll(['id' => $ids]);
            \Yii::$app->purchaseService->receipt->purchaseReceiptSummary($model->receipt_id, $this->purchaseType);
            $trans->commit();
            \Yii::$app->getSession()->setFlash('success', '删除成功');
            return $this->redirect(\Yii::$app->request->referrer);
        } catch (\Exception $e) {
            $trans->rollBack();
            return $this->message($e->getMessage(), $this->redirect(\Yii::$app->request->referrer), 'error');
        }
    }

    /**
     *
     * 同步更新价格
     * @return mixed
     */
    public function actionUpdatePrice()
    {
        $ids = Yii::$app->request->post('ids');
        if (empty($ids)) {
            return $this->message("ID不能为空", $this->redirect(['index']), 'error');
        }
        try {
            $trans = \Yii::$app->db->beginTransaction();
            foreach ($ids as $id) {
                $model = PurchaseReceiptGoodsForm::findOne($id);
                if(!empty($model)){
                    \Yii::$app->purchaseService->receipt->syncUpdatePrice($model);
                }
            }
            \Yii::$app->purchaseService->receipt->purchaseReceiptSummary($model->receipt_id, $this->purchaseType);
            $trans->commit();
            \Yii::$app->getSession()->setFlash('success', '刷新成功');
            return $this->redirect(\Yii::$app->request->referrer);
        } catch (\Exception $e) {
            $trans->rollBack();
            return $this->message($e->getMessage(), $this->redirect(\Yii::$app->request->referrer), 'error');
        }
    }
}

<?php

namespace addons\Warehouse\backend\controllers;

use addons\Warehouse\common\enums\AdjustTypeEnum;
use Yii;
use common\traits\Curd;
use common\helpers\Url;
use common\models\base\SearchModel;
use addons\Warehouse\common\models\WarehouseBill;
use addons\Warehouse\common\models\WarehouseBillGoods;
use addons\Warehouse\common\forms\WarehouseBillBForm;
use addons\Warehouse\common\forms\WarehouseBillCForm;
use addons\Warehouse\common\enums\BillTypeEnum;
use addons\Warehouse\common\forms\WarehouseBillCGoodsForm;
use addons\Warehouse\common\enums\GoodsStatusEnum;
use addons\Warehouse\common\models\WarehouseGoods;
use yii\base\Exception;
use common\helpers\ResultHelper;
use common\helpers\ArrayHelper;
/**
 * WarehouseBillBGoodsController implements the CRUD actions for WarehouseBillBGoodsController model.
 */
class BillCGoodsController extends BaseController
{
    use Curd;
    
    
    public $modelClass = WarehouseBillCGoodsForm::class;
    public $billType = BillTypeEnum::BILL_TYPE_C;

    /**
     * Lists all WarehouseBillBGoods models.
     * @return mixed
     */
    public function actionIndex()
    {
        $tab = Yii::$app->request->get('tab',2);
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['bill-c/index']));
        $bill_id = Yii::$app->request->get('bill_id');
        $searchModel = new SearchModel([
            'model' => $this->modelClass,
            'scenario' => 'default',
            'partialMatchAttributes' => ['goods_name', 'goods_remark'], // 模糊查询
            'defaultOrder' => [
                'id' => SORT_DESC
            ],
            'pageSize' => $this->pageSize,
            'relations' => [

            ]
        ]);

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['=', 'bill_id', $bill_id]);
        $dataProvider->query->andWhere(['>',WarehousebillGoods::tableName().'.status',-1]);

        $bill = WarehouseBill::find()->where(['id'=>$bill_id])->one();
        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'bill' => $bill,
            'tab' => $tab,
            'tabList'=>\Yii::$app->warehouseService->bill->menuTabList($bill_id, $this->billType, $returnUrl),
        ]);
    }

    /**
     * ajax添加商品
     *
     * @return mixed|string|\yii\web\Response
     * @throws \yii\base\ExitException
     */
    public function actionAjaxEdit()
    {
        $id = \Yii::$app->request->get('id');
        $model = $this->findModel($id);
        $model = $model ?? new WarehouseBillCGoodsForm();
        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(\Yii::$app->request->post())) {
            try{
                $trans = \Yii::$app->db->beginTransaction();
                if(false === $model->save()){
                    throw new \Exception($this->getError($model));
                }
                //汇总：总金额和总数量
                $res = \Yii::$app->warehouseService->billC->billCSummary($model->bill_id);
                if(false === $res){
                    throw new \Exception('更新单据汇总失败');
                }
                $trans->commit();
                \Yii::$app->getSession()->setFlash('success', '保存成功');
                return $this->redirect(\Yii::$app->request->referrer);
            }catch (\Exception $e){
                $trans->rollBack();
                return $this->message($e->getMessage(), $this->redirect(\Yii::$app->request->referrer), 'error');
            }
        }
        return $this->renderAjax($this->action->id, [
            'model' => $model,
        ]);
    }
    /**
     * 扫码添加
     */
    public function actionAjaxScan()
    {
        $bill_id  = Yii::$app->request->post('bill_id');
        $goods_id = Yii::$app->request->post('goods_id');
        if($goods_id == "") {
            \Yii::$app->getSession()->setFlash('error', '条码货号不能为空');
            return ResultHelper::json(422, "条码货号不能为空");
        }
        try{
            $trans = \Yii::$app->db->beginTransaction();
            \Yii::$app->warehouseService->billC->scanAddGoods($bill_id,[$goods_id]);            
            $trans->commit();
            
            \Yii::$app->getSession()->setFlash('success', '添加成功');
            return ResultHelper::json(200, "添加成功");
        }catch (\Exception $e){
            $trans->rollBack();
            
            \Yii::$app->getSession()->setFlash('error', $e->getMessage());
            return ResultHelper::json(422, $e->getMessage());
        }
    }

    /**
     *
     * ajax 更新商品明细
     * @param $id
     * @return array
     * @throws
     */
    public function actionAjaxUpdate($id)
    {
        if (!($model = $this->modelClass::findOne($id))) {
            return ResultHelper::json(404, '找不到数据');
        }
        $data = Yii::$app->request->get();
        $model->attributes = ArrayHelper::filter($data, array_keys($data));
        try {
            $trans = Yii::$app->trans->beginTransaction();
            if (!$model->save()) {
                return ResultHelper::json(422, $this->getError($model));
            }
            \Yii::$app->warehouseService->billC->billCSummary($model->bill_id);            
            $trans->commit();
            return ResultHelper::json(200, '修改成功');
        } catch (\Exception $e) {
            $trans->rollback();
            return ResultHelper::json(404, $e->getMessage());
        }

    }
    /**
     * 更改退货数量
     * @return array|mixed
     */
    public function actionAjaxChukuNum()
    {
        $id = Yii::$app->request->get("id");
        $goods_num = Yii::$app->request->get("goods_num");
        if($goods_num <= 0) {
            return ResultHelper::json(422, "出库数量必须大于0");
        }
        try{
            $trans = \Yii::$app->trans->beginTransaction();
            \Yii::$app->warehouseService->billC->updateChukuNum($id,$goods_num);            
            $trans->commit();
            return $this->message("操作成功", $this->redirect(\Yii::$app->request->referrer), 'success');
        }catch (\Exception $e){
            $trans->rollBack();
            return ResultHelper::json(422, $e->getMessage());
        }
    }
    /**
     * 编辑/创建
     * @property WarehouseBillBForm $model
     * @return mixed
     */
    public function actionAdd()
    {
        $this->layout = '@backend/views/layouts/iframe';

        $search = Yii::$app->request->get('search');
        $bill_id = Yii::$app->request->get('bill_id');
        $goods_ids = Yii::$app->request->get('goods_ids');
        $message = Yii::$app->request->get('message');
        $gModel = new WarehouseBillCGoodsForm();
        $billM = WarehouseBillCForm::findOne($bill_id);
        $billM = $billM ?? new WarehouseBillCForm();
        $billM->goods_ids = $goods_ids;
        if($search == 1){
            $valid_goods_ids = $billM->loadGoods();//查询校验
            $error = $billM->getGoodsMessage();//获取错误
            return ResultHelper::json(200, "", ['valid_goods_ids' => $valid_goods_ids, 'message'=>$error]);
        }
        $searchGoods = $billM->getSearchGoods();
        if($billM->load(\Yii::$app->request->post()) && !empty($searchGoods)){
            try {
                $trans = Yii::$app->db->beginTransaction();
                \Yii::$app->warehouseService->billC->batchAddGoods($billM, $searchGoods);
                $trans->commit();
                \Yii::$app->getSession()->setFlash('success','保存成功');
                return $this->redirect(\Yii::$app->request->referrer);
            }catch (\Exception $e){
                $trans->rollBack();
                return $this->message($e->getMessage(), $this->redirect(Yii::$app->request->referrer), 'error');
            }
        }
        return $this->render($this->action->id, [
            'model' => $billM,
            'gModel' => $gModel,
            'message' => $message,
            'searchGoods' => $searchGoods
        ]);
    }

    /**
     * 其它出库单-批量编辑
     * @return mixed
     */
    public function actionEditAll()
    {
        $bill_id = Yii::$app->request->get('bill_id');
        $searchModel = new SearchModel([
            'model' => $this->modelClass,
            'scenario' => 'default',
            'partialMatchAttributes' => ['goods_name', 'goods_remark'], // 模糊查询
            'defaultOrder' => [
                'id' => SORT_DESC
            ],
            'pageSize' => $this->pageSize,
            'relations' => []
        ]);

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['=', 'bill_id', $bill_id]);
        $dataProvider->query->andWhere(['>',WarehousebillGoods::tableName().'.status',-1]);
        $bill = WarehouseBill::find()->where(['id'=>$bill_id])->one();
        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'bill' => $bill,
            'tab' => Yii::$app->request->get('tab',2),
            'tabList'=>\Yii::$app->warehouseService->bill->menuTabList($bill_id, $this->billType, $this->returnUrl),            
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
        $billGoods = $this->findModel($id);
        $bill_id = $billGoods->bill_id;
        $bill = WarehouseBill::find()->where(['id'=>$bill_id])->one();
        try{
            $trans = Yii::$app->db->beginTransaction();
            //删除
            $billGoods->delete();
            //更新单据数量和金额
            $bill->goods_num = Yii::$app->warehouseService->bill->sumGoodsNum($bill_id);
            $bill->total_cost = Yii::$app->warehouseService->bill->sumCostPrice($bill_id);
            $bill->total_sale = Yii::$app->warehouseService->bill->sumSalePrice($bill_id);
            $bill->total_market = Yii::$app->warehouseService->bill->sumMarketPrice($bill_id);
            $bill->save();

            //更新库存表商品状态为库存
            WarehouseGoods::updateAll(['goods_status'=>GoodsStatusEnum::IN_STOCK],['goods_id'=>$billGoods->goods_id]);

            //还原库存
            \Yii::$app->warehouseService->warehouseGoods->updateStockNum($billGoods->goods_id, $billGoods->goods_num, AdjustTypeEnum::RESTORE);
            $trans->commit();
            return $this->message("删除成功", $this->redirect(Yii::$app->request->referrer));
        }catch (\Exception $e){
            $trans->rollBack();
            return $this->message($e->getMessage(), $this->redirect(Yii::$app->request->referrer), 'error');
        }
    }

}

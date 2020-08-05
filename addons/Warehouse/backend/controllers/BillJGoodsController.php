<?php

namespace addons\Warehouse\backend\controllers;

use addons\Warehouse\common\enums\DeliveryTypeEnum;
use addons\Warehouse\common\enums\LendStatusEnum;
use addons\Warehouse\common\enums\QcStatusEnum;
use addons\Warehouse\common\forms\WarehouseBillBForm;
use addons\Warehouse\common\forms\WarehouseBillCForm;
use addons\Warehouse\common\forms\WarehouseBillGoodsForm;
use addons\Warehouse\common\forms\WarehouseBillJForm;
use addons\Warehouse\common\forms\WarehouseBillJGoodsForm;
use addons\Warehouse\common\models\WarehouseBillGoodsJ;
use addons\Warehouse\common\models\WarehouseBillJ;
use common\helpers\ArrayHelper;
use common\helpers\ResultHelper;
use common\helpers\StringHelper;
use Yii;
use common\traits\Curd;
use common\helpers\Url;
use common\models\base\SearchModel;
use addons\Warehouse\common\models\WarehouseBill;
use addons\Warehouse\common\models\WarehouseBillGoods;
use common\enums\StatusEnum;
use yii\base\Exception;
use addons\Warehouse\common\enums\BillTypeEnum;
use addons\Warehouse\common\forms\WarehouseBillCGoodsForm;
use addons\Warehouse\common\enums\GoodsStatusEnum;
use addons\Warehouse\common\models\WarehouseGoods;

/**
 * WarehouseBillBGoodsController implements the CRUD actions for WarehouseBillBGoodsController model.
 */
class BillJGoodsController extends BaseController
{
    use Curd;
    public $modelClass = WarehouseBillJGoodsForm::class;
    public $billType = BillTypeEnum::BILL_TYPE_J;

    /**
     * 单据明细列表
     * @return mixed
     */
    public function actionIndex()
    {
        $tab = Yii::$app->request->get('tab',2);
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['bill-j/index']));
        $bill_id = Yii::$app->request->get('bill_id');
        $searchModel = new SearchModel([
            'model' => $this->modelClass,
            'scenario' => 'default',
            'partialMatchAttributes' => [], // 模糊查询
            'defaultOrder' => [
                'id' => SORT_DESC
            ],
            'pageSize' => $this->pageSize,
            'relations' => [
                'goodsJ' => [
                    'lend_status',
                    'receive_id',
                    'receive_time',
                    'receive_remark',
                    'restore_time',
                    'qc_status',
                    'qc_remark',
                ],
            ]
        ]);

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['=', WarehouseBillJGoodsForm::tableName().'.bill_id', $bill_id]);
        $dataProvider->query->andWhere(['>',WarehouseBillJGoodsForm::tableName().'.status',-1]);

        $bill = WarehouseBillJForm::findOne($bill_id);
        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'bill' => $bill,
            'tab' => $tab,
            'tabList'=>\Yii::$app->warehouseService->bill->menuTabList($bill_id, $this->billType, $returnUrl),
        ]);
    }

    /**
     * 添加明细
     * @property WarehouseBillBForm $model
     * @return mixed
     */
    public function actionAdd()
    {
        $this->layout = '@backend/views/layouts/iframe';

        $search = \Yii::$app->request->get('search');
        $bill_id = \Yii::$app->request->get('bill_id');
        $goods_ids = \Yii::$app->request->get('goods_ids');
        $model = new WarehouseBillJGoodsForm();
        $model->bill_id = $bill_id;
        $model->goods_ids = $goods_ids;
        try {
            $goods_list = $model->getGoodsList();
        }catch (\Exception $e){
            return $this->message($e->getMessage(), $this->redirect(Yii::$app->request->referrer), 'error');
        }
        if($search == 1 && !empty($model->getGoodsIds())){
            $bill_goods = \Yii::$app->request->post('bill_goods');
            if($model->load(\Yii::$app->request->post()) && !empty($bill_goods)){
                try {
                    $trans = Yii::$app->db->beginTransaction();

                    \Yii::$app->warehouseService->billJ->createBillGoodsJ($model, $bill_goods);

                    $trans->commit();
                    \Yii::$app->getSession()->setFlash('success','保存成功');
                    return $this->redirect(\Yii::$app->request->referrer);
                }catch (\Exception $e){
                    $trans->rollBack();
                    return $this->message($e->getMessage(), $this->redirect(Yii::$app->request->referrer), 'error');
                }
            }
        }

        return $this->render($this->action->id, [
            'model' => $model,
            'goods_list' => $goods_list,
        ]);
    }

    /**
     * 批量接收
     *
     * @return mixed
     * @throws \yii\base\ExitException
     */
    public function actionBatchReceive()
    {
        $ids = \Yii::$app->request->get('ids');
        $bill_id = \Yii::$app->request->get('bill_id');
        $check = \Yii::$app->request->get('check', null);
        $model = new WarehouseBillJGoodsForm();
        $model->ids = $ids;
        if($check){
            try{
                \Yii::$app->warehouseService->billJ->receiveValidate($model);
                return ResultHelper::json(200, '', ['url'=>Url::to([$this->action->id, 'bill_id'=>$bill_id, 'ids'=>$ids])]);
            }catch (\Exception $e){
                return ResultHelper::json(422, $e->getMessage());
            }
        }
        if ($model->load(\Yii::$app->request->post())) {
            try{
                $trans = \Yii::$app->trans->beginTransaction();
                \Yii::$app->warehouseService->billJ->receiveGoods($model);

                $trans->commit();
                \Yii::$app->getSession()->setFlash('success','保存成功');
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
     * 批量还货
     *
     * @return mixed
     * @throws \yii\base\ExitException
     */
    public function actionBatchReturn()
    {
        $ids = \Yii::$app->request->get('ids');
        $bill_id = \Yii::$app->request->get('bill_id');
        $check = \Yii::$app->request->get('check', null);
        $model = new WarehouseBillJGoodsForm();
        $model->ids = $ids;
        if($check){
            try{
                \Yii::$app->warehouseService->billJ->returnValidate($model);
                return ResultHelper::json(200, '', ['url'=>Url::to([$this->action->id, 'bill_id'=>$bill_id, 'ids'=>$ids])]);
            }catch (\Exception $e){
                return ResultHelper::json(422, $e->getMessage());
            }
        }
        if ($model->load(\Yii::$app->request->post())) {
            try{
                $trans = \Yii::$app->trans->beginTransaction();
                $model->bill_id = $bill_id;
                \Yii::$app->warehouseService->billJ->returnGoods($model);
                $trans->commit();
                \Yii::$app->getSession()->setFlash('success','保存成功');
                return ResultHelper::json(200, '保存成功');
            }catch (\Exception $e){
                $trans->rollBack();
                return ResultHelper::json(422, $e->getMessage());
            }
        }
        $model->qc_status = QcStatusEnum::PASS;
        return $this->render($this->action->id, [
            'model' => $model,
        ]);
    }

    /**
     * 其他出库单-批量编辑
     * @return mixed
     */
    public function actionEditAll()
    {
        $bill_id = Yii::$app->request->get('bill_id');
        $tab = Yii::$app->request->get('tab',2);
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['bill-j-goods/index','bill_id'=>$bill_id]));
        $searchModel = new SearchModel([
            'model' => $this->modelClass,
            'scenario' => 'default',
            'partialMatchAttributes' => [], // 模糊查询
            'defaultOrder' => [
                'id' => SORT_DESC
            ],
            'pageSize' => $this->pageSize,
            'relations' => []
        ]);

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['=', 'bill_id', $bill_id]);
        $dataProvider->query->andWhere(['>',WarehouseBillJGoodsForm::tableName().'.status',-1]);
        $bill = WarehouseBillJForm::find()->where(['id'=>$bill_id])->one();
        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'bill' => $bill,
            'tabList'=>\Yii::$app->warehouseService->bill->menuTabList($bill_id, $this->billType, $returnUrl, $tab),
            'tab' => $tab,
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
        $bill = WarehouseBillJForm::findOne($bill_id);
        try{
            $trans = Yii::$app->db->beginTransaction();
            //删除
            $billGoods->delete();
            //删除明细关系表
            $goodJ = WarehouseBillGoodsJ::findOne($billGoods->id);
            $goodJ->delete();
            //更新单据数量和金额
            $bill->goods_num = Yii::$app->warehouseService->bill->sumGoodsNum($bill_id);
            $bill->total_cost = Yii::$app->warehouseService->bill->sumCostPrice($bill_id);
            $bill->total_sale = Yii::$app->warehouseService->bill->sumSalePrice($bill_id);
            $bill->total_market = Yii::$app->warehouseService->bill->sumMarketPrice($bill_id);
            $bill->save();

            //更新库存表商品状态为库存
            WarehouseGoods::updateAll(['goods_status'=>GoodsStatusEnum::IN_STOCK],['goods_id'=>$billGoods->goods_id]);
            $trans->commit();
            return $this->message("删除成功", $this->redirect(['bill-j-goods/index','bill_id'=>$bill_id]));
        }catch (\Exception $e){
            $trans->rollBack();
            return $this->message($e->getMessage(), $this->redirect(['bill-j-goods/index','bill_id'=>$bill_id]), 'error');
        }
    }

}

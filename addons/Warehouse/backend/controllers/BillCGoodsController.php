<?php

namespace addons\Warehouse\backend\controllers;

use Yii;
use common\traits\Curd;
use common\helpers\Url;
use common\models\base\SearchModel;
use addons\Warehouse\common\models\WarehouseBill;
use addons\Warehouse\common\models\WarehouseBillGoods;
use addons\Warehouse\common\enums\DeliveryTypeEnum;
use addons\Warehouse\common\forms\WarehouseBillBForm;
use addons\Warehouse\common\forms\WarehouseBillCForm;
use addons\Warehouse\common\enums\BillTypeEnum;
use addons\Warehouse\common\forms\WarehouseBillCGoodsForm;
use addons\Warehouse\common\enums\GoodsStatusEnum;
use addons\Warehouse\common\models\WarehouseGoods;
use yii\base\Exception;

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
            'partialMatchAttributes' => [], // 模糊查询
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
                //更新收货单汇总：总金额和总数量
                $res = \Yii::$app->warehouseService->bill->WarehouseBillSummary($model->bill_id);
                if(false === $res){
                    throw new Exception('更新单据汇总失败');
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
        $bill = WarehouseBillCForm::find()->where(['id' => $bill_id])->one();
        $bill->goods_ids = $goods_ids;
        $warehouse_goods = [];
        if($search == 1 && !empty($goods_ids)){
            $goods_id_arr = $bill->getGoodsIds();
            foreach ($goods_id_arr as $goods_id) {
                $goods = WarehouseGoods::find()->where(['goods_id' => $goods_id, 'goods_status'=>GoodsStatusEnum::IN_STOCK])->one();
                if(!$goods){
                    return $this->message("货号{$goods_id}不存在或者不是库存中", $this->redirect(Yii::$app->request->referrer), 'error');
                }
                $data = [
                    DeliveryTypeEnum::PROXY_PRODUCE,
                    DeliveryTypeEnum::PART_GOODS,
                    DeliveryTypeEnum::ASSEMBLY,
                ];
                if(in_array($bill->delivery_type, $data)){
                    if($goods->supplier_id != $bill->supplier_id){
                        return $this->message("货号{$goods_id}的供应商与单据的供应商不一致", $this->redirect(Yii::$app->request->referrer), 'error');
                    }
                    /*if($goods->put_in_type != $bill->put_in_type){
                        return $this->message("货号{$goods_id}的入库方式与单据的入库方式不一致", $this->redirect(Yii::$app->request->referrer), 'error');
                    }*/
                }
                $goods_info = [];
                $goods_info['id'] = null;
                $goods_info['goods_id'] = $goods_id;
                $goods_info['bill_id'] = $bill_id;
                $goods_info['bill_no'] = $bill->bill_no;
                $goods_info['bill_type'] = $bill->bill_type;
                $goods_info['style_sn'] = $goods->style_sn;
                $goods_info['goods_name'] = $goods->goods_name;
                $goods_info['goods_num'] = $goods->goods_num;
                $goods_info['put_in_type'] = $goods->put_in_type;
                $goods_info['warehouse_id'] = $goods->warehouse_id;
                $goods_info['from_warehouse_id'] = $goods->warehouse_id;
                $goods_info['material'] = $goods->material;
                $goods_info['gold_weight'] = $goods->gold_weight;
                $goods_info['gold_loss'] = $goods->gold_loss;
                $goods_info['diamond_carat'] = $goods->diamond_carat;
                $goods_info['diamond_color'] = $goods->diamond_color;
                $goods_info['diamond_clarity'] = $goods->diamond_clarity;
                $goods_info['diamond_cert_id'] = $goods->diamond_cert_id;
                $goods_info['cost_price'] = $goods->cost_price;
                $goods_info['sale_price'] = $goods->market_price;
                $goods_info['market_price'] = $goods->market_price;
                $warehouse_goods[] = $goods_info;
            }
            $bill_goods = Yii::$app->request->post('bill_goods');
            if($bill->load(\Yii::$app->request->post()) && !empty($bill_goods)){
                try {
                    $trans = Yii::$app->db->beginTransaction();

                    \Yii::$app->warehouseService->billC->createBillGoodsC($bill, $bill_goods);

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
            'model' => $bill,
            'warehouse_goods' => $warehouse_goods
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
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['bill-c-goods/index','bill_id'=>$bill_id]));
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
        $dataProvider->query->andWhere(['>',WarehousebillGoods::tableName().'.status',-1]);
        $bill = WarehouseBill::find()->where(['id'=>$bill_id])->one();
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
            $trans->commit();
            return $this->message("删除成功", $this->redirect(['bill-c-goods/index','bill_id'=>$bill_id]));
        }catch (\Exception $e){
            $trans->rollBack();
            return $this->message($e->getMessage(), $this->redirect(['bill-c-goods/index','bill_id'=>$bill_id]), 'error');
        }
    }

}

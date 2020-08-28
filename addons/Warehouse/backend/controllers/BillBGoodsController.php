<?php

namespace addons\Warehouse\backend\controllers;


use addons\Warehouse\common\forms\WarehouseBillBForm;
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
use addons\Warehouse\common\forms\WarehouseBillBGoodsForm;
use addons\Warehouse\common\enums\GoodsStatusEnum;
use addons\Warehouse\common\models\WarehouseGoods;


/**
 * WarehouseBillBGoodsController implements the CRUD actions for WarehouseBillBGoodsController model.
 */
class BillBGoodsController extends BaseController
{
    use Curd;
    public $modelClass = WarehouseBillBGoodsForm::class;
    public $billType = BillTypeEnum::BILL_TYPE_B;

    /**
     * Lists all WarehouseBillBGoods models.
     * @return mixed
     */
    public function actionIndex()
    {
        $bill_id = Yii::$app->request->get('bill_id');
        $tab = Yii::$app->request->get('tab',2);
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['bill-b/index', 'bill_id'=>$bill_id]));
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
        $bill = WarehouseBillBForm::find()->where(['id' => $bill_id])->one();
        $bill->goods_ids = $goods_ids;
        $warehouse_goods = [];
        if($search == 1 && !empty($goods_ids)){
            $goods_id_arr = $bill->getGoodsIds();
            foreach ($goods_id_arr as $goods_id) {
                $goods = WarehouseGoods::find()->where(['goods_id' => $goods_id, 'goods_status'=>GoodsStatusEnum::IN_STOCK])->one();
                if(!$goods){
                    return $this->message("货号{$goods_id}不存在或者不是库存中", $this->redirect(Yii::$app->request->referrer), 'error');
                }
                if($goods->supplier_id != $bill->supplier_id){
                    return $this->message("货号{$goods_id}供应商与单据不一致", $this->redirect(Yii::$app->request->referrer), 'error');
                }
                if($goods->put_in_type != $bill->put_in_type){
                    return $this->message("货号{$goods_id}入库方式与单据不一致", $this->redirect(Yii::$app->request->referrer), 'error');
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
                $goods_info['warehouse_id'] = $bill->to_warehouse_id;
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

                    \Yii::$app->warehouseService->billB->createBillGoodsB($bill, $bill_goods);

                    $trans->commit();
                    $this->message('保存成功', $this->redirect(Yii::$app->request->referrer), 'success');
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
     * 退货返厂单-编辑
     * @return mixed
     */
    public function actionEditAll()
    {

        $bill_id = Yii::$app->request->get('bill_id');
        $tab = Yii::$app->request->get('tab',2);
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['bill-b-goods/index']));
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
            $res = WarehouseGoods::updateAll(['goods_status'=>GoodsStatusEnum::IN_STOCK],['goods_id'=>$billGoods->goods_id,'goods_status'=>GoodsStatusEnum::IN_RETURN_FACTORY]);
            if($res == 0){
                throw new Exception("商品不是返厂中或者不存在，请查看原因");
            }
            $trans->commit();
            return $this->message("删除成功", $this->redirect(['bill-b-goods/index','bill_id'=>$bill_id]));
        }catch (\Exception $e){
            $trans->rollBack();
            return $this->message($e->getMessage(), $this->redirect(['bill-b-goods/index','bill_id'=>$bill_id]), 'error');
        }
    }

}

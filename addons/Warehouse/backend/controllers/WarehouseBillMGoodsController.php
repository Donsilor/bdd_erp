<?php

namespace addons\Warehouse\backend\controllers;


use addons\Warehouse\common\enums\BillTypeEnum;
use addons\Warehouse\common\enums\GoodsStatusEnum;
use addons\Warehouse\common\forms\WarehouseBillMGoodsForm;
use addons\Warehouse\common\models\WarehouseBill;
use addons\Warehouse\common\models\WarehouseBillGoods;
use addons\Warehouse\common\models\WarehouseGoods;
use Yii;
use common\models\base\SearchModel;
use common\traits\Curd;
use addons\Purchase\common\models\PurchaseReceipt;
use common\helpers\Url;
use addons\Purchase\common\forms\PurchaseReceiptGoodsForm;
use addons\Purchase\common\models\PurchaseReceiptGoods;
use addons\Supply\common\models\Produce;
use addons\Supply\common\models\ProduceAttribute;
use addons\Supply\common\models\ProduceShipment;
use addons\Purchase\common\enums\ReceiptGoodsAttrEnum;
use common\enums\AuditStatusEnum;
use common\enums\StatusEnum;
use yii\base\Exception;

/**
 * WarehouseBillMGoods
 *
 * Class WarehouseBillMGoodsController
 * @property WarehouseBillBGoodsForm $modelClass
 * @package backend\modules\goods\controllers
 */
class WarehouseBillMGoodsController extends BaseController
{
    use Curd;

    /**
     * @var $modelClass WarehouseBillBGoodsForm
     */
    public $modelClass = WarehouseBillMGoodsForm::class;


    /**
     * 首页
     *
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionIndex()
    {
        $bill_id = Yii::$app->request->get('bill_id');
        $tab = Yii::$app->request->get('tab',2);
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['warehouse-bill-m/index']));
        $this->pageSize = 1000;
        $searchModel = new SearchModel([
            'model' => $this->modelClass,
            'scenario' => 'default',
            'partialMatchAttributes' => [], // 模糊查询
            'defaultOrder' => [
                'id' => SORT_DESC
            ],
            'pageSize' => $this->pageSize,
            'relations' => [
                'fromWarehouse' => ['name']
            ]
        ]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['=','bill_id',$bill_id]);
        $bill_goods = $dataProvider->getModels();
        $billInfo = WarehouseBill::find()->where(['id'=>$bill_id])->one();
        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'billInfo' => $billInfo,
            'billGoods' => $bill_goods,
            'tabList' => \Yii::$app->warehouseService->bill->menuTabList($bill_id,BillTypeEnum::BILL_TYPE_M,$returnUrl),
            'returnUrl' => $returnUrl,
            'tab'=>$tab,
        ]);
    }

    /**
     * 编辑/创建
     * @property PurchaseReceiptGoodsForm $model
     * @return mixed
     */
    public function actionEdit()
    {
        $this->layout = '@backend/views/layouts/iframe';

        $bill_id = Yii::$app->request->get('bill_id');
        $goods_ids = Yii::$app->request->get('goods_ids');
        $search = Yii::$app->request->get('search');
        $model = new WarehouseBillGoods();
        $model->goods_id = $goods_ids;
        $model->bill_id = $bill_id;
        $skiUrl = Url::buildUrl(\Yii::$app->request->url,[],['search']);
        $warehouse_goods = [];
        if($search == 1 && !empty($goods_ids)){
            $goods_ids = str_replace(' ',',',$goods_ids);
            $goods_ids = str_replace('，',',',$goods_ids);
            $goods_ids = str_replace(array("\r\n", "\r", "\n"),',',$goods_ids);
            $goods_id_arr = explode(",", $goods_ids);
            $billInfo = WarehouseBill::find()->where(['id'=>$bill_id])->one();
            try {
                foreach ($goods_id_arr as $goods_id) {
                    $goods_info = WarehouseGoods::find()->where(['goods_id' => $goods_id, 'goods_status'=>GoodsStatusEnum::IN_STOCK])->one();
                    if(empty($goods_info)){
                        throw new Exception("货号{$goods_id}不存在或者不是库存中");
                    }
                    $goods = [];
                    $goods['id'] = null;
                    $goods['goods_id'] = $goods_id;
                    $goods['bill_id'] = $bill_id;
                    $goods['bill_no'] = $billInfo->bill_no;
                    $goods['bill_type'] = $billInfo->bill_type;
                    $goods['style_sn'] = $goods_info['style_sn'];
                    $goods['goods_name'] = $goods_info['goods_name'];
                    $goods['goods_num'] = $goods_info['goods_num'];
                    $goods['put_in_type'] = $goods_info['put_in_type'];
                    $goods['warehouse_id'] = $billInfo->to_warehouse_id;
                    $goods['material'] = $goods_info['material'];
                    $goods['gold_weight'] = $goods_info['gold_weight'];
                    $goods['gold_loss'] = $goods_info['gold_loss'];
                    $goods['diamond_carat'] = $goods_info['diamond_carat'];
                    $goods['diamond_color'] = $goods_info['diamond_color'];
                    $goods['diamond_clarity'] = $goods_info['diamond_clarity'];
                    $goods['diamond_cert_id'] = $goods_info['diamond_cert_id'];
                    $goods['cost_price'] = $goods_info['cost_price'];
                    $warehouse_goods[] = $goods;
                }

            }catch (\Exception $e){
                return $this->message($e->getMessage(), $this->redirect($skiUrl), 'error');
            }

            $warehouse_goods_list = Yii::$app->request->post('warehouse_goods_list');
            if(!empty($warehouse_goods_list)){
                try {
                    $trans = Yii::$app->db->beginTransaction();

                    $warehouse_goods_val = [];
                    $goods_id_arr = [];

                    foreach ($warehouse_goods_list as &$goods) {
                        $goods_id = $goods['goods_id'];
                        $goods_info = WarehouseGoods::find()->where(['goods_id' => $goods_id, 'goods_status'=>GoodsStatusEnum::IN_STOCK])->one();
                        //保存时再次判断是否在库存中
                        if(empty($goods_info)){
                            throw new Exception("货号{$goods_id}不存在或者不是库存中");
                        }
                        $goods['bill_id'] = $bill_id;
                        $goods['bill_no'] = $billInfo['bill_no'];
                        $goods['bill_type'] = $billInfo['bill_type'];
                        $goods['warehouse_id'] = $billInfo['to_warehouse_id'];
                        $goods['put_in_type'] = $goods_info['put_in_type'];
                        $warehouse_goods_val[] = array_values($goods);
                        $goods_id_arr[] = $goods['goods_id'];
                        $billInfo->goods_num += $goods['goods_num'];
                        $billInfo->total_cost += $goods['cost_price'];
                    }
                    $warehouse_goods_key = array_keys($warehouse_goods_list[0]);

                    //批量添加单据明细
                    \Yii::$app->db->createCommand()->batchInsert(WarehouseBillGoods::tableName(), $warehouse_goods_key, $warehouse_goods_val)->execute();

                    //更新商品库存状态
                    $execute_num = WarehouseGoods::updateAll(['goods_status'=> GoodsStatusEnum::IN_TRANSFER],['goods_id'=>$goods_id_arr, 'goods_status' => GoodsStatusEnum::IN_STOCK]);
                    if($execute_num <> count($warehouse_goods_list)){
                        throw new Exception("货品改变状态数量与明细数量不一致");
                    }
                    //更新单据数量、价格
                    $billInfo->save();

                    $trans->commit();
                    Yii::$app->getSession()->setFlash('success', '保存成功');
                    return $this->redirect(Yii::$app->request->referrer);
                }catch (\Exception $e){
                    $trans->rollBack();
                    return $this->message($e->getMessage(), $this->redirect(['warehouse-bill-m-goods/index','bill_id'=>$bill_id]), 'error');
                }

            }
        }
        return $this->render($this->action->id, [
            'model' => $model,
            'warehouse_goods' => $warehouse_goods
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
            $bill->save();

            //更新库存表商品状态为库存
            $res = WarehouseGoods::updateAll(['goods_status'=>GoodsStatusEnum::IN_STOCK],['goods_id'=>$billGoods->goods_id,'goods_status'=>GoodsStatusEnum::IN_TRANSFER]);
            if($res == 0){
                throw new Exception("商品不是调拨中或者不存在，请查看原因");
            }
            $trans->commit();
            return $this->message("删除成功", $this->redirect(['warehouse-bill-m-goods/index','bill_id'=>$bill_id]));
        }catch (\Exception $e){
            $trans->rollBack();
            return $this->message($e->getMessage(), $this->redirect(['warehouse-bill-m-goods/index','bill_id'=>$bill_id]), 'error');
        }
    }







}

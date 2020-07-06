<?php

namespace addons\Warehouse\backend\controllers;


use addons\Warehouse\common\enums\BillTypeEnum;
use addons\Warehouse\common\enums\GoodsStatusEnum;
use addons\Warehouse\common\forms\WarehouseBillMGoodsForm;
use addons\Warehouse\common\models\WarehouseBill;
use addons\Warehouse\common\models\WarehouseBillGoods;
use addons\Warehouse\common\models\WarehouseGoods;
use common\helpers\StringHelper;
use Yii;
use common\models\base\SearchModel;
use common\traits\Curd;
use common\helpers\Url;
use addons\Purchase\common\forms\PurchaseReceiptGoodsForm;

use yii\base\Exception;

/**
 * WarehouseBillMGoods
 *
 * Class WarehouseBillMGoodsController
 * @property WarehouseBillBGoodsForm $modelClass
 * @package backend\modules\goods\controllers
 */
class BillMGoodsController extends BaseController
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
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['bill-m/index']));
        $searchModel = new SearchModel([
            'model' => $this->modelClass,
            'scenario' => 'default',
            'partialMatchAttributes' => [], // 模糊查询
            'defaultOrder' => [
                'id' => SORT_DESC
            ],
            'pageSize' => $this->getPageSize(),
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
            $goods_id_arr = StringHelper::explodeIds($goods_ids);
            $billInfo = WarehouseBill::find()->where(['id'=>$bill_id])->one();
            $goods_select = ['goods_id','style_sn','goods_name','goods_num','warehouse_id','put_in_type','material','gold_weight','gold_weight','gold_loss'
                ,'diamond_carat','diamond_color','diamond_clarity','diamond_cert_id','cost_price'];
            foreach ($goods_id_arr as $goods_id) {
                $goods = WarehouseGoods::find()->where(['goods_id' => $goods_id, 'goods_status'=>GoodsStatusEnum::IN_STOCK])->select($goods_select)->one();
                if(empty($goods)){
                    return $this->message("货号{$goods_id}不存在或者不是库存中", $this->redirect($skiUrl), 'error');
                }
                if($goods->warehouse_id != $billInfo->from_warehouse_id){
                    return $this->message("货号{$goods_id}仓库与单据出库仓库不一致", $this->redirect($skiUrl), 'error');
                }
                $goods->put_in_type = \addons\Warehouse\common\enums\PutInTypeEnum::getValue($goods->put_in_type);
                $goods->warehouse_id = $goods->warehouse->name ?? '';
                $goods->material = Yii::$app->attr->valueName($goods->material);
                $goods->diamond_color = Yii::$app->attr->valueName($goods->diamond_color);
                $goods->diamond_clarity = Yii::$app->attr->valueName($goods->diamond_clarity);

                $warehouse_goods[] = $goods;
            }

            $warehouse_goods_list = Yii::$app->request->post('warehouse_goods_list');
            if(!empty($warehouse_goods_list)){
                try {
                    $trans = Yii::$app->db->beginTransaction();

                    $warehouse_goods_val = [];
                    $goods_id_arr = [];

                    foreach ($warehouse_goods_list as &$warehouse_goods) {
                        $goods_id = $warehouse_goods['goods_id'];
                        $goods = WarehouseGoods::find()->where(['goods_id' => $goods_id, 'goods_status'=>GoodsStatusEnum::IN_STOCK])->select($goods_select)->one();
                        //保存时再次判断是否在库存中
                        if(empty($goods)){
                            throw new Exception("货号{$goods_id}不存在或者不是库存中");
                        }
                        $warehouse_goods['bill_id'] = $bill_id;
                        $warehouse_goods['bill_no'] = $billInfo['bill_no'];
                        $warehouse_goods['bill_type'] = $billInfo['bill_type'];
                        $warehouse_goods['warehouse_id'] = $billInfo['to_warehouse_id'];
                        $warehouse_goods['to_warehouse_id'] = $billInfo['to_warehouse_id'];
                        $warehouse_goods['from_warehouse_id'] = $goods->warehouse_id;
                        $warehouse_goods['style_sn'] = $goods->style_sn;
                        $warehouse_goods['goods_name'] = $goods->goods_name;
                        $warehouse_goods['goods_num'] = $goods->goods_num;
                        $warehouse_goods['put_in_type'] = $goods->put_in_type;
                        $warehouse_goods['material'] = $goods->material;
                        $warehouse_goods['gold_weight'] = $goods->gold_weight;
                        $warehouse_goods['gold_loss'] = $goods->gold_loss;
                        $warehouse_goods['diamond_carat'] = $goods->diamond_carat;
                        $warehouse_goods['diamond_color'] = $goods->diamond_color;
                        $warehouse_goods['diamond_cert_id'] = $goods->diamond_cert_id;
                        $warehouse_goods['cost_price'] = $goods->cost_price;

                        $warehouse_goods_val[] = array_values($warehouse_goods);
                        $goods_id_arr[] = $warehouse_goods['goods_id'];
                        $billInfo->goods_num += $warehouse_goods['goods_num'];
                        $billInfo->total_cost += $warehouse_goods['cost_price'];
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
                    if(false === $billInfo->save()){
                        throw new \Exception($this->getError($billInfo));
                    }
                    $trans->commit();
                    Yii::$app->getSession()->setFlash('success', '保存成功');
                    return $this->redirect(Yii::$app->request->referrer);
                }catch (\Exception $e){
                    $trans->rollBack();
                    return $this->message($e->getMessage(), $this->redirect(['index','bill_id'=>$bill_id]), 'error');
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
            $bill->total_sale = Yii::$app->warehouseService->bill->sumSalePrice($bill_id);
            $bill->total_market = Yii::$app->warehouseService->bill->sumMarketPrice($bill_id);
            $bill->save();

            //更新库存表商品状态为库存
            $res = WarehouseGoods::updateAll(['goods_status'=>GoodsStatusEnum::IN_STOCK],['goods_id'=>$billGoods->goods_id,'goods_status'=>GoodsStatusEnum::IN_TRANSFER]);
            if($res == 0){
                throw new Exception("商品不是调拨中或者不存在，请查看原因");
            }
            $trans->commit();
            return $this->message("删除成功", $this->redirect(['bill-m-goods/index','bill_id'=>$bill_id]));
        }catch (\Exception $e){
            $trans->rollBack();
            return $this->message($e->getMessage(), $this->redirect(['bill-m-goods/index','bill_id'=>$bill_id]), 'error');
        }
    }







}

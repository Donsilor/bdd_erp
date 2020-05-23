<?php

namespace addons\Warehouse\backend\controllers;


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

            ]
        ]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['=','bill_id',$bill_id]);
        $dataProvider->query->andWhere(['>','status',-1]);
        $bill_goods = $dataProvider->getModels();
        $billInfo = WarehouseBill::find()->where(['id'=>$bill_id])->one();
        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'billInfo' => $billInfo,
            'billGoods' => $bill_goods,
            'tabList' => \Yii::$app->warehouseService->warehouseMBill->menuTabList($bill_id,$returnUrl),
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
        $billModel = new WarehouseBill();
        $skiUrl = Url::buildUrl(\Yii::$app->request->url,[],['search']);
        $warehouse_goods = [];
        if($search == 1 && !empty($goods_ids)){
            $goods_ids = str_replace(' ',',',$goods_ids);
            $goods_ids = str_replace('，',',',$goods_ids);
            $goods_ids = str_replace(array("\r\n", "\r", "\n"),',',$goods_ids);
            $goods_id_arr = explode(",", $goods_ids);
            $billInfo = $billModel::find()->where(['id'=>$bill_id])->asArray()->one();
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
                    $goods['bill_no'] = $billInfo['bill_no'];
                    $goods['bill_type'] = $billInfo['bill_type'];
                    $goods['style_sn'] = $goods_info['style_sn'];
                    $goods['goods_name'] = $goods_info['goods_name'];
                    $goods['goods_num'] = $goods_info['goods_num'];
                    $goods['put_in_type'] = $goods_info['put_in_type'];
                    $goods['warehouse_id'] = $billInfo['to_warehouse_id'];
                    $goods['material'] = $goods_info['material'];
                    $goods['gold_weight'] = $goods_info['gold_weight'];
                    $goods['gold_loss'] = $goods_info['gold_loss'];
                    $goods['diamond_carat'] = $goods_info['diamond_carat'];
                    $goods['diamond_color'] = $goods_info['diamond_color'];
                    $goods['diamond_clarity'] = $goods_info['diamond_clarity'];
                    $goods['diamond_cert_id'] = $goods_info['diamond_cert_id'];
                    $goods['cost_price'] = $goods_info['cost_price'];
                    $goods['sale_price'] = $goods_info['market_price'];
                    $goods['market_price'] = $goods_info['market_price'];
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
                    $warehouse_bill_update = [
                        'goods_num' => 0,
                        'total_cost' => 0,
                        'total_sale' => 0,
                        'total_market' => 0
                    ];
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
                        $warehouse_bill_update['goods_num'] += $goods['goods_num'];
                        $warehouse_bill_update['total_cost'] += $goods['cost_price'];
                        $warehouse_bill_update['total_sale'] += $goods['sale_price'];
                        $warehouse_bill_update['total_market'] += $goods['market_price'];
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
                    WarehouseBill::updateAll($warehouse_bill_update,['id'=>$bill_id]);

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
     * ajax编辑/创建
     *
     * @return mixed|string|\yii\web\Response
     * @throws \yii\base\ExitException
     */
    public function actionAjaxEdit()
    {
        $receipt_goods_list = Yii::$app->request->post('receipt_goods_list');
        $rurchase_receipt_info = Yii::$app->request->post('PurchaseReceipt');
        $model = new PurchaseReceiptGoods();
        if(!empty($receipt_goods_list)){
            try {
                $trans = Yii::$app->db->beginTransaction();
                $receipt_id = $rurchase_receipt_info['id'];
                foreach ($receipt_goods_list as $key => $goods) {
                    $id = isset($goods['id']) ? $goods['id'] : '';
                    $model = $this->findModel($id);
                    // ajax 校验
                    $this->activeFormValidate($model);
                    if (false === $model::updateAll($goods, ['id' => $id])) {
                        throw new Exception($this->getError($model));
                    }
                }

                //软删除
                $old_list = $model::find()->where(['receipt_id' => $receipt_id])->asArray()->all();
                $old_ids = array_column($old_list, 'id');
                $new_ids = array_column($receipt_goods_list, 'id');
                $del_ids = array_diff($old_ids, $new_ids);
                if(!empty($del_ids)){
                    $res = $model::updateAll(['status' => StatusEnum::DELETE], ['id' => $del_ids]);
                    if(false === $res){
                        throw new Exception('软删除失败');
                    }
                }

                //更新采购收货单汇总：总金额和总数量
                $res = Yii::$app->purchaseService->purchaseReceipt->purchaseReceiptSummary($receipt_id);
                if(false === $res){
                    throw new Exception('更新收货单汇总失败');
                }
                $trans->commit();
                Yii::$app->getSession()->setFlash('success', '保存成功');
                return $this->redirect(Yii::$app->request->referrer);
            }catch (\Exception $e){
                $trans->rollBack();
                return $this->message($e->getMessage(), $this->redirect(['index']), 'error');
            }
        }
        return $this->renderAjax('index', [
            'model' => $model
        ]);
    }

}

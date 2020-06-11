<?php

namespace addons\Warehouse\backend\controllers;


use Yii;
use common\traits\Curd;
use common\helpers\Url;
use common\models\base\SearchModel;
use addons\Warehouse\common\models\WarehouseBill;
use addons\Warehouse\common\models\WarehouseBillGoods;
use common\enums\StatusEnum;
use yii\base\Exception;
use addons\Warehouse\common\enums\BillTypeEnum;
use addons\Warehouse\common\forms\WarehouseBillOForm;
use addons\Warehouse\common\enums\GoodsStatusEnum;
use addons\Warehouse\common\models\WarehouseGoods;


/**
 * WarehouseBillGoodsController implements the CRUD actions for WarehouseBillGoodsController model.
 */
class BillOGoodsController extends BaseController
{
    use Curd;
    public $modelClass = WarehouseBillGoods::class;
    public $billType = BillTypeEnum::BILL_TYPE_O;
    /**
     * Lists all WarehouseBillGoods models.
     * @return mixed
     */
    public function actionIndex()
    {

        $bill_id = Yii::$app->request->get('bill_id');
        $tab = Yii::$app->request->get('tab',2);
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['warehouser-bill-o/index']));
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
        $billGoods = $dataProvider->getModels();
        $model = WarehouseBill::find()->where(['id'=>$bill_id])->one();
        return $this->render($this->action->id, [
            'model' => $model,
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'billGoods' => $billGoods,
            'tabList'=>\Yii::$app->warehouseService->bill->menuTabList($bill_id, $this->billType, $returnUrl),
            'tab' => $tab,
        ]);
    }

    /**
     * 编辑/创建
     * @property WarehouseBillOForm $model
     * @return mixed
     */
    public function actionEdit()
    {
        $this->layout = '@backend/views/layouts/iframe';

        $bill_id = Yii::$app->request->get('bill_id');
        $goods_ids = Yii::$app->request->get('goods_ids');
        $search = Yii::$app->request->get('search');
        $warehouse_goods_list = Yii::$app->request->post('warehouse_goods_list');
        $model = new WarehouseBillOForm();
        $model->goods_ids = $goods_ids;
        $model->id = $bill_id;
        $skiUrl = Url::buildUrl(\Yii::$app->request->url,[],['search']);
        $warehouse_goods = [];
        if($search == 1 && !empty($goods_ids)){
            $goods_id_arr = $model->getGoodsIds($goods_ids);
            $billInfo = WarehouseBill::find()->where(['id'=>$bill_id])->asArray()->one();
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
            if(!empty($warehouse_goods_list)){
                try {
                    $trans = Yii::$app->db->beginTransaction();
                    //批量添加单据明细
                    $warehouse_goods_val = [];
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
                    }
                    $warehouse_goods_key = array_keys($warehouse_goods_list[0]);
                    \Yii::$app->db->createCommand()->batchInsert(WarehouseBillGoods::tableName(), $warehouse_goods_key, $warehouse_goods_val)->execute();

                    //更新商品库存状态
                    $execute_num = WarehouseGoods::updateAll(['goods_status'=> GoodsStatusEnum::IN_RETURN_FACTORY],['goods_id'=>$goods_id_arr, 'goods_status' => GoodsStatusEnum::IN_STOCK]);
                    if($execute_num <> count($warehouse_goods_list)){
                        throw new Exception("货品改变状态数量与明细数量不一致");
                    }
                    //更新收货单汇总：总金额和总数量
                    $res = Yii::$app->warehouseService->bill->WarehouseBillSummary($bill_id);
                    if(false === $res){
                        throw new Exception('更新单据汇总失败');
                    }

                    $trans->commit();
                    Yii::$app->getSession()->setFlash('success', '保存成功');
                    return $this->redirect(Yii::$app->request->referrer);
                }catch (\Exception $e){
                    $trans->rollBack();
                    return $this->message($e->getMessage(), $this->redirect(['bill-b-goods/index','bill_id'=>$bill_id]), 'error');
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
        $goods_list = Yii::$app->request->post('bill_goods_list');
        $bill_info = Yii::$app->request->post('WarehouseBill');
        $model = new WarehouseBillGoods();
        if(!empty($goods_list)){
            try {
                $trans = Yii::$app->db->beginTransaction();
                $bill_id = $bill_info['id'];
                foreach ($goods_list as $key => $goods) {
                    $id = isset($goods['id']) ? $goods['id'] : '';
                    $model = $this->findModel($id);
                    // ajax 校验
                    $this->activeFormValidate($model);
                    if (false === $model::updateAll($goods, ['id' => $id])) {
                        throw new Exception($this->getError($model));
                    }
                }

                $old_list = $model::find()->where(['bill_id' => $bill_id])->asArray()->all();
                $old_ids = array_column($old_list, 'id');
                $new_ids = array_column($goods_list, 'id');
                $del_ids = array_diff($old_ids, $new_ids);
                if(!empty($del_ids)){
                    $res = $model::updateAll(['status' => StatusEnum::DELETE], ['id' => $del_ids]);
                    if(false === $res){
                        throw new Exception('删除明细商品失败');
                    }
                }

                //更新单据汇总：总金额和总数量
                $res = Yii::$app->warehouseService->bill->WarehouseBillSummary($bill_id);
                if(false === $res){
                    throw new Exception('更新单据汇总失败');
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

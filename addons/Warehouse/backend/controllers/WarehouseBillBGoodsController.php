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
class WarehouseBillBGoodsController extends BaseController
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

        $tab = Yii::$app->request->get('tab',2);
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['warehouser-bill-b/index']));
        $bill_id = Yii::$app->request->get('bill_id');
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
        $billModel = new WarehouseBillBForm();
        $bill = $billModel::find()->where(['id' => $bill_id])->one();
        $warehouse_goods = [];
        if($search == 1 && !empty($goods_ids)){
            //$goods_id_arr = $billModel->getGoodsIds($goods_ids);
            $goods_id_arr = StringHelper::explodeIds($goods_ids);
            foreach ($goods_id_arr as $goods_id) {
                $goods_info = WarehouseGoods::find()->where(['goods_id' => $goods_id, 'goods_status'=>GoodsStatusEnum::IN_STOCK])->one();
                if(empty($goods_info)){
                    return $this->message("货号{$goods_id}不存在或者不是库存中", $this->redirect(Yii::$app->request->referrer), 'error');
                }
                $goods = [];
                $goods['id'] = null;
                $goods['goods_id'] = $goods_id;
                $goods['bill_id'] = $bill_id;
                $goods['bill_no'] = $bill->bill_no;
                $goods['bill_type'] = $bill->bill_type;
                $goods['style_sn'] = $goods_info['style_sn'];
                $goods['goods_name'] = $goods_info['goods_name'];
                $goods['goods_num'] = $goods_info['goods_num'];
                $goods['put_in_type'] = $goods_info['put_in_type'];
                $goods['warehouse_id'] = $bill->to_warehouse_id;
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
            $bill_goods = Yii::$app->request->post('bill_goods');
            if($billModel->load(\Yii::$app->request->post()) && !empty($bill_goods)){
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
            'model' => $billModel,
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


    /**
     * 退货返厂单-编辑
     * @return mixed
     */
    public function actionEditAll()
    {

        $bill_id = Yii::$app->request->get('bill_id');
        $tab = Yii::$app->request->get('tab',2);
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['warehouser-bill-b-goods/index']));
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
}

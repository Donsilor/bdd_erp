<?php

namespace addons\Warehouse\backend\controllers;

use addons\Warehouse\common\enums\StoneBillTypeEnum;
use addons\Warehouse\common\forms\WarehouseStoneBillCkGoodsForm;
use addons\Warehouse\common\models\WarehouseStoneBill;
use addons\Warehouse\common\models\WarehouseStoneBillGoods;
use common\helpers\StringHelper;
use Yii;
use common\traits\Curd;
use common\helpers\Url;
use common\models\base\SearchModel;
use addons\Warehouse\common\enums\DeliveryTypeEnum;
use addons\Warehouse\common\forms\WarehouseBillBForm;
use addons\Warehouse\common\forms\WarehouseBillCForm;
use addons\Warehouse\common\enums\GoodsStatusEnum;
use addons\Warehouse\common\models\WarehouseGoods;
use yii\base\Exception;
use common\helpers\ResultHelper;
use common\helpers\ArrayHelper;
/**
 * WarehouseBillBGoodsController implements the CRUD actions for WarehouseBillBGoodsController model.
 */
class StoneBillCkGoodsController extends BaseController
{
    use Curd;
    
    
    public $modelClass = WarehouseStoneBillCkGoodsForm::class;
    public $billType = StoneBillTypeEnum::STONE_CK;

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
        $dataProvider->query->andWhere(['>',WarehouseStoneBillCkGoodsForm::tableName().'.status',-1]);

        $bill = WarehouseStoneBill::find()->where(['id'=>$bill_id])->one();
        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'bill' => $bill,
            'tab' => $tab,
            'tabList'=>\Yii::$app->warehouseService->stoneBill->menuTabList($bill_id, $this->billType, $returnUrl),
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
        $model = $model ?? new WarehouseStoneBillCkGoodsForm();
        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(\Yii::$app->request->post())) {
            try{
                $trans = \Yii::$app->db->beginTransaction();
                if(false === $model->save()){
                    throw new \Exception($this->getError($model));
                }
                //汇总：总金额和总数量
                $res = \Yii::$app->warehouseService->stoneBill->stoneBillSummary($model->bill_id);
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
        $stone_sn = Yii::$app->request->post('stone_sn');
        if($stone_sn == "") {
            \Yii::$app->getSession()->setFlash('error', '批次号不能为空');
            return ResultHelper::json(422, "批次号不能为空");
        }
        $stone_sns = StringHelper::explodeIds($stone_sn);
        try{
            $trans = \Yii::$app->db->beginTransaction();
            \Yii::$app->warehouseService->stoneCk->scanGoods($bill_id,$stone_sns);
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
     * ajax 更新商品明细
     *
     * @param $id
     * @return array
     */
    public function actionAjaxUpdate($id)
    {
        if (!($model = $this->modelClass::findOne($id))) {
            return ResultHelper::json(404, '找不到数据');
        }
        $old_stone_weight = $model->stone_weight; //原有
        $old_stone_num = $model->stone_num; //原有
        $data = Yii::$app->request->get();
        $model->attributes = ArrayHelper::filter($data, array_keys($data));
        $key = array_keys($data);
        if(isset($key['id'])){
            unset($key['id']);
        }
        $key[] = 'cost_price';
        try{
            $trans = Yii::$app->trans->beginTransaction();
            if (!$model->save(true,$key)) {
                return ResultHelper::json(422, $this->getError($model));
            }
            $new_stone_weight = $model->stone_weight;
            $new_stone_num = $model->stone_num;
            $adjust_weight = $old_stone_weight - $new_stone_weight;
            $adjust_num = $old_stone_num - $new_stone_num;
            //更新库存金重
            $res = Yii::$app->warehouseService->stoneCk->updatestoneWeight($model->stone_sn, $adjust_weight,$adjust_num);
            if($res['status'] == false){
                return ResultHelper::json(404, $res['msg']);
            }
            //更新单据库存
            \Yii::$app->warehouseService->stoneBill->stoneBillSummary($model->bill_id);
            $trans->commit();
            return ResultHelper::json(200, '修改成功');
        }catch (\Exception $e) {
            $trans->rollback();
            return ResultHelper::json(404, $e);
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
                $goods_info['stone_weight'] = $goods->stone_weight;
                $goods_info['stone_loss'] = $goods->stone_loss;
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
     * 其它出库单-批量编辑
     * @return mixed
     */
    public function actionEditAll()
    {
        $bill_id = Yii::$app->request->get('bill_id');
        $tab = Yii::$app->request->get('tab',2);
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['stone-bill-o-goods/index','bill_id'=>$bill_id]));
        $searchModel = new SearchModel([
            'model' => $this->modelClass,
            'scenario' => 'default',
            'partialMatchAttributes' => ['stone_name'], // 模糊查询
            'defaultOrder' => [
                'id' => SORT_DESC
            ],
            'pageSize' => $this->pageSize,
            'relations' => []
        ]);

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['=', 'bill_id', $bill_id]);
        $dataProvider->query->andWhere(['>',WarehouseStoneBillGoods::tableName().'.status',-1]);
        $bill = WarehouseStoneBill::find()->where(['id'=>$bill_id])->one();
        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'bill' => $bill,
            'tabList'=>\Yii::$app->warehouseService->stoneBill->menuTabList($bill_id, $this->billType, $returnUrl, $tab),
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
        try{
            $trans = Yii::$app->db->beginTransaction();

            //更新库存金重
            $adjust_weight = $billGoods->stone_weight;
            $adjust_num = $billGoods->stone_num;
            $res = Yii::$app->warehouseService->stoneCk->updatestoneWeight($billGoods->stone_sn, $adjust_weight, $adjust_num);
            if($res['status'] == false){
                return ResultHelper::json(404, $res['msg']);
            }
            //删除
            if(false === $billGoods->delete()){
                throw new \Exception($this->getError($billGoods));
            }
            //更新单据数量和金额
            \Yii::$app->warehouseService->stoneBill->stoneBillSummary($bill_id);
            $trans->commit();
            return $this->message("删除成功", $this->redirect(['stone-bill-ck-goods/index','bill_id'=>$bill_id]));
        }catch (\Exception $e){
            $trans->rollBack();
            return $this->message($e->getMessage(), $this->redirect(['stone-bill-ck-goods/index','bill_id'=>$bill_id]), 'error');
        }
    }




}

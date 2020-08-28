<?php

namespace addons\Warehouse\backend\controllers;

use addons\Warehouse\common\forms\WarehouseBillTGoodsForm;
use common\helpers\ResultHelper;
use Yii;
use common\traits\Curd;
use common\helpers\Url;
use common\models\base\SearchModel;
use addons\Warehouse\common\models\WarehouseGoods;
use addons\Warehouse\common\models\WarehouseBill;
use addons\Warehouse\common\models\WarehouseBillGoods;
use addons\Warehouse\common\models\WarehouseBillGoodsL;
use addons\Purchase\common\models\PurchaseReceiptGoods;
use addons\Warehouse\common\forms\WarehouseBillLGoodsForm;
use addons\Purchase\common\enums\ReceiptGoodsStatusEnum;
use addons\Warehouse\common\enums\OrderTypeEnum;
use addons\Warehouse\common\enums\GoodsStatusEnum;
use addons\Warehouse\common\enums\BillTypeEnum;
use yii\base\Exception;

/**
 * 收货入库单明细
 */
class BillLGoodsController extends BaseController
{
    use Curd;
    public $modelClass = WarehouseBillLGoodsForm::class;
    public $billType = BillTypeEnum::BILL_TYPE_L;
    /**
     * 收货单明细列表
     * @return mixed
     */
    public function actionIndex()
    {

        $bill_id = Yii::$app->request->get('bill_id');
        $tab = Yii::$app->request->get('tab',2);
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['bill-l-goods/index','bill_id'=>$bill_id]));
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
        $dataProvider->query->andWhere(['>',WarehouseBillGoodsL::tableName().'.status',-1]);
        $bill = WarehouseBill::find()->where(['id'=>$bill_id])->one();
        return $this->render($this->action->id, [
            'model' => new WarehouseBillLGoodsForm(),
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'bill' => $bill,
            'tabList'=>\Yii::$app->warehouseService->bill->menuTabList($bill_id, $this->billType, $returnUrl),
            'tab' => $tab,
        ]);
    }

    /**
     * 收货单-编辑
     * @return mixed
     */
    public function actionEditAll()
    {
        $bill_id = Yii::$app->request->get('bill_id');
        $tab = Yii::$app->request->get('tab',3);
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['bill-l-goods/index','bill_id'=>$bill_id]));
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
        $dataProvider->query->andWhere(['>',WarehouseBillGoodsL::tableName().'.status',-1]);
        $bill = WarehouseBill::find()->where(['id'=>$bill_id])->one();
        return $this->render($this->action->id, [
            'model' => new WarehouseBillLGoodsForm(),
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'bill' => $bill,
            'tabList'=>\Yii::$app->warehouseService->bill->menuTabList($bill_id, $this->billType, $returnUrl, $tab),
            'tab' => $tab,
        ]);
    }

    /**
     * ajax编辑
     *
     * @return mixed|string|\yii\web\Response
     * @throws \yii\base\ExitException
     */
    public function actionEdit()
    {
        $this->layout = '@backend/views/layouts/iframe';

        $id = \Yii::$app->request->get('id');
        //$bill_id = Yii::$app->request->get('bill_id');
        $model = $this->findModel($id);
        $model = $model ?? new WarehouseBillGoodsL();
        // ajax 校验
        //$this->activeFormValidate($model);
        if ($model->load(\Yii::$app->request->post())) {
            try{
                //$trans = \Yii::$app->db->beginTransaction();
                //Yii::$app->warehouseService->billT->addBillTGoods($model);
                //$trans->commit();
                //更新收货单汇总：总金额和总数量
                $res = \Yii::$app->warehouseService->billL->warehouseBillLSummary($model->bill_id);
                if(false === $res){
                    throw new \yii\db\Exception('更新单据汇总失败');
                }
                if(false === $model->save()) {
                    throw new \Exception($this->getError($model));
                }
                Yii::$app->getSession()->setFlash('success','保存成功');
                return ResultHelper::json(200, '保存成功');
            }catch (\Exception $e){
                //$trans->rollBack();
                return ResultHelper::json(422, $e->getMessage());
            }
        }
        return $this->render($this->action->id, [
            'model' => $model,
        ]);
    }

    /**
     * ajax批量编辑
     *
     * @return mixed|string|\yii\web\Response
     * @throws \yii\base\ExitException
     */
    public function actionBatchEdit()
    {
        $this->layout = '@backend/views/layouts/iframe';

        $ids = Yii::$app->request->post('ids');
        $ids = $ids ?? Yii::$app->request->get('ids');
        $model = new WarehouseBillTGoodsForm();
        $model->ids = $ids;
        $id_arr = $model->getIds();
        if(!$id_arr){
            return ResultHelper::json(422, "ID不能为空");
        }
        $name = Yii::$app->request->post('name');
        $name = $name ?? Yii::$app->request->get('name');
        if(!$name){
            return ResultHelper::json(422, "字段错误");
        }
        if(Yii::$app->request->isPost){
            $value = Yii::$app->request->post('value');
            if(!$value){
                return ResultHelper::json(422, "输入值不能为空");
            }
            try{
                $trans = Yii::$app->trans->beginTransaction();
                foreach ($id_arr as $id) {
                    $goods = WarehouseBillGoodsL::findOne(['id'=>$id]);
                    $goods->$name = $value;
                    if(false === $goods->validate()) {
                        throw new \Exception($this->getError($goods));
                    }
                    if(false === $goods->save()) {
                        throw new \Exception($this->getError($goods));
                    }
                }
                $trans->commit();
                Yii::$app->getSession()->setFlash('success','保存成功');
                return ResultHelper::json(200, '保存成功');
            }catch (\Exception $e){
                $trans->rollBack();
                return ResultHelper::json(422, $e->getMessage());
            }
        }
        $attr_id = Yii::$app->request->get('attr_id',0);
        if(!$attr_id){
            return ResultHelper::json(422, '参数错误');
        }
        $check = Yii::$app->request->get('check',null);
        if($check){
            return ResultHelper::json(200, '', ['url'=>Url::to([$this->action->id, 'ids'=>$ids, 'name'=>$name, 'attr_id'=>$attr_id])]);
        }
        $style_arr = $model::find()->where(['id'=>$id_arr])->select(['style_sn'])->asArray()->distinct('style_sn')->all();
        if(count($style_arr) != 1){
            return ResultHelper::json(422, '请选择同款的商品进行操作');
        }
        $style_sn = $style_arr[0]['style_sn']??"";
        $attr_arr = Yii::$app->styleService->styleAttribute->getAttrValueListByStyle($style_sn,$attr_id);
        return $this->render($this->action->id, [
            'model' => $model,
            'ids' => $ids,
            'name'=> $name,
            'attr_arr' =>$attr_arr
        ]);

    }

    /**
     * 删除/关闭
     *
     * @param $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        if (!($model = $this->modelClass::findOne($id))) {
            return $this->message("找不到数据", $this->redirect(['index']), 'error');
        }
        try{
            $trans = \Yii::$app->db->beginTransaction();
            if(false === $model->delete()){
                throw new \Exception($this->getError($model));
            }
            $bill = WarehouseBill::find()->where(['id' => $model->bill_id])->one();
            if($bill->order_type = OrderTypeEnum::ORDER_L && $model->source_detail_id){
                $receipt_goods = PurchaseReceiptGoods::find()->where(['id'=>$model->source_detail_id])->one();
                if($receipt_goods){
                    $receipt_goods->goods_status = ReceiptGoodsStatusEnum::IQC_PASS;
                    if(false === $receipt_goods->save()){
                        throw new \Exception($this->getError($receipt_goods));
                    }
                }
            }
            //更新收货单汇总：总金额和总数量
            $res = \Yii::$app->warehouseService->billL->warehouseBillLSummary($model->bill_id);
            if(false === $res){
                throw new \yii\db\Exception('更新单据汇总失败');
            }
            $trans->commit();
            \Yii::$app->getSession()->setFlash('success','删除成功');
            return $this->redirect(\Yii::$app->request->referrer);
        }catch (\Exception $e){
            $trans->rollBack();
            return $this->message($e->getMessage(), $this->redirect(\Yii::$app->request->referrer), 'error');
        }
    }

    /**
     *
     * 同步更新价格
     * @return mixed
     */
    public function actionUpdatePrice()
    {
        $ids = Yii::$app->request->post('ids');
        if (empty($ids)) {
            return $this->message("ID不能为空", $this->redirect(['index']), 'error');
        }
        try {
            $trans = \Yii::$app->db->beginTransaction();
            foreach ($ids as $id) {
                $model = WarehouseBillLGoodsForm::findOne($id);
                if(!empty($model)){
                    \Yii::$app->warehouseService->billT->syncUpdatePrice($model);
                }
            }
            \Yii::$app->warehouseService->billL->WarehouseBillLSummary($model->bill_id);
            $trans->commit();
            \Yii::$app->getSession()->setFlash('success', '刷新成功');
            return $this->redirect(\Yii::$app->request->referrer);
        } catch (\Exception $e) {
            $trans->rollBack();
            return $this->message($e->getMessage(), $this->redirect(\Yii::$app->request->referrer), 'error');
        }
    }
}

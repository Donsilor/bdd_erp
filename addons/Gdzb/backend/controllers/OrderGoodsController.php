<?php
namespace addons\Gdzb\backend\controllers;

use addons\Sales\common\enums\OrderStatusEnum;
use addons\Gdzb\common\forms\OrderGoodsForm;
use addons\Gdzb\common\models\Order;
use common\enums\LogTypeEnum;
use common\helpers\ResultHelper;
use common\helpers\StringHelper;
use common\traits\Curd;
use Yii;

class OrderGoodsController extends BaseController
{
    use Curd;
    /**
     * @var PurchaseGoodsForm
     */
    public $modelClass = OrderGoodsForm::class;

    /**
     * 编辑/创建
     * @var PurchaseGoodsForm $model
     * @return mixed
     */
    public function actionEdit()
    {
        $this->layout = '@backend/views/layouts/iframe';
        $id = Yii::$app->request->get('id');
        $order_id = Yii::$app->request->get('order_id');
        $model = $this->findModel($id);
        $model = $model ?? new OrderGoodsForm();
        $model->order_id = $order_id;
        $order = Order::find()->where(['id'=>$order_id])->one();

        // ajax 校验
        if ($model->load(Yii::$app->request->post())) {
            if(!$model->validate()) {
                return ResultHelper::json(422, $this->getError($model));
            }
            try{
                $trans = Yii::$app->trans->beginTransaction();
                $model->warehouse_id = $order->warehouse_id;
                if(false === $model->save()){
                    throw new \Exception($this->getError($model));
                }
                //没有货号，生成货号
                if(!$model->goods_sn){
                    Yii::$app->gdzbService->orderGoods->createGoodsSn($model);
                }else{
                    //判断货品是否库存，如果是库存，则改变库存货品状态
                    Yii::$app->gdzbService->orderGoods->syncGoods($model);
                }
                //更新采购汇总：总金额和总数量
                Yii::$app->gdzbService->order->orderSummary($model->order_id);
                $trans->commit();
                Yii::$app->getSession()->setFlash('success','保存成功');
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
     * 删除
     *
     * @param $id
     * @return mixed
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id)
    {

        $order_id = Yii::$app->request->get('order_id');
        try{
            $trans = Yii::$app->trans->beginTransaction();
            $order = Order::find()->where(['id'=>$order_id])->one();
            if($order->order_status == OrderStatusEnum::CONFORMED) {
                throw new \Exception("订单已审核,不允许删除",422);
            }
            $model = $this->findModel($id);
            if (!$model->delete()) {
                throw new \Exception("删除失败",422);
            }
            //如果货号已在库存，则还原
            Yii::$app->gdzbService->orderGoods->syncGoods($model,'del');


            //更新单据汇总
            Yii::$app->gdzbService->order->orderSummary($order_id);
            $trans->commit();
            return $this->message("删除成功", $this->redirect($this->returnUrl));
        }catch (\Exception $e) {

            $trans->rollback();
            return $this->message($e->getMessage(), $this->redirect($this->returnUrl), 'error');
        }
    }






    /**
     * @return mixed
     * 生成退货单
     */
    public function actionRefund(){
        $order_id = \Yii::$app->request->get('order_id');
        $ids = \Yii::$app->request->get('ids');
        if(!is_object($ids)) {
            $ids = StringHelper::explodeIds($ids);
        }
        try{
            $trans = Yii::$app->db->beginTransaction();
            //同步生成退货单

            $return = Yii::$app->gdzbService->orderGoods->syncRefund($order_id,$ids);

            $log_msg = "订单退货,退货单号：".$return['refund_sn'].";退货商品（".join(',',$ids)."）;";

            //订单日志
            $log = [
                'order_id' => $order_id,
                'order_sn' => $return['order_sn'],
                'order_status' => $return['order_status'],
                'log_type' => LogTypeEnum::ARTIFICIAL,
                'log_time' => time(),
                'log_module' => '订单审核',
                'log_msg' => $log_msg,
            ];
            \Yii::$app->gdzbService->orderLog->createOrderLog($log);
            $trans->commit();
            return $this->message('操作成功', $this->redirect(\Yii::$app->request->referrer), 'success');
        }catch (\Exception $e){
            $trans->rollBack();
            return $this->message($e->getMessage(), $this->redirect(Yii::$app->request->referrer), 'error');
        }
    }

}


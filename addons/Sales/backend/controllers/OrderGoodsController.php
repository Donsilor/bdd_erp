<?php
namespace addons\Sales\backend\controllers;

use addons\Sales\common\enums\OrderStatusEnum;
use addons\Sales\common\forms\OrderGoodsForm;
use addons\Sales\common\models\Order;
use addons\Sales\common\models\OrderGoods;
use addons\Sales\common\models\OrderGoodsAttribute;
use addons\Style\common\enums\QibanTypeEnum;
use addons\Style\common\forms\QibanAttrForm;
use addons\Style\common\forms\StyleAttrForm;
use addons\Style\common\models\Qiban;
use addons\Style\common\models\Style;
use common\enums\StatusEnum;
use common\helpers\ResultHelper;
use common\helpers\Url;
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
        $model = $this->findModel($id);
        $model = $model ?? new OrderGoodsForm();
        if($model->isNewRecord && ($return = $this->checkGoods($model)) !== true) {
            return $return;
        }

        if ($model->load(Yii::$app->request->post())) {
            if(!$model->validate()) {
                return ResultHelper::json(422, $this->getError($model));
            }
            try{
                $trans = Yii::$app->trans->beginTransaction();

                $model->goods_discount = $model->goods_price - $model->goods_pay_price;
                if(false === $model->save()){
                    throw new \Exception($this->getError($model));
                }

                //创建属性关系表数据
                $model->createAttrs();
                //更新采购汇总：总金额和总数量
                Yii::$app->salesService->order->orderSummary($model->order_id);
                $trans->commit();
                //前端提示
                Yii::$app->getSession()->setFlash('success','保存成功');
                return ResultHelper::json(200, '保存成功');
            }catch (\Exception $e){
                $trans->rollBack();
                return ResultHelper::json(422, $e->getMessage());
            }
        }
        //var_dump(1);die;
        $model->initAttrs();
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

            //删除商品属性
            OrderGoodsAttribute::deleteAll(['id'=>$id]);
            //更新单据汇总
            Yii::$app->salesService->order->orderSummary($order_id);
            $trans->commit();

            return $this->message("删除成功", $this->redirect($this->returnUrl));
        }catch (\Exception $e) {

            $trans->rollback();
            return $this->message($e->getMessage(), $this->redirect($this->returnUrl), 'error');
        }
    }


    /**
     * 查询商品
     * @param unknown $model
     * @param unknown $style_sn
     * @return mixed|string
     */
    private function checkGoods(& $model)
    {

        $order_id = Yii::$app->request->get('order_id');
        $goods_sn = Yii::$app->request->get('goods_sn');
        $search = Yii::$app->request->get('search');
        $jintuo_type = Yii::$app->request->get('jintuo_type');

        if($jintuo_type) {
            $model->jintuo_type = $jintuo_type;
        }
        if($model->isNewRecord) {
            $model->order_id = $order_id;
        }
        if($model->isNewRecord && $search && $goods_sn) {

            $skiUrl = Url::buildUrl(\Yii::$app->request->url,[],['search']);
            $style  = Style::find()->where(['style_sn'=>$goods_sn])->one();
            if(!$style) {
                $qiban = Qiban::find()->where(['qiban_sn'=>$goods_sn])->one();
                if(!$qiban) {
                    return $this->message("[款号/起版号]不存在", $this->redirect($skiUrl), 'error');
                }elseif($qiban->status != StatusEnum::ENABLED) {
                    return $this->message("起版号不可用", $this->redirect($skiUrl), 'error');
                }else{
                    $model->style_id = $qiban->id;
                    $model->qiban_sn = $goods_sn;
                    $model->goods_sn = $goods_sn;
                    $model->qiban_type = $qiban->qiban_type;
                    $model->style_sn = $qiban->style_sn;
                    $model->style_cate_id = $qiban->style_cate_id;
                    $model->product_type_id = $qiban->product_type_id;
                    $model->style_channel_id = $qiban->style_channel_id;
                    $model->style_sex = $qiban->style_sex;
                    $model->goods_name = $qiban->qiban_name;
                    $model->jintuo_type = $qiban->jintuo_type;
                    $model->is_inlay = $qiban->is_inlay;
                    $model->remark = $qiban->remark;
                    $model->goods_image = $qiban->style_image;

                    $qibanForm = new QibanAttrForm();
                    $qibanForm->id = $qiban->id;
                    $qibanForm->initAttrs();

                    $model->attr_custom = $qibanForm->attr_custom;
                    $model->attr_require = $qibanForm->attr_require;
                }
            }elseif($style->status != StatusEnum::ENABLED) {
                return $this->message("款号不可用", $this->redirect($skiUrl), 'error');
            }else{
                $model->style_id = $style->id;
                $model->style_sn = $goods_sn;
                $model->goods_sn = $goods_sn;
                $model->qiban_type = QibanTypeEnum::NON_VERSION;
                $model->style_cate_id = $style->style_cate_id;
                $model->product_type_id = $style->product_type_id;
                $model->style_channel_id = $style->style_channel_id;
                $model->style_sex = $style->style_sex;
                $model->goods_name = $style->style_name;
                $model->is_inlay = $style->is_inlay;
                $model->goods_image = $style->style_image;

                $styleForm = new StyleAttrForm();
                $styleForm->style_id = $style->id;
                $styleForm->initAttrs();

                $model->attr_custom = $styleForm->attr_custom;
                $model->attr_require = $styleForm->attr_require;
            }
        }

        return true;
    }


    /**
     * 详情展示页
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView()
    {
        $id = Yii::$app->request->get('id');
        $model = $this->findModel($id);
        $model = $model ?? new OrderGoodsForm();
        $model->initAttrs();
        return $this->render($this->action->id, [
            'model' => $model,
            'returnUrl'=>$this->returnUrl,
        ]);
    }
}


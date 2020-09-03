<?php
namespace addons\Sales\backend\controllers;

use addons\Sales\common\enums\IsGiftEnum;
use addons\Sales\common\enums\IsStockEnum;
use addons\Sales\common\enums\OrderStatusEnum;
use addons\Sales\common\forms\OrderGiftForm;
use addons\Sales\common\forms\OrderGoodsForm;
use addons\Sales\common\forms\StockGoodsForm;
use addons\Sales\common\models\Order;
use addons\Sales\common\models\OrderGoods;
use addons\Sales\common\models\OrderGoodsAttribute;
use addons\Style\common\enums\AttrIdEnum;
use addons\Style\common\enums\JintuoTypeEnum;
use addons\Style\common\enums\QibanTypeEnum;
use addons\Style\common\enums\StyleSexEnum;
use addons\Style\common\forms\QibanAttrForm;
use addons\Style\common\forms\StyleAttrForm;
use addons\Style\common\models\Diamond;
use addons\Style\common\models\Qiban;
use addons\Style\common\models\Style;
use addons\Supply\common\enums\BuChanEnum;
use addons\Supply\common\enums\FromTypeEnum;
use addons\Warehouse\common\enums\GoodsStatusEnum;
use addons\Warehouse\common\models\WarehouseGift;
use addons\Warehouse\common\models\WarehouseGoods;
use common\enums\AuditStatusEnum;
use common\enums\ConfirmEnum;
use common\enums\StatusEnum;
use common\helpers\ResultHelper;
use common\helpers\StringHelper;
use common\helpers\Url;
use common\models\base\SearchModel;
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
                //期货才会更新属性信息
                if($model->is_stock == IsStockEnum::NO){
                    //创建属性关系表数据
                    $model->createAttrs();
                }
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

    /****
     * 选择现货
     */
    public function actionSelectStock(){
        $order_id = Yii::$app->request->get('order_id');

        $id = Yii::$app->request->get('id');
        $model = $this->findModel($id);
        $model = $model ?? new OrderGoodsForm();
        if (Yii::$app->request->post()) {
            $stock_id = Yii::$app->request->post('stock_id');
            if($stock_id == null){
                return ResultHelper::json(422, '请选择');
            }
            $warehouse_goods = WarehouseGoods::find()->where(['id'=>$stock_id])->andWhere(['=','goods_status',GoodsStatusEnum::IN_STOCK])->one();
            $model->goods_num = 1;
            $model->order_id = $order_id;
            $model->goods_sn = $warehouse_goods->goods_id;
            $model->goods_id = $warehouse_goods->goods_id;
            try{
                $trans = Yii::$app->trans->beginTransaction();
                $model = Yii::$app->salesService->orderGoods->addStock($model);
                $trans->commit();
                //前端提示
                Yii::$app->getSession()->setFlash('success','保存成功');
                return ResultHelper::json(200, '保存成功');
            }catch (\Exception $e){
                $trans->rollBack();
                return ResultHelper::json(422, $e->getMessage());
            }
        }

        $searchModel = new SearchModel([
            'model' => WarehouseGoods::class,
            'scenario' => 'default',
            'partialMatchAttributes' => [], // 模糊查询
            'defaultOrder' => [
                'id' => SORT_DESC
            ],
            'pageSize' => 5
        ]);

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['=', 'goods_status', GoodsStatusEnum::IN_STOCK]);
        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'order_id' => $order_id,
            'model' =>$model

        ]);
    }


    /***
     * @return array|mixed|string
     * @throws \yii\db\Exception
     *编辑现货
     */
    public function actionEditStock(){

        $this->layout = '@backend/views/layouts/iframe';
        $id = Yii::$app->request->get('id');
        $model = $this->findModel($id);
        $model = $model ?? new OrderGoodsForm();
        if($model->isNewRecord){
            $goods_id = Yii::$app->request->get('goods_id');
            $search = Yii::$app->request->get('search');
            $order_id = Yii::$app->request->get('order_id');
            if($model->isNewRecord && $search && $goods_id) {
                $wareshouse_goods = WarehouseGoods::find()->where(['goods_id'=>$goods_id, 'goods_status'=>GoodsStatusEnum::IN_STOCK])->one();
                if(empty($wareshouse_goods)){
                    $skiUrl = Url::buildUrl(\Yii::$app->request->url,[],['search']);
                    return $this->message('此货号不存在或者不是库存状态', $this->redirect($skiUrl), 'error');
                }

                $model->jintuo_type = $wareshouse_goods->jintuo_type;
                $model->qiban_type = $wareshouse_goods->qiban_type;
                $model->style_sex = $wareshouse_goods->style_sex;
                $model->style_cate_id = $wareshouse_goods->style_cate_id;
                $model->product_type_id = $wareshouse_goods->product_type_id;
                $model->goods_num = 1;
                $model->goods_name = $wareshouse_goods->goods_name;
                $model->style_sn = $wareshouse_goods->style_sn;
                $model->qiban_sn = $wareshouse_goods->qiban_sn;

                $model->order_id = $order_id;
                $model->currency = $model->order->currency;
                $model->goods_id = $goods_id;
            }


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
                if($model->isNewRecord) {
                    Yii::$app->salesService->orderGoods->toStock($model);
                }
                $trans->commit();
                //更新采购汇总：总金额和总数量
                \Yii::$app->salesService->order->orderSummary($model->order_id);
                //前端提示
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


    /***
     * @return array|mixed|string
     * @throws \yii\db\Exception
     *备份
     */
    public function actionEditStockBackups(){

        $this->layout = '@backend/views/layouts/iframe';
        $id = Yii::$app->request->get('id');
        $model = $this->findModel($id);
        $model = $model ?? new OrderGoodsForm();
        if($model->isNewRecord){
            $goods_id = Yii::$app->request->get('goods_id');
            $search = Yii::$app->request->get('search');
            $order_id = Yii::$app->request->get('order_id');
            if($model->isNewRecord && $search && $goods_id) {
                $wareshouse_goods = WarehouseGoods::find()->where(['goods_id'=>$goods_id, 'goods_status'=>GoodsStatusEnum::IN_STOCK])->one();
                if(empty($wareshouse_goods)){
                    $skiUrl = Url::buildUrl(\Yii::$app->request->url,[],['search']);
                    return $this->message('此货号不存在或者不是库存状态', $this->redirect($skiUrl), 'error');
                }

                $model->jintuo_type = $wareshouse_goods->jintuo_type;
                $model->qiban_type = $wareshouse_goods->qiban_type;
                $model->style_sex = $wareshouse_goods->style_sex;
                $model->style_cate_id = $wareshouse_goods->style_cate_id;
                $model->product_type_id = $wareshouse_goods->product_type_id;
                $model->goods_num = 1;
                $model->goods_name = $wareshouse_goods->goods_name;
                $model->style_sn = $wareshouse_goods->style_sn;
                $model->qiban_sn = $wareshouse_goods->qiban_sn;

                $model->order_id = $order_id;
                $model->currency = $model->order->currency;
                $model->goods_id = $goods_id;
            }


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
                if($model->isNewRecord) {
                    Yii::$app->salesService->orderGoods->toStock($model);
                }
                $trans->commit();
                //前端提示
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



    /****
     * 选择裸钻
     */
    public function actionSelectDiamond(){
        $order_id = Yii::$app->request->get('order_id');

        $id = Yii::$app->request->get('id');
        $model = $this->findModel($id);
        $model = $model ?? new OrderGoodsForm();
        if (Yii::$app->request->post()) {
            $diamon_id = Yii::$app->request->post('diamon_id');
            if($diamon_id == null){
                return ResultHelper::json(422, '请选择');
            }
            $diamond_goods = Diamond::find()->where(['id'=>$diamon_id,'status'=>StatusEnum::ENABLED])->one();
            $model->goods_num = 1;
            $model->order_id = $order_id;
            $model->goods_sn = $diamond_goods->goods_sn;
            try{
                $trans = Yii::$app->trans->beginTransaction();
                $model->jintuo_type = JintuoTypeEnum::Chengpin;
                $model->qiban_type = QibanTypeEnum::NON_VERSION;
                $model->style_sex = StyleSexEnum::COMMON;
                $model->style_cate_id = 15; //裸钻
                $model->product_type_id = 1; //钻石
                $model->goods_num = 1;
                $model->goods_name = $diamond_goods->goods_name;
                $model->is_stock = $diamond_goods->is_stock;
                $model->goods_price = $diamond_goods->sale_price;
                $model->goods_pay_price = $diamond_goods->sale_price;
                $model->goods_discount = $model->goods_price - $model->goods_pay_price;
                $model->style_sn = \Yii::$app->styleService->stone->getStoneSn(234,$diamond_goods->carat);
                $model->qiban_sn = '';
                $model->goods_image = $diamond_goods->goods_image;
                $model->cert_id = $diamond_goods->cert_id;
                $model->order_id = $order_id;
                $model->currency = $model->order->currency;
                $model->goods_id = (string)$diamond_goods->goods_id;
                if(false === $model->save()){
                    throw new \Exception($this->getError($model));
                }
                Yii::$app->salesService->orderGoods->addDiamond($model);
                $trans->commit();
                //前端提示
                Yii::$app->getSession()->setFlash('success','保存成功');
                return ResultHelper::json(200, '保存成功');
            }catch (\Exception $e){
                $trans->rollBack();
                return ResultHelper::json(422, $e->getMessage());
            }
        }

        $searchModel = new SearchModel([
            'model' => Diamond::class,
            'scenario' => 'default',
            'partialMatchAttributes' => [], // 模糊查询
            'defaultOrder' => [
                'id' => SORT_DESC
            ],
            'pageSize' => 5
        ]);

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['=', 'status', StatusEnum::ENABLED]);
        $dataProvider->query->andWhere(['=', 'audit_status', AuditStatusEnum::PASS]);
        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'order_id' => $order_id,
            'model' =>$model

        ]);
    }

    /***
     * @return array|mixed|string
     * @throws \yii\db\Exception
     * 编辑裸钻
     */
    public function actionEditDiamond(){

        $this->layout = '@backend/views/layouts/iframe';
        $id = Yii::$app->request->get('id');
        $model = $this->findModel($id);
        $model = $model ?? new OrderGoodsForm();
        if($model->isNewRecord){
            $cert_id = Yii::$app->request->get('cert_id');
            $search = Yii::$app->request->get('search');
            $order_id = Yii::$app->request->get('order_id');
            if($search && $cert_id) {
                $diamond_goods = Diamond::find()->where(['cert_id'=>$cert_id, 'audit_status'=>AuditStatusEnum::PASS])->one();
                if(empty($diamond_goods)){
                    $skiUrl = Url::buildUrl(\Yii::$app->request->url,[],['search']);
                    return $this->message('此裸钻不存在或者审核没通过', $this->redirect($skiUrl), 'error');
                }

                $model->jintuo_type = JintuoTypeEnum::Chengpin;
                $model->qiban_type = QibanTypeEnum::NON_VERSION;
                $model->style_sex = StyleSexEnum::COMMON;
                $model->style_cate_id = 15; //裸钻
                $model->product_type_id = 1; //钻石
                $model->goods_num = 1;
                $model->goods_name = $diamond_goods->goods_name;
                $model->is_stock = $diamond_goods->is_stock;
                $model->goods_pay_price = $diamond_goods->sale_price;
                //$model->goods_price = $diamond_goods->sale_price;
                $model->style_sn = \Yii::$app->styleService->stone->getStoneSn(234,$diamond_goods->carat);
                $model->qiban_sn = '';
                $model->goods_image = $diamond_goods->goods_image;
                $model->cert_id = $cert_id;
                $model->order_id = $order_id;
                $model->currency = $model->order->currency;
                $model->goods_id = (string)$diamond_goods->goods_sn;
            }

        }else{
            $order_goods_attr = OrderGoodsAttribute::find()->where(['id'=>$model->id,'attr_id'=>AttrIdEnum::DIA_CERT_NO])->one();
            $model->cert_id = $order_goods_attr->attr_value;
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
                Yii::$app->salesService->orderGoods->addDiamond($model);
                $trans->commit();
                //前端提示
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


    /***
     * @return array|mixed|string
     * @throws \yii\db\Exception
     * 编辑裸钻(备份)
     */
    public function actionEditDiamondBackups(){

        $this->layout = '@backend/views/layouts/iframe';
        $id = Yii::$app->request->get('id');
        $model = $this->findModel($id);
        $model = $model ?? new OrderGoodsForm();
        if($model->isNewRecord){
            $cert_id = Yii::$app->request->get('cert_id');
            $search = Yii::$app->request->get('search');
            $order_id = Yii::$app->request->get('order_id');
            if($search && $cert_id) {
                $diamond_goods = Diamond::find()->where(['cert_id'=>$cert_id, 'audit_status'=>AuditStatusEnum::PASS])->one();
                if(empty($diamond_goods)){
                    $skiUrl = Url::buildUrl(\Yii::$app->request->url,[],['search']);
                    return $this->message('此裸钻不存在或者审核没通过', $this->redirect($skiUrl), 'error');
                }

                $model->jintuo_type = JintuoTypeEnum::Chengpin;
                $model->qiban_type = QibanTypeEnum::NON_VERSION;
                $model->style_sex = StyleSexEnum::COMMON;
                $model->style_cate_id = 15; //裸钻
                $model->product_type_id = 1; //钻石
                $model->goods_num = 1;
                $model->goods_name = $diamond_goods->goods_name;
                $model->is_stock = $diamond_goods->is_stock;
                $model->goods_pay_price = $diamond_goods->sale_price;
                //$model->goods_price = $diamond_goods->sale_price;
                echo \Yii::$app->styleService->stone->getStoneSn(234,$diamond_goods->carat);exit;
                $model->style_sn = \Yii::$app->styleService->stone->getStoneSn(234,$diamond_goods->carat);
                $model->qiban_sn = '';
                $model->goods_image = $diamond_goods->goods_image;
                $model->cert_id = $cert_id;
                $model->order_id = $order_id;
                $model->currency = $model->order->currency;
                $model->goods_id = (string)$diamond_goods->goods_sn;
            }

        }else{
            $order_goods_attr = OrderGoodsAttribute::find()->where(['id'=>$model->id,'attr_id'=>AttrIdEnum::DIA_CERT_NO])->one();
            $model->cert_id = $order_goods_attr->attr_value;
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
                Yii::$app->salesService->orderGoods->addDiamond($model);
                $trans->commit();
                //前端提示
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


    /****
     * 选择赠品
     */
    public function actionSelectGift(){
        $order_id = Yii::$app->request->get('order_id');

        $id = Yii::$app->request->get('id');
        $this->modelClass = OrderGiftForm::class;
        $model = $this->findModel($id);
        $model = $model ?? new OrderGiftForm();
        if (Yii::$app->request->post()) {
            $gift_id = Yii::$app->request->post('gift_id');
            if($gift_id == null){
                return ResultHelper::json(422, '请选择赠品');
            }
            $gift_goods = WarehouseGift::find()->where(['id'=>$gift_id])->andWhere(['>','gift_num',0])->one();
            $model->goods_num = 1;
            $model->order_id = $order_id;
            $model->goods_sn = $gift_goods->gift_sn;
            try{
                $trans = Yii::$app->trans->beginTransaction();

                $num = bcsub(0, $model->goods_num);
                $model = Yii::$app->salesService->orderGoods->addGift($model,$num);
                $trans->commit();
                //前端提示
                Yii::$app->getSession()->setFlash('success','保存成功');
                return ResultHelper::json(200, '保存成功');
            }catch (\Exception $e){
                $trans->rollBack();
                return ResultHelper::json(422, $e->getMessage());
            }
        }

        $searchModel = new SearchModel([
            'model' => WarehouseGift::class,
            'scenario' => 'default',
            'partialMatchAttributes' => [], // 模糊查询
            'defaultOrder' => [
                'id' => SORT_DESC
            ],
            'pageSize' => 5
        ]);

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['>', 'gift_num', 0]);
        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'order_id' => $order_id,
            'model' =>$model

        ]);
    }

    /***
     * 添加赠品
     */
    public function actionEditGift(){
        $this->layout = '@backend/views/layouts/iframe';
        $id = Yii::$app->request->get('id');
        $this->modelClass = OrderGiftForm::class;
        $model = $this->findModel($id);
        $model = $model ?? new OrderGiftForm();

        if($model->isNewRecord){
            $gift_id = Yii::$app->request->get('gift_id');
            $order_id = Yii::$app->request->get('order_id');
            $model->goods_num = 1;
            $model->order_id = $order_id;
            $model->gift_id = $gift_id;
        }
        //获取更改前赠品数量
        $goods_num = $model->goods_num;
        if ($model->load(Yii::$app->request->post())) {
            if(!$model->validate()) {
                return ResultHelper::json(422, $this->getError($model));
            }
            try{
                $trans = Yii::$app->trans->beginTransaction();

                $num = bcsub($goods_num, $model->goods_num);
                $model = Yii::$app->salesService->orderGoods->addGift($model,$num);
                $trans->commit();
                //前端提示
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
            if($model->product_type_id == 1){
                //裸钻
                Yii::$app->salesService->orderGoods->delDiamond($model);
            }elseif ($model->is_gift == IsGiftEnum::YES){
                //赠品
                Yii::$app->salesService->orderGoods->delGift($model);
            }elseif($model->is_stock == IsStockEnum::YES){
                //现货解绑
                Yii::$app->salesService->orderGoods->toUntie($model);
            }

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
            $model->currency = $model->order->currency;
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

//                $styleForm = new StyleAttrForm();
//                $styleForm->style_id = $style->id;
//                $styleForm->initAttrs();

//                $model->attr_custom = $styleForm->attr_custom;
//                $model->attr_require = $styleForm->attr_require;

            }
        }

        return true;
    }


    /***
     * @return mixed|string|\yii\web\Response
     * @throws \yii\base\ExitException
     * @throws \yii\db\Exception
     * 绑定现货
     */
    public function actionStock(){
        $this->layout = '@backend/views/layouts/iframe';
        $id = Yii::$app->request->get('id');
        $this->modelClass = StockGoodsForm::class;
        $model = $this->findModel($id);
        $model = $model ?? new StockGoodsForm();
        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {
            try{
                $trans = Yii::$app->trans->beginTransaction();
                Yii::$app->salesService->orderGoods->toStock($model);
                $trans->commit();
                //前端提示
                Yii::$app->getSession()->setFlash('success','保存成功');
                return $this->redirect(Yii::$app->request->referrer);
            }catch (\Exception $e){
                $trans->rollBack();
                return $this->message($e->getMessage(), $this->redirect(Yii::$app->request->referrer), 'error');
            }
        }

        return $this->render($this->action->id, [
            'model' => $model,
        ]);
    }


    /***
     * @return mixed|\yii\web\Response
     * @throws \yii\db\Exception
     * 解绑
     */
    public function actionUntie(){
        $id = Yii::$app->request->get('id');
        $model = $this->findModel($id);
        try{
            $trans = Yii::$app->trans->beginTransaction();
            Yii::$app->salesService->orderGoods->toUntie($model);
            $trans->commit();
            //前端提示
            Yii::$app->getSession()->setFlash('success','解绑成功');
            return $this->redirect(Yii::$app->request->referrer);
        }catch (\Exception $e){
            $trans->rollBack();
            return $this->message($e->getMessage(), $this->redirect(Yii::$app->request->referrer), 'error');
        }

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
    /**
     * @return mixed
     * 布产
     */
    public function actionBuchan(){
        $ids = \Yii::$app->request->get('ids');
        if(!is_object($ids)) {
            $ids = StringHelper::explodeIds($ids);
        }
        try{
            $trans = Yii::$app->db->beginTransaction();
            $order_goods = OrderGoods::find()->where(['id'=>$ids])->all();
            foreach ($order_goods as $model){
                if($model['is_stock'] == IsStockEnum::YES){
                    return $this->message("商品{$model->id}为现货，不能布产", $this->redirect(\Yii::$app->request->referrer), 'warning');
                }
                if($model['is_bc'] == ConfirmEnum::YES){
                    return $this->message("商品{$model->id}为已经布产", $this->redirect(\Yii::$app->request->referrer), 'warning');
                }

                if($model->qiban_type == QibanTypeEnum::NON_VERSION ){
                    //非起版
                    $is_exeist = Yii::$app->styleService->style->isExist($model->style_sn);
                    if(!$is_exeist){
                        return $this->message("款式库没有此款号{$model->style_sn},请确认", $this->redirect(\Yii::$app->request->referrer), 'warning');
                    }
                }else{
                    //起版
                    $is_exeist = Yii::$app->styleService->qiban->isExist($model->qiban_sn);
                    if(!$is_exeist){
                        return $this->message("起版库没有此起版号{$model->qiban_sn},请确认", $this->redirect(\Yii::$app->request->referrer), 'warning');
                    }
                }


                $goods = [
                    'goods_name' =>$model->goods_name,
                    'goods_num' =>$model->goods_num,
                    'order_detail_id'=>$model->order_id,
                    'order_detail_id' => $model->id,
                    'order_sn'=>$model->order->order_sn,
                    'from_type' => FromTypeEnum::ORDER,
                    'style_sn' => $model->style_sn,
                    'bc_status' => BuChanEnum::INITIALIZATION,
                    'qiban_sn' => $model->qiban_sn,
                    'qiban_type'=>$model->qiban_type,
                    'jintuo_type'=>$model->jintuo_type,
                    'style_sex' =>$model->style_sex,
                    'is_inlay' =>$model->is_inlay,
                    'product_type_id'=>$model->product_type_id,
                    'style_cate_id'=>$model->style_cate_id,
                ];
                $goods_attrs = OrderGoodsAttribute::find()->where(['id'=>$model->id])->asArray()->all();
                $produce = Yii::$app->supplyService->produce->createProduce($goods ,$goods_attrs);
                if($produce) {
                    $model->bc_status = BuChanEnum::INITIALIZATION;
                    $model->is_bc = ConfirmEnum::YES;
                    $model->produce_sn = $produce->produce_sn;
                }
                if(false === $model->save()) {
                    throw new \Exception($this->getError($model),422);
                }
            }
            $trans->commit();
            return $this->message('操作成功', $this->redirect(\Yii::$app->request->referrer), 'success');
        }catch (\Exception $e){
            $trans->rollBack();
            return $this->message($e->getMessage(), $this->redirect(Yii::$app->request->referrer), 'error');
        }
    }

}


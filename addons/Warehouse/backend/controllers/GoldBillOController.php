<?php

namespace addons\Warehouse\backend\controllers;

use addons\Sales\common\models\SaleChannel;
use addons\Style\common\enums\LogTypeEnum;
use addons\Style\common\models\ProductType;
use addons\Style\common\models\StyleCate;
use addons\Supply\common\models\Supplier;
use addons\Warehouse\common\enums\DeliveryTypeEnum;
use addons\Warehouse\common\enums\GoldBillTypeEnum;
use addons\Warehouse\common\enums\OutTypeEnum;
use addons\Warehouse\common\forms\WarehouseBillCForm;
use addons\Warehouse\common\forms\WarehouseGoldBillOForm;
use addons\Warehouse\common\models\Warehouse;
use addons\Warehouse\common\models\WarehouseBillGoods;
use addons\Warehouse\common\models\WarehouseGoldBill;
use addons\Warehouse\common\models\WarehouseGoods;
use common\helpers\ArrayHelper;
use common\helpers\PageHelper;
use common\helpers\StringHelper;
use common\models\backend\Member;
use Yii;
use common\traits\Curd;
use common\helpers\Url;
use common\helpers\SnHelper;
use common\helpers\ExcelHelper;
use common\models\base\SearchModel;
use addons\Warehouse\common\models\WarehouseBill;
use addons\Warehouse\common\enums\BillStatusEnum;
use addons\Warehouse\common\enums\BillTypeEnum;
use common\enums\AuditStatusEnum;
use common\helpers\ResultHelper;
use addons\Warehouse\common\forms\ImportBillCForm;
use yii\web\UploadedFile;
use addons\Warehouse\common\enums\GoodsStatusEnum;

/**
 * WarehouseBillBController implements the CRUD actions for WarehouseBillBController model.
 */
class GoldBillOController extends BaseController
{
    use Curd;
    public $modelClass = WarehouseGoldBillOForm::class;
    public $billType = GoldBillTypeEnum::GOLD_O;

    /**
     * Lists all WarehouseBill models.
     * @return mixed
     */
    public function actionIndex()
    {

        $searchModel = new SearchModel([
            'model' => $this->modelClass,
            'scenario' => 'default',
            'partialMatchAttributes' => [], // 模糊查询
            'defaultOrder' => [
                'id' => SORT_DESC
            ],
            'pageSize' => $this->pageSize,
            'relations' => [
                'creator' => ['username'],
                'auditor' => ['username'],
            ]
        ]);

        $dataProvider = $searchModel
            ->search(\Yii::$app->request->queryParams,['created_at', 'audit_time']);

        $created_at = $searchModel->created_at;
        if (!empty($created_at)) {
            $dataProvider->query->andFilterWhere(['>=',WarehouseGoldBillOForm::tableName().'.created_at', strtotime(explode('/', $created_at)[0])]);//起始时间
            $dataProvider->query->andFilterWhere(['<',WarehouseGoldBillOForm::tableName().'.created_at', (strtotime(explode('/', $created_at)[1]) + 86400)] );//结束时间
        }

        $audit_time = $searchModel->audit_time;
        if (!empty($audit_time)) {
            $dataProvider->query->andFilterWhere(['>=',WarehouseGoldBillOForm::tableName().'.audit_time', strtotime(explode('/', $audit_time)[0])]);//起始时间
            $dataProvider->query->andFilterWhere(['<',WarehouseGoldBillOForm::tableName().'.audit_time', (strtotime(explode('/', $audit_time)[1]) + 86400)] );//结束时间
        }

        $dataProvider->query->andWhere(['>',WarehouseGoldBillOForm::tableName().'.status', -1]);
        $dataProvider->query->andWhere(['=',WarehouseGoldBillOForm::tableName().'.bill_type', $this->billType]);

        //导出
        if(Yii::$app->request->get('action') === 'export'){
            $dataProvider->setPagination(false);
            $list = $dataProvider->models;
            $list = ArrayHelper::toArray($list);
            $ids = array_column($list,'id');
            $this->actionExport($ids);
        }

        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * ajax编辑/创建 其它出库单
     *
     * @return mixed|string|\yii\web\Response
     * @throws \yii\base\ExitException
     */
    public function actionAjaxEdit()
    {
        $id = \Yii::$app->request->get('id');
        $model = $this->findModel($id);
        $model = $model ?? new WarehouseGoldBillOForm();

        if($model->isNewRecord){
            $model->bill_type = $this->billType;
        }
        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(\Yii::$app->request->post())) {
            $isNewRecord = $model->isNewRecord;
            if($model->isNewRecord){
                $model->bill_no = SnHelper::createBillSn($this->billType);
            }
            try{
                $trans = \Yii::$app->db->beginTransaction();
                if(false === $model->save()) {
                    throw new \Exception($this->getError($model));
                }
                if($isNewRecord){
                    $log_msg = "创建其它出库单{$model->bill_no}，出库类型：".OutTypeEnum::getValue($model->out_type);
                }else{
                    $log_msg = "修改其它出库单{$model->bill_no}，出库类型：".OutTypeEnum::getValue($model->out_type);
                }
                $log = [
                    'bill_id' => $model->id,
                    'bill_status' => $model->bill_status,
                    'log_type' => LogTypeEnum::ARTIFICIAL,
                    'log_module' => '其它出库单',
                    'log_msg' => $log_msg
                ];
                \Yii::$app->warehouseService->goldBillLog->createGoldBillLog($log);

                $trans->commit();

                if($isNewRecord) {
                    return $this->message("保存成功", $this->redirect(['view', 'id' => $model->id]), 'success');
                }else {
                    return $this->message('保存成功', $this->redirect(Yii::$app->request->referrer), 'success');
                }
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
     * 详情展示页
     * @return string
     * @throws
     */
    public function actionView()
    {
        $id = Yii::$app->request->get('id');
        $tab = Yii::$app->request->get('tab',1);
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['bill-c/index', 'id'=>$id]));
        $model = $this->findModel($id) ?? new WarehouseGoldBillOForm();
        return $this->render($this->action->id, [
            'model' => $model,
            'tab'=>$tab,
            'tabList'=>\Yii::$app->warehouseService->goldO->menuTabList($id,$returnUrl),
            'returnUrl'=>$returnUrl,
        ]);
    }

    /**
     * ajax 其它出库单-申请审核
     *
     * @return mixed|string|\yii\web\Response
     * @throws \yii\base\ExitException
     */
    public function actionAjaxApply($id){
        
        $id = \Yii::$app->request->get('id');        
        $model = $this->findModel($id) ?? new WarehouseGoldBillOForm();
        if($model->bill_status != BillStatusEnum::SAVE){
            return $this->message('单据不是保存状态', $this->redirect(\Yii::$app->request->referrer), 'error');
        }
        if($model->total_weight<=0){
            return $this->message('单据明细不能为空', $this->redirect(\Yii::$app->request->referrer), 'error');
        }        
        try{
            
            $trans = \Yii::$app->trans->beginTransaction();
            $model->bill_status = BillStatusEnum::PENDING;
            $model->audit_status = AuditStatusEnum::PENDING;
            if(false === $model->save()){
                return $this->message($this->getError($model), $this->redirect(\Yii::$app->request->referrer), 'error');
            }
            //日志
            $log = [
                'bill_id' => $model->id,
                'bill_status' => $model->bill_status,
                'log_type' => LogTypeEnum::ARTIFICIAL,
                'log_module' => '提交审核',
                'log_msg' => "其它出库单申请审核"
            ];
            \Yii::$app->warehouseService->goldBillLog->createGoldBillLog($log);
            $trans->commit();
            return $this->message('操作成功', $this->redirect(\Yii::$app->request->referrer), 'success');

        }catch (\Exception $e){
            $trans->rollBack();
            return $this->message($e->getMessage(), $this->redirect(\Yii::$app->request->referrer), 'error');
        }
    }

    /**
     * ajax 其它出库单-审核
     *
     * @return mixed|string|\yii\web\Response
     * @throws \yii\base\ExitException
     */
    public function actionAjaxAudit()
    {
        $id = Yii::$app->request->get('id');
        $model = $this->findModel($id)  ?? new WarehouseBillCForm();

        if($model->audit_status == AuditStatusEnum::PENDING) {
            $model->audit_status = AuditStatusEnum::PASS;
        }
        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {

            try{
                $trans = \Yii::$app->trans->beginTransaction();

                $model->audit_time = time();
                $model->auditor_id = \Yii::$app->user->identity->getId();

                if($model->bill_status != BillStatusEnum::PENDING) {
                    throw new \Exception("单据不是待审核状态");
                }
                if($model->audit_status == AuditStatusEnum::PASS){
                    $model->bill_status = BillStatusEnum::CONFIRM;
                }else{
                    $model->bill_status = BillStatusEnum::SAVE;
                }
                if(false === $model->save()) {
                    throw new \Exception($this->getError($model));
                }
                //日志
                $log = [
                    'bill_id' => $model->id,
                    'bill_status' => $model->bill_status,
                    'log_type' => LogTypeEnum::ARTIFICIAL,
                    'log_module' => '其它出库单',
                    'log_msg' => "其它出库单审核, 审核状态：".AuditStatusEnum::getValue($model->audit_status).",审核备注：".$model->audit_remark
                ];
                \Yii::$app->warehouseService->goldBillLog->createGoldBillLog($log);

                $trans->commit();
                $this->message('操作成功', $this->redirect(Yii::$app->request->referrer), 'success');
            }catch(\Exception $e){
                $trans->rollBack();
                $this->message($e->getMessage(), $this->redirect(Yii::$app->request->referrer), 'error');
            }
        }

        return $this->renderAjax($this->action->id, [
            'model' => $model,
        ]);
    }

    /**
     * 其它出库单-关闭
     *
     * @param $id
     * @return mixed
     */
    public function actionCancel($id)
    {
        if (!($model = $this->modelClass::findOne($id))) {
            return $this->message("找不到数据", $this->redirect(Yii::$app->request->referrer), 'error');
        }        
        try{
            $trans = \Yii::$app->trans->beginTransaction();

            \Yii::$app->warehouseService->goldO->cancelBillO($model);

            $trans->commit();
            $this->message('操作成功', $this->redirect(Yii::$app->request->referrer), 'success');
        }catch (\Exception $e){
            $trans->rollBack();
            return $this->message($e->getMessage(), $this->redirect(\Yii::$app->request->referrer), 'error');
        }
    }

    /**
     * 其它出库单-删除
     *
     * @param $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        if (!($model = $this->modelClass::findOne($id))) {
            return $this->message("找不到数据", $this->redirect(Yii::$app->request->referrer), 'error');
        }
        try{
            $trans = \Yii::$app->trans->beginTransaction();
            \Yii::$app->warehouseService->goldO->deleteBillO($model);
            $trans->commit();
            $this->message('操作成功', $this->redirect(Yii::$app->request->referrer), 'success');
        }catch (\Exception $e){
            $trans->rollBack();
            return $this->message($e->getMessage(), $this->redirect(\Yii::$app->request->referrer), 'error');
        }
    }
    /**
     * 其它出库单批量导入
     */
    public function actionAjaxImport()
    {
        if(Yii::$app->request->get('download')) {
            $file = dirname(dirname(__FILE__)).'/resources/excel/其它出库单数据模板导入.xlsx';
            $content = file_get_contents($file);
            if (!empty($content)) {
                header("Content-type:application/vnd.ms-excel");
                header("Content-Disposition: attachment;filename=其它出库单数据模板导入".date("Ymd").".xlsx");
                header("Content-Transfer-Encoding: binary");
                exit($content);
            }
        }
        $model =  new ImportBillCForm();
        // ajax 校验
        $this->activeFormValidate($model);
        
        if ($model->load(\Yii::$app->request->post())) {
            
            try{
                $trans = \Yii::$app->trans->beginTransaction();
                
                $model->file = UploadedFile::getInstance($model, 'file');
                $model->bill_type = $this->billType;
                
                \Yii::$app->warehouseService->billC->importBillC($model);
                $trans->commit();
                return $this->message('导入成功', $this->redirect(Yii::$app->request->referrer), 'success');
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
     * 快捷出库 创建
     */
    public function actionQuick()
    {
        $this->layout = '@backend/views/layouts/iframe';
        
         
        if(\Yii::$app->request->get('popCheck')) {
            $ids = StringHelper::explodeIds(Yii::$app->request->get('ids'));
            foreach ($ids as $id) {
                $goods = WarehouseGoods::find()->where(['id'=>$id])->select(['goods_id','goods_status'])->one();
                if($goods->goods_status != GoodsStatusEnum::IN_STOCK) {
                    return ResultHelper::json(422, "[{$goods->goods_id}]条码号不是库存状态");
                }
            }
            return ResultHelper::json(200, "success");
        }
        
        $form = new WarehouseBillCForm();
        if ($form->load(\Yii::$app->request->post())) {
            try{
                $trans = \Yii::$app->trans->beginTransaction();
                $form->goods_ids = Yii::$app->request->get('ids');
                $form->bill_type = $this->billType;
                $model = Yii::$app->warehouseService->billC->quickBillC($form);
                $trans->commit();
                Yii::$app->getSession()->setFlash('success','操作成功，出库单号:'.$model->bill_no);
                return ResultHelper::json(200,'保存成功', ['jumpUrl'=>Url::to(['bill-c-goods/index','bill_id'=>$model->id])]);
            }catch (\Exception $e){
                $trans->rollBack();
                return ResultHelper::json(423,$e->getMessage());
            }
        }        
        return $this->render($this->action->id, [
                'model' => $form,
        ]);
    }
    /***
     * 导出Excel
     */
    public function actionExport($ids=null){
        if(!is_array($ids)){
            $ids = StringHelper::explodeIds($ids);
        }
        if(!$ids){
            return $this->message('单据ID不为空', $this->redirect(['index']), 'warning');
        }
        list($list,) = $this->getData($ids);
        // [名称, 字段名, 类型, 类型规则]
        $header = [
            ['出库单号', 'bill_no', 'text'],
            ['销售渠道', 'channel_name', 'text'],
            ['制单日期', 'created_at', 'date','Y-m-d'],
            ['出库日期', 'audit_time', 'date','Y-m-d'],
            ['制单人', 'creator_name', 'text'],
            ['接收人', 'salesman_name', 'text'],
            ['货号', 'goods_id', 'text'],
            ['款号', 'style_sn', 'text'],
            ['商品名称', 'goods_name', 'text'],
            ['商品数量', 'goods_num', 'text'],
            ['商品状态', 'goods_status', 'function',function($model){
                return GoodsStatusEnum::getValue($model['goods_status']);
            }],
            ['款式分类', 'product_type_name' , 'text'],
            ['产品线', 'style_cate_name' , 'text'],
            ['仓库', 'warehouse_name' , 'text'],
            ['材质', 'material_type', 'function', function ($model) {
                return \Yii::$app->attr->valueName($model['material_type']);
            }],
            ['材质颜色', 'material_color', 'function', function ($model) {
                return \Yii::$app->attr->valueName($model['material_color']);
            }],
            ['连石重', 'suttle_weight' , 'text'],
            ['主石类型', 'main_stone_type', 'function', function ($model) {
                return \Yii::$app->attr->valueName($model['main_stone_type']);
            }],
            ['主石大小（ct)', 'diamond_carat' , 'text'],
            ['主石粒数', 'main_stone_num' , 'text'],
            ['副石1重（ct）', 'second_stone_weight1' , 'text'],
            ['副石1粒数', 'second_stone_num1' , 'text'],
            ['手寸', 'finger', 'function', function ($model) {
                $finger = '';
                if($model['finger']){
                    $finger .= Yii::$app->attr->valueName($model['finger']).'(u)';
                }
                if($model['finger_hk']){
                    $finger .= ' '.Yii::$app->attr->valueName($model['finger_hk']).'(u)';
                }
                return $finger;
            }],
            ['证书号	', 'cert_id' , 'text'],
            ['采购成本/单件	', 'cost_price' , 'text'],
            ['采购总成本	', 'cost_price' , 'function', function($model){
                $cost_price = $model['cost_price'];
                $goods_num = $model['goods_num'];
                return $cost_price * $goods_num;
            }],
            ['出库成本价		', 'chuku_price' , 'text'],

        ];

        return ExcelHelper::exportData($list, $header, '其他出库单_' . date('YmdHis',time()));

    }


    /**
     * 单据打印
     * @return string
     * @throws
     */
    public function actionPrint()
    {
        $this->layout = '@backend/views/layouts/print';
        $id = \Yii::$app->request->get('id');
        if(empty($id)){
            exit("ID不能为空");
        }
        $model = $this->findModel($id);
        if(!$model){
            exit("单据不存在");
        }
        list($lists, $total) = $this->getData($id);
        return $this->render($this->action->id, [
            'model' => $model,
            'lists' => $lists,
            'total' => $total
        ]);
    }

    /**
     * @param array $ids
     * @return array
     */
    public function getData($ids)
    {
        $select = [
            'g.*',
            'w.bill_no', 'w.bill_status','w.created_at','w.audit_time',
            'channel.name as channel_name','salesman.username as salesman_name',
            'creator.username as creator_name',
            'type.name as product_type_name',
            'cate.name as style_cate_name',
            'warehouse.name as warehouse_name',
            'sup.supplier_name'
        ];
        $query = WarehouseBill::find()->alias('w')
            ->leftJoin(WarehouseBillGoods::tableName() . " wg", 'w.id=wg.bill_id')
            ->leftJoin(WarehouseGoods::tableName() . ' g', 'g.goods_id=wg.goods_id')
            ->leftJoin(ProductType::tableName() . ' type', 'type.id=g.product_type_id')
            ->leftJoin(StyleCate::tableName() . ' cate', 'cate.id=g.style_cate_id')
            ->leftJoin(Warehouse::tableName() . ' warehouse', 'warehouse.id=g.warehouse_id')
            ->leftJoin(Supplier::tableName() . ' sup', 'sup.id=w.supplier_id')
            ->leftJoin(SaleChannel::tableName() . ' channel', 'channel.id=w.channel_id')
            ->leftJoin(Member::tableName() . ' creator', 'creator.id=w.creator_id')
            ->leftJoin(Member::tableName() . ' salesman', 'salesman.id=w.salesman_id')
            ->where(['w.id' => $ids])
            ->select($select);
        $lists = PageHelper::findAll($query, 100);
        //汇总
        $total = [
            'goods_num' => 0,
            'cart' => 0,
            'suttle_weight' => 0,
            'market_price' => 0,
            //'chuku_price' => 0,
        ];
        foreach ($lists as &$list) {
            if(empty($list['goods_id'])){
                exit("货号不能为空");
            }
            $main_stone_cart = $list['diamond_carat'] ?? 0;//主石重
            $main_stone_cart = bcmul($main_stone_cart, $list['main_stone_num'], 3);//主石总重=(主石重*主石粒数)
            $second_stone_cart1 = $list['second_stone_weight1'] ?? 0;//副石1重
            $second_stone_cart2 = $list['second_stone_weight2'] ?? 0;//副石2重
            $second_stone_cart3 = $list['second_stone_weight3'] ?? 0;//副石3重
            $cart = $main_stone_cart + $second_stone_cart1 + $second_stone_cart2 + $second_stone_cart3;//石重

            $list['suttle_weight'] = bcmul($list['suttle_weight'], $list['goods_num'], 3);//连石重
            $list['market_price'] = bcmul($list['market_price'], $list['goods_num'], 3);//标签价
            $list['cart'] = bcmul($cart, $list['goods_num'], 3);//总石重=石重*数量
            //汇总
            $total['goods_num'] = bcadd($total['goods_num'], $list['goods_num'], 3);//总货品数量
            $total['cart'] = bcadd($total['cart'], $list['cart'], 3);//总石重
            $total['suttle_weight'] = bcadd($total['suttle_weight'], $list['suttle_weight'], 3);//连石重
            $total['market_price'] = bcadd($total['market_price'], $list['market_price'], 3);//标签价
            //$total['chuku_price'] = bcadd($total['chuku_price'], $list['chuku_price'], 3);//销售价
        }
        return [$lists, $total];
    }
}

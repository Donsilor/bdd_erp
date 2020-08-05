<?php

namespace addons\Warehouse\backend\controllers;

use addons\Style\common\enums\LogTypeEnum;
use addons\Style\common\models\ProductType;
use addons\Style\common\models\StyleCate;
use addons\Supply\common\models\Supplier;
use addons\Warehouse\common\enums\DeliveryTypeEnum;
use addons\Warehouse\common\enums\PutInTypeEnum;
use addons\Warehouse\common\forms\WarehouseBillCForm;
use addons\Warehouse\common\models\Warehouse;
use addons\Warehouse\common\models\WarehouseBillGoods;
use addons\Warehouse\common\models\WarehouseGoods;
use common\helpers\ArrayHelper;
use common\helpers\PageHelper;
use common\helpers\StringHelper;
use Yii;
use common\traits\Curd;
use common\helpers\Url;
use common\helpers\SnHelper;
use common\helpers\ExcelHelper;
use common\models\base\SearchModel;
use addons\Warehouse\common\models\WarehouseBill;
use addons\Warehouse\common\forms\WarehouseBillBForm;
use addons\Warehouse\common\enums\BillStatusEnum;
use addons\Warehouse\common\enums\BillTypeEnum;
use common\enums\AuditStatusEnum;

/**
 * WarehouseBillBController implements the CRUD actions for WarehouseBillBController model.
 */
class BillCController extends BaseController
{
    use Curd;
    public $modelClass = WarehouseBillCForm::class;
    public $billType = BillTypeEnum::BILL_TYPE_C;

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
                'salesman' => ['username'],
            ]
        ]);

        $dataProvider = $searchModel
            ->search(\Yii::$app->request->queryParams,['created_at', 'audit_time']);

        $created_at = $searchModel->created_at;
        if (!empty($created_at)) {
            $dataProvider->query->andFilterWhere(['>=',Warehousebill::tableName().'.created_at', strtotime(explode('/', $created_at)[0])]);//起始时间
            $dataProvider->query->andFilterWhere(['<',Warehousebill::tableName().'.created_at', (strtotime(explode('/', $created_at)[1]) + 86400)] );//结束时间
        }

        $audit_time = $searchModel->audit_time;
        if (!empty($audit_time)) {
            $dataProvider->query->andFilterWhere(['>=',Warehousebill::tableName().'.audit_time', strtotime(explode('/', $audit_time)[0])]);//起始时间
            $dataProvider->query->andFilterWhere(['<',Warehousebill::tableName().'.audit_time', (strtotime(explode('/', $audit_time)[1]) + 86400)] );//结束时间
        }

        $dataProvider->query->andWhere(['>',Warehousebill::tableName().'.status', -1]);
        $dataProvider->query->andWhere(['=',Warehousebill::tableName().'.bill_type', $this->billType]);

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
     * ajax编辑/创建 其他出库单
     *
     * @return mixed|string|\yii\web\Response
     * @throws \yii\base\ExitException
     */
    public function actionAjaxEdit()
    {
        $id = \Yii::$app->request->get('id');
        $model = $this->findModel($id);
        $model = $model ?? new WarehouseBillCForm();

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
            if(in_array($model->delivery_type, [DeliveryTypeEnum::QUICK_SALE, DeliveryTypeEnum::PLATFORM])){
                if(!$model->channel_id){
                    return $this->message("渠道不能为空", $this->redirect(\Yii::$app->request->referrer), 'error');
                }
                if(!$model->order_sn){
                    return $this->message("订单号不能为空", $this->redirect(\Yii::$app->request->referrer), 'error');
                }
            }
            try{
                $trans = \Yii::$app->db->beginTransaction();
                if(false === $model->save()) {
                    throw new \Exception($this->getError($model));
                }

                if($isNewRecord){
                    $log_msg = "创建其他出库单{$model->bill_no}，出库类型：".DeliveryTypeEnum::getValue($model->delivery_type) ."，参考编号/订单号：{$model->order_sn} ";
                }else{
                    $log_msg = "修改其他出库单{$model->bill_no}，出库类型：".DeliveryTypeEnum::getValue($model->delivery_type) ."，参考编号/订单号：{$model->order_sn} ";
                }
                $log = [
                    'bill_id' => $model->id,
                    'log_type' => LogTypeEnum::ARTIFICIAL,
                    'log_module' => '其他出库单',
                    'log_msg' => $log_msg
                ];
                \Yii::$app->warehouseService->billLog->createBillLog($log);

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
        $model = $this->findModel($id) ?? new WarehouseBillCForm();
        return $this->render($this->action->id, [
            'model' => $model,
            'tab'=>$tab,
            'tabList'=>\Yii::$app->warehouseService->bill->menuTabList($id,$this->billType,$returnUrl),
            'returnUrl'=>$returnUrl,
        ]);
    }

    /**
     * ajax 其他出库单-申请审核
     *
     * @return mixed|string|\yii\web\Response
     * @throws \yii\base\ExitException
     */
    public function actionAjaxApply(){
        $id = \Yii::$app->request->get('id');
        $model = $this->findModel($id) ?? new WarehouseBillCForm();
        if($model->bill_status != BillStatusEnum::SAVE){
            return $this->message('单据不是保存状态', $this->redirect(\Yii::$app->request->referrer), 'error');
        }
        if($model->goods_num<=0){
            return $this->message('单据明细不能为空', $this->redirect(\Yii::$app->request->referrer), 'error');
        }

        $trans = \Yii::$app->db->beginTransaction();
        try{
            $model->bill_status = BillStatusEnum::PENDING;
            $model->audit_status = AuditStatusEnum::PENDING;
            if(false === $model->save()){
                return $this->message($this->getError($model), $this->redirect(\Yii::$app->request->referrer), 'error');
            }
            //日志
            $log = [
                'gold_id' => $model->id,
                'log_type' => LogTypeEnum::ARTIFICIAL,
                'log_module' => '其他出库单',
                'log_msg' => '单据提审'
            ];
            \Yii::$app->warehouseService->billLog->createBillLog($log);
            $trans->commit();
            return $this->message('操作成功', $this->redirect(\Yii::$app->request->referrer), 'success');

        }catch (\Exception $e){
            $trans->rollBack();
            return $this->message($e->getMessage(), $this->redirect(\Yii::$app->request->referrer), 'error');
        }
    }

    /**
     * ajax 其他出库单-审核
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

                \Yii::$app->warehouseService->billC->auditBillC($model);

                //日志
                $log = [
                    'gold_id' => $model->id,
                    'log_type' => LogTypeEnum::ARTIFICIAL,
                    'log_module' => '其他出库单',
                    'log_msg' => '单据审核'
                ];
                \Yii::$app->warehouseService->billLog->createBillLog($log);

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
     * 其他出库单-关闭
     *
     * @param $id
     * @return mixed
     */
    public function actionCancel($id)
    {
        $this->modelClass = WarehouseBill::class;
        if (!($model = $this->modelClass::findOne($id))) {
            return $this->message("找不到数据", $this->redirect(Yii::$app->request->referrer), 'error');
        }
        try{
            $trans = \Yii::$app->db->beginTransaction();

            \Yii::$app->warehouseService->billC->cancelBillC($model);

            //日志
            $log = [
                'gold_id' => $model->id,
                'log_type' => LogTypeEnum::ARTIFICIAL,
                'log_module' => '其他出库单',
                'log_msg' => '单据取消'
            ];
            \Yii::$app->warehouseService->billLog->createBillLog($log);
            $trans->commit();
            $this->message('操作成功', $this->redirect(Yii::$app->request->referrer), 'success');
        }catch (\Exception $e){
            $trans->rollBack();
            return $this->message($e->getMessage(), $this->redirect(\Yii::$app->request->referrer), 'error');
        }
    }

    /**
     * 其他出库单-删除
     *
     * @param $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->modelClass = WarehouseBill::class;
        if (!($model = $this->modelClass::findOne($id))) {
            return $this->message("找不到数据", $this->redirect(Yii::$app->request->referrer), 'error');
        }
        try{
            $trans = \Yii::$app->db->beginTransaction();

            \Yii::$app->warehouseService->billC->deleteBillC($model);
            $log = [
                'bill_id' => $model->id,
                'log_type' => LogTypeEnum::ARTIFICIAL,
                'log_module' => '其他出库单',
                'log_msg' => '单据删除'
            ];
            \Yii::$app->warehouseService->billLog->createBillLog($log);
            $trans->commit();
            $this->message('操作成功', $this->redirect(Yii::$app->request->referrer), 'success');
        }catch (\Exception $e){
            $trans->rollBack();
            return $this->message($e->getMessage(), $this->redirect(\Yii::$app->request->referrer), 'error');
        }
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
//            ['维修单号', 'bill_no', 'text'],
//            ['维修状态', 'bill_status', 'text'],
//            ['维修工厂', 'supplier_name', 'text'],
            ['货品名称', 'goods_name', 'text'],
            ['条码号', 'goods_id', 'text'],
            ['款号', 'style_sn', 'text'],
            ['产品分类', 'product_type_name' , 'text'],
            ['商品类型', 'style_cate_name' , 'text'],
            ['仓库', 'warehouse_name' , 'text'],
            ['材质', 'material' , 'text'],
            ['金重', 'gold_weight' , 'text'],
            ['主石类型', 'main_stone_type' , 'text'],
            ['主石重（ct)', 'diamond_carat' , 'text'],
            ['主石粒数', 'main_stone_num' , 'text'],
            ['主石规格', 'main_stone_info' , 'text'],
            ['副石重（ct）', 'second_stone_weight1' , 'text'],
            ['副石粒数', 'second_stone_num1' , 'text'],
            ['总重', 'gross_weight' , 'text'],
            ['手寸	', 'finger' , 'text'],
            ['尺寸	', 'product_size' , 'text'],
            ['证书号	', 'cert_id' , 'text'],
            ['工费	', 'gong_fee' , 'text'],
            ['成本价	', 'cost_price' , 'text'],
            ['备注	', 'remark' , 'text'],
        ];

        return ExcelHelper::exportData($list, $header, '退货返厂单_' . date('YmdHis',time()));

    }


    /**
     * 单据打印
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionPrint()
    {
        $this->layout = '@backend/views/layouts/print';
        $id = \Yii::$app->request->get('id');
        $model = $this->findModel($id);
        list($lists,$total) = $this->getData($id);
        return $this->render($this->action->id, [
            'model' => $model,
            'lists' => $lists,
            'total' =>$total
        ]);
    }

    /**
     *
     * @return bool
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function getData($ids)
    {
        $select = ['g.*','w.bill_no','w.bill_status','type.name as product_type_name','cate.name as style_cate_name',
            'warehouse.name as warehouse_name','sup.supplier_name'];
        $query = WarehouseBill::find()->alias('w')
            ->leftJoin(WarehouseBillGoods::tableName()." wg",'w.id=wg.bill_id')
            ->leftJoin(WarehouseGoods::tableName().' g','g.goods_id=wg.goods_id')
            ->leftJoin(ProductType::tableName().' type','type.id=g.product_type_id')
            ->leftJoin(StyleCate::tableName().' cate','cate.id=g.style_cate_id')
            ->leftJoin(Warehouse::tableName().' warehouse','warehouse.id=g.warehouse_id')
            ->leftJoin(Supplier::tableName().' sup','sup.id=w.supplier_id')
            ->where(['w.id' => $ids])
            ->select($select);
        $lists = PageHelper::findAll($query, 100);
        //统计
        $total = [
            'cost_price_count' => 0,
        ];
        foreach ($lists as &$list){
            $list['bill_status'] = BillStatusEnum::getValue($list['bill_status']);
            $list['material'] = \Yii::$app->attr->valueName($list['material']);
            $list['main_stone_type'] = \Yii::$app->attr->valueName($list['main_stone_type']);
            $diamond_color = $list['diamond_color'] ? \Yii::$app->attr->valueName($list['diamond_color']): '无';
            $diamond_clarity = $list['diamond_clarity'] ?\Yii::$app->attr->valueName($list['diamond_clarity']): '无';
            $diamond_cut = $list['diamond_cut'] ?\Yii::$app->attr->valueName($list['diamond_cut']): '无';
            $diamond_polish = $list['diamond_polish'] ?\Yii::$app->attr->valueName($list['diamond_polish']): '无';
            $diamond_symmetry = $list['diamond_symmetry'] ?\Yii::$app->attr->valueName($list['diamond_symmetry']): '无';
            $diamond_fluorescence = $list['diamond_fluorescence'] ?\Yii::$app->attr->valueName($list['diamond_fluorescence']): '无';
            $list['main_stone_info'] = $diamond_color . '/' . $diamond_clarity . '/' . $diamond_cut . '/'
                . $diamond_polish . '/' . $diamond_symmetry . '/' . $diamond_fluorescence;

            $total['cost_price_count'] += $list['cost_price'];

        }
        return [$lists,$total];



    }

}

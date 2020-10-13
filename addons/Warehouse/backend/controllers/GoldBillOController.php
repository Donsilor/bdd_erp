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
use addons\Warehouse\common\enums\StoneBillTypeEnum;
use addons\Warehouse\common\forms\WarehouseBillCForm;
use addons\Warehouse\common\forms\WarehouseGoldBillOForm;
use addons\Warehouse\common\models\Warehouse;
use addons\Warehouse\common\models\WarehouseBillGoods;
use addons\Warehouse\common\models\WarehouseGoldBill;
use addons\Warehouse\common\models\WarehouseGoldBillGoods;
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
            'tabList'=>\Yii::$app->warehouseService->goldBill->menuTabList($id,$this->billType,$returnUrl),
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
            return $this->message('金料重量不能为空', $this->redirect(\Yii::$app->request->referrer), 'error');
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
    /**
     * 单据打印
     * @return string
     * @throws
     */
    public function actionPrint()
    {
        $this->layout = '@backend/views/layouts/print';
        $id = Yii::$app->request->get('id');
        $model = $this->findModel($id);
        list($lists, $total) = $this->getData($id);
        return $this->render($this->action->id, [
            'model' => $model,
            'lists' => $lists,
            'total' => $total
        ]);
    }

    /**
     * 单据导出
     * @param null $ids
     * @return bool|mixed
     * @throws
     */
    public function actionExport($ids = null)
    {
        $name = '(金料)其他入库单明细';
        if (!is_array($ids)) {
            $ids = StringHelper::explodeIds($ids);
        }
        if (!$ids) {
            return $this->message('单据ID不为空', $this->redirect(['index']), 'warning');
        }
        list($list,) = $this->getData($ids);

        $header = [
            ['出库单号', 'bill_no', 'text'],
            ['工厂名称', 'supplier_name', 'text'],
            ['创建人', 'creator_name', 'text'],
            ['审核时间', 'audit_time', 'date','Y-m-d'],
            ['原料名称', 'gold_name', 'text'],
            ['编号', 'gold_sn', 'text'],
            ['材质', 'gold_type', 'text'],
            ['货品入库单号', 't_bill_no', 'text'],
            ['货品入库用金重(g)', 't_gold_weight', 'text'],
            ['出库金重(g)', 'gold_weight', 'text'],
            ['金价', 'gold_price', 'text'],
            ['总金额', 'cost_price', 'text'],
            ['备注', 'remark', 'text'],

        ];
        return ExcelHelper::exportData($list, $header, $name . '数据导出_' . date('YmdHis', time()));
    }

    private function getData($id)
    {
        $select = ['wg.*','wb.audit_time','sup.supplier_name as supplier_name','m.username as creator_name'];

        $query = WarehouseGoldBill::find()->alias('wb')
            ->leftJoin(WarehouseGoldBillGoods::tableName(). ' wg', 'wg.bill_id=wb.id')
            ->leftJoin(Supplier::tableName(). ' sup', 'sup.id=wb.supplier_id')
            ->leftJoin(Member::tableName(). ' m','m.id=wb.creator_id ' )
            ->where(['wb.id' => $id])->select($select);
        $lists = PageHelper::findAll($query, 100);
//        echo '<pre>';
//        print_r($bill);die;
        $total = [
            'cost_price' => 0,
            'gold_weight' => 0,
            't_gold_weight' => 0,
        ];
        foreach ($lists as &$list) {
            //金料类型
            $gold_type = empty($list['gold_type']) ? 0 : $list['gold_type'];
            $list['gold_type'] = Yii::$app->attr->valueName($gold_type);

            $t_gold = WarehouseGoldBillGoods::find()->where(['bill_type'=>GoldBillTypeEnum::GOLD_T, 'gold_sn'=>$list['gold_sn']])->one();
            $list['t_bill_no'] = $t_gold['bill_no'];
            $list['t_gold_weight'] = $t_gold['gold_weight'];

            //汇总
            $total['cost_price'] = bcadd($total['cost_price'], $list['cost_price'], 2);
            $total['gold_weight'] = bcadd($total['gold_weight'], $list['gold_weight'], 3);
            $total['t_gold_weight'] = bcadd($total['t_gold_weight'], $list['t_gold_weight'], 3);
        }
        return [$lists, $total];
    }
}

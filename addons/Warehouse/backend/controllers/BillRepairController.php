<?php

namespace addons\Warehouse\backend\controllers;


use addons\Style\common\models\ProductType;
use addons\Style\common\models\Style;
use addons\Style\common\models\StyleCate;
use addons\Supply\common\models\Supplier;
use addons\Warehouse\common\enums\RepairStatusEnum;
use addons\Warehouse\common\enums\RepairTypeEnum;
use addons\Warehouse\common\models\Warehouse;
use addons\Warehouse\common\models\WarehouseGoods;
use common\helpers\ExcelHelper;
use common\helpers\PageHelper;
use common\helpers\StringHelper;
use common\helpers\Url;
use Yii;
use common\models\base\SearchModel;
use addons\Warehouse\common\models\WarehouseBillRepair;
use addons\Warehouse\common\forms\WarehouseBillRepairForm;
use addons\Warehouse\common\enums\BillTypeEnum;
use common\enums\AuditStatusEnum;
use common\enums\StatusEnum;
use common\helpers\ResultHelper;
use common\helpers\SnHelper;
use yii\base\Exception;
use common\traits\Curd;
/**
* RepairBill
*
* Class WarehouseBillRepairController
* @package backend\modules\goods\controllers
*/
class BillRepairController extends BaseController
{
    use Curd;

    /**
    * @var WarehouseBillRepair
    */
    public $modelClass = WarehouseBillRepairForm::class;
    public $billType = BillTypeEnum::BILL_TYPE_WX;

    /**
    * 首页
    *
    * @return string
    * @throws \yii\web\NotFoundHttpException
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
                'follower' => ['username'],
            ]
        ]);
        $dataProvider = $searchModel
            ->search(Yii::$app->request->queryParams);

        $dataProvider->query->andWhere(['>',WarehouseBillRepairForm::tableName().'.status',-1]);


        //导出
        if(Yii::$app->request->get('action') === 'export'){
            $queryIds = $dataProvider->query->select(WarehouseBillRepairForm::tableName().'.id');
            $this->actionExport($queryIds);
        }

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * 编辑/创建
     *
     * @return mixed
     */
    public function actionEdit()
    {
        $id = Yii::$app->request->get('id');
        $returnUrl = Yii::$app->request->get('returnUrl',['index']);
        $model = $this->findModel($id);
        $model = $model ?? new WarehouseBillRepairForm();

        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {
            if(!$model->validate()) {
                return ResultHelper::json(422, $this->getError($model));
            }
            try{
                $trans = Yii::$app->db->beginTransaction();
                if($model->isNewRecord) {
                    $model->repair_no = SnHelper::createBillSn($this->billType);
                }

                \Yii::$app->warehouseService->repair->createRepairBill($model);

                $trans->commit();
            }catch (Exception $e){
                $trans->rollBack();
                $error = $e->getMessage();
                \Yii::error($error);
                return $this->message("保存失败:".$error, $this->redirect([$this->action->id,'id'=>$model->id]), 'error');
            }
            return $this->message("保存成功", $this->redirect($returnUrl), 'success');
        }
        return $this->render($this->action->id, [
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
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['bill-l/index']));
        $model = $this->findModel($id);
        if($model->repair_act){
            $repair_act_arr = explode(',', $model->repair_act);
            $repair_act_str = '';
            foreach ($repair_act_arr as $repair_act){
                $repair_act_str .= ','. Yii::$app->attr->valueName($repair_act);
            }
            $model->repair_act = trim($repair_act_str,',' );
        }
        return $this->render($this->action->id, [
            'model' => $model,
            'tab'=>$tab,
            'tabList'=>\Yii::$app->warehouseService->repair->menuTabList($id, $returnUrl),
        ]);
    }

    /**
     * ajax 维修订单-取消
     *
     * @return mixed
     */
    public function actionCancel()
    {
        $id = Yii::$app->request->get('id');
        $model = $this->findModel($id) ?? new WarehouseBillRepairForm();
        // ajax 校验
        $this->activeFormValidate($model);
        try{
            $trans = Yii::$app->trans->beginTransaction();

            \Yii::$app->warehouseService->repair->cancelRepair($model);

            $trans->commit();
        }catch (\Exception $e){
            $trans->rollBack();
            return $this->message("操作失败:". $e->getMessage(),  $this->redirect(Yii::$app->request->referrer), 'error');
        }
        return $this->message("操作成功", $this->redirect(Yii::$app->request->referrer), 'success');
    }

    /**
     * ajax 维修单-申请
     *
     * @return mixed|string|\yii\web\Response
     * @throws \yii\base\ExitException
     */
    public function actionAjaxApply(){
        $id = \Yii::$app->request->get('id');
        $model = $this->findModel($id);
        try{
            $trans = Yii::$app->trans->beginTransaction();

            \Yii::$app->warehouseService->repair->applyRepair($model);

            $trans->commit();
        }catch (\Exception $e){
            $trans->rollBack();
            return $this->message("申请失败:". $e->getMessage(),  $this->redirect(Yii::$app->request->referrer), 'error');
        }
        return $this->message("申请成功", $this->redirect(Yii::$app->request->referrer), 'success');

    }


    /**
     * 维修单-审核
     *
     * @return mixed
     */
    public function actionAjaxAudit()
    {
        $id = Yii::$app->request->get('id');
        $model = $this->findModel($id);

        if($model->audit_status == AuditStatusEnum::PENDING) {
            $model->audit_status = AuditStatusEnum::PASS;
        }

        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {
            try{
                $trans = Yii::$app->trans->beginTransaction();

                $model->audit_time = time();
                $model->auditor_id = \Yii::$app->user->identity->id;

                \Yii::$app->warehouseService->repair->auditRepair($model);

                $trans->commit();
            }catch (\Exception $e){
                $trans->rollBack();
                return $this->message("审核失败:". $e->getMessage(),  $this->redirect(Yii::$app->request->referrer), 'error');
            }
            return $this->message("保存成功", $this->redirect(Yii::$app->request->referrer), 'success');
        }

        return $this->renderAjax($this->action->id, [
            'model' => $model,
        ]);
    }

    /**
     * ajax 维修单-下单
     *
     * @return mixed|string|\yii\web\Response
     * @throws \yii\base\ExitException
     */
    public function actionAjaxOrders(){
        $id = \Yii::$app->request->get('id');
        $model = $this->findModel($id);
        try{
            $trans = Yii::$app->trans->beginTransaction();

            \Yii::$app->warehouseService->repair->ordersRepair($model);

            $trans->commit();
        }catch (\Exception $e){
            $trans->rollBack();
            return $this->message("下单失败:". $e->getMessage(),  $this->redirect(Yii::$app->request->referrer), 'error');
        }
        return $this->message("下单成功", $this->redirect(Yii::$app->request->referrer), 'success');

    }

    /**
     * ajax 维修单-完毕
     *
     * @return mixed|string|\yii\web\Response
     * @throws \yii\base\ExitException
     */
    public function actionAjaxFinish(){
        $id = \Yii::$app->request->get('id');
        $model = $this->findModel($id);
        try{
            $trans = Yii::$app->trans->beginTransaction();

            \Yii::$app->warehouseService->repair->finishRepair($model);

            $trans->commit();
        }catch (\Exception $e){
            $trans->rollBack();
            return $this->message("操作失败:". $e->getMessage(),  $this->redirect(Yii::$app->request->referrer), 'error');
        }
        return $this->message("操作成功", $this->redirect(Yii::$app->request->referrer), 'success');

    }

    /**
     * ajax 维修单-收货
     *
     * @return mixed|string|\yii\web\Response
     * @throws \yii\base\ExitException
     */
    public function actionAjaxReceiving(){
        $id = \Yii::$app->request->get('id');
        $model = $this->findModel($id);
        try{
            $trans = Yii::$app->trans->beginTransaction();

            \Yii::$app->warehouseService->repair->receivingRepair($model);

            $trans->commit();
        }catch (\Exception $e){
            $trans->rollBack();
            return $this->message("收货失败:". $e->getMessage(),  $this->redirect(Yii::$app->request->referrer), 'error');
        }
        return $this->message("收货成功", $this->redirect(Yii::$app->request->referrer), 'success');

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
//            ['维修单号', 'repair_no', 'text'],
//            ['维修状态', 'repair_status', 'text'],
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

        return ExcelHelper::exportData($list, $header, '维修单导出_' . date('YmdHis',time()));

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
        $select = ['g.*','wr.repair_no','wr.repair_status','type.name as product_type_name','cate.name as style_cate_name',
            'warehouse.name as warehouse_name','sup.supplier_name'];
        $query = WarehouseBillRepair::find()->alias('wr')
            ->leftJoin(WarehouseGoods::tableName() . ' g','wr.goods_id=g.goods_id')
            ->leftJoin(ProductType::tableName().' type','type.id=g.product_type_id')
            ->leftJoin(StyleCate::tableName().' cate','cate.id=g.style_cate_id')
            ->leftJoin(Warehouse::tableName().' warehouse','warehouse.id=g.warehouse_id')
            ->leftJoin(Supplier::tableName().' sup','sup.id=wr.supplier_id')
            ->select($select);
        $lists = PageHelper::findAll($query, 100);
        //统计
        $total = [
            'cost_price_count' => 0,
        ];
        foreach ($lists as &$list){
            $list['repair_status'] = RepairStatusEnum::getValue($list['repair_status']);
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

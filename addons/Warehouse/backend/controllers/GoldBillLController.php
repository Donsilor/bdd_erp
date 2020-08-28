<?php

namespace addons\Warehouse\backend\controllers;

use addons\Warehouse\common\models\WarehouseGoldBillGoods;
use common\helpers\PageHelper;
use setasign\Fpdi\PdfReader\Page;
use Yii;
use common\traits\Curd;
use common\models\base\SearchModel;
use addons\Warehouse\common\models\WarehouseGoldBill;
use addons\Warehouse\common\forms\WarehouseGoldBillLForm;
use addons\Warehouse\common\enums\GoldBillTypeEnum;
use addons\Warehouse\common\enums\BillStatusEnum;
use common\enums\AuditStatusEnum;
use common\helpers\Url;
use common\helpers\ExcelHelper;
use common\helpers\StringHelper;

/**
 * StyleChannelController implements the CRUD actions for StyleChannel model.
 */
class GoldBillLController extends GoldBillController
{
    use Curd;
    public $modelClass = WarehouseGoldBillLForm::class;
    public $billType = GoldBillTypeEnum::GOLD_L;

    /**
     * Lists all StyleChannel models.
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
            ->search(Yii::$app->request->queryParams,['created_at']);

        $created_at = $searchModel->created_at;
        if (!empty($created_at)) {
            $dataProvider->query->andFilterWhere(['>=',WarehouseGoldBill::tableName().'.created_at', strtotime(explode('/', $created_at)[0])]);//起始时间
            $dataProvider->query->andFilterWhere(['<',WarehouseGoldBill::tableName().'.created_at', (strtotime(explode('/', $created_at)[1]) + 86400)] );//结束时间
        }

        $dataProvider->query->andWhere(['>', WarehouseGoldBill::tableName().'.status', -1]);
        $dataProvider->query->andWhere(['=', WarehouseGoldBill::tableName().'.bill_type', $this->billType]);

        //导出
        if(\Yii::$app->request->get('action') === 'export'){
            $queryIds = $dataProvider->query->select(WarehouseGoldBill::tableName().'.id');
            $this->actionExport($queryIds);
        }

        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * 详情展示页
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView()
    {
        $bill_id = Yii::$app->request->get('id');
        $tab = Yii::$app->request->get('tab',1);
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['gold-bill-l/index', 'bill_id'=>$bill_id]));
        $model = $this->findModel($bill_id);
        $model = $model ?? new WarehouseGoldBill();
        return $this->render($this->action->id, [
            'model' => $model,
            'tab'=>$tab,
            'tabList'=>\Yii::$app->warehouseService->goldBill->menuTabList($bill_id, $this->billType, $returnUrl),
            'returnUrl'=>$returnUrl,
        ]);
    }

    /**
     * @return mixed
     * 提交审核
     */
    public function actionAjaxApply(){
        $id = \Yii::$app->request->get('id');
        $model = $this->findModel($id);
        $model = $model ?? new WarehouseGoldBill();
        if($model->bill_status != BillStatusEnum::SAVE){
            return $this->message('单据不是保存状态', $this->redirect(\Yii::$app->request->referrer), 'error');
        }
        $goods = WarehouseGoldBillGoods::findOne(['bill_id'=>$id]);
        if(!$goods){
            return $this->message('单据明细不能为空', $this->redirect(\Yii::$app->request->referrer), 'error');
        }
        $model->bill_status = BillStatusEnum::PENDING;
        $model->audit_status = AuditStatusEnum::PENDING;
        if(false === $model->save()){
            return $this->message($this->getError($model), $this->redirect(\Yii::$app->request->referrer), 'error');
        }
        return $this->message('操作成功', $this->redirect(\Yii::$app->request->referrer), 'success');

    }

    /**
     * ajax 收货单-审核
     *
     * @return mixed|string|\yii\web\Response
     * @throws \yii\base\ExitException
     */
    public function actionAjaxAudit()
    {
        $id = Yii::$app->request->get('id');
        $model = $this->findModel($id);
        $model = $model ?? new WarehouseGoldBillLForm();
        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {

            try{
                $trans = \Yii::$app->trans->beginTransaction();

                $model->audit_time = time();
                $model->auditor_id = \Yii::$app->user->identity->id;

                \Yii::$app->warehouseService->goldL->auditGoldL($model);

                $trans->commit();

                $this->message('操作成功', $this->redirect(Yii::$app->request->referrer), 'success');
            }catch(\Exception $e){
                $trans->rollBack();
                $this->message($e->getMessage(), $this->redirect(Yii::$app->request->referrer), 'error');
            }
        }
        $model->audit_status = AuditStatusEnum::PASS;
        return $this->renderAjax($this->action->id, [
            'model' => $model,
        ]);
    }

    /**
     * @param null $ids
     * @return bool|mixed
     * @throws
     */
    public function actionExport($ids = null){
        $name = '金料入库单明细';
        if(!is_array($ids)){
            $ids = StringHelper::explodeIds($ids);
        }
        if(!$ids){
            return $this->message('单据ID不为空', $this->redirect(['index']), 'warning');
        }
        list($list,) = $this->getData($ids);
        $header = [
            ['单据编号', 'bill_no', 'text'],
            ['金料类型', 'gold_type' , 'text'],
            ['名称', 'gold_name' , 'text'],
            ['款号', 'style_sn' , 'text'],
            ['重量(g)', 'gold_weight' , 'text'],
            ['金价/g	', 'gold_price' , 'text'],
            ['金料总额	', 'cost_price' , 'text'],
            ['含税总额	', 'incl_tax_price' , 'text'],
            ['备注', 'remark' , 'text'],
        ];
        return ExcelHelper::exportData($list, $header, $name.'数据导出_' . date('YmdHis',time()));
    }


    private function getData($ids){
        $select = ['wg.*','w.bill_no','w.to_warehouse_id','w.bill_status'];
        $query = WarehouseGoldBillLForm::find()->alias('w')
            ->leftJoin(WarehouseGoldBillGoods::tableName()." wg",'w.id=wg.bill_id')
            ->where(['w.id' => $ids])
            ->select($select);
        $lists = PageHelper::findAll($query, 100);
        //统计
        $total = [
            'total_weight' => 0,
            'total_cost_price' => 0,
            'total_tax_price' => 0,
        ];
        foreach ($lists as &$list){
            $list['gold_type'] = \Yii::$app->attr->valueName($list['gold_type']);

            $total['total_weight'] = bcadd($total['total_weight'], $list['gold_weight'], 3);
            $total['total_cost_price'] = bcadd($total['total_cost_price'], $list['cost_price'], 2);
            $total['total_tax_price'] = bcadd($total['total_tax_price'], $list['incl_tax_price'], 2);
        }
        return [$lists,$total];
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
        $model = $this->findModel($id);
        list($lists,$total) = $this->getData($id);
        return $this->render($this->action->id, [
            'model' => $model,
            'lists' => $lists,
            'total' => $total
        ]);
    }



}

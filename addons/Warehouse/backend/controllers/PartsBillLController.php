<?php

namespace addons\Warehouse\backend\controllers;

use Yii;
use common\traits\Curd;
use common\models\base\SearchModel;
use addons\Warehouse\common\models\WarehousePartsBill;
use addons\Warehouse\common\models\WarehousePartsBillGoods;
use addons\Warehouse\common\forms\WarehousePartsBillLForm;
use addons\Warehouse\common\enums\PartsBillStatusEnum;
use addons\Warehouse\common\enums\PartsBillTypeEnum;
use common\enums\AuditStatusEnum;
use common\helpers\StringHelper;
use common\helpers\ExcelHelper;
use common\helpers\PageHelper;
use common\helpers\Url;

/**
 * PartsBillLController implements the CRUD actions for StyleChannel model.
 */
class PartsBillLController extends PartsBillController
{
    use Curd;
    public $modelClass = WarehousePartsBillLForm::class;
    public $billType = PartsBillTypeEnum::PARTS_L;

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
            $dataProvider->query->andFilterWhere(['>=',WarehousePartsBill::tableName().'.created_at', strtotime(explode('/', $created_at)[0])]);//起始时间
            $dataProvider->query->andFilterWhere(['<',WarehousePartsBill::tableName().'.created_at', (strtotime(explode('/', $created_at)[1]) + 86400)] );//结束时间
        }

        $dataProvider->query->andWhere(['>', WarehousePartsBill::tableName().'.status', -1]);
        $dataProvider->query->andWhere(['=', WarehousePartsBill::tableName().'.bill_type', $this->billType]);

        //导出
        if(\Yii::$app->request->get('action') === 'export'){
            $queryIds = $dataProvider->query->select(WarehousePartsBill::tableName().'.id');
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
     * @throws
     */
    public function actionView()
    {
        $bill_id = Yii::$app->request->get('id');
        $tab = Yii::$app->request->get('tab',1);
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['parts-bill-l/index', 'bill_id'=>$bill_id]));
        $model = $this->findModel($bill_id);
        $model = $model ?? new WarehousePartsBill();
        return $this->render($this->action->id, [
            'model' => $model,
            'tab'=>$tab,
            'tabList'=>\Yii::$app->warehouseService->partsBill->menuTabList($bill_id, $this->billType, $returnUrl),
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
        $model = $model ?? new WarehousePartsBill();
        if($model->bill_status != PartsBillStatusEnum::SAVE){
            return $this->message('单据不是保存状态', $this->redirect(\Yii::$app->request->referrer), 'error');
        }
        $goods = WarehousePartsBillGoods::findOne(['bill_id'=>$id]);
        if(!$goods){
            return $this->message('单据明细不能为空', $this->redirect(\Yii::$app->request->referrer), 'error');
        }
        $model->bill_status = PartsBillStatusEnum::PENDING;
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
        $model = $model ?? new WarehousePartsBillLForm();
        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {

            try{
                $trans = \Yii::$app->trans->beginTransaction();

                $model->audit_time = time();
                $model->auditor_id = \Yii::$app->user->identity->id;

                \Yii::$app->warehouseService->partsL->auditPartsL($model);

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
        $name = '配件入库单明细';
        if(!is_array($ids)){
            $ids = StringHelper::explodeIds($ids);
        }
        if(!$ids){
            return $this->message('单据ID不为空', $this->redirect(['index']), 'warning');
        }
        list($list,) = $this->getData($ids);
        $header = [
            ['单据编号', 'bill_no', 'text'],
            ['配件类型', 'parts_type' , 'text'],
            ['名称', 'parts_name' , 'text'],
            ['款号', 'style_sn' , 'text'],
            ['配件材质', 'material_type' , 'text'],
            ['配件形状', 'shape' , 'text'],
            ['配件颜色', 'color' , 'text'],
            ['链类型', 'chain_type' , 'text'],
            ['扣环', 'cramp_ring' , 'text'],
            ['尺寸', 'size' , 'text'],
            ['重量(g)', 'parts_weight' , 'text'],
            ['价格	', 'parts_price' , 'text'],
            ['备注', 'remark' , 'text'],
        ];

        return ExcelHelper::exportData($list, $header, $name.'数据导出_' . date('YmdHis',time()));
    }

    private function getData($ids){
        $select = ['wg.*','w.bill_no','w.to_warehouse_id','w.bill_status'];
        $query = WarehousePartsBillLForm::find()->alias('w')
            ->leftJoin(WarehousePartsBillGoods::tableName()." wg",'w.id=wg.bill_id')
            ->where(['w.id' => $ids])
            ->select($select);
        $lists = PageHelper::findAll($query, 100);
        //统计
        $total = [

        ];
        foreach ($lists as &$list){
            $list['parts_type'] = \Yii::$app->attr->valueName($list['parts_type']);
            $list['material_type'] = \Yii::$app->attr->valueName($list['material_type']);
            $list['shape'] = \Yii::$app->attr->valueName($list['shape']);
            $list['color'] = \Yii::$app->attr->valueName($list['color']);
            $list['chain_type'] = \Yii::$app->attr->valueName($list['chain_type']);
            $list['cramp_ring'] = \Yii::$app->attr->valueName($list['cramp_ring']);
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

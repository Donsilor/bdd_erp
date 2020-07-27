<?php

namespace addons\Warehouse\backend\controllers;

use addons\Warehouse\common\models\WarehouseStoneBillGoods;
use common\helpers\PageHelper;
use Yii;
use common\traits\Curd;
use common\models\base\SearchModel;
use addons\Warehouse\common\forms\WarehouseStoneBillMsForm;
use addons\Warehouse\common\models\WarehouseStoneBill;
use addons\Warehouse\common\enums\BillStatusEnum;
use addons\Warehouse\common\enums\StoneBillTypeEnum;
use common\enums\AuditStatusEnum;
use common\helpers\Url;
use common\helpers\ExcelHelper;
use common\helpers\StringHelper;


/**
 * StyleChannelController implements the CRUD actions for StyleChannel model.
 */
class StoneBillMsController extends StoneBillController
{
    use Curd;
    public $modelClass = WarehouseStoneBillMsForm::class;
    public $billType = StoneBillTypeEnum::STONE_MS;

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
            ->search(Yii::$app->request->queryParams,['updated_at']);

        $updated_at = $searchModel->updated_at;
        if (!empty($updated_at)) {
            $dataProvider->query->andFilterWhere(['>=',WarehouseStoneBill::tableName().'.updated_at', strtotime(explode('/', $updated_at)[0])]);//起始时间
            $dataProvider->query->andFilterWhere(['<',WarehouseStoneBill::tableName().'.updated_at', (strtotime(explode('/', $updated_at)[1]) + 86400)] );//结束时间
        }

        $dataProvider->query->andWhere(['>', WarehouseStoneBill::tableName().'.status', -1]);
        $dataProvider->query->andWhere(['=', WarehouseStoneBill::tableName().'.bill_type', $this->billType]);

        //导出
        if(Yii::$app->request->get('action') === 'export'){
            $this->actionExport($dataProvider);
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
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['stone-bill-ms/index']));
        $model = $this->findModel($bill_id);
        $model = $model ?? new WarehouseStoneBill();
        return $this->render($this->action->id, [
            'model' => $model,
            'tab'=>$tab,
            'tabList'=>\Yii::$app->warehouseService->stoneBill->menuTabList($bill_id, $this->billType, $returnUrl),
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
        $model = $model ?? new WarehouseStoneBill();
        if($model->bill_status != BillStatusEnum::SAVE){
            return $this->message('单据不是保存状态', $this->redirect(\Yii::$app->request->referrer), 'error');
        }
        if($model->total_num<=0){
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
     * ajax 买石单-审核
     *
     * @return mixed|string|\yii\web\Response
     * @throws \yii\base\ExitException
     */
    public function actionAjaxAudit()
    {
        $id = Yii::$app->request->get('id');
        $model = $this->findModel($id) ?? new WarehouseStoneBillMsForm();
        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {

            try{
                $trans = \Yii::$app->trans->beginTransaction();

                $model->audit_time = time();
                $model->auditor_id = \Yii::$app->user->identity->getId();

                \Yii::$app->warehouseService->stoneMs->auditBillMs($model);

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
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function actionExport($ids = null){
        $name = '石料入库单明细';
        if(!is_array($ids)){
            $ids = StringHelper::explodeIds($ids);
        }
        if(!$ids){
            return $this->message('单据ID不为空', $this->redirect(['index']), 'warning');
        }
        list($list,) = $this->getData($ids);
        $header = [
            ['单据编号', 'bill_no' , 'text'],
            ['名称', 'stone_name' , 'text'],
            ['石类', 'stone_type' , 'text'],
            ['款号', 'style_sn' , 'text'],
            ['石头形状', 'shape' , 'text'],
            ['石头颜色', 'color' , 'text'],
            ['数量', 'stone_num' , 'text'],
            ['尺寸', 'stone_size' , 'text'],
            ['规格(颜色/净度/切工/石重)', 'spec' , 'text'],
            ['单价', 'stone_price' , 'text'],
            ['总价格', 'stone_sum_price' , 'text'],
            ['备注', 'remark' , 'text'],

        ];

        return ExcelHelper::exportData($list, $header, $name.'数据导出_' . date('YmdHis',time()));
    }


    private function getData($ids){
        $select = ['wg.*','w.bill_no','w.to_warehouse_id'];
        $query = WarehouseStoneBillMsForm::find()->alias('w')
            ->leftJoin(WarehouseStoneBillGoods::tableName()." wg",'w.id=wg.bill_id')
            ->where(['w.id' => $ids])
            ->select($select);
        $lists = PageHelper::findAll($query, 100);
        //统计
        $total = [
            'stone_num_count' => 0,
            'stone_sum_price_count' => 0,

        ];
        foreach ($lists as &$list){
            $list['stone_type'] = \Yii::$app->attr->valueName($list['stone_type']);
            $clarity = \Yii::$app->attr->valueName($list['clarity']);
            $cut = $list['carat'];
            $color = \Yii::$app->attr->valueName($list['color']);
            $list['color'] = $color;
            $list['shape'] = \Yii::$app->attr->valueName($list['shape']);
            $list['spec'] = $color.'/'.$clarity.'/'
                .$cut.'/'.$list['carat'];
            $list['stone_sum_price'] = $list['stone_price'] * $list['stone_weight'];
            $total['stone_num_count'] += $list['stone_num'];
            $total['stone_sum_price_count'] += $list['stone_sum_price'];
        }
        return [$lists,$total];
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
            'total' => $total
        ]);
    }

}

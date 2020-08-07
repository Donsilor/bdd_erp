<?php

namespace addons\Warehouse\backend\controllers;

use addons\Warehouse\common\forms\WarehouseStoneBillMsForm;
use addons\Warehouse\common\forms\WarehouseStoneBillSsForm;

use common\helpers\PageHelper;
use common\helpers\SnHelper;
use common\helpers\StringHelper;
use Yii;
use common\traits\Curd;
use common\models\base\SearchModel;
use addons\Warehouse\common\models\WarehouseStoneBill;
use addons\Warehouse\common\enums\StoneBillTypeEnum;
use common\enums\AuditStatusEnum;
use common\helpers\Url;
use common\helpers\ExcelHelper;
use addons\Warehouse\common\models\WarehouseStoneBillGoods;
use addons\Supply\common\models\ProduceStone;
use addons\Warehouse\common\enums\StoneBillStatusEnum;
use addons\Supply\common\enums\PeishiStatusEnum;


/**
 * StyleChannelController implements the CRUD actions for StyleChannel model.
 */
class StoneBillSsController extends StoneBillController
{
    use Curd;
    public $modelClass = WarehouseStoneBillSsForm::class;
    public $billType = StoneBillTypeEnum::STONE_SS;

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
        $dataProvider->query->andWhere(['>',WarehouseStoneBill::tableName().'.status',-1]);
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
     * ajax编辑/创建
     *
     * @return mixed|string|\yii\web\Response
     * @throws \yii\base\ExitException
     */
    public function actionAjaxEdit()
    {
        $id = \Yii::$app->request->get('id');
        $model = $this->findModel($id);
        $model = $model ?? new WarehouseStoneBill();

        if($model->isNewRecord){
            $model->bill_type = $this->billType;
        }
        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(\Yii::$app->request->post())) {
            if($model->isNewRecord){
                $model->bill_no = SnHelper::createBillSn($this->billType);
                $model->bill_status = StoneBillStatusEnum::SAVE;
            }
            try{
                $trans = \Yii::$app->db->beginTransaction();
                if(false === $model->save()) {
                    throw new \Exception($this->getError($model));
                }
                $trans->commit();
                return $this->message('保存成功',$this->redirect(Yii::$app->request->referrer),'success');
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
     * @throws NotFoundHttpException
     */
    public function actionView()
    {
        $bill_id = Yii::$app->request->get('id');
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['index']));
        $model = $this->findModel($bill_id);
        $model = $model ?? new WarehouseStoneBill();
        return $this->render($this->action->id, [
            'model' => $model,
            'tab'=>Yii::$app->request->get('tab',1),
            'tabList'=>\Yii::$app->warehouseService->stoneBill->menuTabList($bill_id, $this->billType, $returnUrl),
            'returnUrl'=>$returnUrl,
        ]);
    }

   /**
    *  提交审核
    * @throws \Exception
    * @return mixed|string
    */
    public function actionAjaxApply(){
        $id = \Yii::$app->request->get('id');
        $model = $this->findModel($id);
        $model = $model ?? new WarehouseStoneBill();
        if($model->bill_status != StoneBillStatusEnum::SAVE){
            return $this->message('单据不是保存状态', $this->redirect(\Yii::$app->request->referrer), 'error');
        }
        if($model->total_num <= 0){
            return $this->message('单据明细不能为空', $this->redirect(\Yii::$app->request->referrer), 'error');
        }
       
        try{
            $trans = Yii::$app->trans->beginTransaction();
            
            $model->bill_status  = StoneBillStatusEnum::PENDING;
            $model->audit_status = AuditStatusEnum::PENDING;            
            
            if(false === $model->save()){
                throw new \Exception($this->getError($model));
            }
            
            //更新配石状态
            $subIdQuery = WarehouseStoneBillGoods::find()->select(['source_detail_id'])->where(['bill_id'=>$id]);
            $produce_sns = ProduceStone::find()->where(['id'=>$subIdQuery])->distinct('produce_sn')->asArray()->all();
            if(!empty($produce_sns)) {
                $produce_sns = array_column($produce_sns, 'produce_sn');
                ProduceStone::updateAll(['peishi_status'=>PeishiStatusEnum::TO_LINGSHI],['id'=>$subIdQuery]);            
                Yii::$app->supplyService->produce->autoPeishiStatus($produce_sns);
            }else{
                throw new \Exception("数据异常");
            }
            
            $trans->commit();
            return $this->message('操作成功', $this->redirect(\Yii::$app->request->referrer), 'success');
        }catch(\Exception $e) {
            $trans->rollback();
            return $this->message($e->getMessage(), $this->redirect(\Yii::$app->request->referrer), 'error');
        }
    }

    /**
     * ajax 领石单-审核
     *
     * @return mixed|string|\yii\web\Response
     * @throws \yii\base\ExitException
     */
    public function actionAjaxAudit()
    {
        return $this->message("您没有操作权限", $this->redirect(Yii::$app->request->referrer), 'error');
        $id = Yii::$app->request->get('id');
        $model = $this->findModel($id);
        $model = $model ?? new WarehouseStoneBillSsForm();
        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {

            try{
                $trans = \Yii::$app->trans->beginTransaction();

                $model->audit_time = time();
                $model->auditor_id = \Yii::$app->user->identity->getId();

                \Yii::$app->warehouseService->stoneSs->auditBillSs($model);

                $trans->commit();

                return $this->message('操作成功', $this->redirect(Yii::$app->request->referrer), 'success');
            }catch(\Exception $e){
                $trans->rollBack();
                return $this->message($e->getMessage(), $this->redirect(Yii::$app->request->referrer), 'error');
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
        $name = '石料领石单明细';
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
        $select = ['wg.*','w.bill_no','w.to_warehouse_id', 'ps.produce_sn'];
        $query = WarehouseStoneBillSsForm::find()->alias('w')
            ->leftJoin(WarehouseStoneBillGoods::tableName()." wg",'w.id=wg.bill_id')
            ->leftJoin(ProduceStone::tableName()." ps", 'ps.id=wg.source_detail_id')
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
            $symmetry = \Yii::$app->attr->valueName($list['symmetry']);
            $polish = \Yii::$app->attr->valueName($list['polish']);
            $fluorescence = \Yii::$app->attr->valueName($list['fluorescence']);
            $list['stone_colour'] = \Yii::$app->attr->valueName($list['stone_colour']);
            $list['color'] = $color;
            $list['shape'] = \Yii::$app->attr->valueName($list['shape']);
            $list['spec'] = $color.'/'.$clarity.'/'.$cut.'/'.$symmetry.'/'.$polish.'/'.$fluorescence;
            $list['stone_sum_price'] = $list['stone_price'] * $list['stone_weight'];
            $total['stone_num_count'] += $list['stone_num'];
            $total['stone_weight_count'] += $list['stone_weight'];
            $total['stone_sum_price_count'] += $list['stone_sum_price'];
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

    public function getExport($dataProvider)
    {
        $list = $dataProvider->models;
        $header = [
            ['ID', 'id'],
            ['渠道名称', 'name', 'text'],
        ];
        return ExcelHelper::exportData($list, $header, '数据导出_' . time());

    }

}

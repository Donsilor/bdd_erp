<?php

namespace addons\Warehouse\backend\controllers;

use Yii;
use common\traits\Curd;
use common\enums\AuditStatusEnum;
use addons\Warehouse\common\enums\GoodsStatusEnum;
use addons\Warehouse\common\enums\GoldBillStatusEnum;
use addons\Warehouse\common\enums\PartsBillTypeEnum;
use addons\Warehouse\common\forms\WarehousePartsBillWForm;
use addons\Warehouse\common\models\WarehouseGoldBillGoods;
use addons\Warehouse\common\models\WarehousePartsBillGoods;
use addons\Warehouse\common\models\WarehousePartsBillGoodsW;
use addons\Warehouse\common\models\WarehousePartsBill;
use addons\Warehouse\common\models\WarehouseGoods;
use common\helpers\PageHelper;
use common\models\base\SearchModel;
use common\helpers\ExcelHelper;
use common\helpers\StringHelper;
use common\helpers\SnHelper;
use common\helpers\Url;

/**
 * PartsBillWController implements the CRUD actions for PartsBillWController model.
 */
class PartsBillWController extends BaseController
{
    use Curd;
    public $modelClass = WarehousePartsBillWForm::class;
    public $billType = PartsBillTypeEnum::PARTS_W;
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
                    'billW' => ['parts_type'],
                ]
        ]);
        $dataProvider = $searchModel
            ->search(Yii::$app->request->queryParams,['parts_type','updated_at','created_at']);

        $parts_type = $searchModel->parts_type;
        if (!empty($parts_type)) {
            $dataProvider->query->andWhere(['=', 'billW.parts_type', $parts_type]);
        }

        $created_at = $searchModel->created_at;
        if (!empty($created_at)) {
            $dataProvider->query->andFilterWhere(['>=',WarehousePartsBill::tableName().'.created_at', strtotime(explode('/', $created_at)[0])]);//起始时间
            $dataProvider->query->andFilterWhere(['<',WarehousePartsBill::tableName().'.created_at', (strtotime(explode('/', $created_at)[1]) + 86400)] );//结束时间
        }

        $dataProvider->query->andWhere(['=',WarehousePartsBill::tableName().'.bill_type',$this->billType]);
        $dataProvider->query->andWhere(['>',WarehousePartsBill::tableName().'.status',-1]);

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
     * ajax编辑/创建 盘点单
     *
     * @return mixed|string|\yii\web\Response
     * @throws \yii\base\ExitException
     */
    public function actionAjaxEdit()
    {
        $id = Yii::$app->request->get('id');
        $model = $this->findModel($id);
        $model = $model ?? new WarehousePartsBillWForm();
        $isNewRecord = $model->isNewRecord;
        if($isNewRecord){
            $model->bill_type = $this->billType;
        }else{
            $model->parts_type = false;
        }
        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {
            if($isNewRecord){
                $model->bill_no   = SnHelper::createBillSn($this->billType);
            }
            try{
                $trans = Yii::$app->trans->beginTransaction();               
                if($isNewRecord) {
                    $model = Yii::$app->warehouseService->partsW->createBillW($model);
                }else {
                    if(false === $model->save()) {
                        throw new \Exception($this->getError($model));
                    }
                }
                $trans->commit();

                if($isNewRecord) {
                    return $this->message("保存成功", $this->redirect(['view', 'id' => $model->id]), 'success');
                }else{
                    return $this->message('保存成功',$this->redirect(Yii::$app->request->referrer),'success');
                }
            }catch (\Exception $e) {   
                $trans->rollback();
                return $this->message($e->getMessage(), $this->redirect(Yii::$app->request->referrer), 'error');
            }
        }
        
        return $this->renderAjax($this->action->id, [
                'model' => $model,
        ]);
    }
    /**
     * ajax 盘点结束
     *
     * @return mixed|string|\yii\web\Response
     * @throws \yii\base\ExitException
     */
    public function actionAjaxFinish()
    {
        $id = Yii::$app->request->get('id');
        try{
            $trans = Yii::$app->trans->beginTransaction();
            
            \Yii::$app->warehouseService->partsW->finishBillW($id);
            
            $trans->commit();
            return $this->message('保存成功',$this->redirect(Yii::$app->request->referrer),'success');
            
        }catch (\Exception $e) {
            $trans->rollback();
            return $this->message($e->getMessage(), $this->redirect(Yii::$app->request->referrer), 'error');
        }

    }
    /**
     * ajax 盘点自动校正
     *
     * @return mixed|string|\yii\web\Response
     * @throws \yii\base\ExitException
     */
    public function actionAjaxAdjust()
    {
        $id = Yii::$app->request->get('id');
        try{
            $trans = Yii::$app->trans->beginTransaction();
            //\Yii::$app->warehouseService->billW->adjustBillW($id);
            \Yii::$app->warehouseService->partsW->billWSummary($id);
            $trans->commit();

            return $this->message('操作成功',$this->redirect(Yii::$app->request->referrer),'success');

        }catch (\Exception $e) {
            $trans->rollback();
            return $this->message($e->getMessage(), $this->redirect(Yii::$app->request->referrer), 'error');
        }
    }
    /**
     * 详情
     * @return mixed|string|\yii\web\Response
     * @throws \yii\base\ExitException
     * @return unknown
     */
    public function actionView()
    {
        $id = Yii::$app->request->get('id');
        $tab = Yii::$app->request->get('tab',1);
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['index']));
        
        $model = $this->findModel($id);
        return $this->render($this->action->id, [
                'model' => $model,
                'tab'=>$tab,
                'tabList'=>\Yii::$app->warehouseService->partsBill->menuTabList($id,$this->billType,$returnUrl),
                'returnUrl'=>$returnUrl,
        ]);
    }
    /**
     * 盘点
     * @return mixed
     * @throws
     */
    public function actionPandian()
    {
        $id = Yii::$app->request->get('id');
        $model = $this->findModel($id) ?? new WarehousePartsBillWForm();
        $model->parts_type = false;
        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {
            try{
                $trans = Yii::$app->trans->beginTransaction();
                
                Yii::$app->warehouseService->partsW->pandianGoods($model);
                
                $trans->commit();
                
                return $this->message("操作成功",$this->redirect(Yii::$app->request->referrer),'success');
            }catch(\Exception $e) {
                $trans->rollback();
                return $this->message($e->getMessage(),$this->redirect(Yii::$app->request->referrer),'error');
            }
        }
        return $this->render($this->action->id, [
                'model' => $model,
        ]);
    }
    /**
     * ajax 审核
     *
     * @return mixed|string|\yii\web\Response
     * @throws \yii\base\ExitException
     */
    public function actionAjaxAudit()
    {
        $id = Yii::$app->request->get('id');
        $model = $this->findModel($id) ?? new WarehousePartsBillWForm();
        $model->parts_type = false;
        //默认值
        /*if($model->audit_status == AuditStatusEnum::PENDING) {
            $model->audit_status = AuditStatusEnum::PASS;
        }*/
        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {
            
            try{
                $trans = \Yii::$app->trans->beginTransaction();
                
                $model->audit_time = time();
                $model->auditor_id = \Yii::$app->user->identity->id;
                
                \Yii::$app->warehouseService->partsW->auditBillW($model);
                
                $trans->commit();
                
                $this->message('操作成功', $this->redirect(Yii::$app->request->referrer), 'success');
            }catch(\Exception $e){
                $this->message($e->getMessage(), $this->redirect(Yii::$app->request->referrer), 'error');
            }            
        }
        $model->audit_status = AuditStatusEnum::PASS;
        return $this->renderAjax($this->action->id, [
                'model' => $model,
        ]);
    }


    /**
     * 关闭
     *
     * @param $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        if (!($model = $this->modelClass::findOne($id))) {
            return $this->message("找不到数据", $this->redirect(['index']), 'error');
        }

        try{
            $trans = \Yii::$app->db->beginTransaction();
            $model->bill_status = GoldBillStatusEnum::CANCEL;

            //仓库解锁
            \Yii::$app->warehouseService->warehouse->unlockWarehouse($model->to_warehouse_id);
            //更新库存状态
            $subQuery = WarehouseGoldBillGoods::find()->where(['bill_id' => $id])->select(['gold_sn']);
            WarehouseGoods::updateAll(['gold_status' => GoodsStatusEnum::IN_STOCK],['gold_sn'=>$subQuery,'gold_status'=>GoodsStatusEnum::IN_PANDIAN]);
            if(false === $model->save()){
                throw new \Exception($this->getError($model));
            }

            //日志
            /*$log = [
                'bill_id' => $model->id,
                'log_type' => LogTypeEnum::ARTIFICIAL,
                'log_module' => '盘点单',
                'log_msg' => '单据关闭'
            ];
            \Yii::$app->warehouseService->billLog->createBillLog($log);*/
            $trans->commit();
            return $this->message('关闭成功', $this->redirect(\Yii::$app->request->referrer), 'success');
        }catch (\Exception $e){
            $trans->rollBack();
            return $this->message($e->getMessage(), $this->redirect(\Yii::$app->request->referrer), 'error');
        }
    }


    /**
     * @param null $ids
     * @return bool|mixed
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function actionExport($ids = null){
        $name = '石料盘点单明细';
        if(!is_array($ids)){
            $ids = StringHelper::explodeIds($ids);
        }
        if(!$ids){
            return $this->message('单据ID不为空', $this->redirect(['index']), 'warning');
        }
        list($list,) = $this->getData($ids);
        $header = [
            ['石头类型', 'parts_type' , 'text'],
            ['名称', 'parts_name' , 'text'],
            ['石包号', 'parts_sn' , 'text'],
            ['款号', 'style_sn' , 'text'],
            ['色彩', 'color' , 'text'],
            ['形状', 'shape' , 'text'],
            ['重量（ct）', 'parts_weight' , 'text'],
            ['库存数量（个）', 'parts_num' , 'text'],
            ['单价/ct', 'parts_price' , 'text'],
            ['尺寸', 'parts_size' , 'text'],
            ['规格(颜色/净度/切工/石重)', 'spec' , 'text'],
            ['实盘(数量)', 'actual_num' , 'text'],
            ['实盘(重量)', 'actual_weight' , 'text'],
            ['差异(数量)', 'diff_num' , 'text'],
            ['差异(重量)', 'diff_weight' , 'text'],
            ['备注', 'remark' , 'text'],

        ];

        return ExcelHelper::exportData($list, $header, $name.'数据导出_' . date('YmdHis',time()));
    }


    private function getData($ids){
        $select = ['wg.*','w.bill_no','w.to_warehouse_id','wbg.actual_num','wbg.actual_weight'];
        $query = WarehousePartsBillWForm::find()->alias('w')
            ->leftJoin(WarehousePartsBillGoods::tableName()." wg",'w.id=wg.bill_id')
            ->leftJoin(WarehousePartsBillGoodsW::tableName().' wbg','wbg.id=wg.id')
            ->where(['w.id' => $ids])
            ->select($select);
        $lists = PageHelper::findAll($query, 100);
        //统计
        $total = [
            'parts_weight_count' => 0,
            'parts_num_count' => 0,
            'actual_weight_count' => 0,
            'actual_num_count' => 0,
        ];
        foreach ($lists as &$list){
            $list['parts_type'] = \Yii::$app->attr->valueName($list['parts_type']);
            $clarity = \Yii::$app->attr->valueName($list['clarity']);
            $cut = $list['carat'];
            $color = \Yii::$app->attr->valueName($list['color']);
            $list['color'] = $color;
            $list['shape'] = \Yii::$app->attr->valueName($list['shape']);
            $list['diff_weight'] = $list['parts_weight'] - $list['actual_weight'];
            $list['diff_num'] = $list['parts_num'] - $list['actual_num'];
            $list['spec'] = $color.'/'.$clarity.'/'
                .$cut.'/'.$list['carat'];

            $total['parts_weight_count'] += $list['parts_weight'];
            $total['parts_num_count'] += $list['parts_num'];
            $total['actual_weight_count'] += $list['actual_weight'];
            $total['actual_num_count'] += $list['actual_num'];
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

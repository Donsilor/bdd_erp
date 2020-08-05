<?php

namespace addons\Warehouse\backend\controllers;

use common\helpers\PageHelper;
use Yii;
use common\traits\Curd;
use common\models\base\SearchModel;
use common\helpers\ExcelHelper;
use common\helpers\ArrayHelper;
use common\helpers\StringHelper;
use common\helpers\SnHelper;
use common\helpers\Url;
use common\enums\AuditStatusEnum;

use addons\Style\common\enums\LogTypeEnum;
use addons\Style\common\models\ProductType;
use addons\Style\common\models\StyleCate;
use addons\Warehouse\common\enums\BillTypeEnum;
use addons\Warehouse\common\enums\BillStatusEnum;
use addons\Warehouse\common\enums\GoodsStatusEnum;
use addons\Warehouse\common\enums\PandianStatusEnum;
use addons\Warehouse\common\models\Warehouse;
use addons\Warehouse\common\models\WarehouseBillGoods;
use addons\Warehouse\common\models\WarehouseBillW;
use addons\Warehouse\common\models\WarehouseGoods;
use addons\Warehouse\common\models\WarehouseBill;
use addons\Warehouse\common\forms\WarehouseBillWForm;



/**
 * WarehouseBillController implements the CRUD actions for WarehouseBillController model.
 */
class BillWController extends BaseController
{
    use Curd;
    public $modelClass = WarehouseBillWForm::class;
    public $billType = BillTypeEnum::BILL_TYPE_W;
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
                        
                ]
        ]);
        
        $dataProvider = $searchModel
            ->search(Yii::$app->request->queryParams,['updated_at','created_at']);
        
        $created_at = $searchModel->created_at;
        if (!empty($updated_at)) {
            $dataProvider->query->andFilterWhere(['>=',Warehousebill::tableName().'.created_at', strtotime(explode('/', $created_at)[0])]);//起始时间
            $dataProvider->query->andFilterWhere(['<',Warehousebill::tableName().'.created_at', (strtotime(explode('/', $created_at)[1]) + 86400)] );//结束时间
        }

        $dataProvider->query->andWhere(['=',Warehousebill::tableName().'.bill_type',$this->billType]);
        $dataProvider->query->andWhere(['>',Warehousebill::tableName().'.status',-1]);

        //导出
        if(\Yii::$app->request->get('action') === 'export'){
            $queryIds = $dataProvider->query->select(Warehousebill::tableName().'.id');
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
        
        if($model->isNewRecord){
            $model->bill_type = $this->billType; 
        }
        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {
            $isNewRecord = $model->isNewRecord;
            if($isNewRecord){
                $model->bill_no   = SnHelper::createBillSn($this->billType);
            }
            try{
                $trans = Yii::$app->trans->beginTransaction();               
                if($isNewRecord) {
                    $model = Yii::$app->warehouseService->billW->createBillW($model);
                }else {
                    if(false === $model->save()) {
                        throw new \Exception($this->getError($model));
                    }
                }
                if($isNewRecord){
                    $log_msg = "创建盘点单{$model->bill_no}，盘点仓库：".$model->toWarehouse->name;
                }else{
                    $log_msg = "修改盘点单{$model->bill_no}，盘点仓库：".$model->toWarehouse->name;
                }
                $log = [
                    'bill_id' => $model->id,
                    'log_type' => LogTypeEnum::ARTIFICIAL,
                    'log_module' => '盘点单',
                    'log_msg' => $log_msg
                ];
                \Yii::$app->warehouseService->billLog->createBillLog($log);


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
            
            \Yii::$app->warehouseService->billW->finishBillW($id);
            //日志
            $log = [
                'bill_id' => $id,
                'log_type' => LogTypeEnum::ARTIFICIAL,
                'log_module' => '盘点单',
                'log_msg' => '盘点完成'
            ];
            \Yii::$app->warehouseService->billLog->createBillLog($log);
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
            \Yii::$app->warehouseService->billW->adjustBillW($id);
            \Yii::$app->warehouseService->billW->billWSummary($id);

            //日志
            $log = [
                'bill_id' => $id,
                'log_type' => LogTypeEnum::ARTIFICIAL,
                'log_module' => '盘点单',
                'log_msg' => '盘点单校正'
            ];
            \Yii::$app->warehouseService->billLog->createBillLog($log);
            $trans->commit();
            return $this->message('操作成功',$this->redirect(Yii::$app->request->referrer),'success');

        }catch (\Exception $e) {
            $trans->rollback();
            return $this->message($e->getMessage(), $this->redirect(Yii::$app->request->referrer), 'error');
        }
    }
    /**
     * 详情
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
                'tabList'=>\Yii::$app->warehouseService->bill->menuTabList($id,$this->billType,$returnUrl),
                'returnUrl'=>$returnUrl,
        ]);
    }
    /**
     * 盘点
     * @return mixed
     */
    public function actionPandian()
    {
        $id = Yii::$app->request->get('id');
        
        $model = $this->findModel($id) ?? new WarehouseBillWForm();      
        
        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {
            try{
                $trans = Yii::$app->trans->beginTransaction();
                
                Yii::$app->warehouseService->billW->pandianGoods($model);

                //日志
                $log = [
                    'bill_id' => $id,
                    'log_type' => LogTypeEnum::ARTIFICIAL,
                    'log_module' => '盘点单',
                    'log_msg' => '盘点单盘点'
                ];
                \Yii::$app->warehouseService->billLog->createBillLog($log);
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
        $model = $this->findModel($id);
        
        //默认值
        if($model->audit_status == AuditStatusEnum::PENDING) {
            $model->audit_status = AuditStatusEnum::PASS;
        }
        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {
            
            try{                
                $trans = \Yii::$app->trans->beginTransaction();
                
                $model->audit_time = time();
                $model->auditor_id = \Yii::$app->user->identity->id;
                
                \Yii::$app->warehouseService->billW->auditBillW($model);
                //日志
                $log = [
                    'bill_id' => $id,
                    'log_type' => LogTypeEnum::ARTIFICIAL,
                    'log_module' => '盘点单',
                    'log_msg' => '盘点单审核'
                ];
                \Yii::$app->warehouseService->billLog->createBillLog($log);
                $trans->commit();
                
                $this->message('操作成功', $this->redirect(Yii::$app->request->referrer), 'success');
            }catch(\Exception $e){
                $this->message($e->getMessage(), $this->redirect(Yii::$app->request->referrer), 'error');
            }            
        }
        
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
            $model->bill_status = BillStatusEnum::CANCEL;

            //仓库解锁
            \Yii::$app->warehouseService->warehouse->unlockWarehouse($model->to_warehouse_id);
            //更新库存状态
            $subQuery = WarehouseBillGoods::find()->where(['bill_id' => $id])->select(['goods_id']);
            WarehouseGoods::updateAll(['goods_status' => GoodsStatusEnum::IN_STOCK],['goods_id'=>$subQuery,'goods_status'=>GoodsStatusEnum::IN_PANDIAN]);
            if(false === $model->save()){
                throw new \Exception($this->getError($model));
            }

            //日志
            $log = [
                'bill_id' => $model->id,
                'log_type' => LogTypeEnum::ARTIFICIAL,
                'log_module' => '盘点单',
                'log_msg' => '单据关闭'
            ];
            \Yii::$app->warehouseService->billLog->createBillLog($log);
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
        $name = '盘点单明细';
        if(!is_array($ids)){
            $ids = StringHelper::explodeIds($ids);
        }
        if(!$ids){
            return $this->message('单据ID不为空', $this->redirect(['index']), 'warning');
        }
        list($list,) = $this->getData($ids);
        $header = [
//            ['单据编号', 'bill_no' , 'text'],
//            ['单据状态', 'bill_status' , 'selectd',BillStatusEnum::getMap()],
            ['货品名称', 'goods_name' , 'text'],
            ['条码号', 'goods_id' , 'text'],
            ['款号', 'style_sn' , 'text'],
            ['产品分类', 'product_type_name' , 'text'],
            ['商品类型', 'style_cate_name' , 'text'],
            ['仓库', 'warehouse_name' , 'text'],
            ['材质', 'material' , 'text', ],
            ['金重', 'gold_weight' , 'text'],
            ['深圳最低价格', 'poll_price' , 'text'],
            ['主石类型', 'main_stone_type' , 'text'],
            ['主石形状', 'diamond_shape' , 'text'],
            ['主石重（ct)', 'diamond_carat' , 'text'],
            ['配石重（ct)', 'second_stone_weight1' , 'text'],
            ['总重(g)', 'diamond_carat_sum' , 'text'],
            ['手寸	', 'finger' , 'text'],
            ['货品尺寸	', 'product_size' , 'text'],
            ['库存数	', 'goods_num' , 'text'],
            ['实盘数', 'actual_num' , 'text'],
            ['盘盈数', 'profit_num' , 'text'],
            ['盘亏数', 'loss_num' , 'text'],
            ['盘点类型', 'status' , 'text'],
            ['备注', 'goods_remark' , 'text'],

        ];

        return ExcelHelper::exportData($list, $header, $name.'数据导出_' . date('YmdHis',time()));
    }


    private function getData($ids){
        $select = ['g.*','w.bill_no','w.bill_type','w.bill_status','w.from_warehouse_id','wg.bill_id','wg.warehouse_id','wg.style_sn','wg.goods_name','wg.put_in_type'
            ,'wg.material','wg.gold_weight','wg.gold_loss','wg.diamond_carat','wg.diamond_color','wg.diamond_clarity',
            'wg.cost_price','wg.diamond_cert_id','wg.status','wg.goods_remark','type.name as product_type_name','cate.name as style_cate_name',
            'ww.actual_num','ww.profit_num','ww.loss_num'];
        $query = WarehouseBill::find()->alias('w')
            ->leftJoin(WarehouseBillGoods::tableName()." wg",'w.id=wg.bill_id')
            ->leftJoin(WarehouseGoods::tableName().' g','g.goods_id=wg.goods_id')
            ->leftJoin(WarehouseBillW::tableName()." ww",'ww.id=w.id')
            ->leftJoin(ProductType::tableName().' type','type.id=g.product_type_id')
            ->leftJoin(StyleCate::tableName().' cate','cate.id=g.style_cate_id')
            ->where(['w.id' => $ids])
            ->select($select);
        $lists = PageHelper::findAll($query, 100);
        //统计
        $total = [
            'goods_num_count' => 0,
        ];
        foreach ($lists as &$list){
            $bill = WarehouseBill::find()->where(['id'=>$list['bill_id']])->one();
            $list['warehouse_name'] = $bill->toWarehouse->name ?? '';
            $list['material'] = \Yii::$app->attr->valueName($list['material']);
            $list['main_stone_type'] = \Yii::$app->attr->valueName($list['main_stone_type']);
            $list['diamond_shape'] = \Yii::$app->attr->valueName($list['diamond_shape']);

            $list['poll_price'] = '';

            $diamond_carat = empty($list['diamond_carat']) ? 0 :$list['diamond_carat'];
            $second_stone_weight1 = empty($list['second_stone_weight1']) ? 0 :$list['second_stone_weight1'];
            $list['diamond_carat_sum'] =  $diamond_carat + $second_stone_weight1;

            $list['status'] = PandianStatusEnum::getValue($list['status']);

            $total['goods_num_count'] += $list['goods_num'];

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

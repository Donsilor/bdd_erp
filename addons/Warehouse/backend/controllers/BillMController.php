<?php

namespace addons\Warehouse\backend\controllers;

use addons\Style\common\enums\LogTypeEnum;
use addons\Style\common\models\ProductType;
use addons\Style\common\models\StyleCate;
use addons\Warehouse\common\enums\BillStatusEnum;
use addons\Warehouse\common\enums\BillTypeEnum;
use addons\Warehouse\common\enums\GoodsStatusEnum;
use addons\Warehouse\common\enums\PutInTypeEnum;
use addons\Warehouse\common\forms\WarehouseBillMForm;
use addons\Warehouse\common\models\WarehouseBill;
use addons\Warehouse\common\models\WarehouseBillGoods;
use addons\Warehouse\common\models\WarehouseGoods;
use common\enums\AuditStatusEnum;
use common\helpers\ArrayHelper;
use common\helpers\ExcelHelper;
use common\helpers\Html;
use common\helpers\PageHelper;
use common\helpers\SnHelper;
use common\helpers\StringHelper;
use common\helpers\Url;
use common\models\base\SearchModel;
use common\traits\Curd;
use yii\db\Exception;


/**
 * Attribute
 *
 * Class AttributeController
 * @package backend\modules\goods\controllers
 */
class BillMController extends BaseController
{
    use Curd;
    public $modelClass = WarehouseBillMForm::class;
    public $billType = BillTypeEnum::BILL_TYPE_M;

    /**
     * 调拨单
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
            'pageSize' => $this->getPageSize(),
            'relations' => [
                'creator' => ['username'],
                'auditor' => ['username'],

            ]
        ]);

        $dataProvider = $searchModel
            ->search(\Yii::$app->request->queryParams,['updated_at']);

        $dataProvider->key = 'id';

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

        $dataProvider->query->andWhere(['>',Warehousebill::tableName().'.status',-1]);
        $dataProvider->query->andWhere(['=',Warehousebill::tableName().'.bill_type','M']);

        //导出
        if(\Yii::$app->request->get('action') === 'export'){
            $queryIds = $dataProvider->query->select(Warehousebill::tableName().'.id');
            $this->actionExport($queryIds);
        }
        return $this->render('index', [
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
        $model = $model ?? new WarehouseBillMForm();
        $to_warehouse_id = $model->to_warehouse_id; //更改前的仓库

        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(\Yii::$app->request->post())) {
            try{
                $trans = \Yii::$app->db->beginTransaction();
                $isNewRecord = $model->isNewRecord;
                if($isNewRecord){
                    $model->bill_no = SnHelper::createBillSn($this->billType);
                    $model->bill_type = $this->billType;
                    $log_msg = "创建调拨单{$model->bill_no}，入库仓库为{$model->toWarehouse->name},出库仓库{$model->fromWarehouse->name}";
                }else{
                    $log_msg = "修改调拨单{$model->bill_no}，入库仓库为{$model->toWarehouse->name},出库仓库{$model->fromWarehouse->name}";
                }

                if(false === $model->save()){
                    throw new \Exception($this->getError($model));
                }

                if(!($isNewRecord) && $model->to_warehouse_id != $to_warehouse_id){
                    //编辑单据明细所有入库仓库
                    WarehouseBillGoods::updateAll(['to_warehouse_id' => $model->to_warehouse_id],['bill_id' => $model->id]);
                }

                $log = [
                    'bill_id' => $model->id,
                    'log_type' => LogTypeEnum::ARTIFICIAL,
                    'log_module' => '调拨单',
                    'log_msg' => $log_msg
                ];
                \Yii::$app->warehouseService->billLog->createBillLog($log);
                $trans->commit();

                if($isNewRecord) {
                    return $this->message("保存成功", $this->redirect(['view', 'id' => $model->id]), 'success');
                }else{
                    \Yii::$app->getSession()->setFlash('success','保存成功');
                    return $this->redirect(\Yii::$app->request->referrer);
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
     * @throws NotFoundHttpException
     */
    public function actionView()
    {
        $id = \Yii::$app->request->get('id');
        $tab = \Yii::$app->request->get('tab',1);
        $returnUrl = \Yii::$app->request->get('returnUrl',Url::to(['bill-m/index']));
        $model = $this->findModel($id);
        return $this->render($this->action->id, [
            'model' => $model,
            'tab'=>$tab,
            'tabList'=>\Yii::$app->warehouseService->bill->menuTabList($id,$this->billType, $returnUrl),
            'returnUrl'=>$returnUrl,
        ]);
    }

    /**
     * @return mixed
     * 申请审核
     */
    public function actionAjaxApply(){
        $id = \Yii::$app->request->get('id');
        $model = $this->findModel($id);
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
                'bill_id' => $model->id,
                'log_type' => LogTypeEnum::ARTIFICIAL,
                'log_module' => '调拨单',
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
     * ajax 审核
     *
     * @return mixed|string|\yii\web\Response
     * @throws \yii\base\ExitException
     */
    public function actionAjaxAudit()
    {
        $id = \Yii::$app->request->get('id');
        $model = $this->findModel($id);
        if($model->audit_status == AuditStatusEnum::PENDING) {
            $model->audit_status = AuditStatusEnum::PASS;
        }
        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(\Yii::$app->request->post())) {
            try{
                $trans = \Yii::$app->db->beginTransaction();
                $model->audit_time = time();
                $model->auditor_id = \Yii::$app->user->identity->id;
                if($model->audit_status == AuditStatusEnum::PASS){
                    $model->bill_status = BillStatusEnum::CONFIRM; //单据状态改成审核
                    //更新库存状态和仓库
                    $billGoods = WarehouseBillGoods::find()->where(['bill_id' => $id])->select(['goods_id'])->all();
                    foreach ($billGoods as $goods){
                        $res = WarehouseGoods::updateAll(['goods_status' => GoodsStatusEnum::IN_STOCK, 'warehouse_id' => $model->to_warehouse_id],['goods_id' => $goods->goods_id, 'goods_status' => GoodsStatusEnum::IN_TRANSFER]);
                        if(!$res){
                            throw new Exception("商品{$goods->goods_id}不是调拨中或者不存在，请查看原因");
                        }
                    }
                }else{
                    $model->bill_status = BillStatusEnum::SAVE;
                }
                if(false === $model->save()){
                    throw new \Exception($this->getError($model));
                }

                //日志
                $log = [
                    'bill_id' => $model->id,
                    'log_type' => LogTypeEnum::ARTIFICIAL,
                    'log_module' => '调拨单',
                    'log_msg' => '单据审核'
                ];
                \Yii::$app->warehouseService->billLog->createBillLog($log);
                \Yii::$app->getSession()->setFlash('success','保存成功');
                $trans->commit();
                return $this->redirect(\Yii::$app->request->referrer);
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
     * 删除/关闭
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
            //更新库存状态
            $billGoods = WarehouseBillGoods::find()->where(['bill_id' => $id])->select(['goods_id'])->all();
            foreach ($billGoods as $goods){
                $res = WarehouseGoods::updateAll(['goods_status' => GoodsStatusEnum::IN_STOCK],['goods_id' => $goods->goods_id, 'goods_status' => GoodsStatusEnum::IN_TRANSFER]);
                if(!$res){
                    throw new Exception("商品{$goods->goods_id}不是调拨中或者不存在，请查看原因");
                }
            }
            if(false === $model->save()){
                throw new \Exception($this->getError($model));
            }

            //日志
            $log = [
                'bill_id' => $model->id,
                'log_type' => LogTypeEnum::ARTIFICIAL,
                'log_module' => '调拨单',
                'log_msg' => '单据取消'
            ];
            \Yii::$app->warehouseService->billLog->createBillLog($log);
            \Yii::$app->getSession()->setFlash('success','关闭成功');
            $trans->commit();
            return $this->redirect(\Yii::$app->request->referrer);
        }catch (\Exception $e){
            $trans->rollBack();
            return $this->message($e->getMessage(), $this->redirect(\Yii::$app->request->referrer), 'error');
        }


        return $this->message("关闭失败", $this->redirect(['index']), 'error');
    }



    /**
     * @param null $ids
     * @return bool|mixed
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function actionExport($ids=null){
        $name = '调拨单明细';
        if(!is_object($ids)){
            $ids = StringHelper::explodeIds($ids);
        }
        if(!$ids){
            return $this->message('单据ID不为空', $this->redirect(['index']), 'warning');
        }

        list($list,) = $this->getData($ids);
        $header = [
//            ['单据编号', 'bill_no' , 'text'],
//            ['单据类型', 'bill_type' , 'text'],
//            ['单据状态', 'bill_status' , 'text'],
            ['货品名称', 'goods_name' , 'text'],
            ['条码号', 'goods_id' , 'text'],
            ['款号', 'style_sn' , 'text'],
            ['产品分类', 'product_type_name' , 'text'],
            ['商品类型', 'style_cate_name' , 'text'],
            ['出库仓库', 'from_warehouse_name' , 'text'],
            ['入库仓库', 'to_warehouse_name' , 'text'],
            ['材质', 'material' , 'text'],
            ['金重', 'gold_weight' , 'text'],
            ['成本价', 'cost_price' , 'text'],
            ['主石类型', 'main_stone_type' , 'text'],
            ['主石重（ct)', 'diamond_carat' , 'text'],
            ['主石粒数', 'main_stone_num' , 'text'],
            ['副石重（ct）', 'second_stone_weight1' , 'text'],
            ['副石粒数', 'second_stone_num1' , 'text'],
            ['总重', 'gross_weight' , 'text'],
            ['手寸	', 'finger' , 'text'],
            ['货品尺寸	', 'product_size' , 'text'],

        ];

        return ExcelHelper::exportData($list, $header, $name.'数据导出_' . date('YmdHis',time()));
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


    private function getData($ids){
        $select = ['g.*','w.bill_no','w.bill_type','w.bill_status','wg.id as wg_id','wg.from_warehouse_id','wg.to_warehouse_id','wg.style_sn','wg.goods_name','wg.put_in_type'
            ,'wg.material','wg.gold_weight','wg.gold_loss','wg.diamond_carat','wg.diamond_color','wg.diamond_clarity',
            'wg.cost_price','wg.diamond_cert_id','type.name as product_type_name','cate.name as style_cate_name'];
        $query = WarehouseBill::find()->alias('w')
            ->leftJoin(WarehouseBillGoods::tableName()." wg",'w.id=wg.bill_id')
            ->leftJoin(WarehouseGoods::tableName().' g','g.goods_id=wg.goods_id')
            ->leftJoin(ProductType::tableName().' type','type.id=g.product_type_id')
            ->leftJoin(StyleCate::tableName().' cate','cate.id=g.style_cate_id')
            ->where(['w.id' => $ids])
            ->select($select);
        $lists = PageHelper::findAll($query, 100);
        //统计
        $total = [
            'cost_price_count' => 0,
        ];
        foreach ($lists as &$list){
            $bill_goods = WarehouseBillGoods::find()->where(['id'=>$list['wg_id']])->one();
            $list['bill_type'] = BillTypeEnum::getValue($list['bill_type']);
            $list['bill_status'] = BillStatusEnum::getValue($list['bill_status']);
            $list['from_warehouse_name'] = $bill_goods->fromWarehouse->name ?? '';
            $list['to_warehouse_name'] = $bill_goods->toWarehouse->name ?? '';
            $list['material'] = \Yii::$app->attr->valueName($list['material']);
            $list['main_stone_type'] = \Yii::$app->attr->valueName($list['main_stone_type']);

            $total['cost_price_count'] += $list['cost_price'];

        }
        return [$lists,$total];
    }








}

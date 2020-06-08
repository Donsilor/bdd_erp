<?php

namespace addons\Warehouse\backend\controllers;

use addons\Warehouse\common\forms\WarehouseBillTForm;
use Yii;
use common\traits\Curd;
use common\models\base\SearchModel;
use common\helpers\ExcelHelper;
use addons\Warehouse\common\models\WarehouseGoods;
use addons\Warehouse\common\models\WarehouseBill;
use addons\Warehouse\common\models\WarehouseBillGoods;
use addons\Warehouse\common\enums\BillStatusEnum;
use addons\Warehouse\common\enums\GoodsStatusEnum;
use addons\Warehouse\common\enums\BillTypeEnum;
use addons\Purchase\common\enums\ReceiptGoodsStatusEnum;
use addons\Purchase\common\models\PurchaseReceiptGoods;
use addons\Style\common\enums\LogTypeEnum;
use addons\Style\common\models\ProductType;
use addons\Style\common\models\StyleCate;
use addons\Warehouse\common\enums\OrderTypeEnum;
use common\helpers\ArrayHelper;
use common\helpers\StringHelper;
use common\enums\AuditStatusEnum;
use common\helpers\SnHelper;
use common\helpers\Url;
use yii\db\Exception;


/**
 * WarehouseBillController implements the CRUD actions for WarehouseBillController model.
 */
class BillTController extends BaseController
{

    use Curd;
    public $modelClass  = WarehouseBillTForm::class;
    public $billType    = BillTypeEnum::BILL_TYPE_T;


    /**
     * Lists all StyleChannel models.
     * @return mixed
     */
    public function actionIndex()
    {

        $searchModel = new SearchModel([
            'model' => $this->modelClass,
            'scenario' => 'default',
            'partialMatchAttributes' => ['name'], // 模糊查询
            'defaultOrder' => [
                'id' => SORT_DESC
            ],
            'pageSize' => $this->pageSize,
            'relations' => [
                'creator' => ['username'],
                'auditor' => ['username'],

            ]
        ]);

        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams,['created_at', 'audit_time']);
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
        $dataProvider->query->andWhere(['=',Warehousebill::tableName().'.bill_type', $this->billType]);

        //导出
        if(\Yii::$app->request->get('action') === 'export'){
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
     * ajax编辑/创建
     *
     * @return mixed|string|\yii\web\Response
     * @throws \yii\base\ExitException
     */
    public function actionAjaxEdit()
    {
        $id = \Yii::$app->request->get('id');
        $model = $this->findModel($id);
        $model = $model ?? new WarehouseBill();
        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(\Yii::$app->request->post())) {
            try{
                $trans = \Yii::$app->db->beginTransaction();
                if($model->isNewRecord){
                    $model->bill_no = SnHelper::createBillSn($this->billType);
                    $model->bill_type = $this->billType;
                }
                if(false === $model->save()) {
                    throw new \Exception($this->getError($model));
                }
                $trans->commit();
                \Yii::$app->getSession()->setFlash('success','保存成功');
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
     * 详情展示页
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView()
    {
        $id = Yii::$app->request->get('id');
        $tab = Yii::$app->request->get('tab',1);
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['bill-t/index']));
        $model = $this->findModel($id);
        $model = $model ?? new WarehouseBill();
        return $this->render($this->action->id, [
            'model' => $model,
            'tab'=>$tab,
            'tabList'=>\Yii::$app->warehouseService->bill->menuTabList($id, $this->billType, $returnUrl),
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
        $model = $model ?? new WarehouseBill();
        if($model->bill_status != BillStatusEnum::SAVE){
            return $this->message('单据不是保存状态', $this->redirect(\Yii::$app->request->referrer), 'error');
        }
        $model->bill_status = BillStatusEnum::PENDING;
        if(false === $model->save()){
            return $this->message($this->getError($model), $this->redirect(\Yii::$app->request->referrer), 'error');
        }
        return $this->message('操作成功', $this->redirect(\Yii::$app->request->referrer), 'success');

    }

    /**
     * ajax收货单审核
     *
     * @return mixed|string|\yii\web\Response
     * @throws \yii\base\ExitException
     */
    public function actionAjaxAudit()
    {
        $id = Yii::$app->request->get('id');
        $model = $this->findModel($id);
        $model = $model ?? new WarehouseBill();
        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {
            try{
                $trans = Yii::$app->trans->beginTransaction();
                $model->audit_time = time();
                $model->auditor_id = Yii::$app->user->identity->getId();

                \Yii::$app->warehouseService->billL->auditBillL($model);
                $trans->commit();
                return $this->message("保存成功", $this->redirect(Yii::$app->request->referrer), 'success');
            }catch (\Exception $e){
                $trans->rollBack();
                return $this->message("审核失败:". $e->getMessage(),  $this->redirect(Yii::$app->request->referrer), 'error');
            }
        }
        $model->audit_status = AuditStatusEnum::PASS;
        return $this->renderAjax($this->action->id, [
            'model' => $model,
        ]);
    }

    /**
     * 删除/关闭/取消
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
            $billGoods = WarehouseBillGoods::find()->where(['bill_id' => $id])->select(['goods_id', 'source_detail_id'])->all();
            if(!$billGoods){
                throw new \Exception("单据明细为空");
            }
            foreach ($billGoods as $goods){
                $res = WarehouseGoods::deleteAll(['goods_id' => $goods->goods_id, 'goods_status' => GoodsStatusEnum::RECEIVING]);
                if(!$res){
                    throw new Exception("商品{$goods->goods_id}不是收货中或者不存在，请查看原因");
                }
            }
            if($model->order_type == OrderTypeEnum::ORDER_L){
                //同步采购收货单货品状态
                $ids = ArrayHelper::getColumn(ArrayHelper::toArray($billGoods), 'source_detail_id');
                $res = PurchaseReceiptGoods::updateAll(['goods_status'=>ReceiptGoodsStatusEnum::IQC_PASS], ['id'=>$ids]);
                if(false === $res) {
                    throw new \Exception("同步采购收货单货品状态失败");
                }
            }
            if(false === $model->save()){
                throw new \Exception($this->getError($model));
            }
            //日志
            $log = [
                'bill_id' => $model->id,
                'log_type' => LogTypeEnum::ARTIFICIAL,
                'log_module' => '收货单',
                'log_msg' => '取消单据'
            ];
            \Yii::$app->warehouseService->bill->createWarehouseBillLog($log);
            \Yii::$app->getSession()->setFlash('success','取消成功');
            $trans->commit();
            return $this->redirect(\Yii::$app->request->referrer);
        }catch (\Exception $e){
            $trans->rollBack();
            return $this->message($e->getMessage(), $this->redirect(\Yii::$app->request->referrer), 'error');
        }

        return $this->message("取消失败", $this->redirect(['index']), 'error');
    }

    /**
     * @param null $ids
     * @return bool|mixed
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function actionExport($ids=null){
        $name = '入库单明细';
        if(!is_array($ids)){
            $ids = StringHelper::explodeIds($ids);
        }
        if(!$ids){
            return $this->message('单据ID不为空', $this->redirect(['index']), 'warning');
        }

        $select = ['w.bill_no','w.bill_type','w.bill_status','g.goods_id','g.finger','g.gross_weight','g.main_stone_type','g.diamond_carat','g.main_stone_num',
            'g.second_stone_type1','g.second_stone_num1','g.second_stone_weight1','g.second_stone_price1','wg.warehouse_id','wg.style_sn','wg.goods_name','wg.goods_num','wg.put_in_type'
            ,'wg.material','wg.gold_weight','wg.gold_loss','wg.diamond_carat','wg.diamond_color','wg.diamond_clarity',
            'wg.cost_price','wg.diamond_cert_id','wg.goods_remark','type.name as product_type_name','cate.name as style_cate_name'];
        $list = WarehouseBill::find()->alias('w')
            ->leftJoin(WarehouseBillGoods::tableName()." wg",'w.id=wg.bill_id')
            ->leftJoin(WarehouseGoods::tableName().' g','g.goods_id=wg.goods_id')
            ->leftJoin(ProductType::tableName().' type','type.id=g.product_type_id')
            ->leftJoin(StyleCate::tableName().' cate','cate.id=g.style_cate_id')
            ->where(['w.id' => $ids])
            ->select($select)->asArray()->all();
        $header = [
            ['款号', 'style_sn' , 'text'],
            ['仓库','warehouse_id' , 'selectd',\Yii::$app->warehouseService->warehouse::getDropDownForAll()],
            ['商品类型', 'style_cate_name' , 'selectd',BillStatusEnum::getMap()],
            ['产品分类', 'product_type_name' , 'text'],
            ['成色', 'material' , 'function',function($model){
                return \Yii::$app->attr->valueName($model['material']);
            }],
            ['手寸', 'finger' , 'text'],
//            ['尺寸（规格）', 'finger' , 'text'],
            ['件数', 'goods_num' , 'text'],
//            ['货重', 'gross_weight' , 'text'],
            ['金重', 'gold_weight' , 'text'],
            ['损耗', 'gold_loss' ,  'text'],
            ['含耗重', 'gross_weight' , 'text'],
//            ['金价', '' , 'text'],
//            ['金料额', '' , 'text'],
            ['石号', 'main_stone_type' , 'function',function($model){
                return Yii::$app->attr->valueName($model->main_stone_type ?? '');
            }],
            ['粒数', 'main_stone_num' , 'text'],
            ['主石重', 'diamond_carat' , 'text'],
//            ['主石单价	', '' , 'text'],
            ['副石号', 'second_stone_type1' , 'function',function($model){
                return Yii::$app->attr->valueName($model->second_stone_type1 ?? '');
            }],
            ['副石粒数', 'second_stone_num1' , 'text'],
            ['副石重量', 'second_stone_weight1' , 'text'],
            ['副石单价', 'second_stone_price1' , 'text'],
//            ['加工费', 'second_stone_price1' , 'text'],
//            ['起版费', 'second_stone_price1' , 'text'],
//            ['镶工费', 'second_stone_price1' , 'text'],
//            ['喷拉砂', 'second_stone_price1' , 'text'],
//            ['分色分件', 'second_stone_price1' , 'text'],
//            ['总金额', 'second_stone_price1' , 'text'],
            ['备注', 'goods_remark' , 'text'],

        ];
        return ExcelHelper::exportData($list, $header, $name.'数据导出_' . date('YmdHis',time()));
    }

}

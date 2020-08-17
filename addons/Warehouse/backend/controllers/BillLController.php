<?php

namespace addons\Warehouse\backend\controllers;

use addons\Warehouse\common\enums\PutInTypeEnum;
use addons\Warehouse\common\models\WarehouseBillGoodsL;
use common\helpers\PageHelper;
use Yii;
use common\traits\Curd;
use common\models\base\SearchModel;
use common\helpers\ExcelHelper;
use addons\Warehouse\common\models\WarehouseGoods;
use addons\Warehouse\common\models\WarehouseBill;
use addons\Warehouse\common\models\WarehouseBillGoods;
use addons\Warehouse\common\forms\WarehouseBillLForm;
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
use common\enums\StatusEnum;
use common\helpers\SnHelper;
use common\helpers\Url;
use yii\db\Exception;


/**
 * WarehouseBillController implements the CRUD actions for WarehouseBillController model.
 */
class BillLController extends BaseController
{

    use Curd;
    public $modelClass  = WarehouseBillLForm::class;
    public $billType    = BillTypeEnum::BILL_TYPE_L;


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
            $queryIds = $dataProvider->query->select(Warehousebill::tableName().'.id');
            $this->actionExport($queryIds);
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
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['bill-l/index', 'id'=>$id]));
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
        if($model->goods_num<=0){
            return $this->message('单据明细不能为空', $this->redirect(\Yii::$app->request->referrer), 'error');
        }

        $trans = \Yii::$app->db->beginTransaction();
        try{
            $model->bill_status = BillStatusEnum::PENDING;
            if(false === $model->save()){
                return $this->message($this->getError($model), $this->redirect(\Yii::$app->request->referrer), 'error');
            }
            \Yii::$app->warehouseService->billT->syncUpdatePriceAll($model);
            //日志
            $log = [
                'bill_id' => $model->id,
                'log_type' => LogTypeEnum::ARTIFICIAL,
                'log_module' => '入库单',
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
                //日志
                $log = [
                    'bill_id' => $model->id,
                    'log_type' => LogTypeEnum::ARTIFICIAL,
                    'log_module' => '入库单',
                    'log_msg' => '单据审核'
                ];
                \Yii::$app->warehouseService->billLog->createBillLog($log);
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
     * 取消
     *
     * @param $id
     * @return mixed
     */
    public function actionCancel($id)
    {
        if (!($model = $this->modelClass::findOne($id))) {
            return $this->message("找不到数据", $this->redirect(['index']), 'error');
        }
        try{
            $trans = \Yii::$app->db->beginTransaction();
            $model->bill_status = BillStatusEnum::CANCEL;
            $billGoods = WarehouseBillGoodsL::find()->where(['bill_id' => $id])->select(['goods_id', 'source_detail_id'])->all();
            if(!$billGoods){
                throw new \Exception("单据明细为空");
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
            \Yii::$app->warehouseService->billLog->createBillLog($log);
            \Yii::$app->getSession()->setFlash('success','取消成功');
            $trans->commit();
            return $this->redirect(\Yii::$app->request->referrer);
        }catch (\Exception $e){
            $trans->rollBack();
            return $this->message($e->getMessage(), $this->redirect(\Yii::$app->request->referrer), 'error');
        }

    }

    /**
     * 删除
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
            $billGoods = WarehouseBillGoodsL::find()->where(['bill_id' => $id])->select(['goods_id', 'source_detail_id'])->all();
            if(!$billGoods){
                throw new \Exception("单据明细为空");
            }
            if($model->order_type == OrderTypeEnum::ORDER_L){
                //同步采购收货单货品状态
                $ids = ArrayHelper::getColumn(ArrayHelper::toArray($billGoods), 'source_detail_id');
                $res = PurchaseReceiptGoods::updateAll(['goods_status'=>ReceiptGoodsStatusEnum::IQC_PASS], ['id'=>$ids]);
                if(false === $res) {
                    throw new \Exception("同步采购收货单货品状态失败");
                }
            }
            $res = WarehouseBillGoodsL::deleteAll(['bill_id' => $id]);
            if(false === $res){
                throw new \Exception("删除明细失败");
            }
            $res = WarehouseBillGoods::deleteAll(['bill_id' => $id]);
            if(false === $res){
                throw new \Exception("删除明细失败2");
            }
            if(false === $model->save()){
                throw new \Exception($this->getError($model));
            }
            //日志
            $log = [
                'bill_id' => $model->id,
                'log_type' => LogTypeEnum::ARTIFICIAL,
                'log_module' => '收货单',
                'log_msg' => '删除单据'
            ];
            \Yii::$app->warehouseService->billLog->createBillLog($log);
            \Yii::$app->getSession()->setFlash('success','删除成功');
            $trans->commit();
            return $this->redirect(\Yii::$app->request->referrer);
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
    public function actionExport($ids=null){
        $name = '入库单明细';
        if(!is_array($ids)){
            $ids = StringHelper::explodeIds($ids);
        }
        if(!$ids){
            return $this->message('单据ID不为空', $this->redirect(['index']), 'warning');
        }

        list($list,) = $this->getData($ids);
        $header = [
                ['条码号', 'goods_id' , 'text'],
                ['款号', 'style_sn' , 'text'],
                ['货品名称', 'goods_name' , 'text'],
                ['产品分类', 'style_cate_name' , 'text'],
                ['产品线', 'product_type_name' , 'text'],
                ['材质', 'material' , 'text'],
                ['成色', 'goods_color' ,  'text'],
                ['件数', 'goods_num' , 'text'],
                ['指圈', 'finger' , 'text'],
                ['尺寸', 'product_size' , 'text'],
                ['货重', 'gold_weight' , 'text'],
                ['净重', 'suttle_weight' , 'text'],
                ['损耗', 'gold_loss' , 'text'],
                ['含耗重', 'gold_weight_sum' , 'text'],
                ['金价', 'gold_price' , 'text'],
                ['金料额', 'gold_amount' , 'text'],
                ['石号', 'main_stone_sn' , 'text'],
                ['粒数', 'main_stone_num' , 'text'],
                ['石重', 'diamond_carat' , 'text'],
                ['颜色', 'main_stone_color' ,'text'],
                ['净度', 'main_stone_clarity' , 'text'],
                ['单价', 'main_stone_price' , 'text'],
                ['金额', 'main_stone_price_sum','text'],
                ['副石号', 'second_stone_sn1' , 'text'],
                ['副石粒数', 'second_stone_num1' , 'text'],
                ['副石石重', 'second_stone_weight1' , 'text'],
                ['副石颜色', 'second_stone_color1' , 'text'],
                ['副石净度', 'second_stone_clarity1' , 'text'],
                ['副石单价', 'second_stone_price1' , 'text'],
                ['副石金额', 'second_stone_price1_sum' , 'text'],
                ['配件(g)', 'parts_gold_weight' , 'text'],
                ['配件额', 'parts_price' , 'text'],
                ['配件工费', 'parts_fee' , 'text'],
                ['工费', 'gong_fee' , 'text'],
                ['镶石费', 'xianqian_fee' , 'text'],
                ['工艺费', 'biaomiangongyi_fee' , 'text'],
                ['分色/分件费', 'fense_fee' , 'text'],
                ['证书费', 'cert_fee' , 'text'],
                ['补口费', 'bukou_fee' , 'text'],
                ['单价', 'price' , 'text'],
                ['总额', 'price_sum' , 'text'],
                ['证书费', 'cert_fee' , 'text'],
                ['备注', 'goods_remark' , 'text'],
                ['倍率', 'markup_rate' , 'text'],
                ['标签价', 'market_price' , 'text'],

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
        $id = Yii::$app->request->get('id');
        $model = $this->findModel($id);
        list($lists,$total) = $this->getData($id);
        return $this->render($this->action->id, [
            'model' => $model,
            'lists' => $lists,
            'total' => $total
        ]);
    }

    private function getData($ids){
        $select = ['g.*','w.bill_no','w.bill_type','w.bill_status','wg.warehouse_id','wg.style_sn','wg.goods_name','wg.goods_num','wg.put_in_type'
            ,'wg.material','wg.gold_weight','wg.gold_loss','wg.diamond_carat','wg.diamond_color','wg.diamond_clarity',
            'wg.cost_price','wg.diamond_cert_id','wg.goods_remark','type.name as product_type_name','cate.name as style_cate_name'];
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
            'goods_num_count' => 0,
            'gold_weight_count' => 0,
            'suttle_weight_count' => 0,
            'gold_amount_count' => 0,
            'main_stone_weight_count' => 0,
            'main_stone_price_sum_count' => 0,
            'second_stone_weight1_count' => 0,
            'second_stone_price1_sum_count' => 0,
            'price_count' => 0,
            'price_sum_count' => 0,
            'cert_fee_count' => 0,

        ];
        foreach ($lists as &$list){
            //成色
            $material = empty($list['material']) ? 0 : $list['material'];
            $list['material'] = Yii::$app->attr->valueName($material);
            //单据状态
            $list['bill_status'] = BillStatusEnum::getValue($list['bill_status']);
            //入库方式
            $list['put_in_type'] = PutInTypeEnum::getValue($list['put_in_type']);
            //主石颜色
            $main_stone_color = empty($list['main_stone_color']) ? 0 : $list['main_stone_color'];
            $list['main_stone_color'] = Yii::$app->attr->valueName($main_stone_color);
            //主石净度
            $main_stone_clarity = empty($list['main_stone_clarity']) ? 0 : $list['main_stone_clarity'];
            $list['main_stone_clarity'] = Yii::$app->attr->valueName($main_stone_clarity);
            //主石金额
            $main_stone_price = empty($list['main_stone_price']) ? 0 : $list['main_stone_price'];
            $list['main_stone_price_sum'] = $main_stone_price * $list['main_stone_num'];
            //副石颜色
            $second_stone_color1 = empty($list['second_stone_color1']) ? 0 : $list['second_stone_color1'];
            $list['second_stone_color1'] = Yii::$app->attr->valueName($second_stone_color1);
            //副石净度
            $second_stone_clarity1 = empty($list['second_stone_clarity1']) ? 0 : $list['second_stone_clarity1'];
            $list['second_stone_clarity1'] = Yii::$app->attr->valueName($second_stone_clarity1);
            //副石金额
            $second_stone_price1 = empty($list['second_stone_price1']) ? 0 : $list['second_stone_price1'];
            $list['second_stone_price1_sum'] = $second_stone_price1 * $list['second_stone_num1'];
            //单价
            $list['price'] = $list['cost_price'] + $list['main_stone_price_sum'] + $list['gong_fee']
                + $list['bukou_fee'] + $list['biaomiangongyi_fee'];
            //总额
            $list['price_sum'] = $list['price'] * $list['goods_num'];
            //含耗重
            $gold_loss = empty($list['gold_loss']) ? 0 : $list['gold_loss'];
            $suttle_weight = empty($list['suttle_weight']) ? 0 : $list['suttle_weight'];
            $list['gold_weight_sum'] = $suttle_weight + $gold_loss;

            //统计
            $total['goods_num_count'] += $list['goods_num'];  //件数
            $total['gold_weight_count'] += $list['gold_weight']; //货重
            $total['suttle_weight_count'] += $list['suttle_weight']; //净重
            $total['gold_amount_count'] += $list['gold_amount']; //金料额
            $total['main_stone_weight_count'] += $list['diamond_carat']; //石重
            $total['main_stone_price_sum_count'] += $list['main_stone_price_sum']; //主石金额
            $total['second_stone_weight1_count'] += $list['second_stone_weight1']; //副石石重
            $total['second_stone_price1_sum_count'] += $list['second_stone_price1_sum']; //副石金额
            $total['price_count'] += $list['price']; //单价
            $total['price_sum_count'] += $list['price_sum']; //总额
            $total['cert_fee_count'] += $list['price_sum']; //证书费

        }
        return [$lists,$total];
    }

}

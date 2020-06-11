<?php

namespace addons\Purchase\backend\controllers;

use addons\Purchase\common\enums\PurchaseStatusEnum;
use addons\Purchase\common\enums\ReceiptGoodsStatusEnum;
use addons\Style\common\models\StyleChannel;
use addons\Supply\common\models\Supplier;
use addons\Warehouse\common\enums\PutInTypeEnum;
use addons\Warehouse\common\enums\RepairStatusEnum;
use common\models\backend\Member;
use Yii;
use common\models\base\SearchModel;
use addons\Purchase\common\models\PurchaseReceipt;
use addons\Purchase\common\forms\PurchaseReceiptForm;
use addons\Purchase\common\models\PurchaseReceiptGoods;
use addons\Warehouse\common\enums\BillStatusEnum;
use addons\Purchase\common\enums\PurchaseTypeEnum;
use addons\Style\common\models\ProductType;
use addons\Style\common\models\StyleCate;
use common\enums\AuditStatusEnum;
use common\enums\WhetherEnum;
use common\helpers\ArrayHelper;
use common\helpers\ExcelHelper;
use common\helpers\StringHelper;
use common\helpers\Url;
use common\traits\Curd;
/**
* Receipt
*
* Class ReceiptController
* @package addons\Purchase\Backend\controllers
*/
class ReceiptController extends BaseController
{
    use Curd;

    /**
    * @var Receipt
    */
    public $modelClass = PurchaseReceiptForm::class;
    public $purchaseType = PurchaseTypeEnum::GOODS;

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
            ]
        ]);

        $dataProvider = $searchModel
            ->search(Yii::$app->request->queryParams, ['created_at', 'audit_time']);

        $created_at = $searchModel->created_at;
        if (!empty($created_at)) {
            $dataProvider->query->andFilterWhere(['>=',PurchaseReceipt::tableName().'.created_at', strtotime(explode('/', $created_at)[0])]);//起始时间
            $dataProvider->query->andFilterWhere(['<',PurchaseReceipt::tableName().'.created_at', (strtotime(explode('/', $created_at)[1]) + 86400)] );//结束时间
        }
        $audit_time = $searchModel->audit_time;
        if (!empty($audit_time)) {
            $dataProvider->query->andFilterWhere(['>=',PurchaseReceipt::tableName().'.audit_time', strtotime(explode('/', $audit_time)[0])]);//起始时间
            $dataProvider->query->andFilterWhere(['<',PurchaseReceipt::tableName().'.audit_time', (strtotime(explode('/', $audit_time)[1]) + 86400)] );//结束时间
        }

        $dataProvider->query->andWhere(['>',PurchaseReceipt::tableName().'.status',-1]);
        $dataProvider->query->andWhere(['=',PurchaseReceipt::tableName().'.purchase_type', $this->purchaseType]);

        //导出
        if(Yii::$app->request->get('action') === 'export'){
            $queryIds = $dataProvider->query->select(PurchaseReceipt::tableName().'.id');
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
        $id = Yii::$app->request->get('id');
        $model = $this->findModel($id);
        $model = $model ?? new PurchaseReceipt();
        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {
            $model->creator_id  = \Yii::$app->user->identity->id;
            $isNewRecord = $model->isNewRecord;
            if(false === $model->save()){
                throw new \Exception($this->getError($model));
            }
            if($isNewRecord) {
                return $this->message("保存成功", $this->redirect(['view', 'id' => $model->id]), 'success');
            }else{
                $this->message($this->getError($model), $this->redirect(['index']), 'error');
            }
        }

        return $this->renderAjax($this->action->id, [
            'model' => $model,
        ]);
    }

    /**
     * @return mixed
     * 申请审核
     */
    public function actionAjaxApply(){
        $id = \Yii::$app->request->get('id');
        $model = $this->findModel($id);
        $model = $model ?? new PurchaseReceipt();
        if($model->receipt_status != BillStatusEnum::SAVE){
            return $this->message('单据不是保存状态', $this->redirect(\Yii::$app->request->referrer), 'error');
        }
        if(!$model->receipt_num){
            return $this->message('单据明细不能为空', $this->redirect(\Yii::$app->request->referrer), 'error');
        }
        $model->audit_status = AuditStatusEnum::PENDING;
        $model->receipt_status = BillStatusEnum::PENDING;
        if(false === $model->save()){
            return $this->message($this->getError($model), $this->redirect(\Yii::$app->request->referrer), 'error');
        }
        return $this->message('操作成功', $this->redirect(\Yii::$app->request->referrer), 'success');

    }


    /**
     * 审核-采购收货单
     *
     * @return mixed
     */
    public function actionAjaxAudit()
    {
        $id = Yii::$app->request->get('id');
        $model = $this->findModel($id);
        $model = $model ?? new PurchaseReceipt();
        if($model->audit_status == AuditStatusEnum::PASS){
            $model->audit_status = AuditStatusEnum::PASS;
        }else{
            $model->audit_status = AuditStatusEnum::UNPASS;
        }
        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {
            try{
                $trans = Yii::$app->trans->beginTransaction();
                $model->audit_time = time();
                $model->auditor_id = \Yii::$app->user->id;
                if($model->audit_status == AuditStatusEnum::PASS){
                    $model->receipt_status = BillStatusEnum::CONFIRM;
                    $res = PurchaseReceiptGoods::updateAll(['goods_status' => ReceiptGoodsStatusEnum::IQC_ING], ['receipt_id'=>$model->id, 'goods_status'=>ReceiptGoodsStatusEnum::SAVE]);
                    if(false === $res) {
                        throw new \Exception("更新货品状态失败");
                    }
                }else{
                    $model->receipt_status = BillStatusEnum::SAVE;
                }
                if(false === $model->save()) {
                    throw new \Exception($this->getError($model));
                }
                $trans->commit();
                return $this->message("保存成功", $this->redirect(Yii::$app->request->referrer), 'success');
            }catch (\Exception $e){
                $trans->rollBack();
                return $this->message("审核失败:". $e->getMessage(),  $this->redirect(Yii::$app->request->referrer), 'error');
            }
        }
        return $this->renderAjax($this->action->id, [
            'model' => $model,
        ]);
    }

    /**
     * 申请入库-采购收货单
     *
     * @return mixed
     */
    public function actionAjaxWarehouse()
    {
        $id = Yii::$app->request->get('id');
        $model = $this->findModel($id);
        $model = $model ?? new PurchaseReceipt();
        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {
            try{
                $trans = Yii::$app->trans->beginTransaction();
                $model->is_to_warehouse = WhetherEnum::ENABLED;
                if(false === $model->save()) {
                    throw new \Exception($this->getError($model));
                }
                //采购收货单同步至L单
                Yii::$app->purchaseService->receipt->syncReceiptToBillInfoL($model);
                $trans->commit();
                return $this->message("申请入库成功", $this->redirect(Yii::$app->request->referrer), 'success');
            }catch (\Exception $e){
                $trans->rollBack();
                return $this->message("申请入库失败:". $e->getMessage(),  $this->redirect(Yii::$app->request->referrer), 'error');
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
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['receipt/index']));
        $model = $this->findModel($id);
        $model = $model ?? new PurchaseReceipt();
        $receipt_no = Yii::$app->request->get('receipt_no');
        if(!$id){
            $receipt = PurchaseReceipt::findOne(['receipt_no'=>$receipt_no]);
            $id = $receipt->id;
        }
        return $this->render($this->action->id, [
            'model' => $model,
            'tab'=>$tab,
            'tabList'=>\Yii::$app->purchaseService->receipt->menuTabList($id, $this->purchaseType, $returnUrl),
            'returnUrl'=>$returnUrl,
        ]);
    }

    /**
     * 关闭
     * @return mixed
     */
    public function actionClose(){

        $id = \Yii::$app->request->get('id');
        $model = $this->findModel($id);
        if($model->receipt_status != BillStatusEnum::SAVE){
            return $this->message('单据不是保存状态', $this->redirect(Yii::$app->request->referrer), 'error');
        }
        $model->receipt_status = BillStatusEnum::CANCEL;
        if(false === $model->save()){
            return $this->message($this->getError($model), $this->redirect(Yii::$app->request->referrer), 'error');
        }
        return $this->message('操作成功', $this->redirect(Yii::$app->request->referrer), 'success');

    }

    /**
     * @param null $ids
     * @return bool|mixed
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function actionExport($ids=null){
        $name = '采购收货单';
        if(!is_array($ids)){
            $ids = StringHelper::explodeIds($ids);
        }
        if(!$ids){
            return $this->message('单据ID不为空', $this->redirect(['index']), 'warning');
        }
        $list = $this->getData($ids);
        $header = [
            ['序号','xuhao','text'],
            ['工厂出货单号/收货单号', 'receipt_no' , 'text'],
            ['采购订单号', 'purchase_sn' , 'text'],
            ['供应商', 'supplier_name' , 'text'],
            ['采购方式', 'put_in_type' , 'text'],
            ['创建人', 'username' , 'text'],
            ['单据状态', 'receipt_status' , 'text'],
            ['款号', 'style_sn' , 'text'],
            ['货品名称', 'goods_name' , 'text'],
            ['产品线', 'product_type_name' , 'text'],
            ['款式分类', 'style_cate_name' , 'text'],
            ['材质', 'material' , 'text'],
            ['成色', 'goods_color' ,  'text'],
            ['件数', 'goods_num' , 'text'],
            ['指圈', 'finger' , 'text'],
            ['尺寸', 'product_size' , 'text'],
            ['货重', 'gold_weight' , 'text'],
            ['净重', 'suttle_weight' , 'text'],
            ['损耗', 'gold_loss' , 'text'],
            ['含耗重', 'gross_weight' , 'text'],
            ['金价', 'gold_price' , 'text'],
            ['金料额', 'gold_amount' , 'text'],
            ['石号', 'main_stone_sn' , 'text'],
            ['粒数', 'main_stone_num' , 'text'],
            ['石重', 'main_stone_weight' , 'text'],
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
            ['配件(g)', 'parts_weight' , 'text'],
            ['配件额', 'parts_price' , 'text'],
            ['配件工费', 'parts_fee' , 'text'],
            ['工费', 'gong_fee' , 'text'],
            ['镶石费', 'xianqian_fee' , 'text'],
            ['工艺费', 'biaomiangongyi_fee' , 'text'],
            ['分色/分件', 'fense_fee' , 'text'],
            ['补口费', 'bukou_fee' , 'text'],
            ['单价', 'price' , 'text'],
            ['总额', 'price_sum' , 'text'],
            ['证书费', 'cert_fee' , 'text'],
            ['备注', 'goods_remark' , 'text'],
            ['倍率', 'markup_rate' , 'text'],
            ['标签价', 'sale_price' , 'text'],
            ['采购订单号', 'purchase_sn' , 'text'],
            ['所属渠道', 'channel_name' , 'text'],
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
        $lists = $this->getData($id);
        return $this->render($this->action->id, [
            'model' => $model,
            'lists' => $lists
        ]);
    }


    private function getData($ids){
        $select = ['pr.receipt_no','pr.receipt_status','pr.put_in_type','type.name as product_type_name',
           'cate.name as style_cate_name', 'channel.name as channel_name','sup.supplier_name','member.username', 'prg.*'];
        $lists = PurchaseReceipt::find()->alias('pr')
            ->innerJoin(PurchaseReceiptGoods::tableName().' prg','pr.id = prg.receipt_id')
            ->leftJoin(ProductType::tableName().' type','type.id=prg.product_type_id')
            ->leftJoin(StyleCate::tableName().' cate','cate.id=prg.style_cate_id')
            ->leftJoin(StyleChannel::tableName().' channel','channel.id=prg.style_channel_id')
            ->leftJoin(Supplier::tableName().' sup','sup.id=pr.supplier_id')
            ->leftJoin(Member::tableName().' member','member.id=pr.creator_id')
            ->where(['pr.id' => $ids])
            ->orderBy('prg.xuhao asc')
            ->select($select)->asArray()->all();
        foreach ($lists as &$list){
            //成色
            $material = empty($list['material']) ? 0 : $list['material'];
            $list['material'] = Yii::$app->attr->valueName($material);
            //单据状态
            $list['receipt_status'] = PurchaseStatusEnum::getValue($list['receipt_status']);
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

        }



        return $lists;
    }
}

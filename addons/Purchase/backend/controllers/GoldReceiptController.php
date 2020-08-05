<?php

namespace addons\Purchase\backend\controllers;

use addons\Purchase\common\enums\ReceiptGoodsStatusEnum;
use addons\Purchase\common\models\PurchaseGoldGoods;
use addons\Style\common\enums\LogTypeEnum;
use common\enums\AuditStatusEnum;
use common\enums\ConfirmEnum;
use Yii;
use common\models\base\SearchModel;
use addons\Purchase\common\models\PurchaseReceipt;
use addons\Purchase\common\forms\PurchaseReceiptForm;
use addons\Purchase\common\models\PurchaseGoldReceiptGoods;
use addons\Purchase\common\enums\PurchaseTypeEnum;
use addons\Style\common\models\ProductType;
use addons\Style\common\models\StyleCate;
use common\helpers\ArrayHelper;
use common\helpers\ExcelHelper;
use common\helpers\StringHelper;
use common\helpers\Url;
use common\traits\Curd;
use addons\Purchase\common\enums\ReceiptStatusEnum;
/**
* Receipt
*
* Class ReceiptController
* @package addons\Purchase\Backend\controllers
*/
class GoldReceiptController extends BaseController
{
    use Curd;

    /**
    * @var Receipt
    */
    public $modelClass = PurchaseReceiptForm::class;
    public $purchaseType = PurchaseTypeEnum::MATERIAL_GOLD;

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
     * @return mixed
     * 申请审核
     */
    public function actionAjaxApply(){
        $id = \Yii::$app->request->get('id');
        $model = $this->findModel($id);
        $model = $model ?? new PurchaseReceiptForm();
        if($model->receipt_status != ReceiptStatusEnum::SAVE){
            return $this->message('单据不是保存状态', $this->redirect(\Yii::$app->request->referrer), 'error');
        }
        if(!$model->receipt_num){
            return $this->message('单据明细不能为空', $this->redirect(\Yii::$app->request->referrer), 'error');
        }
        
        try {
            $trans = \Yii::$app->trans->beginTransaction();
            
            $model->audit_status = AuditStatusEnum::PENDING;
            $model->receipt_status = ReceiptStatusEnum::PENDING;
            if(false === $model->save()){
                throw new \Exception($this->getError($model));
            }            
            $log = [
                    'receipt_id' => $model->id,
                    'receipt_no' => $model->receipt_no,
                    'log_type' => LogTypeEnum::ARTIFICIAL,
                    'log_module' => '申请审核',
                    'log_msg' => "金料收货单-申请审核"
            ];
            \Yii::$app->purchaseService->receiptLog->createReceiptLog($log);
            
            $trans->commit();
            return $this->message('操作成功', $this->redirect(\Yii::$app->request->referrer), 'success');            
        }catch (\Exception $e) {
            $trans->rollback();
            return $this->message($this->getError($model), $this->redirect(\Yii::$app->request->referrer), 'error');
        }
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
                    $model->receipt_status = ReceiptStatusEnum::CONFIRM;
                    $res = PurchaseGoldReceiptGoods::updateAll(['goods_status' => ReceiptGoodsStatusEnum::IQC_ING], ['receipt_id'=>$model->id, 'goods_status'=>ReceiptGoodsStatusEnum::SAVE]);
                    if(false === $res) {
                        throw new \Exception("更新货品状态失败");
                    }
                }else{
                    $model->receipt_status = ReceiptStatusEnum::SAVE;
                }
                if(false === $model->save()) {
                    throw new \Exception($this->getError($model));
                }
                $log = [
                        'receipt_id' => $model->id,
                        'receipt_no' => $model->receipt_no,
                        'log_type' => LogTypeEnum::ARTIFICIAL,
                        'log_module' => '单据审核',
                        'log_msg' => "金料收货单审核, 审核状态：".AuditStatusEnum::getValue($model->audit_status).",审核备注：".$model->audit_remark
                ];
                \Yii::$app->purchaseService->receiptLog->createReceiptLog($log);
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
     * 详情展示页
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView()
    {
        $id = Yii::$app->request->get('id');
        $receipt_no = Yii::$app->request->get('receipt_no');
        $tab = Yii::$app->request->get('tab',1);
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['gold-receipt/index']));
        if(!$id){
            $result = $this->modelClass::find()->where(['receipt_no'=>$receipt_no])->asArray()->one();
            $id = !empty($result)?$result['id']:0;
        }
        $model = $this->findModel($id);
        return $this->render($this->action->id, [
            'model' => $model,
            'tab'=>$tab,
            'tabList'=>\Yii::$app->purchaseService->receipt->menuTabList($id, $this->purchaseType, $returnUrl),
            'returnUrl'=>$returnUrl,
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
            $goods = PurchaseGoldReceiptGoods::find()->select('purchase_detail_id')->where(['receipt_id'=>$id])->all();
            $ids = ArrayHelper::getColumn($goods, 'purchase_detail_id');
            $res = PurchaseGoldGoods::updateAll(['is_receipt'=>ConfirmEnum::NO], ['id'=>$ids]);
            if(false === $res){
                throw new \Exception("更新采购单明细商品状态失败");
            }
            $res = PurchaseGoldReceiptGoods::deleteAll(['receipt_id'=>$id]);
            if(false === $res){
                throw new \Exception("删除明细失败");
            }
            if(false === $model->delete()){
                throw new \Exception($this->getError($model));
            }
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
        $name = '采购收货单';
        if(!is_array($ids)){
            $ids = StringHelper::explodeIds($ids);
        }
        if(!$ids){
            return $this->message('单据ID不为空', $this->redirect(['index']), 'warning');
        }

        $select = ['pr.receipt_no','type.name as product_type_name','cate.name as style_cate_name', 'prg.*'];
        $list = PurchaseReceipt::find()->alias('pr')
            ->leftJoin(PurchaseGoldReceiptGoods::tableName().' prg','pr.id = prg.receipt_id')
            ->leftJoin(ProductType::tableName().' type','type.id=prg.product_type_id')
            ->leftJoin(StyleCate::tableName().' cate','cate.id=prg.style_cate_id')
            ->where(['pr.id' => $ids])
            ->select($select)->asArray()->all();
        $header = [
            ['条码号', 'receipt_no' , 'text'],
            ['款号', 'style_sn' , 'text'],
            ['货品名称', 'goods_name' , 'text'],
            ['产品线', 'product_type_name' , 'text'],
            ['款式分类', 'style_cate_name' , 'text'],
            ['材质', 'material' , 'function', function($model){
                return Yii::$app->attr->valueName($model->material ?? 0);
            }],
            ['成色', 'material' ,  function($model){
                return Yii::$app->attr->valueName($model->material ?? 0);
            }],
            ['件数', 'goods_num' , 'text'],
            ['指圈', 'finger' , 'text'],
            //['尺寸', 'finger' , 'text'],
            ['货重', 'gold_weight' , 'text'],
            ['净重', 'suttle_weight' , 'text'],
            ['损耗', 'gold_loss' , 'text'],
            ['含耗重', 'gross_weight' , 'text'],
            //['金价', 'gross_weight' , 'text'],
            //['金料额', 'gross_weight' , 'text'],
            ['石号', 'main_stone' , 'text'],
            ['粒数', 'main_stone_num' , 'text'],
            ['石重', 'main_stone_weight' , 'text'],
            ['颜色', 'main_stone_color' ,'function', function($model){
                return Yii::$app->attr->valueName($model->main_stone_color ?? 0);
            }],
            ['净度', 'main_stone_clarity' , 'function', function($model){
                return Yii::$app->attr->valueName($model->main_stone_clarity ?? 0);
            }],
            ['单价', 'main_stone_price' , 'text'],
            ['金额', 'main_stone_price' , function($model){
                if($model->main_stone_price){
                    return $model->main_stone_price * $model->main_stone_num;
                }else{
                    return 0;
                }
            }],
            ['副石号', 'second_stone1' , 'text'],
            ['副石粒数', 'second_stone_num1' , 'text'],
            ['副石石重', 'second_stone_weight1' , 'text'],
            ['副石单价', 'second_stone_price1' , 'text'],
            ['副石金额', 'second_stone_price1' , function($model){
                return $model->second_stone_price1 * $model->second_stone_num1;
            }],

            ['配件(g)', 'parts_weight' , 'text'],
            ['配件额', 'parts_price' , 'text'],
            ['配件工费', 'parts_fee' , 'text'],
            ['工费', 'gong_fee' , 'text'],
            ['镶石费', 'xianqian_fee' , 'text'],
            //['车花片', 'xianqian_fee' , 'text'],
            ['分色/分件', 'fense_fee' , 'text'],
            ['补口费', 'bukou_fee' , 'text'],
            ['证书费', 'cert_fee' , 'text'],

            ['单价', 'cost_price' , 'function',function($model){
                $main_stone_price = $model->main_stone_price ?? 0;
                $main_stone_num = $model->main_stone_num ?? 0;
                $cost_price = $model->cost_price ?? 0;
                $gong_fee = $model->gong_fee ?? 0;
                $bukou_fee = $model->bukou_fee ?? 0;
                $biaomiangongyi_fee = $model->biaomiangongyi_fee ?? 0;
                return $main_stone_price * $main_stone_num + $cost_price + $gong_fee + $bukou_fee
                    + $biaomiangongyi_fee;
            }],
            //['总额', 'cost_price' , 'text'],
            ['倍率', 'markup_rate' , 'text'],

            ['备注', 'goods_remark' , 'text'],
            ['标签价', 'sale_price' , 'text'],
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
        $ids = Yii::$app->request->get('ids');
        $id_arr = explode(',', $ids);
        $id = $id_arr[0];//暂时打印一个
        $tab = Yii::$app->request->get('tab',1);
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['gold-receipt/index']));
        $model = $this->findModel($id);
        $goodsModel = new PurchaseGoldReceiptGoods();
        $goodsList = $goodsModel::find()->where(['receipt_id' => $id])->asArray()->all();
        foreach ($goodsList as &$item) {
            $item['stone_zhong'] = $item['main_stone_weight']+$item['second_stone_weight1']+$item['second_stone_weight2']+$item['second_stone_weight3'];
            $item['han_tax_price'] = $item['cost_price'] + $item['tax_fee'];
        }
        return $this->render($this->action->id, [
            'model' => $model,
            'goodsList' => $goodsList,
            'tab'=>$tab,
            'returnUrl'=>$returnUrl,
        ]);
    }
}

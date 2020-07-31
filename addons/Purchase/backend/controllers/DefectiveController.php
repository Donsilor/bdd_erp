<?php

namespace addons\Purchase\backend\controllers;

use Yii;
use common\helpers\Url;
use common\models\base\SearchModel;
use common\enums\AuditStatusEnum;
use common\traits\Curd;

use addons\Purchase\common\models\PurchaseDefective;
use addons\Purchase\common\forms\PurchaseDefectiveForm;
use addons\Purchase\common\enums\DefectiveStatusEnum;
use addons\Purchase\common\enums\PurchaseTypeEnum;
use addons\Purchase\common\models\PurchaseDefectiveGoods;
use addons\Purchase\common\models\PurchaseFqcConfig;
use addons\Purchase\common\models\PurchaseReceipt;
use addons\Purchase\common\models\PurchaseReceiptGoods;
use addons\Style\common\models\ProductType;
use addons\Style\common\models\StyleCate;
use addons\Style\common\models\StyleChannel;
use addons\Supply\common\models\Supplier;
use addons\Warehouse\common\enums\PutInTypeEnum;
use common\enums\LogTypeEnum;
use common\helpers\ArrayHelper;
use common\helpers\ExcelHelper;
use common\helpers\PageHelper;
use common\helpers\StringHelper;

/**
* PurchaseDefective
*
* Class PurchaseDefectiveController
* @package addons\Purchase\Backend\controllers
*/
class DefectiveController extends BaseController
{
    use Curd;

    /**
    * @var PurchaseDefective
    */
    public $modelClass = PurchaseDefectiveForm::class;
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
            ->search(Yii::$app->request->queryParams, ['created_at']);

        $created_at = $searchModel->created_at;
        if (!empty($created_at)) {
            $dataProvider->query->andFilterWhere(['>=',PurchaseDefective::tableName().'.created_at', strtotime(explode('/', $created_at)[0])]);//起始时间
            $dataProvider->query->andFilterWhere(['<',PurchaseDefective::tableName().'.created_at', (strtotime(explode('/', $created_at)[1]) + 86400)] );//结束时间
        }

        $dataProvider->query->andWhere(['>',PurchaseDefective::tableName().'.status',-1]);
        $dataProvider->query->andWhere(['=',PurchaseDefective::tableName().'.purchase_type', $this->purchaseType]);

        //导出
        if(Yii::$app->request->get('action') === 'export'){
            $queryIds = $dataProvider->query->select(PurchaseDefective::tableName().'.id');
            $this->actionExport($queryIds);
        }

        return $this->render('index', [
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
        $id = Yii::$app->request->get('id');
        $tab = Yii::$app->request->get('tab',1);
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['purchase-defective/index']));

        $model = $this->findModel($id);
        return $this->render($this->action->id, [
            'model' => $model,
            'tab'=>$tab,
            'tabList'=>\Yii::$app->purchaseService->defective->menuTabList($id, $this->purchaseType, $returnUrl),
            'returnUrl'=>$returnUrl,
        ]);
    }

    /**
     * ajax 不良返厂单-申请审核
     *
     * @return mixed
     */
    public function actionAjaxApply()
    {
        $id = Yii::$app->request->get('id');
        $model = $this->findModel($id);

        if($model->defective_status != DefectiveStatusEnum::SAVE){
            return $this->message('单据不是保存状态', $this->redirect(\Yii::$app->request->referrer), 'error');
        }
        $model->defective_status = DefectiveStatusEnum::PENDING;
        // ajax 校验
        $this->activeFormValidate($model);
        try{
            $trans = Yii::$app->trans->beginTransaction();

            \Yii::$app->purchaseService->defective->applyAudit($model);

            //日志
            $log = [
                'defective_id' => $model->id,
                'defective_no' => $model->defective_no,
                'log_type' => LogTypeEnum::ARTIFICIAL,
                'log_module' => "不良返厂单",
                'log_msg' => "不良返厂单提交审核",
            ];
            Yii::$app->purchaseService->defectiveLog->createDefectiveLog($log);

            $trans->commit();
        }catch (\Exception $e){
            $trans->rollBack();
            return $this->message("申请审核失败:". $e->getMessage(),  $this->redirect(Yii::$app->request->referrer), 'error');
        }
        return $this->message("申请审核成功", $this->redirect(Yii::$app->request->referrer), 'success');
    }

    /**
     * ajax 不良返厂单-审核
     *
     * @return mixed
     */
    public function actionAjaxAudit()
    {
        $id = Yii::$app->request->get('id');
        $model = $this->findModel($id);
        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {
            try{
                $trans = Yii::$app->trans->beginTransaction();

                $model->auditor_id = \Yii::$app->user->id;
                $model->audit_time = time();

                \Yii::$app->purchaseService->defective->auditDefect($model);
                //日志
                $log = [
                    'defective_id' => $model->id,
                    'defective_no' => $model->defective_no,
                    'log_type' => LogTypeEnum::ARTIFICIAL,
                    'log_module' => "不良返厂单",
                    'log_msg' => "不良返厂单审核",
                ];
                Yii::$app->purchaseService->defectiveLog->createDefectiveLog($log);

                $trans->commit();
            }catch (\Exception $e){
                $trans->rollBack();
                return $this->message("审核失败:". $e->getMessage(),  $this->redirect(Yii::$app->request->referrer), 'error');
            }
            return $this->message("保存成功", $this->redirect(Yii::$app->request->referrer), 'success');
        }
        $model->audit_status = AuditStatusEnum::PASS;
        return $this->renderAjax($this->action->id, [
            'model' => $model,
        ]);
    }

    /**
     * ajax 不良返厂单-取消
     *
     * @return mixed
     */
    public function actionCancel()
    {
        $id = Yii::$app->request->get('id');
        $model = $this->findModel($id);
        // ajax 校验
        $this->activeFormValidate($model);
        try{
            $trans = Yii::$app->trans->beginTransaction();

            \Yii::$app->purchaseService->defective->cancelDefect($model);

            //日志
            $log = [
                'defective_id' => $model->id,
                'defective_no' => $model->defective_no,
                'log_type' => LogTypeEnum::ARTIFICIAL,
                'log_module' => "不良返厂单",
                'log_msg' => "不良返厂单取消",
            ];
            Yii::$app->purchaseService->defectiveLog->createDefectiveLog($log);

            $trans->commit();
        }catch (\Exception $e){
            $trans->rollBack();
            return $this->message("操作失败:". $e->getMessage(),  $this->redirect(Yii::$app->request->referrer), 'error');
        }
        return $this->message("操作成功", $this->redirect(Yii::$app->request->referrer), 'success');
    }

    /**
     * ajax 不良返厂单-删除
     *
     * @return mixed
     */
    public function actionDelete()
    {
        $id = Yii::$app->request->get('id');
        $model = $this->findModel($id);
        // ajax 校验
        $this->activeFormValidate($model);
        try{
            $trans = Yii::$app->trans->beginTransaction();

            \Yii::$app->purchaseService->defective->DeleteDefect($model);

            //日志
            $log = [
                'defective_id' => $model->id,
                'defective_no' => $model->defective_no,
                'log_type' => LogTypeEnum::ARTIFICIAL,
                'log_module' => "不良返厂单",
                'log_msg' => "不良返厂单删除",
            ];
            Yii::$app->purchaseService->defectiveLog->createDefectiveLog($log);

            $trans->commit();
        }catch (\Exception $e){
            $trans->rollBack();
            return $this->message("操作失败:". $e->getMessage(),  $this->redirect(Yii::$app->request->referrer), 'error');
        }
        return $this->message("操作成功", $this->redirect(Yii::$app->request->referrer), 'success');
    }

    /**
     * @param null $ids
     * @return bool|mixed
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function actionExport($ids=null){
        $name = '不良返厂单';
        if(!is_array($ids)){
            $ids = StringHelper::explodeIds($ids);
        }
        if(!$ids){
            return $this->message('单据ID不为空', $this->redirect(['index']), 'warning');
        }

        list($list,) = $this->getData($ids);
        $header = [
//            ['序号', 'xuhao' , 'text'],
//            ['条码号', 'defective_no' , 'text'],
//            ['工厂出货单号', 'receipt_no' , 'text'],
//            ['工厂名称', 'supplier_name' , 'text'],
//            ['模号', 'factory_mo' , 'text'],
//            ['布产单号', 'produce_sn' , 'text'],
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
            ['质检未过原因', 'iqc_name' , 'text'],
            ['质检备注', 'iqc_remark' , 'text']
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
        $select = ['pd.defective_no','pd.defective_status','pd.receipt_no','pd.supplier_id','pdg.xuhao','pdg.style_sn',
            'pdg.factory_mo','pdg.produce_sn','type.name as product_type_name','cate.name as style_cate_name',
            'channel.name as channel_name','sup.supplier_name','pdg.cost_price','pdg.iqc_reason',
            'pdg.iqc_remark','iqc.name as iqc_name','prg.*','pdg.created_at'];

        $query = PurchaseDefective::find()->alias('pd')
            ->innerJoin(PurchaseDefectiveGoods::tableName()." pdg",'pd.id=pdg.defective_id')
            ->innerJoin(PurchaseReceipt::tableName()." pr",'pr.receipt_no=pd.receipt_no')
            ->innerJoin(PurchaseReceiptGoods::tableName().' prg','prg.xuhao=pdg.xuhao and pr.id = prg.receipt_id')
            ->leftJoin(ProductType::tableName().' type','type.id=prg.product_type_id')
            ->leftJoin(StyleCate::tableName().' cate','cate.id=prg.style_cate_id')
            ->leftJoin(StyleChannel::tableName().' channel','channel.id=prg.style_channel_id')
            ->leftJoin(Supplier::tableName().' sup','sup.id=pd.supplier_id')
            ->leftJoin(PurchaseFqcConfig::tableName().' iqc','iqc.id=pdg.iqc_reason')
            ->where(['pd.id' => $ids])
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
            $total['main_stone_weight_count'] += $list['main_stone_weight']; //石重
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

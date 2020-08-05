<?php

namespace addons\Purchase\backend\controllers;


use addons\Purchase\common\enums\DefectiveStatusEnum;
use addons\Purchase\common\enums\PurchaseTypeEnum;
use addons\Purchase\common\models\PurchaseDefectiveGoods;
use addons\Purchase\common\models\PurchaseReceipt;
use addons\Purchase\common\models\PurchaseReceiptGoods;
use addons\Style\common\models\ProductType;
use addons\Style\common\models\StyleCate;
use addons\Warehouse\common\enums\BillStatusEnum;
use common\helpers\ArrayHelper;
use common\helpers\ExcelHelper;
use common\helpers\SnHelper;
use common\helpers\StringHelper;
use Yii;
use common\helpers\Url;
use common\models\base\SearchModel;
use addons\Purchase\common\models\PurchaseDefective;
use addons\Purchase\common\forms\PurchaseDefectiveForm;
use common\enums\AuditStatusEnum;
use common\enums\StatusEnum;
use common\traits\Curd;
use common\enums\LogTypeEnum;
/**
* PurchaseDefective
*
* Class PurchaseDefectiveController
* @package addons\Purchase\Backend\controllers
*/
class PartsDefectiveController extends BaseController
{
    use Curd;

    /**
    * @var PurchaseDefective
    */
    public $modelClass = PurchaseDefectiveForm::class;
    public $purchaseType = PurchaseTypeEnum::MATERIAL_PARTS;

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
            $dataProvider->setPagination(false);
            $list = $dataProvider->models;
            $list = ArrayHelper::toArray($list);
            $ids = array_column($list,'id');
            $this->actionExport($ids);
        }

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * 详情展示页
     * @return string
     * @throws
     */
    public function actionView()
    {
        $id = Yii::$app->request->get('id');
        $tab = Yii::$app->request->get('tab',1);
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['parts-defective/index']));

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
     * @throws
     */
    public function actionAjaxApply()
    {
        $id = Yii::$app->request->get('id');
        $model = $this->findModel($id);

        if($model->defective_status != BillStatusEnum::SAVE){
            return $this->message('单据不是保存状态', $this->redirect(\Yii::$app->request->referrer), 'error');
        }
        $model->defective_status = BillStatusEnum::PENDING;
        // ajax 校验
        $this->activeFormValidate($model);
        try{
            $trans = Yii::$app->trans->beginTransaction();

            \Yii::$app->purchaseService->defective->applyAudit($model);
            
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
     * @throws
     */
    public function actionAjaxAudit()
    {
        $id = Yii::$app->request->get('id');

        $model = $this->findModel($id);

        if($model->audit_status == AuditStatusEnum::PENDING) {
            $model->audit_status = AuditStatusEnum::PASS;
        }

        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {
            try{
                $trans = Yii::$app->trans->beginTransaction();

                $model->auditor_id = \Yii::$app->user->id;
                $model->audit_time = time();

                \Yii::$app->purchaseService->defective->auditDefect($model);
                
                $trans->commit();
            }catch (\Exception $e){
                $trans->rollBack();
                return $this->message("审核失败:". $e->getMessage(),  $this->redirect(Yii::$app->request->referrer), 'error');
            }
            return $this->message("保存成功", $this->redirect(Yii::$app->request->referrer), 'success');
        }

        return $this->renderAjax($this->action->id, [
            'model' => $model,
        ]);
    }

    /**
     * ajax 不良返厂单-取消/删除
     *
     * @return mixed
     * @throws
     */
    public function actionDelete()
    {
        $id = Yii::$app->request->get('id');
        $model = $this->findModel($id);
        // ajax 校验
        $this->activeFormValidate($model);
        try{
            $trans = Yii::$app->trans->beginTransaction();

            \Yii::$app->purchaseService->defective->cancelDefect($model);

            $trans->commit();
        }catch (\Exception $e){
            $trans->rollBack();
            return $this->message("取消失败:". $e->getMessage(),  $this->redirect(Yii::$app->request->referrer), 'error');
        }
        return $this->message("取消成功", $this->redirect(Yii::$app->request->referrer), 'success');
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

        $select = ['pd.defective_no','pd.defective_status','pd.receipt_no','pd.supplier_id','pdg.xuhao','pdg.style_sn',
        'pdg.factory_mo','pdg.produce_sn','type.name as product_type_name','cate.name as style_cate_name',
        'pdg.cost_price','pdg.iqc_reason','pdg.iqc_remark','prg.*','pdg.created_at',];

        $list = PurchaseDefective::find()->alias('pd')
            ->leftJoin(PurchaseDefectiveGoods::tableName()." pdg",'pd.id=pdg.defective_id')
            ->leftJoin(PurchaseReceipt::tableName()." pr",'pr.receipt_no=pd.receipt_no')
            ->leftJoin(PurchaseReceiptGoods::tableName().' prg','prg.xuhao=pdg.xuhao and pr.id = prg.receipt_id')
            ->leftJoin(ProductType::tableName().' type','type.id=pdg.product_type_id')
            ->leftJoin(StyleCate::tableName().' cate','cate.id=pdg.style_cate_id')
            ->where(['pd.id' => $ids])
            ->select($select)->asArray()->all();
        $header = [
            ['条码号', 'defective_no' , 'text'],
            ['工厂出货单号', 'receipt_no' , 'text'],
            ['工厂名称', 'supplier_id' , 'selectd', Yii::$app->supplyService->supplier->getDropDown()],
            ['序号', 'xuhao' , 'text'],
            ['模号', 'factory_mo' , 'text'],
            ['布产单号', 'produce_sn' , 'text'],
            ['款号', 'style_sn' , 'text'],
            ['货品名称', 'goods_name' , 'text'],
            ['产品线', 'product_type_name' , 'text'],
            ['款式分类', 'style_cate_name' , 'text'],
            ['材质', 'material' , 'function', function($model){
                   return Yii::$app->attr->valueName($model->material ?? 0);
            }],
            ['成色', 'material' , 'function', function($model){
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
            ['颜色', 'main_stone_color' ,  'function', function($model){
                return Yii::$app->attr->valueName($model->main_stone_color ?? 0);
            }],
            ['净度', 'main_stone_clarity' , 'function', function($model){
                return Yii::$app->attr->valueName($model->main_stone_clarity ?? 0);
            }],
            ['单价', 'main_stone_price' , 'text'],
            ['金额', 'main_stone_price' , function($model){
                return $model->main_stone_price * $model->main_stone_num;
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

            ['单价', 'cost_price' , 'text'],
            //['总额', 'cost_price' , 'text'],
            ['倍率', 'markup_rate' , 'text'],


            ['质检未过原因', 'iqc_reason' , 'text'],
            ['质检备注', 'iqc_remark' , 'text']
        ];
        return ExcelHelper::exportData($list, $header, $name.'数据导出_' . date('YmdHis',time()));
    }
}

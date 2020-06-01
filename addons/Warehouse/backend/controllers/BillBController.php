<?php

namespace addons\Warehouse\backend\controllers;


use addons\Style\common\models\ProductType;
use addons\Style\common\models\StyleCate;
use addons\Warehouse\common\enums\PutInTypeEnum;
use addons\Warehouse\common\models\WarehouseBillGoods;
use addons\Warehouse\common\models\WarehouseGoods;
use common\helpers\ArrayHelper;
use common\helpers\StringHelper;
use Yii;
use common\traits\Curd;
use common\helpers\Url;
use common\helpers\SnHelper;
use common\helpers\ExcelHelper;
use common\models\base\SearchModel;
use addons\Warehouse\common\models\WarehouseBill;
use addons\Warehouse\common\forms\WarehouseBillBForm;
use addons\Warehouse\common\enums\BillStatusEnum;
use addons\Warehouse\common\enums\BillTypeEnum;
use common\enums\AuditStatusEnum;

/**
 * WarehouseBillBController implements the CRUD actions for WarehouseBillBController model.
 */
class BillBController extends BaseController
{
    use Curd;
    public $modelClass = WarehouseBillBForm::class;
    public $billType = BillTypeEnum::BILL_TYPE_B;

    /**
     * Lists all WarehouseBill models.
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
            ->search(\Yii::$app->request->queryParams,['created_at', 'audit_time']);

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

        $dataProvider->query->andWhere(['>',Warehousebill::tableName().'.status', -1]);
        $dataProvider->query->andWhere(['=',Warehousebill::tableName().'.bill_type', $this->billType]);

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
     * ajax编辑/创建 退货返厂单
     *
     * @return mixed|string|\yii\web\Response
     * @throws \yii\base\ExitException
     */
    public function actionAjaxEdit()
    {
        $id = \Yii::$app->request->get('id');
        $model = $this->findModel($id);
        $model = $model ?? new WarehouseBill();

        if($model->isNewRecord){
            $model->bill_type = $this->billType;
        }
        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(\Yii::$app->request->post())) {
            if($model->isNewRecord){
                $model->bill_no = SnHelper::createBillSn($this->billType);
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
        $id = Yii::$app->request->get('id');
        $tab = Yii::$app->request->get('tab',1);
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['warehouser-bill-b/index']));
        $model = $this->findModel($id);
        return $this->render($this->action->id, [
            'model' => $model,
            'tab'=>$tab,
            'tabList'=>\Yii::$app->warehouseService->bill->menuTabList($id,$this->billType,$returnUrl),
            'returnUrl'=>$returnUrl,
        ]);
    }

    /**
     * ajax 退货返厂单-申请审核
     *
     * @return mixed|string|\yii\web\Response
     * @throws \yii\base\ExitException
     */
    public function actionAjaxApply(){
        $id = \Yii::$app->request->get('id');
        $model = $this->findModel($id);
        if($model->bill_status != BillStatusEnum::SAVE){
            return $this->message('单据不是保存状态', $this->redirect(\Yii::$app->request->referrer), 'error');
        }
        $model->bill_status = BillStatusEnum::PENDING;
        $model->audit_status = AuditStatusEnum::PENDING;
        if(false === $model->save()){
            return $this->message($this->getError($model), $this->redirect(\Yii::$app->request->referrer), 'error');
        }
        return $this->message('操作成功', $this->redirect(\Yii::$app->request->referrer), 'success');
    }

    /**
     * ajax 退货返厂单-审核
     *
     * @return mixed|string|\yii\web\Response
     * @throws \yii\base\ExitException
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
                $trans = \Yii::$app->trans->beginTransaction();

                $model->audit_time = time();
                $model->auditor_id = \Yii::$app->user->identity->id;

                \Yii::$app->warehouseService->billB->auditBillB($model);

                $trans->commit();

                $this->message('操作成功', $this->redirect(Yii::$app->request->referrer), 'success');
            }catch(\Exception $e){
                $trans->rollBack();
                $this->message($e->getMessage(), $this->redirect(Yii::$app->request->referrer), 'error');
            }
        }

        return $this->renderAjax($this->action->id, [
            'model' => $model,
        ]);
    }

    /**
     * 退货返厂单关闭
     *
     * @param $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        if (!($model = $this->modelClass::findOne($id))) {
            return $this->message("找不到数据", $this->redirect(Yii::$app->request->referrer), 'error');
        }
        try{
            $trans = \Yii::$app->db->beginTransaction();
            $model->bill_status = BillStatusEnum::CANCEL;
            \Yii::$app->warehouseService->billB->closeBillB($model);
            $trans->commit();
            $this->message('操作成功', $this->redirect(Yii::$app->request->referrer), 'success');
        }catch (\Exception $e){
            $trans->rollBack();
            return $this->message($e->getMessage(), $this->redirect(\Yii::$app->request->referrer), 'error');
        }
    }

    /**
     * 导出列表
     * @param unknown $dataProvider
     * @return boolean
     */
    private function getExport($dataProvider)
    {
        $list = $dataProvider->models;
        $header = [
            ['ID', 'id'],
            ['渠道名称', 'name', 'text'],
        ];
        return ExcelHelper::exportData($list, $header, '盘点数据导出_' . time());

    }

    /**
     * @param null $ids
     * @return bool|mixed
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function actionExport($ids=null){
        $name = '退货返厂单';
        if(!is_array($ids)){
            $ids = StringHelper::explodeIds($ids);
        }
        if(!$ids){
            return $this->message('单据ID不为空', $this->redirect(['index']), 'warning');
        }

        $select = ['w.bill_no','w.bill_type','w.bill_status','g.goods_id','wg.style_sn','wg.goods_name','wg.put_in_type'
            ,'wg.material','wg.gold_weight','wg.gold_loss','wg.diamond_carat','wg.diamond_color','wg.diamond_clarity',
            'wg.cost_price','wg.diamond_cert_id','type.name as product_type_name','cate.name as style_cate_name'];

        $list = WarehouseBill::find()->alias('w')
            ->leftJoin(WarehouseBillGoods::tableName()." wg",'w.id=wg.bill_id')
            ->leftJoin(WarehouseGoods::tableName().' g','g.goods_id=wg.goods_id')
            ->leftJoin(ProductType::tableName().' type','type.id=g.product_type_id')
            ->leftJoin(StyleCate::tableName().' cate','cate.id=g.style_cate_id')
            ->where(['w.id' => $ids])
            ->select($select)->asArray()->all();
        $header = [
            ['单据编号', 'bill_no' , 'text'],
            ['单据类型', 'bill_type' , 'selectd', BillTypeEnum::getMap()],
            ['单据状态', 'bill_status' , 'selectd',BillStatusEnum::getMap()],
            ['货号', 'goods_id' , 'text'],
            ['款号', 'style_sn' , 'text'],
            ['商品名称', 'goods_name' , 'text'],
            ['产品线', 'product_type_name' , 'text'],
            ['款式分类', 'style_cate_name' , 'text'],
            ['入库方式', 'put_in_type' , 'selectd',PutInTypeEnum::getMap()],
            ['主成色', 'material' , function($model){
                return \Yii::$app->attr->valueName($model['material']);
            }],
            ['金重', 'gold_weight' , 'text'],
            ['金损', 'gold_loss' , 'text'],
            ['钻石大小', 'diamond_carat' , 'text'],
            ['钻石颜色', 'diamond_color' , function($model){
                return \Yii::$app->attr->valueName($model['diamond_color']);
            }],
            ['钻石净度', 'diamond_clarity' ,function($model){
                return \Yii::$app->attr->valueName($model['diamond_clarity']);
            }],
            ['证书号', 'diamond_cert_id' , 'text'],
            ['成本价', 'cost_price' , 'text']

        ];

        return ExcelHelper::exportData($list, $header, $name.'数据导出_' . date('YmdHis',time()));
    }

}

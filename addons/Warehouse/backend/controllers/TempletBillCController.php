<?php

namespace addons\Warehouse\backend\controllers;

use addons\Warehouse\common\forms\WarehouseTempletBillLForm;
use common\helpers\SnHelper;
use Yii;
use common\traits\Curd;
use common\models\base\SearchModel;
use addons\Warehouse\common\models\WarehouseTempletBill;
use addons\Warehouse\common\models\WarehouseTempletBillGoods;
use addons\Warehouse\common\forms\WarehouseTempletBillCForm;
use addons\Supply\common\models\Supplier;
use addons\Supply\common\models\ProduceGold;
use addons\Supply\common\enums\PeiliaoStatusEnum;
use addons\Warehouse\common\enums\TempletBillTypeEnum;
use addons\Warehouse\common\enums\TempletBillStatusEnum;
use common\enums\AuditStatusEnum;
use common\helpers\PageHelper;
use common\helpers\StringHelper;
use common\helpers\ExcelHelper;
use common\helpers\Url;

/**
 * TempletBillCController implements the CRUD actions for StyleChannel model.
 */
class TempletBillCController extends TempletBillController
{
    use Curd;
    public $modelClass = WarehouseTempletBillCForm::class;
    public $billType = TempletBillTypeEnum::TEMPLET_C;

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
                'auditor' => ['username'],
            ]
        ]);

        $dataProvider = $searchModel
            ->search(Yii::$app->request->queryParams,['created_at']);

        $created_at = $searchModel->created_at;
        if (!empty($created_at)) {
            $dataProvider->query->andFilterWhere(['>=',WarehouseTempletBill::tableName().'.created_at', strtotime(explode('/', $created_at)[0])]);//起始时间
            $dataProvider->query->andFilterWhere(['<',WarehouseTempletBill::tableName().'.created_at', (strtotime(explode('/', $created_at)[1]) + 86400)] );//结束时间
        }

        $dataProvider->query->andWhere(['>', WarehouseTempletBill::tableName().'.status', -1]);
        $dataProvider->query->andWhere(['=', WarehouseTempletBill::tableName().'.bill_type', $this->billType]);

        //导出
        if(\Yii::$app->request->get('action') === 'export'){
            $queryIds = $dataProvider->query->select(WarehouseTempletBill::tableName().'.id');
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
        $model = $model ?? new WarehouseTempletBillCForm();
        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(\Yii::$app->request->post())) {
            try{
                $trans = \Yii::$app->db->beginTransaction();
                if($model->isNewRecord){
                    $model->bill_no = SnHelper::createBillSn($this->billType);
                    $model->bill_type = $this->billType;
                    $model->bill_status = TempletBillStatusEnum::SAVE;
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
     * @throws
     */
    public function actionView()
    {
        $bill_id = Yii::$app->request->get('id');
        $tab = Yii::$app->request->get('tab',1);
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['templet-bill-c/index']));
        $model = $this->findModel($bill_id);
        $model = $model ?? new WarehouseTempletBill();
        return $this->render($this->action->id, [
            'model' => $model,
            'tab'=>$tab,
            'tabList'=>\Yii::$app->warehouseService->templetBill->menuTabList($bill_id, $this->billType, $returnUrl),
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
        $model = $model ?? new WarehouseTempletBill();
        if($model->bill_status != TempletBillStatusEnum::SAVE){
            return $this->message('单据不是保存状态', $this->redirect(\Yii::$app->request->referrer), 'error');
        }
        if($model->total_num <= 0){
            return $this->message('单据明细不能为空', $this->redirect(\Yii::$app->request->referrer), 'error');
        }
        
        try{
            $trans = Yii::$app->trans->beginTransaction();
            
            $model->bill_status  = TempletBillStatusEnum::PENDING;
            $model->audit_status = AuditStatusEnum::PENDING;
            
            
            if(false === $model->save()){
                throw new \Exception($this->getError($model));
            }
            
            //更新配石状态
            $subIdQuery = WarehouseTempletBillGoods::find()->select(['source_detail_id'])->where(['bill_id'=>$id]);
            $produce_sns = ProduceGold::find()->where(['id'=>$subIdQuery])->distinct('produce_sn')->asArray()->all();
            if(!empty($produce_sns)) {
                $produce_sns = array_column($produce_sns, 'produce_sn');
                ProduceGold::updateAll(['peiliao_status'=>PeiliaoStatusEnum::TO_LINGLIAO],['id'=>$subIdQuery]);
                Yii::$app->supplyService->produce->autoPeiliaoStatus($produce_sns);
            }else{
                throw new \Exception("数据异常");
            }
            $trans->commit();
            return $this->message('操作成功', $this->redirect(\Yii::$app->request->referrer), 'success');
        }catch(\Exception $e) {
            $trans->rollback();
            return $this->message($e->getMessage(), $this->redirect(\Yii::$app->request->referrer), 'error');
        }

    }

    /**
     * ajax 样板出库单-审核
     *
     * @return mixed|string|\yii\web\Response
     * @throws \yii\base\ExitException
     */
    public function actionAjaxAudit()
    {
        $id = Yii::$app->request->get('id');
        $model = $this->findModel($id);
        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {

            try{
                $trans = \Yii::$app->trans->beginTransaction();

                $model->audit_time = time();
                $model->auditor_id = \Yii::$app->user->identity->getId();

                //\Yii::$app->warehouseService->goldBill->auditGoldL($model);

                $trans->commit();

                $this->message('操作成功', $this->redirect(Yii::$app->request->referrer), 'success');
            }catch(\Exception $e){
                $trans->rollBack();
                $this->message($e->getMessage(), $this->redirect(Yii::$app->request->referrer), 'error');
            }
        }
        $model->audit_status = AuditStatusEnum::PASS;
        return $this->renderAjax($this->action->id, [
            'model' => $model,
        ]);
    }

    /**
     * @param null $ids
     * @return bool|mixed
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function actionExport($ids = null){
        $name = '领料单明细';
        if(!is_array($ids)){
            $ids = StringHelper::explodeIds($ids);
        }
        if(!$ids){
            return $this->message('单据ID不为空', $this->redirect(['index']), 'warning');
        }
        list($list,) = $this->getData($ids);
        $header = [
            ['单据编号', 'bill_no' , 'text'],
            ['供应商', 'supplier_name' , 'text'],
            ['名称', 'gold_type' , 'text'],
            ['金类', 'gold_name' , 'text'],
            ['款号', 'style_no' , 'text'],
            ['重量(g)', 'gold_weight' , 'text'],
            ['价格	', 'gold_price' , 'text'],
            ['备注', 'remark' , 'text'],

        ];

        return ExcelHelper::exportData($list, $header, $name.'数据导出_' . date('YmdHis',time()));
    }


    private function getData($ids){
        $select = ['wg.*','w.bill_no','w.to_warehouse_id','sup.supplier_name'];
        $query = WarehouseTempletBillCForm::find()->alias('w')
            ->leftJoin(WarehouseTempletBillGoods::tableName()." wg",'w.id=wg.bill_id')
            ->leftJoin(Supplier::tableName().' sup','sup.id=w.supplier_id')
            ->where(['w.id' => $ids])
            ->select($select);
        $lists = PageHelper::findAll($query, 100);
        //统计
        $total = [

        ];
        foreach ($lists as &$list){
            //$list['gold_type'] = \Yii::$app->attr->valueName($list['gold_type']);
        }
        return [$lists,$total];
    }

    /**
     * 单据打印
     * @return string
     * @throws
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

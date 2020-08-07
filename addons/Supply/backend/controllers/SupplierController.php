<?php

namespace addons\Supply\backend\controllers;

use common\enums\FlowStatusEnum;
use Yii;
use common\helpers\Url;
use common\models\base\SearchModel;
use addons\Supply\common\models\Supplier;
use addons\Supply\common\forms\SupplierForm;
use common\enums\AuditStatusEnum;
use common\enums\StatusEnum;
use common\helpers\ExcelHelper;
use common\helpers\ResultHelper;
use common\helpers\StringHelper;
use yii\base\Exception;
use common\traits\Curd;

/**
* Supplier
*
* Class SupplierController
* @package backend\modules\goods\controllers
*/
class SupplierController extends BaseController
{
    use Curd;

    /**
    * @var Supplier
    */
    public $modelClass = SupplierForm::class;


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
            'partialMatchAttributes' => ['supplier_name', 'contactor', 'mobile', 'telephone', 'address'], // 模糊查询
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
            ->search(Yii::$app->request->queryParams,['audit_time']);

        $audit_time = $searchModel->audit_time;
        if (!empty($audit_time)) {
            $dataProvider->query->andFilterWhere(['>=',Supplier::tableName().'.audit_time', strtotime(explode('/', $audit_time)[0])]);//起始时间
            $dataProvider->query->andFilterWhere(['<',Supplier::tableName().'.audit_time', (strtotime(explode('/', $audit_time)[1]) + 86400)] );//结束时间
        }

        $dataProvider->query->andWhere(['>',Supplier::tableName().'.status',-1]);

        //导出
        if(\Yii::$app->request->get('action') === 'export'){
            $queryIds = $dataProvider->query->select(Supplier::tableName().'.id');
            $this->actionExport($queryIds);
        }

        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * 编辑/创建
     *
     * @return mixed
     */
    public function actionEdit()
    {
        $id = Yii::$app->request->get('id');
        //$returnUrl = Yii::$app->request->get('returnUrl',['index']);

        $model = $this->findModel($id);
        $model = $model ?? new SupplierForm();

        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {
            if(!$model->validate()) {
                return ResultHelper::json(422, $this->getError($model));
            }
            try{
                $trans = Yii::$app->db->beginTransaction();
                if($model->isNewRecord){
                    $model->creator_id = \Yii::$app->user->id;
                }
                $model->status = StatusEnum::DISABLED;
                $model->supplier_code = StringHelper::getFirstCode($model->supplier_name);
                if(false === $model->save()){
                    throw new Exception($this->getError($model));
                }
                $trans->commit();
            }catch (Exception $e){
                $trans->rollBack();
                $error = $e->getMessage();
                //\Yii::error($error);
                return $this->message("保存失败:".$error, $this->redirect([$this->action->id,'id'=>$model->id]), 'error');
            }

            return $this->message("保存成功", $this->redirect(['index']), 'success');
        }

        return $this->render($this->action->id, [
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
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['supplier/index']));
        $model = $this->findModel($id);
        $model = $model ?? new SupplierForm();
        $model->getTargetType();
        if($model->business_scope){
            $business_scope_arr = explode(',', $model->business_scope);
            $business_scope_arr = array_filter($business_scope_arr);
            $business_scope_str = '';
            foreach ($business_scope_arr as $business_scope){
                $business_scope_str .= ','. \addons\Supply\common\enums\BusinessScopeEnum::getValue($business_scope);
            }
            $model->business_scope = trim( $business_scope_str,',' );
        }

        if($model->pay_type){
            $pay_type_arr = explode(',', $model->pay_type);
            $pay_type_arr = array_filter($pay_type_arr);
            $pay_type_str = '';
            foreach ($pay_type_arr as $pay_type){
                $pay_type_str .= ','. \addons\Supply\common\enums\SettlementWayEnum::getValue($pay_type);
            }
            $model->pay_type = trim( $pay_type_str,',' );
        }
        return $this->render($this->action->id, [
            'model' => $model,
            'tab'=>$tab,
            'tabList'=>\Yii::$app->supplyService->supplier->menuTabList($id,$returnUrl),
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
        $model = $model ?? new SupplierForm();
        if($model->audit_status != AuditStatusEnum::SAVE){
            return $this->message('供应商不是保存状态', $this->redirect(\Yii::$app->request->referrer), 'error');
        }
        $model->getTargetType();
        try{
            $trans = Yii::$app->db->beginTransaction();
            if($model->targetType){
                //审批流程
                Yii::$app->services->flowType->createFlow($model->targetType,$id,$model->supplier_name);
            }
            $model->audit_status = AuditStatusEnum::PENDING;
            if(false === $model->save()){
                return $this->message($this->getError($model), $this->redirect(\Yii::$app->request->referrer), 'error');
            }
            $trans->commit();
            return $this->message('操作成功', $this->redirect(\Yii::$app->request->referrer), 'success');
        }catch (\Exception $e){
            $trans->rollBack();
            return $this->message($e->getMessage(), $this->redirect(Yii::$app->request->referrer), 'error');
        }
    }

    /**
     * 供应商-审核
     *
     * @return mixed
     */
    public function actionAjaxAudit()
    {
        $id = Yii::$app->request->get('id');
        $model = $this->findModel($id);
        $model = $model ?? new SupplierForm();
        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {
            try{
                $trans = Yii::$app->trans->beginTransaction();

                $model->getTargetType();
                if($model->targetType){
                    $audit = [
                        'audit_status' =>  $model->audit_status ,
                        'audit_time' => time(),
                        'audit_remark' => $model->audit_remark
                    ];
                    $res = \Yii::$app->services->flowType->flowAudit($model->targetType,$id,$audit);
                    //审批完结或者审批不通过才会走下面
                    if($res->flow_status == FlowStatusEnum::COMPLETE || $res->flow_status == FlowStatusEnum::CANCEL) {
                        if ($model->audit_status == AuditStatusEnum::PASS) {
                            $model->auditor_id = \Yii::$app->user->id;
                            $model->audit_time = time();
                            $model->status = StatusEnum::ENABLED;
                        } else {
                            $model->status = StatusEnum::DISABLED;
                            $model->audit_status = AuditStatusEnum::SAVE;
                        }
                        if (false === $model->save()) {
                            throw new \Exception($this->getError($model));
                        }
                    }
                }
                $trans->commit();
            }catch (\Exception $e){
                $trans->rollBack();
                return $this->message("审核失败:". $e->getMessage(),  $this->redirect(Yii::$app->request->referrer), 'error');
            }
            return $this->message("保存成功", $this->redirect(Yii::$app->request->referrer), 'success');
        }
        $model->audit_status  = AuditStatusEnum::PASS;
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
        $name = '供应商信息';
        if(!is_array($ids)){
            //$ids = StringHelper::explodeIds($ids);
        }
        if(!$ids){
            //return $this->message('ID不为空', $this->redirect(['index']), 'warning');
        }
        list($list,) = $this->getData($ids);
        $header = [
            ['供应商编码', 'supplier_code' , 'text'],
            ['供应商简称', 'supplier_tag' , 'text'],
            ['供应商名称', 'supplier_name' , 'text'],
            ['营业执照号码', 'business_no' , 'text'],
            ['营业执照地址', 'business_address' , 'text'],
            ['经营范围', 'business_scope' , 'text'],
            ['结算方式', 'pay_type' , 'text', ],
            ['付款周期', 'balance_type' , 'text'],
            ['税务登记证号', 'tax_no' , 'text'],
            ['开户行', 'bank_name' , 'text'],
            ['银行账户', 'bank_account' , 'text'],
            ['开户姓名', 'bank_account_name' , 'text'],
            ['联系人', 'contactor' , 'text'],
            ['联系人手机', 'telephone' , 'text'],
            ['联系电话', 'mobile' , 'text'],
            ['取货地址', 'address' , 'text'],
            ['BDD紧急联系人', 'bdd_contactor' , 'text'],
            ['BDD紧急联系人手机', 'bdd_mobile' , 'text'],
            ['BDD紧急联系人电话', 'bdd_telephone' , 'text'],
            ['供应商备注', 'remark' , 'text'],
        ];

        return ExcelHelper::exportData($list, $header, $name.'导出_' . date('YmdHis',time()));
    }

    private function getData($ids){
        //$query = SupplierForm::find()->where(['id' => $ids]);
        $lists = SupplierForm::find()->asArray()->all();
        //$lists = PageHelper::findAll($query, 100);
        foreach ($lists as &$list){
            if($list['business_scope']){
                $business_scope_arr = explode(',', $list['business_scope']);
                $business_scope_arr = array_filter($business_scope_arr);
                $business_scope_str = '';
                foreach ($business_scope_arr as $business_scope){
                    $business_scope_str .= ','. \addons\Supply\common\enums\BusinessScopeEnum::getValue($business_scope);
                }
                $list['business_scope'] = trim( $business_scope_str,',' );
            }
            if($list['pay_type']){
                $pay_type_arr = explode(',', $list['pay_type']);
                $pay_type_arr = array_filter($pay_type_arr);
                $pay_type_str = '';
                foreach ($pay_type_arr as $pay_type){
                    $pay_type_str .= ','. \addons\Supply\common\enums\SettlementWayEnum::getValue($pay_type);
                }
                $list['pay_type'] = trim( $pay_type_str,',' );
            }
            if($list['balance_type']){
                $balance_type_arr = explode(',', $list['balance_type']);
                $balance_type_arr = array_filter($balance_type_arr);
                $balance_type_str = '';
                foreach ($balance_type_arr as $balance_type){
                    $balance_type_str .= ','. \addons\Supply\common\enums\BalanceTypeEnum::getValue($balance_type);
                }
                $list['balance_type'] = trim( $balance_type_str,',' );
            }
        }
        return [$lists,[]];
    }
}

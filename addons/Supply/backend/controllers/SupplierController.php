<?php

namespace addons\Supply\backend\controllers;

use addons\Purchase\common\forms\PurchaseGoodsForm;
use addons\Supply\common\models\SupplierFollower;
use common\helpers\ResultHelper;
use common\helpers\StringHelper;
use common\helpers\Url;
use Yii;
use common\models\base\SearchModel;
use addons\Supply\common\models\Supplier;
use addons\Supply\common\forms\SupplierAuditForm;
use addons\Supply\common\forms\SupplierForm;
use common\enums\AuditStatusEnum;
use common\enums\StatusEnum;
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
    public $modelClass = Supplier::class;


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
            'pageSize' => $this->pageSize
        ]);
        $dataProvider = $searchModel
            ->search(Yii::$app->request->queryParams);

        //$dataProvider->query->andWhere(['>','status',-1]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * 编辑/创建
     *
     * @return mixed
     */
    public function actionEditLang()
    {
        $id = Yii::$app->request->get('id');
        $returnUrl = Yii::$app->request->get('returnUrl',['index']);

        $model = $this->findModel($id);
        $model = $model ?? new SupplierForm();

        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {
            if(!$model->validate()) {
                return ResultHelper::json(422, $this->getError($model));
            }
            try{
                $trans = Yii::$app->db->beginTransaction();
                $model->status = StatusEnum::DISABLED;
                if(false === $model->save()){
                    throw new Exception($this->getError($model));
                }
                $trans->commit();
            }catch (Exception $e){
                $trans->rollBack();
                $error = $e->getMessage();
                \Yii::error($error);
                return $this->message("保存失败:".$error, $this->redirect([$this->action->id,'id'=>$model->id]), 'error');
            }

            return $this->message("保存成功", $this->redirect($returnUrl), 'success');
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
        if($model->business_scope){
            $business_scope_arr = explode(',', $model->business_scope);
            $business_scope_str = '';
            foreach ($business_scope_arr as $business_scope){
                $business_scope_str .= ','. Yii::$app->attr->valueName($business_scope);
            }
            $model->business_scope = trim( $business_scope_str,',' );
        }

        if($model->pay_type){
            $pay_type_arr = explode(',', $model->pay_type);
            $pay_type_str = '';
            foreach ($pay_type_arr as $pay_type){
                $pay_type_str .= ','. Yii::$app->attr->valueName($pay_type);
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
     * 审核-款号
     *
     * @return mixed
     */
    public function actionAjaxAudit()
    {
        $id = Yii::$app->request->get('id');

        $this->modelClass = SupplierAuditForm::class;
        $model = $this->findModel($id);
        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {
            try{
                $trans = Yii::$app->trans->beginTransaction();
                if($model->audit_status == AuditStatusEnum::PASS){
                    $model->auditor_id = \Yii::$app->user->id;
                    $model->audit_time = time();
                    $model->status = StatusEnum::ENABLED;
                }else{
                    $model->status = StatusEnum::DISABLED;
                }
                if(false === $model->save()) {
                    throw new \Exception($this->getError($model));
                }
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
     * 生成Code
     *
     * @return mixed
     */
    public function actionAutoCode()
    {
        $supplier_name = Yii::$app->request->post('supplier_name');
        $str = StringHelper::getFirstCode($supplier_name);
        return substr($str,0,31);
    }

}

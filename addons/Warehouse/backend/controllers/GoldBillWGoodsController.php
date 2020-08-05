<?php

namespace addons\Warehouse\backend\controllers;

use Yii;
use common\traits\Curd;
use common\helpers\Url;
use common\models\base\SearchModel;
use addons\Warehouse\common\models\WarehouseGoldBillGoods;
use addons\Warehouse\common\forms\WarehouseGoldBillGoodsWForm;
use addons\Warehouse\common\forms\WarehouseGoldBillWForm;
use addons\Warehouse\common\enums\GoldBillTypeEnum;
use addons\Warehouse\common\enums\PandianStatusEnum;
use addons\Warehouse\common\enums\FinAuditStatusEnum;
use common\helpers\ResultHelper;

/**
 * WarehouseBillController implements the CRUD actions for WarehouseBillController model.
 */
class GoldBillWGoodsController extends BaseController
{
    use Curd;
    public $modelClass = WarehouseGoldBillGoods::class;
    public $billType = GoldBillTypeEnum::GOLD_W;
    /**
     * Lists all StyleChannel models.
     * @return mixed
     */
    public function actionIndex()
    {
        $bill_id = Yii::$app->request->get('bill_id');
        $tab = Yii::$app->request->get('tab',2);
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['gold-bill-w/index', 'bill_id'=>$bill_id]));
        $bill = WarehouseGoldBillWForm::find()->where(['id'=>$bill_id])->one();
        
        $searchModel = new SearchModel([
                'model' => $this->modelClass,
                'scenario' => 'default',
                'partialMatchAttributes' => [], // 模糊查询
                'defaultOrder' => [
                        'id' => SORT_DESC
                ],
                'pageSize' =>  $this->getPageSize(15),
                'relations' => [
                    "goodsW"=> [
                        "actual_weight",
                        "fin_status",
                        "fin_checker",
                        "fin_check_time",
                        "fin_remark",
                        "fin_adjust_status",
                        "adjust_status",
                        "adjust_reason",
                    ]
                ]
        ]);
        
        $dataProvider = $searchModel
            ->search(Yii::$app->request->queryParams);
        
        $dataProvider->query->andWhere(['=',WarehouseGoldBillGoods::tableName().'.bill_id',$bill_id]);
        $dataProvider->query->andWhere(['>',WarehouseGoldBillGoods::tableName().'.status',PandianStatusEnum::SAVE]);
        
        return $this->render($this->action->id, [
                'dataProvider' => $dataProvider,
                'searchModel' => $searchModel,
                'bill'=>$bill,
                'tab' =>$tab,
                'tabList'=>\Yii::$app->warehouseService->goldBill->menuTabList($bill_id,$this->billType,$returnUrl),
                'returnUrl'=>$returnUrl
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
        $this->modelClass = new WarehouseGoldBillGoodsWForm();
        $model = $this->findModel($id);
        $model = $model ?? new WarehouseGoldBillGoodsWForm();
        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(\Yii::$app->request->post())) {
            try{
                $trans = \Yii::$app->db->beginTransaction();
                if(false === $model->save()) {
                    throw new \Exception($this->getError($model));
                }
                $trans->commit();
                \Yii::$app->getSession()->setFlash('success', '保存成功');
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
     * ajax 审核
     *
     * @return mixed|string|\yii\web\Response
     * @throws \yii\base\ExitException
     */
    public function actionAjaxAudit()
    {
        $id = Yii::$app->request->get('id');
        $this->modelClass = new WarehouseGoldBillGoodsWForm();
        $model = $this->findModel($id) ?? new WarehouseGoldBillGoodsWForm();
        //默认值
        if($model->fin_status == FinAuditStatusEnum::PENDING) {
            $model->fin_status = FinAuditStatusEnum::PASS;
        }
        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {
            try{
                $trans = \Yii::$app->trans->beginTransaction();

                $model->fin_check_time = time();
                $model->fin_checker = (string) \Yii::$app->user->identity->id;

                \Yii::$app->warehouseService->goldW->auditFinW($model);

                $trans->commit();

                $this->message('操作成功', $this->redirect(Yii::$app->request->referrer), 'success');
            }catch(\Exception $e){
                $this->message($e->getMessage(), $this->redirect(Yii::$app->request->referrer), 'error');
            }
        }

        return $this->renderAjax($this->action->id, [
            'model' => $model,
        ]);
    }

    /**
     * 批量审核
     *
     * @return mixed|string|\yii\web\Response
     * @throws \yii\base\ExitException
     */
    public function actionBatchAudit()
    {
        $ids = Yii::$app->request->get('ids');
        $check = Yii::$app->request->get('check', null);
        $model = new WarehouseGoldBillGoodsWForm();
        $model->ids = $ids;
        //默认值
        if($model->fin_status == FinAuditStatusEnum::PENDING) {
            $model->fin_status = FinAuditStatusEnum::PASS;
        }
        if($check){
            try{
                \Yii::$app->warehouseService->goldW->auditGoodsValidate($model);
                return ResultHelper::json(200, '', ['url'=>Url::to([$this->action->id, 'ids' =>$ids])]);
            }catch (\Exception $e){
                return ResultHelper::json(422, $e->getMessage());
            }
        }
        if ($model->load(Yii::$app->request->post())) {
            try{
                $trans = Yii::$app->trans->beginTransaction();
                \Yii::$app->warehouseService->goldW->auditFinW($model);
                $trans->commit();
                Yii::$app->getSession()->setFlash('success','保存成功');
                return ResultHelper::json(200, '保存成功');
            }catch (\Exception $e){
                $trans->rollBack();
                return ResultHelper::json(422, $e->getMessage());
            }
        }
        return $this->render($this->action->id, [
            'model' => $model,
        ]);
    }
}

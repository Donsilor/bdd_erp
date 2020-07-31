<?php

namespace addons\Warehouse\backend\controllers;

use addons\Warehouse\common\enums\FinAuditStatusEnum;
use addons\Warehouse\common\enums\StoneBillTypeEnum;
use addons\Warehouse\common\forms\WarehouseGoldBillWForm;
use addons\Warehouse\common\forms\WarehouseStoneBillGoodsWForm;
use addons\Warehouse\common\forms\WarehouseStoneBillWForm;
use addons\Warehouse\common\models\WarehouseGoldBillGoodsW;
use addons\Warehouse\common\models\WarehouseStoneBill;
use addons\Warehouse\common\models\WarehouseStoneBillGoods;
use addons\Warehouse\common\models\WarehouseStoneBillGoodsW;
use addons\Warehouse\common\models\WarehouseStoneBillW;
use common\enums\AuditStatusEnum;
use common\helpers\ResultHelper;
use Yii;
use common\traits\Curd;
use common\helpers\Url;
use common\models\base\SearchModel;
use addons\Warehouse\common\enums\GoldBillTypeEnum;
use addons\Warehouse\common\models\WarehouseGoldBillGoods;
use addons\Warehouse\common\forms\WarehouseBillWForm;
use addons\Warehouse\common\enums\PandianStatusEnum;

/**
 * WarehouseBillController implements the CRUD actions for WarehouseBillController model.
 */
class StoneBillWGoodsController extends BaseController
{
    use Curd;
    public $modelClass = WarehouseStoneBillGoods::class;
    public $billType = StoneBillTypeEnum::STONE_W;
    /**
     * Lists all StyleChannel models.
     * @return mixed
     */
    public function actionIndex()
    {
        $bill_id = Yii::$app->request->get('bill_id');
        $tab = Yii::$app->request->get('tab',2);
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['stone-bill-w/index', 'bill_id'=>$bill_id]));
        $bill = WarehouseStoneBill::find()->where(['id'=>$bill_id])->one();
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
                        "actual_grain",
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
        
        $dataProvider->query->andWhere(['=',WarehouseStoneBillGoods::tableName().'.bill_id',$bill_id]);
        $dataProvider->query->andWhere(['>',WarehouseStoneBillGoods::tableName().'.status',PandianStatusEnum::SAVE]);
        
        return $this->render($this->action->id, [
                'dataProvider' => $dataProvider,
                'searchModel' => $searchModel,
                'bill'=>$bill,
                'tab' =>$tab,
                'tabList'=>\Yii::$app->warehouseService->stoneBill->menuTabList($bill_id,$this->billType,$returnUrl),
                'returnUrl'=>$returnUrl
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
        $this->modelClass = new WarehouseStoneBillGoodsWForm();
        $model = $this->findModel($id) ?? new WarehouseStoneBillGoodsWForm();
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
                $model->fin_checker = (string) \Yii::$app->user->identity->getId();

                \Yii::$app->warehouseService->stoneW->auditFinW($model);

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
        $model = new WarehouseStoneBillGoodsWForm();
        $model->ids = $ids;
        //默认值
        if($model->fin_status == FinAuditStatusEnum::PENDING) {
            $model->fin_status = FinAuditStatusEnum::PASS;
        }
        if($check){
            try{
                \Yii::$app->warehouseService->stoneW->auditGoodsValidate($model);
                return ResultHelper::json(200, '', ['url'=>Url::to([$this->action->id, 'ids'=>$ids])]);
            }catch (\Exception $e){
                return ResultHelper::json(422, $e->getMessage());
            }
        }
        if ($model->load(Yii::$app->request->post())) {
            try{
                $trans = Yii::$app->trans->beginTransaction();
                \Yii::$app->warehouseService->stoneW->auditFinW($model);
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

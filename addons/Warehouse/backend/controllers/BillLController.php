<?php

namespace addons\Warehouse\backend\controllers;


use addons\Style\common\enums\LogTypeEnum;
use Yii;
use common\traits\Curd;
use common\models\base\SearchModel;
use common\helpers\ExcelHelper;
use addons\Warehouse\common\models\WarehouseGoods;
use addons\Warehouse\common\models\WarehouseBill;
use addons\Warehouse\common\models\WarehouseBillGoods;
use addons\Warehouse\common\forms\WarehouseBillLForm;
use addons\Warehouse\common\enums\BillStatusEnum;
use addons\Warehouse\common\enums\GoodsStatusEnum;
use addons\Warehouse\common\enums\BillTypeEnum;
use common\enums\AuditStatusEnum;
use common\enums\StatusEnum;
use common\helpers\SnHelper;
use common\helpers\Url;
use yii\db\Exception;


/**
 * WarehouseBillController implements the CRUD actions for WarehouseBillController model.
 */
class BillLController extends BaseController
{

    use Curd;
    public $modelClass  = WarehouseBill::class;
    public $billType    = BillTypeEnum::BILL_TYPE_L;


    /**
     * Lists all StyleChannel models.
     * @return mixed
     */
    public function actionIndex()
    {

        $searchModel = new SearchModel([
            'model' => $this->modelClass,
            'scenario' => 'default',
            'partialMatchAttributes' => ['name'], // 模糊查询
            'defaultOrder' => [
                'id' => SORT_DESC
            ],
            'pageSize' => $this->pageSize,
            'relations' => [
                'creator' => ['username'],
                'auditor' => ['username'],

            ]
        ]);

        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams,['created_at', 'audit_time']);
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
        $dataProvider->query->andWhere(['>',Warehousebill::tableName().'.status',-1]);
        $dataProvider->query->andWhere(['=',Warehousebill::tableName().'.bill_type', $this->billType]);

        //导出
        if(Yii::$app->request->get('action') === 'export'){
            $this->getExport($dataProvider);
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
        $this->modelClass = WarehouseBillLForm::class;
        $model = $this->findModel($id);
        $model = $model ?? new WarehouseBill();
        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(\Yii::$app->request->post())) {
            try{
                $trans = \Yii::$app->db->beginTransaction();
                if($model->isNewRecord){
                    $model->bill_no = SnHelper::createBillSn($this->billType);
                    $model->bill_type = $this->billType;
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
     * @return mixed
     * 提交审核
     */
    public function actionAjaxApply(){
        $id = \Yii::$app->request->get('id');
        $model = $this->findModel($id);
        if($model->bill_status != BillStatusEnum::SAVE){
            return $this->message('单据不是保存状态', $this->redirect(\Yii::$app->request->referrer), 'error');
        }
        $model->bill_status = BillStatusEnum::PENDING;
        if(false === $model->save()){
            return $this->message($this->getError($model), $this->redirect(\Yii::$app->request->referrer), 'error');
        }
        return $this->message('操作成功', $this->redirect(\Yii::$app->request->referrer), 'success');

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
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['bill-l/index']));
        $model = $this->findModel($id);
        return $this->render($this->action->id, [
            'model' => $model,
            'tab'=>$tab,
            'tabList'=>\Yii::$app->warehouseService->bill->menuTabList($id, $this->billType, $returnUrl),
            'returnUrl'=>$returnUrl,
        ]);
    }


    /**
     * ajax收货单审核
     *
     * @return mixed|string|\yii\web\Response
     * @throws \yii\base\ExitException
     */
    public function actionAjaxAudit()
    {
        $id = Yii::$app->request->get('id');
        $this->modelClass = WarehouseBillLForm::class;
        $model = $this->findModel($id);
        $model = $model ?? new WarehouseBill();
        $billGoodsModel = new WarehouseBillGoods();
        $goodsModel = new WarehouseGoods();
        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {
            try{
                $trans = Yii::$app->trans->beginTransaction();
                $model->audit_time = time();
                $model->auditor_id = Yii::$app->user->identity->getId();
                if($model->audit_status == AuditStatusEnum::PASS){
                    $model->status = StatusEnum::ENABLED;
                    $model->bill_status = BillStatusEnum::CONFIRM;
                }else{
                    $model->status = StatusEnum::DISABLED;
                    $model->bill_status = BillStatusEnum::SAVE;
                }
                if(false === $model->save()) {
                    throw new \Exception($this->getError($model));
                }
                $billGoods = $billGoodsModel::find()->select('goods_id')->where(['bill_id' => $id])->asArray()->all();
                if(empty($billGoods)){
                    throw new \Exception("单据明细不能为空");
                }
                $goods_ids = array_column($billGoods, 'goods_id');
                $condition = ['goods_status' => GoodsStatusEnum::RECEIVING, 'goods_id' => $goods_ids];
                $goods_status = $model->audit_status == AuditStatusEnum::PASS ? GoodsStatusEnum::IN_STOCK : GoodsStatusEnum::CANCEL;
                $res = $goodsModel::updateAll([
                    'goods_status' => $goods_status,
                    'put_in_type' => $model->put_in_type,
                    'warehouse_id' => $model->to_warehouse_id],
                    $condition
                );
                if(false === $res) {
                    throw new \Exception($this->getError($goodsModel));
                }
                $trans->commit();
                return $this->message("保存成功", $this->redirect(Yii::$app->request->referrer), 'success');
            }catch (\Exception $e){
                $trans->rollBack();
                return $this->message("审核失败:". $e->getMessage(),  $this->redirect(Yii::$app->request->referrer), 'error');
            }
        }
        $model->audit_status = AuditStatusEnum::PASS;
        return $this->renderAjax($this->action->id, [
            'model' => $model,
        ]);
    }


    /**
     * 删除/关闭/取消
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
            $model->bill_status = BillStatusEnum::CANCEL;
            //更新库存状态
            $billGoods = WarehouseBillGoods::find()->where(['bill_id' => $id])->select(['goods_id'])->all();
            foreach ($billGoods as $goods){
                $res = WarehouseGoods::updateAll(['goods_status' => GoodsStatusEnum::CANCEL],['goods_id' => $goods->goods_id, 'goods_status' => GoodsStatusEnum::RECEIVING]);
                if(!$res){
                    throw new Exception("商品{$goods->goods_id}不是收货中或者不存在，请查看原因");
                }
            }
            if(false === $model->save()){
                throw new \Exception($this->getError($model));
            }

            //日志
            $log = [
                'bill_id' => $model->id,
                'log_type' => LogTypeEnum::ARTIFICIAL,
                'log_module' => '收货单',
                'log_msg' => '取消单据'
            ];
            \Yii::$app->warehouseService->bill->createWarehouseBillLog($log);
            \Yii::$app->getSession()->setFlash('success','取消成功');
            $trans->commit();
            return $this->redirect(\Yii::$app->request->referrer);
        }catch (\Exception $e){
            $trans->rollBack();
            return $this->message($e->getMessage(), $this->redirect(\Yii::$app->request->referrer), 'error');
        }

        return $this->message("取消失败", $this->redirect(['index']), 'error');
    }


    /**
     * 列表导出
     *
     * @return mixed|string|\yii\web\Response
     * @throws \yii\base\ExitException
     */
    public function getExport($dataProvider)
    {
        $list = $dataProvider->models;
        $header = [
            ['ID', 'id'],
            ['渠道名称', 'name', 'text'],
        ];
        return ExcelHelper::exportData($list, $header, '数据导出_' . time());

    }


}

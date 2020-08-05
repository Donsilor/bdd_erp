<?php

namespace addons\Warehouse\backend\controllers;

use addons\Style\common\enums\LogTypeEnum;
use addons\Warehouse\common\forms\WarehouseGoodsForm;
use addons\Warehouse\common\models\WarehouseGoods;
use common\enums\AuditStatusEnum;
use common\enums\ConfirmEnum;
use common\helpers\ExcelHelper;
use common\helpers\ResultHelper;
use common\helpers\Url;
use Yii;
use common\traits\Curd;
use common\models\base\SearchModel;


/**
 * StyleChannelController implements the CRUD actions for StyleChannel model.
 */
class WarehouseGoodsController extends BaseController
{
    use Curd;
    public $modelClass = WarehouseGoods::class;
    /**
     * Lists all StyleChannel models.
     * @return mixed
     */
    public function actionIndex()
    {

        $searchModel = new SearchModel([
            'model' => $this->modelClass,
            'scenario' => 'default',
            'partialMatchAttributes' => ['goods_name'], // 模糊查询
            'defaultOrder' => [
                'id' => SORT_DESC
            ],
            'pageSize' => $this->getPageSize(),
            'relations' => [
                'productType' => ['name','is_inlay'],
                'styleCate' => ['name'],
                'supplier' => ['supplier_name'],
                'warehouse' => ['name'],
                'weixiuWarehouse' => ['name'],
                'creator' => ['username'],

            ]
        ]);

        $dataProvider = $searchModel
            ->search(Yii::$app->request->queryParams,['updated_at']);

        $created_at = $searchModel->created_at;
        if (!empty($created_at)) {
            $dataProvider->query->andFilterWhere(['>=',WarehouseGoods::tableName().'.created_at', strtotime(explode('/', $created_at)[0])]);//起始时间
            $dataProvider->query->andFilterWhere(['<',WarehouseGoods::tableName().'.created_at', (strtotime(explode('/', $created_at)[1]) + 86400)] );//结束时间
        }


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
     * 详情展示页
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView()
    {
        $id = Yii::$app->request->get('id');
        $goods_id = Yii::$app->request->get('goods_id');
        $tab = Yii::$app->request->get('tab',1);
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['warehouser-goods/index']));
        if(empty($id) && !empty($goods_id)){
            $goodsInfo = WarehouseGoods::find()->where(['goods_id'=>$goods_id])->asArray()->one();
            $id = $goodsInfo['id']??0;
        }
        $model = $this->findModel($id);
        return $this->render($this->action->id, [
            'model' => $model,
            'tab'=>$tab,
            'tabList'=>\Yii::$app->warehouseService->warehouseGoods->menuTabList($id,$returnUrl),
            'returnUrl'=>$returnUrl,
        ]);
    }

    /**
     * @return array|mixed|string
     * WarehouseGoodsForm $model
     */
    public function actionEdit(){
        $this->layout = '@backend/views/layouts/iframe';
        $id = Yii::$app->request->get('id', null);
        $this->modelClass = WarehouseGoodsForm::class;
        $model = $this->findModel($id);
        $old_model = clone $model;
        $model = $model ?? new WarehouseGoodsForm();
        if ($model->load(Yii::$app->request->post())) {
            if(!$model->validate()) {
                return ResultHelper::json(422, $this->getError($model));
            }
            try{
                $trans = Yii::$app->trans->beginTransaction();

                $data = \Yii::$app->request->post('WarehouseGoodsForm');
                $model->apply_info = json_encode($data);
                $model->is_apply = ConfirmEnum::YES;
                $model->audit_status = AuditStatusEnum::SAVE;
                $model->apply_id = \Yii::$app->user->identity->getId();
                if(false === $model->save(true,['apply_id', 'is_apply', 'apply_info','audit_status'])) {
                    throw new \Exception("保存失败",500);
                }

                //日志
                $log_msg = '申请修改；';
                foreach ($data as $k=>$val) {
                    $old = $old_model->$k;
                    if($old != $val){
                        $log_msg .= "{$model->getAttributeLabel($k)} 由 ({$old}) 改成 ({$val})";
                    }
                }
                $log = [
                    'goods_id' => $model->id,
                    'log_type' => LogTypeEnum::ARTIFICIAL,
                    'log_msg' => $log_msg
                ];
                \Yii::$app->warehouseService->warehouseGoods->createWarehouseGoodsLog($log);


                $trans->commit();
                //前端提示
                return ResultHelper::json(200, '操作成功');
            }catch (\Exception $e){
                $trans->rollBack();
                return ResultHelper::json(422, $e->getMessage());
            }
        }
        $model->initApplyEdit();
        return $this->render($this->action->id, [
            'model' => $model,
        ]);
    }

    /**
     * @return mixed
     * 申请审核
     */
    public function actionAjaxApply(){
        $id = \Yii::$app->request->get('id');
        $model = $this->findModel($id);
        if($model->audit_status != AuditStatusEnum::SAVE){
            return $this->message('单据不是保存状态', $this->redirect(\Yii::$app->request->referrer), 'error');
        }
        $model->audit_status = AuditStatusEnum::PENDING;
        if(false === $model->save()){
            return $this->message($this->getError($model), $this->redirect(\Yii::$app->request->referrer), 'error');
        }
        return $this->message('操作成功', $this->redirect(\Yii::$app->request->referrer), 'success');

    }

    /**
     * 查看审批
     * @property PurchaseGoodsForm $model
     * @return mixed
     */
    public function actionApplyView()
    {

        $id = Yii::$app->request->get('id');
        $this->modelClass = WarehouseGoodsForm::class;
        $model = $this->findModel($id);
        $model = $model ?? new WarehouseGoodsForm();
        $model->initApplyView();

        return $this->render($this->action->id, [
            'model' => $model,
            'returnUrl'=>$this->returnUrl
        ]);
    }
    /**
     * 申请编辑-审核(ajax)
     * @property PurchaseGoodsForm $model
     * @return mixed
     */
    public function actionApplyAudit()
    {

        $returnUrl = Yii::$app->request->get('returnUrl',Yii::$app->request->referrer);

        $id = Yii::$app->request->get('id');

        $this->modelClass = WarehouseGoodsForm::class;
        $model = $this->findModel($id);
        $model = $model ?? new WarehouseGoodsForm();
        $old_model = clone $model;

        $model->audit_status = AuditStatusEnum::PASS;
        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {
            try {
                $trans = Yii::$app->trans->beginTransaction();
                if($model->audit_status == AuditStatusEnum::PASS){
                    $model->initApplyEdit();
                    //日志
                    $log_msg = '审核通过；';
                    $data = json_decode($model->apply_info,true) ?? [];
                    foreach ($data as $k=>$val) {
                        $old = $old_model->$k;
                        if($old != $val){
                            $log_msg .= "{$model->getAttributeLabel($k)} 由 ({$old}) 改成 ({$val})";
                        }
                    }
                    $log = [
                        'goods_id' => $model->id,
                        'log_type' => LogTypeEnum::ARTIFICIAL,
                        'log_msg' => $log_msg
                    ];
                    \Yii::$app->warehouseService->warehouseGoods->createWarehouseGoodsLog($log);
                }
                $model->apply_id = '';
                $model->apply_info = '';
                $model->is_apply = ConfirmEnum::NO;
                $model->save(false);
                $trans->commit();
                return $this->message("操作成功", $this->redirect(['warehouse-goods/view','id'=>$id]), 'success');
            }catch (\Exception $e){
                $trans->rollback();
                return $this->message($e->getMessage(), $this->redirect($returnUrl), 'error');
            }

        }
        return $this->renderAjax($this->action->id, [
            'model' => $model,
        ]);
    }



    public function actionEditss()
    {
        $this->layout = '@backend/views/layouts/iframe';
        $id = Yii::$app->request->get('id', null);
        $model = $this->findModel($id);
        $old_model = clone $model;
        if ($model->load(Yii::$app->request->post())) {
            try{
                $trans = Yii::$app->db->beginTransaction();
                $param = Yii::$app->request->post('WarehouseGoods');
                foreach ($param as $key=>$new){
                    $old = $old_model->$key;
                    if($old != $new){
                        $log_msg = "{$model->getAttributeLabel($key)} 由 ({$old}) 改成 ({$new})";
                        $log = [
                            'goods_id' => $model->id,
                            'log_type' => LogTypeEnum::ARTIFICIAL,
                            'log_msg' => $log_msg
                        ];
                        Yii::$app->warehouseService->warehouseGoods->createWarehouseGoodsLog($log);
                    }
                }
                $model->save();
                $trans->commit();
                Yii::$app->getSession()->setFlash('success','保存成功');
                return $this->redirect(Yii::$app->request->referrer);
            }catch(\Exception $e){
                $trans->rollBack();
                return $this->message($e->getMessage(), $this->redirect(Yii::$app->request->referrer), 'error');
            }
        }

        return $this->render($this->action->id, [
            'model' => $model,
        ]);
    }


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

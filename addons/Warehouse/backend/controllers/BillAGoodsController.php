<?php

namespace addons\Warehouse\backend\controllers;

use addons\Style\common\enums\LogTypeEnum;
use addons\Warehouse\common\enums\GoodsStatusEnum;
use addons\Warehouse\common\forms\WarehouseBillAForm;
use addons\Warehouse\common\forms\WarehouseBillAGoodsForm;
use addons\Warehouse\common\models\WarehouseBillGoods;
use addons\Warehouse\common\models\WarehouseBillGoodsA;
use addons\Warehouse\common\models\WarehouseGoods;
use common\enums\AuditStatusEnum;
use Yii;
use common\traits\Curd;
use common\helpers\Url;
use common\models\base\SearchModel;
use addons\Warehouse\common\models\WarehouseBill;
use addons\Warehouse\common\enums\BillTypeEnum;
use common\helpers\ResultHelper;
use yii\base\Exception;

/**
 * WarehouseBillGoodsController implements the CRUD actions for WarehouseBillGoodsController model.
 */
class BillAGoodsController extends BaseController
{
    use Curd;
    public $modelClass = WarehouseBillGoodsA::class;
    public $billType = BillTypeEnum::BILL_TYPE_A;
    /**
     * Lists all WarehouseBillGoods models.
     * @return mixed
     */
    public function actionIndex()
    {
        $bill_id = Yii::$app->request->get('bill_id');
        $tab = Yii::$app->request->get('tab',2);
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['bill-a/index']));
        $searchModel = new SearchModel([
            'model' => $this->modelClass,
            'scenario' => 'default',
            'partialMatchAttributes' => [], // 模糊查询
            'defaultOrder' => [
                'id' => SORT_DESC
            ],
            'pageSize' => $this->pageSize,
            'relations' => [
                'goods'=>['style_sn','product_type_id','style_cate_id','style_sex','produce_sn','material','jintuo_type']
            ]
        ]);

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['=', 'bill_id', $bill_id]);
        $bill = WarehouseBill::find()->where(['id'=>$bill_id])->one();
        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'bill' => $bill,
            'tabList'=>\Yii::$app->warehouseService->bill->menuTabList($bill_id, $this->billType, $returnUrl),
            'tab' => $tab,
        ]);
    }

    /**
     * ajax添加商品
     *
     * @return mixed|string|\yii\web\Response
     * @throws \yii\base\ExitException
     */
    public function actionAjaxEdit()
    {
        $id = \Yii::$app->request->get('id');
        $bill_id = Yii::$app->request->get('bill_id');
        $this->modelClass = WarehouseBillAGoodsForm::class;
        $model = $this->findModel($id);
        $model = $model ?? new WarehouseBillAGoodsForm();
        if ($model->load(\Yii::$app->request->post())) {
            $model->bill_id = $bill_id;
            // ajax 校验
            $this->activeFormValidate($model);
            try{
                $trans = \Yii::$app->db->beginTransaction();
                //字符串转数组
                Yii::$app->warehouseService->billA->addBillGoods($model);
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
            'bill_id' => $bill_id
        ]);
    }

    /**
     * ajax编辑
     *
     * @return mixed|string|\yii\web\Response
     * @throws \yii\base\ExitException
     */
    public function actionEdit()
    {
        $this->layout = '@backend/views/layouts/iframe';

        $id = \Yii::$app->request->get('id');
        //$bill_id = Yii::$app->request->get('bill_id');
        $model = $this->findModel($id);
        if ($model->load(\Yii::$app->request->post())) {
            // ajax 校验
            $model->audit_status = AuditStatusEnum::SAVE;
            if(false === $model->save()) {
                return ResultHelper::json(422, $this->getError($model));
            }
            Yii::$app->getSession()->setFlash('success','保存成功');
            return $this->redirect(Yii::$app->request->referrer);
        }
        return $this->render($this->action->id, [
            'model' => $model,
        ]);
    }

    /**
     * ajax批量编辑
     *
     * @return mixed|string|\yii\web\Response
     * @throws \yii\base\ExitException
     */
    public function actionBatchEdit()
    {
        $this->layout = '@backend/views/layouts/iframe';

        $ids = Yii::$app->request->post('ids');
        $ids = $ids ?? Yii::$app->request->get('ids');
        $model = new WarehouseBillAGoodsForm();
        $model->ids = $ids;
        $id_arr = $model->getIds();
        if(!$id_arr){
            return ResultHelper::json(422, "ID不能为空");
        }
        $name = Yii::$app->request->post('name');
        $name = $name ?? Yii::$app->request->get('name');
        if(!$name){
            return ResultHelper::json(422, "字段错误");
        }
        if(Yii::$app->request->isPost){
            $value = Yii::$app->request->post('value');
            if(!$value){
                return ResultHelper::json(422, "输入值不能为空");
            }
            try{
                $trans = Yii::$app->trans->beginTransaction();
                foreach ($id_arr as $id) {
                    $goods = WarehouseBillGoodsA::findOne(['id'=>$id]);
                    $goods->$name = $value;
                    if(false === $goods->validate()) {
                        throw new \Exception($this->getError($goods));
                    }
                    $goods->audit_status = AuditStatusEnum::SAVE;
                    if(false === $goods->save()) {
                        throw new \Exception($this->getError($goods));
                    }
                }
                $trans->commit();
                Yii::$app->getSession()->setFlash('success','保存成功');
                return ResultHelper::json(200, '保存成功');
            }catch (\Exception $e){
                $trans->rollBack();
                return ResultHelper::json(422, $e->getMessage());
            }
        }
        $attr_id = Yii::$app->request->get('attr_id',0);
        if(!$attr_id){
            return ResultHelper::json(422, '参数错误');
        }
        $check = Yii::$app->request->get('check',null);
        if($check){
            return ResultHelper::json(200, '', ['url'=>'/warehouse/bill-a-goods/batch-edit?ids='.$ids.'&name='.$name."&attr_id=".$attr_id]);
        }
        $style_arr = $model::find()->where(['id'=>$id_arr])->select(['style_sn'])->asArray()->distinct('style_sn')->all();
        if(count($style_arr) != 1){
            return ResultHelper::json(422, '请选择同款的商品进行操作');
        }
        $style_sn = $style_arr[0]['style_sn']??"";
        $attr_arr = Yii::$app->styleService->styleAttribute->getAttrValueListByStyle($style_sn,$attr_id);
        return $this->render($this->action->id, [
            'model' => $model,
            'ids' => $ids,
            'name'=> $name,
            'attr_arr' =>$attr_arr
        ]);

    }

    /**
     * 调整单-编辑
     * @return mixed
     */
    public function actionEditAll()
    {

        $bill_id = Yii::$app->request->get('bill_id');
        $tab = Yii::$app->request->get('tab',2);
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['bill-a-goods/index']));
        $searchModel = new SearchModel([
            'model' => $this->modelClass,
            'scenario' => 'default',
            'partialMatchAttributes' => [], // 模糊查询
            'defaultOrder' => [
                'id' => SORT_DESC
            ],
            'pageSize' => $this->pageSize,
            'relations' => [
                'goods'=>['style_sn','product_type_id','style_cate_id','style_sex','produce_sn','material','jintuo_type']
            ]
        ]);

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['=', 'bill_id', $bill_id]);
        $bill = WarehouseBill::find()->where(['id'=>$bill_id])->one();
        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'bill' => $bill,
            'tabList'=>\Yii::$app->warehouseService->bill->menuTabList($bill_id, $this->billType, $returnUrl, $tab),
            'tab' => $tab,
        ]);
    }


    /**
     * @return mixed
     * 申请审核
     */
    public function actionAjaxApply(){
        $id = \Yii::$app->request->get('id');
        $model = $this->findModel($id);
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
        $returnUrl = Yii::$app->request->get('returnUrl',Yii::$app->request->referrer);
        $id = Yii::$app->request->get('id');
        $this->modelClass = WarehouseBillAGoodsForm::class;
        $model = $this->findModel($id);
        $model = $model ?? new WarehouseBillAGoodsForm();
        $model->initApplyView();

        return $this->render($this->action->id, [
            'model' => $model,
            'returnUrl'=>$returnUrl
        ]);
    }


    /**
     * 申请编辑-审核(ajax)
     * @property PurchaseGoodsForm $model
     * @return mixed
     */
    public function actionApplyAudit()
    {

        $id = Yii::$app->request->get('id');

        $this->modelClass = WarehouseBillAGoodsForm::class;
        $model = $this->findModel($id);
        $model = $model ?? new WarehouseBillAGoodsForm();
        $goods = WarehouseGoods::find()->where(['goods_id'=>$model->goods_id])->one();

        $model->audit_status = AuditStatusEnum::PASS;
        if ($model->load(Yii::$app->request->post())) {
            try {
                $trans = Yii::$app->trans->beginTransaction();

                $model->auditor_id = Yii::$app->user->identity->getId();
                $model->audit_time = time();
                $model->save(true,['auditor_id','audit_status','audit_time','audit_remark']);
                if($model->audit_status == AuditStatusEnum::PASS){
                    $model->initSynch();
                    //日志
                    $log_msg = '审核通过；货号'.$model->goods_id;
                    $fields = $model->getMap();
                    foreach ($fields as $bill_goods_field => $goods_field) {
                        $val = $model -> $bill_goods_field;
                        $old = $goods -> $goods_field;
                        if($old != $val){
                            $log_msg .= "{$model->getAttributeLabel($bill_goods_field)} 由 ({$old}) 改成 ({$val})";
                        }
                    }
                    $log = [
                        'bill_id' => $model->id,
                        'log_type' => LogTypeEnum::ARTIFICIAL,
                        'log_module' => '调整单',
                        'log_msg' => $log_msg
                    ];
                    \Yii::$app->warehouseService->billLog->realCreateBillLog($log);
                }

                $trans->commit();
                $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['bill-a-goods/index','bill_id'=>$model->bill_id,'tab'=>2]));
                return $this->message("操作成功", $this->redirect($returnUrl), 'success');
            }catch (\Exception $e){
                $trans->rollback();
                return $this->message($e->getMessage(), $this->redirect(Yii::$app->request->referrer), 'error');
            }

        }
        return $this->renderAjax($this->action->id, [
            'model' => $model,
        ]);
    }

    /**
     * 删除/关闭
     *
     * @param $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        if (!($model = $this->modelClass::findOne($id))) {
            return $this->message("找不到数据", $this->redirect(['index']), 'error');
        }
        $bill_id = $model->bill_id;
        $goods_id = $model->goods_id;
        try{
            $trans = \Yii::$app->db->beginTransaction();
            //删除单据明细信息
            $bill_goods_model = WarehouseBillGoods::find()->where(['bill_id'=>$bill_id,'goods_id'=>$goods_id])->one();
            if(false === $bill_goods_model->delete()){
                throw new \Exception($this->getError($model));
            }

            if(false === $model->delete()){
                throw new \Exception($this->getError($model));
            }
            //更新收货单汇总：总金额和总数量
            $res = \Yii::$app->warehouseService->bill->warehouseBillSummary($bill_id);
            if(false === $res){
                throw new \yii\db\Exception('更新单据汇总失败');
            }

            //更新库存表商品状态为库存
            $res = WarehouseGoods::updateAll(['goods_status'=>GoodsStatusEnum::IN_STOCK],['goods_id'=>$goods_id,'goods_status'=>GoodsStatusEnum::IN_ADJUS]);
            if($res == 0){
                throw new Exception("商品不是调整中或者不存在，请查看原因");
            }

            $trans->commit();
            \Yii::$app->getSession()->setFlash('success','删除成功');
            return $this->redirect(\Yii::$app->request->referrer);
        }catch (\Exception $e){
            $trans->rollBack();
            return $this->message($e->getMessage(), $this->redirect(\Yii::$app->request->referrer), 'error');
        }
    }
}

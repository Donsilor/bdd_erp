<?php

namespace addons\Gdzb\backend\controllers;

use addons\Gdzb\common\enums\RefundStatusEnum;
use addons\Gdzb\common\forms\RefundGoodsForm;
use addons\Gdzb\common\models\Goods;
use addons\Gdzb\common\models\OrderRefund;
use addons\Gdzb\common\models\RefundGoods;
use addons\Warehouse\common\enums\GoodsStatusEnum;
use common\enums\AuditStatusEnum;
use common\enums\ConfirmEnum;
use common\enums\LogTypeEnum;
use Yii;
use common\models\base\SearchModel;
use addons\Gdzb\common\models\Customer;
use common\traits\Curd;


/**
 * 客户管理
 *
 * Class CustomerController
 * @package addons\Sales\backend\controllers
 */
class OrderRefundController extends BaseController
{
    use Curd;

    /**
     * @var Customer
     */
    public $modelClass = OrderRefund::class;

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
                'order' => ['order_sn'],
                'customer' => ['wechat'],
            ]
        ]);

        $dataProvider = $searchModel
            ->search(Yii::$app->request->queryParams,['created_at']);

        $created_at = $searchModel->created_at;
        if (!empty($created_at)) {
            $dataProvider->query->andFilterWhere(['>=',Goods::tableName().'.created_at', strtotime(explode('/', $created_at)[0])]);//起始时间
            $dataProvider->query->andFilterWhere(['<',Goods::tableName().'.created_at', (strtotime(explode('/', $created_at)[1]) + 86400)] );//结束时间
        }

        //$dataProvider->query->andWhere(['>',Customer::tableName().'.status',-1]);

        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }



    /**
     * 申请审核
     * @return mixed
     */
    public function actionAjaxApply(){

        $id = \Yii::$app->request->get('id');
        $model = $this->findModel($id);

        if($model->refund_status != RefundStatusEnum::SAVE){
            return $this->message('单据不是保存状态', $this->redirect($this->returnUrl), 'error');
        }
        try{
            $trans = Yii::$app->db->beginTransaction();
            $model->refund_status = RefundStatusEnum::PENDING;
            $model->audit_status = AuditStatusEnum::PENDING;
            if(false === $model->save()){
                throw new \Exception($this->getError($model));
            }
            //日志
            $log = [
                'refund_id' => $model->id,
                'refund_sn' => $model->refund_sn,
                'log_type' => LogTypeEnum::ARTIFICIAL,
                'log_module' => "申请审核",
                'log_msg' => "退货单提交申请",
            ];
            Yii::$app->gdzbService->refundLog->createRefundLog($log);
            $trans->commit();
            return $this->message('操作成功', $this->redirect(Yii::$app->request->referrer), 'success');
        }catch (\Exception $e){
            $trans->rollBack();
            return $this->message($e->getMessage(), $this->redirect(Yii::$app->request->referrer), 'error');
        }
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
        $model = $this->findModel($id);
        $model->audit_status = AuditStatusEnum::PASS;
        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {
            try{
                $trans = Yii::$app->db->beginTransaction();
                $model->audit_time = time();
                $model->auditor_id = \Yii::$app->user->identity->id;
                $model->refund_status = RefundStatusEnum::CONFIRM;
                if($model->audit_status == AuditStatusEnum::PASS){
                    Yii::$app->gdzbService->orderRefund->syncAuditPass($model);
                }else{
                    Yii::$app->gdzbService->orderRefund->syncAuditNoPass($model);
                }
                if(false === $model->save()){
                    throw new \Exception($this->getError($model));
                }
                //日志
                $log = [
                    'refund_id' => $model->id,
                    'refund_sn' => $model->refund_sn,
                    'log_type' => LogTypeEnum::ARTIFICIAL,
                    'log_module' => "单据审核",
                    'log_msg' => "退货单审核,审核状态：".AuditStatusEnum::getValue($model->audit_status).",审核备注：".$model->audit_remark
                ];
                Yii::$app->gdzbService->refundLog->createRefundLog($log);

                $trans->commit();
                Yii::$app->getSession()->setFlash('success','保存成功');
                return $this->redirect(Yii::$app->request->referrer);
            }catch (\Exception $e){
                $trans->rollBack();
                return $this->message($e->getMessage(), $this->redirect(Yii::$app->request->referrer), 'error');
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
        $model = $this->findModel($id);
        $dataProvider = null;
        if (!is_null($id)) {
            $searchModel = new SearchModel([
                'model' => RefundGoods::class,
                'scenario' => 'default',
                'partialMatchAttributes' => [], // 模糊查询
                'defaultOrder' => [
                    'id' => SORT_DESC
                ],
                'pageSize' => 1000,
            ]);

            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
            $dataProvider->query->andWhere(['=', 'refund_id', $id]);
            $dataProvider->setSort(false);
        }
        return $this->render($this->action->id, [
            'model' => $model,
            'dataProvider' => $dataProvider,
            'tab'=>Yii::$app->request->get('tab',1),
            'tabList'=>Yii::$app->gdzbService->orderRefund->menuTabList($id,$this->returnUrl),
            'returnUrl'=>$this->returnUrl,
        ]);
    }


    /**
     * 是否返厂
     * @return mixed
     */
    public function actionAjaxFactory(){
            $id = Yii::$app->request->get('id');
            $this->modelClass = RefundGoodsForm::class;
            $model = $this->findModel($id);
            $refund = OrderRefund::find()->where(['id'=>$model->refund_id])->one();
            // ajax 校验
            $this->activeFormValidate($model);
            if ($model->load(Yii::$app->request->post())) {
                try{
                    $trans = Yii::$app->db->beginTransaction();
                    if($model->is_factory == ConfirmEnum::YES){
                        Goods::updateAll(['goods_status'=>GoodsStatusEnum::HAS_RETURN_FACTORY],['goods_sn'=>$model->goods_sn]);
                    }else{
                        Goods::updateAll(['goods_status'=>GoodsStatusEnum::IN_STOCK],['goods_sn'=>$model->goods_sn]);
                    }

                    if(false === $model->save()){
                        throw new \Exception($this->getError($model));
                    }
                    //日志
                    $log = [
                        'refund_id' => $refund->id,
                        'refund_sn' => $refund->refund_sn,
                        'log_type' => LogTypeEnum::ARTIFICIAL,
                        'log_module' => "是否返厂",
                        'log_msg' => "是否返厂：".ConfirmEnum::getValue($model->is_factory).",备注：".$model->factory_remark
                    ];
                    Yii::$app->gdzbService->refundLog->createRefundLog($log);

                    $trans->commit();
                    Yii::$app->getSession()->setFlash('success','保存成功');
                    return $this->redirect(Yii::$app->request->referrer);
                }catch (\Exception $e){
                    $trans->rollBack();
                    return $this->message($e->getMessage(), $this->redirect(Yii::$app->request->referrer), 'error');
                }

            }
            return $this->renderAjax($this->action->id, [
                'model' => $model,
            ]);
        }

}
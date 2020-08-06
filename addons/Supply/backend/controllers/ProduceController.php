<?php

namespace addons\Supply\backend\controllers;

use addons\Style\common\enums\InlayEnum;
use addons\Supply\common\enums\BuChanEnum;
use addons\Supply\common\enums\FromTypeEnum;
use addons\Supply\common\enums\LogModuleEnum;
use addons\Supply\common\enums\NopassReasonEnum;
use addons\Supply\common\enums\PeiliaoTypeEnum;
use addons\Supply\common\enums\PeishiTypeEnum;
use addons\Supply\common\forms\ProduceFollowerForm;
use addons\Supply\common\forms\SetPeiliaoForm;
use addons\Supply\common\forms\ToFactoryForm;
use addons\Supply\common\models\Produce;
use addons\Supply\common\models\ProduceAttribute;
use addons\Supply\common\models\ProduceOqc;
use addons\Supply\common\models\ProduceShipment;
use addons\Supply\common\models\Supplier;
use addons\Supply\common\models\SupplierFollower;
use common\enums\AuditStatusEnum;
use common\enums\LogTypeEnum;
use common\enums\StatusEnum;
use common\helpers\ArrayHelper;
use common\helpers\ResultHelper;
use common\helpers\SnHelper;
use common\helpers\Url;
use common\models\backend\Member;
use common\models\base\SearchModel;
use common\traits\Curd;
use Yii;
use common\controllers\AddonsController;
use addons\Supply\common\enums\PeiliaoStatusEnum;
use addons\Supply\common\enums\PeishiStatusEnum;

/**
 * 默认控制器
 *
 * Class DefaultController
 * @package addons\Supply\backend\controllers
 */
class ProduceController extends BaseController
{
    use Curd;

    /**
     * @var Attribute
     */
    public $modelClass = Produce::class;
    /**
    * 首页
    *
    * @return string
    */
    public function actionIndex()
    {
        $searchModel = new SearchModel([
            'model' => $this->modelClass,
            'scenario' => 'default',
            'partialMatchAttributes' => ['follower_name'], // 模糊查询
            'defaultOrder' => [
                'id' => SORT_DESC
            ],
            'pageSize' => $this->pageSize,
            'relations' => [
                
            ]
        ]);

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams,['factory_distribute_time','factory_order_time','factory_delivery_time']);

        $factory_distribute_time = $searchModel->factory_distribute_time ?? '';
        if (count($factory_distribute_times = explode('/', $factory_distribute_time)) ==2) {
            $dataProvider->query->andFilterWhere(['>=',Produce::tableName().'.factory_distribute_time', strtotime($factory_distribute_times[0])]);//起始时间
            $dataProvider->query->andFilterWhere(['<',Produce::tableName().'.factory_distribute_time', (strtotime($factory_distribute_times[1]) + 86400)] );//结束时间
        }

        $factory_order_time = $searchModel->factory_order_time ?? '';
        if (count($factory_order_times = explode('/', $searchModel->factory_order_time)) ==2 ) {
            $dataProvider->query->andFilterWhere(['>=',Produce::tableName().'.factory_order_time', strtotime($factory_order_times[0])]);//起始时间
            $dataProvider->query->andFilterWhere(['<',Produce::tableName().'.factory_order_time', (strtotime($factory_order_times[1]) + 86400)] );//结束时间
        }

        $factory_delivery_time = $searchModel->factory_delivery_time ?? '';
        if (count($factory_delivery_times = explode('/', $factory_delivery_time)) ==2) {
            $dataProvider->query->andFilterWhere(['>=',Produce::tableName().'.factory_delivery_time', strtotime($factory_delivery_times[0])]);//起始时间
            $dataProvider->query->andFilterWhere(['<',Produce::tableName().'.factory_delivery_time', (strtotime($factory_delivery_times[1]) + 86400)] );//结束时间
        }

        return $this->render('index', [
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
        $tab = Yii::$app->request->get('tab',1);
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['produce/index']));

        $produce_attr = ProduceAttribute::find()->where(['produce_id'=>$id])->all();

        $model = $this->findModel($id);
        return $this->render($this->action->id, [
            'model' => $model,
            'produce_attr' => $produce_attr,
            'tab'=>$tab,
            'tabList'=>\Yii::$app->supplyService->produce->menuTabList($id,$returnUrl),
            'returnUrl'=>$returnUrl,
        ]);
    }


    //分配工厂
    public function actionToFactory(){
        $id = Yii::$app->request->get('id');
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['produce/index']));
        $this->modelClass = ToFactoryForm::class;
        $model = $this->findModel($id);
        $this->activeFormValidate($model);
        $supplier = Yii::$app->supplyService->supplier->getDropDown();
        if ($model->load(Yii::$app->request->post())) {
            if($model->bc_status != BuChanEnum::INITIALIZATION && $model->bc_status != BuChanEnum::TO_CONFIRMED){
                return $this->message('不是'.BuChanEnum::getValue(BuChanEnum::INITIALIZATION).'/'.BuChanEnum::getValue(BuChanEnum::TO_CONFIRMED).'，不能操作', $this->redirect(Yii::$app->request->referrer), 'warning');
            }
            $model->factory_distribute_time = time();
            $model->bc_status = BuChanEnum::TO_CONFIRMED;
            $model->follower_name = $model->follower->username;
            if(false === $model->save()){
                return $this->message($this->getError($model), $this->redirect(Yii::$app->request->referrer), 'error');
            }

            //日志
            $follower = SupplierFollower::find()->where(['id'=>$model->follower_id])->one();
            $log = [
                'produce_id' => $id,
                'produce_sn' => $model->produce_sn,
                'log_type' => LogTypeEnum::ARTIFICIAL,
                'bc_status' => $model->bc_status,
                'log_module' => LogModuleEnum::getValue(LogModuleEnum::TO_FACTORY),
                'log_msg' => "布产单{$model->produce_sn}分配到供应商{$supplier[$model->supplier_id]}生产，跟单人是{$follower->member_name}"
            ];
            Yii::$app->supplyService->produce->createProduceLog($log);
            Yii::$app->getSession()->setFlash('success','保存成功');
            return $this->redirect(Yii::$app->request->referrer);



        }
        return $this->renderAjax($this->action->id, [
            'model' => $model,
            'supplier' => $supplier,
            'returnUrl' => $returnUrl
        ]);

    }

    //确认分配
    public function actionToConfirmed(){
        $id = Yii::$app->request->get('id');
        $model = $this->findModel($id);
        // ajax 校验
        $this->activeFormValidate($model);
        if($model->bc_status != BuChanEnum::TO_CONFIRMED){
            return $this->message('不是'.BuChanEnum::getValue(BuChanEnum::TO_CONFIRMED).'，不能操作', $this->redirect(Yii::$app->request->referrer), 'warning');
        }
        $model->bc_status = BuChanEnum::ASSIGNED;
        if(false === $model->save()){
            $this->message($this->getError($model), $this->redirect(Yii::$app->request->referrer), 'error');
        }

        //日志
        $log = [
            'produce_id' => $id,
            'produce_sn' => $model->produce_sn,
            'log_type' => LogTypeEnum::ARTIFICIAL,
            'bc_status' => $model->bc_status,
            'log_module' => LogModuleEnum::getValue(LogModuleEnum::TO_CONFIRMED),
            'log_msg' => "确认分配"
        ];
        Yii::$app->supplyService->produce->createProduceLog($log);
        Yii::$app->getSession()->setFlash('success','保存成功');
        return $this->redirect(Yii::$app->request->referrer);
    }


    //设置配料信息
    public function actionSetPeiliao(){
        $id = Yii::$app->request->get('id');
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['produce/index']));
        $this->modelClass = SetPeiliaoForm::class;
        $model = $this->findModel($id);
        $model->peishi_type = $model->is_inlay == InlayEnum::No ? PeishiTypeEnum::None : '';

        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {
            if($model->bc_status != BuChanEnum::ASSIGNED || $model->from_type != FromTypeEnum::ORDER){
                return $this->message('不是'.BuChanEnum::getValue(BuChanEnum::ASSIGNED).'且不是'.FromTypeEnum::getValue(FromTypeEnum::ORDER).'，不能操作', $this->redirect(Yii::$app->request->referrer), 'warning');
            }

            $model->peishi_status = PeishiTypeEnum::getPeishiStatus($model->peishi_type);
            $model->peiliao_status = PeiliaoTypeEnum::getPeiliaoStatus($model->peiliao_type);
            if(PeishiTypeEnum::isPeishi($model->peishi_type) || PeiliaoTypeEnum::isPeiliao($model->peiliao_type)) {
                $model->bc_status = BuChanEnum::TO_PEILIAO;
            }else{
                $model->bc_status = BuChanEnum::TO_PRODUCTION;
            }
            if(false === $model->save()){
                return $this->message($this->getError($model), $this->redirect(Yii::$app->request->referrer), 'error');
            }

            //日志
            $follower = SupplierFollower::find()->where(['id'=>$model->follower_id])->one();
            $log = [
                'produce_id' => $id,
                'produce_sn' => $model->produce_sn,
                'log_type' => LogTypeEnum::ARTIFICIAL,
                'bc_status' => $model->bc_status,
                'log_module' => LogModuleEnum::getValue(LogModuleEnum::SET_PEILIAO),
                'log_msg' => LogModuleEnum::getValue(LogModuleEnum::SET_PEILIAO)
            ];
            Yii::$app->supplyService->produce->createProduceLog($log);
            Yii::$app->getSession()->setFlash('success','保存成功');
            return $this->redirect(Yii::$app->request->referrer);



        }
        return $this->renderAjax($this->action->id, [
            'model' => $model,
            'returnUrl' => $returnUrl
        ]);

    }


    //更新跟单人
    public function actionChangeFollower(){
        $id = Yii::$app->request->get('id');
        $this->modelClass = ProduceFollowerForm::class;
        $model = $this->findModel($id);
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['produce/index']));
        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {
            $model->audit_follower_status = AuditStatusEnum::PENDING;
            if(false === $model->save()){
                return $this->message($this->getError($model), $this->redirect(Yii::$app->request->referrer), 'error');
            }

            //日志
            $apply_follower = Member::find()->where(['id'=>$model->apply_follower_id])->one();
            $log = [
                'produce_id' => $id,
                'produce_sn' => $model->produce_sn,
                'log_type' => LogTypeEnum::ARTIFICIAL,
                'bc_status' => $model->bc_status,
                'log_module' => '更改跟单人',
                'log_msg' => "跟单人申请改成为".$apply_follower->username
            ];
            Yii::$app->supplyService->produce->createProduceLog($log);
            Yii::$app->getSession()->setFlash('success','保存成功');
            return $this->redirect(Yii::$app->request->referrer);
        }


        return $this->renderAjax($this->action->id, [
            'model' => $model,
            'returnUrl' => $returnUrl
        ]);

    }


    /**
     * ajax 跟单人审核
     *
     * @return mixed|string|\yii\web\Response
     * @throws \yii\base\ExitException
     */
    public function actionAjaxAuditFollower()
    {
        $id = Yii::$app->request->get('id');
        $model = $this->findModel($id);

        if($model->audit_follower_status == AuditStatusEnum::PENDING) {
            $model->audit_follower_status = AuditStatusEnum::PASS;
        }
        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {
            $apply_follower = Member::find()->where(['id'=>$model->apply_follower_id])->one();

            if($model->audit_follower_status == AuditStatusEnum::PASS){
                $log_msg = "跟单人审核通过,跟单人由{$model->follower->username}更改为{$apply_follower->username}";
                $model->follower_id = $model->apply_follower_id;
                $model->follower_name = $apply_follower->username;
            }else{
                $log_msg = "跟单人审核不通过";
            }
            if(false === $model->save()){
                return $this->message($this->getError($model), $this->redirect(Yii::$app->request->referrer), 'error');
            }
            //日志

            $log = [
                'produce_id' => $id,
                'produce_sn' => $model->produce_sn,
                'log_type' => LogTypeEnum::ARTIFICIAL,
                'bc_status' => $model->bc_status,
                'log_module' => '审核跟单人',
                'log_msg' => $log_msg
            ];
            Yii::$app->supplyService->produce->createProduceLog($log);
            Yii::$app->getSession()->setFlash('success','保存成功');
            return $this->redirect(Yii::$app->request->referrer);
        }

        return $this->renderAjax($this->action->id, [
            'model' => $model,
        ]);
    }
    /**
     * 申请配料
     * @return mixed|string|\yii\web\Response
     */
    public function actionApplyPeiliao(){
        
        $id = Yii::$app->request->get('id');
        $model = $this->findModel($id) ?? new Produce();
        
        try{
            
            $trans = Yii::$app->trans->beginTransaction();
            Yii::$app->supplyService->produce->createPeiliao($model);
            
            $trans->commit();
            return $this->message("保存成功", $this->redirect(Yii::$app->request->referrer), 'success'); 
        }catch (\Exception $e) {            
             $trans->rollback();
             return $this->message($e->getMessage(), $this->redirect(Yii::$app->request->referrer), 'error');            
        }
        
    }
    /**
     * 开始生产
     * @return mixed|string|\yii\web\Response
     */
    public function actionToProduce(){
        $id = Yii::$app->request->get('id');
        $model = $this->findModel($id);
        // ajax 校验
        $this->activeFormValidate($model);
        if($model->bc_status != BuChanEnum::TO_PRODUCTION){
            return $this->message('不是'.BuChanEnum::getValue(BuChanEnum::TO_PRODUCTION).'，不能操作', $this->redirect(Yii::$app->request->referrer), 'warning');
        }
        $model->bc_status = BuChanEnum::IN_PRODUCTION;
        $model->factory_order_time = time();
        if(false === $model->save()){
            $this->message($this->getError($model), $this->redirect(Yii::$app->request->referrer), 'error');
        }

        //日志
        $log = [
            'produce_id' => $id,
            'produce_sn' => $model->produce_sn,
            'log_type' => LogTypeEnum::ARTIFICIAL,
            'bc_status' => $model->bc_status,
            'log_module' => LogModuleEnum::getValue(LogModuleEnum::TO_PRODUCE),
            'log_msg' => "开始生产"
        ];
        Yii::$app->supplyService->produce->createProduceLog($log);
        Yii::$app->getSession()->setFlash('success','保存成功');
        return $this->redirect(Yii::$app->request->referrer);
    }



    //生产出厂
    public function actionProduceShipment(){
        $produce_id = Yii::$app->request->get('id');
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['produce/index']));
        $this->modelClass = ProduceShipment::class;
        $model = $this->findModel(null);
        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {
            try {
                $trans = Yii::$app->trans->beginTransaction();
                $produce = Produce::find()->where(['id' => $produce_id])->one();

                if ($produce->bc_status != BuChanEnum::PARTIALLY_SHIPPED && $produce->bc_status != BuChanEnum::IN_PRODUCTION) {
                    return $this->message('不是' . BuChanEnum::getValue(BuChanEnum::PARTIALLY_SHIPPED) . '/' . BuChanEnum::getValue(BuChanEnum::IN_PRODUCTION) . '，不能操作', $this->redirect(Yii::$app->request->referrer), 'warning');
                }

                $produce->factory_delivery_time = time();
                $produce->standard_delivery_time = time();


                //判断是全部出厂还是部分出厂
                $shippent_count = Yii::$app->supplyService->produce->getShippentNum($produce_id);
                $surplus_num = $produce->goods_num - $shippent_count;  //未出厂商品数量
                $shippent_num = $model->shippent_num; //这次出厂数量
                $nopass_num = $model->nopass_num; //这次质检不通过数量

                if($nopass_num > $surplus_num){
                    return $this->message('质检不通过数量大于未出厂数量', $this->redirect(Yii::$app->request->referrer), 'warning');
                }elseif($shippent_num > $surplus_num){
                    return $this->message('出厂数量大于未出厂数量', $this->redirect(Yii::$app->request->referrer), 'warning');
                }elseif($shippent_num == $surplus_num){
                    $produce->bc_status = BuChanEnum::FACTORY;
                }else{
                    $produce->bc_status = BuChanEnum::PARTIALLY_SHIPPED;
                }

                $model->produce_id = $produce_id;

                if(false === $model->save()){
                    return $this->message($this->getError($model), $this->redirect(Yii::$app->request->referrer), 'error');
                }
                $produce->save();

                //日志
                $log = [
                    'produce_id' => $produce_id,
                    'produce_sn' => $produce->produce_sn,
                    'log_type' => LogTypeEnum::ARTIFICIAL,
                    'bc_status' => $produce->bc_status,
                    'log_module' => LogModuleEnum::getValue(LogModuleEnum::LEAVE_FACTORY),
                    'log_msg' => $model->status == 1 ? "质检通过，出厂数量：{$model->shippent_num}" : "质检未通过，未通过数量：{$model->nopass_num},原因：".NopassReasonEnum::getValue($model->nopass_reason)
                ];

                Yii::$app->supplyService->produce->createProduceLog($log);
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
            'produce_id' => $produce_id,
            'returnUrl' => $returnUrl
        ]);

    }


    //QC质检
    public function actionProduceOqc(){
        $produce_id = Yii::$app->request->get('id');
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['produce/index']));
        $this->modelClass = ProduceOqc::class;
        $model = $this->findModel(null);
        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {
            try{
                $trans = Yii::$app->trans->beginTransaction();
                $produce = Produce::find()->where(['id'=>$produce_id])->one();
                if($produce->bc_status != BuChanEnum::FACTORY && $produce->bc_status != BuChanEnum::PARTIALLY_SHIPPED){
                    return $this->message('不是'.BuChanEnum::getValue(BuChanEnum::FACTORY).'/'.BuChanEnum::getValue(BuChanEnum::PARTIALLY_SHIPPED).'，不能操作', $this->redirect(Yii::$app->request->referrer), 'warning');
                }

                //判断是全部出厂还是部分出厂
                $num = $model->pass_num + $model->failed_num + $model->nopass_num;
                $goods_num = $produce->goods_num;
                if($num > $goods_num){
                    return $this->message('出货数量大于商品数量', $this->redirect(Yii::$app->request->referrer), 'warning');
                }elseif($num == $goods_num){
                    $produce->bc_status = BuChanEnum::FACTORY;
                }else{
                    $produce->bc_status = BuChanEnum::PARTIALLY_SHIPPED;
                }

                //出货单号
                $model->produce_id = $produce_id;

                if(false === $model->save()){
                    return $this->message($this->getError($model), $this->redirect(Yii::$app->request->referrer), 'error');
                }
                $produce->save();

                //日志
                $log = [
                    'produce_id' => $produce_id,
                    'produce_sn' => $produce->produce_sn,
                    'log_type' => LogTypeEnum::ARTIFICIAL,
                    'bc_status' => $produce->bc_status,
                    'log_module' => LogModuleEnum::getValue(LogModuleEnum::QC_QUALITY),
                    'log_msg' => "QC质检"
                ];
                Yii::$app->supplyService->produce->createProduceLog($log);

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
            'produce_id' => $produce_id,
            'returnUrl' => $returnUrl
        ]);

    }


    public function actionGetFollower(){
        $supplier_id = Yii::$app->request->post('supplier_id');
        $model = Yii::$app->supplyService->supplier->getFollowers($supplier_id);
        return ResultHelper::json(200, 'ok',$model);
    }
}
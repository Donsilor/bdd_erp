<?php

namespace addons\Supply\backend\controllers;

use addons\Supply\common\enums\BuChanEnum;
use addons\Supply\common\enums\LogModuleEnum;
use addons\Supply\common\forms\ToFactoryForm;
use addons\Supply\common\models\Produce;
use addons\Supply\common\models\ProduceAttribute;
use addons\Supply\common\models\ProduceShipment;
use addons\Supply\common\models\Supplier;
use addons\Supply\common\models\SupplierFollower;
use common\enums\LogTypeEnum;
use common\enums\StatusEnum;
use common\helpers\ArrayHelper;
use common\helpers\ResultHelper;
use common\helpers\Url;
use common\models\base\SearchModel;
use common\traits\Curd;
use Yii;
use common\controllers\AddonsController;

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
            'partialMatchAttributes' => [], // 模糊查询
            'defaultOrder' => [
                'id' => SORT_DESC
            ],
            'pageSize' => $this->pageSize,
            'relations' => [
                'purchaseGoods' => ['goods_name'],
                'follower' => ['username']
            ]
        ]);

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

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
            if($model->bc_status != BuChanEnum::INITIALIZATION){
                return $this->message('不是'.BuChanEnum::getValue(BuChanEnum::INITIALIZATION).'，不能操作', $this->redirect(Yii::$app->request->referrer), 'warning');
            }
            $model->factory_distribute_time = time();
            $model->bc_status = BuChanEnum::TO_CONFIRMED;
            if(false === $model->save()){
                return $this->message($this->getError($model), $this->redirect(Yii::$app->request->referrer), 'error');
            }

            //日志
            $follower = SupplierFollower::find()->where(['id'=>$model->follower_id])->one();
            $log = [
                'produce_id'=>$id,
                'produce_sn'=>$model->produce_sn,
                'log_type'=> LogTypeEnum::ARTIFICIAL,
                'log_module' => LogModuleEnum::getValue(LogModuleEnum::TO_FACTORY),
                'log_msg' => "采购单{$model->produce_sn}分配到供应商{$supplier[$model->supplier_id]}生产，跟单人是{$follower->member_name}"
            ];
            Yii::$app->supplyService->produce_log->createProduceLog($log);
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
            'produce_id'=>$id,
            'produce_sn'=>$model->produce_sn,
            'log_type'=> LogTypeEnum::ARTIFICIAL,
            'log_module' => LogModuleEnum::getValue(LogModuleEnum::TO_CONFIRMED),
            'log_msg' => "确认分配"
        ];
        Yii::$app->supplyService->produce_log->createProduceLog($log);
        Yii::$app->getSession()->setFlash('success','保存成功');
        return $this->redirect(Yii::$app->request->referrer);
    }

    //开始生产
    public function actionToProduce(){
        $id = Yii::$app->request->get('id');
        $model = $this->findModel($id);
        // ajax 校验
        $this->activeFormValidate($model);
        if($model->bc_status != BuChanEnum::ASSIGNED){
            return $this->message('不是'.BuChanEnum::getValue(BuChanEnum::ASSIGNED).'，不能操作', $this->redirect(Yii::$app->request->referrer), 'warning');
        }
        $model->bc_status = BuChanEnum::IN_PRODUCTION;
        $model->factory_order_time = time();
        if(false === $model->save()){
            $this->message($this->getError($model), $this->redirect(Yii::$app->request->referrer), 'error');
        }

        //日志
        $log = [
            'produce_id'=>$id,
            'produce_sn'=>$model->produce_sn,
            'log_type'=> LogTypeEnum::ARTIFICIAL,
            'log_module' => LogModuleEnum::getValue(LogModuleEnum::TO_PRODUCE),
            'log_msg' => "开始生产"
        ];
        Yii::$app->supplyService->produce_log->createProduceLog($log);
        Yii::$app->getSession()->setFlash('success','保存成功');
        return $this->redirect(Yii::$app->request->referrer);
    }



    //生产出厂
    public function actionLeaveFactory(){
        $id = Yii::$app->request->get('id');
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['produce/index']));
        $this->modelClass = ProduceShipment::class;
        $model = $this->findModel($id);
        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {
            if($model->bc_status != BuChanEnum::IN_PRODUCTION){
                $this->message('不是生产中状态，不能操作', $this->redirect(Yii::$app->request->referrer), 'warning');
            }
            $model->factory_delivery_time = time();
            $model->standard_delivery_time = time();
            //需判断是全部出厂还是部分出厂

            $model->bc_status = BuChanEnum::PARTIALLY_SHIPPED;

            if(false === $model->save()){
                $this->message($this->getError($model), $this->redirect(Yii::$app->request->referrer), 'error');
            }
            return $this->redirect(Yii::$app->request->referrer);



        }
        return $this->renderAjax($this->action->id, [
            'model' => $model,

            'returnUrl' => $returnUrl
        ]);

    }


    //QC质检
    public function actionQcQuality(){
        $id = Yii::$app->request->get('id');
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['produce/index']));
        $model = $this->findModel($id);
        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {
            if($model->bc_status != BuChanEnum::PARTIALLY_SHIPPED || $model->bc_status != BuChanEnum::FACTORY){
                $this->message('不是出厂/部分出厂状态，不能操作', $this->redirect(Yii::$app->request->referrer), 'warning');
            }


            if(false === $model->save()){
                $this->message($this->getError($model), $this->redirect(Yii::$app->request->referrer), 'error');
            }
            return $this->redirect(Yii::$app->request->referrer);



        }
        return $this->renderAjax($this->action->id, [
            'model' => $model,

            'returnUrl' => $returnUrl
        ]);

    }


    public function actionGetFollower(){
        $supplier_id = Yii::$app->request->post('supplier_id');
        $model = SupplierFollower::find()
            ->where(['supplier_id'=>$supplier_id,'status' => StatusEnum::ENABLED])
            ->select(['id','member_name'])
            ->asArray()
            ->all();
        $model = ArrayHelper::map($model,'id', 'member_name');
        return ResultHelper::json(200, 'ok',$model);
    }
}
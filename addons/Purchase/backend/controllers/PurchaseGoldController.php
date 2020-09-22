<?php

namespace addons\Purchase\backend\controllers;

use addons\Purchase\common\enums\PurchaseTypeEnum;
use common\helpers\ArrayHelper;
use Yii;
use addons\Purchase\common\models\PurchaseGold;
use common\enums\AuditStatusEnum;
use addons\Purchase\common\enums\PurchaseStatusEnum;
use common\helpers\SnHelper;
use common\models\base\SearchModel;
use common\traits\Curd;
use common\enums\LogTypeEnum;
use common\helpers\StringHelper;
use common\helpers\ExcelHelper;
/**
 *
 *
 * Class PurchaseGoldController
 * @package backend\modules\goods\controllers
 */
class PurchaseGoldController extends PurchaseMaterialController
{  
    /**
     * @var PurchaseGold
     */
    public $modelClass = PurchaseGold::class;
    public $purchaseType = PurchaseTypeEnum::MATERIAL_GOLD;
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
                'pageSize' => $this->getPageSize(),
                'relations' => [
                        
                ]
        ]);
        
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);        
        $dataProvider->query->andWhere(['>','status',-1]);
        //导出
        if(\Yii::$app->request->get('action') === 'export'){
            $dataProvider->setPagination(false);
            $list = $dataProvider->models;
            $list = ArrayHelper::toArray($list);
            $ids = array_column($list,'id');
            $this->actionExport($ids);
        }
        
        return $this->render('index', [
                'dataProvider' => $dataProvider,
                'searchModel' => $searchModel,
        ]);
    }

    /**
     * ajax编辑/创建
     *
     * @return mixed|string|\yii\web\Response
     * @throws \yii\base\ExitException
     *
     */
    public function actionAjaxEdit()
    {
        $id = Yii::$app->request->get('id');
        $model = $this->findModel($id);
        $isNewRecord = $model->isNewRecord;
        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {
            try{
                $trans = Yii::$app->db->beginTransaction();
                if($isNewRecord){
                    $model->purchase_sn = SnHelper::createPurchaseSn();
                    $model->creator_id  = \Yii::$app->user->identity->id;
                }
                if(false === $model->save()){
                    throw new \Exception($this->getError($model));
                }
                if($isNewRecord) {
                    //日志
                    $log = [
                        'purchase_id' => $model->id,
                        'purchase_sn' => $model->purchase_sn,
                        'log_type' => LogTypeEnum::ARTIFICIAL,
                        'log_module' => "创建单据",
                        'log_msg' => "创建采购单，单号:".$model->purchase_sn
                    ];
                    Yii::$app->purchaseService->purchaseLog->createPurchaseLog($log,$this->purchaseType);
                    $trans->commit();
                    return $this->message("保存成功", $this->redirect(['purchase-gold-goods/index', 'purchase_id' => $model->id, 'tab'=>2]), 'success');
                }else{
                    $trans->commit();
                    return $this->redirect(Yii::$app->request->referrer);
                }
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
        $tab = Yii::$app->request->get('tab',1);
        
        $model = $this->findModel($id);
        return $this->render($this->action->id, [
                'model' => $model,
                'tab'=>$tab,
                'tabList'=>Yii::$app->purchaseService->gold->menuTabList($id,$this->returnUrl),
                'returnUrl'=>$this->returnUrl,
        ]);
    }

}

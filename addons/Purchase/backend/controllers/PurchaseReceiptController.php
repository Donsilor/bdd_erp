<?php

namespace addons\Purchase\backend\controllers;


use addons\Purchase\common\models\PurchaseReceiptGoods;
use common\helpers\Url;
use Yii;
use common\models\base\SearchModel;
use addons\Purchase\common\models\PurchaseReceipt;
use addons\Purchase\common\forms\PurchaseReceiptForm;
use common\enums\AuditStatusEnum;
use common\enums\StatusEnum;
use common\traits\Curd;
/**
* PurchaseReceipt
*
* Class PurchaseReceiptController
* @package addons\Purchase\Backend\controllers
*/
class PurchaseReceiptController extends BaseController
{
    use Curd;

    /**
    * @var PurchaseReceipt
    */
    public $modelClass = PurchaseReceipt::class;


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
                'member' => ['username'],
            ]
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
     * ajax编辑/创建
     *
     * @return mixed|string|\yii\web\Response
     * @throws \yii\base\ExitException
     */
    public function actionAjaxEdit()
    {
        $id = Yii::$app->request->get('id');
        $model = $this->findModel($id);

        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {
            $model->creator_id  = \Yii::$app->user->identity->id;
            return $model->save()
                ? $this->redirect(Yii::$app->request->referrer)
                : $this->message($this->getError($model), $this->redirect(['index']), 'error');
        }

        return $this->renderAjax($this->action->id, [
            'model' => $model,
        ]);
    }

    /**
     * 审核-采购收货单
     *
     * @return mixed
     */
    public function actionAjaxAudit()
    {
        $id = Yii::$app->request->get('id');
        $this->modelClass = PurchaseReceiptForm::class;
        $model = $this->findModel($id);
        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {
            try{
                $trans = Yii::$app->trans->beginTransaction();
                $model->audit_time = time();
                $model->auditor_id = \Yii::$app->user->id;
                if($model->audit_status == AuditStatusEnum::PASS){
                    $model->status = StatusEnum::ENABLED;
                }else{
                    $model->status = StatusEnum::DISABLED;
                }
                if(false === $model->save()) {
                    throw new \Exception($this->getError($model));
                }
                if($model->audit_status == AuditStatusEnum::PASS){
                    Yii::$app->purchaseService->purchaseReceipt->syncReceiptToBillInfoL($id);
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
     * 详情展示页
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView()
    {
        $id = Yii::$app->request->get('id');
        $receipt_no = Yii::$app->request->get('receipt_no');
        $tab = Yii::$app->request->get('tab',1);
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['purchase-receipt/index']));
        if(!$id){
            $result = $this->modelClass::find()->where(['receipt_no'=>$receipt_no])->asArray()->one();
            $id = !empty($result)?$result['id']:0;
        }
        $model = $this->findModel($id);
        return $this->render($this->action->id, [
            'model' => $model,
            'tab'=>$tab,
            'tabList'=>\Yii::$app->purchaseService->purchaseReceipt->menuTabList($id,$returnUrl),
            'returnUrl'=>$returnUrl,
        ]);
    }

    /**
     * 单据打印
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionPrint()
    {
        $ids = Yii::$app->request->get('ids');
        $id_arr = explode(',', $ids);
        $id = $id_arr[0];//暂时打印一个
        $tab = Yii::$app->request->get('tab',1);
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['purchase-receipt/index']));
        $model = $this->findModel($id);
        $goodsModel = new PurchaseReceiptGoods();
        $goodsList = $goodsModel::find()->where(['receipt_id' => $id])->asArray()->all();
        foreach ($goodsList as &$item) {
            $item['stone_zhong'] = $item['main_stone_weight']+$item['second_stone_weight1']+$item['second_stone_weight2']+$item['second_stone_weight3'];
            $item['han_tax_price'] = $item['cost_price'] + $item['tax_fee'];
        }
        return $this->render($this->action->id, [
            'model' => $model,
            'goodsList' => $goodsList,
            'tab'=>$tab,
            'returnUrl'=>$returnUrl,
        ]);
    }
}

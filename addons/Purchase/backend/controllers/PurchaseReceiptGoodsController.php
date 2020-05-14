<?php

namespace addons\Purchase\backend\controllers;


use Yii;
use common\models\base\SearchModel;
use common\traits\Curd;
use addons\Purchase\common\models\PurchaseReceipt;
use common\helpers\Url;
use addons\Purchase\common\forms\PurchaseReceiptGoodsForm;
use addons\Purchase\common\models\PurchaseReceiptGoods;
use addons\Supply\common\models\Produce;
use addons\Supply\common\models\ProduceAttribute;
use addons\Supply\common\models\ProduceShipment;
use common\enums\AuditStatusEnum;
use common\enums\StatusEnum;
use yii\base\Exception;

/**
 * PurchaseReceiptGoods
 *
 * Class PurchaseReceiptGoodsController
 * @property PurchaseReceiptGoodsForm $modelClass
 * @package backend\modules\goods\controllers
 */
class PurchaseReceiptGoodsController extends BaseController
{
    use Curd;
    
    /**
     * @var $modelClass PurchaseReceiptGoodsForm
     */
    public $modelClass = PurchaseReceiptGoodsForm::class;
    
    
    /**
     * 首页
     *
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionIndex()
    {
        $receipt_id = Yii::$app->request->get('receipt_id');
        $tab = Yii::$app->request->get('tab',2);
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['purchase-receipt/index']));
        
        $searchModel = new SearchModel([
                'model' => $this->modelClass,
                'scenario' => 'default',
                'partialMatchAttributes' => ['purchase_sn'], // 模糊查询
                'defaultOrder' => [
                     'id' => SORT_DESC
                ],
                'pageSize' => $this->pageSize,
                'relations' => [
                     
                ]
        ]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['=','receipt_id',$receipt_id]);
        //$dataProvider->query->andWhere(['>','status',-1]);
        $receipt_goods = $dataProvider->getModels();
        $receiptInfo = PurchaseReceipt::find()->where(['id'=>$receipt_id])->one();
        return $this->render('index', [
                'dataProvider' => $dataProvider,
                'searchModel' => $searchModel,
                'receiptInfo' => $receiptInfo,
                'receiptGoods' => $receipt_goods,
                'tabList' => \Yii::$app->purchaseService->purchaseReceipt->menuTabList($receipt_id,$returnUrl),
                'returnUrl' => $returnUrl,
                'tab'=>$tab,
        ]);
    }

    /**
     * 编辑/创建
     * @property PurchaseReceiptGoodsForm $model
     * @return mixed
     */
    public function actionEdit()
    {
        $this->layout = '@backend/views/layouts/iframe';

        $receipt_id = Yii::$app->request->get('receipt_id');
        $produce_sns = Yii::$app->request->get('produce_sns');
        $search = Yii::$app->request->get('search');

        $model = new PurchaseReceiptGoods();
        $this->modelClass = PurchaseReceiptGoodsForm::class;
        $skiUrl = Url::buildUrl(\Yii::$app->request->url,[],['search']);
        if($search == 1 && !empty($produce_sns)){
            $produce_sns = str_replace(' ',',',$produce_sns);
            $produce_sns = str_replace('，',',',$produce_sns);
            $produce_sns = str_replace(array("\r\n", "\r", "\n"),',',$produce_sns);
            $produce_arr = explode(",", $produce_sns);
            try {
                $trans = Yii::$app->db->beginTransaction();
                foreach ($produce_arr as $produce_sn) {
                    $produce_info = Produce::find()->where(['produce_sn' => $produce_sn])->one();
                    $produce_id = $produce_info['id'];
                    $shippent_num = ProduceShipment::find()->where(['produce_id' => $produce_id])->sum('shippent_num');
                    $purchase_receipt_info = PurchaseReceiptGoods::find()->joinWith(['receipt'])
                        ->select('supplier_id')
                        ->where(['produce_sn' => $produce_sn])
                        ->andWhere(['<=', 'audit_status', AuditStatusEnum::PASS])
                        ->asArray()
                        ->all();
                    $receipt_num = count($purchase_receipt_info);
                    $the_receipt_num = $shippent_num - $receipt_num;

                    $produce_attr = ProduceAttribute::find()->where(['produce_id'=> $produce_id])->asArray()->all();
                    foreach ($produce_attr as $attr) {
                        $produce_attr_arr = Yii::$app->styleService->attribute->getAttrNameByAttrId($attr['attr_id']);
                    }
                    if ($the_receipt_num) {
                        for ($i = 0; $i <= $the_receipt_num; $i++) {

                        }
                    } else {
                        throw new Exception("布产单{$produce_sn}没有可出货数量");
                    }
                }
            }catch (\Exception $e){
                $trans->rollBack();
                return $this->message($e->getMessage(), $this->redirect($skiUrl), 'error');
            }
        }
        return $this->render($this->action->id, [
            'model' => $model,
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
        $receipt_goods_list = Yii::$app->request->post('receipt_goods_list');
        $rurchase_receipt_info = Yii::$app->request->post('PurchaseReceipt');
        $model = new PurchaseReceiptGoods();
        if(!empty($receipt_goods_list)){
            try {
                $trans = Yii::$app->db->beginTransaction();
                $receipt_id = $rurchase_receipt_info['id'];
                foreach ($receipt_goods_list as $key => $goods) {
                    $id = isset($goods['id']) ? $goods['id'] : '';
                    $model = $this->findModel($id);
                    // ajax 校验
                    $this->activeFormValidate($model);
                    //if(!empty($id)){
                        if (false === $model::updateAll($goods, ['id' => $id])) {
                            throw new Exception($this->getError($model));
                        }
                    //}else{
                    //    if(false === $model->save()){
                    //        throw new Exception($this->getError($model));
                    //    }
                    //}
                }
                //更新采购收货单汇总：总金额和总数量
                //$res = Yii::$app->purchaseService->purchaseReceipt->purchaseReceiptSummary($receipt_id);
                //if(false === $res){
                //    throw new Exception('更新收货单汇总失败！');
                //}
                $trans->commit();
                return $this->redirect(Yii::$app->request->referrer);
            }catch (\Exception $e){
                $trans->rollBack();
                return $this->message($e->getMessage(), $this->redirect(['index']), 'error');
            }
        }
        return $this->renderAjax('index', [
            'model' => $model
        ]);
    }

}

<?php

namespace addons\Purchase\backend\controllers;

use Yii;
use common\models\base\SearchModel;
use common\traits\Curd;
use addons\Purchase\common\models\PurchaseReceipt;
use common\helpers\Url;
use addons\Purchase\common\forms\PurchaseReceiptGoodsForm;
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
     * ajax编辑/创建
     *
     * @return mixed|string|\yii\web\Response
     * @throws \yii\base\ExitException
     */
    public function actionAjaxEdit()
    {
        $receipt_goods_list = Yii::$app->request->post('receipt_goods_list');
        if(!empty($receipt_goods_list)){
            try {
                $trans = Yii::$app->db->beginTransaction();
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
                $trans->commit();
                return $this->redirect(Yii::$app->request->referrer);
            }catch (\Exception $e){
                $trans->rollBack();
                return $this->message($e->getMessage(), $this->redirect(['index']), 'error');
            }
        }
        return $this->renderAjax('index');
    }

}

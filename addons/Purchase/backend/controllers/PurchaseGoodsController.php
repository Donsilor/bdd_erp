<?php

namespace addons\Purchase\backend\controllers;

use Yii;
use addons\Style\common\models\Attribute;
use common\models\base\SearchModel;
use common\traits\Curd;
use addons\Purchase\common\models\Purchase;
use common\helpers\Url;
use addons\Purchase\common\models\PurchaseGoods;
/**
 * Attribute
 *
 * Class AttributeController
 * @package backend\modules\goods\controllers
 */
class PurchaseGoodsController extends BaseController
{
    use Curd;
    
    /**
     * @var Attribute
     */
    public $modelClass = PurchaseGoods::class;
    
    
    /**
     * 首页
     *
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionIndex()
    {
        $purchase_id = Yii::$app->request->get('purchase_id');
        $tab = Yii::$app->request->get('tab',2);
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['purchase/index']));
        
        $searchModel = new SearchModel([
                'model' => $this->modelClass,
                'scenario' => 'default',
                'partialMatchAttributes' => [], // 模糊查询
                'defaultOrder' => [
                     'id' => SORT_DESC
                ],
                'pageSize' => $this->pageSize,
                'relations' => [
                     
                ]
        ]);
        
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        $dataProvider->query->andWhere(['>','status',-1]);
        $purchase = Purchase::find()->where(['id'=>$purchase_id])->one();
        return $this->render('index', [
                'dataProvider' => $dataProvider,
                'searchModel' => $searchModel,
                'purchase'=> $purchase,
                'tab'=>$tab,
                'tabList'=>\Yii::$app->purchaseService->purchase->menuTabList($purchase_id,$returnUrl),
                'returnUrl'=>$returnUrl,
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
        $tab = Yii::$app->request->get('tab',2);
        $purchase_id = Yii::$app->request->get('purchase_id');
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['purchase-goods/index','purchase_id'=>$purchase_id]));
        $model = $this->findModel($id);
        
        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {
            try{
                $trans = Yii::$app->trans->beginTransaction();
                if(false === $model->save()) {
                    throw new \Exception($this->getError($model),423);
                }
                $trans->commit();
            }catch (\Exception $e){
                $trans->rollBack();
                return $this->message("保存失败:". $e->getMessage(), $this->redirect([$this->action->id,'purchase_id'=>$purchase_id,'tab'=>$tab,'returnUrl'=>$returnUrl]), 'error');
            }
            return $this->message("保存成功", $this->redirect([$this->action->id,'purchase_id'=>$purchase_id,'tab'=>$tab,'returnUrl'=>$returnUrl]), 'success');
        }
        
        return $this->renderAjax($this->action->id, [
            'model' => $model,                
        ]);
    }


}

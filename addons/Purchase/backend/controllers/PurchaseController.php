<?php

namespace addons\Purchase\backend\controllers;

use Yii;
use addons\Style\common\models\Attribute;
use common\models\base\SearchModel;
use common\traits\Curd;
use addons\Purchase\common\models\Purchase;
use common\helpers\SnHelper;
use common\helpers\Url;
/**
 * Attribute
 *
 * Class AttributeController
 * @package backend\modules\goods\controllers
 */
class PurchaseController extends BaseController
{
    use Curd;
    
    /**
     * @var Attribute
     */
    public $modelClass = Purchase::class;
    
    
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
                     
                ]
        ]);
        
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        $dataProvider->query->andWhere(['>','status',-1]);
        
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
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['purchase/index']));
        
        $model = $this->findModel($id);     
        return $this->render($this->action->id, [
            'model' => $model,
            'tab'=>$tab,
            'tabList'=>\Yii::$app->purchaseService->purchase->menuTabList($id,$returnUrl),
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
        $model = $this->findModel($id);
        
        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {
            if($model->isNewRecord){
              $model->purchase_sn = SnHelper::createPurchaseSn();
            }
            return $model->save()
            ? $this->redirect(Yii::$app->request->referrer)
            : $this->message($this->getError($model), $this->redirect(['index']), 'error');
        }
        
        return $this->renderAjax($this->action->id, [
            'model' => $model,
        ]);
    }


}

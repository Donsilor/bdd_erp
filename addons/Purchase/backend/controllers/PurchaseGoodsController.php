<?php

namespace addons\Purchase\backend\controllers;

use Yii;
use addons\Style\common\models\Attribute;
use common\models\base\SearchModel;
use common\traits\Curd;
use addons\Purchase\common\models\Purchase;
use common\helpers\Url;
use addons\Purchase\common\models\PurchaseGoods;
use common\helpers\ResultHelper;
use addons\Style\common\models\Style;
use addons\Purchase\common\forms\PurchaseGoodsForm;
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
                'partialMatchAttributes' => ['goods_name'], // 模糊查询
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
        $purchase_id = Yii::$app->request->get('purchase_id');
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['purchase/index','purchase_id'=>$purchase_id]));
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
                return $this->message("保存失败:". $e->getMessage(), $this->redirect(['index','purchase_id'=>$purchase_id,'returnUrl'=>$returnUrl]), 'error');
            }
            return $this->message("保存成功", $this->redirect(['index','purchase_id'=>$purchase_id,'returnUrl'=>$returnUrl]), 'success');
        }
        
        return $this->renderAjax($this->action->id, [
            'model' => $model,                
        ]);
    }
    
    /**
     * 编辑/创建
     * @property PurchaseGoodsForm $model
     * @return mixed
     */
    public function actionEdit()
    {
        $this->layout = '@backend/views/layouts/iframe';
        
        $id = Yii::$app->request->get('id');        
        $purchase_id = Yii::$app->request->get('purchase_id');
        
        $this->modelClass = PurchaseGoodsForm::class;
        $model = $this->findModel($id);
        if($model->isNewRecord) {
            $model->purchase_id = $purchase_id;            
        }else{
            $purchase_id = $model->purchase_id;
        }
        
        $style_sn = Yii::$app->request->get('style_sn');
        $search = Yii::$app->request->get('search');
        
        if($search && $style_sn) {   
            $skiUrl = Url::buildUrl(\Yii::$app->request->url,[],['search']);
            $style  = Style::find()->where(['style_sn'=>$style_sn])->one();
            if(!$style) {
                return $this->message("无效的款号", $this->redirect($skiUrl), 'error');
            }elseif($style->status != 1) {
                return $this->message("款号不可用", $this->redirect($skiUrl), 'error');
            }
            $model->style_id = $style->id;
            $model->style_sn = $style_sn;
            $model->style_cate_id = $style->style_cate_id;
            $model->product_type_id = $style->product_type_id;
            $model->goods_type = 1;
            $model->goods_name = $style->style_name;
        }
        //$this->activeFormValidate($model);
        
        if ($model->load(Yii::$app->request->post())) {              
            try{
                
                $trans = Yii::$app->trans->beginTransaction();  
                
                if($model->isNewRecord) {
                    $model->purchase_id = $purchase_id;
                }
                if(false === $model->save()){
                    throw new \Exception($this->getError($model));
                }     
                //创建属性关系表数据
                //$model->createGoodsAttribute();
                $trans->commit();
                return ResultHelper::json(200, '保存成功');
            }catch (\Exception $e){
                $trans->rollBack();
                return ResultHelper::json(422, $e->getMessage());
            }
        }
        
        $model->initAttrs();

        return $this->render($this->action->id, [
                'model' => $model,
        ]);
    }


}

<?php

namespace addons\Style\backend\controllers;

use Yii;
use common\models\base\SearchModel;
use yii\base\Exception;
use common\helpers\ResultHelper;
use common\helpers\ArrayHelper;
use common\traits\Curd;

use addons\Style\backend\controllers\BaseController;
use addons\Style\common\models\Style;
use addons\Style\common\forms\StyleAttrForm;

/**
* Style
*
* Class StyleController
* @package backend\modules\goods\controllers
*/
class StyleController extends BaseController
{
    use Curd;

    /**
    * @var Style
    */
    public $modelClass = Style::class;


    /**
    * 首页
    *
    * @return string
    * @throws \yii\web\NotFoundHttpException
    */
    public function actionIndex()
    {
        $cate_id = Yii::$app->request->get('cate_id');
        $searchModel = new SearchModel([
            'model' => $this->modelClass,
            'scenario' => 'default',
            'partialMatchAttributes' => ['style_name'], // 模糊查询
            'defaultOrder' => [
                'id' => SORT_DESC
            ],
            'pageSize' => $this->pageSize,
            'relations' => [
                 
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
        
        $model = $this->findModel($id);
        
        $dataProvider = null;      
        
        return $this->render($this->action->id, [
                'model' => $model,
                'dataProvider' => $dataProvider,
        ]);
    }
    
    /**
     * 编辑/创建  基础信息
     *
     * @return mixed
     */
    public function actionEditInfo()
    {
        
        $id = Yii::$app->request->get('id');
        $tab = Yii::$app->request->get('tab',1);
        $returnUrl = Yii::$app->request->get('returnUrl',['index']);
        $model = $this->findModel($id);
        
        if ($model->load(Yii::$app->request->post())) {
            
            try{
                $trans = Yii::$app->trans->beginTransaction();
                if(false === $model->save()){
                    throw new Exception($this->getError($model));
                }
                $trans->commit();
            }catch (Exception $e){
                $trans->rollBack();
                return $this->message("保存失败:". $e->getMessage(), $this->redirect([$this->action->id,'id'=>$model->id]), 'error');
            }
            return $this->message("保存成功", $this->redirect($returnUrl), 'success');
        }
        return $this->render($this->action->id, [
                'model' => $model,
                'tab'=>$tab,
                'tabList'=>\Yii::$app->styleService->style->editTabList($id),
        ]);
    }
    /**
     * 编辑-款式属性
     *
     * @return mixed
     */
    public function actionEditAttr()
    {    
        
        $id = Yii::$app->request->get('id');
        $tab = Yii::$app->request->get('tab');
        $returnUrl = Yii::$app->request->get('returnUrl',['index']);
        $model = StyleAttrForm::find($id)->one();   
        // ajax 校验
        $this->activeFormValidate($model);
        
        if ($model->load(Yii::$app->request->post())) {
            $attr_list = $model->getPostAttrs();
            try{
                $trans = Yii::$app->trans->beginTransaction();
                Yii::$app->styleService->style->createStyleAttribute($attr_list);
                $trans->commit();
            }catch (Exception $e){
                $trans->rollBack();
                return $this->message("保存失败:". $e->getMessage(), $this->redirect([$this->action->id,'id'=>$model->id]), 'error');
            }
            return $this->message("保存成功", $this->redirect($returnUrl), 'success');
        }
        return $this->render($this->action->id, [
                'model' => $model,
                'tab'=>$tab,
                'tabList'=>\Yii::$app->styleService->style->editTabList($id),
        ]);
    }
    /**
     * 编辑-款式商品
     *
     * @return mixed
     */
    public function actionEditGoods()
    {
        
        $id = Yii::$app->request->get('id');
        $tab = Yii::$app->request->get('tab');
        $returnUrl = Yii::$app->request->get('returnUrl',['index']);
        $model = $this->findModel($id);
        
        if ($model->load(Yii::$app->request->post())) {
            print_r($model->combineAttrs());exit;
            try{
                $trans = Yii::$app->trans->beginTransaction();
                if(false === $model->save()){
                    throw new Exception($this->getError($model));
                }
                $trans->commit();
            }catch (Exception $e){
                $trans->rollBack();
                return $this->message("保存失败:". $e->getMessage(), $this->redirect([$this->action->id,'id'=>$model->id]), 'error');
            }
            return $this->message("保存成功", $this->redirect($returnUrl), 'success');
        }
        return $this->render($this->action->id, [
                'model' => $model,
                'tab'=>$tab,
                'tabList'=>\Yii::$app->styleService->style->editTabList($id),
        ]);
    }
    /**
     * 编辑-属性模块
     *
     * @return mixed
     */
    public function actionEditStone()
    {
        
        $id = Yii::$app->request->get('id');
        $tab = Yii::$app->request->get('tab');
        $returnUrl = Yii::$app->request->get('returnUrl',['index']);
        $model = $this->findModel($id);
        
        if ($model->load(Yii::$app->request->post())) {
            
            try{
                $trans = Yii::$app->trans->beginTransaction();
                if(false === $model->save()){
                    throw new Exception($this->getError($model));
                }
                $trans->commit();
            }catch (Exception $e){
                $trans->rollBack();
                return $this->message("保存失败:". $e->getMessage(), $this->redirect([$this->action->id,'id'=>$model->id]), 'error');
            }
            return $this->message("保存成功", $this->redirect($returnUrl), 'success');
        }
        return $this->render($this->action->id, [
                'model' => $model,
                'tab'=>$tab,
                'tabList'=>\Yii::$app->styleService->style->editTabList($id),
        ]);
    }
    /**
     * 编辑-工厂模块
     *
     * @return mixed
     */
    public function actionEditFactory()
    {
        
        $id = Yii::$app->request->get('id');
        $tab = Yii::$app->request->get('tab');
        $returnUrl = Yii::$app->request->get('returnUrl',['index']);
        $model = $this->findModel($id);
        
        if ($model->load(Yii::$app->request->post())) {
            
            try{
                $trans = Yii::$app->trans->beginTransaction();
                if(false === $model->save()){
                    throw new Exception($this->getError($model));
                }
                $trans->commit();
            }catch (Exception $e){
                $trans->rollBack();
                return $this->message("保存失败:". $e->getMessage(), $this->redirect([$this->action->id,'id'=>$model->id]), 'error');
            }
            return $this->message("保存成功", $this->redirect($returnUrl), 'success');
        }
        return $this->render($this->action->id, [
                'model' => $model,
                'tab'=>$tab,
                'tabList'=>\Yii::$app->styleService->style->editTabList($id),
        ]);
    }
    /**
     * 编辑-工费
     *
     * @return mixed
     */
    public function actionEditFactoryFee()
    {
        
        $id = Yii::$app->request->get('id');
        $tab = Yii::$app->request->get('tab');
        $returnUrl = Yii::$app->request->get('returnUrl',['index']);
        $model = $this->findModel($id);
        
        if ($model->load(Yii::$app->request->post())) {
            
            try{
                $trans = Yii::$app->trans->beginTransaction();
                if(false === $model->save()){
                    throw new Exception($this->getError($model));
                }
                $trans->commit();
            }catch (Exception $e){
                $trans->rollBack();
                return $this->message("保存失败:". $e->getMessage(), $this->redirect([$this->action->id,'id'=>$model->id]), 'error');
            }
            return $this->message("保存成功", $this->redirect($returnUrl), 'success');
        }
        return $this->render($this->action->id, [
                'model' => $model,
                'tab'=>$tab,
                'tabList'=>\Yii::$app->styleService->style->editTabList($id),
        ]);
    }
    /**
     * 编辑-工费
     *
     * @return mixed
     */
    public function actionEditImages()
    {
        
        $id = Yii::$app->request->get('id');
        $tab = Yii::$app->request->get('tab');
        $returnUrl = Yii::$app->request->get('returnUrl',['index']);
        $model = $this->findModel($id);
        
        if ($model->load(Yii::$app->request->post())) {
            
            try{
                $trans = Yii::$app->trans->beginTransaction();
                if(false === $model->save()){
                    throw new Exception($this->getError($model));
                }
                $trans->commit();
            }catch (Exception $e){
                $trans->rollBack();
                return $this->message("保存失败:". $e->getMessage(), $this->redirect([$this->action->id,'id'=>$model->id]), 'error');
            }
            return $this->message("保存成功", $this->redirect($returnUrl), 'success');
        }
        return $this->render($this->action->id, [
                'model' => $model,
                'tab'=>$tab,
                'tabList'=>\Yii::$app->styleService->style->editTabList($id),
        ]);
    }
    /**
     * 款式日志
     *
     * @return mixed
     */
    public function actionLogs()
    {
        
        $id = Yii::$app->request->get('id');
        $tab = Yii::$app->request->get('tab');
        $returnUrl = Yii::$app->request->get('returnUrl',['index']);
        $model = $this->findModel($id);
        
        if ($model->load(Yii::$app->request->post())) {
            
            try{
                $trans = Yii::$app->trans->beginTransaction();
                if(false === $model->save()){
                    throw new Exception($this->getError($model));
                }
                $trans->commit();
            }catch (Exception $e){
                $trans->rollBack();
                return $this->message("保存失败:". $e->getMessage(), $this->redirect([$this->action->id,'id'=>$model->id]), 'error');
            }
            return $this->message("保存成功", $this->redirect($returnUrl), 'success');
        }
        return $this->render($this->action->id, [
                'model' => $model,
                'tab'=>$tab,
                'tabList'=>\Yii::$app->styleService->style->editTabList($id),                
        ]);
    }
    
}

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
use common\helpers\Url;

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
       /*  if (!is_null($id)) {
            $searchModel = new SearchModel([
                    'model' => OrderGoods::class,
                    'scenario' => 'default',
                    'partialMatchAttributes' => [], // 模糊查询
                    'defaultOrder' => [
                            'id' => SORT_DESC
                    ],
                    'pageSize' => $this->pageSize,
            ]);
            
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
            
            $dataProvider->query->andWhere(['=', 'order_id', $id]);
            
            $dataProvider->setSort(false);
        } */
        
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
                'tabList'=>$this->editTabList(),
                'tab'=>$tab
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
        $tab = Yii::$app->request->get('tab',2);
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
                'tabList'=>$this->editTabList(),
                'tab'=>$tab
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
        $tab = Yii::$app->request->get('tab',3);
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
                'tabList'=>$this->editTabList(),
                'tab'=>$tab
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
        $tab = Yii::$app->request->get('tab',4);
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
                'tabList'=>$this->editTabList(),
                'tab'=>$tab
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
        $tab = Yii::$app->request->get('tab',5);
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
                'tabList'=>$this->editTabList(),
                'tab'=>$tab
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
                'tabList'=>$this->editTabList(),
                'tab'=>$tab
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
                'tabList'=>$this->editTabList(),
                'tab'=>$tab
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
                'tabList'=>$this->editTabList(),
                'tab'=>$tab
        ]);
    }
    /**
     * 款式编辑 tab
     * @param string $mod
     * @return string[][]|boolean[][]
     */
    private function editTabList()
    {
        $id = \Yii::$app->request->get('id');
        $tab_list = [
                1=>['name'=>'基础信息','url'=>Url::to(['edit-info','id'=>$id,'tab'=>1])],
                2=>['name'=>'款式属性','url'=>Url::to(['edit-attr','id'=>$id,'tab'=>2])],
                3=>['name'=>'款式规格','url'=>Url::to(['edit-goods','id'=>$id,'tab'=>3])],
                4=>['name'=>'石头信息','url'=>Url::to(['edit-stone','id'=>$id,'tab'=>4])],
                5=>['name'=>'工厂信息','url'=>Url::to(['edit-factory','id'=>$id,'tab'=>5])],
                6=>['name'=>'工费信息','url'=>Url::to(['edit-factory-fee','id'=>$id,'tab'=>6])],
                7=>['name'=>'款式图片','url'=>Url::to(['edit-images','id'=>$id,'tab'=>7])],
                8=>['name'=>'日志信息','url'=>Url::to(['logs','id'=>$id,'tab'=>8])]
        ];
        
        return $tab_list;
    }
   
}

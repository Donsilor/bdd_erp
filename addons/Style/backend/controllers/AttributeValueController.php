<?php

namespace addons\Style\backend\controllers;

use Yii;
use common\traits\Curd;
use common\models\base\SearchModel;
use backend\controllers\BaseController;
use addons\Style\common\models\AttributeValue;
use common\helpers\Url;

/**
 * Attribute
 *
 * Class AttributeController
 * @package backend\modules\goods\controllers
 */
class AttributeValueController extends BaseController
{
  use Curd;
  
  /**
   * @var Attribute
   */
  public $modelClass = AttributeValue::class;
  
  
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
          'pageSize' => $this->pageSize
        ]);
        
        $dataProvider = $searchModel
            ->search(Yii::$app->request->queryParams);
        $dataProvider->query->with(['lang']);
        $dataProvider->query->where(['>','status','-1']);
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
  public function actionAjaxEditLang()
  {
      $id = Yii::$app->request->get('id');
      $attr_id = Yii::$app->request->get('attr_id');

      $model = $this->findModel($id);
      if(!empty($attr_id) && $model->isNewRecord){
          $model->attr_id = $attr_id;
      }
      // ajax 校验
      $this->activeFormValidate($model);
      if ($model->load(Yii::$app->request->post())) {
          try{
              $trans = Yii::$app->trans->beginTransaction();
              if(false === $model->save()){
                 throw new \Exception($this->getError($model));
              }
              if(!$model->code) {
                  $model->code = $model->id;
                  $model->save(false);
              }              
              //多语言编辑
              $this->editLang($model,true);
              
              //更新属性值到attribute_lang.attr_values;
              Yii::$app->styleService->attribute->updateAttrValues($model->attr_id);
              $trans->commit();
              
              return $this->message("保存成功", $this->redirect(Yii::$app->request->referrer), 'success');
          }catch (\Exception $e) {
              $trans->rollback();
              return $this->message($e->getMessage(), $this->redirect(Yii::$app->request->referrer), 'error');
          }
            
      }
      
      return $this->renderAjax($this->action->id, [
          'model' => $model,
      ]);
  }
  /**
   * 删除
   *
   * @param $id
   * @return mixed
   * @throws \Throwable
   * @throws \yii\db\StaleObjectException
   */
  public function actionDelete($id)
  {
      if ($model = $this->findModel($id)) {
          $model->status = -1;
          $model->save(false);
          
          //更新属性值到attribute_lang.attr_values;
          Yii::$app->styleService->attribute->updateAttrValues($model->attr_id);
          return $this->message("删除成功", $this->redirect(['attribute/edit-lang?id='.$model->attr_id]));
      }
      
      return $this->message("删除失败", $this->redirect(['attribute/edit-lang?id='.$model->attr_id]), 'error');
  }
}

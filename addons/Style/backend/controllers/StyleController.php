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
        $cate_id = Yii::$app->request->get('cate_id',0);
        $searchModel = new SearchModel([
            'model' => $this->modelClass,
            'scenario' => 'default',
            'partialMatchAttributes' => [], // 模糊查询
            'defaultOrder' => [
                'id' => SORT_DESC
            ],
            'pageSize' => $this->pageSize,
            'relations' => [
                 'lang' => ['style_name'],
            ]
        ]);

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        exit;
        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel, 

        ]);
    }
    
    /**
     * 编辑/创建 多语言
     *
     * @return mixed
     */
    public function actionEditLang()
    {
        $id = Yii::$app->request->get('id', null);
        $type_id = Yii::$app->request->get('type_id', 0);
        $returnUrl = Yii::$app->request->get('returnUrl',['index','type_id'=>$type_id]);
        $model = $this->findModel($id);
        
        $status = $model ? $model->status:0;        
        if ($model->load(Yii::$app->request->post())) {
            
            try{
                $trans = Yii::$app->db->beginTransaction();
                if($model->status == 1 && $status == 0){
                    $model->onsale_time = time();
                }                
                if(false === $model->save()){
                    throw new Exception($this->getError($model));
                }                
                $this->editLang($model);
                
                $trans->commit();                
            }catch (Exception $e){
                $trans->rollBack();
                $error = $e->getMessage();
                \Yii::error($error);
                return $this->message("保存失败:".$error, $this->redirect([$this->action->id,'id'=>$model->id,'type_id'=>$type_id]), 'error');
            }
            
            //商品更新
            \Yii::$app->services->goods->syncStyleToGoods($model->id);
            return $this->message("保存成功", $this->redirect($returnUrl), 'success');
        }
        return $this->render($this->action->id, [
                'model' => $model,
        ]);
    }
    
    /**
     * ajax更新排序/状态
     *
     * @param $id
     * @return array
     */
    public function actionAjaxUpdate($id)
    {
        if (!($model = $this->modelClass::findOne($id))) {
            return ResultHelper::json(404, '找不到数据');
        }
        $status = $model ? $model->status :0;
        $model->attributes = ArrayHelper::filter(Yii::$app->request->get(), ['sort', 'status']);
        
        if($model->status ==1 && $status == 0){
            $model->onsale_time = time();
        }
        if (!$model->save(false)) {
            return ResultHelper::json(422, $this->getError($model));
        }
        return ResultHelper::json(200, '修改成功');
    }
    
    public function actionTest($id)
    {
        $model = $this->modelClass::findOne($id);
        $res = \Yii::$app->services->goods->formatStyleAttrs($model,true);
        echo '<pre/>';
        print_r($res);
        exit;
    }
}

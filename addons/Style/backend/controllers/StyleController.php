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
     * 编辑/创建 多语言
     *
     * @return mixed
     */
    public function actionEditLang()
    {
        
        $id = Yii::$app->request->get('id');
        
        $cate_id = Yii::$app->request->get('cate_id');
        $returnUrl = Yii::$app->request->get('returnUrl',['index','cate_id'=>$cate_id]);
        $model = $this->findModel($id);

        $status = $model ? $model->status:0;        
        if ($model->load(Yii::$app->request->post())) {
            
            try{
                $trans = Yii::$app->trans->beginTransaction();              
                if(false === $model->save()){
                    throw new Exception($this->getError($model));
                } 
                
                $this->editLang($model);
                
                $trans->commit();                
            }catch (Exception $e){
                $trans->rollBack();
                return $this->message("保存失败:". $e->getMessage(), $this->redirect([$this->action->id,'id'=>$model->id,'cate_id'=>$cate_id]), 'error');
            }
            
            //商品更新
            //\Yii::$app->services->goods->syncStyleToGoods($model->id);
            return $this->message("保存成功", $this->redirect($returnUrl), 'success');
        }
        return $this->render($this->action->id, [
                'model' => $model,
        ]);
    }
   
}

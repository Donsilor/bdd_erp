<?php

namespace addons\Style\backend\controllers;

use Yii;
use addons\Style\common\models\Attribute;
use common\traits\Curd;
use common\models\base\SearchModel;
use backend\controllers\BaseController;
use addons\Style\common\models\SearchSpec;

/**
 * Attribute
 *
 * Class AttributeController
 * @package backend\modules\goods\controllers
 */
class SearchSpecController extends BaseController
{
    use Curd;
    
    /**
     * @var Attribute
     */
    public $modelClass = SearchSpec::class;
    
    
    /**
     * 首页
     *
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionIndex()
    {
        //Yii::$app->language = 'zh-TW';
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
            ->search(Yii::$app->request->queryParams,['attr_name','language']);
        
        $this->setLocalLanguage($searchModel->language);
        
        $dataProvider->query->andWhere(['>','status',-1]);
        $dataProvider->query->joinWith(['attr']);
        $dataProvider->query->with(['type']);
        
        $dataProvider->query->andFilterWhere(['like', 'attr.attr_name',$searchModel->attr_name]) ;
        
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
    public function actionAjaxEdit()
    {
        $id = Yii::$app->request->get('id');
        $returnUrl = Yii::$app->request->get('returnUrl',['index']);
        $model = $this->findModel($id);     
        
        $this->activeFormValidate($model);
        
        if ($model->load(Yii::$app->request->post())) {
            
            return $model->save()
            ? $this->message("添加成功", $this->redirect($returnUrl), 'success')
            : $this->message($this->getError($model), $this->redirect($returnUrl), 'error');
        }
        
        $attrValues = [];
        if ($model->attr_id){
            $attrValues = \Yii::$app->services->goodsAttribute->getValuesByAttrId($model->attr_id);
        }
        return $this->renderAjax($this->action->id, [
                'model' => $model,
                'attrValues'=>$attrValues,
        ]);
    }
    
    /**
     * 获取属性值
     */
    public function actionAjaxAttrValues()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        $id = Yii::$app->request->post("id");
        $attr_id = Yii::$app->request->post("attr_id");
               
        $checked_values = false;
        if ($id && $model = $this->findModel($id)) {
            $checked_values = explode(",",trim($model->attr_values,','));
        }        
        $html = '';
        $values = Yii::$app->services->goodsAttribute->getValuesByAttrId($attr_id);
        foreach ($values as $key=>$val) {
            $checked = $checked_values === false || in_array($key,$checked_values)?" checked":'';
            $html .= '<label style="color:#636f7a"><input type="checkbox" name="SearchSpec[attr_values][]" value="'.$key.'"'.$checked.'>'.$val.'</label>&nbsp;';  
        } 
        return $html;
    }
}

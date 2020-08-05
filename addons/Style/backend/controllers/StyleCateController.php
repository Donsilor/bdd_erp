<?php

namespace addons\Style\backend\controllers;

use addons\Style\common\models\StyleCate;
use common\models\base\SearchModel;
use Yii;
use common\traits\Curd;
use yii\data\ActiveDataProvider;

/**
 * 商品分类
 *
 * Class ArticleCateController
 * @package addons\RfArticle\backend\controllers
 * @author jianyan74 <751393839@qq.com>
 */
class StyleCateController extends BaseController
{
    use Curd;

    /**
     * @var StyleCateController
     */
    public $modelClass = StyleCate::class;

    /**
     * Lists all Tree models.
     * @return mixed
     */
    public function actionIndex()
    {
        $title = Yii::$app->request->get('title',null);
        $status = Yii::$app->request->get('status',-1);
        $searchModel = new SearchModel([
            'model' => $this->modelClass,
            'scenario' => 'default',
            'partialMatchAttributes' => [], // 模糊查询
            'defaultOrder' => [
                'id' => SORT_ASC
            ],
            'pageSize' => $this->pageSize
        ]);
        $query = StyleCate::find()
            ->orderBy('sort asc, created_at asc');
        if(!empty($title)){
            $query->andWhere(['or',['=','id',$title],['like','name',$title]]);
        }
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false
        ]);
        if($status != -1 ){
            $dataProvider->query->andWhere(['=','status',$status]);
        }else{
            $dataProvider->query->andWhere(['>','status',-1]);
        }


        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'status' => $status
        ]);
    }

    /**
     * @return mixed|string|\yii\console\Response|\yii\web\Response
     * @throws \yii\base\ExitException
     */
    public function actionAjaxEdit()
    {
        $request = Yii::$app->request;
        $id = $request->get('id');
        $model = $this->findModel($id);


        $model->pid = $request->get('pid', null) ?? $model->pid; // 父id

        // ajax 验证
        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {
            $res = $model->save();
            if($res){
                $this->redirect(['index']);
            }else{
                $this->message($this->getError($model), $this->redirect(['index']), 'error');
            }

        }
        return $this->renderAjax($this->action->id, [
            'model' => $model,
            'cateDropDownList' => Yii::$app->styleService->styleCate->getDropDownForEdit($id),

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
            $model->save();
            return $this->message("删除成功", $this->redirect(['index', 'id' => $model->id]));
        }
        
        return $this->message("删除失败", $this->redirect(['index', 'id' => $model->id]), 'error');
    }
}
<?php

namespace addons\Style\backend\controllers;

use Yii;
use common\traits\Curd;
use addons\style\common\models\Category;
use yii\data\ActiveDataProvider;
/**
 * 商品分类
 *
 * Class ArticleCateController
 * @package addons\RfArticle\backend\controllers
 * @author jianyan74 <751393839@qq.com>
 */
class CategoryController extends BaseController
{
    use Curd;

    /**
     * @var CategoryController
     */
    public $modelClass = Cate::class;

    /**
     * Lists all Tree models.
     * @return mixed
     */
    public function actionIndex()
    {        

        return $this->render('index', [
            'dataProvider' => $dataProvider
        ]);
    }

    /**
     * @return mixed|string|\yii\console\Response|\yii\web\Response
     * @throws \yii\base\ExitException
     */
    public function actionAjaxEditLang()
    {
        $request = Yii::$app->request;
        $id = $request->get('id');
        $model = $this->findModel($id);


        $model->pid = $request->get('pid', null) ?? $model->pid; // 父id

        // ajax 验证
        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {
            $trans = Yii::$app->db->beginTransaction();
            $res = $model->save();
            $resl = $this->editLang($model,true);
            $resl = true;

            if($res && $resl){
                $trans->commit();
                $this->redirect(['index']);
            }else{
                $trans->rollBack();
                $this->message($this->getError($model), $this->redirect(['index']), 'error');
            }

        }

        return $this->renderAjax($this->action->id, [
            'model' => $model,
            'cateDropDownList' => Category::getDropDownForEdit($id),
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
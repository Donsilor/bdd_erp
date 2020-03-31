<?php

namespace backend\modules\goods\controllers;

use addons\style\common\models\CategoryLang;
use Yii;
use common\components\Curd;
use addons\style\common\models\Category;
use yii\data\ActiveDataProvider;
use backend\controllers\BaseController;

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
    public $modelClass = Category::class;

    /**
     * Lists all Tree models.
     * @return mixed
     */
    public function actionIndex()
    {
        $title = Yii::$app->request->get('title',null);
        $query = Category::find()->alias('a')
            ->orderBy('sort asc, created_at asc')
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->leftJoin('{{%goods_category_lang}} b', 'b.master_id = a.id and b.language = "'.Yii::$app->language.'"')
            ->select(['a.*', 'b.cat_name']);
        if(!empty($title)){
            $query->andWhere(['or',['=','a.id',$title],['like','b.cat_name',$title]]);
        }
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false
        ]);
        $dataProvider->query->andWhere(['>','status',-1]);

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
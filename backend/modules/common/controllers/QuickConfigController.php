<?php

namespace backend\modules\common\controllers;

use Yii;
use common\traits\Curd;
use common\models\base\SearchModel;
use common\models\common\QuickConfig;
use backend\controllers\BaseController;
use yii\data\ActiveDataProvider;

/**
 * 快捷入口配置
 *
 * Class QuickConfigController
 * @package addons\RfArticle\backend\controllers
 * @author jianyan74 <751393839@qq.com>
 */
class QuickConfigController extends BaseController
{
    use Curd;

    /**
     * @var QuickConfig
     */
    public $modelClass = QuickConfig::class;

    /**
     * Lists all Tree models.
     * @return mixed
     * @throws
     */
    public function actionIndex()
    {
        $title = Yii::$app->request->get('title', null);
        $status = Yii::$app->request->get('status', -1);
        $searchModel = new SearchModel([
            'model' => $this->modelClass,
            'scenario' => 'default',
            'partialMatchAttributes' => [], // 模糊查询
            'defaultOrder' => [
                'id' => SORT_ASC
            ],
            'pageSize' => $this->pageSize
        ]);
        $query = QuickConfig::find()
            ->orderBy('sort asc, created_at asc');
        if (!empty($title)) {
            $query->andWhere(['or', ['=', 'id', $title], ['like', 'name', $title]]);
        }
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false
        ]);
        if ($status != -1) {
            $dataProvider->query->andWhere(['=', 'status', $status]);
        } else {
            $dataProvider->query->andWhere(['>', 'status', -1]);
        }
        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'status' => $status
        ]);
    }

    /**
     *
     * 添加/编辑
     * @return string
     * @throws
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
            if ($res) {
                $this->redirect(['index']);
            } else {
                $this->message($this->getError($model), $this->redirect(['index']), 'error');
            }
        }
        return $this->renderAjax($this->action->id, [
            'model' => $model,
            'cateDropDownList' => Yii::$app->services->quick->getDropDownForEdit($id),
        ]);
    }

    /**
     *
     * 删除
     * @param $id
     * @return mixed
     * @throws
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
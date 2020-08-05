<?php

namespace backend\modules\common\controllers;

use common\models\base\SearchModel;
use common\models\common\FlowType;
use Yii;
use common\traits\Curd;
use common\enums\AppEnum;
use common\models\common\ConfigCate;
use backend\controllers\BaseController;

/**
 * Class ConfigCateController
 * @package backend\modules\common\controllers
 * @author jianyan74 <751393839@qq.com>
 */
class FlowTypeController extends BaseController
{
    use Curd;

    /**
     * @var ConfigCate
     */
    public $modelClass = FlowType::class;

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
        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * 编辑/创建
     *
     * @return mixed|string|\yii\web\Response
     * @throws \yii\base\ExitException
     */
    public function actionAjaxEdit()
    {
        $this->layout = '@backend/views/layouts/iframe';
        $id = Yii::$app->request->get('id');
        $model = $this->findModel($id);
       // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {
                return $model->save()
                    ? $this->redirect(['index'])
                    : $this->message($this->getError($model), $this->redirect(['index']), 'error');

        }
        $user_id_arr = [];
        if(!$model->isNewRecord){
            $users = explode(',',$model->users);
            $user_id_arr = [];
            foreach ($users as $user_id){
                $user_id_arr[] = array('user_id'=>$user_id);
            }
        }
        return $this->render($this->action->id, [
            'model' => $model,
            'user_id_arr' => $user_id_arr
        ]);
    }
}
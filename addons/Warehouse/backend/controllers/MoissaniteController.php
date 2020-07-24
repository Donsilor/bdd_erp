<?php

namespace addons\Warehouse\backend\controllers;

use Yii;
use common\traits\Curd;
use addons\Warehouse\common\forms\MoissaniteForm;
use addons\Style\common\enums\AttrIdEnum;
use common\models\base\SearchModel;

/**
 * 莫桑石列表
 *
 * Class MoissaniteController
 * @package addons\Warehouse\backend\controllers
 */
class MoissaniteController extends BaseController
{
    use Curd;

    /**
     * @var MoissaniteForm
     */
    public $modelClass = MoissaniteForm::class;
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
            'pageSize' => $this->pageSize,
            'relations' => [
                'creator' => ['username'],
            ]
        ]);

        $dataProvider = $searchModel
            ->search(Yii::$app->request->queryParams,['created_at']);

        $created_at = $searchModel->created_at;
        if (!empty($created_at)) {
            $dataProvider->query->andFilterWhere(['>=',MoissaniteForm::tableName().'.created_at', strtotime(explode('/', $created_at)[0])]);//起始时间
            $dataProvider->query->andFilterWhere(['<',MoissaniteForm::tableName().'.created_at', (strtotime(explode('/', $created_at)[1]) + 86400)] );//结束时间
        }

        //$dataProvider->query->andWhere(['>',MoissaniteForm::tableName().'.status',-1]);

        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * Ajax 编辑/创建
     * @throws
     * @return mixed
     */
    public function actionAjaxEdit()
    {
        $id = \Yii::$app->request->get('id');
        $model = $this->findModel($id);
        $model = $model ?? new MoissaniteForm();
        if($model->isNewRecord){
            $model->type = AttrIdEnum::STONE_TYPE_MO;
            $model->creator_id = \Yii::$app->user->identity->getId();
        }
        $this->activeFormValidate($model);
        if ($model->load(\Yii::$app->request->post())) {
            try{
                $stone_type = \Yii::$app->attr->valueName($model->type)??"";
                $stone_shape = \Yii::$app->attr->valueName($model->shape)??"";
                $model->name = $stone_type.$stone_shape.$model->size;
                $model->est_cost = bcmul($model->real_carat, $model->karat_price, 2);
                if(false === $model->save()){
                    throw new \Exception($this->getError($model));
                }
            }catch (\Exception $e){
                return $this->message($this->getError($model), $this->redirect(\Yii::$app->request->referrer), 'error');
            }
            \Yii::$app->getSession()->setFlash('success','保存成功');
            return $this->redirect(\Yii::$app->request->referrer);
        }
        return $this->renderAjax($this->action->id, [
            'model' => $model,
        ]);
    }
}
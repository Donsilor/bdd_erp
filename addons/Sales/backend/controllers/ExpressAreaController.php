<?php

namespace addons\Sales\backend\controllers;

use common\helpers\ResultHelper;
use Yii;
use common\helpers\Url;
use common\traits\Curd;
use addons\Sales\common\models\Express;
use addons\Sales\common\models\ExpressArea;
use addons\Sales\common\forms\ExpressAreaForm;
use common\models\base\SearchModel;

/**
 * 物流配送区域
 *
 * Class ExpressController
 * @package addons\Sales\backend\controllers
 */
class ExpressAreaController extends BaseController
{
    use Curd;

    /**
     * @var ExpressArea
     */
    public $modelClass = ExpressAreaForm::class;
    /**
     * 首页
     *
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionIndex()
    {
        $express_id = Yii::$app->request->get('express_id');
        $tab = Yii::$app->request->get('tab',2);
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['express-area/index', 'express_id'=>$express_id]));
        $searchModel = new SearchModel([
            'model' => $this->modelClass,
            'scenario' => 'default',
            'partialMatchAttributes' => [], // 模糊查询
            'defaultOrder' => [
                'id' => SORT_DESC
            ],
            'pageSize' => $this->pageSize,
            'relations' => [
                'member' => ['username'],
            ]
        ]);

        $dataProvider = $searchModel
            ->search(Yii::$app->request->queryParams,['created_at']);

        $dataProvider->query->andWhere(['=', 'express_id', $express_id]);

        $created_at = $searchModel->created_at;
        if (!empty($created_at)) {
            $dataProvider->query->andFilterWhere(['>=',ExpressAreaForm::tableName().'.created_at', strtotime(explode('/', $created_at)[0])]);//起始时间
            $dataProvider->query->andFilterWhere(['<',ExpressAreaForm::tableName().'.created_at', (strtotime(explode('/', $created_at)[1]) + 86400)] );//结束时间
        }

        //$dataProvider->query->andWhere(['>',ExpressAreaForm::tableName().'.status',-1]);
        $express = Express::find()->where(['id'=>$express_id])->one();
        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'tabList' => Yii::$app->salesService->express->menuTabList($express_id, $returnUrl),
            'express' => $express,
            'returnUrl' => $returnUrl,
            'tab'=>$tab,
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
        $express_id = \Yii::$app->request->get('express_id');

        $model = $this->findModel($id);
        $model = $model ?? new ExpressAreaForm();
        $this->activeFormValidate($model);
        if ($model->load(\Yii::$app->request->post())) {
            try{
                $trans = \Yii::$app->db->beginTransaction();
                if(false === $model->save()){
                    throw new \Exception($this->getError($model));
                }
                $trans->commit();
            }catch (\Exception $e){
                $trans->rollBack();
                return $this->message($this->getError($model), $this->redirect(\Yii::$app->request->referrer), 'error');
            }
            \Yii::$app->getSession()->setFlash('success','保存成功');
            return $this->redirect(\Yii::$app->request->referrer);
        }
        $model->express_id = $express_id;
        return $this->renderAjax($this->action->id, [
            'model' => $model,
        ]);
    }
}
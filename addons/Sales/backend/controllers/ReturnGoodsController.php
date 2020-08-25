<?php

namespace addons\Sales\backend\controllers;

use addons\Sales\common\models\SalesReturn;
use common\helpers\Url;
use Yii;
use common\traits\Curd;
use common\models\base\SearchModel;
use addons\Sales\common\forms\ReturnGoodsForm;

/**
 * 退款单明细
 *
 * Class ReturnController
 * @package addons\Sales\backend\controllers
 */
class ReturnGoodsController extends BaseController
{
    use Curd;

    /**
     * @var ReturnGoodsForm
     */
    public $modelClass = ReturnGoodsForm::class;

    /**
     *
     * 首页
     * @return string
     * @throws
     */
    public function actionIndex()
    {
        $return_id = Yii::$app->request->get('return_id');
        $tab = Yii::$app->request->get('tab',2);
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['return-goods/index', 'return_id'=>$return_id]));
        $searchModel = new SearchModel([
            'model' => $this->modelClass,
            'scenario' => 'default',
            'partialMatchAttributes' => [], // 模糊查询
            'defaultOrder' => [
                'id' => SORT_DESC
            ],
            'pageSize' => $this->pageSize,
            'relations' => [

            ]
        ]);

        $dataProvider = $searchModel
            ->search(Yii::$app->request->queryParams, ['created_at']);

        $created_at = $searchModel->created_at;
        if (!empty($created_at)) {
            $dataProvider->query->andFilterWhere(['>=', ReturnGoodsForm::tableName() . '.created_at', strtotime(explode('/', $created_at)[0])]);//起始时间
            $dataProvider->query->andFilterWhere(['<', ReturnGoodsForm::tableName() . '.created_at', (strtotime(explode('/', $created_at)[1]) + 86400)]);//结束时间
        }

        $dataProvider->query->andWhere(['=',ReturnGoodsForm::tableName().'.return_id', $return_id]);

        $return = SalesReturn::find()->where(['id' => $return_id])->one();
        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'return' => $return,
            'tab' => $tab,
            'tabList'=>\Yii::$app->salesService->return->menuTabList($return_id, $returnUrl),
        ]);
    }

    /**
     *
     * Ajax 编辑/创建
     * @return mixed
     * @throws
     */
    public function actionAjaxEdit()
    {
        $id = \Yii::$app->request->get('id');
        $model = $this->findModel($id);
        $model = $model ?? new ReturnGoodsForm();
        $this->activeFormValidate($model);
        if ($model->load(\Yii::$app->request->post())) {
            try {
                $trans = \Yii::$app->db->beginTransaction();
                if ($model->isNewRecord) {
                    //$model->status = StatusEnum::DISABLED;
                }
                if (false === $model->save()) {
                    throw new \Exception($this->getError($model));
                }
                $trans->commit();
            } catch (\Exception $e) {
                $trans->rollBack();
                return $this->message($this->getError($model), $this->redirect(\Yii::$app->request->referrer), 'error');
            }
            \Yii::$app->getSession()->setFlash('success', '保存成功');
            return $this->redirect(\Yii::$app->request->referrer);
        }
        return $this->renderAjax($this->action->id, [
            'model' => $model,
        ]);
    }

}
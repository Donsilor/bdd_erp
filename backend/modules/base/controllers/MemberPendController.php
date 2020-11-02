<?php

namespace backend\modules\base\controllers;

use Yii;
use common\traits\Curd;
use backend\controllers\BaseController;
use common\models\base\SearchModel;
use common\models\backend\MemberPend;
use common\enums\StatusEnum;

/**
 * Class PendController
 * @package backend\modules\common\controllers
 * @author jianyan74 <751393839@qq.com>
 */
class MemberPendController extends BaseController
{
    use Curd;
    /**
     * @var MemberPend
     */
    public $modelClass = MemberPend::class;

    /**
     * 首页
     *
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionIndex()
    {

    }

    /**
     * 待处理
     *
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionPending()
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
            ->search(Yii::$app->request->queryParams, ['created_at', 'pend_time']);
        $created_at = $searchModel->created_at;
        if (!empty($created_at)) {
            $dataProvider->query->andFilterWhere(['>=', 'created_at', strtotime(explode('/', $created_at)[0])]);//起始时间
            $dataProvider->query->andFilterWhere(['<', 'created_at', (strtotime(explode('/', $created_at)[1]) + 86400)]);//结束时间
        }
        $pend_time = $searchModel->pend_time;
        if (!empty($pend_time)) {
            $dataProvider->query->andFilterWhere(['>=', 'pend_time', strtotime(explode('/', $pend_time)[0])]);//起始时间
            $dataProvider->query->andFilterWhere(['<', 'pend_time', (strtotime(explode('/', $pend_time)[1]) + 86400)]);//结束时间
        }
        $dataProvider->query
            ->andWhere(['>=', 'status', StatusEnum::DISABLED]);
        $uid = \Yii::$app->user->identity->getId();
        if ($uid > 1) {
            $dataProvider->query
                ->andWhere(['=', 'operor_id', $uid]);
        }
        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

}
<?php

namespace addons\Sales\backend\controllers;

use Yii;
use common\traits\Curd;
use common\models\base\SearchModel;
use addons\Sales\common\models\SalesReturn;
use addons\Sales\common\models\SalesReturnLog;

/**
 * 退款日志
 *
 * Class ReturnLogController
 * @package addons\Order\backend\controllers
 */
class ReturnLogController extends BaseController
{
    use Curd;
    /**
     * @var SalesReturnLog
     */
    public $modelClass = SalesReturnLog::class;

    /**
     * Lists all OrderChannel models.
     * @return mixed
     */
    public function actionIndex()
    {
        $return_id = Yii::$app->request->get('return_id');

        $return = SalesReturn::find()->where(['id' => $return_id])->one();
        $searchModel = new SearchModel([
            'model' => $this->modelClass,
            'scenario' => 'default',
            'partialMatchAttributes' => ['log_msg'], // 模糊查询
            'defaultOrder' => [
                'id' => SORT_DESC
            ],
            'pageSize' => $this->getPageSize(),

        ]);

        $dataProvider = $searchModel
            ->search(Yii::$app->request->queryParams);

        $dataProvider->query->andWhere(['=', SalesReturnLog::tableName() . '.return_id', $return_id]);

        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'return' => $return,
            'tab' => Yii::$app->request->get('tab', 2),
            'tabList' => \Yii::$app->salesService->return->menuTabList($return_id, $this->returnUrl),
        ]);
    }


}

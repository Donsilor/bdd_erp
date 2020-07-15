<?php

namespace addons\Sales\backend\controllers;

use Yii;
use common\traits\Curd;
use common\models\base\SearchModel;
use addons\Sales\common\models\Order;
use addons\Sales\common\models\OrderLog;
use addons\Sales\common\models\OrderFqc;

/**
 * 质检问题列表
 *
 * Class OrderFqcListController
 * @package addons\Order\backend\controllers
 */
class OrderFqcListController extends BaseController
{
    use Curd;
    /**
     * @var OrderFqc
     */
    public $modelClass = OrderFqc::class;
    
    /**
     * Lists all OrderFqc models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SearchModel([
                'model' => $this->modelClass,
                'scenario' => 'default',
                'partialMatchAttributes' => ['remark'], // 模糊查询
                'defaultOrder' => [
                     'id' => SORT_DESC
                ],
                'pageSize' => $this->getPageSize(),
                'relations' => [
                    'creator' => ['username'],
                ]
        ]);
        
        $dataProvider = $searchModel
            ->search(Yii::$app->request->queryParams, ['created_at']);

        //创建时间过滤
        if (!empty($searchParams['created_at'])) {
            list($start_date, $end_date) = explode('/', $searchParams['created_at']);
            $dataProvider->query->andFilterWhere(['between', Order::tableName().'.created_at', strtotime($start_date), strtotime($end_date) + 86400]);
        }

        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }
    
    
}

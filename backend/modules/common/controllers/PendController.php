<?php

namespace backend\modules\common\controllers;

use common\enums\StatusEnum;
use Yii;
use common\traits\Curd;
use common\models\base\SearchModel;
use common\models\common\ConfigCate;
use common\models\common\Pend;
use backend\controllers\BaseController;

/**
 * Class PendController
 * @package backend\modules\common\controllers
 * @author jianyan74 <751393839@qq.com>
 */
class PendController extends BaseController
{
    use Curd;
    /**
     * @var Pend
     */
    public $modelClass = Pend::class;

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
        $dataProvider->query
            ->andWhere(['>=', 'status', StatusEnum::DISABLED]);
        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

}
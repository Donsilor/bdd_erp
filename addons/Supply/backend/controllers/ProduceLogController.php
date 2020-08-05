<?php

namespace addons\Supply\backend\controllers;

use addons\Supply\common\enums\BuChanEnum;
use addons\Supply\common\enums\LogModuleEnum;
use addons\Supply\common\forms\ToFactoryForm;
use addons\Supply\common\models\Produce;
use addons\Supply\common\models\ProduceLog;
use addons\Supply\common\models\ProduceAttribute;
use addons\Supply\common\models\Supplier;
use addons\Supply\common\models\SupplierFollower;
use common\enums\StatusEnum;
use common\helpers\ArrayHelper;
use common\helpers\ResultHelper;
use common\helpers\Url;
use common\models\base\SearchModel;
use common\traits\Curd;
use Yii;
use common\controllers\AddonsController;

/**
 * 默认控制器
 *
 * Class DefaultController
 * @package addons\Supply\backend\controllers
 */
class ProduceLogController extends BaseController
{
    use Curd;

    /**
     * @var Attribute
     */
    public $modelClass = ProduceLog::class;
    /**
    * 首页
    *
    * @return string
    */
    public function actionIndex()
    {
        $produce_id = Yii::$app->request->get('produce_id');
        $tab = Yii::$app->request->get('tab');
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['produce/index']));
        $produce = Produce::find()->where(['id'=>$produce_id])->one();
        $searchModel = new SearchModel([
            'model' => $this->modelClass,
            'scenario' => 'default',
            'partialMatchAttributes' => ['log_msg'], // 模糊查询
            'defaultOrder' => [
                'id' => SORT_DESC
            ],
            'pageSize' => $this->pageSize,

        ]);

        $dataProvider = $searchModel
            ->search(Yii::$app->request->queryParams);

        $dataProvider->query->andWhere(['=','produce_id',$produce_id]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'tab'=>$tab,
            'produce_id' => $produce_id,
            'produce' => $produce,
            'tabList'=>\Yii::$app->supplyService->produce->menuTabList($produce_id,$returnUrl),
        ]);
    }



}
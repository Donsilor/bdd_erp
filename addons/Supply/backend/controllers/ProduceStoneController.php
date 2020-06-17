<?php

namespace addons\Supply\backend\controllers;

use common\models\base\SearchModel;
use common\traits\Curd;
use Yii;

use addons\Supply\common\models\ProduceStone;
use addons\Supply\common\models\Produce;

/**
 * 配石列表
 *
 * Class ProduceStoneController
 * @package addons\Supply\backend\controllers
 */
class ProduceStoneController extends BaseController
{
    use Curd;
    
    /**
     * @var ProduceStone
     */
    public $modelClass = ProduceStone::class;
    /**
     * 首页
     *
     * @return string
     */
    public function actionIndex()
    {
        $produce_id = Yii::$app->request->get('produce_id');
        
        $produce = Produce::find()->where(['id'=>$produce_id])->one();
        
        $searchModel = new SearchModel([
                'model' => $this->modelClass,
                'scenario' => 'default',
                'partialMatchAttributes' => [], // 模糊查询
                'defaultOrder' => [
                        'stone_position' => SORT_ASC
                ],
                'relations' => [
                     
                ],
                'pageSize' => $this->getPageSize(),
                
        ]);
        
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['=','produce_id',$produce_id]);

        return $this->render('index', [
                'dataProvider' => $dataProvider,
                'searchModel' => $searchModel,
                'produce' => $produce,
                'tab'=> Yii::$app->request->get('tab'),                
                'tabList'=>\Yii::$app->supplyService->produce->menuTabList($produce_id,$this->returnUrl),
        ]);
    }
        
    
}
<?php

namespace addons\Warehouse\backend\controllers;


use Yii;
use common\traits\Curd;
use common\models\base\SearchModel;
use addons\Supply\common\models\ProduceStone;
use common\helpers\ResultHelper;



/**
 * 
 * 配石列表
 */
class StoneApplyController extends BaseController
{
    use Curd;
    public $modelClass = ProduceStone::class;
    /**
     * Lists all StyleChannel models.
     * @return mixed
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
                'relations' => [
                        
                ],
                'pageSize' => $this->getPageSize(),
                
        ]);
        
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        return $this->render('index', [
                'dataProvider' => $dataProvider,
                'searchModel' => $searchModel,                
        ]);
        
    }
    
    public function actionTest()
    {
        return ResultHelper::json(200, 'shibai');
    }
    
}

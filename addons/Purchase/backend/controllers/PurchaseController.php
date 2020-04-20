<?php

namespace addons\Purchase\backend\controllers;

use Yii;
use addons\Style\common\models\Attribute;
use common\models\base\SearchModel;
use common\traits\Curd;
use addons\Purchase\common\models\Purchase;
/**
 * Attribute
 *
 * Class AttributeController
 * @package backend\modules\goods\controllers
 */
class PurchaseController extends BaseController
{
    use Curd;
    
    /**
     * @var Attribute
     */
    public $modelClass = Purchase::class;
    
    
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
                     
                ]
        ]);
        
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        $dataProvider->query->andWhere(['>','status',-1]);
        
        return $this->render('index', [
                'dataProvider' => $dataProvider,
                'searchModel' => $searchModel,
        ]);
    }


}

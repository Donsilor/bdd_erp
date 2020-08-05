<?php

namespace addons\Purchase\backend\controllers;

use Yii;
use common\models\base\SearchModel;
use addons\Purchase\common\models\PurchaseGift;
use addons\Purchase\common\enums\PurchaseTypeEnum;
use common\helpers\ArrayHelper;

/**
 *
 *
 * Class PurchaseGiftController
 * @package backend\modules\goods\controllers
 */
class PurchaseGiftController extends PurchaseMaterialController
{  
    /**
     * @var PurchaseGift
     */
    public $modelClass = PurchaseGift::class;
    public $purchaseType = PurchaseTypeEnum::MATERIAL_GIFT;
    /**
     * 首页
     *
     * @return string
     * @throws
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
                'pageSize' => $this->getPageSize(),
                'relations' => [
                        
                ]
        ]);
        
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);        
        $dataProvider->query->andWhere(['>','status',-1]);
        //导出
        if(\Yii::$app->request->get('action') === 'export'){
            $dataProvider->setPagination(false);
            $list = $dataProvider->models;
            $list = ArrayHelper::toArray($list);
            $ids = array_column($list,'id');
            $this->actionExport($ids);
        }
        
        return $this->render($this->action->id, [
                'dataProvider' => $dataProvider,
                'searchModel' => $searchModel,
        ]);
    }
    /**
     * 详情展示页
     * @return string
     * @throws
     */
    public function actionView()
    {
        $id = Yii::$app->request->get('id');
        $tab = Yii::$app->request->get('tab',1);
        
        $model = $this->findModel($id);
        return $this->render($this->action->id, [
                'model' => $model,
                'tab'=>$tab,
                'tabList'=>Yii::$app->purchaseService->gift->menuTabList($id,$this->returnUrl),
                'returnUrl'=>$this->returnUrl,
        ]);
    }

}

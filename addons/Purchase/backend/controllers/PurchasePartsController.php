<?php

namespace addons\Purchase\backend\controllers;

use Yii;
use addons\Purchase\common\models\PurchaseParts;
use common\enums\AuditStatusEnum;
use addons\Purchase\common\enums\PurchaseStatusEnum;
use addons\Purchase\common\enums\PurchaseTypeEnum;
use common\helpers\ArrayHelper;
use common\helpers\SnHelper;
use common\models\base\SearchModel;
use common\traits\Curd;
/**
 *
 *
 * Class PurchasePartsController
 * @package backend\modules\goods\controllers
 */
class PurchasePartsController extends PurchaseMaterialController
{  
    /**
     * @var PurchaseParts
     */
    public $modelClass = PurchaseParts::class;
    public $purchaseType = PurchaseTypeEnum::MATERIAL_PARTS;
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
                'tabList'=>Yii::$app->purchaseService->parts->menuTabList($id,$this->returnUrl),
                'returnUrl'=>$this->returnUrl,
        ]);
    }

}

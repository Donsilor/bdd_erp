<?php

namespace addons\Purchase\backend\controllers;


use addons\Purchase\common\enums\PurchaseTypeEnum;
use addons\Purchase\common\forms\PurchasePartsDefectiveGoodsForm;
use addons\Purchase\common\models\PurchaseGold;
use addons\Purchase\common\models\PurchaseReceipt;
use addons\Purchase\common\models\PurchaseReceiptGoods;
use Yii;
use common\models\base\SearchModel;
use common\traits\Curd;
use addons\Purchase\common\models\PurchaseDefective;
use common\helpers\Url;
use addons\Purchase\common\forms\PurchaseGoldDefectiveGoodsForm;
use addons\Purchase\common\models\PurchaseDefectiveGoods;
use addons\Supply\common\models\Produce;
use addons\Supply\common\models\ProduceAttribute;
use addons\Supply\common\models\ProduceShipment;
use addons\Purchase\common\enums\ReceiptGoodsAttrEnum;
use common\enums\AuditStatusEnum;
use common\enums\StatusEnum;
use yii\base\Exception;

/**
 * PurchaseDefectiveGoods
 *
 * Class PurchaseDefectiveGoodsController
 * @property PurchaseGoldDefectiveGoodsForm $modelClass
 * @package backend\modules\goods\controllers
 */
class PartsDefectiveGoodsController extends BaseController
{
    use Curd;
    
    /**
     * @var $modelClass PurchaseGoldDefectiveGoodsForm
     */
    public $modelClass = PurchasePartsDefectiveGoodsForm::class;
    public $purchaseType = PurchaseTypeEnum::MATERIAL_PARTS;
    
    /**
     * 首页
     *
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionIndex()
    {
        $defective_id = Yii::$app->request->get('defective_id');
        $tab = Yii::$app->request->get('tab',2);
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['parts-defective-goods/index']));
        $searchModel = new SearchModel([
                'model' => $this->modelClass,
                'scenario' => 'default',
                'partialMatchAttributes' => ['purchase_sn'], // 模糊查询
                'defaultOrder' => [
                     'id' => SORT_DESC
                ],
                'pageSize' => $this->pageSize,
                'relations' => [
                     
                ]
        ]);

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $dataProvider->query->andWhere(['=','defective_id',$defective_id]);
        $dataProvider->query->andWhere(['>',PurchaseDefectiveGoods::tableName().'.status',-1]);

        $defective = PurchaseDefective::find()->where(['id'=>$defective_id])->one();
        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'defective' => $defective,
            'tabList' => \Yii::$app->purchaseService->defective->menuTabList($defective_id, $this->purchaseType, $returnUrl),
            'returnUrl' => $returnUrl,
            'tab'=>$tab,
        ]);
    }

    /**
     * 编辑明细
     *
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionEditAll()
    {
        $defective_id = Yii::$app->request->get('defective_id');
        $tab = Yii::$app->request->get('tab',3);
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['parts-defective-goods/index']));
        $searchModel = new SearchModel([
            'model' => $this->modelClass,
            'scenario' => 'default',
            'partialMatchAttributes' => ['purchase_sn'], // 模糊查询
            'defaultOrder' => [
                'id' => SORT_DESC
            ],
            'pageSize' => $this->pageSize,
            'relations' => [

            ]
        ]);

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $dataProvider->query->andWhere(['=','defective_id',$defective_id]);
        $dataProvider->query->andWhere(['>',PurchaseDefectiveGoods::tableName().'.status',-1]);

        $defective = PurchaseDefective::find()->where(['id'=>$defective_id])->one();
        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'defective' => $defective,
            'tabList' => \Yii::$app->purchaseService->defective->menuTabList($defective_id, $this->purchaseType, $returnUrl, $tab),
            'returnUrl' => $returnUrl,
            'tab'=>$tab,
        ]);
    }
}

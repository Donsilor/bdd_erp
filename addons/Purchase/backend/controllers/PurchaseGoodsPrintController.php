<?php

namespace addons\Purchase\backend\controllers;

use addons\Purchase\common\models\PurchaseGoodsPrint;
use Yii;
use common\traits\Curd;
use addons\Purchase\common\models\Purchase;
use common\helpers\ResultHelper;
use addons\Purchase\common\forms\PurchaseGoodsForm;

/**
 * Attribute
 *
 * Class AttributeController
 * @property PurchaseGoodsForm $modelClass
 * @package backend\modules\goods\controllers
 */
class PurchaseGoodsPrintController extends BaseController
{
    use Curd;
    
    /**
     * @var PurchaseGoodsForm
     */
    public $modelClass = PurchaseGoodsPrint::class;
    /**
     * 编辑/创建
     * @var PurchaseGoodsForm $model
     * @return mixed
     */
    public function actionEdit()
    {
        $this->layout = '@backend/views/layouts/print';

        $id = Yii::$app->request->get('purchase_goods_id');
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post())) {  
            if(!$model->validate()) {
                return ResultHelper::json(422, $this->getError($model));
            }

        }
        

        return $this->render($this->action->id, [
                'model' => $model,
        ]);
    }
    /**
     * 详情展示页
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView()
    {
        $id = Yii::$app->request->get('id');
        $purchase_id = Yii::$app->request->get('purchase_id');
        $this->modelClass = PurchaseGoodsForm::class;
        $model = $this->findModel($id);
        $model = $model ?? new PurchaseGoodsForm();
        $model->initAttrs();
        $purchase = Purchase::find()->where(['id'=>$purchase_id])->one();
        return $this->render($this->action->id, [
            'model' => $model,
            'purchase' => $purchase
        ]);
    }




}

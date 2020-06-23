<?php

namespace addons\Purchase\backend\controllers;

use addons\Purchase\common\forms\PurchaseGoodsPrintForm;
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
    public $modelClass = PurchaseGoodsPrintForm::class;
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
        $model = $model ?? new PurchaseGoodsPrintForm();
        $model->purchase_goods_id = $id;
        $model->getPurchaseInfo();
        if ($model->load(Yii::$app->request->post())) {  
            if(!$model->validate()) {
                return ResultHelper::json(422, $this->getError($model));
            }

            return $model->save()
                ? $this->redirect(Yii::$app->request->referrer)
                : $this->message($this->getError($model), $this->redirect(Yii::$app->request->referrer), 'error');

        }
        

        return $this->render($this->action->id, [
                'model' => $model,
        ]);
    }


    /**
     * 单据打印
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionPrint()
    {
        $this->layout = '@backend/views/layouts/print';
        $id = Yii::$app->request->get('id');
        $ids = explode(',',$id);
        $lists = PurchaseGoodsPrintForm::find()->where(['purchase_goods_id'=>$ids])->all();
        if(empty($lists)) {
            echo '没有内容';exit;
        }
        foreach ($lists as &$model){
            $model->getPurchaseInfo();
        }
        return $this->render($this->action->id, [
            'lists' => $lists,
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

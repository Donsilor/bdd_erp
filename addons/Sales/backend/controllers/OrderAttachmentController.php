<?php

namespace addons\Sales\backend\controllers;

use Yii;
use common\traits\Curd;
use addons\Sales\common\models\Order;
use addons\Sales\common\models\OrderAttachment;


/**
 * Default controller for the `order` module
 */
class OrderAttachmentController extends BaseController
{
    use Curd;
    
    /**
     * @var Order
     */
    public $modelClass = OrderAttachment::class;  
    
    /**
     * 批量上传附件
     * @return \yii\web\Response|mixed|string|string
     */
    public function actionAjaxUpload()
    {
        $order_id = Yii::$app->request->get('order_id');
        $model = $this->findModel(null);
        $model->order_id = $order_id;
        // ajax 校验
        //$this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {
            try{
                $trans = Yii::$app->trans->beginTransaction();
                foreach ($model->file as $file) {
                    $_model = new OrderAttachment();
                    $_model->order_id = $model->order_id;
                    $_model->file = $file;
                    if(false === $_model->save()) {
                        throw new \Exception($this->getError($_model));
                    }
                }
                $trans->commit();                
                return $this->message("保存成功", $this->redirect(Yii::$app->request->referrer), 'success');
            }catch (\Exception $e) {
                $trans->rollback();
                return $this->message($e->getMessage(), $this->redirect(Yii::$app->request->referrer), 'error');
            }
        }
        return $this->renderAjax($this->action->id, [
                'model' => $model,
        ]);
    }    
}


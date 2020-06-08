<?php

use yii\widgets\ActiveForm;
use common\helpers\Url;

$form = ActiveForm::begin([
        'id' => $model->formName(),
        'enableAjaxValidation' => true,
        'validationUrl' => Url::to(['change-follower','id' => $model['id']]),
        'fieldConfig' => [
                //'template' => "<div class='col-sm-2 text-right'>{label}</div><div class='col-sm-10'>{input}\n{hint}\n{error}</div>",
        ]
]);
?>
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span></button>
        <h4 class="modal-title">基本信息</h4>
    </div>

    <div class="modal-body">
        <div class="tab-content">
            <?= $form->field($model, 'apply_follower_id')->dropDownList(\Yii::$app->supplyService->supplier->getFollowers($model->supplier_id),['prompt'=>'请选择']);?>
            <?= $form->field($model,'id')->hiddenInput()->label(false)?>

            <!-- /.tab-pane -->
        </div>
        <!-- /.tab-content -->
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
        <button class="btn btn-primary" type="submit">保存</button>
    </div>
<?php ActiveForm::end(); ?>


<?php

use yii\widgets\ActiveForm;
use common\helpers\Url;
$form = ActiveForm::begin([
        'id' => $model->formName(),
        'enableAjaxValidation' => true,
        'validationUrl' => Url::to(['ajax-fqc', 'id' => $model['id']]),
        'fieldConfig' => [
                //'template' => "<div class='col-sm-2 text-right'>{label}</div><div class='col-sm-10'>{input}\n{hint}\n{error}</div>",
        ]
]);
?>

<div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
        <h4 class="modal-title">基本信息</h4>
</div>
    <div class="modal-body">
       <div class="col-sm-12">
            <?= $form->field($model, 'order_id')->hiddenInput(['value'=>$model->order_id])->label(false)?>
            <?= $form->field($model, 'order_sn')->textInput(['readonly'=>true])?>
            <?= $form->field($model, 'problem_type')->radioList(\addons\Sales\common\enums\ProblemTypeEnum::getMap())?>
            <?= $form->field($model, 'problem')->dropDownList(\Yii::$app->salesService->fqc->getDropDown(),['prompt'=>'请选择']);?>
            <?= $form->field($model, 'is_pass')->hiddenInput(['value'=>$model->is_pass])->label(false)?>
            <?= $form->field($model, 'remark')->textArea(['options'=>['maxlength' => true]])?>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
        <button class="btn btn-primary" type="submit">保存</button>
    </div>
<?php ActiveForm::end(); ?>
<?php

use yii\widgets\ActiveForm;
use common\helpers\Url;
$form = ActiveForm::begin([
    'id' => $model->formName(),
    'enableAjaxValidation' => true,
    'validationUrl' => Url::to(['ajax-edit', 'id' => $model['id']]),
    'fieldConfig' => [
        'template' => "<div class='col-sm-2 text-right'>{label}</div><div class='col-sm-10'>{input}\n{hint}\n{error}</div>",
    ]
]);
?>

    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
        <h4 class="modal-title">基本信息</h4>
    </div>
    <div class="modal-body">

        <?= $form->field($model, 'member_id')->widget(kartik\select2\Select2::class, [
            'data' => Yii::$app->services->backendMember->getDropDown(),
            'options' => ['placeholder' => '请选择'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);?>
        <?= $form->field($model, 'member_name')->textInput() ?>
        <?= $form->field($model, 'status')->radioList(common\enums\StatusEnum::getMap())?>

                   
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
        <button class="btn btn-primary" type="submit">保存</button>
    </div>
<?php ActiveForm::end(); ?>
<script>
    var formId = 'follower';
    $("#"+formId+"-member_id").change(function(){
        var member_id = $("#"+formId+"-member_id").val();
        if(member_id != '') {
            var member_name=$("#"+formId+"-member_id option:selected").text();
            $("#"+formId+"-member_name").val(member_name);

        }
    });
</script>

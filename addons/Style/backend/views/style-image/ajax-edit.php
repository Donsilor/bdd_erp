<?php

use yii\widgets\ActiveForm;
use common\helpers\Url;
use kartik\datetime\DateTimePicker;
$style_id =  Yii::$app->request->get('style_id');
$returnUrl = Yii::$app->request->get('returnUrl',Url::to(['style/index']));
$form = ActiveForm::begin([
    'id' => $model->formName(),
    'enableAjaxValidation' => true,
    'validationUrl' => Url::to(['ajax-edit', 'id' => $model['id'],'returnUrl'=>$returnUrl]),
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
        <?= $form->field($model, 'image')->widget(common\widgets\webuploader\Files::class, [
            'config' => [
                'pick' => [
                    'multiple' => false,
                ],

            ]
        ]); ?>
        <?= $form->field($model, 'type')->dropDownList(\addons\Style\common\enums\ImageTypeEnum::getMap(),['prompt'=>'请选择']);?>
        <?= $form->field($model, 'position')->dropDownList(\addons\Style\common\enums\ImagePositionEnum::getMap(),['prompt'=>'请选择']);?>
        <?= $form->field($model, 'is_default')->radioList(common\enums\ConfirmEnum::getMap())?>
        <?= $form->field($model, 'status')->radioList(common\enums\StatusEnum::getMap())?>
        <?= \yii\helpers\Html::activeHiddenInput($model,'style_id',array('value'=>$style_id)) ?>

    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
        <button class="btn btn-primary" type="submit">保存</button>
    </div>
<?php ActiveForm::end(); ?>

<script>
    jQuery("#styleimages-type").change(function () {
        var html = '<option>请选择</option>';
        $.ajax({
            url: '<?= \yii\helpers\Url::to(["get-position"]) ?>',
            type: 'post',
            dataType: 'json',
            data: {type: $(this).val()},
            success: function (msg) {
                console.log(msg.data)
                $.each(msg.data, function (key, val) {
                    html += '<option value="' + key + '">' + val + '</option>';
                });
                $("#styleimages-position").html(html);
            }
        })
    });

</script>

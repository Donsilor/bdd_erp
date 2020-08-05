<?php

use addons\Style\common\enums\AttrIdEnum;
use yii\widgets\ActiveForm;
use common\helpers\Url;
use yii\base\Widget;

$form = ActiveForm::begin([
    'id' => $model->formName(),
    'enableAjaxValidation' => true,
    'validationUrl' => Url::to(['ajax-edit', 'id' => $model['id']]),
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
        <div class="row">
            <div class="col-lg-4">
                <?= $form->field($model, 'name')->textInput(['disabled'=>true, "placeholder"=>"系统自动生成"]) ?>
            </div>
            <div class="col-lg-4">
                <?= $form->field($model, 'type')->dropDownList(Yii::$app->attr->valueMap(AttrIdEnum::MAT_STONE_TYPE),['disabled'=>true, 'prompt'=>'请选择']);?>
            </div>
            <div class="col-lg-4">
                <?= $form->field($model, 'style_sn')->widget(\kartik\select2\Select2::class, [
                    'data' => Yii::$app->styleService->stone->getDropDown($model->type),
                    'options' => ['placeholder' => '请选择'],
                    'pluginOptions' => [
                        'allowClear' => false
                    ],
                ]);?>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-4">
                <?= $form->field($model, 'shape')->widget(\kartik\select2\Select2::class, [
                    'data' => \Yii::$app->attr->valueMap(AttrIdEnum::DIA_SHAPE),
                    'options' => ['placeholder' => '请选择'],
                    'pluginOptions' => [
                        'allowClear' => false
                    ],
                ]);?>
            </div>
            <div class="col-lg-4">
                <?= $form->field($model, 'size')->textInput() ?>
            </div>
            <div class="col-lg-4">
                <?= $form->field($model, 'ref_carat')->textInput() ?>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-4">
                <?= $form->field($model, 'real_carat')->textInput() ?>
            </div>
            <div class="col-lg-4">
                <?= $form->field($model, 'karat_num')->textInput() ?>
            </div>
            <div class="col-lg-4">
                <?= $form->field($model, 'karat_price')->textInput() ?>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-4">
                <?= $form->field($model, 'est_cost')->textInput(['disabled'=>true, "placeholder"=>"系统自动计算"]) ?>
            </div>
            <div class="col-lg-4">
                <?= $form->field($model, 'color_scope')->textInput() ?>
            </div>
            <div class="col-lg-4">
                <?= $form->field($model, 'clarity_scope')->textInput() ?>
            </div>
        </div>
        <?= $form->field($model, 'remark')->textarea() ?>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
        <button class="btn btn-primary" type="submit">保存</button>
    </div>
<?php ActiveForm::end(); ?>
<script>
    var formId = 'moissaniteform';
    function fillStyleForm(){
        var style_sn = $("#"+formId+"-style_sn").val();
        if(style_sn != '') {
            $.ajax({
                type: "get",
                url: '<?php echo Url::to(['ajax-get-style'])?>',
                dataType: "json",
                data: {
                    'style_sn': style_sn,
                },
                success: function (data) {
                    if (parseInt(data.code) == 200 && data.data) {
                        $("#"+formId+"-shape").val(data.data.stone_shape).trigger("change");
                    }
                }
            });
        }
    }
    $("#"+formId+"-style_sn").change(function(){
        fillStyleForm();
    });
</script>
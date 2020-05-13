<?php

use yii\widgets\ActiveForm;
use common\helpers\Url;

$form = ActiveForm::begin([
        'id' => $model->formName(),
        'enableAjaxValidation' => true,
        'validationUrl' => Url::to(['produce-oqc','id' => $produce_id]),
        'fieldConfig' => [
                //'template' => "<div class='col-sm-2 text-right'>{label}</div><div class='col-sm-10'>{input}\n{hint}\n{error}</div>",
        ]
]);
?>
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span></button>
        <h4 class="modal-title">OQC质检</h4>
    </div>

    <div class="modal-body">
        <div class="tab-content">
            <div class="row"><div class="col-lg-12"><?= $form->field($model, 'pass_result')->radioList(\addons\Supply\common\enums\QcTypeEnum::getMap())?></div></div>
            <div class="row">
                <div class="col-lg-4"><?= $form->field($model,'pass_num')->textInput()->hint('')?></div>
                <div class="col-lg-4"><?= $form->field($model,'failed_num')->textInput()->hint('')?></div>
                <div class="col-lg-4"><?= $form->field($model,'failed_reason')->textInput()->hint('')?></div>
            </div>
            <div class="row" style="display: none" id="nopass_param">
                <div class="col-lg-4"><?= $form->field($model,'nopass_num')->textInput()->hint('')?></div>
                <div class="col-lg-4"><?= $form->field($model, 'nopass_type')->dropDownList(\addons\Supply\common\enums\NopassTypeEnum::getMap(),['prompt'=>'请选择']);?></div>
                <div class="col-lg-4"><?= $form->field($model, 'nopass_reason')->dropDownList(\addons\Supply\common\enums\NopassReasonEnum::getMap(),['prompt'=>'请选择']);?></div>
            </div>
            <div class="row"><div class="col-lg-12"><?= $form->field($model,'remark')->textInput()->hint('')?></div></div>

        </div>
        <!-- /.tab-content -->
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
        <input type="hidden" name="id" value="<?= $produce_id?>"/>
        <button class="btn btn-primary" type="submit">保存</button>
    </div>
<?php ActiveForm::end(); ?>
<script>
    $("#produceoqc-pass_result").change(function(){
        var pass_result = $(this).find(':checked').val();
        if(pass_result == 0){
            $("#nopass_param").show()
        }else {
            $("#nopass_param").find('input').val('')
            $("#nopass_param").find('select').find("option:first").prop("selected",true);
            $("#nopass_param").hide()



        }
    })
    
</script>

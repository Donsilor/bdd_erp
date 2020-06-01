<?php

use yii\widgets\ActiveForm;
use common\helpers\Url;
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
        <div class="col-lg-12"><?= $form->field($model, 'goods_status')->radioList(\addons\Supply\common\enums\QcTypeEnum::getMap())->label("是否质检通过")?></div>
        <div class="col-sm-12" style="display: none" id="nopass_param">
	        <?= $form->field($model, 'iqc_reason')->widget(\kartik\select2\Select2::class, [
                    'data' => Yii::$app->purchaseService->fqc->getDropDown(),
                    'options' => ['placeholder' => '请选择'],
                    'pluginOptions' => [
                        'allowClear' => false
                    ],
                ]);?>
            <?= $form->field($model, 'iqc_remark')->textArea(['options'=>['maxlength' => true]])?>
       </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
        <button class="btn btn-primary" type="submit">保存</button>
    </div>
<?php ActiveForm::end(); ?>
<script>
    $("#purchasereceiptgoodsform-goods_status").change(function(){
        var status = $(this).find(':checked').val();
        if(status == 0){
            $("#nopass_param").show();
        }else {
            $("#select2-purchasereceiptgoodsform-iqc_reason-container").find('select').find("option:first").prop("selected",true);
            $("#purchasereceiptgoodsform-iqc_remark").val("");
            $("#nopass_param").hide();
        }
    })
</script>

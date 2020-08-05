<?php

use yii\widgets\ActiveForm;
?>
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <?php $form = ActiveForm::begin([]); ?>
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
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

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
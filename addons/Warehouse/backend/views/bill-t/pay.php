<?php
use yii\widgets\ActiveForm;
?>
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <?php $form = ActiveForm::begin([]); ?>
            <div class="modal-body">
                <?= $form->field($model, 'supplier_id')->widget(\kartik\select2\Select2::class, [
                    'data' => \Yii::$app->supplyService->supplier->getDropDown(),
                    'options' => ['placeholder' => '请选择'],
                    'pluginOptions' => [
                        'allowClear' => false,
                        'disabled' => true,
                    ],
                ]); ?>
                <div class="row">
                    <div class="col-sm-6">
                        <?= $form->field($model, 'pay_content')->widget(\kartik\select2\Select2::class, [
                            'data' => \addons\Warehouse\common\enums\PayContentEnum::getMap(),
                            'options' => ['placeholder' => '请选择'],
                            'pluginOptions' => [
                                'allowClear' => false
                            ],
                        ]); ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model, 'pay_amount')->textInput() ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <?= $form->field($model, 'pay_method')->radioList(\addons\Warehouse\common\enums\PayMethodEnum::getMap()) ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model, 'pay_tax')->radioList(\addons\Warehouse\common\enums\PayTaxEnum::getMap()) ?>
                    </div>
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

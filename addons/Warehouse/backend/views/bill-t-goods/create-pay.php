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
                    <div class="col-sm-6" id="pay1">
                        <?= $form->field($model, 'pay_amount')->textInput(['value'=> $total['pay_amount']]) ?>
                    </div>
                </div>
                <div class="row" style="display: none" id="pay2">
                    <div class="col-sm-6">
                        <?= $form->field($model, 'pay_material')->radioList(\addons\Warehouse\common\enums\PayMaterialEnum::getMap()) ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model, 'pay_gold_weight')->textInput(['value'=> $total['pay_gold_weight']]) ?>
                    </div>
                </div>
                <div class="row" style="display: none" id="pay2">
                    <div class="col-sm-6">
                        <?= $form->field($model, 'pay_material')->radioList(\addons\Warehouse\common\enums\PayMaterialEnum::getMap()) ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model, 'pay_gold_weight')->textInput() ?>
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
    var formId = 'warehousebillpayform';
    $("#"+formId+"-pay_content").change(function () {
        var pay_content = $(this).find(':checked').val();
        if (pay_content == 1) {
            $("#pay1").show();
            $("#pay2").hide();
            // $("#"+formId+"-pay_material").attr("checked",false);
            // $("#"+formId+"-pay_gold_weight").val("");
        } else {
            $("#pay2").show();
            $("#pay1").hide();
            //$("#"+formId+"-pay_amount").val("");
        }
    })
</script>

<?php

use addons\Warehouse\common\enums\BillTypeEnum;
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
    <div class="col-sm-12">
        <?= $form->field($model, 'bill_no')->textInput(['disabled'=>true, "placeholder"=>"系统自动生成"])?>
        <?= $form->field($model, 'delivery_type')->widget(\kartik\select2\Select2::class, [
            'data' => \addons\Warehouse\common\enums\DeliveryTypeEnum::getMap(),
            'options' => ['placeholder' => '请选择'],
            'pluginOptions' => [
                'allowClear' => false
            ],
        ]);?>
        <div style="display: none" id="supplier">
            <?= $form->field($model, 'supplier_id')->widget(\kartik\select2\Select2::class, [
                'data' => \Yii::$app->supplyService->supplier->getDropDown(),
                'options' => ['placeholder' => '请选择'],
                'pluginOptions' => [
                    'allowClear' => false
                ],
            ]);?>
        </div>
        <div style="display: none" id="channel">
        <?= $form->field($model, 'channel_id')->widget(\kartik\select2\Select2::class, [
            'data' => \Yii::$app->styleService->styleChannel->getDropDown(),
            'options' => ['placeholder' => '请选择'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);?>
        </div>
        <?= $form->field($model, 'order_sn')->textInput() ?>
        <?= $form->field($model, 'remark')->textArea(); ?>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
    <button class="btn btn-primary" type="submit">保存</button>
</div>
<?php ActiveForm::end(); ?>
<script>
    $("#warehousebillcform-delivery_type").change(function(){
        var id = $(this).find(':checked').val();
        if($.inArray(id,['3','4','5'])>=0){
            $("#supplier").show();
        }else {
            $("#warehousebillcform-supplier_id").find('select').find("option:first").prop("selected",true);
            $("#supplier").hide();
        }
        if($.inArray(id,['1','2'])>=0){
            $("#channel").show();
        }else {
            $("#warehousebillcform-channel_id").find('select').find("option:first").prop("selected",true);
            $("#channel").hide();
        }
    })
</script>

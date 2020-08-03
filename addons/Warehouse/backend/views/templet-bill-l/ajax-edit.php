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
    <?= $form->field($model, 'bill_no')->textInput(['disabled'=>true, "placeholder"=>"系统自动生成"])?>
    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'supplier_id')->widget(\kartik\select2\Select2::class, [
                'data' => \Yii::$app->supplyService->supplier->getDropDown(),
                'options' => ['placeholder' => '请选择'],
                'pluginOptions' => [
                    'allowClear' => false
                ],
            ]);?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'channel_id')->widget(\kartik\select2\Select2::class, [
                'data' => \Yii::$app->salesService->saleChannel->getDropDown(),
                'options' => ['placeholder' => '请选择'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'to_warehouse_id')->widget(\kartik\select2\Select2::class, [
                'data' => Yii::$app->warehouseService->warehouse->getDropDown(),
                'options' => ['placeholder' => '请选择'],
                'pluginOptions' => [
                    'allowClear' => false
                ],
            ]);?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'put_in_type')->widget(\kartik\select2\Select2::class, [
                'data' => \addons\Warehouse\common\enums\PutInTypeEnum::getMap(),
                'options' => ['placeholder' => '请选择'],
                'pluginOptions' => [
                    'allowClear' => false
                ],
            ]);?>
        </div>
    </div>
    <?= $form->field($model, 'delivery_no')->textInput(); ?>
    <?= $form->field($model, 'remark')->textArea(); ?>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
    <button class="btn btn-primary" type="submit">保存</button>
</div>
<?php ActiveForm::end(); ?>

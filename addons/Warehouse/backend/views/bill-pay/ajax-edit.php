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
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span>
    </button>
    <h4 class="modal-title">基本信息</h4>
</div>
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
<div class="modal-footer">
    <?= $form->field($bill, 'id')->hiddenInput()->label(false) ?>
    <button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
    <button class="btn btn-primary" type="submit">保存</button>
</div>
<?php ActiveForm::end(); ?>

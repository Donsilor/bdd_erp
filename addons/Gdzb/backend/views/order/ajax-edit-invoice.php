<?php

use yii\widgets\ActiveForm;
use common\helpers\Url;
use yii\base\Widget;

$form = ActiveForm::begin([
        'id' => $model->formName(),
        'enableAjaxValidation' => true,
        'validationUrl' => Url::to(['ajax-edit-invoice', 'id' => $model->id]),
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
            <?= $form->field($model, 'id')->hiddenInput()->label(false)?>
            <div class="row">
                <div class="col-lg-6"> 
                <?= $form->field($model, 'is_invoice')->radioList(addons\Sales\common\enums\IsInvoiceEnum::getMap())?>
                </div>
                <div class="col-lg-6">
                 <?= $form->field($model, 'invoice_type')->radioList(addons\Sales\common\enums\InvoiceTypeEnum::getMap())?>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6"> 
                <?= $form->field($model, 'title_type')->radioList(addons\Sales\common\enums\InvoiceTitleTypeEnum::getMap())?>                 
                </div>
                <div class="col-lg-6">                 
               <?= $form->field($model, 'invoice_title')->textInput(['maxlength' => true]) ?>                          
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6"> 
                <?= $form->field($model, 'tax_number')->textInput(['maxlength' => true]) ?>                
                </div>
                <div class="col-lg-6">                 
                <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>                          
                </div>
            </div>
        <div class="row">
            <div class="col-lg-12">
                <?= $form->field($model, 'invoice_status')->radioList(\addons\Gdzb\common\enums\InvoiceStatusEnum::getMap())?>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
        <button class="btn btn-primary" type="submit">保存</button>
    </div>
<?php ActiveForm::end(); ?>

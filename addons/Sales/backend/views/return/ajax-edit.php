<?php

use common\enums\StatusEnum;
use common\widgets\webuploader\Files;
use kartik\date\DatePicker;
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
                <?= $form->field($model, 'name')->textInput(['maxlength' => true]); ?>
            </div>
            <div class="col-lg-4">
                <?= $form->field($model, 'express_man')->textInput(['maxlength' => true]); ?>
            </div>
            <div class="col-lg-4">
                <?= $form->field($model, 'express_phone')->textInput(['maxlength' => true]); ?>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-4">
                <?= $form->field($model, 'company_man')->textInput(['maxlength' => true]); ?>
            </div>
            <div class="col-lg-4">
                <?= $form->field($model, 'company_phone')->textInput(['maxlength' => true]); ?>
            </div>
            <div class="col-lg-4">
                <?= $form->field($model, 'receive_time')->textInput(['maxlength' => true]); ?>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-4">
                <?php $model->settlement_way = !empty($model->settlement_way)?array_filter(explode(',', $model->settlement_way)):null;?>
                <?= $form->field($model, 'settlement_way')->checkboxList(\addons\Sales\common\enums\SettlementWayEnum::getMap())?>
            </div>
            <div class="col-lg-4">
                <?php $model->settlement_period = !empty($model->settlement_period)?array_filter(explode(',', $model->settlement_period)):null;?>
                <?= $form->field($model, 'settlement_period')->checkboxList(\addons\Sales\common\enums\SettlementPeriodEnum::getMap())?>
            </div>
            <div class="col-lg-4">
                <?php $model->delivery_scope = !empty($model->delivery_scope)?array_filter(explode(',', $model->delivery_scope)):null;?>
                <?= $form->field($model, 'delivery_scope')->checkboxList(\addons\Sales\common\enums\DeliveryScopeEnum::getMap())?>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
        <button class="btn btn-primary" type="submit">保存</button>
    </div>
<?php ActiveForm::end(); ?>

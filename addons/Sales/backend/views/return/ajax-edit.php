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
<!--            <div class="col-lg-4">-->
<!--                --><?//= $form->field($model, 'return_type')->radioList(\addons\Sales\common\enums\ReturnTypeEnum::getMap()) ?>
<!--            </div>-->
<!--            <div class="col-lg-6">-->
<!--                --><?//= $form->field($model, 'is_quick_refund')->radioList(\common\enums\ConfirmEnum::getMap()) ?>
<!--            </div>-->
            <div class="col-lg-6">
                <?= $form->field($model, 'return_by')->radioList(\addons\Sales\common\enums\ReturnByEnum::getMap()) ?>
            </div>
            <div class="col-lg-6">
                <?= $form->field($model, 'return_reason')->dropDownList(\Yii::$app->salesService->returnConfig->getDropDown(),['prompt'=>'请选择']);?>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6">
                <?= $form->field($model, 'bank_name')->textInput(); ?>
            </div>
            <div class="col-lg-6">
                <?= $form->field($model, 'bank_card')->textInput(); ?>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <?= $form->field($model, 'remark')->textarea() ?>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
        <button class="btn btn-primary" type="submit">保存</button>
    </div>
<?php ActiveForm::end(); ?>

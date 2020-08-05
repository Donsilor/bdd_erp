<?php

use common\widgets\webuploader\Files;
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
            <?php if($model->isNewRecord){?>
                <?= $form->field($model, 'express_id')->hiddenInput()->label(false); ?>
            <?php }?>
            <div class="col-lg-6">
                <?= $form->field($model, 'delivery_area')->textInput(['maxlength' => true]); ?>
            </div>
            <div class="col-lg-6">
                <?= $form->field($model, 'delivery_time')->textInput(['maxlength' => true]); ?>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6">
                <?= $form->field($model, 'first_price')->textInput(['maxlength' => true]); ?>
            </div>
            <div class="col-lg-6">
                <?= $form->field($model, 'supply_price')->textInput(['maxlength' => true]); ?>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6">
                <?= $form->field($model, 'is_holidays')->radioList(\common\enums\ConfirmEnum::getMap())?>
            </div>
            <div class="col-lg-6">
                <?= $form->field($model, 'status')->radioList(\common\enums\StatusEnum::getMap())?>
            </div>
        </div>
        <?= $form->field($model, 'remark')->textarea(); ?>
        <?= $form->field($model, 'last_first_price')->hiddenInput(['value'=>$model->first_price??0])->label(false); ?>
        <?= $form->field($model, 'last_supply_price')->hiddenInput(['value'=>$model->supply_price??0])->label(false); ?>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
        <button class="btn btn-primary" type="submit">保存</button>
    </div>
<?php ActiveForm::end(); ?>

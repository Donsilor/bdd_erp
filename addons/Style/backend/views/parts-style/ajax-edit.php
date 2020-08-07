<?php

use yii\widgets\ActiveForm;
use common\helpers\Url;
use addons\Style\common\enums\AttrIdEnum;

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
    <div class="row">
        <div class="col-lg-6">
            <?= $form->field($model, 'style_sn')->textInput() ?>
        </div>
        <div class="col-lg-6">
            <?= $form->field($model, 'parts_type')->dropDownList(Yii::$app->attr->valueMap(AttrIdEnum::MAT_PARTS_TYPE), ['prompt' => '请选择']); ?>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-6">
            <?= $form->field($model, 'parts_name')->textInput() ?>
        </div>
        <div class="col-lg-6">
            <?= $form->field($model, 'metal_type')->dropDownList(Yii::$app->attr->valueMap(AttrIdEnum::MATERIAL_TYPE), ['prompt' => '请选择']); ?>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-6">
            <?= $form->field($model, 'color')->dropDownList(Yii::$app->attr->valueMap(AttrIdEnum::MATERIAL_COLOR), ['prompt' => '请选择']); ?>
        </div>
        <div class="col-lg-6">
            <?= $form->field($model, 'shape')->dropDownList(Yii::$app->attr->valueMap(AttrIdEnum::MAT_PARTS_SHAPE), ['prompt' => '请选择']); ?>
        </div>
    </div>
    <?= $form->field($model, 'remark')->textarea() ?>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
    <button class="btn btn-primary" type="submit">保存</button>
</div>
<?php ActiveForm::end(); ?>

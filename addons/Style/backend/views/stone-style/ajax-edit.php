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
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
        <h4 class="modal-title">基本信息</h4>
    </div>
    <div class="modal-body">
        <?= $form->field($model, 'style_sn')->textInput() ?>
        <?= $form->field($model, 'stone_type')->dropDownList(Yii::$app->attr->valueMap(AttrIdEnum::MAT_STONE_TYPE),['prompt'=>'请选择']);?>
        <div class="row">
            <div class="col-lg-6">
                <?= $form->field($model, 'stone_weight_min')->textInput() ?>
            </div>
            <div class="col-lg-6">
                <?= $form->field($model, 'stone_weight_max')->textInput() ?>
            </div>
        </div>
        <?= $form->field($model, 'cert_type')->dropDownList(Yii::$app->attr->valueMap(AttrIdEnum::DIA_CERT_TYPE),['prompt'=>'请选择']);?>
        <?= $form->field($model, 'remark')->textarea() ?>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
        <button class="btn btn-primary" type="submit">保存</button>
    </div>
<?php ActiveForm::end(); ?>

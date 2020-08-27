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
        <div class="col-sm-12">
            <div class="row">
                <div class="col-lg-6"><?= $form->field($model, 'style_name')->textInput() ?></div>
                <div class="col-lg-6"><?= $form->field($model, 'style_sn')->textInput(['disabled' => $model->isNewRecord ? null : 'disabled', 'placeholder' => '编号为空时系统自动生成']) ?></div>
            </div>
            <div class="row">
                <div class="col-lg-6">
                    <?= $form->field($model, 'style_cate_id')->widget(\kartik\select2\Select2::class, [
                        'data' => \Yii::$app->styleService->styleCate->getGrpDropDown(),
                        'options' => ['placeholder' => '请选择'],
                        'pluginOptions' => [
                            'allowClear' => false,
                            'disabled' => $model->isNewRecord ? null : 'disabled'
                        ],
                    ]); ?>
                </div>
                <div class="col-lg-6">
                    <?= $form->field($model, 'product_type_id')->widget(\kartik\select2\Select2::class, [
                        'data' => \Yii::$app->styleService->productType->getGrpDropDown(),
                        'options' => ['placeholder' => '请选择'],
                        'pluginOptions' => [
                            'allowClear' => false,
                            'disabled' => $model->isNewRecord || empty($model->product_type_id) ? null : 'disabled'
                        ],
                    ]); ?>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6">
                    <?= $form->field($model, 'style_channel_id')->widget(\kartik\select2\Select2::class, [
                        'data' => \Yii::$app->styleService->styleChannel->getDropDown(),
                        'options' => ['placeholder' => '请选择'],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ]); ?>
                </div>
                <div class="col-lg-6">
                    <?= $form->field($model, 'style_source_id')->widget(\kartik\select2\Select2::class, [
                        'data' => \Yii::$app->styleService->styleSource->getDropDown(),
                        'options' => ['placeholder' => '请选择'],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ]); ?>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6"><?= $form->field($model, 'style_material')->radioList(\addons\Style\common\enums\StyleMaterialEnum::getMap()) ?></div>
                <div class="col-lg-6"><?= $form->field($model, 'style_sex')->radioList(\addons\Style\common\enums\StyleSexEnum::getMap()) ?></div>
            </div>
            <div class="row">
                <div class="col-lg-6"><?= $form->field($model, 'is_made')->radioList(\common\enums\ConfirmEnum::getMap()) ?></div>
                <div class="col-lg-6"><?= $form->field($model, 'is_gift')->radioList(\common\enums\ConfirmEnum::getMap()) ?></div>
            </div>
            <div class="row">
                <div class="col-lg-12"><?= $form->field($model, 'remark')->textArea(['options' => ['maxlength' => true]]) ?></div>
            </div>
        </div>

    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
        <button class="btn btn-primary" type="submit">保存</button>
    </div>
<?php ActiveForm::end(); ?>
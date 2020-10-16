<?php

use common\helpers\Url;
use kartik\select2\Select2;
use yii\widgets\ActiveForm;

$form = ActiveForm::begin([
    'id' => $model->formName(),
    'enableAjaxValidation' => true,
    'validationUrl' => Url::to(['batch-edit', 'id' => $model['id']]),
    'fieldConfig' => [
        //'template' => "<div class='col-sm-3 text-right'>{label}</div><div class='col-sm-9'>{input}\n{hint}\n{error}</div>",
    ]
]);
?>
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-body">
                <div class="row">
                    <div class="col-sm-2 text-right">
                        <label class="control-label">
                            <?= $model->getAttributeLabel($model->batch_name) ?>
                        </label>
                    </div>
                    <div class="col-sm-8">
                        <?= $form->field($model, 'batch_value')->widget(kartik\select2\Select2::class, [
                            'data' => $model->attr_list,
                            'options' => ['placeholder' => '请选择'],
                            'pluginOptions' => [
                                'allowClear' => true
                            ],
                        ])->label(false); ?>
                    </div>
                    <?= $form->field($model, 'ids')->hiddenInput()->label(false) ?>
                    <?= $form->field($model, 'attr_id')->hiddenInput()->label(false) ?>
                    <?= $form->field($model, 'batch_name')->hiddenInput()->label(false) ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>
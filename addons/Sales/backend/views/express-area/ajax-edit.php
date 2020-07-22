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
            <div>
                <?= $form->field($model, 'name')->textInput(['maxlength' => true]); ?>
                <?= $form->field($model, 'express_man')->textInput(['maxlength' => true]); ?>
                <?= $form->field($model, 'express_phone')->textInput(['maxlength' => true]); ?>
            </div>
            <!--<?= $form->field($model, 'code')->textInput(); ?>-->
            <?= $form->field($model, 'cover')->widget(common\widgets\webuploader\Files::class, [
                'config' => [
                    'pick' => [
                        'multiple' => false,
                    ],
                ]
            ]); ?>
            <?= $form->field($model, 'sort')->textInput(); ?>
            <?= $form->field($model, 'status')->radioList(common\enums\StatusEnum::getMap())?>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
        <button class="btn btn-primary" type="submit">保存</button>
    </div>
<?php ActiveForm::end(); ?>

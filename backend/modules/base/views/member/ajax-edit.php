<?php

use yii\widgets\ActiveForm;
use common\helpers\Url;
use common\enums\AppEnum;

$form = ActiveForm::begin([
    'id' => $model->formName(),
    'enableAjaxValidation' => true,
    'validationUrl' => Url::to(['ajax-edit', 'id' => $model['id']]),
    'fieldConfig' => [
        //'template' => "<div class='col-sm-3 text-right'>{label}</div><div class='col-sm-9'>{input}\n{hint}\n{error}</div>",
    ]
]);
?>
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
        <h4 class="modal-title">基本信息</h4>
    </div>
    <div class="modal-body">
        <?= $form->field($model, 'username')->textInput([
            'readonly' => !empty($model->username)
        ])->hint('账号创建后不可修改') ?>
        <?= $form->field($model, 'password')->passwordInput() ?>
        <?php if ($model->id != Yii::$app->params['adminAccount']) { ?>
            <?= $form->field($model, 'dept_id')->dropDownList(Yii::$app->services->department->getDropDown(),['prompt' => '请选择']) ?>
            <?= $form->field($model, 'role_id')->dropDownList(Yii::$app->services->rbacAuthRole->getDropDown(AppEnum::BACKEND, true),['prompt' => '请选择']) ?>
        <?php } ?>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
        <button class="btn btn-primary" type="submit">保存</button>
    </div>
<?php ActiveForm::end(); ?>
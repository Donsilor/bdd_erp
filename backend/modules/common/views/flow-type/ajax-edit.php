<?php

use yii\widgets\ActiveForm;
use common\helpers\Url;
use common\enums\StatusEnum;

$form = ActiveForm::begin([
    'id' => $model->formName(),
    'enableAjaxValidation' => true,
    'validationUrl' => Url::to(['ajax-edit', 'id' => $model['id']]),
    'fieldConfig' => [
        'template' => "<div class='col-sm-2 text-right'>{label}</div><div class='col-sm-10'>{input}\n{hint}\n{error}</div>",
    ]
]);
?>

    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
        <h4 class="modal-title">基本信息</h4>
    </div>
    <div class="modal-body">
        <?= $form->field($model, 'name')->textInput(); ?>
        <?= $form->field($model, 'cate')->dropDownList(\common\enums\FlowCateEnum::getMap(),['prompt'=>'请选择']) ?>
        <?= $form->field($model, 'method')->dropDownList(\common\enums\FlowMethodEnum::getMap(),['prompt'=>'请选择']) ?>
        <?= $form->field($model, 'users')->widget(unclead\multipleinput\MultipleInput::class, [
            'max' => 15,
            'value' => $user_id_arr,
            'allowEmptyList'=>false,
            'enableGuessTitle'=>true,
            'columns'=> [
                [
                    'name'  => 'user_id',
                    'type'  => \kartik\select2\Select2::class,
                    'options' => [
                        'data'  =>Yii::$app->services->backendMember->getMap(),
                    ],


                ],
            ]

        ]);
        ?>
        <?= $form->field($model, 'status')->radioList(StatusEnum::getMap()); ?>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
        <button class="btn btn-primary" type="submit">保存</button>
    </div>
<?php ActiveForm::end(); ?>
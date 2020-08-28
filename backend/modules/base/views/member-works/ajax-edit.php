<?php

use yii\widgets\ActiveForm;
use common\helpers\Url;
use kartik\date\DatePicker;
$returnUrl = Yii::$app->request->get('returnUrl',Url::to(['member-works/index']));
$form = ActiveForm::begin([
    'id' => $model->formName(),
    'enableAjaxValidation' => true,
    'validationUrl' => Url::to(['ajax-edit', 'id' => $model['id'],'returnUrl'=>$returnUrl]),
    'fieldConfig' => [
//        'template' => "<div class='col-sm-2 text-right'>{label}</div><div class='col-sm-10'>{input}\n{hint}\n{error}</div>",
    ]
]);
?>
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
        <h4 class="modal-title">基本信息</h4>
    </div>
    <div class="modal-body">
<!--        --><?//= $form->field($model, 'type')->dropDownList(Yii::$app->attr->valueMap(\common\enums\WorksTypeEnum::getMap()), ['prompt' => '请选择']); ?>
        <?= $form->field($model, 'title')->textInput() ?>
        <?= $form->field($model, 'content')->textarea(['placeholder'=>'1.工作1...（已完成）','rows'=>8]) ?>
        <?= $form->field($model, 'date')->widget(DatePicker::class, [
            'options' => [
                'value' => $model->isNewRecord ? date('Y-m-d') : $model->date,
                'readonly' => true,
            ],
            'pluginOptions' => [
                'format' => 'yyyy-mm-dd',
                'todayHighlight' => true,//今日高亮
                'autoclose' => true,//选择后自动关闭
                'endDate' => date("yyyy-MM-dd H:i:s"),
                'todayBtn' => true,//今日按钮显示
            ]
        ]);?>

    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
        <button class="btn btn-primary" type="submit">保存</button>
    </div>
<?php ActiveForm::end(); ?>

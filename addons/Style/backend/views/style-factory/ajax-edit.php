<?php

use yii\widgets\ActiveForm;
use common\helpers\Url;
use kartik\datetime\DateTimePicker;
$style_id =  Yii::$app->request->get('style_id');
$returnUrl = Yii::$app->request->get('returnUrl',Url::to(['style/index']));
$form = ActiveForm::begin([
    'id' => $model->formName(),
    'enableAjaxValidation' => true,
    'validationUrl' => Url::to(['ajax-edit', 'id' => $model['id'],'returnUrl'=>$returnUrl]),
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
        <?= $form->field($model, 'factory_id')->widget(kartik\select2\Select2::class, [
            'data' => Yii::$app->supplyService->factory->getDropDown(),
            'options' => ['placeholder' => '请选择'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);?>
        <?= $form->field($model, 'factory_mo')->textInput() ?>
        <?= $form->field($model, 'remark')->textInput() ?>
        <?= $form->field($model, 'shipping_time')->widget(DateTimePicker::class, [
            'language' => 'zh-CN',
            'options' => [
                'value' => $model->isNewRecord ? date('Y-m-d H:i:s') : date('Y-m-d H:i:s', $model->shipping_time),
            ],
            'pluginOptions' => [
                'format' => 'yyyy-mm-dd hh:ii',
                'todayHighlight' => true,//今日高亮
                'autoclose' => true,//选择后自动关闭
                'todayBtn' => true,//今日按钮显示
            ]
        ]);?>
            <?= $form->field($model, 'is_made')->radioList(common\enums\ConfirmEnum::getMap())?>
            <?= $form->field($model, 'is_default')->radioList(common\enums\ConfirmEnum::getMap())?>
            <?= $form->field($model, 'status')->radioList(common\enums\StatusEnum::getMap())?>
            <?= \yii\helpers\Html::activeHiddenInput($model,'style_id',array('value'=>$style_id)) ?>

    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
        <button class="btn btn-primary" type="submit">保存</button>
    </div>
<?php ActiveForm::end(); ?>

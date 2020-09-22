<?php

use yii\widgets\ActiveForm;
use common\helpers\Url;

$form = ActiveForm::begin([
        'id' => $model->formName(),
        'enableAjaxValidation' => true,
        'validationUrl' => Url::to(['ajax-delivery','id' => $model['id']]),
        'fieldConfig' => [
                //'template' => "<div class='col-sm-2 text-right'>{label}</div><div class='col-sm-10'>{input}\n{hint}\n{error}</div>",
        ]
]);
?>
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span></button>
        <h4 class="modal-title">基本信息</h4>
    </div>

    <div class="modal-body">
        <div class="tab-content">
            <?= $form->field($model, 'express_id')->widget(kartik\select2\Select2::class, [
                'data' => \Yii::$app->salesService->express->getDropDown(),
                'options' => ['placeholder' => '请选择'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);?>
            <?= $form->field($model, 'express_no')->textArea(); ?>
            <?= $form->field($model, 'delivery_time')->widget(\kartik\date\DatePicker::class, [
                'options' => [
                    'value' => $model->isNewRecord ? date('Y-m-d') : $model->delivery_time,
                    'readonly' => true,
                ],
                'pluginOptions' => [
                    'format' => 'yyyy-mm-dd',
                    'todayHighlight' => true,//今日高亮
                    'autoclose' => true,//选择后自动关闭
//                    'endDate' => date("yyyy-MM-dd H:i:s"),
                    'todayBtn' => true,//今日按钮显示
                ]
            ]);?>
            <!-- /.tab-pane -->
        </div>
        <!-- /.tab-content -->
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
        <button class="btn btn-primary" type="submit">保存</button>
    </div>
<?php ActiveForm::end(); ?>
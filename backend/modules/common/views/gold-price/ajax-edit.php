<?php
use yii\widgets\ActiveForm;
use common\helpers\Url;

$form = ActiveForm::begin([
    'id' => $model->formName(),
    'enableAjaxValidation' => true,
    'validationUrl' => Url::to(['ajax-edit','id' => $model['id']]),
    'fieldConfig' => [
        //'template' => "<div class='col-sm-2 text-right'>{label}</div><div class='col-sm-10'>{input}\n{hint}\n{error}</div>",
    ]
]);
if(is_string($model->notice_users)) {
    $model->notice_users = explode(',',$model->notice_users);
}
?>
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span></button>
        <h4 class="modal-title">基本信息</h4>
    </div>

    <div class="modal-body">
        <div class="tab-content">
           <?= $form->field($model, 'name')->textInput(); ?>
           <?= $form->field($model, 'code')->textInput(); ?>
           <?= $form->field($model, 'notice_range')->textInput(); ?>
           <?= $form->field($model, 'notice_users')->widget(\kartik\select2\Select2::class, [
                   'data' => Yii::$app->services->backendMember->getDropDown(),
                   'options' => ['placeholder' => '请选择','multiple' => true],
                    'pluginOptions' => [
                        'allowClear' => true,                        
                    ],
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
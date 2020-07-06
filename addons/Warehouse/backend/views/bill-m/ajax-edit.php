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
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
        <h4 class="modal-title">基本信息</h4>
</div>
    <div class="modal-body"> 
       <div class="col-sm-12">
            <?= $form->field($model, 'bill_no')->textInput(['disabled'=>true, "placeholder"=>"系统自动生成"])?>
            <?= $form->field($model, 'from_warehouse_id')->widget(\kartik\select2\Select2::class, [
                'data' => \Yii::$app->warehouseService->warehouse::getDropDown(),
                'options' => ['placeholder' => '请选择'],
                'pluginOptions' => [
                    'allowClear' => false
                ],
            ]);?>
	        <?= $form->field($model, 'to_warehouse_id')->widget(\kartik\select2\Select2::class, [
                    'data' => \Yii::$app->warehouseService->warehouse::getDropDown(),
                    'options' => ['placeholder' => '请选择'],
                    'pluginOptions' => [
                        'allowClear' => false
                    ],
            ]);?>
            <?= $form->field($model, 'remark')->textArea(['options'=>['maxlength' => true]])?>
        </div>    
                   
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
        <button class="btn btn-primary" type="submit">保存</button>
    </div>
<?php ActiveForm::end(); ?>
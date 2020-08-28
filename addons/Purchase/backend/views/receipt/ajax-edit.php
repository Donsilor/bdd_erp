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
            <?= $form->field($model, 'receipt_no')->textInput(['disabled'=>true, "placeholder"=>"系统自动生成"])?>
	        <?= $form->field($model, 'supplier_id')->widget(\kartik\select2\Select2::class, [
                    'data' => \Yii::$app->supplyService->supplier->getDropDown(),
                    'options' => ['placeholder' => '请选择'],
                    'pluginOptions' => [
                        'allowClear' => false,
                        'disabled'=>$model->isNewRecord ? null:'disabled'
                    ],
                ]);?>
            <?= $form->field($model, 'put_in_type')->widget(\kartik\select2\Select2::class, [
                'data' => \addons\Warehouse\common\enums\PutInTypeEnum::getMap(),
                'options' => ['placeholder' => '请选择'],
                'pluginOptions' => [
                    'allowClear' => false
                ],
            ]);?>
            <?= $form->field($model, 'delivery_no')->textInput(["placeholder"=>"请输入工厂出货单号"])?>
            <?= $form->field($model, 'remark')->textArea(['options'=>['maxlength' => true]])?>
        </div>    
                   
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
        <button class="btn btn-primary" type="submit">保存</button>
    </div>
<?php ActiveForm::end(); ?>
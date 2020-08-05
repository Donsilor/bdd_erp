<?php

use yii\widgets\ActiveForm;
use common\helpers\Url;
use kartik\date\DatePicker;
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
            <?= $form->field($model, 'purchase_sn')->textInput(['disabled'=>true, "placeholder"=>"系统自动生成"])?>
	        <?= $form->field($model, 'supplier_id')->widget(\kartik\select2\Select2::class, [
                    'data' => \Yii::$app->supplyService->supplier->getDropDown(['like', 'business_scope', ',10,']),
                    'options' => ['placeholder' => '请选择'],
                    'pluginOptions' => [
                        'allowClear' => false
                    ],
            ]);?>    
            <?= $form->field($model, 'delivery_time')->widget(DatePicker::class, [
                'language' => 'zh-CN',
                'options' => [
                     'value' => $model->delivery_time ? date('Y-m-d', $model->delivery_time) :'',
                ],
                'pluginOptions' => [
                    'format' => 'yyyy-mm-dd',
                    'todayHighlight' => true,//今日高亮
                    'autoclose' => true,//选择后自动关闭
                    'todayBtn' => true,//今日按钮显示
                ]
            ]);?>    
            <?= $form->field($model, 'remark')->textArea(['options'=>['maxlength' => true]])?>
        </div>    
                   
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
        <button class="btn btn-primary" type="submit">保存</button>
    </div>
<?php ActiveForm::end(); ?>
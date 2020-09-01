<?php

use yii\widgets\ActiveForm;
use common\helpers\Url;
$form = ActiveForm::begin([
        'id' => $model->formName(),
        'enableAjaxValidation' => true,
        'validationUrl' => Url::to(['ajax-confirm', 'id' => $model['id']]),
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
           <div class="row">
               <div class="col-lg-6">
                   <?= $form->field($model->order, 'currency')->dropDownList(common\enums\CurrencyEnum::getMap(),['prompt'=>'请选择','disabled'=>'disabled']);?>
               </div>
               <div class="col-lg-6"><?= $form->field($model, 'pay_amount')->textInput(['readonly'=>true])?></div>
           </div>
           <div class="row">
               <div class="col-lg-6"><?= $form->field($model, 'arrival_amount')->textInput()?></div>
               <div class="col-lg-6">
                   <?= $form->field($model, 'arrival_time')->widget(\kartik\date\DatePicker::class, [
                       'options' => [
                           'value' => date('Y-m-d') ,
                           'readonly' => false,
                       ],
                       'pluginOptions' => [
                           'format' => 'yyyy-mm-dd',
                           'todayHighlight' => true,//今日高亮
                           'autoclose' => true,//选择后自动关闭
                           'endDate' => date("yyyy-MM-dd"),
                           'todayBtn' => true,//今日按钮显示
                       ]
                   ]);?>
               </div>
           </div>

        </div>    
                   
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
        <button class="btn btn-primary" type="submit">确认到账</button>
    </div>
<?php ActiveForm::end(); ?>
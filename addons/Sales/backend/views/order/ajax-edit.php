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
            <div class="row">
                <div class="col-lg-6">
                <?= $form->field($model, 'sale_channel_id')->widget(\kartik\select2\Select2::class, [
                    'data' => \Yii::$app->styleService->styleChannel->getDropDown(),
                    'options' => ['placeholder' => '请选择'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]);?>              
                </div>
                <div class="col-lg-6"><?= $form->field($model, 'customer_name')->textInput()?></div>
            </div>
            <div class="row">
            	<div class="col-lg-6"><?= $form->field($model, 'customer_mobile')->textInput()?></div>
            	<div class="col-lg-6"><?= $form->field($model, 'customer_email')->textInput()?></div>                
            </div>
            <div class="row">
                <div class="col-lg-6">
                <?= $form->field($model, 'language')->dropDownList(common\enums\LanguageEnum::getMap(),['prompt'=>'请选择']);?>              
                </div>
               <div class="col-lg-6">
                <?= $form->field($model, 'currency')->dropDownList(common\enums\CurrencyEnum::getMap(),['prompt'=>'请选择']);?>             
               </div>
            </div>
            <div class="row">
            	<div class="col-lg-6"><?= $form->field($model, 'out_trade_no')->textArea(['options'=>['maxlength' => true]])?></div>
                <div class="col-lg-6"><?= $form->field($model, 'remark')->textArea(['options'=>['maxlength' => true]])?></div>
            </div>
        </div>    
                   
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
        <button class="btn btn-primary" type="submit">保存</button>
    </div>
<?php ActiveForm::end(); ?>
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
            	<div class="col-lg-6"><?= $form->field($model, 'order_sn')->textInput(['readonly'=>true])?></div>
            	<div class="col-lg-6"><?= $form->field($model, 'out_trade_no')->textInput(['readonly'=>true])?></div>                
            </div>
            <div class="row">
                <div class="col-lg-6">
                <?= $form->field($model, 'sale_channel_id')->widget(\kartik\select2\Select2::class, [
                    'data' => Yii::$app->salesService->saleChannel->getDropDown(),
                    'options' => ['placeholder' => '请选择'],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'disabled'=>'disabled'
                    ],
                ]);?>              
                </div>
                <div class="col-lg-6">
                <?= $form->field($model, 'pay_type')->widget(\kartik\select2\Select2::class, [
                    'data' => Yii::$app->salesService->payment->getDropDown(),
                    'options' => ['placeholder' => '请选择'],
                    'pluginOptions' => [
                        'allowClear' => true,                        
                    ],
                ]);?>              
                </div>
            </div>
           <div class="row">
               <div class="col-lg-6">
               <?= $form->field($model, 'currency')->dropDownList(common\enums\CurrencyEnum::getMap(),['prompt'=>'请选择','disabled'=>'disabled']);?>
               </div>
               <div class="col-lg-6"><?= $form->field($model->account, 'pay_amount')->textInput(['readonly'=>true])?></div>
           </div>

            <div class="row">
                <div class="col-lg-6"><?= $form->field($model, 'out_pay_no')->textInput()?></div>    
                <div class="col-lg-6"><?= $form->field($model, 'paid_amount')->textInput()?></div>
            </div>
           <div class="row">
               <div class="col-lg-6">
                   <?= $form->field($model, 'arrive_type')->dropDownList(\addons\Finance\common\enums\ArriveTypeEnum::getMap(),['prompt'=>'请选择']);?>
               </div>
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
                           'startDate' => date("yyyy-MM-dd H:i:s"),
                           'todayBtn' => true,//今日按钮显示
                       ]
                   ]);?>
               </div>
           </div>
           <div class="row">
               <div class="col-lg-12"><?= $form->field($model, 'remark')->textarea()?></div>
           </div>
        </div>    
                   
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
        <button class="btn btn-primary" type="submit">确认支付</button>
    </div>
<?php ActiveForm::end(); ?>
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
            <?= $form->field($model, 'customer_id')->hiddenInput()->label(false)?>
            <div class="row">
                <div class="col-lg-6">
                <?= $form->field($model, 'sale_channel_id')->widget(\kartik\select2\Select2::class, [
                    'data' => Yii::$app->salesService->saleChannel->getDropDown(),
                    'options' => ['placeholder' => '请选择'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]);?>              
                </div>
                <div class="col-lg-6"><?= $form->field($model, 'customer_name')->textInput(['readonly'=>$model->isNewRecord ?true:false])?></div>
            </div>
			<div class="row">
            	<div class="col-lg-6"><?= $form->field($model, 'customer_mobile')->textInput()?></div>
            	<div class="col-lg-6"><?= $form->field($model, 'customer_email')->textInput()?></div>                
			</div>
            <div class="row">
            	<div class="col-lg-6">
                	<?= $form->field($model, 'customer_source')->dropDownList(Yii::$app->salesService->sources->getDropDown(),['prompt'=>'请选择']);?>
                </div>
                <div class="col-lg-6"><?= $form->field($model, 'customer_level')->dropDownList(\addons\Sales\common\enums\CustomerLevelEnum::getMap(),['prompt'=>'请选择']);?></div>
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
            	<div class="col-lg-6">
                	<?= $form->field($model, 'pay_type')->widget(\kartik\select2\Select2::class, [
                        'data' => Yii::$app->salesService->payment->getDropDown(),
                        'options' => ['placeholder' => '请选择'],
                        'pluginOptions' => [
                            'allowClear' => true,                        
                        ],
                    ]);?> 
                </div>
                <div class="col-lg-6"><?= $form->field($model, 'out_pay_no')->textInput()?></div>
            </div>
            <div class="row">
            	<div class="col-lg-6"><?= $form->field($model, 'customer_account')->textInput()?></div>
                <div class="col-lg-6"><?= $form->field($model, 'store_account')->textInput()?></div>
            </div>
            <div class="row">
            	<div class="col-lg-6"><?= $form->field($model, 'pay_remark')->textArea(['options'=>['maxlength' => true]])?></div>
                <div class="col-lg-6"><?= $form->field($model, 'remark')->textArea(['options'=>['maxlength' => true]])?></div>
            </div>
        </div>    
                   
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
        <button class="btn btn-primary" type="submit">保存</button>
    </div>
<?php ActiveForm::end(); ?>
<script>
var formId = 'orderform';
function fillCustomerFormByMobile(){
	var sale_channel_id = $("#"+formId+"-sale_channel_id").val();	
	var customer_mobile = $("#"+formId+"-customer_mobile").val();
    var customer_name  = $("#"+formId+"-customer_name").val();
    var customer_email = $("#"+formId+"-customer_email").val();
    
    if(customer_mobile != '' && sale_channel_id ) {        
    	$.ajax({
            type: "get",
            url: '<?php echo Url::to(['ajax-get-customer'])?>',
            dataType: "json",
            data: {
                'mobile': customer_mobile,
                'channel_id':sale_channel_id
            },
            success: function (data) {
                if (parseInt(data.code) == 200 && data.data) {                       
             	   $("#"+formId+"-customer_name").val(data.data.realname).attr("readonly",false);
             	   $("#"+formId+"-customer_email").val(data.data.email).attr("readonly",false);
              	   $("#"+formId+"-customer_level").val(data.data.level).attr("readonly",false);
                   $("#"+formId+"-customer_source").val(data.data.source_id).attr("readonly",false);
                }
            }
        });
   
    }
}
function fillCustomerFormByEmail(){
	var sale_channel_id = $("#"+formId+"-sale_channel_id").val();	
	var customer_mobile = $("#"+formId+"-customer_mobile").val();
    var customer_name  = $("#"+formId+"-customer_name").val();
    var customer_email = $("#"+formId+"-customer_email").val(); alert('customer_email:'+customer_email);
    if(customer_email !=''  && sale_channel_id ) {
    	$.ajax({
            type: "get",
            url: '<?php echo Url::to(['ajax-get-customer'])?>',
            dataType: "json",
            data: {
                'email': customer_email,
                'channel_id':sale_channel_id
            },
            success: function (data) {
                if (parseInt(data.code) == 200 && data.data) {                       
             	   $("#"+formId+"-customer_name").val(data.data.realname).attr("readonly",false);
             	   $("#"+formId+"-customer_mobile").val(data.data.mobile).attr("readonly",false);
              	   $("#"+formId+"-customer_level").val(data.data.level).attr("readonly",false);
                   $("#"+formId+"-customer_source").val(data.data.source_id).attr("readonly",false);
                }
            }
        });
   
    }
}
$("#"+formId+"-customer_mobile").blur(function(){
	if($("#"+formId+"-sale_channel_id").val() != 3){
		fillCustomerFormByMobile();
	}
});
$("#"+formId+"-customer_email").blur(function(){alert($("#"+formId+"-sale_channel_id").val());
	if($("#"+formId+"-sale_channel_id").val() == 3){
		fillCustomerFormByEmail();
	}
});
$("#"+formId+"-sale_channel_id").change(function(){
	if($(this).val()==3) {
        fillCustomerFormByEmail();
	}else{
		fillCustomerFormByMobile();
	}
});
</script>
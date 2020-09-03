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
                    'options' => ['placeholder' => '请选择',],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]);?>              
                </div>
                <div class="col-lg-6"><?= $form->field($model, 'customer_name')->textInput(['readonly'=>$model->isNewRecord ?true:false])?></div>
            </div>
            <div class="row" id="customer_info_mobile" style="<?= $model->sale_channel_id!=3 ?'':'display:none' ?>">
            	<div class="col-lg-6"><?= $form->field($model, 'customer_mobile_1')->textInput()->label("客户手机<font color='red'>[必填]</font>")?></div>
            	<div class="col-lg-6"><?= $form->field($model, 'customer_email_1')->textInput(['readonly'=>$model->isNewRecord ?true:false])?></div>                
            </div>
            <div class="row" id="customer_info_email" style="<?= $model->sale_channel_id==3?'':'display:none' ?>">
            	<div class="col-lg-6"><?= $form->field($model, 'customer_email_2')->textInput()->label("客户邮箱<font color='red'>[必填]</font>")?></div>
            	<div class="col-lg-6"><?= $form->field($model, 'customer_mobile_2')->textInput(['readonly'=>$model->isNewRecord ?true:false])?></div>                
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
            	<div class="col-lg-6"><?= $form->field($model, 'out_trade_no')->textArea(['options'=>['maxlength' => true]])?></div>
            </div>
            <div class="row">
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
var org_customer_mobile = '<?php echo $model->customer_mobile?>';
function fillCustomerFormByMobile(){
	var sale_channel_id = $("#"+formId+"-sale_channel_id").val();	
	var customer_mobile = $("#"+formId+"-customer_mobile_1").val();
    var customer_name  = $("#"+formId+"-customer_name").val();
    var customer_email = $("#"+formId+"-customer_email_1").val();    
    if(customer_mobile != '' && org_customer_mobile != customer_mobile && sale_channel_id ) {        
        //if((customer_name=='' || customer_email == '')) {
        	$.ajax({
                type: "get",
                url: '<?php echo Url::to(['ajax-get-customer'])?>',
                dataType: "json",
                data: {
                    'mobile': $.trim(customer_mobile),
                    'channel_id':sale_channel_id
                },
                success: function (data) {
                    if (parseInt(data.code) == 200 && data.data) { 
                    	if($.isEmptyObject(data.data) == false) {
                     	   $("#"+formId+"-customer_name").val(data.data.realname);
                     	   
                      	   $("#"+formId+"-customer_level").val(data.data.level);
                           $("#"+formId+"-customer_source").val(data.data.source_id);
                           $("#"+formId+"-customer_mobile_1").val(data.data.mobile);
                           $("#"+formId+"-customer_mobile_2").val(data.data.mobile);
                           $("#"+formId+"-customer_email_1").val(data.data.email);
                           $("#"+formId+"-customer_email_2").val(data.data.email);
                     	   rfMsg("该手机号为老用户，系统已自动填充客户信息");
                    	} else {
                    	   rfMsg("该手机号为新用户，请手动完善客户信息");
                    	}
                    	$("#"+formId+"-customer_name").attr("readonly",false);
                    	$("#"+formId+"-customer_mobile_1").attr("readonly",false);
                        $("#"+formId+"-customer_mobile_2").attr("readonly",false);
                   	    $("#"+formId+"-customer_email_1").attr("readonly",false);
                      	$("#"+formId+"-customer_email_2").attr("readonly",false);
                    	$("#"+formId+"-customer_level").attr("readonly",false);
                        $("#"+formId+"-customer_source").attr("readonly",false);
                    }
                }
            });
        //}	   
    }
    org_customer_mobile =  customer_mobile;  
}
function fillCustomerFormByEmail(){
	var sale_channel_id = $("#"+formId+"-sale_channel_id").val();	
    var customer_name  = $("#"+formId+"-customer_name").val();
    var customer_mobile = $("#"+formId+"-customer_mobile_2").val();
    var customer_email  = $("#"+formId+"-customer_email_2").val();
    if(customer_email !=''  && sale_channel_id ) {        
        //if((customer_name=='' || customer_mobile == '')) {
        	$.ajax({
                type: "get",
                url: '<?php echo Url::to(['ajax-get-customer'])?>',
                dataType: "json",
                data: {
                    'email': $.trim(customer_email),
                    'channel_id':sale_channel_id
                },
                success: function (data) {
                    if (parseInt(data.code) == 200 && data.data) {    
                       if($.isEmptyObject(data.data) == false) { 
                    	   $("#"+formId+"-customer_email_1").val(data.data.email).attr("readonly",false);
                           $("#"+formId+"-customer_email_2").val(data.data.email).attr("readonly",false);
                    	   $("#"+formId+"-customer_mobile_2").val(data.data.mobile).attr("readonly",false); 
                    	   $("#"+formId+"-customer_mobile_1").val(data.data.mobile).attr("readonly",false);                 
                     	   $("#"+formId+"-customer_name").val(data.data.realname).attr("readonly",false);                  	  
                      	   $("#"+formId+"-customer_level").val(data.data.level).attr("readonly",false);
                           $("#"+formId+"-customer_source").val(data.data.source_id).attr("readonly",false);
                       }else{
                    	   rfError("客户邮箱不存在，请先添加客户");
                       }
                    }
                }
            });
        //}	   
   }
}
$("#"+formId+"-customer_mobile_1").blur(function(){
	if($("#"+formId+"-sale_channel_id").val() != 3){
		fillCustomerFormByMobile();
	}
	$("#"+formId+"-customer_mobile_2").val($(this).val());
});
$("#"+formId+"-customer_email_2").blur(function(){
	if($("#"+formId+"-sale_channel_id").val() ==3){
		fillCustomerFormByEmail();
	}
	$("#"+formId+"-customer_mobile_1").val($(this).val());
});

$("#"+formId+"-customer_mobile_2").blur(function(){
	$("#"+formId+"-customer_mobile_1").val($(this).val());
});
$("#"+formId+"-customer_email_1").blur(function(){
	$("#"+formId+"-customer_email_2").val($(this).val());
});
$("#"+formId+"-sale_channel_id").change(function(){
	if($(this).val()==3) {
        $("#customer_info_email").show();
        $("#customer_info_mobile").hide();
        fillCustomerFormByEmail();
	}else{
		$("#customer_info_mobile").show();
		$("#customer_info_email").hide();
		fillCustomerFormByMobile();
	}
});

$("#" + formId + "-sale_channel_id").change(function () {
    var channel_id = $(this).val();
    if (channel_id == 5 || channel_id == 6){
        $("#" + formId + "-language").val("<?= \common\enums\LanguageEnum::ZH_CN ?>");
        $("#" + formId + "-currency").val("<?= \common\enums\CurrencyEnum::CNY ?>");

    }
})
</script>
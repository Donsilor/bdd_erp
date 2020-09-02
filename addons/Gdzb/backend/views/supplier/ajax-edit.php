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
           <div class="row">
               <div class="col-sm-6">
                   <?= $form->field($model, 'supplier_code')->textInput(['disabled'=>true, "placeholder"=>"系统自动生成"])?>
               </div>
               <div class="col-sm-6">
                   <?= $form->field($model, 'contactor')->textInput()?>
               </div>
           </div>
           <div class="row">
               <div class="col-sm-6">
                   <?= $form->field($model, 'mobile')->textInput()?>
               </div>
               <div class="col-sm-6">
                   <?= $form->field($model, 'wechat')->textInput()?>
               </div>
           </div>
           <div class="row">
               <div class="col-sm-4">
                   <?= $form->field($model, 'grade')->dropDownList(\addons\Gdzb\common\enums\GradeEnum::getMap(),['prompt'=>'请选择']) ?>
               </div>
               <div class="col-sm-4">
                   <?= $form->field($model, 'type')->dropDownList(\addons\Gdzb\common\enums\TypeEnum::getMap(),['prompt'=>'请选择']) ?>
               </div>
               <div class="col-sm-4">
                   <?= $form->field($model, 'source_id')->dropDownList(\addons\Gdzb\common\enums\SourceEnum::getMap(),['prompt'=>'请选择']) ?>
               </div>
           </div>
            <div class="row">
            	<div class="col-sm-6">
                    <?= $form->field($model, 'channel_id')->widget(\kartik\select2\Select2::class, [
                        'data' => Yii::$app->salesService->saleChannel->getDropDown(),
                        'options' => ['placeholder' => '请选择',],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ]);?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'follower_id')->widget(kartik\select2\Select2::class, [
                        'data' => Yii::$app->services->backendMember->getDropDown(),
                        'options' => ['placeholder' => '请选择'],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ]);?>
                </div>
            </div>
           <div class="row">
               <div class="col-sm-4">
                   <?= $form->field($model, 'bank_name')->textInput()?>
               </div>
               <div class="col-sm-4">
                   <?= $form->field($model, 'bank_account')->textInput()?>
               </div>
               <div class="col-sm-4">
                   <?= $form->field($model, 'bank_account_name')->textInput()?>
               </div>
           </div>
           <div class="row">
               <div class="col-lg-12">
                   <?php $model->business_scope = !empty($model->business_scope)?array_filter(explode(',', $model->business_scope)):null;?>
                   <?= $form->field($model, 'business_scope')->checkboxList(\addons\Supply\common\enums\BusinessScopeEnum::getMap()) ?>

               </div>
           </div>
           <div class="row">
               <div class="col-sm-6">
                   <?= $form->field($model, 'supplier_name')->textInput()?>
               </div>
               <div class="col-sm-6">
                   <?= $form->field($model, 'address')->textInput(['maxlength' => true]) ?>
               </div>
           </div>

           <div class="row">
               <div class="col-lg-12">
                   <?= $form->field($model, 'remark')->textArea(['options'=>['maxlength' => true]])?>
               </div>
           </div>


        </div>    
                   
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
        <button class="btn btn-primary" type="submit">保存</button>
    </div>
<?php ActiveForm::end(); ?>
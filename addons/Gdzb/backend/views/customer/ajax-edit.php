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
               <div class="col-lg-12">
                   <?= $form->field($model, 'channel_id')->widget(\kartik\select2\Select2::class, [
                       'data' => \Yii::$app->salesService->saleChannel->getDropDown(),
                       'options' => ['placeholder' => '请选择'],
                       'pluginOptions' => [
                           'allowClear' => true
                       ],
                   ]);?>
               </div>
           </div>
           <div class="row">
               <div class="col-lg-12">
                   <?= $form->field($model, 'source_id')->widget(\kartik\select2\Select2::class, [
                       'data' => \Yii::$app->salesService->sources->getDropDown(),
                       'options' => ['placeholder' => '请选择'],
                       'pluginOptions' => [
                           'allowClear' => true
                       ],
                   ]);?>
               </div>
           </div>
           <div class="row">
               <div class="col-lg-6">
                   <?= $form->field($model, 'level')->widget(\kartik\select2\Select2::class, [
                       'data' => \addons\Sales\common\enums\LevelEnum::getMap(),
                       'options' => ['placeholder' => '请选择'],
                       'pluginOptions' => [
                           'allowClear' => true
                       ],
                   ]);?>
               </div>
               <div class="col-lg-6"><?= $form->field($model, 'realname')->textInput()?></div>
           </div>
           <div class="row">
                <div class="col-lg-6"><?= $form->field($model, 'mobile')->textInput()->label("手机号码[<sapn style=\"color:red;\">非国际批发必填</sapn>]")->hint("当客户归属渠道为[非国际批发]时必填")?></div>
                <div class="col-lg-6"><?= $form->field($model, 'email')->textInput()->label("邮箱[<sapn style=\"color:red;\">国际批发必填</sapn>]")->hint("当客户归属渠道为[国际批发]时必填")?></div>
           </div>
           <div class="row">
               <div class="col-lg-12"><?= $form->field($model, 'remark')->textarea()?></div>
           </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
        <button class="btn btn-primary" type="submit">保存</button>
    </div>
<?php ActiveForm::end(); ?>
<?php

use addons\Style\common\enums\AttrIdEnum;
use common\widgets\webuploader\Files;
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
               <div class="col-lg-4">
                   <?= $form->field($model, 'goods_name')->textInput() ?>
               </div>
               <div class="col-lg-4">
                   <?= $form->field($model, 'layout_type')->dropDownList(\addons\Warehouse\common\enums\LayoutTypeEnum::getMap(),['prompt'=>'请选择']) ?>
               </div>
               <div class="col-lg-4">
                   <?= $form->field($model, 'style_sn')->textInput() ?>
               </div>
           </div>
           <div class="row">
               <div class="col-lg-4">
                   <?= $form->field($model, 'qiban_sn')->textInput() ?>
               </div>
               <div class="col-lg-4">
                   <?= $form->field($model, 'finger_hk')->dropDownList(Yii::$app->attr->valueMap(AttrIdEnum::PORT_NO),['prompt'=>'请选择']) ?>
               </div>
               <div class="col-lg-4">
                   <?= $form->field($model, 'finger')->dropDownList(Yii::$app->attr->valueMap(AttrIdEnum::FINGER),['prompt'=>'请选择']) ?>
               </div>
           </div>
           <div class="row">
               <div class="col-lg-4">
                   <?= $form->field($model, 'goods_num')->textInput() ?>
               </div>
               <div class="col-lg-4">
                   <?= $form->field($model, 'suttle_weight')->textInput() ?>
               </div>
               <div class="col-lg-4">
                   <?= $form->field($model, 'goods_weight')->textInput() ?>
               </div>
           </div>
           <div class="row">
               <div class="col-lg-4">
                   <?= $form->field($model, 'goods_size')->textInput() ?>
               </div>
               <div class="col-lg-4">
                   <?= $form->field($model, 'stone_weight')->textInput() ?>
               </div>
               <div class="col-lg-4">
                   <?= $form->field($model, 'stone_size')->textInput() ?>
               </div>
           </div>
           <div class="row">
               <div class="col-lg-4">
                   <?= $form->field($model, 'cost_price')->textInput() ?>
               </div>
               <div class="col-lg-4">
                   <?= $form->field($model, 'goods_image')->widget(common\widgets\webuploader\Files::class, [
                       'config' => [
                           'pick' => [
                               'multiple' => false,
                           ],
                       ]
                   ]); ?>
               </div>
               <div class="col-lg-4">
                   <?= $form->field($model, 'remark')->textarea() ?>
               </div>
           </div>
       </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
        <button class="btn btn-primary" type="submit">保存</button>
    </div>
<?php ActiveForm::end(); ?>
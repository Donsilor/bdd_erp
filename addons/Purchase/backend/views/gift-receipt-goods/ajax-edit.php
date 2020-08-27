<?php

use addons\Style\common\enums\AttrIdEnum;
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
                   <?= $form->field($model, 'goods_sn')->textInput(['disabled'=>'disabled']) ?>
               </div>
               <div class="col-lg-4">
                   <?= $form->field($model, 'goods_name')->textInput() ?>
               </div>
<!--               <div class="col-lg-4">-->
<!--                   --><?//= $form->field($model, 'product_type_id')->dropDownList(Yii::$app->styleService->productType::getDropDown(),['disabled'=>true]) ?>
<!--               </div>-->
               <div class="col-lg-4">
                   <?= $form->field($model, 'style_cate_id')->dropDownList(Yii::$app->styleService->productType::getDropDown(),['disabled'=>true]) ?>
               </div>
           </div>
           <div class="row">
               <div class="col-lg-4">
                   <?= $form->field($model, 'style_sex')->dropDownList(\addons\Style\common\enums\StyleSexEnum::getMap(),['disabled'=>true]) ?>
               </div>
               <div class="col-lg-4">
                   <?= $form->field($model, 'material_type')->dropDownList($model->getMaterialTypeMap(),['prompt'=>'请选择']) ?>
               </div>
               <div class="col-lg-4">
                   <?= $form->field($model, 'material_color')->dropDownList($model->getMaterialColorMap(),['prompt'=>'请选择']) ?>
               </div>
           </div>
           <div class="row">
               <div class="col-lg-4">
                   <?= $form->field($model, 'finger_hk')->dropDownList(Yii::$app->attr->valueMap(\addons\Style\common\enums\AttrIdEnum::PORT_NO),['prompt'=>'请选择']) ?>
               </div>
               <div class="col-lg-4">
                   <?= $form->field($model, 'finger')->dropDownList(Yii::$app->attr->valueMap(AttrIdEnum::FINGER),['prompt'=>'请选择']) ?>
               </div>
               <div class="col-lg-4">
                   <?= $form->field($model, 'main_stone_type')->dropDownList($model->getMainStoneTypeMap(),['prompt'=>'请选择']) ?>
               </div>
           </div>
           <div class="row">
               <div class="col-lg-4">
                   <?= $form->field($model, 'main_stone_num')->textInput() ?>
               </div>
               <div class="col-lg-4">
                   <?= $form->field($model, 'chain_length')->textInput() ?>
               </div>
               <div class="col-lg-4">
                   <?= $form->field($model, 'goods_size')->textInput() ?>
               </div>
           </div>
           <div class="row">
               <div class="col-lg-4">
                   <?= $form->field($model, 'goods_num')->textInput() ?>
               </div>
               <div class="col-lg-4">
                   <?= $form->field($model, 'goods_weight')->textInput() ?>
               </div>
               <div class="col-lg-4">
                   <?= $form->field($model, 'gold_price')->textInput() ?>
               </div>
           </div>
           <div class="row">
               <div class="col-lg-4">
<!--                   ['disabled'=>'disabled']-->
                   <?= $form->field($model, 'cost_price')->textInput() ?>
               </div>
               <div class="col-lg-8">
                   <?= $form->field($model, 'goods_remark')->textarea() ?>
               </div>
           </div>
       </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
        <button class="btn btn-primary" type="submit">保存</button>
    </div>
<?php ActiveForm::end(); ?>
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
               <div class="col-lg-4">
                   <?= $form->field($model, 'goods_name')->textInput() ?>
               </div>
               <div class="col-lg-4">
                   <?= $form->field($model, 'material_type')->dropDownList($model->getStoneTypeMap(),['prompt'=>'请选择', 'disabled'=>true]) ?>
               </div>
               <div class="col-lg-4">
                   <?= $form->field($model, 'goods_sn')->textInput(['disabled'=>true])?>
               </div>
           </div>
           <div class="row">
               <div class="col-lg-4">
                   <?= $form->field($model, 'stone_num')->textInput() ?>
               </div>
               <div class="col-lg-4">
                   <?= $form->field($model, 'goods_weight')->textInput() ?>
               </div>
               <div class="col-lg-4">
                   <?= $form->field($model, 'stone_price')->textInput() ?>
               </div>
           </div>
           <div class="row">
               <div class="col-lg-4">
                   <?= $form->field($model, 'cost_price')->textInput(['disabled'=>true]) ?>
               </div>
               <div class="col-lg-4">
                   <?= $form->field($model, 'goods_color')->dropDownList($model->getColorMap(),['prompt'=>'请选择']) ?>
               </div>
               <div class="col-lg-4">
                   <?= $form->field($model, 'goods_clarity')->dropDownList($model->getClarityMap(),['prompt'=>'请选择']) ?>
               </div>
           </div>
           <div class="row">
               <div class="col-lg-4">
                   <?= $form->field($model, 'goods_cut')->dropDownList($model->getCutMap(),['prompt'=>'请选择']) ?>
               </div>
               <div class="col-lg-4">
                   <?= $form->field($model, 'goods_symmetry')->dropDownList($model->getSymmetryMap(),['prompt'=>'请选择']) ?>
               </div>
               <div class="col-lg-4">
                   <?= $form->field($model, 'goods_polish')->dropDownList($model->getPolishMap(),['prompt'=>'请选择']) ?>
               </div>
           </div>
           <div class="row">
               <div class="col-lg-4">
                   <?= $form->field($model, 'goods_fluorescence')->dropDownList($model->getFluorescenceMap(),['prompt'=>'请选择']) ?>
               </div>
               <div class="col-lg-4">
                   <?= $form->field($model, 'goods_shape')->dropDownList($model->getShapeMap(),['prompt'=>'请选择']) ?>
               </div>
               <div class="col-lg-4">
                   <?= $form->field($model, 'goods_colour')->dropDownList($model->getColourMap(),['prompt'=>'请选择']) ?>
               </div>
           </div>
           <div class="row">
               <div class="col-lg-4">
                   <?= $form->field($model, 'cert_type')->dropDownList($model->getCertTypeMap(),['prompt'=>'请选择']) ?>
               </div>
               <div class="col-lg-4">
                   <?= $form->field($model, 'cert_id')->textInput() ?>
               </div>
               <div class="col-lg-4">
                   <?= $form->field($model, 'goods_size')->textInput() ?>
               </div>
           </div>
           <div class="row">
               <div class="col-lg-4">
                   <?= $form->field($model, 'goods_norms')->textarea() ?>
               </div>
               <div class="col-lg-4">
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
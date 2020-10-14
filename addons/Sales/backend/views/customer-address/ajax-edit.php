<?php

use yii\widgets\ActiveForm;
use common\helpers\Url;
use yii\base\Widget;

$form = ActiveForm::begin([
        'id' => $model->formName(),
        'enableAjaxValidation' => false,
        'validationUrl' => Url::to(['ajax-edit-address', 'id' => $model->id]),
        'fieldConfig' => [
                
        ]
]);
?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
    <h4 class="modal-title">基本信息</h4>
</div>
<div class="modal-body">
        <?= $form->field($model, 'customer_id')->hiddenInput()->label(false)?>
        <div class="row">
            <div class="col-lg-4">
                <?= $form->field($model, 'realname')->textInput(['maxlength' => true])?>
            </div>
            <div class="col-lg-4">
                <?= $form->field($model, 'mobile')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-lg-4">
                <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
            </div>
        </div>      
        
        <?= \common\widgets\country\Country::widget([
                            'form' => $form,
                            'model' => $model,
                            'countryName' => 'country_id',
                            'provinceName' => 'province_id',// 省字段名
                            'cityName' => 'city_id',// 市字段名
                            //'areaName' => 'area_id',// 区字段名
                            'template' => 'short' //合并为一行显示
                        ]); ?>
        <?= $form->field($model, 'address_details')->textInput(['maxlength' => true]) ?>
        <div class="row">
            <div class="col-lg-4">
                <?= $form->field($model, 'zip_code')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-lg-4">
                <?= $form->field($model, 'is_default')->radioList(\common\enums\ConfirmEnum::getMap()) ?>
            </div>            
        </div> 
        
        
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
    <button class="btn btn-primary" type="submit">保存</button>
</div>
<?php ActiveForm::end(); ?>

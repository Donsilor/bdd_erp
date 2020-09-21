<?php

use common\enums\StatusEnum;
use common\widgets\webuploader\Files;
use kartik\date\DatePicker;
use yii\widgets\ActiveForm;
use common\helpers\Url;
use yii\base\Widget;

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
        <div class="row">
            <div class="col-lg-4">
                <?= $form->field($model, 'name')->textInput(['maxlength' => true]); ?>
            </div>
            <div class="col-lg-4">
                <?= $form->field($model, 'type')->dropDownList(addons\Sales\common\enums\DeliveryTypeEnum::getMap(),['prompt'=>'请选择']);?>             
            </div>
            <div class="col-lg-4">
                <?= $form->field($model, 'channel_id')->widget(\kartik\select2\Select2::class, [
                            'data' => Yii::$app->salesService->saleChannel->getDropDown(),
                            'options' => ['placeholder' => '请选择',],
                            'pluginOptions' => [
                                'allowClear' => true
                            ],
                ]);?>  
            </div>
        </div>
        <div class="row">            
            <div class="col-lg-4">
                <?= $form->field($model, 'realname')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-lg-4">
                <?= $form->field($model, 'mobile')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-lg-4">
                <?= $form->field($model, 'zip_code')->textInput(['maxlength' => true]) ?>
            </div>
        </div>
        <div class="row">            
            <div class="col-lg-6">
                <?= \common\widgets\country\Country::widget([
                                'form' => $form,
                                'model' => $model,
                                'countryName' => 'country_id',
                                'provinceName' => 'province_id',// 省字段名
                                'cityName' => 'city_id',// 市字段名
                                //'areaName' => 'area_id',// 区字段名
                                'template' => 'short' //合并为一行显示
                            ]); ?>
            </div>
             <div class="col-lg-6">
                <?= $form->field($model, 'address_details')->textInput(['maxlength' => true]) ?>
            </div>           
        </div>        
        <div class="row">
            <div class="col-sm-4">
            	<?= $form->field($model, 'payment_id')->widget(\kartik\select2\Select2::class, [
                    'data' => Yii::$app->salesService->payment->getDropDown(),
                    'options' => ['placeholder' => '请选择'],
                    'pluginOptions' => [
                        'allowClear' => true,                        
                    ],
                ]);?>               
            </div>
            <div class="col-sm-4">
            <?= $form->field($model, 'language')->dropDownList(common\enums\LanguageEnum::getMap(),['prompt'=>'请选择']);?>             
            </div>
            <div class="col-sm-4">
            <?= $form->field($model, 'currency')->dropDownList(common\enums\CurrencyEnum::getMap(),['prompt'=>'请选择']);?>             
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
        <button class="btn btn-primary" type="submit">保存</button>
    </div>
<?php ActiveForm::end(); ?>

<?php

use common\enums\StatusEnum;
use common\widgets\webuploader\Files;
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
                <?= $form->field($model, 'express_man')->textInput(['maxlength' => true]); ?>
            </div>
            <div class="col-lg-4">
                <?= $form->field($model, 'express_phone')->textInput(['maxlength' => true]); ?>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-4">
                <?= $form->field($model, 'company_man')->textInput(['maxlength' => true]); ?>
            </div>
            <div class="col-lg-4">
                <?= $form->field($model, 'company_phone')->textInput(['maxlength' => true]); ?>
            </div>
            <div class="col-lg-4">
                <?= $form->field($model, 'settlement_way')->dropDownList(\addons\Sales\common\enums\SettlementWayEnum::getMap(),['prompt'=>'请选择']);?>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-4">
                <?= $form->field($model, 'settlement_period')->dropDownList(\addons\Sales\common\enums\SettlementPeriodEnum::getMap(),['prompt'=>'请选择']);?>
            </div>
            <div class="col-lg-4">
                <?= $form->field($model, 'settlement_account')->textInput(['maxlength' => true]); ?>
            </div>
            <div class="col-lg-4">
                <?= $form->field($model, 'delivery_scope')->dropDownList(\addons\Sales\common\enums\DeliveryScopeEnum::getMap(),['prompt'=>'请选择']);?>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-4">
                <?= $form->field($model, 'receive_time')->textInput(['maxlength' => true]); ?>
            </div>
            <div class="col-lg-4">
                <?= $form->field($model, 'status')->radioList(common\enums\StatusEnum::getMap())?>
            </div>
            <div class="col-lg-4">
                <?= $form->field($model, 'sort')->textInput(); ?>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-4">
                <?= $form->field($model, 'cover')->widget(common\widgets\webuploader\Files::class, [
                    'config' => [
                        'pick' => [
                            'multiple' => false,
                        ],
                    ]
                ]); ?>
            </div>
            <div class="col-lg-4">
                <?= $form->field($model, 'pact_file')->widget(common\widgets\webuploader\Files::class, [
                    'type' => 'files',
                    'config' => [
                        'pick' => [
                            'multiple' => false,
                        ],
                        'formData' => [
                            // 'drive' => 'local',// 默认本地 支持 qiniu/oss 上传
                        ],
                    ]
                ]); ?>
            </div>
            <div class="col-lg-4">
                <?= $form->field($model, 'cert_file')->widget(common\widgets\webuploader\Files::class, [
                    'type' => 'files',
                    'config' => [
                        'pick' => [
                            'multiple' => false,
                        ],
                        'formData' => [
                            // 'drive' => 'local',// 默认本地 支持 qiniu/oss 上传
                        ],
                    ]
                ]); ?>
            </div>
        </div>
        <!--<?= $form->field($model, 'code')->textInput(); ?>-->

    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
        <button class="btn btn-primary" type="submit">保存</button>
    </div>
<?php ActiveForm::end(); ?>

<?php

use addons\Sales\common\enums\CheckStatusEnum;
use common\enums\CurrencyEnum;
use common\enums\LanguageEnum;
use yii\widgets\ActiveForm;
use common\helpers\Url;

$form = ActiveForm::begin([
        'id' => $model->formName(),
        'enableAjaxValidation' => true,
        'validationUrl' => Url::to(['ajax-audit','id' => $model['id']]),
        'fieldConfig' => [
                //'template' => "<div class='col-sm-2 text-right'>{label}</div><div class='col-sm-10'>{input}\n{hint}\n{error}</div>",
        ]
]);
?>
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span></button>
        <h4 class="modal-title">基本信息</h4>
    </div>

    <div class="modal-body">
        <div class="tab-content">
            <?= $form->field($model, 'check_status')->hiddenInput()->label(false); ?>
            <div class="row">
                <div class="col-lg-6">
                    <?= $form->field($model, $status)->radioList(\common\enums\AuditStatusEnum::getAuditMap()) ?>
                </div>
                <div class="col-lg-6">
                    <?= $form->field($model, 'should_amount')->textInput(['readonly'=>true])?>
                </div>
            </div>
            <?php if($model->check_status == CheckStatusEnum::STOREKEEPER) {?>
                <div class="row">
                    <div class="col-lg-6">
                        <?= $form->field($model, 'currency')->dropDownList(common\enums\CurrencyEnum::getMap(),['prompt'=>'请选择', 'disabled'=>true]);?>
                    </div>
                    <div class="col-lg-6">
                        <?= $form->field($model, 'real_amount')->textInput(['value'=>$model->should_amount])?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <?= $form->field($model, 'is_finance_refund')->radioList(\common\enums\ConfirmEnum::getMap()) ?>
                    </div>
                    <div class="col-lg-6">
                        <?= $form->field($model, 'is_quick_refund')->radioList(\common\enums\ConfirmEnum::getMap()) ?>
                    </div>
                </div>
            <?php } ?>
            <?php if($model->check_status == CheckStatusEnum::LEADER) {?>
                <div class="row">
                    <div class="col-lg-6">
                        <?= $form->field($model, 'return_by')->radioList(\addons\Sales\common\enums\ReturnByEnum::getMap()) ?>
                    </div>
                    <div class="col-lg-6">
                        <?= $form->field($model, 'to_warehouse_id')->widget(\kartik\select2\Select2::class, [
                            'data' => Yii::$app->warehouseService->warehouse::getDropDown(),
                            'options' => ['placeholder' => '请选择', 'disabled'=>true],
                            'pluginOptions' => [
                                'allowClear' => false
                            ],
                        ]);?>
                    </div>
                </div>
            <?php } ?>
            <?= $form->field($model, $remark)->textArea(); ?>
            <!-- /.tab-pane -->
        </div>
        <!-- /.tab-content -->
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
        <button class="btn btn-primary" type="submit">保存</button>
    </div>
<?php ActiveForm::end(); ?>
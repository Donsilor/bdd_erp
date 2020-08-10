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
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span>
        </button>
        <h4 class="modal-title">基本信息</h4>
    </div>
    <div class="modal-body">
        <div class="col-sm-12">
            <div class="row">
                <div class="col-lg-4">
                    <?= $form->field($model, 'parts_type')->dropDownList($model->getPartsTypeMap(), ['prompt' => '请选择', 'disabled' => true]) ?>
                </div>
                <div class="col-lg-4">
                    <?= $form->field($model, 'goods_sn')->textInput(['disabled' => true]) ?>
                </div>
                <div class="col-lg-4">
                    <?= $form->field($model, 'goods_name')->textInput() ?>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-4">
                    <?= $form->field($model, 'material_type')->dropDownList($model->getMaterialTypeMap(), ['prompt' => '请选择', 'disabled' => true]) ?>
                </div>
                <div class="col-lg-4">
                    <?= $form->field($model, 'goods_num')->textInput() ?>
                </div>
                <div class="col-lg-4">
                    <?= $form->field($model, 'goods_weight')->textInput() ?>
                </div>

            </div>
            <div class="row">
                <div class="col-lg-4">
                    <?= $form->field($model, 'goods_color')->dropDownList($model->getColorMap(), ['prompt' => '请选择', 'disabled' => true]) ?>
                </div>
                <div class="col-lg-4">
                    <?= $form->field($model, 'goods_shape')->dropDownList($model->getShapeMap(), ['prompt' => '请选择', 'disabled' => true]) ?>
                </div>
                <div class="col-lg-4">
                    <?= $form->field($model, 'chain_type')->dropDownList($model->getChainTypeMap(), ['prompt' => '请选择']) ?>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-4">
                    <?= $form->field($model, 'cramp_ring')->dropDownList($model->getCrampRingMap(), ['prompt' => '请选择']) ?>
                </div>
                <div class="col-lg-4">
                    <?= $form->field($model, 'goods_size')->textInput() ?>
                </div>
                <div class="col-lg-4">
                    <?= $form->field($model, 'parts_price')->textInput() ?>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-4">
                    <?= $form->field($model, 'cost_price')->textInput(['disabled' => true]) ?>
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
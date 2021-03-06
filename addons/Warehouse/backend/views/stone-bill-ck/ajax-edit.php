<?php

use addons\Warehouse\common\enums\BillTypeEnum;
use kartik\date\DatePicker;
use kartik\select2\Select2;
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
        <?= $form->field($model, 'bill_no')->textInput(['disabled'=>true, "placeholder"=>"系统自动生成"])?>
        <div class="row">
            <div class="col-sm-6"><?= $form->field($model, 'out_type')->dropDownList(\addons\Warehouse\common\enums\OutTypeEnum::getMap(),['prompt'=>'请选择']) ?></div>
            <div class="col-sm-6">
                <?= $form->field($model, 'supplier_id')->widget(\kartik\select2\Select2::class, [
                    'data' => \Yii::$app->supplyService->supplier->getDropDown(['like','goods_type',\addons\Supply\common\enums\GoodsTypeEnum::RAW_MATERIAL]),
                    'options' => ['placeholder' => '请选择'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]);?>
            </div>
<!--            <div class="col-sm-6">-->
<!--                --><?//= $form->field($model, 'receiv_id')->widget(kartik\select2\Select2::class, [
//                    'data' => Yii::$app->services->backendMember->getDropDown(),
//                    'options' => [
//                        'placeholder' => '请选择',
//                        'value' => $model->receiv_id??'',
//                    ],
//                    'pluginOptions' => [
//                        'allowClear' => true
//                    ],
//                ]);?>
<!--            </div>-->
        </div>
        <?= $form->field($model, 'purchase_sn')->textInput() ?>
        <?= $form->field($model, 'remark')->textArea(); ?>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
    <button class="btn btn-primary" type="submit">保存</button>
</div>
<?php ActiveForm::end(); ?>
<?php

use yii\widgets\ActiveForm;

$form = ActiveForm::begin([]);
?>
<div class="modal-body">
    <div class="tab-content">
        <?= $form->field($model, 'put_in_type')->widget(\kartik\select2\Select2::class, [
            'data' => \addons\Warehouse\common\enums\PutInTypeEnum::getMap(),
            'options' => ['placeholder' => '请选择'],
            'pluginOptions' => [
                'allowClear' => false
            ],
        ]);?>
        <?= $form->field($model, 'to_warehouse_id')->widget(\kartik\select2\Select2::class, [
            'data' => Yii::$app->warehouseService->warehouse::getDropDown(),
            'options' => ['placeholder' => '请选择'],
            'pluginOptions' => [
                'allowClear' => false
            ],
        ]);?>
        <!-- /.tab-pane -->
    </div>
    <!-- /.tab-content -->
</div>
<?php ActiveForm::end(); ?>
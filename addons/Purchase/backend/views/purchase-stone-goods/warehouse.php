<?php

use yii\widgets\ActiveForm;
use common\helpers\Url;

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
        ])->label("采购方式");?>
        <!-- /.tab-pane -->
    </div>
    <!-- /.tab-content -->
</div>
<?php ActiveForm::end(); ?>
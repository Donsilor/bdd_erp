<?php

use yii\widgets\ActiveForm;
$form = ActiveForm::begin([]);
?>
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="modal-body">
                <div class="tab-content">
                    <?= $form->field($model, 'fin_status')->radioList(\addons\Warehouse\common\enums\FinAuditStatusEnum::getAuditMap()); ?>
                    <?= $form->field($model, 'fin_adjust_status')->dropDownList(\addons\Warehouse\common\enums\FinAdjustStatusEnum::getMap())?>
                    <?= $form->field($model, 'adjust_reason')->dropDownList(\addons\Warehouse\common\enums\AdjustReasonEnum::getMap())?>
                    <?= $form->field($model, 'fin_remark')->textArea(); ?>
                    <!-- /.tab-pane -->
                </div>
                <!-- /.tab-content -->
            </div>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>
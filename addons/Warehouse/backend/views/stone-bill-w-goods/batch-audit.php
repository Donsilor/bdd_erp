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
                    <?= $form->field($model, 'adjust_status')->dropDownList(\addons\Warehouse\common\enums\PandianAdjustEnum::getMap())?>
                    <?= $form->field($model, 'fin_remark')->textArea(); ?>
                    <!-- /.tab-pane -->
                </div>
                <!-- /.tab-content -->
            </div>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>
<?php

use yii\widgets\ActiveForm;
use common\helpers\Url;

?>
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <?php $form = ActiveForm::begin([]); ?>
            <div class="modal-body">
                <div class="tab-content">
                    <?= $form->field($model, 'status')->radioList(\addons\Warehouse\common\enums\LendStatusEnum::getMap()); ?>
                    <?= $form->field($model, 'goods_remark')->textArea(); ?>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
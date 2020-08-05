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
                    <?= $form->field($model, 'remark')->textArea(['options'=>['maxlength' => true]])?>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
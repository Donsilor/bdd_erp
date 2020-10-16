<?php

use kartik\select2\Select2;
use yii\widgets\ActiveForm;

?>
<?php $form = ActiveForm::begin(['action' => ['batch-edit']]); ?>
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-body">
                <div class="row">
                    <div class="col-sm-2 text-right">
                        <label class="control-label">
                            <?= $model->getAttributeLabel($model->batch_name) ?>
                        </label>
                    </div>
                    <div class="col-sm-8">
                        <?= $form->field($model, 'attr_list')->widget(kartik\select2\Select2::class, [
                            'data' => $model->attr_list,
                            'options' => ['placeholder' => '请选择'],
                            'pluginOptions' => [
                                'allowClear' => true
                            ],
                        ])->label(false); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>
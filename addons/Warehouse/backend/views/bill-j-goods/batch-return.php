<?php

use kartik\date\DatePicker;
use yii\widgets\ActiveForm;
use common\helpers\Url;

?>
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <?php $form = ActiveForm::begin([]); ?>
            <div class="modal-body">
                <div class="tab-content">
                    <?= $form->field($model, 'qc_status')->radioList(\addons\Warehouse\common\enums\QcStatusEnum::getMap()); ?>
                    <?= $form->field($model, 'restore_time')->widget(DatePicker::class, [
                        'language' => 'zh-CN',
                        'options' => [
                            'value' => date('Y-m-d', time()),
                        ],
                        'pluginOptions' => [
                            'format' => 'yyyy-mm-dd',
                            'todayHighlight' => true,//今日高亮
                            'autoclose' => true,//选择后自动关闭
                            'todayBtn' => true,//今日按钮显示
                        ]
                    ]);?>
                    <?= $form->field($model, 'qc_remark')->textArea(); ?>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
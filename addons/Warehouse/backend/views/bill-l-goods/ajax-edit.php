<?php

use yii\widgets\ActiveForm;
use common\helpers\Url;
$form = ActiveForm::begin([
    'id' => $model->formName(),
    'enableAjaxValidation' => true,
    'validationUrl' => Url::to(['ajax-edit', 'id' => $model['id']]),
    'fieldConfig' => [
        //'template' => "<div class='col-sm-3 text-right'>{label}</div><div class='col-sm-9'>{input}\n{hint}\n{error}</div>",
    ]
]);
?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
    <h4 class="modal-title">基本信息</h4>
</div>
<div class="modal-body">
    <div class="col-sm-12">
        <?= $form->field($model, 'goods_sn')->textInput(["placeholder"=>"请输入款号/起版号"]) ?>
        <?= $form->field($model, 'goods_num')->textInput(["placeholder"=>"请输入数量"]) ?>
        <?= $form->field($model, 'cost_price')->textInput(["placeholder"=>"请输入成本单价"]) ?>
        <?= $form->field($model, 'order_sn')->textInput(["placeholder"=>"请输入订单号"]) ?>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
    <button class="btn btn-primary" type="submit">保存</button>
</div>
<?php ActiveForm::end(); ?>

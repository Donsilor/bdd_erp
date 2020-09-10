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
        <div class="row">
            <div class="col-sm-7">
                <?= $form->field($model, 'is_wholesale')->radioList(addons\Warehouse\common\enums\IsWholeSaleEnum::getMap())->label('是否批发(<span style="color: red;">批发入库时出库销售不可拆分</span>)')?>
            </div>
            <div class="col-sm-5">
                <?= $form->field($model, 'auto_goods_id')->radioList(\common\enums\ConfirmEnum::getMap()) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6">
                <?= $form->field($model, 'goods_num')->textInput(["placeholder"=>"请输入数量"]) ?>
            </div>
            <div class="col-sm-6">
                <?= $form->field($model, 'order_sn')->textInput(["placeholder"=>"请输入订单号"]) ?>
<!--                --><?//= $form->field($model, 'cost_price')->textInput(["placeholder"=>"请输入成本单价"]) ?>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
    <button class="btn btn-primary" type="submit">保存</button>
</div>
<?php ActiveForm::end(); ?>

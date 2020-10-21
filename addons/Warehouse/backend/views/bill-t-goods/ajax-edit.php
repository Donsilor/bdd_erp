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
            <div class="col-sm-6">
                <?= $form->field($model, 'is_wholesale')->radioList(addons\Warehouse\common\enums\IsWholeSaleEnum::getMap())->label('是否同一条码号(<span style="color: red;">(选择否：会产生多个条码号)</span>)')?>
            </div>
<!--            <div class="col-sm-6">-->
<!--                --><?//= $form->field($model, 'auto_goods_id')->radioList(\common\enums\ConfirmEnum::getMap()) ?>
<!--            </div>-->
            <div class="col-sm-6">
                <?= $form->field($model, 'goods_num')->textInput(["placeholder"=>"请输入数量"]) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6">
                <?= $form->field($model, 'to_warehouse_id')->widget(\kartik\select2\Select2::class, [
                    'data' => Yii::$app->warehouseService->warehouse::getDropDown(),
                    'options' => ['placeholder' => '请选择'],
                    'pluginOptions' => [
                        'allowClear' => false
                    ],
                ]); ?>
            </div>
            <div class="col-sm-6">
                <?= $form->field($model, 'cost_amount')->textInput(["placeholder"=>"请输入公司总成本"]) ?>
            </div>
        </div>
<!--        <div class="row">-->
<!--            <div class="col-sm-6">-->
<!--                --><?//= $form->field($model, 'order_sn')->textInput(["placeholder"=>"请输入订单号"]) ?>
<!--            </div>-->
<!--        </div>-->
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
    <button id="save" class="btn btn-primary" type="submit" data-loading-text="保存中...">保存</button>
</div>
<?php ActiveForm::end(); ?>
<script>
    // $(function () {
    //     $("#save").click(function () {
    //         $(this).button('loading').delay(2000).queue(function () {
    //              //$(this).button('reset');
    //              //$(this).dequeue();
    //         });
    //     });
    // });
</script>
<?php

use common\helpers\Html;
use yii\widgets\ActiveForm;
use common\helpers\Url;

$form = ActiveForm::begin([
    'id' => $model->formName(),
    'enableAjaxValidation' => true,
    'validationUrl' => Url::to(['ajax-edit', 'id' => $model['id']]),
    'fieldConfig' => [
        //'template' => "<div class='col-sm-2 text-right'>{label}</div><div class='col-sm-10'>{input}\n{hint}\n{error}</div>",
    ]
]);
?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span>
    </button>
    <h4 class="modal-title">基本信息</h4>
</div>
<div class="modal-body">
    <div class="col-sm-12">
        <?= $form->field($model, 'bill_no')->textInput(['disabled' => true, "placeholder" => "系统自动生成"]) ?>
        <div class="row">
            <div class="col-sm-4">
                <?= $form->field($model, 'supplier_id')->widget(\kartik\select2\Select2::class, [
                    'data' => \Yii::$app->supplyService->supplier->getDropDown(['goods_type'=>\addons\Supply\common\enums\GoodsTypeEnum::RAW_MATERIAL]),
                    'options' => ['placeholder' => '请选择'],
                    'pluginOptions' => [
                        'allowClear' => false
                    ],
                ]); ?>
            </div>
            <div class="col-sm-4"><?= $form->field($model, 'delivery_no')->textInput() ?></div>
            <div class="col-sm-4">
                <?= $form->field($model, 'is_settle_accounts')->radioList(\addons\Warehouse\common\enums\IsSettleAccountsEnum::getMap()) ?>
            </div>

        </div>
<!--        <div class="row">-->
<!--            -->
<!--            --><?php //if ($model->isNewRecord) { ?>
<!--                <div class="col-sm-4">-->
<!--                    --><?//= $form->field($model, 'file')->fileInput() ?>
<!--                    --><?//= Html::a("下载数据导入格式", ['bill-t-goods/ajax-upload', 'download' => 1], ['style' => "text-decoration:underline;color:#3c8dbc"]) ?>
<!--                </div>-->
<!--            --><?php //} ?>
<!--        </div>-->
        <?= $form->field($model, 'remark')->textArea(); ?>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
    <button class="btn btn-primary" type="submit">保存</button>
</div>
<?php ActiveForm::end(); ?>

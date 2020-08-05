<?php

use yii\widgets\ActiveForm;
use common\helpers\Url;

$supplier_id = Yii::$app->request->get('supplier_id');
$disabled = false;
if($supplier_id){
    $model->supplier_id = $supplier_id;
    $disabled = true;
}


$form = ActiveForm::begin([
    'id' => $model->formName(),
    'enableAjaxValidation' => true,
    'validationUrl' => Url::to(['ajax-edit', 'id' => $model['id']]),
    'fieldConfig' => [
        'template' => "<div class='col-sm-2 text-right'>{label}</div><div class='col-sm-10'>{input}\n{hint}\n{error}</div>",
    ]
]);
?>

    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
        <h4 class="modal-title">基本信息</h4>
    </div>
    <div class="modal-body">
        <?= $form->field($model, 'box_sn')->textInput() ?>
        <?= $form->field($model, 'warehouse_id')->widget(kartik\select2\Select2::class, [
            'data' => Yii::$app->warehouseService->warehouse::getDropDown(),
            'options' => ['placeholder' => '请选择','disabled'=>$disabled],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);?>
        <?= $form->field($model, 'status')->radioList(common\enums\StatusEnum::getMap())?>
        <?= $form->field($model, 'remark')->textarea() ?>

    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
        <button class="btn btn-primary" type="submit">保存</button>
    </div>
<?php ActiveForm::end(); ?>
<script type="text/javascript">
    $('#warehouse-name').blur(function(){
        var url = "<?=Url::to('auto-code') ?>";
        var value = $(this).val();
        var data = {'name':value};
        $.post(url,data,function(e){
            $('#warehouse-code').val($.trim(e)).change();
        });
    });
</script>

<?php

use addons\Warehouse\common\enums\BillTypeEnum;
use kartik\date\DatePicker;
use kartik\select2\Select2;
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
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
    <h4 class="modal-title">基本信息</h4>
</div>
<div class="modal-body">
    <div class="col-sm-12">
        <?= $form->field($model, 'bill_no')->textInput(['disabled'=>true, "placeholder"=>"系统自动生成"])?>
        <?= $form->field($model, 'channel_id')->widget(\kartik\select2\Select2::class, [
            'data' => \Yii::$app->styleService->styleChannel->getDropDown(),
            'options' => ['placeholder' => '请选择'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);?>
        <div class="row">
            <div class="col-sm-6">
                <?= $form->field($model, 'lender_id')->widget(kartik\select2\Select2::class, [
                    'data' => Yii::$app->services->backendMember->getDropDown(),
                    'options' => [
                            'placeholder' => '请选择',
                            'value' => $model->billJ->lender_id??'',
                    ],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]);?>
            </div>
            <div class="col-sm-6">
                <?= $form->field($model, 'est_restore_time')->widget(DatePicker::class, [
                    'language' => 'zh-CN',
                    'options' => [
                        'value' => isset($model->billJ->est_restore_time) ? date('Y-m-d', $model->billJ->est_restore_time) :'',
                    ],
                    'pluginOptions' => [
                        'format' => 'yyyy-mm-dd',
                        'todayHighlight' => true,//今日高亮
                        'autoclose' => true,//选择后自动关闭
                        'todayBtn' => true,//今日按钮显示
                    ]
                ]);?>
            </div>
        </div>
        <?= $form->field($model, 'order_sn')->textInput() ?>
        <?= $form->field($model, 'remark')->textArea(); ?>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
    <button class="btn btn-primary" type="submit">保存</button>
</div>
<?php ActiveForm::end(); ?>
<script>
    $(document).ready(function(){
        var id = $("#warehousebillcform-delivery_type").find(':checked').val();
        load(id);
        $("#warehousebillcform-delivery_type").change(function(){
            var id = $(this).find(':checked').val();
            load(id);
        })
    });
    function load(id) {
        if($.inArray(id,['3','4','5'])>=0){
            $("#div1").show();
        }else {
            $("#warehousebillcform-supplier_id").select2("val",'');
            $("#div1").hide();
        }
        if($.inArray(id,['1','2'])>=0){
            $("#div2").show();
        }else {
            $("#warehousebillcform-channel_id").select2("val",'');
            $("#div2").hide();
        }
        if($.inArray(id,['1'])>=0){
            $("#div3").show();
        }else {
            $("#warehousebillcform-restore_time").val("");
            $("#warehousebillcform-lender_id").select2("val",'');
            $("#div3").hide();
        }
    }
</script>

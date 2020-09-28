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
        <?= $form->field($model, 'gold_sn')->textInput([ "placeholder" => "单据审核自动生成"]) ?>
        <div class="row">

            <div class="col-lg-6">
                <?= $form->field($model, 'gold_type')->dropDownList(Yii::$app->attr->valueMap(\addons\Style\common\enums\AttrIdEnum::MAT_GOLD_TYPE),['prompt'=>'请选择']) ?>
            </div>
            <div class="col-lg-6">
                <?= $form->field($model, 'style_sn')->widget(\kartik\select2\Select2::class, [
                    'data' => Yii::$app->styleService->gold->getDropDown(),
                    'options' => ['placeholder' => '请选择'],
                    'pluginOptions' => [
                        'allowClear' => false
                    ],
                ]);?>
            </div>


        </div>
        <div class="row">
            <div class="col-lg-12">
                <?= $form->field($model, 'gold_name')->textInput() ?>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6">
                <?= $form->field($model, 'gold_weight')->textInput() ?>
            </div>
            <div class="col-lg-6">
                <?= $form->field($model, 'gold_price')->textInput() ?>
            </div>

        </div>
        <div class="row">
            <div class="col-lg-12">
                <?= $form->field($model, 'incl_tax_price')->textInput(["placeholder" => "不填：将自动计算：（金料总重*金料单价）"]) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <?= $form->field($model, 'remark')->textarea() ?>
            </div>

        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
    <button class="btn btn-primary" type="submit">保存</button>
</div>
<?php ActiveForm::end(); ?>
<script>
    var formId = 'warehousegoldbilltgoodsform';
    function fillStoneForm(){
        var style_sn = $("#"+formId+"-style_sn").val();
        if(style_sn != '') {
            $.ajax({
                type: "get",
                url: '<?php echo Url::to(['ajax-get-gold'])?>',
                dataType: "json",
                data: {
                    'style_sn': style_sn,
                },
                success: function (data) {
                    if (parseInt(data.code) == 200 && data.data) {
                        $("#"+formId+"-gold_name").val(data.data.gold_name);
                        $("#"+formId+"-gold_type").val(data.data.gold_type);
                    }
                }
            });
        }
    }
    function getGoodsSn() {
        var gold_type = $("#"+formId+"-gold_type").val();
        var html = '<option>请选择</option>';
        $.ajax({
            url: '<?php echo Url::to(['get-goods-sn'])?>',
            type: 'post',
            dataType: 'json',
            data: {gold_type: gold_type},
            success: function (msg) {
                console.log(msg.data)
                $.each(msg.data, function (key, val) {
                    html += '<option value="' + key + '">' + val + '</option>';
                });
                $("#"+formId+"-style_sn").html(html);
                $("#"+formId+"-gold_name").val('');
            }
        })
    }

    $("#"+formId+"-style_sn").change(function(){
        fillStoneForm();
    });

    $("#"+formId+"-gold_type").change(function(){
        getGoodsSn();
    });
</script>
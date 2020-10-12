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

        <div class="row">
            <div class="col-lg-4">
                <?= $form->field($model, 'stone_sn')->textInput([ "placeholder" => "单据审核自动生成"]) ?>
            </div>

            <div class="col-lg-4">
                <?= $form->field($model, 'stone_type')->dropDownList(Yii::$app->attr->valueMap(\addons\Style\common\enums\AttrIdEnum::MAT_STONE_TYPE),['prompt'=>'请选择']) ?>
            </div>
            <div class="col-lg-4">
                <?= $form->field($model, 'style_sn')->widget(\kartik\select2\Select2::class, [
                    'data' => Yii::$app->styleService->stone->getDropDown(),
                    'options' => ['placeholder' => '请选择'],
                    'pluginOptions' => [
                        'allowClear' => false
                    ],
                ]);?>
            </div>


        </div>
        <div class="row">
            <div class="col-lg-4">
                <?= $form->field($model, 'stone_name')->textInput() ?>
            </div>
            <div class="col-lg-4">
                <?= $form->field($model, 'cert_type')->dropDownList(Yii::$app->attr->valueMap(\addons\Style\common\enums\AttrIdEnum::DIA_CERT_TYPE),['prompt'=>'请选择']) ?>
            </div>
            <div class="col-lg-4">
                <?= $form->field($model, 'cert_id')->textInput() ?>
            </div>

        </div>
        <div class="row">
            <div class="col-lg-4">
                <?= $form->field($model, 'shape')->dropDownList(Yii::$app->attr->valueMap(\addons\Style\common\enums\AttrIdEnum::MAIN_STONE_SHAPE),['prompt'=>'请选择']) ?>
            </div>
            <div class="col-lg-4">
                <?= $form->field($model, 'color')->dropDownList(Yii::$app->attr->valueMap(\addons\Style\common\enums\AttrIdEnum::DIA_COLOR),['prompt'=>'请选择']) ?>
            </div>
            <div class="col-lg-4">
                <?= $form->field($model, 'clarity')->dropDownList(Yii::$app->attr->valueMap(\addons\Style\common\enums\AttrIdEnum::DIA_CLARITY),['prompt'=>'请选择']) ?>
            </div>


        </div>
        <div class="row">
            <div class="col-lg-4">
                <?= $form->field($model, 'cut')->dropDownList(Yii::$app->attr->valueMap(\addons\Style\common\enums\AttrIdEnum::DIA_CUT),['prompt'=>'请选择']) ?>
            </div>
            <div class="col-lg-4">
                <?= $form->field($model, 'symmetry')->dropDownList(Yii::$app->attr->valueMap(\addons\Style\common\enums\AttrIdEnum::DIA_SYMMETRY),['prompt'=>'请选择']) ?>
            </div>
            <div class="col-lg-4">
                <?= $form->field($model, 'polish')->dropDownList(Yii::$app->attr->valueMap(\addons\Style\common\enums\AttrIdEnum::DIA_POLISH),['prompt'=>'请选择']) ?>
            </div>

        </div>
        <div class="row">
            <div class="col-lg-4">
                <?= $form->field($model, 'fluorescence')->dropDownList(Yii::$app->attr->valueMap(\addons\Style\common\enums\AttrIdEnum::DIA_FLUORESCENCE),['prompt'=>'请选择']) ?>
            </div>
            <div class="col-lg-4">
                <?= $form->field($model, 'stone_colour')->dropDownList(Yii::$app->attr->valueMap(\addons\Style\common\enums\AttrIdEnum::DIA_COLOUR),['prompt'=>'请选择']) ?>
            </div>
            <div class="col-lg-4">
                <?= $form->field($model, 'stone_norms')->textInput() ?>
            </div>


        </div>
        <div class="row">
            <div class="col-lg-4">
                <?= $form->field($model, 'stone_size')->textInput() ?>
            </div>
            <div class="col-lg-4">
                <?= $form->field($model, 'stone_num')->textInput() ?>
            </div>
            <div class="col-lg-4">
                <?= $form->field($model, 'stone_weight')->textInput() ?>
            </div>


        </div>
        <div class="row">
            <div class="col-lg-4">
                <?= $form->field($model, 'stone_price')->textInput() ?>
            </div>
            <div class="col-lg-4">
                <?= $form->field($model, 'incl_tax_price')->textInput(["placeholder" => "不填：将自动计算：（金料总重*金料单价）"])->hint('<font color="red">（自动计算：“金料总重*金料单价”，可编辑）</font>') ?>
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
    var formId = 'warehousestonebillrkgoodsform';
    function fillStoneForm(){
        var style_sn = $("#"+formId+"-style_sn").val();
        if(style_sn != '') {
            $.ajax({
                type: "get",
                url: '<?php echo Url::to(['ajax-get-stone'])?>',
                dataType: "json",
                data: {
                    'style_sn': style_sn,
                },
                success: function (data) {
                    if (parseInt(data.code) == 200 && data.data) {
                        $("#"+formId+"-stone_name").val(data.data.stone_name);
                        $("#"+formId+"-stone_type").val(data.data.stone_type);
                        $("#"+formId+"-cert_type").val(data.data.cert_type);
                        $("#"+formId+"-shape").val(data.data.stone_shape);
                    }
                }
            });
        }
    }
    function getGoodsSn() {
        var stone_type = $("#"+formId+"-stone_type").val();
        var html = '<option>请选择</option>';
        $.ajax({
            url: '<?php echo Url::to(['get-goods-sn'])?>',
            type: 'post',
            dataType: 'json',
            data: {stone_type: stone_type},
            success: function (msg) {
                console.log(msg.data)
                $.each(msg.data, function (key, val) {
                    html += '<option value="' + key + '">' + val + '</option>';
                });
                $("#"+formId+"-style_sn").html(html);
                $("#"+formId+"-stone_name").val('');
            }
        })
    }

    function getInclTaxPrice(){
        var incl_tax_price = $("#"+formId+"-stone_price").val() * $("#"+formId+"-stone_weight").val();
        $("#"+formId+"-incl_tax_price").val(incl_tax_price);
    }
    $("#"+formId+"-stone_price").blur(function(){
        getInclTaxPrice();
    });
    $("#"+formId+"-stone_weight").blur(function(){
        getInclTaxPrice();
    });

    $("#"+formId+"-style_sn").change(function(){
        fillStoneForm();
    });

    $("#"+formId+"-stone_type").change(function(){
        getGoodsSn();
    });


</script>
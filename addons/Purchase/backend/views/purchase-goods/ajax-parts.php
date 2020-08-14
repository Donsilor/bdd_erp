<?php

use addons\Style\common\enums\AttrIdEnum;
use yii\widgets\ActiveForm;
use common\helpers\Url;
use kartik\date\DatePicker;

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
        <?= $form->field($model, 'parts_info')->widget(unclead\multipleinput\MultipleInput::class, [
            'max' => ($model->peijian_type-1),
            'value' => $parts_list ?? [],
            'columns' => [
                [
                    'name' => 'style_sn',
                    'title' => '配件款号',
                    'enableError' => false,
                    'options' => [
                        'class' => 'input-priority',
                        'style' => 'width:120px',
                        'Onblur' => 'fillPartsForm(this)',
                    ]
                ],
                [
                    'name' => 'parts_name',
                    'title' => '配件名称',
                    'enableError' => false,
                    'options' => [
                        'class' => 'input-priority',
                        'readonly' =>'true',
                        'style' => 'width:120px'
                    ]
                ],
                [
                    'name' => "parts_type",
                    'title' => '配件类型',
                    'enableError' => false,
                    'type' => 'dropDownList',
                    'options' => [
                        'class' => 'input-priority',
                        'readonly' =>'true',
                        'style' => 'width:100px',
                        'prompt' => '请选择',
                    ],
                    'items' => \Yii::$app->attr->valueMap(AttrIdEnum::MAT_PARTS_TYPE)
                ],
                [
                    'name' => "material_type",
                    'title' => '配件材质',
                    'enableError' => false,
                    'type' => 'dropDownList',
                    'options' => [
                        'class' => 'input-priority',
                        'readonly' =>'true',
                        'style' => 'width:100px',
                        'prompt' => '请选择',
                    ],
                    'items' => \Yii::$app->attr->valueMap(AttrIdEnum::MATERIAL_TYPE)
                ],
                [
                    'name' => 'parts_num',
                    'title' => '配件数量',
                    'enableError' => false,
                    'defaultValue' => '1',
                    'options' => [
                        'class' => 'input-priority',
                        'type' => 'number',
                        'style' => 'width:80px'
                    ]
                ],
                [
                    'name' => 'parts_gold_weight',
                    'title' => '配件金重(g)',
                    'enableError' => false,
                    'defaultValue' => '0.000',
                    'options' => [
                        'class' => 'input-priority',
                        'type' => 'number',
                        'style' => 'width:80px'
                    ]
                ],
                [
                    'name' => 'parts_price',
                    'title' => '配件单价/g',
                    'enableError' => false,
                    'defaultValue' => '0.00',
                    'options' => [
                        'class' => 'input-priority',
                        'type' => 'number',
                        'style' => 'width:80px'
                    ]
                ]
            ]
        ])->label("");
        ?>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
    <button class="btn btn-primary" type="submit">保存</button>
</div>
<?php ActiveForm::end(); ?>
<script>
    var formId = 'purchasegoodsform-parts_info';

    function fillPartsForm(obj) {
        var id = $(obj).attr("id");
        var ids = id.split("-");
        var i = ids[2];
        var style_sn = $("#" + formId + "-" + i + "-style_sn").val();
        if (style_sn != '') {
            $.ajax({
                type: "get",
                url: '<?php echo Url::to(['ajax-get-parts'])?>',
                dataType: "json",
                data: {
                    'style_sn': style_sn,
                },
                success: function (data) {
                    if (parseInt(data.code) == 200 && data.data) {
                        $("#" + formId + "-" + i + "-parts_name").val(data.data.parts_name);
                        $("#" + formId + "-" + i + "-parts_type").val(data.data.parts_type);
                        $("#" + formId + "-" + i + "-material_type").val(data.data.metal_type);
                    } else {
                        rfInfo(data.message);
                        $("#" + formId + "-" + i + "-style_sn").val("");
                    }
                }
            });
        }
    }
</script>

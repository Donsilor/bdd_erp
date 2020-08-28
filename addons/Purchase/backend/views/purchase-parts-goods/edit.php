<?php

use yii\widgets\ActiveForm;
use common\helpers\Html;
use common\helpers\Url;

$this->title = $model->isNewRecord ? '创建' : '编辑';
$this->params['breadcrumbs'][] = ['label' => 'Curd', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <?php $form = ActiveForm::begin([]); ?>
            <div class="box-body" style="padding:20px 50px">
                <?= $form->field($model, 'purchase_id')->hiddenInput()->label(false) ?>
                <div class="row">
                    <div class="col-lg-4">
                        <?= $form->field($model, 'goods_sn')->widget(\kartik\select2\Select2::class, [
                            'data' => Yii::$app->styleService->parts->getDropDown(),
                            'options' => ['placeholder' => '请选择'],
                            'pluginOptions' => [
                                'allowClear' => false
                            ],
                        ]); ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'parts_type')->dropDownList($model->getPartsTypeMap(), ['prompt' => '请选择']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'goods_name')->textInput() ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4">
                        <?= $form->field($model, 'material_type')->dropDownList($model->getMaterialTypeMap(), ['prompt' => '请选择']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'goods_color')->dropDownList($model->getColorMap(), ['prompt' => '请选择']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'goods_shape')->dropDownList($model->getShapeMap(), ['prompt' => '请选择']) ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4">
                        <?= $form->field($model, 'chain_type')->dropDownList($model->getChainTypeMap(), ['prompt' => '请选择']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'cramp_ring')->dropDownList($model->getCrampRingMap(), ['prompt' => '请选择']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'goods_size')->textInput() ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4">
                        <?= $form->field($model, 'goods_num')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'goods_weight')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'gold_price')->textInput() ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4">
                        <?= $form->field($model, 'cost_price')->textInput(['disabled' => 'disabled']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'remark')->textarea() ?>
                    </div>
                </div>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
<script>
    var formId = 'purchasepartsgoodsform';

    function fillPartsForm() {
        var goods_sn = $("#" + formId + "-goods_sn").val();
        if (goods_sn != '') {
            $.ajax({
                type: "get",
                url: '<?php echo Url::to(['ajax-get-parts'])?>',
                dataType: "json",
                data: {
                    'goods_sn': goods_sn,
                },
                success: function (data) {
                    if (parseInt(data.code) == 200 && data.data) {
                        $("#" + formId + "-goods_name").val(data.data.parts_name);
                        $("#" + formId + "-parts_type").val(data.data.parts_type);
                        $("#" + formId + "-material_type").val(data.data.metal_type);
                        $("#" + formId + "-goods_color").val(data.data.color);
                        $("#" + formId + "-goods_shape").val(data.data.shape);
                    }
                }
            });
        }
    }

    $("#" + formId + "-goods_sn").change(function () {
        fillPartsForm();
    });
</script>
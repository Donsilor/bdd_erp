<?php

use common\helpers\Url;
use yii\widgets\ActiveForm;
use addons\Style\common\enums\AttrIdEnum;

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
                    <div class="col-lg-3">
                        <?= $form->field($model, 'goods_sn')->widget(\kartik\select2\Select2::class, [
                            'data' => Yii::$app->styleService->gift->getDropDown(),
                            'options' => ['placeholder' => '请选择'],
                            'pluginOptions' => [
                                'allowClear' => false
                            ],
                        ]); ?>
                    </div>
                    <div class="col-lg-3">
                        <?= $form->field($model, 'goods_name')->textInput() ?>
                    </div>
<!--                    <div class="col-lg-3">-->
<!--                        --><?//= $form->field($model, 'product_type_id')->dropDownList(Yii::$app->styleService->productType::getDropDown(),['disabled'=>true]) ?>
<!--                    </div>-->
                    <div class="col-lg-3">
                        <?= $form->field($model, 'style_cate_id')->dropDownList(Yii::$app->styleService->styleCate::getDropDown(),['disabled'=>true]) ?>
                    </div>
                    <div class="col-lg-3">
                        <?= $form->field($model, 'style_sex')->dropDownList(\addons\Style\common\enums\StyleSexEnum::getMap(),['disabled'=>true]) ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-3">
                        <?= $form->field($model, 'material_type')->dropDownList($model->getMaterialTypeMap(),['prompt'=>'请选择']) ?>
                    </div>
                    <div class="col-lg-3">
                        <?= $form->field($model, 'material_color')->dropDownList($model->getMaterialColorMap(),['prompt'=>'请选择']) ?>
                    </div>
                    <div class="col-lg-3">
                        <?= $form->field($model, 'finger_hk')->dropDownList(Yii::$app->attr->valueMap(\addons\Style\common\enums\AttrIdEnum::PORT_NO),['prompt'=>'请选择']) ?>
                    </div>
                    <div class="col-lg-3">
                        <?= $form->field($model, 'finger')->dropDownList(Yii::$app->attr->valueMap(AttrIdEnum::FINGER),['prompt'=>'请选择']) ?>
                    </div>
                </div>
			   <div class="row">
                   <div class="col-lg-3">
                       <?= $form->field($model, 'main_stone_type')->dropDownList($model->getMainStoneTypeMap(),['prompt'=>'请选择']) ?>
                   </div>
                   <div class="col-lg-3">
                       <?= $form->field($model, 'main_stone_num')->textInput() ?>
                   </div>
                   <div class="col-lg-3">
                       <?= $form->field($model, 'chain_length')->textInput() ?>
                   </div>
                   <div class="col-lg-3">
                       <?= $form->field($model, 'goods_size')->textInput() ?>
                   </div>
               </div>
                <div class="row">
                    <div class="col-lg-3">
                        <?= $form->field($model, 'goods_num')->textInput() ?>
                    </div>
                    <div class="col-lg-3">
                        <?= $form->field($model, 'goods_weight')->textInput() ?>
                    </div>
                    <div class="col-lg-3">
                        <?= $form->field($model, 'gold_price')->textInput() ?>
                    </div>
                    <div class="col-lg-3">
<!--                        ['disabled'=>'disabled']-->
                        <?= $form->field($model, 'cost_price')->textInput() ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <?= $form->field($model, 'remark')->textarea() ?>
                    </div>
                </div>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
<script>
    var formId = 'purchasegiftgoodsform';

    function fillGiftForm() {
        var goods_sn = $("#" + formId + "-goods_sn").val();
        if (goods_sn != '') {
            $.ajax({
                type: "get",
                url: '<?php echo Url::to(['ajax-get-gift'])?>',
                dataType: "json",
                data: {
                    'goods_sn': goods_sn,
                },
                success: function (data) {
                    if (parseInt(data.code) == 200 && data.data) {
                        $("#" + formId + "-goods_name").val(data.data.gift_name);
                        $("#" + formId + "-style_cate_id").val(data.data.style_cate_id);
                        //$("#" + formId + "-product_type_id").val(data.data.product_type_id);
                        $("#" + formId + "-style_sex").val(data.data.style_sex);
                        $("#" + formId + "-material_type").val(data.data.material_type);
                        $("#" + formId + "-material_color").val(data.data.material_color);
                        $("#" + formId + "-finger_hk").val(data.data.finger_hk);
                        $("#" + formId + "-finger").val(data.data.finger);
                        $("#" + formId + "-chain_length").val(data.data.chain_length);
                        $("#" + formId + "-goods_size").val(data.data.goods_size);
                        $("#" + formId + "-cost_price").val(data.data.cost_price);
                    }
                }
            });
        }
    }

    $("#" + formId + "-goods_sn").change(function () {
        fillGiftForm();
    });
</script>
<?php

use yii\widgets\ActiveForm;
use addons\Style\common\enums\AttrIdEnum;

$this->params['breadcrumbs'][] = ['label' => 'Curd', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <?php $form = ActiveForm::begin([]); ?>
            <div class="box-body" style="padding:20px 50px">
                <div class="row">
                    <div class="with-border">
                        <h5 class="box-title" style="font-weight: bold">基本信息</h5>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'goods_status')->dropDownList(\addons\Purchase\common\enums\ReceiptGoodsStatusEnum::getMap(),['disabled'=>true]) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'purchase_sn')->textInput(['disabled'=>true]) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'purchase_sn')->textInput(['disabled'=>true]) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'produce_sn')->textInput(['disabled'=>true]) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'order_sn')->textInput(['disabled'=>true]) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'goods_sn')->textInput(['disabled'=>true]) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'style_sn')->textInput(['disabled'=>true]) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'jintuo_type')->dropDownList(\addons\Style\common\enums\JintuoTypeEnum::getMap(),['disabled'=>true]) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'product_type_id')->dropDownList(Yii::$app->styleService->productType::getDropDown(),['disabled'=>true]) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'style_cate_id')->dropDownList(Yii::$app->styleService->productType::getDropDown(),['disabled'=>true]) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'style_sex')->dropDownList(\addons\Style\common\enums\StyleSexEnum::getMap(),['disabled'=>true]) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'qiban_sn')->textInput(['disabled'=>true]) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'qiban_type')->dropDownList(\addons\Style\common\enums\QibanTypeEnum::getMap(),['disabled'=>true]) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'goods_name')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'material')->dropDownList(Yii::$app->attr->valueMap(AttrIdEnum::MATERIAL),['prompt'=>'请选择']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'material_type')->dropDownList(Yii::$app->attr->valueMap(AttrIdEnum::MATERIAL_TYPE),['prompt'=>'请选择']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'material_color')->dropDownList(Yii::$app->attr->valueMap(AttrIdEnum::MATERIAL_COLOR),['prompt'=>'请选择']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'finger_hk')->dropDownList(Yii::$app->attr->valueMap(\addons\Style\common\enums\AttrIdEnum::PORT_NO),['prompt'=>'请选择']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'finger')->dropDownList(Yii::$app->attr->valueMap(AttrIdEnum::FINGER),['prompt'=>'请选择']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'factory_mo')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'xiangkou')->dropDownList(Yii::$app->attr->valueMap(AttrIdEnum::XIANGKOU),['prompt'=>'请选择']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'is_inlay')->dropDownList(\addons\Style\common\enums\InlayEnum::getMap(),['prompt'=>'请选择']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'kezi')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'gross_weight')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'suttle_weight')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'goods_color')->dropDownList(Yii::$app->attr->valueMap(AttrIdEnum::GOODS_COLOR),['prompt'=>'请选择']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'product_size')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'chain_long')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'chain_type')->dropDownList(Yii::$app->attr->valueMap(AttrIdEnum::CHAIN_TYPE),['prompt'=>'请选择']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'cramp_ring')->dropDownList(Yii::$app->attr->valueMap(AttrIdEnum::CHAIN_BUCKLE),['prompt'=>'请选择']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'talon_head_type')->dropDownList(Yii::$app->attr->valueMap(AttrIdEnum::TALON_HEAD_TYPE),['prompt'=>'请选择']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'xiangqian_craft')->dropDownList(Yii::$app->attr->valueMap(AttrIdEnum::XIANGQIAN_CRAFT),['prompt'=>'请选择']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'biaomiangongyi')->dropDownList(Yii::$app->attr->valueMap(AttrIdEnum::FACEWORK),['prompt'=>'请选择']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'cost_price')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'market_price')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'sale_price')->textInput() ?>
                    </div>
                </div>

                <div class="row">
                    <div class="with-border">
                        <h5 class="box-title" style="font-weight: bold">金属信息</h5>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'gold_weight')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'parts_weight')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'gold_loss')->textInput() ?>
                    </div>
                </div>

                <div class="row">
                    <div class="with-border">
                        <h5 class="box-title" style="font-weight: bold">石头信息</h5>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'cert_id')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'cert_type')->dropDownList(Yii::$app->attr->valueMap(AttrIdEnum::DIA_CERT_TYPE),['prompt'=>'请选择']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'main_stone')->dropDownList(Yii::$app->attr->valueMap(AttrIdEnum::MAIN_STONE_TYPE),['prompt'=>'请选择']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'main_stone_sn')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'main_stone_num')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'main_stone_weight')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'main_cert_id')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'main_cert_type')->dropDownList(Yii::$app->attr->valueMap(AttrIdEnum::DIA_CERT_TYPE),['prompt'=>'请选择']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'main_stone_shape')->dropDownList(Yii::$app->attr->valueMap(AttrIdEnum::MAIN_STONE_SHAPE),['prompt'=>'请选择']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'main_stone_color')->dropDownList(Yii::$app->attr->valueMap(AttrIdEnum::MAIN_STONE_COLOR),['prompt'=>'请选择']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'main_stone_clarity')->dropDownList(Yii::$app->attr->valueMap(AttrIdEnum::MAIN_STONE_CLARITY),['prompt'=>'请选择']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'main_stone_cut')->dropDownList(Yii::$app->attr->valueMap(AttrIdEnum::MAIN_STONE_CUT),['prompt'=>'请选择']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'main_stone_symmetry')->dropDownList(Yii::$app->attr->valueMap(AttrIdEnum::MAIN_STONE_SYMMETRY),['prompt'=>'请选择']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'main_stone_polish')->dropDownList(Yii::$app->attr->valueMap(AttrIdEnum::MAIN_STONE_POLISH),['prompt'=>'请选择']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'main_stone_fluorescence')->dropDownList(Yii::$app->attr->valueMap(AttrIdEnum::MAIN_STONE_FLUORESCENCE),['prompt'=>'请选择']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'main_stone_colour')->dropDownList(Yii::$app->attr->valueMap(AttrIdEnum::MAIN_STONE_COLOUR),['prompt'=>'请选择']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'main_stone_size')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'main_stone_price')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'second_cert_id1')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'second_stone1')->dropDownList(Yii::$app->attr->valueMap(AttrIdEnum::SIDE_STONE1_TYPE),['prompt'=>'请选择']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'second_stone_num1')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'second_stone_weight1')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'second_stone_shape1')->dropDownList(Yii::$app->attr->valueMap(AttrIdEnum::SIDE_STONE1_SHAPE),['prompt'=>'请选择']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'second_stone_color1')->dropDownList(Yii::$app->attr->valueMap(AttrIdEnum::SIDE_STONE1_COLOR),['prompt'=>'请选择']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'second_stone_clarity1')->dropDownList(Yii::$app->attr->valueMap(AttrIdEnum::SIDE_STONE1_CLARITY),['prompt'=>'请选择']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'second_stone_size1')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'second_stone_price1')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'second_stone2')->dropDownList(Yii::$app->attr->valueMap(AttrIdEnum::SIDE_STONE2_TYPE),['prompt'=>'请选择']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'second_stone_num2')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'second_stone_weight2')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'second_stone_shape2')->dropDownList(Yii::$app->attr->valueMap(AttrIdEnum::SIDE_STONE2_SHAPE),['prompt'=>'请选择']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'second_stone_color2')->dropDownList(Yii::$app->attr->valueMap(AttrIdEnum::SIDE_STONE1_COLOR),['prompt'=>'请选择']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'second_stone_clarity2')->dropDownList(Yii::$app->attr->valueMap(AttrIdEnum::SIDE_STONE1_CLARITY),['prompt'=>'请选择']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'second_stone_size2')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'second_stone_price2')->textInput() ?>
                    </div>
                </div>
                <div class="row">
                    <div class="with-border">
                        <h5 class="box-title" style="font-weight: bold">费用信息</h5>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'markup_rate')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'gong_fee')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'parts_price')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'parts_fee')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'xianqian_fee')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'biaomiangongyi_fee')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'fense_fee')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'bukou_fee')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'cert_fee')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'extra_stone_fee')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'tax_fee')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'other_fee')->textInput() ?>
                    </div>
                </div>
               <!-- ./box-body -->
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

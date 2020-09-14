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
                        <?= $form->field($model, 'goods_id')->textInput(['readonly'=>true]) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'goods_name')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'xiangkou')->dropDownList(Yii::$app->attr->valueMap(AttrIdEnum::XIANGKOU),['prompt'=>'请选择']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'finger')->dropDownList(Yii::$app->attr->valueMap(AttrIdEnum::FINGER),['prompt'=>'请选择']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'product_size')->textInput() ?>
                    </div>
                    <?php
                    if(\common\helpers\Auth::verify(\common\enums\SpecialAuthEnum::VIEW_CAIGOU_PRICE)){
                    ?>
                    <?php } ?>
                </div>

                <div class="row">
                    <div class="with-border">
                        <h5 class="box-title" style="font-weight: bold">金属信息</h5>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'gold_weight')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'suttle_weight')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'gold_loss')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'gold_price')->textInput() ?>
                    </div>

                </div>

                <div class="row">
                    <div class="with-border">
                        <h5 class="box-title" style="font-weight: bold">石头信息</h5>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'main_stone_sn')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'main_stone_type')->dropDownList(Yii::$app->attr->valueMap(AttrIdEnum::MAIN_STONE_TYPE),['prompt'=>'请选择']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'main_stone_num')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'main_stone_price')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'diamond_shape')->dropDownList(Yii::$app->attr->valueMap(AttrIdEnum::DIA_SHAPE),['prompt'=>'请选择']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'diamond_carat')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'diamond_color')->dropDownList(Yii::$app->attr->valueMap(AttrIdEnum::DIA_COLOR),['prompt'=>'请选择']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'diamond_clarity')->dropDownList(Yii::$app->attr->valueMap(AttrIdEnum::DIA_CLARITY),['prompt'=>'请选择']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'diamond_cut')->dropDownList(Yii::$app->attr->valueMap(AttrIdEnum::DIA_CUT),['prompt'=>'请选择']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'diamond_polish')->dropDownList(Yii::$app->attr->valueMap(AttrIdEnum::DIA_POLISH),['prompt'=>'请选择']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'diamond_symmetry')->dropDownList(Yii::$app->attr->valueMap(AttrIdEnum::DIA_SYMMETRY),['prompt'=>'请选择']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'diamond_fluorescence')->dropDownList(Yii::$app->attr->valueMap(AttrIdEnum::DIA_FLUORESCENCE),['prompt'=>'请选择']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'diamond_cert_type')->dropDownList(Yii::$app->attr->valueMap(AttrIdEnum::DIA_CERT_TYPE),['prompt'=>'请选择']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'diamond_cert_id')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'second_stone_sn1')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'second_stone_type1')->dropDownList(Yii::$app->attr->valueMap(AttrIdEnum::SIDE_STONE1_TYPE),['prompt'=>'请选择']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'second_stone_shape1')->dropDownList(Yii::$app->attr->valueMap(AttrIdEnum::DIA_SHAPE),['prompt'=>'请选择']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'second_stone_weight1')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'second_stone_num1')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'second_stone_color1')->dropDownList(Yii::$app->attr->valueMap(AttrIdEnum::SIDE_STONE1_COLOR),['prompt'=>'请选择']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'second_stone_clarity1')->dropDownList(Yii::$app->attr->valueMap(AttrIdEnum::SIDE_STONE1_CLARITY),['prompt'=>'请选择']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'second_stone_price1')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'second_stone_sn2')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'second_stone_type2')->dropDownList(Yii::$app->attr->valueMap(AttrIdEnum::SIDE_STONE2_TYPE),['prompt'=>'请选择']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'second_stone_num2')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'second_stone_weight2')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'second_stone_price2')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'second_stone_sn3')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'second_stone_type3')->dropDownList(Yii::$app->attr->valueMap(AttrIdEnum::SIDE_STONE3_TYPE),['prompt'=>'请选择']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'second_stone_num3')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'second_stone_weight3')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'second_stone_price3')->textInput() ?>
                    </div>
                </div>
                <div class="row">
                    <div class="with-border">
                        <h5 class="box-title" style="font-weight: bold">其它信息</h5>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'parts_gold_weight')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'pure_gold')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'parts_price')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'gong_fee')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'bukou_fee')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'cert_fee')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'biaomiangongyi_fee')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'parts_fee')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'fense_fee')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'penrasa_fee')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'piece_fee')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'edition_fee')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'other_fee')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'second_stone_fee1')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'second_stone_fee2')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'second_stone_fee3')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'peishi_fee')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'peishi_weight')->textInput() ?>
                    </div>
                </div>
               <!-- ./box-body -->
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

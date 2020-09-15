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
                        <?= $form->field($model, 'style_cate_id')->dropDownList($model->getCateMap(), ['disabled' => true]) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'product_type_id')->dropDownList($model->getProductMap(), ['disabled' => true]) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'goods_id')->textInput(['disabled' => $model->auto_goods_id ? false : true]) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'style_sn')->textInput(['disabled' => true]) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'qiban_sn')->textInput(['disabled' => true]) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'qiban_type')->dropDownList($model->getQibanTypeMap(), ['disabled' => true]) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'goods_name')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'material_type')->dropDownList($model->getMaterialTypeDrop($model), ['prompt' => '请选择']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'material_color')->dropDownList($model->getMaterialColorDrop($model), ['prompt' => '请选择']) ?>
                    </div>
                    <!--                    <div class="col-lg-4">-->
                    <!--                        --><? //= $form->field($model, 'goods_num')->textInput(['disabled'=>true]) ?>
                    <!--                    </div>-->
                    <!--                    <div class="col-lg-4">-->
                    <!--                        --><? //= $form->field($model, 'material')->dropDownList(\Yii::$app->styleService->styleAttribute->getAttrValueListByStyle($model->style_sn,AttrIdEnum::MATERIAL),['prompt'=>'请选择']) ?>
                    <!--                    </div>-->
                    <div class="col-lg-4">
                        <?= $form->field($model, 'finger_hk')->dropDownList($model->getPortNoDrop($model), ['prompt' => '请选择']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'finger')->dropDownList($model->getFingerDrop($model), ['prompt' => '请选择']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'length')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'product_size')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'xiangkou')->dropDownList($model->getXiangkouDrop($model), ['prompt' => '请选择']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'kezi')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'chain_type')->dropDownList($model->getChainTypeDrop($model), ['prompt' => '请选择']) ?>
                    </div>
<!--                    <div class="col-lg-4">-->
<!--                        --><?//= $form->field($model, 'chain_long')->textInput() ?>
<!--                    </div>-->
                    <div class="col-lg-4">
                        <?= $form->field($model, 'cramp_ring')->dropDownList($model->getCrampRingDrop($model), ['prompt' => '请选择']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'talon_head_type')->dropDownList($model->getTalonHeadTypeDrop($model), ['prompt' => '请选择']) ?>
                    </div>
<!--                    <div class="col-lg-4">-->
<!--                        --><?//= $form->field($model, 'cert_type')->dropDownList($model->getCertTypeDrop($model), ['prompt' => '请选择']) ?>
<!--                    </div>-->
<!--                    <div class="col-lg-4">-->
<!--                        --><?//= $form->field($model, 'cert_id')->textInput() ?>
<!--                    </div>-->
                    <div class="col-lg-4">
                        <?= $form->field($model, 'markup_rate')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'xiangqian_craft')->dropDownList($model->getXiangqianCraftDrop($model), ['prompt' => '请选择']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'biaomiangongyi')->widget(kartik\select2\Select2::class, [
                            'data' => $model->getFaceCraftDrop($model),
                            'options' => ['placeholder' => '请选择','multiple'=>true],
                            'pluginOptions' => [
                                'allowClear' => true
                            ],
                        ]);?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'jintuo_type')->dropDownList($model->getJietuoTypeMap($model), ['prompt' => '请选择']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'is_inlay')->dropDownList($model->getIsInlayMap($model), ['prompt' => '请选择']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'factory_mo')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'order_sn')->textInput() ?>
                    </div>
                    <!--                    <div class="col-lg-4">-->
                    <!--                        --><? //= $form->field($model, 'gross_weight')->textInput() ?>
                    <!--                    </div>-->
                    <!--                    <div class="col-lg-4">-->
                    <!--                        --><? //= $form->field($model, 'goods_color')->dropDownList(\Yii::$app->styleService->styleAttribute->getAttrValueListByStyle($model->style_sn,AttrIdEnum::GOODS_COLOR),['prompt'=>'请选择']) ?>
                    <!--                    </div>-->
                </div>
                <div class="row">
                    <div class="with-border">
                        <h5 class="box-title" style="font-weight: bold">金料信息</h5>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'peiliao_way')->dropDownList($model->getPeiLiaoWayMap(), ['prompt' => '请选择']) ?>
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
                    <div class="col-lg-4">
                        <?= $form->field($model, 'pure_gold')->textInput() ?>
                    </div>
                </div>
<!--                <div class="row">-->
<!--                    <div class="with-border">-->
<!--                        <h5 class="box-title" style="font-weight: bold">钻石信息</h5>-->
<!--                    </div>-->
<!--                    <div class="col-lg-4">-->
<!--                        --><?//= $form->field($model, 'diamond_cert_id')->textInput() ?>
<!--                    </div>-->
<!--                    <div class="col-lg-4">-->
<!--                        --><?//= $form->field($model, 'diamond_cert_type')->dropDownList($model->getDiamondCertTypeDrop($model), ['prompt' => '请选择']) ?>
<!--                    </div>-->
<!--                    <div class="col-lg-4">-->
<!--                        --><?//= $form->field($model, 'diamond_carat')->textInput() ?>
<!--                    </div>-->
<!--                    <div class="col-lg-4">-->
<!--                        --><?//= $form->field($model, 'diamond_shape')->dropDownList($model->getDiamondShapeDrop($model), ['prompt' => '请选择']) ?>
<!--                    </div>-->
<!--                    <div class="col-lg-4">-->
<!--                        --><?//= $form->field($model, 'diamond_color')->dropDownList($model->getDiamondColorDrop($model), ['prompt' => '请选择']) ?>
<!--                    </div>-->
<!--                    <div class="col-lg-4">-->
<!--                        --><?//= $form->field($model, 'diamond_clarity')->dropDownList($model->getDiamondClarityDrop($model), ['prompt' => '请选择']) ?>
<!--                    </div>-->
<!--                    <div class="col-lg-4">-->
<!--                        --><?//= $form->field($model, 'diamond_cut')->dropDownList($model->getDiamondCutDrop($model), ['prompt' => '请选择']) ?>
<!--                    </div>-->
<!--                    <div class="col-lg-4">-->
<!--                        --><?//= $form->field($model, 'diamond_polish')->dropDownList($model->getDiamondPolishDrop($model), ['prompt' => '请选择']) ?>
<!--                    </div>-->
<!--                    <div class="col-lg-4">-->
<!--                        --><?//= $form->field($model, 'diamond_symmetry')->dropDownList($model->getDiamondSymmetryDrop($model), ['prompt' => '请选择']) ?>
<!--                    </div>-->
<!--                    <div class="col-lg-4">-->
<!--                        --><?//= $form->field($model, 'diamond_fluorescence')->dropDownList($model->getDiamondFluorescenceDrop($model), ['prompt' => '请选择']) ?>
<!--                    </div>-->
<!--                    <div class="col-lg-4">-->
<!--                        --><?//= $form->field($model, 'diamond_discount')->textInput() ?>
<!--                    </div>-->
<!--                </div>-->
                <div class="row">
                    <div class="with-border">
                        <h5 class="box-title" style="font-weight: bold">主石信息</h5>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'main_pei_type')->dropDownList(\addons\Warehouse\common\enums\PeiShiWayEnum::getMap(), ['prompt' => '请选择']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'main_stone_sn')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'main_stone_type')->dropDownList($model->getMainStoneTypeDrop($model), ['prompt' => '请选择']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'main_stone_num')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'main_stone_weight')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'main_stone_price')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'main_stone_shape')->dropDownList($model->getMainStoneShapeDrop($model), ['prompt' => '请选择']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'main_stone_color')->dropDownList($model->getMainStoneColorDrop($model), ['prompt' => '请选择']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'main_stone_clarity')->dropDownList($model->getMainStoneClarityDrop($model), ['prompt' => '请选择']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'main_stone_cut')->dropDownList($model->getMainStoneCutDrop($model), ['prompt' => '请选择']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'main_stone_colour')->dropDownList($model->getMainStoneColourDrop($model), ['prompt' => '请选择']) ?>
                    </div>
<!--                    <div class="col-lg-4">-->
<!--                        --><?//= $form->field($model, 'main_stone_size')->textInput() ?>
<!--                    </div>-->
                    <div class="col-lg-4">
                        <?= $form->field($model, 'main_cert_id')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'main_cert_type')->dropDownList($model->getMainCertTypeDrop($model), ['prompt' => '请选择']) ?>
                    </div>
                </div>
                <div class="row">
                    <div class="with-border">
                        <h5 class="box-title" style="font-weight: bold">副石1信息</h5>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'second_pei_type')->dropDownList(\addons\Warehouse\common\enums\PeiShiWayEnum::getMap(), ['prompt' => '请选择']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'second_stone_sn1')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'second_stone_type1')->dropDownList($model->getSecondStoneType1Drop($model), ['prompt' => '请选择']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'second_stone_num1')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'second_stone_weight1')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'second_stone_price1')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'second_stone_shape1')->dropDownList($model->getSecondStoneShape1Drop($model), ['prompt' => '请选择']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'second_stone_color1')->dropDownList($model->getSecondStoneColor1Drop($model), ['prompt' => '请选择']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'second_stone_clarity1')->dropDownList($model->getSecondStoneClarity1Drop($model), ['prompt' => '请选择']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'second_stone_cut1')->dropDownList($model->getSecondStoneCut1Drop($model), ['prompt' => '请选择']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'second_stone_colour1')->dropDownList($model->getSecondStoneColour1Drop($model), ['prompt' => '请选择']) ?>
                    </div>
<!--                    <div class="col-lg-4">-->
<!--                        --><?//= $form->field($model, 'second_stone_size1')->textInput() ?>
<!--                    </div>-->
<!--                    <div class="col-lg-4">-->
<!--                        --><?//= $form->field($model, 'second_cert_id1')->textInput() ?>
<!--                    </div>-->
<!--                    <div class="col-lg-4">-->
<!--                        --><?//= $form->field($model, 'second_stone_type1')->dropDownList($model->getSecondStoneType1Drop($model), ['prompt' => '请选择']) ?>
<!--                    </div>-->
<!--                    <div class="col-lg-4">-->
<!--                        --><?//= $form->field($model, 'peishi_fee')->textInput() ?>
<!--                    </div>-->
                </div>
                <div class="row">
                    <div class="with-border">
                        <h5 class="box-title" style="font-weight: bold">副石2信息</h5>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'second_pei_type2')->dropDownList(\addons\Warehouse\common\enums\PeiShiWayEnum::getMap(), ['prompt' => '请选择']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'second_stone_sn2')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'second_stone_type2')->dropDownList($model->getSecondStoneType2Drop($model), ['prompt' => '请选择']) ?>
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
<!--                    <div class="col-lg-4">-->
<!--                        --><?//= $form->field($model, 'second_stone_shape2')->dropDownList($model->getSecondStoneShape2Drop($model), ['prompt' => '请选择']) ?>
<!--                    </div>-->
<!--                    <div class="col-lg-4">-->
<!--                        --><?//= $form->field($model, 'second_stone_color2')->dropDownList($model->getSecondStoneClarity2Drop($model), ['prompt' => '请选择']) ?>
<!--                    </div>-->
<!--                    <div class="col-lg-4">-->
<!--                        --><?//= $form->field($model, 'second_stone_clarity2')->dropDownList($model->getSecondStoneClarity2Drop($model), ['prompt' => '请选择']) ?>
<!--                    </div>-->
<!--                    <div class="col-lg-4">-->
<!--                        --><?//= $form->field($model, 'second_stone_colour2')->dropDownList($model->getSecondStoneColour2Drop($model), ['prompt' => '请选择']) ?>
<!--                    </div>-->
<!--                    <div class="col-lg-4">-->
<!--                        --><?//= $form->field($model, 'second_stone_size2')->textInput() ?>
<!--                    </div>-->
<!--                    <div class="col-lg-4">-->
<!--                        --><?//= $form->field($model, 'second_cert_id2')->textInput() ?>
<!--                    </div>-->
<!--                    <div class="col-lg-4">-->
<!--                        --><?//= $form->field($model, 'second_stone_type2')->dropDownList($model->getSecondStoneType2Drop($model), ['prompt' => '请选择']) ?>
<!--                    </div>-->
                </div>
                <div class="row">
                    <div class="with-border">
                        <h5 class="box-title" style="font-weight: bold">副石3信息</h5>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'second_pei_type3')->dropDownList(\addons\Warehouse\common\enums\PeiShiWayEnum::getMap(), ['prompt' => '请选择']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'second_stone_sn3')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'second_stone_type3')->dropDownList($model->getSecondStoneType3Drop($model), ['prompt' => '请选择']) ?>
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
                    <div class="col-lg-4">
                        <?= $form->field($model, 'stone_remark')->textInput() ?>
                    </div>
                </div>
                <div class="row">
                    <div class="with-border">
                        <h5 class="box-title" style="font-weight: bold">配件信息</h5>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'parts_way')->dropDownList($model->getPeiJianWayMap(), ['prompt' => '请选择']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'parts_type')->dropDownList($model->getPartsTypeMap(), ['prompt' => '请选择']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'parts_material')->dropDownList($model->getPartsMaterialMap(), ['prompt' => '请选择']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'parts_num')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'parts_gold_weight')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'parts_price')->textInput() ?>
                    </div>
                </div>
                <div class="row">
                    <div class="with-border">
                        <h5 class="box-title" style="font-weight: bold">工费及其它费用信息</h5>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'gong_fee')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'piece_fee')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'peishi_weight')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'peishi_gong_fee')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'parts_fee')->textInput() ?>
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
<!--                    <div class="col-lg-4">-->
<!--                        --><?//= $form->field($model, 'xianqian_price')->textInput() ?>
<!--                    </div>-->
                    <div class="col-lg-4">
                        <?= $form->field($model, 'biaomiangongyi_fee')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'fense_fee')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'penlasha_fee')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'lasha_fee')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'bukou_fee')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'templet_fee')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'cert_fee')->textInput() ?>
                    </div>
<!--                    <div class="col-lg-4">-->
<!--                        --><?//= $form->field($model, 'extra_stone_fee')->textInput() ?>
<!--                    </div>-->
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

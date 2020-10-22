<?php

use kartik\select2\Select2;
use yii\widgets\ActiveForm;
use addons\Style\common\enums\AttrIdEnum;

$this->params['breadcrumbs'][] = ['label' => 'Curd', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$goods_type = $bill->billL->goods_type ?? 0;
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
                        <?= $form->field($model, 'goods_name')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'qiban_sn')->textInput(['disabled' => true]) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'qiban_type')->dropDownList($model->getQibanTypeMap(), ['disabled' => true]) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'goods_num')->textInput(['disabled' => true]) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'material_type')->dropDownList($model->getMaterialTypeDrop($model), ['prompt' => '请选择']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'material_color')->dropDownList($model->getMaterialColorDrop($model), ['prompt' => '请选择']) ?>
                    </div>
                    <?php if (!in_array($goods_type, [\addons\Warehouse\common\enums\GoodsTypeEnum::PlainGold])) { ?>
                        <div class="col-lg-4">
                            <?= $form->field($model, 'xiangkou')->dropDownList($model->getXiangkouDrop($model), ['prompt' => '请选择']) ?>
                        </div>
                        <!--                    <div class="col-lg-4">-->
                        <!--                        --><? //= $form->field($model, 'goods_num')->textInput(['disabled'=>true]) ?>
                        <!--                    </div>-->
                        <!--                    <div class="col-lg-4">-->
                        <!--                        --><? //= $form->field($model, 'material')->dropDownList(\Yii::$app->styleService->styleAttribute->getAttrValueListByStyle($model->style_sn,AttrIdEnum::MATERIAL),['prompt'=>'请选择']) ?>
                        <!--                    </div>-->
                    <?php }?>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'finger_hk')->dropDownList($model->getFingerHkDrop($model), ['prompt' => '请选择']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'finger')->dropDownList($model->getFingerDrop($model), ['prompt' => '请选择']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'kezi')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'length')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'product_size')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'chain_type')->dropDownList($model->getChainTypeDrop($model), ['prompt' => '请选择']) ?>
                    </div>
                    <!--                    <div class="col-lg-4">-->
                    <!--                        --><? //= $form->field($model, 'chain_long')->textInput() ?>
                    <!--                    </div>-->
                    <div class="col-lg-4">
                        <?= $form->field($model, 'cramp_ring')->dropDownList($model->getCrampRingDrop($model), ['prompt' => '请选择']) ?>
                    </div>
                    <?php if (!in_array($goods_type, [\addons\Warehouse\common\enums\GoodsTypeEnum::PlainGold])) { ?>
                        <div class="col-lg-4">
                            <?= $form->field($model, 'talon_head_type')->dropDownList($model->getTalonHeadTypeDrop($model), ['prompt' => '请选择']) ?>
                        </div>
                    <?php }?>
                    <!--                    <div class="col-lg-4">-->
                    <!--                        --><? //= $form->field($model, 'cert_type')->dropDownList($model->getCertTypeDrop($model), ['prompt' => '请选择']) ?>
                    <!--                    </div>-->
                    <!--                    <div class="col-lg-4">-->
                    <!--                        --><? //= $form->field($model, 'cert_id')->textInput() ?>
                    <!--                    </div>-->
                    <div class="col-lg-4">
                        <?= $form->field($model, 'markup_rate')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'xiangqian_craft')->dropDownList($model->getXiangqianCraftDrop($model), ['prompt' => '请选择']) ?>
                    </div>
                    <div class="col-sm-4">
                        <?= $form->field($model, 'auto_goods_id')->radioList(\common\enums\ConfirmEnum::getMap()) ?>
                    </div>
                    <div class="col-sm-4">
                        <?= $form->field($model, 'jintuo_type')->radioList($model->getJietuoTypeMap($model)) ?>
                    </div>
                    <div class="col-sm-4">
                        <?= $form->field($model, 'is_inlay')->radioList($model->getIsInlayMap($model)) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'biaomiangongyi')->widget(kartik\select2\Select2::class, [
                            'data' => $model->getFaceCraftDrop($model),
                            'options' => ['placeholder' => '请选择', 'multiple' => true],
                            'pluginOptions' => [
                                'allowClear' => true
                            ],
                        ]); ?>
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
                    <div class="col-sm-4">
                        <?= $form->field($model, 'peiliao_way')->radioList($model->getPeiLiaoWayMap()) ?>
                    </div>
                    <!--                    <div class="col-lg-4">-->
                    <!--                        --><? //= $form->field($model, 'gold_weight')->textInput() ?>
                    <!--                    </div>-->
                    <div class="col-lg-4">
                        <?= $form->field($model, 'suttle_weight')->textInput(['onblur' => 'rfClearVal(this)']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'lncl_loss_weight')->textInput(['onblur' => 'rfClearVal(this)']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'gold_loss')->textInput(['onblur' => 'rfClearVal(this)']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'gold_price')->textInput(['onblur' => 'rfClearVal(this)']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'pure_gold')->textInput(['onblur' => 'rfClearVal(this)']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'gold_amount')->textInput(['onblur' => 'rfClearVal(this)']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'factory_gold_weight')->textInput(['onblur' => 'rfClearVal(this)']) ?>
                    </div>
                </div>
                <!--                <div class="row">-->
                <!--                    <div class="with-border">-->
                <!--                        <h5 class="box-title" style="font-weight: bold">钻石信息</h5>-->
                <!--                    </div>-->
                <!--                    <div class="col-lg-4">-->
                <!--                        --><? //= $form->field($model, 'diamond_cert_id')->textInput() ?>
                <!--                    </div>-->
                <!--                    <div class="col-lg-4">-->
                <!--                        --><? //= $form->field($model, 'diamond_cert_type')->dropDownList($model->getDiamondCertTypeDrop($model), ['prompt' => '请选择']) ?>
                <!--                    </div>-->
                <!--                    <div class="col-lg-4">-->
                <!--                        --><? //= $form->field($model, 'diamond_carat')->textInput() ?>
                <!--                    </div>-->
                <!--                    <div class="col-lg-4">-->
                <!--                        --><? //= $form->field($model, 'diamond_shape')->dropDownList($model->getDiamondShapeDrop($model), ['prompt' => '请选择']) ?>
                <!--                    </div>-->
                <!--                    <div class="col-lg-4">-->
                <!--                        --><? //= $form->field($model, 'diamond_color')->dropDownList($model->getDiamondColorDrop($model), ['prompt' => '请选择']) ?>
                <!--                    </div>-->
                <!--                    <div class="col-lg-4">-->
                <!--                        --><? //= $form->field($model, 'diamond_clarity')->dropDownList($model->getDiamondClarityDrop($model), ['prompt' => '请选择']) ?>
                <!--                    </div>-->
                <!--                    <div class="col-lg-4">-->
                <!--                        --><? //= $form->field($model, 'diamond_cut')->dropDownList($model->getDiamondCutDrop($model), ['prompt' => '请选择']) ?>
                <!--                    </div>-->
                <!--                    <div class="col-lg-4">-->
                <!--                        --><? //= $form->field($model, 'diamond_polish')->dropDownList($model->getDiamondPolishDrop($model), ['prompt' => '请选择']) ?>
                <!--                    </div>-->
                <!--                    <div class="col-lg-4">-->
                <!--                        --><? //= $form->field($model, 'diamond_symmetry')->dropDownList($model->getDiamondSymmetryDrop($model), ['prompt' => '请选择']) ?>
                <!--                    </div>-->
                <!--                    <div class="col-lg-4">-->
                <!--                        --><? //= $form->field($model, 'diamond_fluorescence')->dropDownList($model->getDiamondFluorescenceDrop($model), ['prompt' => '请选择']) ?>
                <!--                    </div>-->
                <!--                    <div class="col-lg-4">-->
                <!--                        --><? //= $form->field($model, 'diamond_discount')->textInput() ?>
                <!--                    </div>-->
                <!--                </div>-->
                <?php if (!in_array($goods_type, [\addons\Warehouse\common\enums\GoodsTypeEnum::PlainGold])) { ?>
                    <div class="row">
                        <div class="with-border">
                            <h5 class="box-title" style="font-weight: bold">主石信息</h5>
                        </div>
                        <div class="col-sm-4">
                            <?= $form->field($model, 'main_pei_type')->radioList($model->getPeiShiWayMap()) ?>
                        </div>
                        <div class="col-lg-4">
                            <?= $form->field($model, 'main_stone_sn')->textInput() ?>
                        </div>
                        <div class="col-lg-4">
                            <?= $form->field($model, 'main_stone_type')->dropDownList($model->getMainStoneTypeDrop($model), ['prompt' => '请选择']) ?>
                        </div>
                        <div class="col-lg-4">
                            <?= $form->field($model, 'main_stone_num')->textInput(['onblur' => 'rfClearVal(this)']) ?>
                        </div>
                        <div class="col-lg-4">
                            <?= $form->field($model, 'main_stone_weight')->textInput(['onblur' => 'rfClearVal(this)']) ?>
                        </div>
                        <div class="col-lg-4">
                            <?= $form->field($model, 'main_stone_price')->textInput(['onblur' => 'rfClearVal(this)']) ?>
                        </div>
                        <div class="col-lg-4">
                            <?= $form->field($model, 'main_stone_amount')->textInput(['onblur' => 'rfClearVal(this)']) ?>
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
                            <?= $form->field($model, 'main_stone_polish')->dropDownList($model->getMainStonePolishDrop($model), ['prompt' => '请选择']) ?>
                        </div>
                        <div class="col-lg-4">
                            <?= $form->field($model, 'main_stone_symmetry')->dropDownList($model->getMainStoneSymmetryDrop($model), ['prompt' => '请选择']) ?>
                        </div>
                        <div class="col-lg-4">
                            <?= $form->field($model, 'main_stone_fluorescence')->dropDownList($model->getMainStoneFluorescenceDrop($model), ['prompt' => '请选择']) ?>
                        </div>
                        <div class="col-lg-4">
                            <?= $form->field($model, 'main_stone_colour')->dropDownList($model->getMainStoneColourDrop($model), ['prompt' => '请选择']) ?>
                        </div>
                        <!--                    <div class="col-lg-4">-->
                        <!--                        --><? //= $form->field($model, 'main_stone_size')->textInput() ?>
                        <!--                    </div>-->
                        <div class="col-lg-4">
                            <?= $form->field($model, 'main_cert_type')->dropDownList($model->getMainCertTypeDrop($model), ['prompt' => '请选择']) ?>
                        </div>
                        <div class="col-lg-4">
                            <?= $form->field($model, 'main_cert_id')->textInput() ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="with-border">
                            <h5 class="box-title" style="font-weight: bold">副石1信息</h5>
                        </div>
                        <div class="col-sm-4">
                            <?= $form->field($model, 'second_pei_type')->radioList($model->getPeiShiWayMap()) ?>
                        </div>
                        <div class="col-lg-4">
                            <?= $form->field($model, 'second_stone_type1')->dropDownList($model->getSecondStoneType1Drop($model), ['prompt' => '请选择']) ?>
                        </div>
                        <div class="col-lg-4">
                            <?= $form->field($model, 'second_stone_sn1')->textInput() ?>
                        </div>
                        <div class="col-lg-4">
                            <?= $form->field($model, 'second_stone_num1')->textInput(['onblur' => 'rfClearVal(this)']) ?>
                        </div>
                        <div class="col-lg-4">
                            <?= $form->field($model, 'second_stone_weight1')->textInput(['onblur' => 'rfClearVal(this)']) ?>
                        </div>
                        <div class="col-lg-4">
                            <?= $form->field($model, 'second_stone_price1')->textInput(['onblur' => 'rfClearVal(this)']) ?>
                        </div>
                        <div class="col-lg-4">
                            <?= $form->field($model, 'second_stone_amount1')->textInput(['onblur' => 'rfClearVal(this)']) ?>
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
                        <!--                        --><? //= $form->field($model, 'second_stone_size1')->textInput() ?>
                        <!--                    </div>-->
                        <!--                    <div class="col-lg-4">-->
                        <!--                        --><? //= $form->field($model, 'second_cert_id1')->textInput() ?>
                        <!--                    </div>-->
                        <!--                    <div class="col-lg-4">-->
                        <!--                        --><? //= $form->field($model, 'second_stone_type1')->dropDownList($model->getSecondStoneType1Drop($model), ['prompt' => '请选择']) ?>
                        <!--                    </div>-->
                        <!--                    <div class="col-lg-4">-->
                        <!--                        --><? //= $form->field($model, 'peishi_fee')->textInput() ?>
                        <!--                    </div>-->
                    </div>
                    <div class="row">
                        <div class="with-border">
                            <h5 class="box-title" style="font-weight: bold">副石2信息</h5>
                        </div>
                        <div class="col-sm-4">
                            <?= $form->field($model, 'second_pei_type2')->radioList($model->getPeiShiWayMap()) ?>
                        </div>
                        <div class="col-lg-4">
                            <?= $form->field($model, 'second_stone_type2')->dropDownList($model->getSecondStoneType2Drop($model), ['prompt' => '请选择']) ?>
                        </div>
                        <div class="col-lg-4">
                            <?= $form->field($model, 'second_stone_sn2')->textInput() ?>
                        </div>
                        <div class="col-lg-4">
                            <?= $form->field($model, 'second_stone_num2')->textInput(['onblur' => 'rfClearVal(this)']) ?>
                        </div>
                        <div class="col-lg-4">
                            <?= $form->field($model, 'second_stone_weight2')->textInput(['onblur' => 'rfClearVal(this)']) ?>
                        </div>
                        <div class="col-lg-4">
                            <?= $form->field($model, 'second_stone_color2')->dropDownList($model->getSecondStoneColor2Drop($model), ['prompt' => '请选择']) ?>
                        </div>
                        <div class="col-lg-4">
                            <?= $form->field($model, 'second_stone_clarity2')->dropDownList($model->getSecondStoneClarity2Drop($model), ['prompt' => '请选择']) ?>
                        </div>
                        <div class="col-lg-4">
                            <?= $form->field($model, 'second_stone_price2')->textInput(['onblur' => 'rfClearVal(this)']) ?>
                        </div>
                        <div class="col-lg-4">
                            <?= $form->field($model, 'second_stone_amount2')->textInput(['onblur' => 'rfClearVal(this)']) ?>
                        </div>
                        <!--                    <div class="col-lg-4">-->
                        <!--                        --><? //= $form->field($model, 'second_stone_shape2')->dropDownList($model->getSecondStoneShape2Drop($model), ['prompt' => '请选择']) ?>
                        <!--                    </div>-->
                        <!--                    <div class="col-lg-4">-->
                        <!--                        --><? //= $form->field($model, 'second_stone_color2')->dropDownList($model->getSecondStoneClarity2Drop($model), ['prompt' => '请选择']) ?>
                        <!--                    </div>-->
                        <!--                    <div class="col-lg-4">-->
                        <!--                        --><? //= $form->field($model, 'second_stone_colour2')->dropDownList($model->getSecondStoneColour2Drop($model), ['prompt' => '请选择']) ?>
                        <!--                    </div>-->
                        <!--                    <div class="col-lg-4">-->
                        <!--                        --><? //= $form->field($model, 'second_stone_size2')->textInput() ?>
                        <!--                    </div>-->
                        <!--                    <div class="col-lg-4">-->
                        <!--                        --><? //= $form->field($model, 'second_cert_id2')->textInput() ?>
                        <!--                    </div>-->
                        <!--                    <div class="col-lg-4">-->
                        <!--                        --><? //= $form->field($model, 'second_stone_type2')->dropDownList($model->getSecondStoneType2Drop($model), ['prompt' => '请选择']) ?>
                        <!--                    </div>-->
                    </div>
                    <div class="row">
                        <div class="with-border">
                            <h5 class="box-title" style="font-weight: bold">副石3信息</h5>
                        </div>
                        <div class="col-sm-4">
                            <?= $form->field($model, 'second_pei_type3')->radioList($model->getPeiShiWayMap()) ?>
                        </div>
                        <div class="col-lg-4">
                            <?= $form->field($model, 'second_stone_type3')->dropDownList($model->getSecondStoneType3Drop($model), ['prompt' => '请选择']) ?>
                        </div>
                        <div class="col-lg-4">
                            <?= $form->field($model, 'second_stone_sn3')->textInput() ?>
                        </div>
                        <div class="col-lg-4">
                            <?= $form->field($model, 'second_stone_num3')->textInput(['onblur' => 'rfClearVal(this)']) ?>
                        </div>
                        <div class="col-lg-4">
                            <?= $form->field($model, 'second_stone_weight3')->textInput(['onblur' => 'rfClearVal(this)']) ?>
                        </div>
                        <div class="col-lg-4">
                            <?= $form->field($model, 'second_stone_color3')->dropDownList($model->getSecondStoneColor3Drop($model), ['prompt' => '请选择']) ?>
                        </div>
                        <div class="col-lg-4">
                            <?= $form->field($model, 'second_stone_clarity3')->dropDownList($model->getSecondStoneClarity3Drop($model), ['prompt' => '请选择']) ?>
                        </div>
                        <div class="col-lg-4">
                            <?= $form->field($model, 'second_stone_price3')->textInput(['onblur' => 'rfClearVal(this)']) ?>
                        </div>
                        <div class="col-lg-4">
                            <?= $form->field($model, 'second_stone_amount3')->textInput(['onblur' => 'rfClearVal(this)']) ?>
                        </div>
                        <div class="col-lg-8">
                            <?= $form->field($model, 'stone_remark')->textInput() ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="with-border">
                            <h5 class="box-title" style="font-weight: bold">配件信息</h5>
                        </div>
                        <div class="col-sm-4">
                            <?= $form->field($model, 'parts_way')->radioList($model->getPeiJianWayMap()) ?>
                        </div>
                        <div class="col-lg-4">
                            <?= $form->field($model, 'parts_type')->dropDownList($model->getPartsTypeMap(), ['prompt' => '请选择']) ?>
                        </div>
                        <div class="col-lg-4">
                            <?= $form->field($model, 'parts_material')->dropDownList($model->getPartsMaterialMap(), ['prompt' => '请选择']) ?>
                        </div>
                        <div class="col-lg-4">
                            <?= $form->field($model, 'parts_num')->textInput(['onblur' => 'rfClearVal(this)']) ?>
                        </div>
                        <div class="col-lg-4">
                            <?= $form->field($model, 'parts_gold_weight')->textInput(['onblur' => 'rfClearVal(this)']) ?>
                        </div>
                        <div class="col-lg-4">
                            <?= $form->field($model, 'parts_price')->textInput(['onblur' => 'rfClearVal(this)']) ?>
                        </div>
                        <div class="col-lg-4">
                            <?= $form->field($model, 'parts_amount')->textInput(['onblur' => 'rfClearVal(this)']) ?>
                        </div>
                    </div>
                <?php } ?>
                <div class="row">
                    <div class="with-border">
                        <h5 class="box-title" style="font-weight: bold">工费及其它费用信息</h5>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'gong_fee')->textInput(['onblur' => 'rfClearVal(this)']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'piece_fee')->textInput(['onblur' => 'rfClearVal(this)']) ?>
                    </div>
                    <?php if (!in_array($goods_type, [\addons\Warehouse\common\enums\GoodsTypeEnum::PlainGold])) { ?>
                        <div class="col-lg-4">
                            <?= $form->field($model, 'peishi_weight')->textInput(['onblur' => 'rfClearVal(this)']) ?>
                        </div>
                        <div class="col-lg-4">
                            <?= $form->field($model, 'peishi_gong_fee')->textInput(['onblur' => 'rfClearVal(this)']) ?>
                        </div>
                        <div class="col-lg-4">
                            <?= $form->field($model, 'parts_fee')->textInput(['onblur' => 'rfClearVal(this)']) ?>
                        </div>
                        <div class="col-lg-4">
                            <?= $form->field($model, 'second_stone_fee1')->textInput(['onblur' => 'rfClearVal(this)']) ?>
                        </div>
                        <div class="col-lg-4">
                            <?= $form->field($model, 'second_stone_fee2')->textInput(['onblur' => 'rfClearVal(this)']) ?>
                        </div>
                        <div class="col-lg-4">
                            <?= $form->field($model, 'second_stone_fee3')->textInput(['onblur' => 'rfClearVal(this)']) ?>
                        </div>
                        <div class="col-lg-4">
                            <?= $form->field($model, 'xianqian_fee')->textInput(['onblur' => 'rfClearVal(this)']) ?>
                        </div>
                        <div class="col-lg-4">
                            <?= $form->field($model, 'bukou_fee')->textInput(['onblur' => 'rfClearVal(this)']) ?>
                        </div>
                    <?php }?>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'biaomiangongyi_fee')->textInput(['onblur' => 'rfClearVal(this)']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'fense_fee')->textInput(['onblur' => 'rfClearVal(this)']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'penlasha_fee')->textInput(['onblur' => 'rfClearVal(this)']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'lasha_fee')->textInput(['onblur' => 'rfClearVal(this)']) ?>
                    </div>

                    <div class="col-lg-4">
                        <?= $form->field($model, 'templet_fee')->textInput(['onblur' => 'rfClearVal(this)']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'tax_fee')->textInput(['onblur' => 'rfClearVal(this)']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'tax_amount')->textInput(['onblur' => 'rfClearVal(this)']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'cert_fee')->textInput(['onblur' => 'rfClearVal(this)']) ?>
                    </div>
                    <!--                    <div class="col-lg-4">-->
                    <!--                        --><? //= $form->field($model, 'extra_stone_fee')->textInput() ?>
                    <!--                    </div>-->
                    <div class="col-lg-4">
                        <?= $form->field($model, 'other_fee')->textInput(['onblur' => 'rfClearVal(this)']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'factory_cost')->textInput(['onblur' => 'rfClearVal(this)']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'cost_price')->textInput(['onblur' => 'rfClearVal(this)']) ?>
                    </div>
                </div>
                <?php if(\Yii::$app->user->identity->getId() == 1){?>
                    <div class="row">
                        <div class="with-border">
                            <h5 class="box-title" style="font-weight: bold">自动计算开关</h5>
                        </div>
                        <div class="col-sm-4">
                            <?= $form->field($model, 'auto_loss_weight')->radioList(\addons\Warehouse\common\enums\IsAutoCalculateEnum::getMap()) ?>
                        </div>
                        <div class="col-sm-4">
                            <?= $form->field($model, 'auto_gold_amount')->radioList(\addons\Warehouse\common\enums\IsAutoCalculateEnum::getMap()) ?>
                        </div>
                        <div class="col-sm-4">
                            <?= $form->field($model, 'auto_main_stone')->radioList(\addons\Warehouse\common\enums\IsAutoCalculateEnum::getMap()) ?>
                        </div>
                        <div class="col-sm-4">
                            <?= $form->field($model, 'auto_second_stone1')->radioList(\addons\Warehouse\common\enums\IsAutoCalculateEnum::getMap()) ?>
                        </div>
                        <div class="col-sm-4">
                            <?= $form->field($model, 'auto_second_stone2')->radioList(\addons\Warehouse\common\enums\IsAutoCalculateEnum::getMap()) ?>
                        </div>
                        <div class="col-sm-4">
                            <?= $form->field($model, 'auto_second_stone3')->radioList(\addons\Warehouse\common\enums\IsAutoCalculateEnum::getMap()) ?>
                        </div>
                        <div class="col-sm-4">
                            <?= $form->field($model, 'auto_parts_amount')->radioList(\addons\Warehouse\common\enums\IsAutoCalculateEnum::getMap()) ?>
                        </div>
                        <div class="col-sm-4">
                            <?= $form->field($model, 'auto_peishi_fee')->radioList(\addons\Warehouse\common\enums\IsAutoCalculateEnum::getMap()) ?>
                        </div>
                        <div class="col-sm-4">
                            <?= $form->field($model, 'auto_xianqian_fee')->radioList(\addons\Warehouse\common\enums\IsAutoCalculateEnum::getMap()) ?>
                        </div>
                        <div class="col-sm-4">
                            <?= $form->field($model, 'auto_tax_amount')->radioList(\addons\Warehouse\common\enums\IsAutoCalculateEnum::getMap()) ?>
                        </div>
                        <div class="col-sm-4">
                            <?= $form->field($model, 'auto_factory_cost')->radioList(\addons\Warehouse\common\enums\IsAutoCalculateEnum::getMap()) ?>
                        </div>
                        <div class="col-sm-4">
                            <?= $form->field($model, 'is_auto_price')->radioList(\addons\Warehouse\common\enums\IsAutoCalculateEnum::getMap()) ?>
                        </div>
                    </div>
                <?php }else{?>
                    <div class="row">
                        <div class="with-border">
                            <h5 class="box-title" style="font-weight: bold">自动计算开关</h5>
                        </div>
                        <div class="col-sm-4">
                            <?= $form->field($model, 'auto_loss_weight')->radioList(\addons\Warehouse\common\enums\IsAutoCalculateEnum::getMap(), ['onclick'=>'return false;']) ?>
                        </div>
                        <div class="col-sm-4">
                            <?= $form->field($model, 'auto_gold_amount')->radioList(\addons\Warehouse\common\enums\IsAutoCalculateEnum::getMap(), ['onclick'=>'return false;']) ?>
                        </div>
                        <div class="col-sm-4">
                            <?= $form->field($model, 'auto_main_stone')->radioList(\addons\Warehouse\common\enums\IsAutoCalculateEnum::getMap(), ['onclick'=>'return false;']) ?>
                        </div>
                        <div class="col-sm-4">
                            <?= $form->field($model, 'auto_second_stone1')->radioList(\addons\Warehouse\common\enums\IsAutoCalculateEnum::getMap(), ['onclick'=>'return false;']) ?>
                        </div>
                        <div class="col-sm-4">
                            <?= $form->field($model, 'auto_second_stone2')->radioList(\addons\Warehouse\common\enums\IsAutoCalculateEnum::getMap(), ['onclick'=>'return false;']) ?>
                        </div>
                        <div class="col-sm-4">
                            <?= $form->field($model, 'auto_second_stone3')->radioList(\addons\Warehouse\common\enums\IsAutoCalculateEnum::getMap(), ['onclick'=>'return false;']) ?>
                        </div>
                        <div class="col-sm-4">
                            <?= $form->field($model, 'auto_parts_amount')->radioList(\addons\Warehouse\common\enums\IsAutoCalculateEnum::getMap(), ['onclick'=>'return false;']) ?>
                        </div>
                        <div class="col-sm-4">
                            <?= $form->field($model, 'auto_peishi_fee')->radioList(\addons\Warehouse\common\enums\IsAutoCalculateEnum::getMap(), ['onclick'=>'return false;']) ?>
                        </div>
                        <div class="col-sm-4">
                            <?= $form->field($model, 'auto_xianqian_fee')->radioList(\addons\Warehouse\common\enums\IsAutoCalculateEnum::getMap(), ['onclick'=>'return false;']) ?>
                        </div>
                        <div class="col-sm-4">
                            <?= $form->field($model, 'auto_tax_amount')->radioList(\addons\Warehouse\common\enums\IsAutoCalculateEnum::getMap(), ['onclick'=>'return false;']) ?>
                        </div>
                        <div class="col-sm-4">
                            <?= $form->field($model, 'auto_factory_cost')->radioList(\addons\Warehouse\common\enums\IsAutoCalculateEnum::getMap(), ['onclick'=>'return false;']) ?>
                        </div>
                        <div class="col-sm-4">
                            <?= $form->field($model, 'is_auto_price')->radioList(\addons\Warehouse\common\enums\IsAutoCalculateEnum::getMap(), ['onclick'=>'return false;']) ?>
                        </div>
                    </div>
                <?php }?>
                <!-- ./box-body -->
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
<script type="text/javascript">
    function rfClearVal(obj) {
        var val = $(obj).val();
        if (val == "") {
            $(obj).val("0");
        }
    }
</script>
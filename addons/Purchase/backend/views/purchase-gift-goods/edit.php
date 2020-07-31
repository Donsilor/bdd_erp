<?php

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
                        <?= $form->field($model, 'goods_sn')->textInput() ?>
                    </div>
                    <div class="col-lg-3">
                        <?= $form->field($model, 'goods_name')->textInput() ?>
                    </div>
                    <div class="col-lg-3">
                        <?= $form->field($model, 'product_type_id')->dropDownList(Yii::$app->styleService->productType::getDropDown(),['disabled'=>true]) ?>
                    </div>
                    <div class="col-lg-3">
                        <?= $form->field($model, 'style_cate_id')->dropDownList(Yii::$app->styleService->productType::getDropDown(),['disabled'=>true]) ?>
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
                        <?= $form->field($model, 'cost_price')->textInput(['disabled'=>'disabled']) ?>
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
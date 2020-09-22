<?php

use yii\widgets\ActiveForm;
use common\helpers\Url;

?>
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <?php $form = ActiveForm::begin([]); ?>
            <div class="box-body" style="padding:20px 50px">
                <div class="row">
                    <?= $form->field($model, 'order_id')->hiddenInput()->label(false) ?>
                    <div class="col-lg-6">
                        <?= $form->field($model, 'goods_sn')->textInput(["placeholder"=>"如未填写，系统自动生成"]); ?>
                    </div>
                    <div class="col-lg-6">
                        <?= $form->field($model, 'goods_name')->textInput(); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <?= $form->field($model, 'style_cate_id')->dropDownList(Yii::$app->styleService->styleCate->getDropDown()) ?>
                    </div>
                    <div class="col-lg-6">
                        <?= $form->field($model, 'product_type_id')->dropDownList(Yii::$app->styleService->productType->getDropDown()) ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <?= $form->field($model, 'goods_size')->textInput(); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <?= $form->field($model, 'goods_price')->textInput(); ?>
                    </div>
                    <div class="col-lg-6">
                        <?= $form->field($model, 'cost_price')->textInput(); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <?php
                        $model->goods_image = $model->goods_image ? explode(',', $model->goods_image) : [];
                        ?>
                        <?= $form->field($model, 'goods_image')->widget(common\widgets\webuploader\Files::class, [
                            'config' => [
                                'pick' => [
                                    'multiple' => true,
                                ],
                                'style'=> 'width:50px'
                            ]
                        ]); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <?= $form->field($model, 'remark')->textArea(['options'=>['maxlength' => true]])?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>

<?php

use common\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model addons\style\common\models\Diamond */
/* @var $form yii\widgets\ActiveForm */

$this->title = Yii::t('goods_diamond', 'Diamond');
$this->params['breadcrumbs'][] = ['label' => Yii::t('goods_diamond', 'Diamonds'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">基本信息</h3>
            </div>
            <div class="box-body">
                <?php $form = ActiveForm::begin([
                    'fieldConfig' => [
                        'template' => "<div class='col-sm-1 text-right'>{label}</div><div class='col-sm-11'>{input}\n{hint}\n{error}</div>",
                    ],
                ]); ?>
                <div class="col-sm-12">
                    <?= $form->field($model, 'goods_sn')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($model, 'goods_image')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($model, 'goods_num')->textInput() ?>
                    <?= $form->field($model, 'cert_type')->dropDownList(\common\enums\DiamondEnum::$typeOptions) ?>
                    <?= $form->field($model, 'cert_id')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($model, 'market_price')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($model, 'sale_price')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($model, 'cost_price')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($model, 'carat')->textInput() ?>
                    <?= $form->field($model, 'clarity')->dropDownList(\common\enums\DiamondEnum::$clarityOptions) ?>
                    <?= $form->field($model, 'cut')->dropDownList(\common\enums\DiamondEnum::$cutOptions) ?>
                    <?= $form->field($model, 'color')->dropDownList(\common\enums\DiamondEnum::$colorOptions) ?>
                    <?= $form->field($model, 'shape')->dropDownList(\common\enums\DiamondEnum::$shapeOptions) ?>
                    <?= $form->field($model, 'depth_lv')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($model, 'table_lv')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($model, 'symmetry')->dropDownList(\common\enums\DiamondEnum::$symmetryOptions) ?>
                    <?= $form->field($model, 'polish')->dropDownList(\common\enums\DiamondEnum::$polishOptions) ?>
                    <?= $form->field($model, 'fluorescence')->dropDownList(\common\enums\DiamondEnum::$fluorescenceOptions) ?>
                    <?= $form->field($model, 'source_id')->dropDownList(['22'=>'dd']) ?>
                    <?= $form->field($model, 'source_discount')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($model, 'is_stock')->radioList(\common\enums\IsStockEnum::getMap()) ?>
                    <?= $form->field($model, 'status')->radioList(\common\enums\StatusEnum::getMap()) ?>

                </div>
                <div class="form-group">
                    <div class="col-sm-12 text-center">
                        <button class="btn btn-primary" type="submit">保存</button>
                        <span class="btn btn-white" onclick="history.go(-1)">返回</span>
                    </div>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>

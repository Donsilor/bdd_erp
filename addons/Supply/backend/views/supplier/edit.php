<?php

use common\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model addons\Supply\common\models\Supplier */
/* @var $form yii\widgets\ActiveForm */

$this->title = Yii::t('supplier', 'Supplier');
$this->params['breadcrumbs'][] = ['label' => Yii::t('supplier', 'Supply'), 'url' => ['index']];
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
                    <?= $form->field($model, 'supplier_name')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($model, 'supplier_code')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($model, 'business_scope')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($model, 'bank_name')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($model, 'bank_account_name')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($model, 'tax_no')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($model, 'business_address')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($model, 'contract_no')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($model, 'contactor')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($model, 'mobile')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($model, 'telephone')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($model, 'bdd_contactor')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($model, 'bdd_mobile')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($model, 'bdd_telephone')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($model, 'remark')->textArea(['maxlength' => true]) ?>
                    <?= $form->field($model, 'balance_type')->dropDownList(\common\enums\BalanceTypeEnum::getMap()) ?>

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

<?php

use common\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model addons\Style\common\models\Goods */
/* @var $form yii\widgets\ActiveForm */

$this->title = Yii::t('goods', 'Goods');
$this->params['breadcrumbs'][] = ['label' => Yii::t('goods', 'Goods'), 'url' => ['index']];
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
                        'template' => "<div class='col-sm-2 text-right'>{label}</div><div class='col-sm-10'>{input}\n{hint}\n{error}</div>",
                    ],
                ]); ?>
                <div class="col-sm-12">
                    <?= $form->field($model, 'style_id')->dropDownList([]) ?>
                    <?= $form->field($model, 'goods_sn')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($model, 'type_id')->dropDownList([]) ?>
                    <?= $form->field($model, 'goods_image')->widget(\common\widgets\webuploader\Files::class, [
                            'type' => 'images',
                            'theme' => 'default',
                            'themeConfig' => [],
                            'config' => [
                                // 可设置自己的上传地址, 不设置则默认地址
                                // 'server' => '',
                                'pick' => [
                                    'multiple' => false,
                                ],
                            ]
                    ]); ?>
                    <?= $form->field($model, 'merchant_id')->textInput() ?>
                    <?= $form->field($model, 'cat_id')->textInput() ?>
                    <?= $form->field($model, 'sale_price')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($model, 'market_price')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($model, 'promotion_price')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($model, 'promotion_type')->textInput() ?>
                    <?= $form->field($model, 'storage_alarm')->textInput() ?>
                    <?= $form->field($model, 'goods_clicks')->textInput() ?>
                    <?= $form->field($model, 'goods_collects')->textInput() ?>
                    <?= $form->field($model, 'goods_comments')->textInput() ?>
                    <?= $form->field($model, 'goods_stars')->textInput() ?>
                    <?= $form->field($model, 'goods_storage')->textInput() ?>
                    <?= $form->field($model, 'status')->checkboxList(\common\enums\StatusEnum::getMap()) ?>
                    <?= $form->field($model, 'verify_status')->textInput() ?>
                    <?= $form->field($model, 'verify_remark')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($model, 'created_at')->widget(kartik\date\DatePicker::class, [
                        'language' => 'zh-CN',
                        'layout'=>'{picker}{input}',
                        'pluginOptions' => [
                            'format' => 'yyyy-mm-dd',
                            'todayHighlight' => true, // 今日高亮
                            'autoclose' => true, // 选择后自动关闭
                            'todayBtn' => true, // 今日按钮显示
                        ],
                        'options'=>[
                            'class' => 'form-control no_bor',
                        ]
                    ]) ?>
                    <?= $form->field($model, 'updated_at')->widget(kartik\date\DatePicker::class, [
                        'language' => 'zh-CN',
                        'layout'=>'{picker}{input}',
                        'pluginOptions' => [
                            'format' => 'yyyy-mm-dd',
                            'todayHighlight' => true, // 今日高亮
                            'autoclose' => true, // 选择后自动关闭
                            'todayBtn' => true, // 今日按钮显示
                        ],
                        'options'=>[
                            'class' => 'form-control no_bor',
                        ]
                    ]) ?>
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

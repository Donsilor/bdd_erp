<?php
use yii\widgets\ActiveForm;
use common\helpers\Url;

$this->title = '创建其它出库单';
$this->params['breadcrumbs'][] = ['label' => 'Curd', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <?php $form = ActiveForm::begin([]); ?>
            <div class="box-body" style="padding:20px 50px">
                    <?= $form->field($model, 'bill_no')->textInput(['disabled'=>true, "placeholder"=>"系统自动生成"])?>
                    <?= $form->field($model, 'delivery_type')->widget(\kartik\select2\Select2::class, [
                        'data' => \addons\Warehouse\common\enums\DeliveryTypeEnum::getMap(),
                        'options' => ['placeholder' => '请选择'],
                        'pluginOptions' => [
                            'allowClear' => false
                        ],
                    ]);?>        
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'channel_id')->widget(\kartik\select2\Select2::class, [
                                'data' => \Yii::$app->salesService->saleChannel->getDropDown(),
                                'options' => ['placeholder' => '请选择'],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ],
                            ]);?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'salesman_id')->widget(kartik\select2\Select2::class, [
                                'data' => Yii::$app->services->backendMember->getDropDown(),
                                'options' => [
                                    'placeholder' => '请选择',
                                    'value' => $model->salesman_id??'',
                                ],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ],
                            ]);?>
                        </div>
                    </div>
                    <?= $form->field($model, 'order_sn')->textInput() ?>
                    <?= $form->field($model, 'remark')->textArea(); ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
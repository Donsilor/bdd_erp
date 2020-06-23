<?php
use yii\widgets\ActiveForm;
use common\helpers\Html;
use common\helpers\Url;

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
                <div class="col-lg-4">
                    <?= $form->field($model, 'material_type')->dropDownList($model->getMaterialTypeMap(),['prompt'=>'请选择']) ?>
                </div>
                <div class="col-lg-4">
                    <?= $form->field($model, 'goods_name')->textInput() ?>
                </div>
              <div class="col-lg-4">
                  <?= $form->field($model, 'goods_sn')->widget(\kartik\select2\Select2::class, [
                      'data' => Yii::$app->styleService->gold->getDropDown(),
                      'options' => ['placeholder' => '请选择'],
                      'pluginOptions' => [
                          'allowClear' => false
                      ],
                  ]);?>
              </div>
              </div>
			   <div class="row">
                   <div class="col-lg-4">
                       <?= $form->field($model, 'goods_weight')->textInput() ?>
                   </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'gold_price')->textInput() ?>
                    </div>
                   <div class="col-lg-4">
                       <?= $form->field($model, 'cost_price')->textInput(['disabled'=>'disabled']) ?>
                   </div>
               </div>
                <div class="row">
                    <div class="col-lg-4">
                        <?= $form->field($model, 'remark')->textarea() ?>
                    </div>
                </div>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
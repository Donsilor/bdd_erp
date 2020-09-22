<?php

use common\helpers\Html;
use common\widgets\webuploader\Files;
use kartik\date\DatePicker;
use yii\widgets\ActiveForm;
use common\helpers\Url;
//use common\enums\AreaEnum;

/* @var $this yii\web\View */
/* @var $model addons\Sales\common\models\Customer */
/* @var $form yii\widgets\ActiveForm */

$this->title = $model->id?\Yii::t('customer', '编辑订单'):\Yii::t('customer', '新增订单');
$this->params['breadcrumbs'][] = ['label' => \Yii::t('customer', 'Sales'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<?php $form = ActiveForm::begin([
         'id' => $model->formName(),
        'enableAjaxValidation' => true,
        'validationUrl' => Url::to(['edit', 'id' => $model['id']]),
        'fieldConfig' => [
            //'template' => "{label}{input}{hint}",
        ],
]); ?>
<div class="box-body nav-tabs-custom">
     <h2 class="page-header"><?php echo $this->title;?></h2>
      <?php $tab_list = [0=>'全部',1=>'基本信息',2=>'客户信息',3=>'供应商'];?>
     <?php echo Html::tab($tab_list,0,'tab')?>
     <div class="tab-content">
           <div class="row nav-tabs-custom tab-pane tab0 active" id="tab_1">
                <ul class="nav nav-tabs pull-right">
                  <li class="pull-left header"><i class="fa fa-th"></i> <?= $tab_list[1]??''?></li>
                </ul>
                <div class="box-body col-lg-12" style="padding-left:30px">
                    <div class="row">
                        <div class="col-lg-4">
                            <?= $form->field($model, 'order_sn')->textInput(['disabled'=>true, "placeholder"=>"系统自动生成"])?>
                        </div>
                        <div class="col-lg-4">
                            <?= $form->field($model, 'channel_id')->widget(\kartik\select2\Select2::class, [
                                'data' => \Yii::$app->salesService->saleChannel->getDropDown(),
                                'options' => ['placeholder' => '请选择'],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ],
                            ]);?>
                        </div>
                        <div class="col-lg-4">
                            <?= $form->field($model, 'warehouse_id')->widget(\kartik\select2\Select2::class, [
                                'data' => \Yii::$app->warehouseService->warehouse->getDropDown(),
                                'options' => ['placeholder' => '请选择'],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ],
                            ]);?>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-lg-4">
                            <?= $form->field($model, 'order_from')->dropDownList(\addons\Gdzb\common\enums\OrderFromEnum::getMap(),['prompt'=>'请选择']) ?>
                        </div>
                        <div class="col-lg-4">
                            <?= $form->field($model, 'collect_type')->dropDownList(\addons\Gdzb\common\enums\PayTypeEnum::getMap(),['prompt'=>'请选择']) ?>
                        </div>
                        <div class="col-lg-4">
                            <?= $form->field($model, 'collect_no')->textInput()?>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-lg-4">
                            <?= $form->field($model, 'is_invoice')->radioList(\common\enums\ConfirmEnum::getMap(),['prompt'=>'请选择']) ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-6">
                            <?= $form->field($model, 'remark')->textarea(['maxlength' => true]) ?>
                        </div>
                    </div>
                </div>
           </div>
          <div class="row nav-tabs-custom tab-pane tab0 active" id="tab_2">
                <ul class="nav nav-tabs pull-right">
                  <li class="pull-left header"><i class="fa fa-th"></i> <?= $tab_list[2]??''?></li>
                </ul>
                <div class="box-body col-lg-12" style="padding-left:30px">
                    <div class="row">
                        <div class="col-lg-4">
                            <?= $form->field($model, 'customer_name')->textInput()?>
                        </div>
                        <div class="col-lg-4">
                            <?= $form->field($model, 'customer_mobile')->textInput(['maxlength' => true]) ?>
                        </div>
                        <div class="col-lg-4">
                            <?= $form->field($model, 'customer_weixin')->textInput(['maxlength' => true]) ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-9">
                            <?= \common\widgets\country\Country::widget([
                                'form' => $form,
                                'model' => $model,
                                'countryName' => 'country_id',
                                'provinceName' => 'province_id',// 省字段名
                                'cityName' => 'city_id',// 市字段名
                                //'areaName' => 'area_id',// 区字段名
                                'template' => 'short' //合并为一行显示
                            ]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <?= $form->field($model, 'address')->textInput(['maxlength' => true]) ?>
                        </div>
                    </div>

                </div>
             <!-- ./box-body -->
          </div>
          <div class="row nav-tabs-custom tab-pane tab0 active" id="tab_3">
                <ul class="nav nav-tabs pull-right">
                  <li class="pull-left header"><i class="fa fa-th"></i> <?= $tab_list[3]??''?></li>
                </ul>
                <div class="box-body col-lg-12" style="padding-left:30px">
                    <div class="row">
                        <div class="col-lg-4">
                            <?= $form->field($model, 'supplier_id')->widget(\kartik\select2\Select2::class, [
                                'data' => \Yii::$app->gdzbService->supplier->getDropDown(),
                                'options' => ['placeholder' => '请选择'],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ],
                            ]);?>
                        </div>
                    </div>
                </div>
              <!-- ./box-body -->
          </div>

      <!-- ./row -->
    </div>
    <div class="modal-footer">
        <div class="col-sm-12 text-center">
            <button class="btn btn-primary" type="submit">保存</button>
            <span class="btn btn-white" onclick="history.go(-1)">返回</span>
        </div>
	</div>
</div>

<?php ActiveForm::end(); ?>

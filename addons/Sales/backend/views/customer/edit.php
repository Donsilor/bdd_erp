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

$this->title = $model->id?\Yii::t('customer', '编辑客户'):\Yii::t('customer', '新增客户');
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
      <?php $tab_list = [0=>'全部',1=>'基本信息',2=>'联系方式',3=>'客户地址',4=>'发票信息'];?>
     <?php echo Html::tab($tab_list,0,'tab')?>
     <div class="tab-content">
           <div class="row nav-tabs-custom tab-pane tab0 active" id="tab_1">
                <ul class="nav nav-tabs pull-right">
                  <li class="pull-left header"><i class="fa fa-th"></i> <?= $tab_list[1]??''?></li>
                </ul>
                <div class="box-body col-lg-12" style="padding-left:30px">
                    <div class="row">
                        <!--<div class="col-lg-3">
                            <?= $form->field($model, 'firstname')->textInput(['maxlength' => true]) ?>
                        </div>
                        <div class="col-lg-3">
                            <?= $form->field($model, 'lastname')->textInput(['maxlength' => true]) ?>
                        </div>-->
                        <div class="col-lg-3">
                            <?= $form->field($model, 'customer_no')->textInput(['disabled'=>true, "placeholder"=>"系统自动生成"])?>
                        </div>
                        <div class="col-lg-3">
                            <?= $form->field($model, 'realname')->textInput(['maxlength' => true]) ?>
                        </div>
                        <div class="col-lg-3">
                            <?= $form->field($model, 'channel_id')->widget(\kartik\select2\Select2::class, [
                                'data' => \Yii::$app->salesService->saleChannel->getDropDown(),
                                'options' => ['placeholder' => '请选择'],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ],
                            ]);?>
                        </div>
                        <div class="col-lg-3">
                            <?= $form->field($model, 'source_id')->widget(\kartik\select2\Select2::class, [
                                'data' => \Yii::$app->salesService->sources->getDropDown(),
                                'options' => ['placeholder' => '请选择'],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ],
                            ]);?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-3">
                            <?= $form->field($model, 'birthday')->widget(DatePicker::class, [
                                'language' => 'zh-CN',
                                'options' => [
                                    'value' => $model->birthday??'',
                                ],
                                'pluginOptions' => [
                                    'format' => 'yyyy-mm-dd',
                                    'todayHighlight' => true,//今日高亮
                                    'autoclose' => true,//选择后自动关闭
                                    'todayBtn' => true,//今日按钮显示
                                ]
                            ]);?>
                        </div>
                        <div class="col-lg-3">
                            <?= $form->field($model, 'gender')->radioList(\common\enums\GenderEnum::getMap()) ?>
                        </div>
                        <div class="col-lg-3">
                            <?= $form->field($model, 'marriage')->radioList(\addons\Sales\common\enums\MarriageEnum::getMap()) ?>
                        </div>
                        <div class="col-lg-3">
                            <?= $form->field($model, 'level')->radioList(\addons\Sales\common\enums\LevelEnum::getMap()) ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-3">
                            <?= $form->field($model, 'age')->textInput(['maxlength' => true]) ?>
                        </div>
                        <div class="col-lg-3">
                            <?= $form->field($model, 'language')->widget(\kartik\select2\Select2::class, [
                                'data' => \common\enums\LanguageEnum::getMap(),
                                'options' => ['placeholder' => '请选择'],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ],
                            ]);?>
                        </div>
                        <div class="col-lg-3">
                            <?= $form->field($model, 'currency')->widget(\kartik\select2\Select2::class, [
                                'data' => \common\enums\CurrencyEnum::getMap(),
                                'options' => ['placeholder' => '请选择'],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ],
                            ]);?>
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
                        <div class="col-lg-3">
                            <?= $form->field($model, 'mobile')->textInput()->label("手机号码[<sapn style=\"color:red;\">非国际批发必填</sapn>]")->hint("当客户归属渠道为[非国际批发]时必填")?>
                        </div>
                        <div class="col-lg-3">
                            <?= $form->field($model, 'home_phone')->textInput(['maxlength' => true]) ?>
                        </div>
                        <div class="col-lg-3">
                            <?= $form->field($model, 'email')->textInput()->label("邮箱[<sapn style=\"color:red;\">国际批发必填</sapn>]")->hint("当客户归属渠道为[国际批发]时必填")?>
                        </div>
                        <div class="col-lg-3">
                            <?= $form->field($model, 'qq')->textInput(['maxlength' => true]) ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-3">
                            <?= $form->field($model, 'google_account')->textInput(['maxlength' => true]) ?>
                        </div>
                        <div class="col-lg-3">
                            <?= $form->field($model, 'facebook_account')->textInput(['maxlength' => true]) ?>
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
         <div class="row nav-tabs-custom tab-pane tab0 active" id="tab_4">
             <ul class="nav nav-tabs pull-right">
                 <li class="pull-left header"><i class="fa fa-th"></i> <?= $tab_list[4]??''?></li>
             </ul>
             <div class="box-body col-lg-12" style="padding-left:30px">
                 <div class="row">
                     <div class="col-lg-3">
                         <?= $form->field($model, 'is_invoice')->radioList(\common\enums\ConfirmEnum::getMap()) ?>
                     </div>
                     <div class="col-lg-3">
                         <?= $form->field($model, 'invoice_type')->radioList(\addons\Sales\common\enums\InvoiceTypeEnum::getMap()) ?>
                     </div>
                     <div class="col-lg-3">
                         <?= $form->field($model, 'invoice_title_type')->radioList(\addons\Sales\common\enums\InvoiceTitleTypeEnum::getMap()) ?>
                     </div>
                 </div>
                 <div class="row">
                     <div class="col-lg-3">
                         <?= $form->field($model, 'invoice_title')->textInput(['maxlength' => true]) ?>
                     </div>
                     <div class="col-lg-3">
                         <?= $form->field($model, 'invoice_tax')->textInput(['maxlength' => true]) ?>
                     </div>
                     <div class="col-lg-3">
                         <?= $form->field($model, 'invoice_email')->textInput(['maxlength' => true]) ?>
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

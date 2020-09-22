<?php

use common\enums\SettlementWayEnum;
use common\helpers\Html;
use common\widgets\webuploader\Files;
use yii\widgets\ActiveForm;
use common\helpers\Url;
//use common\enums\AreaEnum;

/* @var $this yii\web\View */
/* @var $model addons\Supply\common\models\Supplier */
/* @var $form yii\widgets\ActiveForm */
$id = $model->id;
if(empty($id)){
    $this->title = Yii::t('supplier', '新增供应商');
}else{
    $this->title = Yii::t('supplier', '编辑供应商');
}
$this->params['breadcrumbs'][] = ['label' => Yii::t('supplier', 'Supply'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
//
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
      <?php $tab_list = [0=>'全部',1=>'基本信息',2=>'付款信息',3=>'联系信息'];?>
     <?php echo Html::tab($tab_list,0,'tab')?>
     <div class="tab-content">     
           <div class="row nav-tabs-custom tab-pane tab0 active" id="tab_1">
                <ul class="nav nav-tabs pull-right">
                  <li class="pull-left header"><i class="fa fa-th"></i> <?= $tab_list[1]??''?></li>
                </ul>
                <div class="box-body col-lg-9" style="padding-left:30px">
                    <div class="row">
                        <div class="col-lg-4">
                            <?= $form->field($model, 'supplier_code')->textInput(['disabled'=>true, "placeholder"=>"系统自动生成"])?>
                        </div>
                        <div class="col-lg-4">
                            <?= $form->field($model, 'level')->dropDownList(\addons\Gdzb\common\enums\GradeEnum::getMap(),['prompt'=>'请选择']) ?>
                        </div>
                        <div class="col-lg-4">
                            <?= $form->field($model, 'type')->dropDownList(\addons\Gdzb\common\enums\TypeEnum::getMap(),['prompt'=>'请选择']) ?>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-lg-4">
                            <?= $form->field($model, 'source_id')->dropDownList(\addons\Gdzb\common\enums\SupplierSourceEnum::getMap(),['prompt'=>'请选择']) ?>
                        </div>
                        <div class="col-lg-4">
                            <?= $form->field($model, 'channel_id')->widget(\kartik\select2\Select2::class, [
                                'data' => Yii::$app->salesService->saleChannel->getDropDown(),
                                'options' => ['placeholder' => '请选择',],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ],
                            ]);?>
                        </div>
                    </div>
                    <?php $model->business_scope = !empty($model->business_scope)?array_filter(explode(',', $model->business_scope)):null;?>
                    <?= $form->field($model, 'business_scope')->checkboxList(\addons\Gdzb\common\enums\BusinessScopeEnum::getMap()) ?>
                    <!-- ./nav-tabs-custom -->
                </div>
            <!-- ./box-body -->
          </div>
          <div class="row nav-tabs-custom tab-pane tab0 active" id="tab_2">
                <ul class="nav nav-tabs pull-right">
                  <li class="pull-left header"><i class="fa fa-th"></i> <?= $tab_list[2]??''?></li>
                </ul>
              <div class="box-body col-lg-12" style="padding-left:30px">
                  <div class="row">
                      <div class="col-lg-4">
                          <?= $form->field($model, 'bank_name')->textInput()?>
                      </div>
                      <div class="col-lg-4">
                          <?= $form->field($model, 'bank_account')->textInput()?>
                      </div>
                      <div class="col-lg-4">
                          <?= $form->field($model, 'bank_account_name')->textInput()?>
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
                            <?= $form->field($model, 'contactor')->textInput()?>
                        </div>
                        <div class="col-lg-4">
                            <?= $form->field($model, 'mobile')->textInput()?>
                        </div>
                        <div class="col-lg-4">
                            <?= $form->field($model, 'wechat')->textInput()?>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-lg-4">
                            <?= $form->field($model, 'supplier_name')->textInput()?>
                        </div>
                        <div class="col-lg-4">
                            <?= $form->field($model, 'address')->textInput(['maxlength' => true]) ?>
                        </div>
                        <div class="col-lg-4">
                            <?= $form->field($model, 'follower_id')->widget(kartik\select2\Select2::class, [
                                'data' => Yii::$app->services->backendMember->getDropDown(),
                                'options' => ['placeholder' => '请选择'],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ],
                            ]);?>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <?= $form->field($model, 'remark')->textArea(['maxlength' => true]) ?>
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

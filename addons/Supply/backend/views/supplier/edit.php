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
      <?php $tab_list = [0=>'全部',1=>'基本信息',2=>'证件信息',3=>'联系信息',4=>'附件上传'];?>
     <?php echo Html::tab($tab_list,0,'tab')?>
     <div class="tab-content">     
           <div class="row nav-tabs-custom tab-pane tab0 active" id="tab_1">
                <ul class="nav nav-tabs pull-right">
                  <li class="pull-left header"><i class="fa fa-th"></i> <?= $tab_list[1]??''?></li>
                </ul>
                <div class="box-body col-lg-9" style="padding-left:30px">
                    <div class="row">
                        <div class="col-lg-4">
                            <?= $form->field($model, 'supplier_name')->textInput(['maxlength' => true]) ?>
                        </div>
                        <div class="col-lg-4">
                            <?= $form->field($model, 'supplier_tag')->textInput(['maxlength' => true,'readonly'=>$model->audit_status == \common\enums\AuditStatusEnum::PASS]) ?>
                        </div>
                        <div class="col-lg-4">
                            <?= $form->field($model, 'balance_type')->dropDownList(\addons\Supply\common\enums\BalanceTypeEnum::getMap()) ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4">
                            <?= $form->field($model, 'goods_type')->dropDownList(\addons\Supply\common\enums\GoodsTypeEnum::getMap(),['prompt'=>'请选择']) ?>
                        </div>
                        <div class="col-lg-4">
                            <?= $form->field($model, 'supplier_status')->dropDownList(\addons\Supply\common\enums\SupplierStatusEnum::getMap(),['prompt'=>'请选择']) ?>
                        </div>
                    </div>
                    <?php $model->business_scope = !empty($model->business_scope)?array_filter(explode(',', $model->business_scope)):null;?>
                    <?= $form->field($model, 'business_scope')->checkboxList(\addons\Supply\common\enums\BusinessScopeEnum::getMap()) ?>
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
                      <div class="col-lg-3">
                          <?= $form->field($model, 'bank_name')->textInput(['maxlength' => true]) ?>
                      </div>
                      <div class="col-lg-3">
                          <?= $form->field($model, 'bank_account_name')->textInput(['maxlength' => true]) ?>
                      </div>
                      <div class="col-lg-3">
                          <?= $form->field($model, 'bank_account')->textInput(['maxlength' => true]) ?>
                      </div>
                      <div class="col-lg-3">
                          <?= $form->field($model, 'business_no')->textInput(['maxlength' => true]) ?>
                      </div>
                  </div>
                  <div class="row">
                      <div class="col-lg-3">
                            <?php $model->pay_type = !empty($model->pay_type)?array_filter(explode(',', $model->pay_type)):null;?>
                            <?= $form->field($model, 'pay_type')->checkboxList(\addons\Supply\common\enums\SettlementWayEnum::getMap()) ?>
                      </div>
                      <div class="col-lg-3">
                            <?= $form->field($model, 'tax_no')->textInput(['maxlength' => true]) ?>
                      </div>
                      <div class="col-lg-6">
                            <?= $form->field($model, 'business_address')->textInput(['maxlength' => true]) ?>
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
                        <div class="col-lg-3">
                            <?= $form->field($model, 'contactor')->textInput(['maxlength' => true]) ?>
                        </div>
                        <div class="col-lg-3">
                            <?= $form->field($model, 'mobile')->textInput(['maxlength' => true]) ?>
                        </div>
                        <div class="col-lg-3">
                            <?= $form->field($model, 'telephone')->textInput(['maxlength' => true]) ?>
                        </div>
                        <div class="col-lg-3">
                            <?= $form->field($model, 'bdd_contactor')->textInput(['maxlength' => true]) ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-3">
                            <?= $form->field($model, 'bdd_mobile')->textInput(['maxlength' => true]) ?>
                        </div>
                        <div class="col-lg-3">
                            <?= $form->field($model, 'bdd_telephone')->textInput(['maxlength' => true]) ?>
                        </div>
                        <div class="col-lg-6">
                            <?= $form->field($model, 'address')->textInput(['maxlength' => true]) ?>
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
        <div class="row nav-tabs-custom tab-pane tab0 active" id="tab_4">
            <ul class="nav nav-tabs pull-right">
              <li class="pull-left header"><i class="fa fa-th"></i> <?= $tab_list[4]??''?></li>
            </ul>
            <div class="box-body col-lg-12" style="padding-left:30px">
                <div class="row">
                    <div class="col-lg-4">
                        <?= $form->field($model, 'contract_file')->widget(common\widgets\webuploader\Files::class, [
                            'type' => 'files',
                            'config' => [
                                'pick' => [
                                    'multiple' => false,
                                ],
                                'formData' => [
                                // 'drive' => 'local',// 默认本地 支持 qiniu/oss 上传
                                ],
                            ]
                        ]); ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'business_file')->widget(common\widgets\webuploader\Files::class, [
                            'config' => [
                                'pick' => [
                                    'multiple' => false,
                                ],
                            ]
                        ]); ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'tax_file')->widget(common\widgets\webuploader\Files::class, [
                            'config' => [
                                'pick' => [
                                    'multiple' => false,
                                ],
                            ]
                        ]); ?>
                    </div>
                </div>
            </div>
            <div class="box-body col-lg-12" style="padding-left:30px">
                <div class="row">
                    <div class="col-lg-4">
                        <?= $form->field($model, 'structure_cert')->widget(common\widgets\webuploader\Files::class, [
                            'type' => 'files',
                            'config' => [
                                'pick' => [
                                    'multiple' => false,
                                ],
                                'formData' => [
                                    // 'drive' => 'local',// 默认本地 支持 qiniu/oss 上传
                                ],
                            ]
                        ]); ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'production_licence')->widget(common\widgets\webuploader\Files::class, [
                            'type' => 'files',
                            'config' => [
                                'pick' => [
                                    'multiple' => false,
                                ],
                                'formData' => [
                                    // 'drive' => 'local',// 默认本地 支持 qiniu/oss 上传
                                ],
                            ]
                        ]); ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'taxpayer_cert')->widget(common\widgets\webuploader\Files::class, [
                            'type' => 'files',
                            'config' => [
                                'pick' => [
                                    'multiple' => false,
                                ],
                                'formData' => [
                                    // 'drive' => 'local',// 默认本地 支持 qiniu/oss 上传
                                ],
                            ]
                        ]); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4">
                        <?= $form->field($model, 'account_licence')->widget(common\widgets\webuploader\Files::class, [
                            'type' => 'files',
                            'config' => [
                                'pick' => [
                                    'multiple' => false,
                                ],
                                'formData' => [
                                    // 'drive' => 'local',// 默认本地 支持 qiniu/oss 上传
                                ],
                            ]
                        ]); ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'insure_cert')->widget(common\widgets\webuploader\Files::class, [
                            'type' => 'files',
                            'config' => [
                                'pick' => [
                                    'multiple' => false,
                                ],
                                'formData' => [
                                    // 'drive' => 'local',// 默认本地 支持 qiniu/oss 上传
                                ],
                            ]
                        ]); ?>
                    </div>
                </div>
            </div>
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

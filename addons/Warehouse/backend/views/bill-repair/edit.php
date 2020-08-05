<?php

use addons\Warehouse\common\enums\RepairActEnum;
use addons\Warehouse\common\enums\RepairTypeEnum;
use common\enums\BusinessScopeEnum;
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
    $this->title = Yii::t('supplier', '新增维修出库单');
}else{
    $this->title = Yii::t('supplier', '编辑维修出库单');
}
$this->params['breadcrumbs'][] = ['label' => Yii::t('warehouse_bill', 'repair'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
//
?>
<?php $form = ActiveForm::begin([
         'id' => $model->formName(),
        'enableAjaxValidation' => true,
        'validationUrl' => Url::to(['edit', 'id' => $model->id]),
        'fieldConfig' => [
            'template' => "{label}{input}{hint}",
        ],
]); ?>
<div class="box-body nav-tabs-custom">
     <h2 class="page-header"><?php echo $this->title;?></h2>
     <?php //echo Html::tab($tab_list,0,'tab')?>
     <div class="tab-content">     
       <div class="row nav-tabs-custom tab-pane tab0 active" id="tab_1">
            <ul class="nav nav-tabs pull-right">
              <li class="pull-left header"><i class="fa fa-th"></i></li>
            </ul>
            <div class="box-body col-lg-12" style="padding-left:30px">
                <div class="row">
                    <div class="col-lg-3">
                        <?= $form->field($model, 'repair_no')->textInput(['disabled'=>true, "placeholder"=>"系统自动生成"])?>
                    </div>
                    <div class="col-lg-3">
                        <?= $form->field($model, 'supplier_id')->widget(\kartik\select2\Select2::class, [
                            'data' => \Yii::$app->supplyService->supplier->getDropDown(),
                            'options' => ['placeholder' => '请选择'],
                            'pluginOptions' => [
                                'allowClear' => false
                            ],
                        ]);?>
                    </div>
                    <div class="col-lg-3">
                        <?= $form->field($model, 'repair_type')->dropDownList(\addons\Warehouse\common\enums\RepairTypeEnum::getMap(), ['prompt'=>'请选择'])?>
                    </div>
                    <div class="col-lg-3">
                        <?= $form->field($model, 'goods_id')->textInput(['maxlength' => true]) ?>
                    </div>
                </div>
                <!-- ./nav-tabs-custom -->
                <div class="row">
                    <div class="col-lg-3">
                        <?= $form->field($model, 'produce_sn')->textInput(['maxlength' => true]) ?>
                    </div>
                    <div class="col-lg-3">
                        <?= $form->field($model, 'bill_m_no')->textInput(['maxlength' => true]) ?>
                    </div>
                    <div class="col-lg-3">
                        <?= $form->field($model, 'order_sn')->textInput(['maxlength' => true]) ?>
                    </div>
                    <div class="col-lg-3">
                        <?= $form->field($model, 'consignee')->textInput(['maxlength' => true]) ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-9">
                        <?php $model->repair_act = !empty($model->repair_act)?explode(',', $model->repair_act):null;?>
                        <?= $form->field($model, 'repair_act')->checkboxList(addons\Warehouse\common\enums\RepairActEnum::getMap()) ?>
                    </div>
                    <div class="col-lg-3">
                        <?= $form->field($model, 'repair_price')->textInput(['maxlength' => true]) ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <?= $form->field($model, 'remark')->textArea(['maxlength' => true]) ?>
                    </div>
                </div>
                <!-- ./nav-tabs-custom -->
            </div>
        <!-- ./box-body -->
      </div>
     </div>
    <div class="modal-footer">
        <div class="col-sm-12 text-center">
            <button class="btn btn-primary" type="submit">保存</button>
            <span class="btn btn-white" onclick="history.go(-1)">返回</span>
        </div>
	</div>
</div>

<?php ActiveForm::end(); ?>

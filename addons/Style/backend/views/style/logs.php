<?php

use common\helpers\Html;
use yii\widgets\ActiveForm;
use common\widgets\langbox\LangBox;
use yii\base\Widget;

use common\helpers\Url;
use common\enums\StatusEnum;
use common\helpers\AmountHelper;
use common\enums\AreaEnum;
use addons\Style\common\models\Goods;

/* @var $this yii\web\View */
/* @var $model addons\Style\common\models\Style */
/* @var $form yii\widgets\ActiveForm */

$this->title = Yii::t('goods', 'Style');
$this->params['breadcrumbs'][] = ['label' => Yii::t('goods', 'Styles'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<?php $form = ActiveForm::begin([
        'id' => $model->formName(),
        'enableAjaxValidation' => true,
        'validationUrl' => Url::to(['ajax-edit', 'id' => $model->id]),       
]); ?>
<div class="box-body nav-tabs-custom">
     <h2 class="page-header">款式发布</h2>
     <?php echo Html::menuTab($tabList,$tab)?>
     <div class="tab-content">     
       <div class="row nav-tabs-custom tab-pane tab0 active">
            <ul class="nav nav-tabs pull-right">
              <li class="pull-left header"><i class="fa fa-th"></i> <?= $tabList[$tab]['name']??'';?></li>
            </ul>
            <div class="box-body col-sm-10" style="margin-left:9px">
       			<div class="row">
                    <div class="col-lg-6"><?= $form->field($model, 'style_sn')->textInput(['disabled'=>$model->isNewRecord?null:'disabled'])?></div>
                    <div class="col-lg-6"><?= $form->field($model, 'style_name')->textInput()?></div>
                </div>
    			<div class="row">
                    <div class="col-lg-6">
                    <?= $form->field($model, 'style_cate_id')->widget(\kartik\select2\Select2::class, [
                        'data' => \Yii::$app->styleService->styleCate->getGrpDropDown(),
                        'options' => ['placeholder' => '请选择'],
                        'pluginOptions' => [
                            'allowClear' => false,
                            'disabled'=>$model->isNewRecord?null:'disabled'
                        ],
                    ]);?>
                    </div>
                    <div class="col-lg-6">
                    <?= $form->field($model, 'product_type_id')->widget(\kartik\select2\Select2::class, [
                        'data' => \Yii::$app->styleService->productType->getGrpDropDown(),
                        'options' => ['placeholder' => '请选择'],
                        'pluginOptions' => [
                            'allowClear' => false,
                            'disabled'=>$model->isNewRecord?null:'disabled'
                        ],
                    ]);?>                
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                    <?= $form->field($model, 'style_source_id')->widget(\kartik\select2\Select2::class, [
                        'data' => \Yii::$app->styleService->styleSource->getdropDown(),
                        'options' => ['placeholder' => '请选择'],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ]);?>
                    </div>
                    <div class="col-lg-6">
                    <?= $form->field($model, 'style_channel_id')->widget(\kartik\select2\Select2::class, [
                        'data' => \Yii::$app->styleService->styleChannel->getdropDown(),
                        'options' => ['placeholder' => '请选择'],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ]);?>                
                    </div>
                </div>
       		    <div class="row">
                    <div class="col-lg-6"><?= $form->field($model, 'style_sex')->radioList(\addons\Style\common\enums\StyleSexEnum::getMap())?></div>
                    <div class="col-lg-6"><?= $form->field($model, 'is_made')->radioList(\common\enums\ConfirmEnum::getMap())?></div>
                </div>
                <div class="row">
                <?= $form->field($model, 'remark')->textArea(['options'=>['maxlength' => true]])?>
                </div>
      
         </div>
        <!-- ./box-body -->
      </div>          
      
    </div>
    <div class="modal-footer">
        <div class="col-sm-10 text-center">
            <button class="btn btn-primary" type="submit">保存</button>
            <span class="btn btn-white" onclick="history.go(-1)">返回</span>
        </div>
	</div>
</div>
<?php ActiveForm::end(); ?>

<?php

use common\helpers\Html;
use yii\widgets\ActiveForm;
use common\widgets\langbox\LangBox;
use yii\base\Widget;
use common\widgets\skutable\SkuTable;
use common\helpers\Url;
use common\enums\AuditStatusEnum;

/* @var $this yii\web\View */
/* @var $model common\models\order\order */
/* @var $form yii\widgets\ActiveForm */

$this->title = '款式详情';
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
//
?>
    <div class="box-body nav-tabs-custom">
        <h2 class="page-header">款式详情 - <?php echo $model->style_sn?></h2>
        <?php echo Html::menuTab($tabList,$tab)?>
        <div class="tab-content">
            <div class="row nav-tabs-custom tab-pane tab0 active" id="tab_1">
                <ul class="nav nav-tabs pull-right">
                    <li class="pull-left header"><i class="fa fa-th"></i> 基本信息&nbsp;
                   </li>
                </ul>                
                <div class="box-body col-lg-12" style="margin-left:9px">
                    <div class="row">
                        <div class="col-lg-4">
                            <label class="text-right col-lg-4"><?= $model->getAttributeLabel('id') ?> ：</label>                        
                            <?= $model->id ?>
                        </div>
                        <div class="col-lg-4">
                            <label class="text-right col-lg-4"><?= $model->getAttributeLabel('style_name') ?>：</label>
                            <?= $model->style_name ?>
                        </div>
                        <div class="col-lg-4">
                            <label class="text-right col-lg-4"><?= $model->getAttributeLabel('status') ?>：</label>
                            <?= \common\enums\StatusEnum::getValue($model->status)?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4">
                            <label class="text-right col-lg-4"><?= $model->getAttributeLabel('style_sn') ?>：</label>
                            <?= $model->style_sn ?>
                        </div>
                        <div class="col-lg-4">
                            <label class="text-right col-lg-4"><?= $model->getAttributeLabel('style_sex') ?>：</label>
                            <?= \addons\Style\common\enums\StyleSexEnum::getValue($model->style_sex)?>
                        </div>
                        <div class="col-lg-4">
                            <label class="text-right col-lg-4"><?= $model->getAttributeLabel('creator_id') ?>：</label>
                            <?= $model->creator->username??'' ?>
                        </div>
                    </div>
                    <div class="row">
                         <div class="col-lg-4">
                        	<label class="text-right col-lg-4"><?= $model->getAttributeLabel('product_type_id') ?>：</label>
                            <?= $model->type->name ??'' ?>
                         </div>

                        <div class="col-lg-4">
                            <label class="text-right col-lg-4"><?= $model->getAttributeLabel('style_source_id') ?>：</label>
                            <?= $model->source->name ??'' ?>
                        </div>
                        <div class="col-lg-4">
                            <label class="text-right col-lg-4"><?= $model->getAttributeLabel('created_at') ?>：</label>
                            <?= \Yii::$app->formatter->asDatetime($model->created_at) ?>
                        </div>                        
                    </div>
                    <div class="row">
                        <div class="col-lg-4">
                            <label class="text-right col-lg-4"><?= $model->getAttributeLabel('style_cate_id') ?>：</label>
                            <?= $model->cate->name ?? '' ?>
                        </div>
                        <div class="col-lg-4">
                            <label class="text-right col-lg-4">默认工厂：</label>
                            AAAAAAAAAAAAAAAAAAA
                        </div>
                        <div class="col-lg-4">
                            <label class="text-right col-lg-4"><?= $model->getAttributeLabel('audit_status') ?>：</label>
                            <?= \common\enums\AuditStatusEnum::getValue($model->audit_status)?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4">
                            <label class="text-right col-lg-4"><?= $model->getAttributeLabel('style_channel_id') ?> ：</label>
                            <?= $model->channel->name ?? '' ?>
                        </div>
                        <div class="col-lg-4">
                            <label class="text-right col-lg-4">工厂模号：</label>
                           	xxxxxxxxx
                        </div>
                        
                        <div class="col-lg-4">
                            <label class="text-right col-lg-4"><?= $model->getAttributeLabel('audit_time') ?>：</label>
                            <?= \Yii::$app->formatter->asDatetime($model->audit_time) ?>
                        </div>
                    </div>                    
                    <div class="row">
                        <div class="col-lg-4">
                            <label class="text-right col-lg-4"><?= $model->getAttributeLabel('remark') ?>：</label>
                            <?= $model->remark ?>
                        </div>
                    
                        <div class="col-lg-4">
                            <label class="text-right col-lg-4"><?= $model->getAttributeLabel('is_made') ?>：</label>
                            <?= \common\enums\ConfirmEnum::getValue($model->is_made)?>
                        </div>
        
                        <div class="col-lg-4">
                            <label class="text-right col-lg-4"><?= $model->getAttributeLabel('audit_remark') ?>：</label>
                            <?= $model->audit_remark ?>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
        <div class="modal-footer">
            <div class="text-center">
                 <?php echo Html::edit(['ajax-edit','id'=>$model->id], '编辑', [
                            'data-toggle' => 'modal',
                            'data-target' => '#ajaxModalLg',
                 ]); ?>   
                 <?php 
                 if($model->audit_status != AuditStatusEnum::PASS){
                     echo Html::edit(['ajax-audit','id'=>$model->id], '审核', [
                             'class'=>'btn btn-success btn-sm',
                             'data-toggle' => 'modal',
                             'data-target' => '#ajaxModal',
                     ]);
                 }
                 ?>             
                <span class="btn btn-white" onclick="window.location.href='<?php echo $returnUrl;?>'">返回</span>
            </div>
        </div>
 </div>
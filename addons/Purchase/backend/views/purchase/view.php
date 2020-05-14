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

$this->title = '采购单详情';
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
//
?>
<div class="box-body nav-tabs-custom">
    <h2 class="page-header">采购详情 - <?php echo $model->purchase_sn?></h2>
    <?php echo Html::menuTab($tabList,$tab)?>
    <div class="tab-content">
         <div class="box col-xs-12">
            <div class="box-header">
                <h3 class="box-title">基本信息</h3>
                <div class="box-tools" >
                </div>
            </div>
            <div class="box-body">
                    <div class="row">
                        <div class="col-lg-4">
                            <label class="text-right col-lg-4"><?= $model->getAttributeLabel('purchase_sn') ?>：</label>                        
                            <?= $model->purchase_sn ?>
                        </div>
                        <div class="col-lg-4">
                            <label class="text-right col-lg-4"><?= $model->getAttributeLabel('cost_total') ?>：</label>
                            <?= $model->cost_total ?>
                        </div>
                        <div class="col-lg-4">
                            <label class="text-right col-lg-4"><?= $model->getAttributeLabel('status') ?>：</label>
                            <?= \common\enums\StatusEnum::getValue($model->status)?>
                        </div>
                    </div>
                    <div class="row">

                        <div class="col-lg-4">
                            <label class="text-right col-lg-4"><?= $model->getAttributeLabel('goods_count') ?>：</label>
                            <?= $model->goods_count ?>
                        </div>
                        <div class="col-lg-4">
                            <label class="text-right col-lg-4"><?= $model->getAttributeLabel('audit_status') ?>：</label>
                            <?= \common\enums\AuditStatusEnum::getValue($model->audit_status)?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4">
                            <label class="text-right col-lg-4"><?= $model->getAttributeLabel('creator_id') ?>：</label>
                            <?= $model->creator ? $model->creator->username:''  ?>
                        </div>
                        <div class="col-lg-4">
                            <label class="text-right col-lg-4"><?= $model->getAttributeLabel('auditor_id') ?>：</label>
                            <?= $model->auditor ? $model->auditor->username:''  ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4">
                             <label class="text-right col-lg-4"><?= $model->getAttributeLabel('remark') ?>：</label>
                             <?= $model->remark ?>
                        </div>
                        <div class="col-lg-4">
                            <label class="text-right col-lg-4"><?= $model->getAttributeLabel('created_at') ?>：</label>
                            <?= \Yii::$app->formatter->asDatetime($model->created_at) ?>
                        </div>
                        <div class="col-lg-4">
                            <label class="text-right col-lg-4"><?= $model->getAttributeLabel('audit_time') ?>：</label>
                            <?= \Yii::$app->formatter->asDatetime($model->audit_time) ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4"></div>
                        <div class="col-lg-4">
                            <label class="text-right col-lg-4"></label>
                        </div>
                        <div class="col-lg-4">
                            <label class="text-right col-lg-4"><?= $model->getAttributeLabel('audit_remark') ?>：</label>
                            <?= $model->audit_remark ?>
                        </div>
                    </div>
            </div>
            <div class="box-footer text-center">
                 <?php echo Html::edit(['ajax-edit','id'=>$model->id], '编辑', [
                            'data-toggle' => 'modal',
                            'data-target' => '#ajaxModalLg',
                 ]); ?>
                <?php
                if($model->audit_status != AuditStatusEnum::PASS){
                    echo Html::edit(['set-follower','id'=>$model->id], '分配跟单人', [
                        'class'=>'btn btn-success btn-sm',
                        'data-toggle' => 'modal',
                        'data-target' => '#ajaxModal',
                    ]);
                }
                ?>
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
    <!-- box end -->
</div>
<!-- tab-content end -->
</div>
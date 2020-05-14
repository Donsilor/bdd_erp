<?php

use common\helpers\Html;
use yii\widgets\ActiveForm;
use common\widgets\langbox\LangBox;
use yii\base\Widget;
use common\widgets\skutable\SkuTable;
use common\helpers\Url;
use common\enums\AuditStatusEnum;

/* @var $this yii\web\View */
/* @var $model common\models\PurchaseReceipt */
/* @var $form yii\widgets\ActiveForm */

$this->title = '采购收货单详情';
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box-body nav-tabs-custom">
    <h2 class="page-header"><?php echo $this->title; ?> - <?php echo $model->receipt_no?></h2>
    <?php echo Html::menuTab($tabList,$tab)?>
    <div class="tab-content">
         <div class="box col-xs-12">
            <div class="box-body">
                <div class="row">
                    <div class="col-lg-3">
                        <label class="text-right col-lg-4"><?= $model->getAttributeLabel('receipt_no') ?>：</label>
                        <?= $model->receipt_no ?>
                    </div>
                    <div class="col-lg-3">
                        <label class="text-right col-lg-4"><?= $model->getAttributeLabel('total_cost') ?>：</label>
                        <?= $model->total_cost ?>
                    </div>
                    <div class="col-lg-3">
                        <label class="text-right col-lg-4"><?= $model->getAttributeLabel('status') ?>：</label>
                        <?= \common\enums\StatusEnum::getValue($model->status)?>
                    </div>
                    <div class="col-lg-3">
                        <label class="text-right col-lg-4"><?= $model->getAttributeLabel('receipt_num') ?>：</label>
                        <?= $model->receipt_num ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-3">
                        <label class="text-right col-lg-4"><?= $model->getAttributeLabel('audit_status') ?>：</label>
                        <?= \common\enums\AuditStatusEnum::getValue($model->audit_status)?>
                    </div>
                    <div class="col-lg-3">
                        <label class="text-right col-lg-4"><?= $model->getAttributeLabel('creator_id') ?>：</label>
                        <?= $model->creator ? $model->creator->username:''  ?>
                    </div>
                    <div class="col-lg-3">
                        <label class="text-right col-lg-4"><?= $model->getAttributeLabel('auditor_id') ?>：</label>
                        <?= $model->auditor ? $model->auditor->username:''  ?>
                    </div>
                    <div class="col-lg-3">
                        <label class="text-right col-lg-4"><?= $model->getAttributeLabel('remark') ?>：</label>
                        <?= $model->remark ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-3">
                        <label class="text-right col-lg-4"><?= $model->getAttributeLabel('created_at') ?>：</label>
                        <?= \Yii::$app->formatter->asDatetime($model->created_at) ?>
                    </div>
                    <div class="col-lg-3">
                        <label class="text-right col-lg-4"><?= $model->getAttributeLabel('audit_time') ?>：</label>
                        <?= \Yii::$app->formatter->asDatetime($model->audit_time) ?>
                    </div>
                    <div class="col-lg-3">
                        <label class="text-right col-lg-4"><?= $model->getAttributeLabel('audit_remark') ?>：</label>
                        <?= $model->audit_remark ?>
                    </div>
                    <div class="col-lg-3">
                        <label class="text-right col-lg-4"></label>
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
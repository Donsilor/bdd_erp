<?php

use common\helpers\Html;
use addons\Purchase\common\enums\PurchaseStatusEnum;
use common\enums\AuditStatusEnum;

/* @var $this yii\web\View */
/* @var $model common\models\order\order */
/* @var $form yii\widgets\ActiveForm */

$this->title = '金料采购详情';
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
//
?>
<div class="box-body nav-tabs-custom">
    <h2 class="page-header"><?php echo $this->title;?> - <?php echo $model->purchase_sn?></h2>
    <?php echo Html::menuTab($tabList,$tab)?>
    <div class="tab-content">
        <div class="col-xs-12">
            <div class="box">
                <div class=" table-responsive" >
                     <table class="table table-hover">
                        <tr>
                            <td class="col-xs-1 text-right no-border-top"><?= $model->getAttributeLabel('purchase_sn') ?>：</td>
                            <td class="no-border-top"><?= $model->purchase_sn ?></td>
                        </tr>                        
                        <tr>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('purchase_status') ?>：</td>
                            <td><?= PurchaseStatusEnum::getValue($model->purchase_status)?></td>
                        </tr>                        
                        <tr>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('supplier_id') ?>：</td>
                            <td><?= $model->supplier->supplier_name ?? '';  ?></td>
                        </tr>                        
                        <tr>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('total_num') ?>：</td>
                            <td><?= $model->total_num ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('total_cost') ?>：</td>
                            <td><?= $model->total_cost ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('creator_id') ?>：</td>
                            <td><?= $model->creator->username ?? ''  ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('created_at') ?>：</td>
                            <td><?= \Yii::$app->formatter->asDatetime($model->created_at) ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('audit_status') ?>：</td>
                            <td><?= \common\enums\AuditStatusEnum::getValue($model->audit_status)?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('auditor_id') ?>：</td>
                            <td><?= $model->auditor->username ?? ''  ?></td>
                        </tr>                        
                        <tr>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('audit_time') ?>：</td>
                            <td><?= \Yii::$app->formatter->asDatetime($model->audit_time) ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('audit_remark') ?>：</td>
                            <td><?= $model->audit_remark ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('remark') ?>：</td>
                            <td><?= $model->remark ?></td>
                        </tr>
                    </table>
                </div>
                <div class="box-footer text-center">
                    <?php
                        if($model->purchase_status == PurchaseStatusEnum::SAVE) {
                            echo Html::edit(['ajax-edit', 'id' => $model->id], '编辑', [
                                'data-toggle' => 'modal',
                                'class' => 'btn btn-primary btn-ms',
                                'data-target' => '#ajaxModal',
                            ]);
                        }
                    ?>
                    <?php
                    if($model->purchase_status == PurchaseStatusEnum::SAVE){
                        echo Html::edit(['ajax-apply','id'=>$model->id], '提审', [
                            'class'=>'btn btn-success btn-ms',
                            'onclick' => 'rfTwiceAffirm(this,"提交审核", "确定提交吗？");return false;',
                        ]);
                    }
                    ?>
                    <?php
                    if($model->purchase_status == PurchaseStatusEnum::PENDING){
                        echo Html::edit(['ajax-audit','id'=>$model->id], '审核', [
                            'class'=>'btn btn-success btn-ms',
                            'data-toggle' => 'modal',
                            'data-target' => '#ajaxModal',
                        ]);
                    }
                    ?>

                </div>
            </div>
        </div>



    <!-- box end -->
</div>
<!-- tab-content end -->
</div>
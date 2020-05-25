<?php

use common\helpers\Html;
use common\enums\AuditStatusEnum;

/* @var $this yii\web\View */
/* @var $model common\models\WarehouseBill */
/* @var $form yii\widgets\ActiveForm */

$this->title = '盘点单详情';
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box-body nav-tabs-custom">
    <h2 class="page-header"><?php echo $this->title; ?> - <?php echo $model->bill_no?></h2>
    <?php echo Html::menuTab($tabList,$tab)?>
    <div class="tab-content">
        <div class="col-xs-12" style="padding-left: 0px;padding-right: 0px;">
            <div class="box">
                <div class="box-body table-responsive" style="padding-left: 0px;padding-right: 0px;">
                    <table class="table table-hover">
                        <tr>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('bill_no') ?>：</td>
                            <td><?= $model->bill_no ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('bill_type') ?>：</td>
                            <td><?= \addons\Warehouse\common\enums\BillTypeEnum::getValue($model->bill_type)?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('bill_status') ?>：</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td class="col-xs-1 text-right">盘点仓库：</td>
                            <td><?= $model->toWarehouse->name ??'' ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-1 text-right">应盘数量：</td>
                            <td><?= $model->goods_num ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-1 text-right">实盘数量：</td>
                            <td><?= Yii::$app->warehouseService->billW->getPandianCount($model->id) ?></td>
                        </tr>                        
 						<tr>
                            <td class="col-xs-1 text-right">盘盈：</td>
                            <td>0</td>
                        </tr>
                        <tr>
                            <td class="col-xs-1 text-right">盘亏：</td>
                            <td>0</td>
                        </tr>
                        <tr>
                            <td class="col-xs-1 text-right">正常：</td>
                            <td>0</td>
                        </tr>
                        <tr>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('total_cost') ?>：</td>
                            <td><?= $model->total_cost ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('creator_id') ?>：</td>
                            <td><?= $model->creator ? $model->creator->username:''  ?></td>
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
                            <td><?= $model->auditor ? $model->auditor->username:''  ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('audit_remark') ?>：</td>
                            <td><?= $model->audit_remark ?></td>
                        </tr>
                        
                        <tr>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('audit_time') ?>：</td>
                            <td><?= \Yii::$app->formatter->asDatetime($model->audit_time) ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('remark') ?>：</td>
                            <td><?= $model->remark ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>


        <div class="box-footer text-center">
            <?php echo Html::edit(['ajax-edit','id'=>$model->id], '编辑', [
                'data-toggle' => 'modal',
                'class'=>'btn btn-primary btn-ms',
                'data-target' => '#ajaxModalLg',
            ]); ?>
            <?php
            if($model->audit_status != AuditStatusEnum::PASS){
                echo Html::edit(['ajax-audit','id'=>$model->id], '审核', [
                    'class'=>'btn btn-success btn-ms',
                    'data-toggle' => 'modal',
                    'data-target' => '#ajaxModal',
                ]);
            }
            ?>
        </div>

        <!-- box end -->
    </div>
    <!-- tab-content end -->
</div>
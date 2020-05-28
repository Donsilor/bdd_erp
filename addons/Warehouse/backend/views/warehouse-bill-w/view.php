<?php

use common\helpers\Html;
use common\enums\AuditStatusEnum;
use common\helpers\Url;
use addons\Warehouse\common\enums\BillStatusEnum;
use addons\Warehouse\common\enums\BillWStatusEnum;

/* @var $this yii\web\View */
/* @var $model common\models\WarehouseBill */
/* @var $form yii\widgets\ActiveForm */

$this->title = '盘点单详情';
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box-body nav-tabs-custom">
    <h2 class="page-header"><?php echo $this->title; ?> - <?php echo $model->bill_no?> - <?php echo BillStatusEnum::getValue($model->bill_status)?></h2>
    <?php echo Html::menuTab($tabList,$tab)?>
    <div class="tab-content">
        <div class="col-xs-12" style="padding-left: 0px;padding-right: 0px;">
            <div class="box">
                <div class="box-body table-responsive" style="padding-left: 0px;padding-right: 0px;">
                    <table class="table table-hover">
                        <tr>
                            <td class="col-xs-1 text-right no-border-top"><?= $model->getAttributeLabel('bill_no') ?>：</td>
                            <td class="no-border-top"><?= $model->bill_no ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('bill_type') ?>：</td>
                            <td><?= \addons\Warehouse\common\enums\BillTypeEnum::getValue($model->bill_type)?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('bill_status') ?>：</td>
                            <td>
                            <?= \addons\Warehouse\common\enums\BillStatusEnum::getValue($model->bill_status) ?>                            
                            </td>
                        </tr>
                        <tr>
                            <td class="col-xs-1 text-right">盘点仓库：</td>
                            <td><?= $model->toWarehouse->name ??'' ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-1 text-right">应盘数量：</td>
                            <td><?= $model->billW->should_num ?? 0; ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-1 text-right">实盘数量：</td>
                            <td><?= $model->billW->actual_num ?? 0; ?></td>
                        </tr>  
                        <tr>
                            <td class="col-xs-1 text-right">正常数量：</td>
                            <td><?= $model->billW->normal_num ?? 0; ?></td>
                        </tr>                                              
 						<tr>
                            <td class="col-xs-1 text-right">盘盈数量：</td>
                            <td><?= $model->billW->profit_num ?? 0; ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-1 text-right">盘亏数量：</td>
                            <td><?= $model->billW->loss_num ?? 0; ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-1 text-right">调整数量：</td>
                            <td><?= $model->billW->adjust_num ?? 0; ?></td>
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
            
            <?php
                if($model->bill_status == BillStatusEnum::SAVE){
                    echo Html::edit(['ajax-edit','id'=>$model->id], '编辑', [
                        'data-toggle' => 'modal',
                        'class'=>'btn btn-primary btn-ms',
                        'data-target' => '#ajaxModalLg',
                    ]);
                    echo '&nbsp;';
                    echo Html::edit(['pandian', 'id' => $model->id,'returnUrl'=>Url::getReturnUrl()], '盘点', ['class'=>'btn btn-warning btn-ms']);
                    echo '&nbsp;';
                    echo Html::edit(['ajax-finish','id'=>$model->id], '盘点结束', [
                        'class'=>'btn btn-success btn-ms',
                        'onclick' => 'rfTwiceAffirm(this,"盘点结束","确定结束吗？");return false;',
                    ]);
                }
            ?>

 
           <?php
               echo Html::edit(['ajax-adjust', 'id' => $model->id], '刷新', [
                    'class'=>'btn btn-primary btn-ms'
                    ]);
            ?>


            <?php if($model->bill_status == BillStatusEnum::PENDING){?>
                <?php echo Html::edit(['ajax-audit','id'=>$model->id], '审核', [
                    'class'=>'btn btn-success btn-ms',
                    'data-toggle' => 'modal',
                    'data-target' => '#ajaxModal',
                ]);?>
            <?php }?>
        </div>

        <!-- box end -->
    </div>
    <!-- tab-content end -->
</div>
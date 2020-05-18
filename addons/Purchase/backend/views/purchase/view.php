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
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title"><i class="fa fa-bars"></i> 基本信息</h3>
                    <div class="box-tools">
                        <?php echo Html::edit(['ajax-edit','id'=>$model->id], '编辑', [
                            'data-toggle' => 'modal',
                            'class'=>'btn btn-primary btn-xs',
                            'data-target' => '#ajaxModalLg',
                        ]); ?>
                        <?php
                        if($model->audit_status != AuditStatusEnum::PASS){
                            echo Html::edit(['set-follower','id'=>$model->id], '分配跟单人', [
                                'class'=>'btn btn-info btn-xs',
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

                    </div>
                </div>
                <div class="box-body table-responsive">
                    <table class="table table-hover">
                        <tr>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('purchase_sn') ?>：</td>
                            <td><?= $model->purchase_sn ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('cost_total') ?>：</td>
                            <td><?= $model->cost_total ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('status') ?>：</td>
                            <td><?= \common\enums\StatusEnum::getValue($model->status)?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('goods_count') ?>：</td>
                            <td><?= $model->goods_count ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('audit_status') ?>：</td>
                            <td><?= \common\enums\AuditStatusEnum::getValue($model->audit_status)?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('creator_id') ?>：</td>
                            <td><?= $model->creator ? $model->creator->username:''  ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('auditor_id') ?>：</td>
                            <td><?= $model->auditor ? $model->auditor->username:''  ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('remark') ?>：</td>
                            <td><?= $model->remark ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('created_at') ?>：</td>
                            <td><?= \Yii::$app->formatter->asDatetime($model->created_at) ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('audit_time') ?>：</td>
                            <td><?= \Yii::$app->formatter->asDatetime($model->audit_time) ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('audit_remark') ?>：</td>
                            <td><?= $model->audit_remark ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>



    <!-- box end -->
</div>
<!-- tab-content end -->
</div>
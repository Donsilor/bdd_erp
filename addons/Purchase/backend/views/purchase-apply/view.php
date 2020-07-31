<?php

use common\helpers\Html;
use addons\Purchase\common\enums\ApplyStatusEnum;
use common\enums\AuditStatusEnum;

/* @var $this yii\web\View */
/* @var $model common\models\order\order */
/* @var $form yii\widgets\ActiveForm */

$this->title = '采购申请详情 ';
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
//
?>
<div class="box-body nav-tabs-custom">
    <h2 class="page-header">采购申请详情 - <?php echo $model->apply_sn?></h2>
    <?php echo Html::menuTab($tabList,$tab)?>
    <div class="tab-content">
        <div class="col-xs-12">
            <div class="box">
                <div class=" table-responsive" >
                    <table class="table table-hover">
                        <tr>
                            <td class="col-xs-1 text-right no-border-top"><?= $model->getAttributeLabel('apply_sn') ?>：</td>
                            <td class="no-border-top"><?= $model->apply_sn ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('channel_id') ?>：</td>
                            <td><?= $model->channel->name ?? '';  ?></td>
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
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('warehouse_id') ?>：</td>
                            <td><?= $model->warehouse->name ?? '';  ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('delivery_time') ?>：</td>
                            <td><?= $model->delivery_time ? Yii::$app->formatter->asDate($model->delivery_time) : '';  ?></td>
                        </tr>  
                        <tr>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('apply_status') ?>：</td>
                            <td><?= ApplyStatusEnum::getValue($model->apply_status)?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('audit_status') ?>：</td>
                            <td><?= \common\enums\AuditStatusEnum::getValue($model->audit_status)?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('creator_id') ?>：</td>
                            <td><?=  $model->creator->username ?? ''  ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('auditor_id') ?>：</td>
                            <td><?= $model->auditor->username ?? ''  ?></td>
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
                <div class="box-footer text-center">
                    <?php
                        if($model->apply_status == ApplyStatusEnum::SAVE) {
                            echo Html::edit(['ajax-edit', 'id' => $model->id], '编辑', [
                                'data-toggle' => 'modal',
                                'class' => 'btn btn-primary btn-ms',
                                'data-target' => '#ajaxModalLg',
                            ]);
                        }
                    ?>                    
                    <?php
                    if($model->apply_status == ApplyStatusEnum::SAVE){
                        echo Html::edit(['ajax-apply','id'=>$model->id], '提审', [
                            'class'=>'btn btn-success btn-ms',
                            'onclick' => 'rfTwiceAffirm(this,"提交审核", "确定提交吗？");return false;',
                        ]);
                    }
                    ?>
                    <?php
                    $isAudit = Yii::$app->services->flowType->isAudit(Yii::$app->purchaseService->apply->getTargetYType($model->channel_id),$model->id);
                    $isAudit1 = Yii::$app->services->flowType->isAudit(\common\enums\TargetTypeEnum::PURCHASE_APPLY_S_MENT,$model->id);
                    if($model->apply_status == ApplyStatusEnum::PENDING && $isAudit){
                        echo Html::edit(['ajax-audit','id'=>$model->id], '审核', [
                            'class'=>'btn btn-success btn-ms',
                            'data-toggle' => 'modal',
                            'data-target' => '#ajaxModal',
                        ]);
                    }elseif($model->apply_status == ApplyStatusEnum::CONFIRM && $isAudit1){
                        echo Html::edit(['final-audit','id'=>$model->id], '审核', [
                            'class'=>'btn btn-success btn-ms',
                            'data-toggle' => 'modal',
                            'data-target' => '#ajaxModal',
                        ]);
                    }
                    ?>
<!--                    --><?//= Html::a('打印',['print','id'=>$model->id],[
//                        'target'=>'_blank',
//                        'class'=>'btn btn-info btn-ms',
//                    ]); ?>
                    <?= Html::button('导出', [
                        'class'=>'btn btn-success btn-ms',
                        'onclick' => 'batchExport()',
                    ]);?>
                </div>
            </div>
        </div>

        <?php
        if(!empty($flow_detail)){
        ?>

        <style>
            .time-line li div.mbox{
                border: 1px solid #f0f0f0;
                margin-left: 20px;
                line-height: 25px;
                padding: 10px;
                min-height: 60px;
            }
            .time-line li div.mbox .left{
                float: left;
            }
            .time-line li div.mbox .right{
                float: right;
            }
            .time-line li div.mbox .clear{
                clear: both;
            }
            .time-line li.grey div{
                color: grey;
            }

        </style>
        <div class="row">
            <div class="col-lg-12">
                <div class="box">

                    <div class="box-header">
                        <h3 class="box-title"><i class="fa fa-user"></i> 审批流程</h3>
                    </div>
                    <div class="box-body">
                        <div class="col-md-12 changelog-info">
                            <ul class="time-line">
                                <?php
                                foreach ($flow_detail as $flow){
                                    ?>
                                    <li class="grey">
                                        <div class="mbox">
                                            <div class="one">
                                                <div class="left">审&nbsp;&nbsp;核&nbsp;&nbsp;人:  <?= $flow->member->username ?? ''?></div>
                                                <?php if($flow->audit_status == \common\enums\AuditStatusEnum::PASS){ ?>
                                                    <div class="right">审核时间：<?= \Yii::$app->formatter->asDatetime($flow->audit_time) ?></div>
                                                <?php } ?>
                                            </div>
                                            <div class="clear">
                                                审核状态：<?= \common\enums\AuditStatusEnum::getValue($flow->audit_status);?>
                                            </div>
                                            <div>备&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;注：<?= $flow->audit_remark?></div>
                                        </div>
                                    </li>
                                <?php } ?>

                            </ul>
                            <!-- /.widget-user -->
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <?php } ?>



        <!-- box end -->
</div>
<!-- tab-content end -->
</div>
<script>
    function batchExport() {
        window.location.href = "<?= \common\helpers\Url::buildUrl('export',[],['ids'])?>?ids=<?php echo $model->id ?>";
    }

</script>
<?php

use common\helpers\Html;
use addons\Warehouse\common\enums\BillStatusEnum;
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
    <div class="tab-content" >
        <div class="col-xs-12">
            <div class="box">
                <div class=" table-responsive" >
                    <table class="table table-hover">
                        <tr>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('purchase_sn') ?>：</td>
                            <td><?= $model->purchase_sn ?></td>
                        </tr>
 						<tr>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('purchase_cate') ?>：</td>
                            <td><?= \addons\Purchase\common\enums\PurchaseCateEnum::getValue($model->purchase_cate)?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('order_type') ?>：</td>
                            <td><?= \addons\Purchase\common\enums\OrderTypeEnum::getValue($model->order_type)?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('purchase_status') ?>：</td>
                            <td><?= \addons\Purchase\common\enums\PurchaseStatusEnum::getValue($model->purchase_status)?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('channel_id') ?>：</td>
                            <td><?= $model->channel->name ?? ''?></td>
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
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('supplier_id') ?>：</td>
                            <td><?= $model->supplier ? $model->supplier->supplier_name : '';  ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('follower_id') ?>：</td>
                            <td><?= $model->follower ? $model->follower->username : ''; ?></td>
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
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('delivery_time') ?>：</td>
                            <td><?= $model->delivery_time ? \Yii::$app->formatter->asDatetime($model->delivery_time) : '' ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('apply_sn') ?>：</td>
                            <td><?= $model->apply_sn ?></td>
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
                        if($model->purchase_status == BillStatusEnum::SAVE) {
                            echo Html::edit(['ajax-edit', 'id' => $model->id], '编辑', [
                                'data-toggle' => 'modal',
                                'class' => 'btn btn-primary btn-ms',
                                'data-target' => '#ajaxModalLg',
                            ]);
                        }
                    ?>
                    <?php
                    if($model->purchase_status <= BillStatusEnum::PENDING){
                        echo Html::edit(['ajax-follower','id'=>$model->id], '跟单人', [
                            'class'=>'btn btn-info btn-ms',
                            'data-toggle' => 'modal',
                            'data-target' => '#ajaxModal',
                        ]);
                    }
                    ?>
                    <?php
                    if($model->purchase_status == BillStatusEnum::SAVE){
                        echo Html::edit(['ajax-apply','id'=>$model->id], '提审', [
                            'class'=>'btn btn-success btn-ms',
                            'onclick' => 'rfTwiceAffirm(this,"提交审核", "确定提交吗？");return false;',
                        ]);
                    }
                    ?>
                    <?php
                    $isAudit = Yii::$app->services->flowType->isAudit(\common\enums\TargetTypeEnum::PURCHASE_MENT,$model->id);
                    if($model->purchase_status == BillStatusEnum::PENDING && $isAudit){
                        echo Html::edit(['ajax-audit','id'=>$model->id], '审核', [
                            'class'=>'btn btn-success btn-ms',
                            'data-toggle' => 'modal',
                            'data-target' => '#ajaxModalLg',
                        ]);
                    }
                    ?>
                    <?= Html::a('打印',['print','id'=>$model->id],[
                        'target'=>'_blank',
                        'class'=>'btn btn-info btn-ms',
                    ]); ?>
                    <?= Html::button('导出', [
                        'class'=>'btn btn-success btn-ms',
                        'onclick' => 'batchExport()',
                    ]);?>
                </div>
            </div>
        </div>

        <div id="flow">

        </div>
        <!-- box end -->
</div>
<!-- tab-content end -->
</div>
<script>
    function batchExport() {
        window.location.href = "<?= \common\helpers\Url::buildUrl('export',[],['ids'])?>?ids=<?php echo $model->id ?>";
    }
    $("#flow").load("<?= \common\helpers\Url::to(['../common/flow/audit-view','flow_type_id'=> \common\enums\TargetTypeEnum::PURCHASE_MENT,'target_id'=>$model->id])?>")

</script>
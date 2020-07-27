<?php

use common\helpers\Html;
use common\enums\AuditStatusEnum;
use addons\Warehouse\common\enums\BillStatusEnum;

/* @var $this yii\web\View */
/* @var $model addons
/* @var $form yii\widgets\ActiveForm */

$this->title = '领石单详情';
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
                            <td><?= \addons\Warehouse\common\enums\StoneBillTypeEnum::getValue($model->bill_type)?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('supplier_id') ?>：</td>
                            <td><?= $model->supplier->supplier_name??"" ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('bill_status') ?>：</td>
                            <td><?= \addons\Warehouse\common\enums\StoneBillStatusEnum::getValue($model->bill_status)?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('put_in_type') ?>：</td>
                            <td><?= \addons\Warehouse\common\enums\PutInTypeEnum::getValue($model->put_in_type) ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('total_num') ?>：</td>
                            <td><?= $model->total_num ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('total_weight') ?>：</td>
                            <td><?= $model->total_weight ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('account_type') ?>：</td>
                            <td><?= \addons\Warehouse\common\enums\AccountTypeEnum::getValue($model->account_type) ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('total_cost') ?>：</td>
                            <td><?= $model->total_cost ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('delivery_no') ?>：</td>
                            <td><?= $model->delivery_no ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('status') ?>：</td>
                            <td><?= \common\enums\StatusEnum::getValue($model->status)?></td>
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
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('audit_remark') ?>：</td>
                            <td><?= $model->audit_remark ?></td>
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
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('remark') ?>：</td>
                            <td><?= $model->remark ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>


        <div class="box-footer text-center">
            <?php
            if($model->bill_status == BillStatusEnum::SAVE) {
                echo Html::edit(['ajax-edit', 'id' => $model->id], '编辑', [
                    'data-toggle' => 'modal',
                    'class' => 'btn btn-primary btn-ms',
                    'data-target' => '#ajaxModalLg',
                ]);
                echo '&nbsp;';
                echo Html::edit(['ajax-apply', 'id' => $model->id], '提审', [
                    'class' => 'btn btn-success btn-ms',
                    'onclick' => 'rfTwiceAffirm(this,"提交审核","确定提交吗？");return false;',
                ]);
            }
            ?>
            <?= Html::a('打印',['print','id'=>$model->id],[
                'target'=>'_blank',
                'class'=>'btn btn-info btn-ms',
            ]);
            echo '&nbsp;';
            ?>
            <?= Html::button('导出', [
                'class'=>'btn btn-success btn-ms',
                'onclick' => 'batchExport()',
            ]);?>
        </div>

        <!-- box end -->
    </div>
    <!-- tab-content end -->
</div>
<script>
    function batchExport() {
        window.location.href = "<?= \common\helpers\Url::buildUrl('export',[],['ids'])?>?ids=<?php echo $model->id ?>";
    }
</script>
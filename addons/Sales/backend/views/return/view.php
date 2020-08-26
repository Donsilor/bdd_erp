<?php

use common\helpers\Html;
use common\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\order\order */
/* @var $form yii\widgets\ActiveForm */

$this->title = '退款单详情';
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
//
?>

<div class="box-body nav-tabs-custom">
    <h2 class="page-header"><?= $this->title ?> - <?= $model->return_no?> - <?= \addons\Sales\common\enums\CheckStatusEnum::getValue($model->check_status)?></h2>
    <?php echo Html::menuTab($tabList,$tab)?>
    <div class="row">
         <div class="col-xs-12">
             <div class="box">
                 <div class="col-xs-6" style="padding: 0px;">
                     <div class="box" style="margin-bottom: 0px;">
                         <div class="box-body table-responsive">
                             <table class="table table-hover">
                                 <tr>
                                     <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('return_no') ?>：</td>
                                     <td><?= $model->return_no ?></td>
                                 </tr>
                                 <tr>
                                     <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('channel_id') ?>：</td>
                                     <td><?= $model->channel->name??"" ?></td>
                                 </tr>
                                 <tr>
                                     <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('order_sn') ?>：</td>
                                     <td><?= $model->order_sn ?></td>
                                 </tr>
<!--                                 <tr>-->
<!--                                     <td class="col-xs-3 text-right">--><?//= $model->getAttributeLabel('goods_id') ?><!--：</td>-->
<!--                                     <td>--><?//= $model->goods_id ?><!--</td>-->
<!--                                 </tr>-->
                                 <tr>
                                     <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('goods_num') ?>：</td>
                                     <td><?= $model->goods_num ?></td>
                                 </tr>
                                 <tr>
                                     <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('should_amount') ?>：</td>
                                     <td><?= $model->should_amount ?></td>
                                 </tr>
                                 <tr>
                                     <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('apply_amount') ?>：</td>
                                     <td><?= $model->apply_amount??""?></td>
                                 </tr>
                                 <tr>
                                     <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('real_amount') ?>：</td>
                                     <td><?= $model->real_amount ?></td>
                                 </tr>
                                 <tr>
                                     <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('return_reason') ?>：</td>
                                     <td><?= $model->config->name??""?></td>
                                 </tr>
                                 <tr>
                                     <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('return_by') ?>：</td>
                                     <td><?= \addons\Sales\common\enums\ReturnByEnum::getValue($model->return_by)??"" ?></td>
                                 </tr>
                                 <tr>
                                     <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('return_type') ?>：</td>
                                     <td><?= \addons\Sales\common\enums\ReturnTypeEnum::getValue($model->return_type)??"" ?></td>
                                 </tr>
                                 <tr>
                                     <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('customer_name') ?>：</td>
                                     <td><?= $model->customer_name ?></td>
                                 </tr>
                                 <tr>
                                     <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('customer_mobile') ?>：</td>
                                     <td><?= $model->customer_mobile ?></td>
                                 </tr>
                                 <tr>
                                     <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('customer_email') ?>：</td>
                                     <td><?= $model->customer_email ?></td>
                                 </tr>
                                 <tr>
                                     <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('currency') ?>：</td>
                                     <td><?= \common\enums\CurrencyEnum::getValue($model->currency)??"" ?></td>
                                 </tr>
                                 <tr>
                                     <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('bank_name') ?>：</td>
                                     <td><?= $model->bank_name ?></td>
                                 </tr>
                                 <tr>
                                     <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('bank_card') ?>：</td>
                                     <td><?= $model->bank_card ?></td>
                                 </tr>
                                 <tr>
                                     <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('is_finance_refund') ?>：</td>
                                     <td><?= \common\enums\ConfirmEnum::getValue($model->is_finance_refund)??"" ?></td>
                                 </tr>
                                 <tr>
                                     <td class="col-xs-4 text-right"><?= $model->getAttributeLabel('is_quick_refund') ?>：</td>
                                     <td><?= \common\enums\ConfirmEnum::getValue($model->is_quick_refund)??"" ?></td>
                                 </tr>
                                 <tr>
                                     <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('payer_id') ?>：</td>
                                     <td><?= $model->payer_id ?></td>
                                 </tr>
                                 <tr>
                                     <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('pay_status') ?>：</td>
                                     <td><?= \common\enums\PayStatusEnum::getValue($model->pay_status)??"" ?></td>
                                 </tr>
                                 <tr>
                                     <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('pay_remark') ?>：</td>
                                     <td><?= $model->pay_remark ?></td>
                                 </tr>
                             </table>
                         </div>
                     </div>
                 </div>
                 <div class="col-xs-6" style="padding: 0px;">
                     <div class="box" style="margin-bottom: 0px;">
                         <div class="box-body table-responsive" >
                             <table class="table table-hover">
                                 <tr>
                                     <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('leader_id') ?>：</td>
                                     <td><?= $model->leader->username??"" ?></td>
                                 </tr>
                                 <tr>
                                     <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('leader_status') ?>：</td>
                                     <td><?= \common\enums\AuditStatusEnum::getValue($model->leader_status)??"" ?></td>
                                 </tr>
                                 <tr>
                                     <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('leader_time') ?>：</td>
                                     <td><?= $model->leader_time?\Yii::$app->formatter->asDatetime($model->leader_time):''; ?></td>
                                 </tr>
                                 <tr>
                                     <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('leader_remark') ?>：</td>
                                     <td><?= $model->leader_remark ?></td>
                                 </tr>
                                 <tr>
                                     <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('storekeeper_id') ?>：</td>
                                     <td><?= $model->storekeeper->username??"" ?></td>
                                 </tr>
                                 <tr>
                                     <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('storekeeper_status') ?>：</td>
                                     <td><?= \common\enums\AuditStatusEnum::getValue($model->storekeeper_status)??"" ?></td>
                                 </tr>
                                 <tr>
                                     <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('storekeeper_time') ?>：</td>
                                     <td><?= $model->storekeeper_time?\Yii::$app->formatter->asDatetime($model->storekeeper_time):''; ?></td>
                                 </tr>
                                 <tr>
                                     <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('storekeeper_remark') ?>：</td>
                                     <td><?= $model->storekeeper_remark ?></td>
                                 </tr>
                                 <tr>
                                     <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('finance_id') ?>：</td>
                                     <td><?= $model->finance->username??"" ?></td>
                                 </tr>
                                 <tr>
                                     <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('finance_status') ?>：</td>
                                     <td><?= \common\enums\AuditStatusEnum::getValue($model->finance_status)??"" ?></td>
                                 </tr>
                                 <tr>
                                     <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('finance_time') ?>：</td>
                                     <td><?= $model->finance_time?\Yii::$app->formatter->asDatetime($model->finance_time):''; ?></td>
                                 </tr>
                                 <tr>
                                     <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('finance_remark') ?>：</td>
                                     <td><?= $model->finance_remark ?></td>
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
                                     <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('check_status') ?>：</td>
                                     <td><?= \addons\Sales\common\enums\CheckStatusEnum::getValue($model->check_status)??""?></td>
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
                                     <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('updated_at') ?>：</td>
                                     <td><?= \Yii::$app->formatter->asDatetime($model->updated_at) ?></td>
                                 </tr>
                                 <tr>
                                     <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('remark') ?>：</td>
                                     <td><?= $model->remark ?></td>
                                 </tr>
                             </table>
                         </div>
                     </div>
                 </div>
                 <div class="col-xs-12" style="padding: 0px;">
                     <div class="box">
                         <div class="box-body table-responsive" >
                             <table class="table table-hover">
                                 <tr>
                                     <?php if($model->pay_receipt){?><td class="col-xs-4 text-center"><?= \common\helpers\ImageHelper::fancyBox($model->pay_receipt,90,90) ?></td><?php } ?>
                                 </tr>
                                 <tr>
                                     <?php if($model->pay_receipt){?><td class="col-xs-4 text-center"><?= $model->getAttributeLabel('pay_receipt') ?>：</td><?php } ?>
                                 </tr>
                             </table>
                         </div>
                     </div>
                 </div>
             </div>
         </div>
        <div class="box-footer text-center">
            <?php
                if($model->audit_status == \common\enums\AuditStatusEnum::SAVE){
                    echo Html::edit(['ajax-edit','id' => $model->id,'returnUrl' => Url::getReturnUrl()], '编辑', [
                        'data-toggle' => 'modal',
                        'data-target' => '#ajaxModalLg',
                    ]);
                }
                if($model->audit_status == \common\enums\AuditStatusEnum::SAVE){
                    echo '&nbsp;';
                    echo Html::edit(['ajax-apply','id'=>$model->id], '提审', [
                        'class'=>'btn btn-info btn-sm',
                        'onclick' => 'rfTwiceAffirm(this,"提交审核", "确定提交吗？");return false;',
                    ]);
                }
//                if($model->audit_status == \common\enums\AuditStatusEnum::PENDING){
//                    echo '&nbsp;';
//                    echo Html::edit(['ajax-audit','id'=>$model->id], '审核', [
//                        'class'=>'btn btn-success btn-sm',
//                        'data-toggle' => 'modal',
//                        'data-target' => '#ajaxModal',
//                    ]);
//                }
                echo '&nbsp;';
                echo Html::a('返回列表', ['index'], ['class' => 'btn btn-default btn-sm']);
            ?>
        </div>
    </div>
</div>



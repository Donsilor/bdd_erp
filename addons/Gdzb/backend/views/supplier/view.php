<?php

use common\helpers\Html;
use common\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\order\order */
/* @var $form yii\widgets\ActiveForm */

$this->title = '供应商详情';
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
//
?>

<div class="box-body nav-tabs-custom">
    <h2 class="page-header"><?= $this->title ?> - <?= $model->supplier_code?> - <?= \common\enums\AuditStatusEnum::getValue($model->audit_status)?></h2>
    <?php echo Html::menuTab($tabList,$tab)?>
    <div class="row">
         <div class="col-xs-12">
             <div class="box">
                 <div class="col-xs-6" style="padding: 0px;">
                     <div class="box" style="margin-bottom: 0px;">
                         <div class="box-body table-responsive">
                             <table class="table table-hover">
                                 <tr>
                                     <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('supplier_code') ?>：</td>
                                     <td><?= $model->supplier_code ?></td>
                                 </tr>
                                 <tr>
                                     <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('level') ?>：</td>
                                     <td><?= \addons\Gdzb\common\enums\GradeEnum::getValue($model->level) ?></td>
                                 </tr>
                                 <tr>
                                     <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('type') ?>：</td>
                                     <td><?= \addons\Gdzb\common\enums\TypeEnum::getValue($model->type) ?></td>
                                 </tr>

                                 <tr>
                                     <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('source_id') ?>：</td>
                                     <td><?= \addons\Gdzb\common\enums\SourceEnum::getValue($model->source_id) ?></td>
                                 </tr>
                                 <tr>
                                     <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('channel_id') ?>：</td>
                                     <td><?= $model->saleChannel->name ?? ''; ?></td>
                                 </tr>

                                 <tr>
                                     <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('business_scope') ?>：</td>
                                     <td><?= $model->business_scope ?></td>
                                 </tr>
                                 <tr>
                                     <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('follower_id') ?>：</td>
                                     <td><?= $model->follower->username ?? ''; ?></td>
                                 </tr>
                                 <tr>
                                     <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('trade_num') ?>：</td>
                                     <td></td>
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
                                     <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('auditor_id') ?>：</td>
                                     <td><?= $model->auditor ? $model->auditor->username:''  ?></td>
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
                                     <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('contactor') ?>：</td>
                                     <td><?= $model->contactor ?></td>
                                 </tr>
                                 <tr>
                                     <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('mobile') ?>：</td>
                                     <td><?= $model->mobile ?></td>
                                 </tr>
                                 <tr>
                                     <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('address') ?>：</td>
                                     <td><?= $model->address ?></td>
                                 </tr>
                                 <tr>
                                     <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('bank_name') ?>：</td>
                                     <td><?= $model->bank_name ?></td>
                                 </tr>

                                 <tr>
                                     <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('bank_account_name') ?>：</td>
                                     <td><?= $model->bank_account_name ?></td>
                                 </tr>

                                 <tr>
                                     <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('bank_account') ?>：</td>
                                     <td><?= $model->bank_account ?></td>
                                 </tr>
                                 <tr>
                                     <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('remark') ?>：</td>
                                     <td><?= $model->remark ?></td>
                                 </tr>

                                 <tr>
                                     <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('audit_status') ?>：</td>
                                     <td><?= \common\enums\AuditStatusEnum::getValue($model->audit_status)
                                         ?></td>
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

             </div>
         </div>
        <div class="box-footer text-center">
            <?php
                if($model->audit_status == \common\enums\AuditStatusEnum::SAVE){
                    echo Html::edit(['edit', 'id' => $model->id, 'returnUrl' => Url::getReturnUrl()]);
                    echo '&nbsp;';
                    echo Html::edit(['ajax-apply','id'=>$model->id], '提审', [
                        'class'=>'btn btn-success btn-sm',
                        'onclick' => 'rfTwiceAffirm(this,"提交审核", "确定提交吗？");return false;',
                    ]);
                }

                if($model->audit_status == \common\enums\AuditStatusEnum::PENDING ){
                    echo '&nbsp;';
                    echo Html::edit(['ajax-audit','id'=>$model->id], '审核', [
                        'class'=>'btn btn-success btn-sm',
                        'data-toggle' => 'modal',
                        'data-target' => '#ajaxModal',
                    ]);
                }
            ?>
        </div>
    </div>

</div>

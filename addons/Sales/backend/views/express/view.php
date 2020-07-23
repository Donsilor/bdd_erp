<?php

use common\helpers\Html;
use common\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\order\order */
/* @var $form yii\widgets\ActiveForm */

$this->title = '快递公司详情';
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
//
?>

<div class="box-body nav-tabs-custom">
    <h2 class="page-header"><?= $this->title ?> - <?= $model->name?> - <?= \common\enums\AuditStatusEnum::getValue($model->audit_status)?></h2>
    <?php echo Html::menuTab($tabList,$tab)?>
    <div class="row">
         <div class="col-xs-12">
             <div class="box">
                 <div class="col-xs-6" style="padding: 0px;">
                     <div class="box" style="margin-bottom: 0px;">
                         <div class="box-body table-responsive">
                             <table class="table table-hover">
                                 <tr>
                                     <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('name') ?>：</td>
                                     <td><?= $model->name ?></td>
                                 </tr>
                                 <tr>
                                     <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('express_man') ?>：</td>
                                     <td><?= $model->express_man ?></td>
                                 </tr>
                                 <tr>
                                     <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('express_phone') ?>：</td>
                                     <td><?= $model->express_phone ?></td>
                                 </tr>
                                 <tr>
                                     <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('company_man') ?>：</td>
                                     <td><?= $model->company_man ?></td>
                                 </tr>
                                 <tr>
                                     <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('company_phone') ?>：</td>
                                     <td><?= $model->company_phone ?></td>
                                 </tr>
                                 <tr>
                                     <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('settlement_way') ?>：</td>
                                     <td><?= $model->settlement_way??""?></td>
                                 </tr>
                                 <tr>
                                     <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('settlement_period') ?>：</td>
                                     <td><?= $model->settlement_period??"" ?></td>
                                 </tr>
                                 <tr>
                                     <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('settlement_account') ?>：</td>
                                     <td><?= $model->settlement_account ?></td>
                                 </tr>
                                 <tr>
                                     <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('delivery_scope') ?>：</td>
                                     <td><?= $model->delivery_scope??""?></td>
                                 </tr>
                                 <tr>
                                     <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('receive_time') ?>：</td>
                                     <td><?= $model->receive_time ?></td>
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
                                     <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('stop_receive_time') ?>：</td>
                                     <td><?= $model->stop_receive_time?\Yii::$app->formatter->asDatetime($model->stop_receive_time):''; ?></td>
                                 </tr>
                                 <tr>
                                     <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('start_receive_time') ?>：</td>
                                     <td><?= $model->start_receive_time?\Yii::$app->formatter->asDatetime($model->start_receive_time):''; ?></td>
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
                                     <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('sort') ?>：</td>
                                     <td><?= $model->sort ?></td>
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
                                     <?php if($model->cover){?><td class="col-xs-4 text-center"><?= \common\helpers\ImageHelper::fancyBox($model->cover,90,90) ?></td><?php } ?>
                                     <?php if($model->pact_file){?><td class="col-xs-4 text-center"><?= \common\helpers\ImageHelper::fancyBox($model->pact_file,90,90) ?></td><?php } ?>
                                     <?php if($model->cert_file){?><td class="col-xs-4 text-center"><?= \common\helpers\ImageHelper::fancyBox($model->cert_file,90,90) ?></td><?php } ?>
                                 </tr>
                                 <tr>
                                     <?php if($model->cover){?><td class="col-xs-4 text-center"><?= $model->getAttributeLabel('cover') ?>：</td><?php } ?>
                                     <?php if($model->pact_file){?><td class="col-xs-4 text-center"><?= $model->getAttributeLabel('pact_file') ?>：</td><?php } ?>
                                     <?php if($model->cert_file){?><td class="col-xs-4 text-center"><?= $model->getAttributeLabel('cert_file') ?>：</td><?php } ?>
                                 </tr>
                             </table>
                         </div>
                     </div>
                 </div>
             </div>
         </div>
        <div class="box-footer text-center">
            <?php
                echo Html::edit(['ajax-edit','id' => $model->id,'returnUrl' => Url::getReturnUrl()], '编辑', [
                    'data-toggle' => 'modal',
                    'data-target' => '#ajaxModalLg',
                ]);
                if($model->audit_status == \common\enums\AuditStatusEnum::SAVE){
                    echo '&nbsp;';
                    echo Html::edit(['ajax-apply','id'=>$model->id], '提审', [
                        'class'=>'btn btn-info btn-sm',
                        'onclick' => 'rfTwiceAffirm(this,"提交审核", "确定提交吗？");return false;',
                    ]);
                }
                if($model->audit_status == \common\enums\AuditStatusEnum::PENDING){
                    echo '&nbsp;';
                    echo Html::edit(['ajax-audit','id'=>$model->id], '审核', [
                        'class'=>'btn btn-success btn-sm',
                        'data-toggle' => 'modal',
                        'data-target' => '#ajaxModal',
                    ]);
                }
                echo '&nbsp;';
                echo Html::a('返回列表', ['index'], ['class' => 'btn btn-default btn-sm']);
            ?>
        </div>
    </div>
</div>



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
    <h2 class="page-header">供应商详情 - <?php echo $model->supplier_name?></h2>
    <?php echo Html::menuTab($tabList,$tab)?>

    <div class="row">
         <div class="col-xs-12">
             <div class="box">
                 <div class="col-xs-6" style="padding: 0px;">
                     <div class="box" style="margin-bottom: 0px;">
                         <div class="box-body table-responsive">
                             <table class="table table-hover">
                                 <tr>
                                     <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('supplier_name') ?>：</td>
                                     <td><?= $model->supplier_name ?></td>
                                 </tr>
                                 <tr>
                                     <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('supplier_code') ?>：</td>
                                     <td><?= $model->supplier_code ?></td>
                                 </tr>
                                 <tr>
                                     <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('supplier_tag') ?>：</td>
                                     <td><?= $model->supplier_tag ?></td>
                                 </tr>
                                 <tr>
                                     <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('balance_type') ?>：</td>
                                     <td><?= \addons\Supply\common\enums\BalanceTypeEnum::getValue($model->balance_type) ?></td>
                                 </tr>

                                 <tr>
                                     <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('business_scope') ?>：</td>
                                     <td><?= $model->business_scope ?></td>
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
                                     <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('business_no') ?>：</td>
                                     <td><?= $model->business_no ?></td>
                                 </tr>

                                 <tr>
                                     <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('tax_no') ?>：</td>
                                     <td><?= $model->tax_no ?></td>
                                 </tr>
                                 <tr>
                                     <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('business_address') ?>：</td>
                                     <td><?= $model->business_address ?></td>
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
                                     <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('telephone') ?>：</td>
                                     <td><?= $model->telephone ?></td>
                                 </tr>
                                 <tr>
                                     <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('bdd_contactor') ?>：</td>
                                     <td><?= $model->bdd_contactor ?></td>
                                 </tr>
                                 <tr>
                                     <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('bdd_mobile') ?>：</td>
                                     <td><?= $model->bdd_mobile ?></td>
                                 </tr>
                                 <tr>
                                     <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('bdd_telephone') ?>：</td>
                                     <td><?= $model->bdd_telephone ?></td>
                                 </tr>
                                 <tr>
                                     <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('address') ?>：</td>
                                     <td><?= $model->address ?></td>
                                 </tr>
                                 <tr>
                                     <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('remark') ?>：</td>
                                     <td><?= $model->remark ?></td>
                                 </tr>

                                 <tr>
                                     <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('pay_type') ?>：</td>
                                     <td><?= $model->pay_type ?></td>
                                 </tr>
                                 <tr><td>&nbsp;</td><td>&nbsp;</td></tr>
                             </table>
                         </div>
                     </div>
                 </div>
                 <div class="col-xs-12" style="padding: 0px;">
                     <div class="box">
                         <div class="box-body table-responsive" >
                             <table class="table table-hover">
                                 <tr>
                                     <?php if($model->contract_file){?><td class="col-xs-4 text-center"><?= \common\helpers\ImageHelper::fancyBox($model->contract_file,90,90) ?></td><?php } ?>
                                     <?php if($model->business_file){?><td class="col-xs-4 text-center"><?= \common\helpers\ImageHelper::fancyBox($model->business_file,90,90) ?></td><?php } ?>
                                     <?php if($model->tax_file){?><td class="col-xs-4 text-center"><?= \common\helpers\ImageHelper::fancyBox($model->tax_file,90,90) ?></td><?php } ?>
                                 </tr>
                                 <tr>
                                     <?php if($model->contract_file){?><td class="col-xs-4 text-center"><?= $model->getAttributeLabel('contract_file') ?>：</td><?php } ?>
                                     <?php if($model->business_file){?><td class="col-xs-4 text-center"><?= $model->getAttributeLabel('business_file') ?>：</td><?php } ?>
                                     <?php if($model->tax_file){?><td class="col-xs-4 text-center"><?= $model->getAttributeLabel('tax_file') ?>：</td><?php } ?>
                                 </tr>
                             </table>
                         </div>
                     </div>
                 </div>
             </div>
         </div>
        <div class="box-footer text-center">
            <?php
                echo Html::edit(['edit', 'id' => $model->id, 'returnUrl' => Url::getReturnUrl()]);
                if($model->audit_status == \common\enums\AuditStatusEnum::SAVE){
                    echo '&nbsp;';
                    echo Html::edit(['ajax-apply','id'=>$model->id], '提审', [
                        'class'=>'btn btn-success btn-sm',
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
            ?>
        </div>
    </div>
</div>



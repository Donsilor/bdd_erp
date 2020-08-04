<?php

use common\enums\GenderEnum;
use common\helpers\Html;
use common\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\order\order */
/* @var $form yii\widgets\ActiveForm */

$this->title = '配件款式详情';
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
//
?>

<div class="box-body nav-tabs-custom">
    <h2 class="page-header"><?php echo $this->title;?></h2>
    <?php echo Html::menuTab($tabList,$tab)?>
    <div class="row">
         <div class="col-xs-12">
             <div class="box">
                 <div class="col-xs-6">
                     <div class="box">
                         <div class="box-body table-responsive">
                             <table class="table table-hover">
                                 <tr>
                                     <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('gold_type') ?>：</td>
                                     <td><?= \Yii::$app->attr->valueName($model->gold_type)??''?></td>
                                 </tr>
                                 <tr>
                                     <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('style_sn') ?>：</td>
                                     <td><?= $model->style_sn ?></td>
                                 </tr>
                                 <tr>
                                     <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('gold_name') ?>：</td>
                                     <td><?= $model->gold_name ?></td>
                                 </tr>
                                 <tr>
                                     <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('audit_status') ?>：</td>
                                     <td><?= \common\enums\AuditStatusEnum::getValue($model->audit_status)?></td>
                                 </tr>
                                 <tr>
                                     <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('audit_time') ?>：</td>
                                     <td><?= \Yii::$app->formatter->asDatetime($model->audit_time) ?></td>
                                 </tr>
                                 <tr>
                                     <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('auditor_id') ?>：</td>
                                     <td><?= $model->auditor ? $model->auditor->username:''  ?></td>
                                 </tr>
                                 <tr>
                                     <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('audit_remark') ?>：</td>
                                     <td><?= $model->audit_remark ?></td>
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
                                     <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('creator_id') ?>：</td>
                                     <td><?= $model->creator ? $model->creator->username:''  ?></td>
                                 </tr>
                                 <tr>
                                     <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('created_at') ?>：</td>
                                     <td><?= \Yii::$app->formatter->asDatetime($model->created_at) ?></td>
                                 </tr>
                                 <tr>
                                     <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('updated_at') ?>：</td>
                                     <td><?= \Yii::$app->formatter->asDatetime($model->updated_at) ?></td>
                                 </tr>
                                 <tr>
                                     <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('sort') ?>：</td>
                                     <td><?= $model->sort ?></td>
                                 </tr>
                                 <tr>
                                     <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('status') ?>：</td>
                                     <td><?= \common\enums\StatusEnum::getValue($model->status)?></td>
                                 </tr>
                                 <tr>
                                     <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('remark') ?>：</td>
                                     <td><?= $model->remark ?></td>
                                 </tr>
                             </table>
                         </div>
                     </div>
                 </div>
             </div>
         </div>
        <div class="box-footer text-center">
            <?php
                echo Html::edit(['ajax-edit', 'id' => $model->id, 'returnUrl' => Url::getReturnUrl()], '编辑', [
                        'data-toggle' => 'modal',
                        'data-target' => '#ajaxModalLg',
                    ]);
            ?>
            <span class="btn btn-white" onclick="history.go(-1)">返回</span>
        </div>
    </div>
</div>



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

$this->title = '款式详情';
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
//
?>
    <div class="box-body nav-tabs-custom">
        <h2 class="page-header">款式详情 - <?php echo $model->style_sn?></h2>
        <?php echo Html::menuTab($tabList,$tab)?>
    <div class="tab-content">
        <div class="col-xs-12" style="padding-left: 0px;padding-right: 0px;">
            <div class="box">

                <div class="box-body table-responsive" style="padding-left: 0px;padding-right: 0px;">
                    <table class="table table-hover">
                        <tr>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('id') ?>：</td>
                            <td><?= $model->id ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('style_name') ?>：</td>
                            <td><?= $model->style_name ?></td>
                        </tr>                        
                        <tr>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('style_sn') ?>：</td>
                            <td><?= $model->style_sn ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('style_sex') ?>：</td>
                            <td><?= \addons\Style\common\enums\StyleSexEnum::getValue($model->style_sex) ?></td>
                        </tr>                        
                        <tr>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('style_cate_id') ?>：</td>
                            <td><?= $model->cate->name ?? '' ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('product_type_id') ?>：</td>
                            <td><?= $model->type->name ??'' ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('is_inlay') ?>：</td>
                            <td><?= \addons\Style\common\enums\InlayEnum::getValue($model->is_inlay) ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('style_source_id') ?>：</td>
                            <td><?= $model->source->name ??'' ?></td>
                        </tr>                                               
                        <tr>
                            <td class="col-xs-1 text-right">默认工厂：</td>
                            <td><?= $model->supplier ? $model->supplier->supplier_name : '' ?></td>
                        </tr>    
                        <tr>
                            <td class="col-xs-1 text-right">工厂模号：</td>
                            <td><?= $model->factory_mo ?? ''?></td>
                        </tr>                     
                        <tr>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('style_channel_id') ?>：</td>
                            <td> <?= $model->channel->name ?? '' ?></td>
                        </tr>                                                                      
                        <tr>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('is_made') ?>：</td>
                            <td><?= \common\enums\ConfirmEnum::getValue($model->is_made)?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('status') ?>：</td>
                            <td><?= \common\enums\StatusEnum::getValue($model->status)?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('creator_id') ?>：</td>
                            <td><?= $model->creator->username??'' ?></td>
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
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('audit_status') ?>：</td>
                            <td><?= \common\enums\AuditStatusEnum::getValue($model->audit_status)?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('auditor_id') ?>：</td>
                            <td><?= $model->auditor->username??'' ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('audit_remark') ?>：</td>
                            <td><?= $model->audit_remark ?></td>
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
            <div class="text-center" >

                <?php echo Html::edit(['ajax-edit','id'=>$model->id], '编辑', [
                    'class' => 'btn btn-primary btn-ms',
                    'data-toggle' => 'modal',
                    'data-target' => '#ajaxModalLg',
                ]); ?>
                <?php
                if($model->audit_status != AuditStatusEnum::PASS){
                    echo Html::edit(['ajax-audit','id'=>$model->id], '审核', [
                        'class'=>'btn btn-success btn-ms',
                        'data-toggle' => 'modal',
                        'data-target' => '#ajaxModal',
                    ]);
                }
                ?>

            </div>
        </div>


</div>    
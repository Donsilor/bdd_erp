<?php

use common\enums\GenderEnum;
use common\helpers\Html;
use common\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\order\order */
/* @var $form yii\widgets\ActiveForm */

$this->title = '客户详情';
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
                                     <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('customer_no') ?>：</td>
                                     <td><?= $model->customer_no ?></td>
                                 </tr>
                                 <tr>
                                     <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('realname') ?>：</td>
                                     <td><?= $model->realname ?></td>
                                 </tr>


                                 <tr>
                                     <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('mobile') ?>：</td>
                                     <td><?= $model->mobile ?></td>
                                 </tr>

                                 <tr>
                                     <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('wechat') ?>：</td>
                                     <td><?= $model->qq ?></td>
                                 </tr>
                                 <tr>
                                     <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('country_id') ?>：</td>
                                     <td><?= $model->country->title ?? "" ?></td>
                                 </tr>
                                 <tr>
                                     <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('province_id') ?>：</td>
                                     <td><?= $model->province->title ?? "" ?></td>
                                 </tr>
                                 <tr>
                                     <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('city_id') ?>：</td>
                                     <td><?= $model->city->title ?? "" ?></td>
                                 </tr>
                                 <tr>
                                     <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('address') ?>：</td>
                                     <td><?= $model->address ?></td>
                                 </tr>

                                 <tr>
                                     <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('remark') ?>：</td>
                                     <td><?= $model->remark ?></td>
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
                                     <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('channel_id') ?>：</td>
                                     <td><?= $model->channel->name ?? '' ?></td>
                                 </tr>
                                 <tr>
                                     <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('source_id') ?>：</td>
                                     <td><?= $model->source->name ?? '' ?></td>
                                 </tr>

                                 <tr>
                                     <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('level') ?>：</td>
                                     <td><?= \addons\Sales\common\enums\LevelEnum::getValue($model->level) ?></td>
                                 </tr>
                                 <tr>
                                     <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('follower_id') ?>：</td>
                                     <td><?= $model->follower->username ?? ''; ?></td>
                                 </tr>
                                 <tr>
                                     <td class="col-xs-3 text-right">交易单量：</td>
                                     <td></td>
                                 </tr>
                                 <tr>
                                     <td class="col-xs-3 text-right">交易订单总金额：</td>
                                     <td></td>
                                 </tr>
                                 <tr>
                                     <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('created_at') ?>：</td>
                                     <td><?= \Yii::$app->formatter->asDatetime($model->created_at) ?></td>
                                 </tr>
                                 <tr>
                                     <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('updated_at') ?>：</td>
                                     <td><?= \Yii::$app->formatter->asDatetime($model->updated_at) ?></td>
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
            ?>
            <span class="btn btn-white" onclick="history.go(-1)">返回</span>
        </div>
    </div>
</div>



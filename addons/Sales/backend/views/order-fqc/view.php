<?php

use common\helpers\Html;
use addons\Sales\common\enums\OrderStatusEnum;
use addons\Sales\common\enums\IsStockEnum;
use common\helpers\Url;
use yii\grid\GridView;
use common\helpers\AmountHelper;

/* @var $this yii\web\View */
/* @var $model common\models\order\order */
/* @var $form yii\widgets\ActiveForm */

$this->title = 'FQC质检';
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
//
?>
<div class="box-body nav-tabs-custom">
    <h2 class="page-header"><?php echo $this->title;?> - <?php echo $model->order_sn?></h2>
    <?php echo Html::menuTab($tabList,$tab)?>
    <div class="tab-content" >
        <div class="col-xs-12">
            <div class="box"  style="margin:0px">
                <div class="box-header" style="margin:0">
                    <h3 class="box-title"><i class="fa fa-info"></i> 订单信息</h3>
                </div>
                <div class=" table-responsive" >
                    <table class="table table-hover">
                        <tr>
                            <td class="col-xs-1 text-right no-border-top"><?= $model->getAttributeLabel('order_sn') ?>：</td>
                            <td class="col-xs-3 no-border-top"><?= $model->order_sn ?></td>                            
                            <td class="col-xs-1 text-right no-border-top"><?= $model->getAttributeLabel('language') ?>：</td>
                            <td class="col-xs-3 no-border-top"><?= common\enums\LanguageEnum::getValue($model->language) ?></td>
                            <td class="col-xs-1 text-right no-border-top"><?= $model->getAttributeLabel('currency') ?>：</td>
                            <td class="col-xs-3 no-border-top"><?= common\enums\CurrencyEnum::getValue($model->currency) ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('sale_channel_id') ?>：</td>
                            <td><?= $model->saleChannel->name ??'' ?></td>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('order_status') ?>：</td>
                            <td><?= addons\Sales\common\enums\OrderStatusEnum::getValue($model->order_status) ?></td>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('pay_type') ?>：</td>
                            <td><?= addons\Sales\common\enums\PayTypeEnum::getValue($model->pay_type) ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('delivery_status') ?>：</td>
                            <td><?= addons\Sales\common\enums\DeliveryStatusEnum::getValue($model->delivery_status) ?></td>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('distribute_status') ?>：</td>
                            <td><?= addons\Sales\common\enums\DistributeStatusEnum::getValue($model->distribute_status) ?></td>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('pay_status') ?>：</td>
                            <td><?= addons\Sales\common\enums\PayStatusEnum::getValue($model->pay_status) ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('customer_name') ?>：</td>
                            <td><?= $model->customer_name ?></td>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('customer_mobile') ?>：</td>
                            <td><?= $model->customer_mobile ?></td>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('customer_email') ?>：</td>
                            <td><?= $model->customer_email ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('delivery_status') ?>：</td>
                            <td><?= addons\Sales\common\enums\DeliveryStatusEnum::getValue($model->delivery_status) ?></td>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('express_id') ?>：</td>
                            <td><?= $model->express->name ?? '' ?></td>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('express_no') ?>：</td>
                            <td><?= $model->express_no ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('order_type') ?>：</td>
                            <td><?= addons\Sales\common\enums\OrderTypeEnum::getValue($model->order_type) ?></td>
                            <td class="col-xs-1 text-right"></td>
                            <td></td>
                            <td class="col-xs-1 text-right"></td>
                            <td></td>
                        </tr>
                    </table>
                </div>
                <!-- <div class="box-footer text-center">
                    
                </div>-->
            </div>
        </div>
    <!-- box end -->
    <!-- box begin -->
    <div class="col-xs-12">
            <div class="box"  style="margin:0px">
                <div class="box-header" style="margin:0">
                    <h3 class="box-title"><i class="fa fa-info"></i> 商品信息</h3>
                </div>
                <div class="table-responsive col-lg-12">
                    <?php $order = $model ?>
                     <?= GridView::widget([
                            'dataProvider' => $dataProvider,
                            'tableOptions' => ['class' => 'table table-hover'],
                            'columns' => [
                                [
                                    'class' => 'yii\grid\SerialColumn',
                                    'visible' => false,
                                ],
                                [
                                    'value'=>function($model){

                                    }
                                ],
                                [
                                    'attribute' => 'goods_image',
                                    'value' => function ($model) {
                                        return common\helpers\ImageHelper::fancyBox($model->goods_image);
                                    },
                                    'filter' => false,
                                    'format' => 'raw',
                                    'headerOptions' => ['width'=>'80'],
                                ],
                                [
                                        'attribute'=>'goods_name',
                                        'value' => 'goods_name'
                                ],
                                [
                                    'attribute'=>'goods_id',
                                    'value' => 'goods_id'
                                ],
                                [
                                        'attribute'=>'style_sn',
                                        'value' => 'style_sn'
                                ],
                                [
                                    'attribute'=>'qiban_sn',
                                    'value' => 'qiban_sn'
                                ],
                                [
                                    'attribute'=>'qiban_type',
                                    'value' => function($model){
                                        return \addons\Style\common\enums\QibanTypeEnum::getValue($model->qiban_type);
                                    }
                                ],

                                [
                                    'attribute'=>'goods_num',
                                    'value' => 'goods_num'
                                ],
                                [
                                    'attribute'=>'goods_price',
                                    'value' => function($model) {
                                        return common\helpers\AmountHelper::outputAmount($model->goods_price, 2,$model->currency);
                                    }
                                ],
                                [
                                    'attribute'=>'goods_discount',
                                    'value' => function($model) {
                                        return common\helpers\AmountHelper::outputAmount($model->goods_discount, 2,$model->currency);
                                    }
                                ],
                                [
                                    'attribute'=>'goods_pay_price',
                                    'value' => function($model) {
                                        return common\helpers\AmountHelper::outputAmount($model->goods_pay_price, 2,$model->currency);
                                    }
                                ],
                                [
                                        'attribute'=>'produce_sn',
                                        'value' => 'produce_sn'
                                ],
                                [
                                        'label'=>'布产状态',
                                        'value' =>function($model){
                                            return '未布产';
                                        }
                                ],
                                [
                                    'attribute' => 'is_stock',
                                    'value' => function ($model) {
                                        return IsStockEnum::getValue($model->is_stock);
                                    }
                                ],
                                [
                                    'attribute' => 'is_gift',
                                    'value' => function ($model) {
                                        return \addons\Sales\common\enums\IsGiftEnum::getValue($model->is_gift);
                                    }
                                ],
                            ]
                        ]); ?>
                </div>
            </div>
    </div>
        <div class="box-footer text-center">
            <?php
            if($model->delivery_status == \addons\Sales\common\enums\DeliveryStatusEnum::SAVE){
                echo Html::edit(['ajax-fqc', 'id' => $model->id, 'is_pass'=>\addons\Sales\common\enums\IsPassEnum::YES, 'returnUrl' => Url::getReturnUrl()], '质检通过',[
                    'onclick' => 'rfTwiceAffirm(this,"质检通过", "确定通过吗？");return false;',
                    'class'=>"btn btn-success btn-sm",
                ]);
                echo "&nbsp";
                echo Html::edit(['ajax-fqc', 'id' => $model->id, 'is_pass'=>\addons\Sales\common\enums\IsPassEnum::NO, 'returnUrl' => Url::getReturnUrl()], '质检不通过',[
                    'class'=>"btn btn-danger btn-sm",
                    'data-toggle' => 'modal',
                    'data-target' => '#ajaxModal',
                ]);
            }
            ?>
            <span class="btn btn-white" onclick="history.go(-1)">返回</span>
        </div>
</div>
<!-- tab-content end -->
</div>
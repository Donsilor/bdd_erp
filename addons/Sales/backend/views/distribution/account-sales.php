<?php

use addons\Sales\common\enums\DistributeStatusEnum;
use addons\Sales\common\enums\IsStockEnum;
use common\helpers\Html;
use addons\Sales\common\enums\OrderStatusEnum;
use common\helpers\Url;
use yii\grid\GridView;
use common\helpers\AmountHelper;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\order\order */
/* @var $form yii\widgets\ActiveForm */

$this->title = '待配货详情';
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
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('express_id') ?>：</td>
                            <td><?= $model->express->name ?? '' ?></td>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('express_no') ?>：</td>
                            <td><?= $model->express_no ?></td>
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
<?php $form = ActiveForm::begin([
    'id' => $model->formName(),
    'enableAjaxValidation' => true,
    'validationUrl' => Url::to(['account-sales', 'id' => $model['id']]),
    'fieldConfig' => [
        //'template' => "{label}{input}{hint}",
    ],
]); ?>
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
                            //'options' => ['style'=>'white-space:nowrap;'],
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
                                    'attribute' => 'goods_id',
                                    'format' => 'raw',
                                    'value' => function ($model, $key, $index, $column) use($order){
                                         if($model->is_gift){
                                             return "赠品无需销账";
                                         }else{
                                              if($order->distribute_status == DistributeStatusEnum::ALLOWED){
                                                    return  Html::input('text', 'goods_ids['.$model->id.']', $model->goods_id ,['class' => 'form-control','placeholder' => '请输入货号',]);
                                              }else{
                                                  return $model->goods_id??"";
                                              }
                                         }
                                    },
                                    'headerOptions' => ['width' => '160'],
                                ],
                                [
                                    'label' => '款号/起版号/批次号',
                                    'attribute'=>'goods_sn',
                                    'value' => 'goods_sn'
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
                                /*[
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
                                ],*/
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
    <div class="modal-footer">
        <div class="col-sm-12 text-center">
            <?php if($model->distribute_status == DistributeStatusEnum::ALLOWED){ ?>
                <?= Html::submitButton('销账', ['class' => 'btn btn-primary']) ?>
            <?php }else{
                echo Html::a('打印提货单',['print','id'=>$model->id],[
                'target'=>'_blank',
                'class'=>'btn btn-info btn-sm',
                ]);
            }?>
            <span class="btn btn-white" onclick="history.go(-1)">返回</span>
        </div>
    </div>
     <!-- box begin -->
     <?php ActiveForm::end(); ?>
</div>
<!-- tab-content end -->
</div>
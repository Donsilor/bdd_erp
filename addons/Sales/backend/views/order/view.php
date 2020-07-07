<?php

use common\helpers\Html;
use addons\Warehouse\common\enums\BillStatusEnum;
use common\enums\AuditStatusEnum;
use yii\grid\GridView;
use common\helpers\AmountHelper;

/* @var $this yii\web\View */
/* @var $model common\models\order\order */
/* @var $form yii\widgets\ActiveForm */

$this->title = '订单详情';
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
                            <td class="no-border-top"><?= $model->order_sn ?></td>                            
                            <td class="col-xs-1 text-right no-border-top"><?= $model->getAttributeLabel('language') ?>：</td>
                            <td class="no-border-top"><?= common\enums\LanguageEnum::getValue($model->language) ?></td>
                            <td class="col-xs-1 text-right no-border-top"><?= $model->getAttributeLabel('currency') ?>：</td>
                            <td class="no-border-top"><?= common\enums\CurrencyEnum::getValue($model->currency) ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('sale_channel_id') ?>：</td>
                            <td><?= $model->saleChannel->name ??'' ?></td>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('order_status') ?>：</td>
                            <td><?= addons\Sales\common\enums\OrderStatusEnum::getValue($model->order_status) ?></td>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('order_sn') ?>：</td>
                            <td><?= $model->order_sn ?></td>
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
                     <?= GridView::widget([
                                'dataProvider' => $dataProvider,
                                'tableOptions' => ['class' => 'table table-hover'],
                                'columns' => [
                                    [
                                        'class' => 'yii\grid\SerialColumn',
                                        'visible' => false,
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
                                            'attribute'=>'style_sn',
                                            'value' => 'style_sn'
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
                                            'attribute'=>'is_stock',
                                            'value' => function($model){
                                                    return $model->is_stock;
                                            }
                                    ],
                                    [
                                            'attribute'=>'is_gift',
                                            'value' => function($model){
                                                    return $model->is_gift;
                                            }
                                    ],
                                ]
                            ]); ?>
                </div>
               <div class="box-footer">
                   <div class="col-lg-12">
                      <div class="row">
                            <div class="col-lg-7 text-right"><label><?= $model->getAttributeLabel('goods_num') ?>：</label></div>
                            <div class="col-lg-5"><?= $model->goods_num ?></div>
                      </div> 
                      <div class="row">
                            <div class="col-lg-7 text-right"><label><?= $model->getAttributeLabel('account.goods_amount') ?>：</label></div>
                            <div class="col-lg-5"><?= AmountHelper::outputAmount($model->account->goods_amount ?? 0,2,$model->currency) ?></div>
                      </div> 
                      <div class="row">
                            <div class="col-lg-7 text-right"><label><?= $model->getAttributeLabel('account.shipping_fee') ?>：</label></div>
                            <div class="col-lg-5"><?= AmountHelper::outputAmount($model->account->shipping_fee ?? 0,2,$model->currency) ?></div>
                      </div> 
                       <div class="row">
                            <div class="col-lg-7 text-right"><label><?= $model->getAttributeLabel('account.tax_fee') ?>：</label></div>
                            <div class="col-lg-5"><?= AmountHelper::outputAmount($model->account->tax_fee ?? 0,2,$model->currency) ?></div>
                      </div> 
                      <div class="row">
                            <div class="col-lg-7 text-right"><label><?= $model->getAttributeLabel('account.safe_fee') ?>：</label></div>
                            <div class="col-lg-5"><?= AmountHelper::outputAmount($model->account->safe_fee ?? 0,2,$model->currency) ?></div>
                      </div> 
                      <div class="row">
                            <div class="col-lg-7 text-right"><label><?= $model->getAttributeLabel('account.order_amount') ?>：</label></div>
                            <div class="col-lg-5"><?= AmountHelper::outputAmount($model->account->order_amount ?? 0,2,$model->currency) ?></div>
                      </div>
                      <div class="row">
                            <div class="col-lg-7 text-right"><label><?= $model->getAttributeLabel('account.discount_amount') ?>：</label></div>
                            <div class="col-lg-5"><?= AmountHelper::outputAmount($model->account->discount_amount ?? 0,2,$model->currency) ?></div>
                      </div> 
                      <div class="row">
                            <div class="col-lg-7 text-right"><label><?= $model->getAttributeLabel('account.pay_amount') ?>：</label></div>
                            <div class="col-lg-5"><?= AmountHelper::outputAmount($model->account->pay_amount ?? 0,2,$model->currency) ?></div>
                      </div> 
                       <div class="row">
                            <div class="col-lg-7 text-right"><label><?= $model->getAttributeLabel('account.paid_amount') ?>：</label></div>
                            <div class="col-lg-5"><?= AmountHelper::outputAmount($model->account->paid_amount ?? 0,2,$model->currency) ?></div>
                      </div>  
                   </div><!-- end col-lg-6 -->       
               </div><!-- end footer -->                
            </div>
    </div>
   
    
     <!-- box begin -->
        <div class="col-xs-12">
            <div class="box"  style="margin:0px">
                <div class="box-header" style="margin:0">
                    <h3 class="box-title"><i class="fa fa-info"></i> 收货地址</h3>
                </div>
                <div class=" table-responsive" >
                    <table class="table table-hover">
                        <tr>
                            <td class="col-xs-1 text-right no-border-top"><?= $model->getAttributeLabel('order_sn') ?>：</td>
                            <td class="no-border-top"><?= $model->order_sn ?></td>
                            <td class="col-xs-1 text-right no-border-top"><?= $model->getAttributeLabel('order_sn') ?>：</td>
                            <td class="no-border-top"><?= $model->order_sn ?></td>
                            <td class="col-xs-1 text-right no-border-top"><?= $model->getAttributeLabel('order_sn') ?>：</td>
                            <td class="no-border-top"><?= $model->order_sn ?></td>
                        </tr>                      
                    </table>
                </div>
            </div>
        </div>
    <!-- box end -->
         <!-- box begin -->
        <div class="col-xs-12">
            <div class="box" style="margin:0px">
                <div class="box-header" style="margin:0">
                    <h3 class="box-title"><i class="fa fa-info"></i> 发票信息</h3>
                </div>
                <div class=" table-responsive" >
                    <table class="table table-hover">
                        <tr>
                            <td class="col-xs-1 text-right no-border-top"><?= $model->getAttributeLabel('order_sn') ?>：</td>
                            <td class="no-border-top"><?= $model->order_sn ?></td>
                            <td class="col-xs-1 text-right no-border-top"><?= $model->getAttributeLabel('order_sn') ?>：</td>
                            <td class="no-border-top"><?= $model->order_sn ?></td>
                            <td class="col-xs-1 text-right no-border-top"><?= $model->getAttributeLabel('order_sn') ?>：</td>
                            <td class="no-border-top"><?= $model->order_sn ?></td>
                        </tr>                                               
                    </table>
                </div>
            </div>
        </div>
    <!-- box end -->
    
</div>
<!-- tab-content end -->
</div>
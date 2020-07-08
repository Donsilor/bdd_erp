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
                            <div class="col-lg-8 text-right"><label><?= $model->getAttributeLabel('goods_num') ?>：</label></div>
                            <div class="col-lg-4"><?= $model->goods_num ?></div>
                      </div> 
                      <div class="row">
                            <div class="col-lg-8 text-right"><label><?= $model->getAttributeLabel('account.goods_amount') ?>：</label></div>
                            <div class="col-lg-4"><?= AmountHelper::outputAmount($model->account->goods_amount ?? 0,2,$model->currency) ?></div>
                      </div> 
                      <div class="row">
                            <div class="col-lg-8 text-right"><label><?= $model->getAttributeLabel('account.shipping_fee') ?>：</label></div>
                            <div class="col-lg-4"><?= AmountHelper::outputAmount($model->account->shipping_fee ?? 0,2,$model->currency) ?></div>
                      </div> 
                       <div class="row">
                            <div class="col-lg-8 text-right"><label><?= $model->getAttributeLabel('account.tax_fee') ?>：</label></div>
                            <div class="col-lg-4"><?= AmountHelper::outputAmount($model->account->tax_fee ?? 0,2,$model->currency) ?></div>
                      </div> 
                      <div class="row">
                            <div class="col-lg-8 text-right"><label><?= $model->getAttributeLabel('account.safe_fee') ?>：</label></div>
                            <div class="col-lg-4"><?= AmountHelper::outputAmount($model->account->safe_fee ?? 0,2,$model->currency) ?></div>
                      </div> 
                      <div class="row">
                            <div class="col-lg-8 text-right"><label><?= $model->getAttributeLabel('account.order_amount') ?>：</label></div>
                            <div class="col-lg-4"><?= AmountHelper::outputAmount($model->account->order_amount ?? 0,2,$model->currency) ?></div>
                      </div>
                      <div class="row">
                            <div class="col-lg-8 text-right"><label><?= $model->getAttributeLabel('account.discount_amount') ?>：</label></div>
                            <div class="col-lg-4"><?= AmountHelper::outputAmount($model->account->discount_amount ?? 0,2,$model->currency) ?></div>
                      </div> 
                      <div class="row">
                            <div class="col-lg-8 text-right"><label><?= $model->getAttributeLabel('account.pay_amount') ?>：</label></div>
                            <div class="col-lg-4" style="color:red"><?= AmountHelper::outputAmount($model->account->pay_amount ?? 0,2,$model->currency) ?></div>
                      </div> 
                       <div class="row">
                            <div class="col-lg-8 text-right"><label><?= $model->getAttributeLabel('account.paid_amount') ?>：</label></div>
                            <div class="col-lg-4" style="color:red"><?= AmountHelper::outputAmount($model->account->paid_amount ?? 0,2,$model->currency) ?></div>
                      </div>  
                   </div><!-- end col-lg-6 -->       
               </div><!-- end footer -->                
            </div>
    </div>
     <!-- box begin -->
        <div class="col-xs-12">
            <div class="box"  style="margin:0px">
                <div class="box-header" style="margin:0">
                    <h3 class="box-title"><i class="fa fa-info"></i> 收货人信息</h3>
                </div>
                <div class=" table-responsive" >
                <table class="table table-hover">
                    <thead>
                    	<tr><th>收货人</th><th>联系方式</th><th>国家</th><th>省份</th><th>城市</th><th>详细地址</th><th>邮编</th><th>操作</th></tr>
                    </thead>                    
                    <tbody>
                    	<tr>
                    		<td><?= $model->address->realname ?? '' ?></td>
                    		<td>
                    		<?php 
                    		$str = '';
                    		if($model->address) {
                    		    if($model->address->mobile) {
                    		        $str .= $model->address->mobile.'<br/>';
                    		    }
                    		    if($model->address->email) {
                    		        $str .= $model->address->email;
                    		    }
                    		}
                    		echo $str;
                            ?>
                            </td>
                        	<td><?= $model->address->country_name ??''?></td>
                        	<td><?= $model->address->province_name ??'' ?></td>
                        	<td><?= $model->address->city_name ??'' ?></td>                        	
                        	<td><?= $model->address->address_details ??'' ?></td> 
                        	<td><?= $model->address->zip_code ??'' ?></td> 
                        	<td><?= Html::edit(['ajax-edit-address','id'=>$model->id ,'returnUrl'=>$returnUrl], '编辑', [
                                        'class'=>'btn btn-primary btn-ms',
                                        'style'=>"margin-left:5px",
                                        'data-toggle' => 'modal',
                                        'data-target' => '#ajaxModal',
                                    ]);?>
                            </td> 
                                                 	
                    	</tr>
                    </tbody>
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
                    <thead>
                    	<tr><th>发票类型</th><th>发票抬头</th><th>纳税人识别号</th><th>是否电子发票</th><th>发票邮箱</th><th>发送次数</th><th>操作</th></tr>
                    </thead>
                    <tbody>
                    	<tr>
                    		<td><?= addons\Sales\common\enums\InvoiceTypeEnum::getValue($model->invoice->invoice_type ?? '') ?></td>
                        	<td><?= $model->invoice->invoice_title ??''?></td>
                        	<td><?= $model->invoice->tax_number ??''?></td>
                        	<td><?= addons\Sales\common\enums\InvoiceElectronicEnum::getValue($model->invoice->is_electronic ??'') ?></td>
                        	<td><?= $model->invoice->email ??'' ?></td>
                        	<td><?= $model->invoice->send_num ??'' ?></td>
                        	<td><?= Html::edit(['ajax-edit-invoice','id'=>$model->id ,'returnUrl'=>$returnUrl], '编辑', [
                                        'class'=>'btn btn-primary btn-ms',
                                        'style'=>"margin-left:5px",
                                        'data-toggle' => 'modal',
                                        'data-target' => '#ajaxModal',
                                    ]);?>
                                    <?= Html::edit(['ajax-send-invoice','id'=>$model->id ,'returnUrl'=>$returnUrl], '发送', [
                                        'class'=>'btn btn-success btn-ms',
                                        'style'=>"margin-left:5px",
                                        'data-toggle' => 'modal',
                                        'data-target' => '#ajaxModal',
                                    ]);?>
                            </td>                       	
                    	</tr>
                    </tbody>
                </table>
                </div>
            </div>
        </div>
    <!-- box end -->
    
</div>
<!-- tab-content end -->
</div>
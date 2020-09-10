<?php

use addons\Sales\common\enums\DistributeStatusEnum;
use addons\Sales\common\enums\PayStatusEnum;
use common\helpers\Html;
use addons\Sales\common\enums\OrderStatusEnum;
use addons\Sales\common\enums\IsStockEnum;
use common\helpers\Url;
use yii\grid\GridView;
use common\helpers\AmountHelper;
use addons\Style\common\enums\AttrIdEnum;

/* @var $this yii\web\View */
/* @var $model common\models\order\order */
/* @var $form yii\widgets\ActiveForm */

$this->title = '订单详情';
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
//
?>
<div class="box-body nav-tabs-custom">
    <h2 class="page-header"><?php echo $this->title; ?> - <?php echo $model->order_sn ?></h2>
    <?php echo Html::menuTab($tabList, $tab) ?>
    <div class="tab-content">
        <div class="col-xs-12">
            <div class="box" style="margin:0px">
                <div class="box-header" style="margin:0">
                    <h3 class="box-title"><i class="fa fa-info"></i> 订单信息</h3>
                </div>
                <div class=" table-responsive">
                    <table class="table table-hover">
                        <tr>
                            <td class="col-xs-1 text-right no-border-top"><?= $model->getAttributeLabel('order_sn') ?>
                                ：
                            </td>
                            <td class="col-xs-3 no-border-top"><?= $model->order_sn ?></td>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('sale_channel_id') ?>：</td>
                            <td><?= $model->saleChannel->name ?? '' ?></td>
                            <td class="col-xs-1 text-right no-border-top">语言/货币：</td>
                            <td class="col-xs-3 no-border-top"><?= common\enums\LanguageEnum::getValue($model->language) ?>
                                （<?= common\enums\CurrencyEnum::getValue($model->currency) ?>）
                            </td>

                        </tr>
                        <tr>

                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('order_type') ?>：</td>
                            <td><?= addons\Sales\common\enums\OrderTypeEnum::getValue($model->order_type) ?></td>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('order_status') ?>：</td>
                            <td><?= addons\Sales\common\enums\OrderStatusEnum::getValue($model->order_status) ?></td>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('pay_type') ?>：</td>
                            <td><?= $model->payType->name ?? '' ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('distribute_status') ?>：</td>
                            <td><?= addons\Sales\common\enums\DistributeStatusEnum::getValue($model->distribute_status) ?></td>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('delivery_status') ?>：</td>
                            <td><?= addons\Sales\common\enums\DeliveryStatusEnum::getValue($model->delivery_status) ?></td>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('pay_status') ?>：</td>
                            <td><?= addons\Sales\common\enums\PayStatusEnum::getValue($model->pay_status) ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('customer_name') ?>：</td>
                            <td><?= $model->customer_name ?></td>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('express_id') ?>：</td>
                            <td><?= $model->express->name ?? '' ?></td>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('refund_status') ?>：</td>
                            <td><?= addons\Sales\common\enums\RefundStatusEnum::getValue($model->refund_status) ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('customer_email') ?>：</td>
                            <td><?= $model->customer_email ?></td>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('express_no') ?>：</td>
                            <td><?= $model->express_no ?>
                                <?php if ($model->express_no) {
                                    echo Html::a("(物流轨迹)", ['logistics', 'id' => $model->id],
                                        [
                                            'style' => "text-decoration:underline;color:#3c8dbc",
                                            'class' => 'openIframe',
                                            'data-width' => '60%',
                                            'data-height' => '80%',
                                            'data-offset' => '20px',
                                        ]);
                                } ?></td>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('pay_sn') ?>：</td>
                            <td><?= $model->pay_sn ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('customer_mobile') ?>：</td>
                            <td><?= $model->customer_mobile ?></td>

                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('out_trade_no') ?>：</td>
                            <td><?= $model->out_trade_no ?></td>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('out_pay_no') ?>：</td>
                            <td><?= $model->out_pay_no ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('customer_account') ?>：</td>
                            <td><?= $model->customer_account ?></td>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('store_account') ?>：</td>
                            <td><?= $model->store_account ?></td>
                            <td class="col-xs-1 text-right"></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('pay_remark') ?>：</td>
                            <td colspan="5"><?= $model->pay_remark ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('remark') ?>：</td>
                            <td colspan="5"><?= $model->remark ?></td>
                        </tr>
                    </table>
                </div>
                <div class="box-footer text-center">
                    <?php
                    if ($model->order_status == \addons\Sales\common\enums\OrderStatusEnum::SAVE) {
                        echo Html::edit(['ajax-edit', 'id' => $model->id], '编辑', [
                            'data-toggle' => 'modal',
                            'class' => 'btn btn-primary btn-ms',
                            'data-target' => '#ajaxModalLg',
                        ]);
                    }
                    ?>
                    <?php
                    if ($model->order_status == \addons\Sales\common\enums\OrderStatusEnum::SAVE) {
                        echo Html::edit(['ajax-edit-fee', 'id' => $model->id], '编辑费用', [
                            'data-toggle' => 'modal',
                            'class' => 'btn btn-primary btn-ms',
                            'data-target' => '#ajaxModalLg',
                        ]);
                    }
                    ?>
                    <?php
                    if ($model->order_status == \addons\Sales\common\enums\OrderStatusEnum::SAVE) {
                        echo Html::edit(['ajax-apply', 'id' => $model->id], '提审', [
                            'class' => 'btn btn-success btn-ms',
                            'onclick' => 'rfTwiceAffirm(this,"提交审核", "确定提交吗？");return false;',
                        ]);
                    }
                    ?>
                    <?php
                    if ($model->targetType) {
                        $isAudit = Yii::$app->services->flowType->isAudit($model->targetType, $model->id);
                    } else {
                        $isAudit = true;
                    }
                    if ($model->order_status == \addons\Sales\common\enums\OrderStatusEnum::PENDING && $isAudit) {
                        echo Html::edit(['ajax-audit', 'id' => $model->id], '审核', [
                            'class' => 'btn btn-success btn-ms',
                            'data-toggle' => 'modal',
                            'data-target' => '#ajaxModalLg',
                        ]);
                    }
                    ?>
                    <?php
                    if ($model->order_status == \addons\Sales\common\enums\OrderStatusEnum::CONFORMED && empty($model->apply_id)) {
                        echo Html::edit(['ajax-purchase-apply', 'id' => $model->id], '申请采购', [
                            'class' => 'btn btn-success btn-ms',
                            'onclick' => 'rfTwiceAffirm(this,"申请采购", "确定申请采购吗？");return false;',
                        ]);
                    }
                    ?>
                    <?php
                    if ($model->pay_status == \addons\Sales\common\enums\PayStatusEnum::HAS_PAY
                        && !in_array($model->refund_status, [\addons\Sales\common\enums\RefundStatusEnum::HAS_RETURN])) {
                        echo Html::edit(['return', 'id' => $model->id], '退款', [
                            //'data-toggle' => 'modal',
                            'class' => 'btn btn-warning btn-ms openIframe',
                            //'data-target' => '#ajaxModalLg',
                            'data-width' => '90%', 'data-height' => '90%', 'data-offset' => '20px'
                        ]);
                    }
                    ?>
                    <?php
                    if ($model->distribute_status == DistributeStatusEnum::ALLOWED) {
                        echo Html::edit(['distribution/account-sales', 'id' => $model->id, 'returnUrl' => Url::getReturnUrl()], '配货', [
                            'class' => 'btn btn-primary btn-ms',
                        ]);
                    }
                    ?>
                    <?php
                    if ($model->distribute_status == DistributeStatusEnum::HAS_PEIHUO && $model->delivery_status == \addons\Sales\common\enums\DeliveryStatusEnum::SAVE) {
                        echo Html::a('发货质检', ['order-fqc/view', 'id' => $model->id, 'returnUrl' => Url::getReturnUrl()], ['class' => 'btn btn-primary btn-ms']);
                    }
                    ?>
                    <?php
                    if ($model->delivery_status == \addons\Sales\common\enums\DeliveryStatusEnum::TO_SEND) {
                        echo Html::a('发货', ['shipping/view', 'id' => $model->id, 'returnUrl' => Url::getReturnUrl()], ['class' => 'btn btn-primary btn-ms']);
                    }
                    ?>
                </div>
            </div>
        </div>
        <!-- box end -->
        <!-- box begin -->
        <div class="col-xs-12">
            <div class="box" style="margin:0px">
                <div class="box-header" style="margin:0">
                    <h3 class="box-title"><i class="fa fa-info"></i> 商品信息</h3>
                    <h6 style="color:red">*有款起版的商品（既有款号又有起版号的），下订单只能用起版号下单</h6>
                    <?php
                    if ($model->order_status == \addons\Sales\common\enums\OrderStatusEnum::SAVE) {
                        echo Html::create(['order-goods/edit', 'order_id' => $model->id], '期货商品', [
                            'class' => 'btn btn-primary btn-xs openIframe',
                            'data-width' => '90%',
                            'data-height' => '90%',
                            'data-offset' => '20px',
                        ]);
                        echo '&nbsp;';
                        echo Html::create(['order-goods/select-stock', 'order_id' => $model->id], '现货商品', [
                            'class' => 'btn btn-primary btn-xs openIframe',
                            'data-width' => '90%',
                            'data-height' => '90%',
                            'data-offset' => '20px',
                        ]);
                        echo '&nbsp;';
                        echo Html::create(['order-goods/select-diamond', 'order_id' => $model->id], '裸钻商品', [
                            'class' => 'btn btn-primary btn-xs openIframe',
                            'data-width' => '90%',
                            'data-height' => '90%',
                            'data-offset' => '20px',
                        ]);
                        echo '&nbsp;';
                        echo Html::create(['order-goods/select-gift', 'order_id' => $model->id], '赠品', [
                            'class' => 'btn btn-primary btn-xs openIframe',
                            'data-width' => '90%',
                            'data-height' => '90%',
                            'data-offset' => '20px',
                        ]);
                    }
                    ?>
                    <?php
                    //                    if($model->order_status == \addons\Sales\common\enums\OrderStatusEnum::CONFORMED) {
                    //                        echo Html::button('布产', [
                    //                            'class'=>'btn btn-success btn-xs',
                    //                            'onclick' => 'batchBuchan()',
                    //                        ]);
                    //                    }
                    ?>
                </div>
                <div class="table-responsive col-lg-12">
                    <?php $order = $model ?>
                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'tableOptions' => ['class' => 'table table-hover'],
                        'options' => ['id' => 'order-goods', 'style' => ' width:100%;white-space:nowrap;'],
                        'columns' => [
                            [
                                'class' => 'yii\grid\SerialColumn',
                                'visible' => false,
                            ],
                            [
                                'class' => 'yii\grid\CheckboxColumn',
                                'name' => 'id',  //设置每行数据的复选框属性
                                'headerOptions' => ['width' => '30'],
                            ],
                            'id',
                            [
                                'attribute' => 'goods_image',
                                'value' => function ($model) {
                                    return common\helpers\ImageHelper::fancyBox($model->goods_image);
                                },
                                'filter' => false,
                                'format' => 'raw',
                                'headerOptions' => ['width' => '80'],
                            ],
                            [
                                'attribute' => 'goods_name',
                                'value' => function ($model) {
                                    return "<div style='width:200px;white-space:pre-wrap;'>" . $model->goods_name . "</div>";
                                },
                                'format' => 'raw',
                            ],
                            [
                                'attribute' => 'goods_id',
                                'value' => 'goods_id',
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'attribute' => 'goods_sn',
                                'value' => 'goods_sn',
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'attribute' => 'style_sn',
                                'value' => function ($model) {
                                    $style_sn = $model->style_sn;
                                    $is_exist = Yii::$app->styleService->style->isExist($style_sn);
                                    if (!$is_exist && $style_sn) {
                                        $style_sn = "<font color='red'>{$style_sn}(erp无此款)</font>";
                                    }
                                    return $style_sn;
                                },
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'label' => '裸钻证书号',
                                'value' => function ($model) {
                                    $order_goods_attr = \addons\Sales\common\models\OrderGoodsAttribute::find()->where(['id' => $model->id, 'attr_id' => \addons\Style\common\enums\AttrIdEnum::DIA_CERT_NO])->one();
                                    $cert_id = $order_goods_attr->attr_value ?? '';
                                    return $cert_id;
                                }
                            ],
                            [
                                'label' => '证书类型',
                                'value' => function ($model) {
                                    return $model->attr[AttrIdEnum::DIA_CERT_TYPE] ?? "";
                                },
                            ],
                            /* [
                                'attribute'=>'qiban_sn',
                                'value' => 'qiban_sn'
                            ], */
                            [
                                'attribute' => 'qiban_type',
                                'value' => function ($model) {
                                    $qiban_sn = $model->qiban_sn;
                                    $is_exist = Yii::$app->styleService->qiban->isExist($qiban_sn);
                                    if (!$is_exist && $qiban_sn) {
                                        $qiban_sn = "<font color='red'>{$qiban_sn}（erp无此起版号）</font>";
                                    }
                                    return \addons\Style\common\enums\QibanTypeEnum::getValue($model->qiban_type) . '<br/>' . $qiban_sn;
                                },
                                'format' => 'raw',
                            ],
                            [
                                'attribute' => 'jintuo_type',
                                'value' => function ($model) {
                                    return \addons\Style\common\enums\JintuoTypeEnum::getValue($model->jintuo_type);
                                }
                            ],
                            [
                                'attribute' => 'style_cate_id',
                                'value' => function ($model) {
                                    return $model->cate->name ?? '';
                                },
                                'format' => 'raw',
                            ],
                            [
                                'attribute' => 'product_type_id',
                                'value' => function ($model) {
                                    return $model->type->name ?? '';
                                },
                                'format' => 'raw',
                            ],
                            [
                                'attribute' => 'goods_num',
                                'value' => 'goods_num'
                            ],
                            [
                                'attribute' => 'goods_price',
                                'value' => function ($model) {
                                    return common\helpers\AmountHelper::outputAmount($model->goods_price, 2, $model->currency);
                                }
                            ],
                            [
                                'attribute' => 'goods_discount',
                                'value' => function ($model) {
                                    return common\helpers\AmountHelper::outputAmount($model->goods_discount, 2, $model->currency);
                                }
                            ],
                            [
                                'attribute' => 'goods_pay_price',
                                'value' => function ($model) {
                                    return common\helpers\AmountHelper::outputAmount($model->goods_pay_price, 2, $model->currency);
                                }
                            ],
                            [
                                'class' => 'yii\grid\CheckboxColumn',
                                'name' => 'id',  //设置每行数据的复选框属性
                                'headerOptions' => ['width' => '30'],
                            ],
                            /* [
                                    'attribute'=>'produce_sn',
                                    'value' => 'produce_sn'
                            ], */
                            [
                                'attribute' => 'bc_status',
                                'value' => function ($model) {
                                    return \addons\Supply\common\enums\BuChanEnum::getValue($model->bc_status) ?? '未布产' . '<br/>' . $model->produce_sn;
                                },
                                'format' => 'raw',
                            ],
                            [
                                'attribute' => 'is_return',
                                'value' => function ($model) {
                                    $str = "";
                                    if (in_array($model->is_return,
                                            [\addons\Sales\common\enums\IsReturnEnum::APPLY, \addons\Sales\common\enums\IsReturnEnum::HAS_RETURN])
                                    && !empty($model->return_id)) {
                                        $str .= Html::a("(" .$model->return_no. ")", ['return/view', 'id' => $model->return_id, 'returnUrl' => Url::getReturnUrl()], ['style' => "text-decoration:underline;color:#3c8dbc"]);
                                    }
                                    return \addons\Sales\common\enums\IsReturnEnum::getValue($model->is_return) . $str ?? '未操作';
                                },
                                'format' => 'raw',
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
                            [
                                'label' => '材质',
                                'value' => function ($model) {
                                    return $model->attr[AttrIdEnum::MATERIAL] ?? "";
                                },
                            ],
                            [
                                'label' => '金料颜色',
                                'value' => function ($model) {
                                    return $model->attr[AttrIdEnum::MATERIAL_COLOR] ?? "";
                                },

                            ],
                            [
                                'label' => '金重（g）',
                                'value' => function ($model) {
                                    return $model->attr[AttrIdEnum::JINZHONG] ?? "";
                                },
                            ],
                            [
                                'label' => '美号',
                                'value' => function ($model) {
                                    return $model->attr[AttrIdEnum::FINGER] ?? "";
                                },
                            ],
                            [
                                'label' => '港号',
                                'value' => function ($model) {
                                    return $model->attr[AttrIdEnum::PORT_NO] ?? "";
                                },
                            ],
                            [
                                'label' => '链长（cm）',
                                'value' => function ($model) {
                                    return $model->attr[AttrIdEnum::CHAIN_LENGTH] ?? "";
                                },
                            ],
                            [
                                'label' => '镶口',
                                'value' => function ($model) {
                                    return $model->attr[AttrIdEnum::XIANGKOU] ?? "";
                                },
                            ],
                            [
                                'label' => '表面工艺',
                                'value' => function ($model) {
                                    return $model->attr[AttrIdEnum::FACEWORK] ?? "";
                                },
                            ],
                            [
                                'label' => '主石类型',
                                'value' => function ($model) {
                                    return $model->attr[AttrIdEnum::MAIN_STONE_TYPE] ?? "";
                                },
                            ],
                            [
                                'label' => '主石石重和数量',
                                'value' => function ($model) {
                                    $main_stone_weight = $model->attr[AttrIdEnum::MAIN_STONE_WEIGHT] ?? "无";
                                    $main_stone_num = $model->attr[AttrIdEnum::MAIN_STONE_NUM] ?? "无";
                                    $main_stone_weight = $main_stone_weight == '' ? "无" : $main_stone_weight;
                                    $main_stone_num = $main_stone_num == '' ? "无" : $main_stone_num;
                                    return $main_stone_weight . '/' . $main_stone_num;
                                },

                            ],
                            [
                                'label' => '主石规格(颜色/净度/切工/抛光/对称/荧光)',
                                'value' => function ($model) {
                                    $main_stone_color = $model->attr[AttrIdEnum::MAIN_STONE_COLOR] ?? "无";
                                    $main_stone_clarity = $model->attr[AttrIdEnum::MAIN_STONE_CLARITY] ?? "无";
                                    $main_stone_cut = $model->attr[AttrIdEnum::MAIN_STONE_CUT] ?? "无";
                                    $main_stone_polish = $model->attr[AttrIdEnum::MAIN_STONE_POLISH] ?? "无";
                                    $main_stone_symmetry = $model->attr[AttrIdEnum::MAIN_STONE_SYMMETRY] ?? "无";
                                    $main_stone_fluorescence = $model->attr[AttrIdEnum::MAIN_STONE_FLUORESCENCE] ?? "无";
                                    return $main_stone_color . '/' . $main_stone_clarity . '/' . $main_stone_cut .
                                        '/' . $main_stone_polish . '/' . $main_stone_symmetry . '/' . $main_stone_fluorescence;
                                },

                            ],
                            [
                                'label' => '副石石重和数量',
                                'value' => function ($model) {
                                    $side_stone_weight = $model->attr[AttrIdEnum::SIDE_STONE1_WEIGHT] ?? "无";
                                    $side_stone_num = $model->attr[AttrIdEnum::SIDE_STONE1_NUM] ?? "无";
                                    $side_stone_weight = $side_stone_weight == '' ? "无" : $side_stone_weight;
                                    $side_stone_num = $side_stone_num == '' ? "无" : $side_stone_num;
                                    return $side_stone_weight . '/' . $side_stone_num;
                                },

                            ],
                            [
                                'label' => '副石规格(颜色/净度)',
                                'value' => function ($model) {
                                    $side_stone_color = $model->attr[AttrIdEnum::SIDE_STONE1_COLOR] ?? "无";
                                    $side_stone_clarity = $model->attr[AttrIdEnum::SIDE_STONE1_CLARITY] ?? "无";
                                    $side_stone_color = $side_stone_color == '' ? "无" : $side_stone_color;
                                    $side_stone_clarity = $side_stone_clarity == '' ? "无" : $side_stone_clarity;
                                    return $side_stone_color . '/' . $side_stone_clarity;
                                },

                            ],
                            'remark',
                            [
                                'class' => 'yii\grid\CheckboxColumn',
                                'name' => 'id',  //设置每行数据的复选框属性
                                'headerOptions' => ['width' => '30'],
                            ],
                            [
                                'class' => 'yii\grid\ActionColumn',
                                'header' => '操作',
                                //'headerOptions' => ['width' => '150'],
                                'template' => '{view} {edit} {delete} <br/>{stock} {untie} {apply-edit} ',
                                'buttons' => [
                                    'view' => function ($url, $model, $key) {
                                        return Html::edit(['order-goods/view', 'id' => $model->id, 'order_id' => $model->order_id, 'returnUrl' => Url::getReturnUrl()], '详情', [
                                            'class' => 'btn btn-info btn-xs',
                                        ]);
                                    },
                                    'edit' => function ($url, $model, $key) use ($order) {
                                        if ($order->order_status == OrderStatusEnum::SAVE) {
                                            if ($model->product_type_id == 1) {
                                                return Html::edit(['order-goods/edit-diamond', 'id' => $model->id], '编辑', ['class' => 'btn btn-primary btn-xs openIframe', 'data-width' => '90%', 'data-height' => '90%', 'data-offset' => '20px']);
                                            } elseif ($model->is_gift == \addons\Sales\common\enums\IsGiftEnum::YES) {
                                                return Html::edit(['order-goods/edit-gift', 'id' => $model->id], '编辑', ['class' => 'btn btn-primary btn-xs openIframe', 'data-width' => '90%', 'data-height' => '90%', 'data-offset' => '20px']);
                                            } elseif ($model->is_stock == IsStockEnum::NO) {
                                                return Html::edit(['order-goods/edit', 'id' => $model->id], '编辑', ['class' => 'btn btn-primary btn-xs openIframe', 'data-width' => '90%', 'data-height' => '90%', 'data-offset' => '20px']);
                                            } else {
                                                return Html::edit(['order-goods/edit-stock', 'id' => $model->id], '编辑', ['class' => 'btn btn-primary btn-xs openIframe', 'data-width' => '90%', 'data-height' => '90%', 'data-offset' => '20px']);
                                            }

                                        }
                                    },
                                    'stock' => function ($url, $model, $key) use ($order) {
                                        if ($order->order_status == OrderStatusEnum::SAVE && $model->is_stock == IsStockEnum::NO) {
                                            return Html::edit(['order-goods/stock', 'id' => $model->id], '绑定现货', ['class' => 'btn btn-primary btn-xs', 'data-toggle' => 'modal', 'data-target' => '#ajaxModalLg',]);
                                        }
                                    },
                                    'untie' => function ($url, $model, $key) use ($order) {
                                        if ($order->order_status == OrderStatusEnum::SAVE && $model->is_stock == IsStockEnum::YES && $model->product_type_id != 1 && $model->is_gift == \addons\Sales\common\enums\IsGiftEnum::NO) {
                                            return Html::edit(['order-goods/untie', 'id' => $model->id], '解绑', [
                                                'class' => 'btn btn-primary btn-xs',
                                                'onclick' => 'rfTwiceAffirm(this,"解绑现货", "确定解绑吗？");return false;',
                                            ]);
                                        }

                                    },
//                                            'apply-edit' =>function($url, $model, $key) use($order){
//                                                if($order->order_status == OrderStatusEnum::CONFORMED) {
//                                                    return Html::edit(['order-goods/apply-edit','id' => $model->id],'申请编辑',['class' => 'btn btn-primary btn-xs openIframe','data-width'=>'90%','data-height'=>'90%','data-offset'=>'20px']);
//                                                }
//                                            },
                                    'delete' => function ($url, $model, $key) use ($order) {
                                        if ($order->order_status == OrderStatusEnum::SAVE) {
                                            return Html::delete(['order-goods/delete', 'id' => $model->id, 'order_id' => $model->order_id, 'returnUrl' => Url::getReturnUrl()], '删除', ['class' => 'btn btn-danger btn-xs']);
                                        }
                                    },
                                ]
                            ]
                        ]
                    ]); ?>
                </div>
                <div class="box-footer">
                    <div class="col-lg-12">
                        <div class="row">
                            <div class="col-lg-8 text-right"><label><?= $model->getAttributeLabel('goods_num') ?>
                                    ：</label></div>
                            <div class="col-lg-4"><?= $model->goods_num ?></div>
                        </div>
                        <div class="row">
                            <div class="col-lg-8 text-right">
                                <label><?= $model->getAttributeLabel('account.goods_amount') ?>：</label></div>
                            <div class="col-lg-4"><?= AmountHelper::outputAmount($model->account->goods_amount ?? 0, 2, $model->currency) ?></div>
                        </div>
                        <div class="row">
                            <div class="col-lg-8 text-right">
                                <label><?= $model->getAttributeLabel('account.shipping_fee') ?>：</label></div>
                            <div class="col-lg-4"><?= AmountHelper::outputAmount($model->account->shipping_fee ?? 0, 2, $model->currency) ?></div>
                        </div>
                        <div class="row">
                            <div class="col-lg-8 text-right"><label><?= $model->getAttributeLabel('account.tax_fee') ?>
                                    ：</label></div>
                            <div class="col-lg-4"><?= AmountHelper::outputAmount($model->account->tax_fee ?? 0, 2, $model->currency) ?></div>
                        </div>
                        <div class="row">
                            <div class="col-lg-8 text-right"><label><?= $model->getAttributeLabel('account.safe_fee') ?>
                                    ：</label></div>
                            <div class="col-lg-4"><?= AmountHelper::outputAmount($model->account->safe_fee ?? 0, 2, $model->currency) ?></div>
                        </div>
                        <div class="row">
                            <div class="col-lg-8 text-right">
                                <label><?= $model->getAttributeLabel('account.order_amount') ?>：</label></div>
                            <div class="col-lg-4"><?= AmountHelper::outputAmount($model->account->order_amount ?? 0, 2, $model->currency) ?></div>
                        </div>
                        <div class="row">
                            <div class="col-lg-8 text-right">
                                <label><?= $model->getAttributeLabel('account.discount_amount') ?>：</label></div>
                            <div class="col-lg-4"><?= AmountHelper::outputAmount($model->account->discount_amount ?? 0, 2, $model->currency) ?></div>
                        </div>
                        <div class="row">
                            <div class="col-lg-8 text-right">
                                <label><?= $model->getAttributeLabel('account.pay_amount') ?>：</label></div>
                            <div class="col-lg-4"
                                 style="color:red"><?= AmountHelper::outputAmount($model->account->pay_amount ?? 0, 2, $model->currency) ?></div>
                        </div>
                        <div class="row">
                            <div class="col-lg-8 text-right">
                                <label><?= $model->getAttributeLabel('account.paid_amount') ?>：</label></div>
                            <div class="col-lg-4"
                                 style="color:red"><?= AmountHelper::outputAmount($model->account->paid_amount ?? 0, 2, $model->currency) ?></div>
                        </div>
                        <div class="row">
                            <div class="col-lg-8 text-right">
                                <label><?= $model->getAttributeLabel('account.refund_amount') ?>：</label></div>
                            <div class="col-lg-4"
                                 style="color:red"><?= AmountHelper::outputAmount($model->account->refund_amount ?? 0, 2, $model->currency) ?></div>
                        </div>
                    </div><!-- end col-lg-6 -->
                </div><!-- end footer -->
            </div>
        </div>
        <!-- box begin -->
        <div class="col-xs-12">
            <div class="box" style="margin:0px">
                <div class="box-header" style="margin:0">
                    <h3 class="box-title"><i class="fa fa-info"></i> 收货人信息</h3>
                </div>
                <div class=" table-responsive">
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th>收货人</th>
                            <th>联系方式</th>
                            <th>国家</th>
                            <th>省份</th>
                            <th>城市</th>
                            <th>详细地址</th>
                            <th>邮编</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td><?= $model->address->realname ?? '' ?></td>
                            <td>
                                <?php
                                $str = '';
                                if ($model->address) {
                                    if ($model->address->mobile) {
                                        $str .= $model->address->mobile . '<br/>';
                                    }
                                    if ($model->address->email) {
                                        $str .= $model->address->email;
                                    }
                                }
                                echo $str;
                                ?>
                            </td>
                            <td><?= $model->address->country_name ?? '' ?></td>
                            <td><?= $model->address->province_name ?? '' ?></td>
                            <td><?= $model->address->city_name ?? '' ?></td>
                            <td><?= $model->address->address_details ?? '' ?></td>
                            <td><?= $model->address->zip_code ?? '' ?></td>
                            <td><?= Html::edit(['ajax-edit-address', 'id' => $model->id, 'returnUrl' => $returnUrl], '编辑', [
                                    'class' => 'btn btn-primary btn-ms',
                                    'style' => "margin-left:5px",
                                    'data-toggle' => 'modal',
                                    'data-target' => '#ajaxModal',
                                ]); ?>
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
                <div class=" table-responsive">
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th>是否开发票</th>
                            <th>发票抬头</th>
                            <th>纳税人识别号</th>
                            <th>发票类型</th>
                            <th>发票邮箱</th>
                            <th>发送次数</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td><?= addons\Sales\common\enums\IsInvoiceEnum::getValue($model->invoice->is_invoice ?? '') ?></td>
                            <td><?= $model->invoice->invoice_title ?? '' ?></td>
                            <td><?= $model->invoice->tax_number ?? '' ?></td>
                            <td><?= addons\Sales\common\enums\InvoiceTypeEnum::getValue($model->invoice->invoice_type ?? '') ?></td>
                            <td><?= $model->invoice->email ?? '' ?></td>
                            <td><?= $model->invoice->send_num ?? '' ?></td>
                            <td><?= Html::edit(['ajax-edit-invoice', 'id' => $model->id, 'returnUrl' => $returnUrl], '编辑', [
                                    'class' => 'btn btn-primary btn-ms',
                                    'style' => "margin-left:5px",
                                    'data-toggle' => 'modal',
                                    'data-target' => '#ajaxModal',
                                ]); ?>
                                <?= Html::edit(['ajax-send-invoice', 'id' => $model->id, 'returnUrl' => $returnUrl], '发送', [
                                    'class' => 'btn btn-success btn-ms',
                                    'style' => "margin-left:5px",
                                    'data-toggle' => 'modal',
                                    'data-target' => '#ajaxModal',
                                ]); ?>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- box end -->

        <div id="flow">

        </div>
    </div>
    <!-- tab-content end -->
</div>

<script>
    function batchBuchan() {
        appConfirm("提交布产", '确定布产吗', function (value) {
            switch (value) {
                case "defeat":
                    var ids = $("#order-goods").yiiGridView("getSelectedRows");
                    if (ids == '') {
                        rfMsg('请选中商品明细')
                        return false;
                    }
                    var url = "<?= Url::to(['order-goods/buchan'])?>?ids=" + ids;
                    window.location = url;

                    break;
                default:
            }
        });
    }

</script>
<script>
    $("#flow").load("<?= \common\helpers\Url::to(['../common/flow/audit-view', 'flow_type_id' => $model->targetType, 'target_id' => $model->id])?>");

    $('#order-goods table tbody tr').each(function () {
        var id = Number($(this).attr('data-key'));
        var arr = <?= $return ?? []?>;
        if ($.inArray(id, arr) != -1) {
            $(this).children().each(function () {
                $(this).attr('style', "position:relative;");
                $(this).append('<div style="width:100%;position:absolute;top:14px;left:-1px;border-bottom:solid 1px red;"></div><div style="width:100%;position:absolute;top:19px;left:-1px;border-bottom:solid 1px red;"></div>');
            });
        }
    });
</script>
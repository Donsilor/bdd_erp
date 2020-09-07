<?php

use addons\Warehouse\common\enums\BillStatusEnum;
use addons\Purchase\common\enums\ReceiptGoodsStatusEnum;
use common\enums\WhetherEnum;
use common\helpers\Html;
use common\helpers\Url;
use kartik\select2\Select2;
use yii\grid\GridView;
use kartik\daterange\DateRangePicker;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('bill_t_goods', '其他入库单详情');
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box-body nav-tabs-custom">
    <h2 class="page-header"><?= $this->title; ?> - <?= $bill->bill_no ?>
        - <?= \addons\Warehouse\common\enums\BillStatusEnum::getValue($bill->bill_status) ?></h2>
    <?php echo Html::menuTab($tabList, $tab) ?>
    <div class="box-tools" style="float:right;margin-top:-40px; margin-right: 20px;">
        <?php
        if ($bill->bill_status == \addons\Warehouse\common\enums\BillStatusEnum::SAVE) {
            echo Html::create(['ajax-edit', 'bill_id' => $bill->id], '新增货品', [
                'class' => 'btn btn-primary btn-xs',
                'data-toggle' => 'modal',
                'data-target' => '#ajaxModal',
            ]);
            echo '&nbsp;';
            echo Html::edit(['edit-all', 'bill_id' => $bill->id], '编辑货品', ['class' => 'btn btn-info btn-xs']);
            echo '&nbsp;';
            echo Html::edit(['ajax-upload', 'bill_id' => $bill->id], '批量导入', [
                'class' => 'btn btn-success btn-xs',
                'data-toggle' => 'modal',
                'data-target' => '#ajaxModal',
            ]);
            echo '&nbsp;';
            echo Html::tag('span', '刷新价格', ["class" => "btn btn-warning btn-xs jsBatchStatus", "data-grid" => "grid", "data-url" => Url::to(['update-price']),]);
            echo '&nbsp;';
            echo Html::tag('span', '批量删除', ["class" => "btn btn-danger btn-xs jsBatchStatus", "data-grid" => "grid", "data-url" => Url::to(['batch-delete']),]);
        }
        ?>
    </div>
    <div class="tab-content">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-body table-responsive">
                    <?php echo Html::batchButtons(false) ?>
                    <span style="color:red;">Ctrl+F键可快速查找字段名</span>
                    <span style="font-size:16px">
                        <!--<span style="font-weight:bold;">明细汇总：</span>-->
                        货品总数：<span style="color:green;"><?= $bill->goods_num?></span>
                        总成本价：<span style="color:green;"><?= $bill->total_cost?></span>
                    </span>
                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        //'tableOptions' => ['class' => 'table table-hover'],
                        'options' => ['style' => 'white-space:nowrap;'],
                        'rowOptions' => function ($model, $key, $index) {
                            if ($index % 2 === 0) {
                                return ['style' => 'background:#E1FFFF'];
                            }
                        },
                        'showFooter' => true,//显示footer行
                        'id' => 'grid',
                        'columns' => [
                            [
                                'class' => 'yii\grid\SerialColumn',
                                'visible' => false,
                            ],
                            [
                                'class' => 'yii\grid\CheckboxColumn',
                                'name' => 'id',  //设置每行数据的复选框属性
                            ],
                            [
                                'attribute' => 'id',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#feeeed;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#feeeed;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = "Total：";
                                    return $model->id ?? 0;
                                },
                                'filter' => false,
                            ],
                            [
                                'class' => 'yii\grid\ActionColumn',
                                'header' => '操作',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#feeeed;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#feeeed;'],
                                'contentOptions' => ['style' => ['white-space' => 'nowrap']],
                                'template' => '{image} {edit} {delete}',
                                'buttons' => [
                                    'image' => function ($url, $model, $key) {
                                        return Html::edit(['ajax-image', 'id' => $model->id], '图片', [
                                            'class' => 'btn btn-warning btn-xs',
                                            'data-toggle' => 'modal',
                                            'data-target' => '#ajaxModal',
                                        ]);
                                    },
                                    'edit' => function ($url, $model, $key) use ($bill) {
                                        if ($bill->bill_status == BillStatusEnum::SAVE) {
                                            return Html::edit(['edit', 'id' => $model->id, 'bill_id' => $bill->id], '编辑', [
                                                'class' => 'btn btn-primary btn-xs openIframe',
                                                'data-width' => '90%',
                                                'data-height' => '90%',
                                                'data-offset' => '20px',
                                            ]);
                                        }
                                    },
                                    'delete' => function ($url, $model, $key) use ($bill) {
                                        if ($bill->bill_status == BillStatusEnum::SAVE) {
                                            return Html::delete(['delete', 'id' => $model->id], '删除', [
                                                'class' => 'btn btn-danger btn-xs',
                                            ]);
                                        }
                                    },
                                ],
                            ],
                            [
                                'attribute' => 'style_cate_id',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#feeeed;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#feeeed;'],
                                //'value' => 'styleCate.name',
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('style_cate_id');
                                    return $model->styleCate->name ?? "";
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'style_cate_id', $model->getCateMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:120px;'
                                ]),
                            ],
                            [
                                'attribute' => 'product_type_id',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#feeeed;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#feeeed;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('product_type_id');
                                    return $model->productType->name ?? "";
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'product_type_id', $model->getProductMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:120px;'

                                ]),
                            ],
                            [
                                'attribute' => 'goods_id',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#feeeed;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#feeeed;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('goods_id');
                                    return $model->goods_id ?? "";
                                },
                                'filter' => Html::activeTextInput($searchModel, 'goods_id', [
                                    'class' => 'form-control',
                                    'style' => 'width:100px;'
                                ]),
                            ],
                            [
                                'attribute' => 'style_sn',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#feeeed;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#feeeed;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('style_sn');
                                    return $model->style_sn ?? "";
                                },
                                'filter' => Html::activeTextInput($searchModel, 'style_sn', [
                                    'class' => 'form-control',
                                    'style' => 'width:100px;'
                                ]),
                            ],
                            [
                                'attribute' => 'qiban_sn',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#feeeed;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#feeeed;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('qiban_sn');
                                    return $model->qiban_sn ?? "";
                                },
                                'filter' => Html::activeTextInput($searchModel, 'qiban_sn', [
                                    'class' => 'form-control',
                                    'style' => 'width:100px;'
                                ]),
                            ],
                            [
                                'attribute' => 'goods_name',
                                //'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#feeeed;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#feeeed;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('goods_name');
                                    return $model->goods_name ?? "";
                                },
                                'filter' => Html::activeTextInput($searchModel, 'goods_name', [
                                    'class' => 'form-control',
                                    'style' => 'width:200px;'
                                ]),
                            ],
                            /* [
                                 'attribute' => 'material',
                                'headerOptions' => ['class' => 'col-md-1'],
                                 'value' => function ($model) {
                                     return Yii::$app->attr->valueName($model->material);
                                 },
                                 'filter' => Html::activeDropDownList($searchModel, 'material', Yii::$app->attr->valueMap(\addons\Style\common\enums\AttrIdEnum::MATERIAL), [
                                     'prompt' => '全部',
                                     'class' => 'form-control',
                                     'style' => 'width:80px;'
                                 ]),
                             ],*/
                            [
                                'attribute' => 'material_type',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#feeeed;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#feeeed;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('material_type');
                                    return Yii::$app->attr->valueName($model->material_type) ?? "";
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'material_type', $model->getPartsMaterialMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                            ],
                            [
                                'attribute' => 'material_color',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#feeeed;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#feeeed;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('material_color');
                                    return Yii::$app->attr->valueName($model->material_color) ?? "";
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'material_color', $model->getMaterialColorMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                            ],
                            [
                                'attribute' => 'goods_num',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#feeeed;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#feeeed;'],
                                'value' => function ($model, $key, $index, $widget) use ($total) {
                                    $widget->footer = $model->getFooterValues('goods_num', $total);
                                    return $model->goods_num ?? 0;
                                },
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'goods_num', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'finger_hk',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#afdfe4;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#afdfe4;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('material_type');
                                    return Yii::$app->attr->valueName($model->finger_hk) ?? "";
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'finger_hk', $model->getPortNoMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                            ],
                            [
                                'attribute' => 'finger',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#afdfe4;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#afdfe4;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('finger');
                                    return Yii::$app->attr->valueName($model->finger) ?? "";
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'finger', $model->getFingerMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                            ],
                            [
                                'attribute' => 'length',
                                //'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#afdfe4;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#afdfe4;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('length');
                                    return $model->length ?? "";
                                },
                                'filter' => Html::activeTextInput($searchModel, 'length', [
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                            ],
                            [
                                'attribute' => 'product_size',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#afdfe4;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#afdfe4;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('product_size');
                                    return $model->product_size ?? "";
                                },
                                'filter' => Html::activeTextInput($searchModel, 'product_size', [
                                    'class' => 'form-control',
                                    'style' => 'width:100px;'
                                ]),
                            ],
                            [
                                'attribute' => 'xiangkou',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#afdfe4;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#afdfe4;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('xiangkou');
                                    return Yii::$app->attr->valueName($model->xiangkou) ?? "";
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'xiangkou', $model->getXiangkouMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                            ],
                            [
                                'attribute' => 'kezi',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#afdfe4;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#afdfe4;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('kezi');
                                    return $model->kezi ?? "";
                                },
                                'filter' => Html::activeTextInput($searchModel, 'kezi', [
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                            ],
                            [
                                'attribute' => 'chain_type',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#afdfe4;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#afdfe4;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('chain_type');
                                    return Yii::$app->attr->valueName($model->chain_type) ?? "";
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'chain_type', $model->getChainTypeMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                            ],
//                            [
//                                'attribute' => 'chain_long',
//                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#afdfe4'],
//                                'filter' => Html::activeTextInput($searchModel, 'chain_long', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:100px;'
//                                ]),
//                            ],
                            [
                                'attribute' => 'cramp_ring',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#afdfe4;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#afdfe4;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('cramp_ring');
                                    return Yii::$app->attr->valueName($model->cramp_ring) ?? "";
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'cramp_ring', $model->getCrampRingMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                            ],
                            [
                                'attribute' => 'talon_head_type',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#afdfe4;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#afdfe4;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('talon_head_type');
                                    return Yii::$app->attr->valueName($model->talon_head_type) ?? "";
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'talon_head_type', $model->getTalonHeadTypeMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                            ],
                            [
                                'attribute' => 'peiliao_way',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#FFD700;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#FFD700;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('peiliao_way');
                                    return \addons\Warehouse\common\enums\PeiLiaoWayEnum::getValue($model->peiliao_way) ?? "";
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'peiliao_way', $model->getPeiLiaoWayMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                            ],
                            [
                                'attribute' => 'suttle_weight',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#FFD700;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#FFD700;'],
                                'value' => function ($model, $key, $index, $widget) use ($total) {
                                    $widget->footer = $model->getFooterValues('suttle_weight', $total, "0.000");
                                    return $model->suttle_weight ?? "0.000";
                                },
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'suttle_weight', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'gold_weight',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#FFD700;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#FFD700;'],
                                'value' => function ($model, $key, $index, $widget) use ($total) {
                                    $widget->footer = $model->getFooterValues('gold_weight', $total, "0.000");
                                    return $model->gold_weight ?? "0.000";
                                },
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'gold_weight', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'gold_loss',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#FFD700;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#FFD700;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('gold_loss');
                                    return $model->gold_loss ?? "0";
                                },
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'gold_loss', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'lncl_loss_weight',
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#FFD700;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#FFD700;'],
                                'value' => function ($model, $key, $index, $widget) use ($total) {
                                    $widget->footer = $model->getFooterValues('lncl_loss_weight', $total, "0.000");
                                    return $model->lncl_loss_weight ?? "0.000";
                                },
//                                'filter' => Html::activeTextInput($searchModel, 'lncl_loss_weight', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'gold_price',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#FFD700;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#FFD700;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('gold_price');
                                    return $model->gold_price ?? "";
                                },
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'gold_price', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'gold_amount',
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#FFD700;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#FFD700;'],
                                'value' => function ($model, $key, $index, $widget) use ($total) {
                                    $widget->footer = $model->getFooterValues('gold_amount', $total, "0.000");
                                    return $model->gold_amount ?? "0.000";
                                },
//                                'filter' => Html::activeTextInput($searchModel, 'gold_amount', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                            ],
                            /*[
                                'attribute' => 'cert_id',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#afdfe4;'],
                                'filter' => Html::activeTextInput($searchModel, 'cert_id', [
                                    'class' => 'form-control',
                                    'style' => 'width:100px;'
                                ]),
                            ],
                            [
                                'attribute' => 'cert_type',
                                'value' => function ($model) {
                                    return Yii::$app->attr->valueName($model->cert_type) ?? "";
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'cert_type', $model->getCertTypeMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#afdfe4;'],
                            ],
                            [
                                'attribute' => 'diamond_cert_id',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#d5c59f;'],
                                'filter' => Html::activeTextInput($searchModel, 'diamond_cert_id', [
                                    'class' => 'form-control',
                                    'style' => 'width:100px;'
                                ]),
                            ],
                            [
                                'attribute' => 'diamond_cert_type',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    return Yii::$app->attr->valueName($model->diamond_cert_type) ?? "";
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'diamond_cert_type', $model->getDiamondCertTypeMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:100px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#d5c59f;'],
                            ],
                            [
                                'attribute' => 'diamond_carat',
                                'format' => 'raw',
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#d5c59f;'],
//                                'filter' => Html::activeTextInput($searchModel, 'diamond_carat', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'diamond_shape',
                                'value' => function ($model) {
                                    return Yii::$app->attr->valueName($model->diamond_shape) ?? "";
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'diamond_shape', $model->getDiamondClarityMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#d5c59f;'],
                            ],
                            [
                                'attribute' => 'diamond_color',
                                'value' => function ($model) {
                                    return Yii::$app->attr->valueName($model->diamond_color) ?? "";
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'diamond_color', $model->getDiamondColorMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#d5c59f;'],
                            ],
                            [
                                'attribute' => 'diamond_clarity',
                                'value' => function ($model) {
                                    return Yii::$app->attr->valueName($model->diamond_clarity) ?? "";
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'diamond_clarity', $model->getDiamondClarityMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#d5c59f;'],
                            ],
                            [
                                'attribute' => 'diamond_cut',
                                'value' => function ($model) {
                                    return Yii::$app->attr->valueName($model->diamond_cut) ?? "";
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'diamond_cut', $model->getDiamondCutMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#d5c59f;'],
                            ],
                            [
                                'attribute' => 'diamond_polish',
                                'value' => function ($model) {
                                    return Yii::$app->attr->valueName($model->diamond_polish) ?? "";
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'diamond_polish', $model->getDiamondPolishMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#d5c59f;'],
                            ],
                            [
                                'attribute' => 'diamond_symmetry',
                                'value' => function ($model) {
                                    return Yii::$app->attr->valueName($model->diamond_symmetry) ?? "";
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'diamond_symmetry', $model->getDiamondSymmetryMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#d5c59f;'],
                            ],
                            [
                                'attribute' => 'diamond_fluorescence',
                                'value' => function ($model) {
                                    return Yii::$app->attr->valueName($model->diamond_fluorescence) ?? "";
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'diamond_fluorescence', $model->getDiamondFluorescenceMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#d5c59f;'],
                            ],
                            [
                                'attribute' => 'diamond_discount',
                                'format' => 'raw',
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#d5c59f;'],
//                                'filter' => Html::activeTextInput($searchModel, 'diamond_discount', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                            ],*/
                            [
                                'attribute' => 'main_pei_type',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#afb4db;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#afb4db;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('main_pei_type');
                                    return \addons\Warehouse\common\enums\PeiShiWayEnum::getValue($model->main_pei_type) ?? "";
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'main_pei_type', $model->getPeiShiWayMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                            ],
                            [
                                'attribute' => 'main_stone_sn',
                                //'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#afb4db;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#afb4db;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('main_stone_sn');
                                    return $model->main_stone_sn ?? "";
                                },
                                'filter' => Html::activeTextInput($searchModel, 'main_stone_sn', [
                                    'class' => 'form-control',
                                    'style' => 'width:100px;'
                                ]),
                            ],
                            [
                                'attribute' => 'main_stone_type',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#afb4db;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#afb4db;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('main_stone_type');
                                    return Yii::$app->attr->valueName($model->main_stone_type) ?? "";
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'main_stone_type', $model->getMainStoneTypeMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                            ],
                            [
                                'attribute' => 'main_stone_num',
                                //'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#afb4db;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#afb4db;'],
                                'value' => function ($model, $key, $index, $widget) use ($total) {
                                    $widget->footer = $model->getFooterValues('main_stone_num', $total);
                                    return $model->main_stone_num ?? 0;
                                },
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'main_stone_num', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'main_stone_weight',
                                //'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#afb4db;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#afb4db;'],
                                'value' => function ($model, $key, $index, $widget) use ($total) {
                                    $widget->footer = $model->getFooterValues('main_stone_weight', $total, "0.000");
                                    return $model->main_stone_weight ?? "0.000";
                                },
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'main_stone_weight', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'main_stone_price',
                                //'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#afb4db;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#afb4db;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('main_stone_price');
                                    return $model->main_stone_price ?? "0.00";
                                },
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'main_stone_price', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'main_stone_amount',
                                //'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#afb4db;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#afb4db;'],
                                'value' => function ($model, $key, $index, $widget) use ($total) {
                                    $widget->footer = $model->getFooterValues('main_stone_amount', $total, "0.000");
                                    return $model->main_stone_amount ?? "0.00";
                                },
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'main_stone_amount', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'main_stone_shape',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#afb4db;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#afb4db;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('main_stone_price');
                                    return Yii::$app->attr->valueName($model->main_stone_shape) ?? "";
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'main_stone_shape', $model->getMainStoneShapeMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:100px;'
                                ]),
                            ],
                            [
                                'attribute' => 'main_stone_color',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#afb4db;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#afb4db;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('main_stone_color');
                                    return Yii::$app->attr->valueName($model->main_stone_color) ?? "";
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'main_stone_color', $model->getMainStoneColorMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                            ],
                            [
                                'attribute' => 'main_stone_clarity',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#afb4db;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#afb4db;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('main_stone_clarity');
                                    return Yii::$app->attr->valueName($model->main_stone_clarity) ?? "";
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'main_stone_clarity', $model->getMainStoneClarityMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                            ],
                            [
                                'attribute' => 'main_stone_cut',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#afb4db;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#afb4db;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('main_stone_cut');
                                    return Yii::$app->attr->valueName($model->main_stone_cut) ?? "";
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'main_stone_cut', $model->getMainStoneCutMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                            ],
                            [
                                'attribute' => 'main_stone_colour',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#afb4db;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#afb4db;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('main_stone_colour');
                                    return Yii::$app->attr->valueName($model->main_stone_colour) ?? "";
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'main_stone_colour', $model->getMainStoneColourMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                            ],
//                            [
//                                'attribute' => 'main_stone_size',
//                                //'format' => 'raw',
//                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#afb4db;'],
//                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#afb4db;'],
//                                'value' => function ($model, $key, $index, $widget) {
//                                    $widget->footer = $model->getAttributeLabel('main_stone_size');
//                                    return $model->main_stone_size ?? "";
//                                },
//                                'filter' => Html::activeTextInput($searchModel, 'main_stone_size', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:100px;'
//                                ]),
//                            ],
                            [
                                'attribute' => 'main_cert_id',
                                //'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#afb4db;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#afb4db;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('main_cert_id');
                                    return $model->main_cert_id ?? "";
                                },
                                'filter' => Html::activeTextInput($searchModel, 'main_cert_id', [
                                    'class' => 'form-control',
                                    'style' => 'width:100px;'
                                ]),
                            ],
                            [
                                'attribute' => 'main_cert_type',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#afb4db;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#afb4db;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('main_cert_type');
                                    return Yii::$app->attr->valueName($model->main_cert_type) ?? "";
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'main_cert_type', $model->getMainCertTypeMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                            ],
                            [
                                'attribute' => 'second_pei_type',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#dec674;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#dec674;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('second_pei_type');
                                    return \addons\Warehouse\common\enums\PeiShiWayEnum::getValue($model->second_pei_type) ?? "";
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'second_pei_type', $model->getPeiShiWayMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                            ],
                            [
                                'attribute' => 'second_stone_sn1',
                                //'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#dec674;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#dec674;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('second_stone_sn1');
                                    return Yii::$app->attr->valueName($model->second_stone_sn1) ?? "";
                                },
                                'filter' => Html::activeTextInput($searchModel, 'second_stone_sn1', [
                                    'class' => 'form-control',
                                    'style' => 'width:100px;'
                                ]),
                            ],
                            [
                                'attribute' => 'second_stone_type1',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#dec674;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#dec674;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('second_stone_type1');
                                    return Yii::$app->attr->valueName($model->second_stone_type1) ?? "";
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'second_stone_type1', $model->getSecondStoneType1Map(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                            ],
                            [
                                'attribute' => 'second_stone_num1',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#dec674;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#dec674;'],
                                'value' => function ($model, $key, $index, $widget) use ($total) {
                                    $widget->footer = $model->getFooterValues('second_stone_num1', $total);
                                    return $model->second_stone_num1 ?? 0;
                                },
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'second_stone_num1', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'second_stone_weight1',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#dec674;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#dec674;'],
                                'value' => function ($model, $key, $index, $widget) use ($total) {
                                    $widget->footer = $model->getFooterValues('second_stone_weight1', $total, "0.000");
                                    return $model->second_stone_weight1 ?? "0.000";
                                },
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'second_stone_weight1', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'second_stone_price1',
                                //'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#dec674;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#dec674;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('second_stone_price1');
                                    return $model->second_stone_price1 ?? "0.00";
                                },
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'second_stone_price1', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'second_stone_amount1',
                                //'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#dec674;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#dec674;'],
                                'value' => function ($model, $key, $index, $widget) use ($total) {
                                    $widget->footer = $model->getFooterValues('second_stone_amount1', $total, "0.00");
                                    return $model->second_stone_amount1 ?? "0.00";
                                },
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'second_stone_amount1', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'second_stone_color1',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#dec674;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#dec674;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('second_stone_color1');
                                    return Yii::$app->attr->valueName($model->second_stone_color1) ?? "";
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'second_stone_color1', $model->getSecondStoneColor1Map(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                            ],
                            [
                                'attribute' => 'second_stone_shape1',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#dec674;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#dec674;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('second_stone_shape1');
                                    return Yii::$app->attr->valueName($model->second_stone_shape1) ?? "";
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'second_stone_shape1', $model->getSecondStoneShape1Map(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                            ],
                            [
                                'attribute' => 'second_stone_cut1',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#dec674;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#dec674;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('second_stone_cut1');
                                    return Yii::$app->attr->valueName($model->second_stone_cut1) ?? "";
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'second_stone_cut1', $model->getSecondStoneCut1Map(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                            ],
                            [
                                'attribute' => 'second_stone_clarity1',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#dec674;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#dec674;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('second_stone_clarity1');
                                    return Yii::$app->attr->valueName($model->second_stone_clarity1) ?? "";
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'second_stone_clarity1', $model->getSecondStoneClarity1Map(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                            ],
                            [
                                'attribute' => 'second_stone_colour1',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#dec674;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#dec674;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('second_stone_colour1');
                                    return Yii::$app->attr->valueName($model->second_stone_colour1) ?? "";
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'second_stone_colour1', $model->getSecondStoneColour1Map(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                            ],
//                            [
//                                'attribute' => 'second_stone_size1',
//                                'format' => 'raw',
//                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#dec674;'],
//                                'filter' => Html::activeTextInput($searchModel, 'second_stone_size1', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:100px;'
//                                ]),
//                            ],
//                            [
//                                'attribute' => 'second_cert_id1',
//                                'format' => 'raw',
//                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#dec674;'],
//                                'filter' => Html::activeTextInput($searchModel, 'second_cert_id1', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:100px;'
//                                ]),
//                            ],
//                            [
//                                'attribute' => 'second_stone_type1',
//                                'value' => function ($model) {
//                                    return Yii::$app->attr->valueName($model->second_stone_type1) ?? "";
//                                },
//                                'filter' => Html::activeDropDownList($searchModel, 'second_stone_type1', $model->getSecondStoneType1Map(), [
//                                    'prompt' => '全部',
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
//                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#dec674;'],
//                            ],
                            [
                                'attribute' => 'second_pei_type2',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#84bf96;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#84bf96;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('second_pei_type2');
                                    return \addons\Warehouse\common\enums\PeiShiWayEnum::getValue($model->second_pei_type2) ?? "";
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'second_pei_type2', $model->getPeiShiWayMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                            ],
                            [
                                'attribute' => 'second_stone_sn2',
                                //'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#84bf96;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#84bf96;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('second_stone_sn2');
                                    return $model->second_stone_sn2 ?? "";
                                },
                                'filter' => Html::activeTextInput($searchModel, 'second_stone_sn2', [
                                    'class' => 'form-control',
                                    'style' => 'width:100px;'
                                ]),
                            ],
                            [
                                'attribute' => 'second_stone_type2',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#84bf96;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#84bf96;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('second_stone_type2');
                                    return Yii::$app->attr->valueName($model->second_stone_type2) ?? "";
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'second_stone_type2', $model->getSecondStoneType2Map(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                            ],
                            [
                                'attribute' => 'second_stone_num2',
                                //'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#84bf96;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#84bf96;'],
                                'value' => function ($model, $key, $index, $widget) use ($total) {
                                    $widget->footer = $model->getFooterValues('second_stone_num2', $total);
                                    return $model->second_stone_num2 ?? 0;
                                },
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'second_stone_num2', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'second_stone_weight2',
                                //'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#84bf96;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#84bf96;'],
                                'value' => function ($model, $key, $index, $widget) use ($total) {
                                    $widget->footer = $model->getFooterValues('second_stone_weight2', $total, "0.000");
                                    return $model->second_stone_weight2 ?? "0.000";
                                },
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'second_stone_weight2', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'second_stone_price2',
                                //'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#84bf96;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#84bf96;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('second_stone_price2');
                                    return $model->second_stone_price2 ?? "0.00";
                                },
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'second_stone_price2', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'second_stone_amount2',
                                //'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#84bf96;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#84bf96;'],
                                'value' => function ($model, $key, $index, $widget) use ($total) {
                                    $widget->footer = $model->getFooterValues('second_stone_amount2', $total, "0.00");
                                    return $model->second_stone_amount2 ?? "0.00";
                                },
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'second_stone_amount2', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'stone_remark',
                                //'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#84bf96;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#84bf96;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('stone_remark');
                                    return $model->stone_remark ?? "";
                                },
                                'filter' => Html::activeTextInput($searchModel, 'stone_remark', [
                                    'class' => 'form-control',
                                    'style' => 'width:160px;'
                                ]),
                            ],
                            /*[
                                'attribute' => 'second_stone_shape2',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#84bf96;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#84bf96;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('second_stone_shape2');
                                    return Yii::$app->attr->valueName($model->second_stone_shape2) ?? "";
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'second_stone_shape2', $model->getSecondStoneShape2Map(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                            ],
                            [
                                'attribute' => 'second_stone_color2',
                                'value' => function ($model) {
                                    return Yii::$app->attr->valueName($model->second_stone_color2) ?? "";
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'second_stone_color2', $model->getSecondStoneColor2Map(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#84bf96;'],
                            ],
                            [
                                'attribute' => 'second_stone_clarity2',
                                'value' => function ($model) {
                                    return Yii::$app->attr->valueName($model->second_stone_clarity2) ?? "";
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'second_stone_clarity2', $model->getSecondStoneClarity2Map(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#84bf96;'],
                            ],
                            [
                                'attribute' => 'second_stone_colour2',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    return Yii::$app->attr->valueName($model->second_stone_colour2)??"";
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'second_stone_colour2', $model->getSecondStoneColour2Map(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#84bf96;'],
                            ],
                            [
                                'attribute' => 'second_stone_size2',
                                //'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#84bf96;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#84bf96;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('second_stone_size2');
                                    return $model->second_stone_size2 ?? "";
                                },
                                'filter' => Html::activeTextInput($searchModel, 'second_stone_size2', [
                                    'class' => 'form-control',
                                    'style' => 'width:100px;'
                                ]),
                            ],
                            [
                                'attribute' => 'second_cert_id2',
                                //'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#84bf96;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#84bf96;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('second_cert_id2');
                                    return $model->second_cert_id2 ?? "";
                                },
                                'filter' => Html::activeTextInput($searchModel, 'second_cert_id2', [
                                    'class' => 'form-control',
                                    'style' => 'width:100px;'
                                ]),
                            ],
                            [
                                'attribute' => 'second_stone_type2',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#84bf96;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#84bf96;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('second_stone_type2');
                                    return Yii::$app->attr->valueName($model->second_stone_type2) ?? "";
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'second_stone_type2', $model->getSecondStoneType2Map(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                            ],
                            [
                                'attribute' => 'second_pei_type3',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    return \addons\Warehouse\common\enums\PeiShiWayEnum::getValue($model->second_pei_type3) ?? "";
                                },
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#f8aba6;'],
                                'filter' => Html::activeDropDownList($searchModel, 'second_pei_type3', $model->getPeiShiWayMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                            ],
                            [
                                'attribute' => 'second_stone_type3',
                                'value' => function ($model) {
                                    return Yii::$app->attr->valueName($model->second_stone_type3) ?? "";
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'second_stone_type3', $model->getSecondStoneType3Map(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#f8aba6;'],
                            ],
                            [
                                'attribute' => 'second_stone_num3',
                                'format' => 'raw',
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#f8aba6;'],
//                                'filter' => Html::activeTextInput($searchModel, 'second_stone_num3', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'second_stone_weight3',
                                'format' => 'raw',
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#f8aba6;'],
//                                'filter' => Html::activeTextInput($searchModel, 'second_stone_weight3', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'second_stone_price3',
                                'format' => 'raw',
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#f8aba6;'],
//                                'filter' => Html::activeTextInput($searchModel, 'second_stone_price3', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'second_stone_amount3',
                                'format' => 'raw',
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#f8aba6;'],
//                                'filter' => Html::activeTextInput($searchModel, 'second_stone_amount3', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                            ],*/
                            [
                                'attribute' => 'parts_way',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#cde6c7;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#cde6c7;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('parts_way');
                                    return \addons\Warehouse\common\enums\PeiJianWayEnum::getValue($model->parts_way) ?? "";
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'parts_way', $model->getPeiJianWayMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                            ],
                            [
                                'attribute' => 'parts_type',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#cde6c7;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#cde6c7;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('parts_way');
                                    return Yii::$app->attr->valueName($model->parts_type) ?? "";
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'parts_type', $model->getPartsTypeMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                            ],
                            [
                                'attribute' => 'parts_material',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#cde6c7;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#cde6c7;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('parts_material');
                                    return Yii::$app->attr->valueName($model->parts_material) ?? "";
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'parts_material', $model->getPartsMaterialMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                            ],
                            [
                                'attribute' => 'parts_num',
                                //'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#cde6c7;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#cde6c7;'],
                                'value' => function ($model, $key, $index, $widget) use ($total) {
                                    $widget->footer = $model->getFooterValues('parts_num', $total);
                                    return $model->parts_num ?? 0;
                                },
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'parts_num', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'parts_gold_weight',
                                //'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#cde6c7;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#cde6c7;'],
                                'value' => function ($model, $key, $index, $widget) use ($total) {
                                    $widget->footer = $model->getFooterValues('parts_gold_weight', $total, "0.000");
                                    return $model->parts_gold_weight ?? "0.000";
                                },
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'parts_gold_weight', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'parts_price',
                                //'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#cde6c7;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#cde6c7;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('parts_price');
                                    return $model->parts_price ?? "0.00";
                                },
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'parts_price', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'parts_amount',
                                //'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#cde6c7;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#cde6c7;'],
                                'value' => function ($model, $key, $index, $widget) use ($total) {
                                    $widget->footer = $model->getFooterValues('parts_amount', $total, "0.00");
                                    return $model->parts_amount ?? "0.00";
                                },
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'parts_amount', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'basic_gong_fee',
                                //'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
                                'value' => function ($model, $key, $index, $widget) use ($total) {
                                    $widget->footer = $model->getFooterValues('basic_gong_fee', $total, "0.00");
                                    return $model->basic_gong_fee ?? "0.00";
                                },
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'basic_gong_fee', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'gong_fee',
                                //'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('gong_fee');
                                    return $model->gong_fee ?? "0.00";
                                },
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'gong_fee', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'peishi_weight',
                                //'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
                                'value' => function ($model, $key, $index, $widget) use ($total) {
                                    $widget->footer = $model->getFooterValues('peishi_weight', $total, "0.000");
                                    return $model->peishi_weight ?? "0.000";
                                },
                                'filter' => false,

//                                'filter' => Html::activeTextInput($searchModel, 'peishi_weight', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'peishi_gong_fee',
                                //'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
                                'value' => function ($model, $key, $index, $widget) use ($total) {
                                    $widget->footer = $model->getFooterValues('peishi_gong_fee', $total, "0.00");
                                    return $model->peishi_gong_fee ?? "0.00";
                                },
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'peishi_gong_fee', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'peishi_fee',
                                //'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
                                'value' => function ($model, $key, $index, $widget) use ($total) {
                                    $widget->footer = $model->getFooterValues('peishi_fee', $total, "0.00");
                                    return $model->peishi_fee ?? "0.00";
                                },
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'peishi_fee', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'parts_fee',
                                //'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
                                'value' => function ($model, $key, $index, $widget) use ($total) {
                                    $widget->footer = $model->getFooterValues('parts_fee', $total, "0.00");
                                    return $model->parts_fee ?? "0.000";
                                },
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'parts_fee', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'xiangqian_craft',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('xiangqian_craft');
                                    return Yii::$app->attr->valueName($model->xiangqian_craft) ?? "";
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'xiangqian_craft', $model->getXiangqianCraftMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                            ],
                            [
                                'attribute' => 'xianqian_price',
                                //'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('xianqian_price');
                                    return $model->xianqian_price ?? "0.00";
                                },
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, xianqian_price, [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'xianqian_fee',
                                //'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
                                'value' => function ($model, $key, $index, $widget) use ($total) {
                                    $widget->footer = $model->getFooterValues('xianqian_fee', $total, "0.00");
                                    return $model->xianqian_fee ?? "0.00";
                                },
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'xianqian_fee', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'biaomiangongyi',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('biaomiangongyi');
                                    return Yii::$app->attr->valueName($model->biaomiangongyi) ?? "0.00";
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'biaomiangongyi', $model->getFaceCraftMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:100px;'
                                ]),
                            ],
                            [
                                'attribute' => 'biaomiangongyi_fee',
                                //'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
                                'value' => function ($model, $key, $index, $widget) use ($total) {
                                    $widget->footer = $model->getFooterValues('biaomiangongyi_fee', $total, "0.00");
                                    return $model->biaomiangongyi_fee ?? "0.00";
                                },
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'biaomiangongyi_fee', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'fense_fee',
                                //'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
                                'value' => function ($model, $key, $index, $widget) use ($total) {
                                    $widget->footer = $model->getFooterValues('fense_fee', $total, "0.00");
                                    return $model->fense_fee ?? "0.00";
                                },
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'fense_fee', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'penlasha_fee',
                                //'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
                                'value' => function ($model, $key, $index, $widget) use ($total) {
                                    $widget->footer = $model->getFooterValues('penlasha_fee', $total, "0.00");
                                    return $model->penlasha_fee ?? "0.00";
                                },
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'penlasha_fee', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'bukou_fee',
                                //'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
                                'value' => function ($model, $key, $index, $widget) use ($total) {
                                    $widget->footer = $model->getFooterValues('bukou_fee', $total, "0.00");
                                    return $model->bukou_fee ?? "0.00";
                                },
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'bukou_fee', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'templet_fee',
                                //'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
                                'value' => function ($model, $key, $index, $widget) use ($total) {
                                    $widget->footer = $model->getFooterValues('templet_fee', $total, "0.00");
                                    return $model->templet_fee ?? "0.00";
                                },
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'templet_fee', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'cert_fee',
                                //'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
                                'value' => function ($model, $key, $index, $widget) use ($total) {
                                    $widget->footer = $model->getFooterValues('cert_fee', $total, "0.00");
                                    return $model->cert_fee ?? "0.00";
                                },
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'cert_fee', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'other_fee',
                                //'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
                                'value' => function ($model, $key, $index, $widget) use ($total) {
                                    $widget->footer = $model->getFooterValues('other_fee', $total, "0.00");
                                    return $model->other_fee ?? "0.00";
                                },
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'other_fee', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'factory_cost',
                                //'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#9b95c9;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#9b95c9;'],
                                'value' => function ($model, $key, $index, $widget) use ($total) {
                                    $widget->footer = $model->getFooterValues('factory_cost', $total, "0.00");
                                    return $model->factory_cost ?? "0.00";
                                },
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'factory_cost', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:100px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'cost_price',
                                //'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#9b95c9;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#9b95c9;'],
                                'value' => function ($model, $key, $index, $widget) use ($total) {
                                    $widget->footer = $model->getFooterValues('cost_price', $total, "0.00");
                                    return $model->cost_price ?? "0.00";
                                },
                                'visible' => \common\helpers\Auth::verify(\common\enums\SpecialAuthEnum::VIEW_CAIGOU_PRICE),
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'cost_price', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:100px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'markup_rate',
                                //'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#9b95c9;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#9b95c9;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('markup_rate');
                                    return $model->markup_rate ?? "";
                                },
                                'filter' => Html::activeTextInput($searchModel, 'markup_rate', [
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                            ],
                            [
                                'attribute' => 'market_price',
                                //'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#9b95c9;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#9b95c9;'],
                                'value' => function ($model, $key, $index, $widget) use ($total) {
                                    $widget->footer = $model->getFooterValues('market_price', $total, "0.00");
                                    return $model->market_price ?? "0.00";
                                },
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'market_price', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:100px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'style_sex',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#9b95c9;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#9b95c9;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('style_sex');
                                    return \addons\Style\common\enums\StyleSexEnum::getValue($model->style_sex) ?? "";
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'style_sex', $model->getStyleSexMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                            ],
                            [
                                'attribute' => 'jintuo_type',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#9b95c9;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#9b95c9;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('jintuo_type');
                                    return \addons\Style\common\enums\JintuoTypeEnum::getValue($model->jintuo_type) ?? "";
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'jintuo_type', $model->getJietuoTypeMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                            ],
                            [
                                'attribute' => 'qiban_type',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#9b95c9;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#9b95c9;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('qiban_type');
                                    return \addons\Style\common\enums\QibanTypeEnum::getValue($model->qiban_type) ?? "";
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'qiban_type', $model->getQibanTypeMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                            ],
                            [
                                'attribute' => 'is_inlay',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#9b95c9;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#9b95c9;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('is_inlay');
                                    return \addons\Style\common\enums\InlayEnum::getValue($model->is_inlay) ?? "";
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'is_inlay', $model->getIsInlayMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                            ],
                            [
                                'attribute' => 'factory_mo',
                                //'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#9b95c9;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#9b95c9;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('factory_mo');
                                    return $model->factory_mo ?? "";
                                },
                                'filter' => Html::activeTextInput($searchModel, 'factory_mo', [
                                    'class' => 'form-control',
                                    'style' => 'width:100px;'
                                ]),
                            ],
                            [
                                'attribute' => 'order_sn',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#9b95c9;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#9b95c9;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('order_sn');
                                    return $model->order_sn ?? "";
                                },
                                'filter' => Html::activeTextInput($searchModel, 'order_sn', [
                                    'class' => 'form-control',
                                    'style' => 'width:100px;'
                                ]),
                            ],
                            [
                                'attribute' => 'remark',
                                //'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#9b95c9;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#9b95c9;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('remark');
                                    return $model->remark ?? "";
                                },
                                'filter' => Html::activeTextInput($searchModel, 'remark', [
                                    'class' => 'form-control',
                                    'style' => 'width:160px;'
                                ]),
                            ],
                            /*[
                                'attribute' => 'produce_sn',
                                'headerOptions' => ['class' => 'col-md-1'],
                                'filter' => Html::activeTextInput($searchModel, 'produce_sn', [
                                    'class' => 'form-control',
                                    'style' => 'width:120px;'
                                ]),
                            ],
                            [
                                'attribute' => 'gross_weight',
                                'headerOptions' => ['class' => 'col-md-1'],
                                'filter' => Html::activeTextInput($searchModel, 'gross_weight', [
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                            ],*/
                            [
                                'class' => 'yii\grid\ActionColumn',
                                'header' => '操作',
                                'contentOptions' => ['style' => ['white-space' => 'nowrap']],
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#9b95c9;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#9b95c9;'],
                                'template' => '{edit} {delete}',
                                'buttons' => [
                                    'edit' => function ($url, $model, $key) use ($bill) {
                                        if ($bill->bill_status == BillStatusEnum::SAVE) {
                                            return Html::edit(['edit', 'id' => $model->id, 'bill_id' => $bill->id], '编辑', [
                                                'class' => 'btn btn-primary btn-xs openIframe',
                                                'data-width' => '90%',
                                                'data-height' => '90%',
                                                'data-offset' => '20px',
                                            ]);
                                        }
                                    },
                                    'delete' => function ($url, $model, $key) use ($bill) {
                                        if ($bill->bill_status == BillStatusEnum::SAVE) {
                                            return Html::delete(['delete', 'id' => $model->id], '删除', [
                                                'class' => 'btn btn-danger btn-xs',
                                            ]);
                                        }
                                    },
                                ],
                            ]
                        ]
                    ]); ?>
                </div>
            </div>
        </div>
        <!-- box end -->
    </div>
    <!-- tab-content end -->
</div>
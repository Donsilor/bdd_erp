<?php

use common\helpers\Html;
use common\helpers\Url;
use yii\grid\GridView;
use addons\Warehouse\common\enums\BillStatusEnum;
use addons\Purchase\common\enums\ReceiptStatusEnum;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('receipt_goods', '采购收货单详情');
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box-body nav-tabs-custom">
    <h2 class="page-header"><?php echo $this->title; ?> - <?php echo $receipt->receipt_no ?>
        - <?php echo ReceiptStatusEnum::getValue($receipt->receipt_status) ?? "" ?></h2>
    <?php echo Html::menuTab($tabList, $tab) ?>
    <div class="box-tools" style="float:right;margin-top:-40px; margin-right: 20px;">
        <?php
        if ($receipt->receipt_status == ReceiptStatusEnum::SAVE) {
            echo Html::create(['add', 'receipt_id' => $receipt->id], '添加货品', [
                'class' => 'btn btn-primary btn-xs openIframe',
                'data-width' => '90%',
                'data-height' => '90%',
                'data-offset' => '20px',
            ]);
            echo '&nbsp;';
            echo Html::edit(['edit-all', 'receipt_id' => $receipt->id], '批量编辑', ['class' => 'btn btn-info btn-xs']);
            echo '&nbsp;';
            echo Html::tag('span', '批量删除', ["class" => "btn btn-danger btn-xs jsBatchStatus", "data-grid" => "grid", "data-url" => Url::to(['batch-delete']),]);
            echo '&nbsp;';
            echo Html::tag('span', '刷新价格', ["class" => "btn btn-warning btn-xs jsBatchStatus", "data-grid" => "grid", "data-url" => Url::to(['update-price']),]);
        }
        if ($receipt->receipt_status == ReceiptStatusEnum::CONFIRM) {
            echo Html::batchPopButton(['warehouse', 'check' => 1], '批量入库', [
                'class' => 'btn btn-success btn-xs',
                'data-width' => '40%',
                'data-height' => '60%',
                'data-offset' => '20px',
            ]);
        }
        ?>
    </div>
    <div class="tab-content">
        <div class="col-xs-12" style="padding-left: 0px;padding-right: 0px;">
            <div class="box">
                <div class="box-body table-responsive">
                    <?php echo Html::batchButtons(false) ?>
                    <span style="color:red;">Ctrl+F键可快速查找字段名</span>
                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'tableOptions' => ['class' => 'table table-hover'],
                        'options' => ['style' => 'white-space:nowrap;'],
                        'showFooter' => false,//显示footer行
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
                                'attribute' => 'xuhao',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#feeeed;'],
                                'filter' => Html::activeTextInput($searchModel, 'xuhao', [
                                    'class' => 'form-control',
                                ]),
                            ],
                            [
                                'class' => 'yii\grid\ActionColumn',
                                'header' => '操作',
                                'contentOptions' => ['style' => ['white-space' => 'nowrap']],
                                'template' => '{edit} {delete}',
                                'buttons' => [
                                    'edit' => function ($url, $model, $key) use ($receipt) {
                                        if ($receipt->receipt_status == ReceiptStatusEnum::SAVE) {
                                            return Html::edit(['edit', 'id' => $model->id, 'receipt_id' => $receipt->id], '编辑', [
                                                'class' => 'btn btn-primary btn-xs openIframe',
                                                'data-width' => '90%',
                                                'data-height' => '90%',
                                                'data-offset' => '20px',
                                            ]);
                                        }
                                    },
                                    'delete' => function ($url, $model, $key) use ($receipt) {
                                        if ($receipt->receipt_status == ReceiptStatusEnum::SAVE) {
                                            return Html::delete(['delete', 'id' => $model->id], '删除', [
                                                'class' => 'btn btn-danger btn-xs',
                                            ]);
                                        }
                                    },
                                ],
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#feeeed;'],
                            ],
                            [
                                'attribute' => 'style_cate_id',
                                'value' => "cate.name",
                                'filter' => Html::activeDropDownList($searchModel, 'style_cate_id', $model->getCateMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:150px;'
                                ]),
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#feeeed;'],
                            ],
                            [
                                'attribute' => 'product_type_id',
                                'value' => "type.name",
                                'filter' => Html::activeDropDownList($searchModel, 'product_type_id', $model->getProductMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:150px;'
                                ]),
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#feeeed;'],
                            ],
                            [
                                'attribute' => 'goods_status',
                                'value' => function ($model) {
                                    return \addons\Purchase\common\enums\ReceiptGoodsStatusEnum::getValue($model->goods_status);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'goods_status', \addons\Purchase\common\enums\ReceiptGoodsStatusEnum::getMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:100px;',
                                ]),
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#feeeed;'],
                            ],
                            [
                                'attribute' => 'purchase_sn',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#feeeed;'],
                                'filter' => Html::activeTextInput($searchModel, 'purchase_sn', [
                                    'class' => 'form-control',
                                    'style' => 'width:120px;'
                                ]),
                            ],
                            [
                                'attribute' => 'produce_sn',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#feeeed;'],
                                'filter' => Html::activeTextInput($searchModel, 'produce_sn', [
                                    'class' => 'form-control',
                                    'style' => 'width:120px;'
                                ]),
                            ],
                            [
                                'attribute' => 'style_sn',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#feeeed;'],
                                'filter' => Html::activeTextInput($searchModel, 'style_sn', [
                                    'class' => 'form-control',
                                    'style' => 'width:100px;'
                                ]),
                            ],
                            [
                                'attribute' => 'qiban_sn',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#feeeed;'],
                                'filter' => Html::activeTextInput($searchModel, 'qiban_sn', [
                                    'class' => 'form-control',
                                    'style' => 'width:100px;'
                                ]),
                            ],
                            [
                                'attribute' => 'goods_name',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#feeeed;'],
                                'filter' => Html::activeTextInput($searchModel, 'goods_name', [
                                    'class' => 'form-control',
                                    'style' => 'width:200px;'
                                ]),
                            ],
                            /* [
                                 'attribute' => 'material',
                                 'value' => function ($model) {
                                     return Yii::$app->attr->valueName($model->material);
                                 },
                                 'filter' => Html::activeDropDownList($searchModel, 'material', Yii::$app->attr->valueMap(\addons\Style\common\enums\AttrIdEnum::MATERIAL), [
                                     'prompt' => '全部',
                                     'class' => 'form-control',
                                     'style' => 'width:80px;'
                                 ]),
                                 'headerOptions' => ['class' => 'col-md-1'],
                             ],*/
                            [
                                'attribute' => 'material_type',
                                'value' => function ($model) {
                                    return Yii::$app->attr->valueName($model->material_type) ?? "";
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'material_type', $model->getPartsMaterialMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#feeeed;'],
                            ],
                            [
                                'attribute' => 'material_color',
                                'value' => function ($model) {
                                    return Yii::$app->attr->valueName($model->material_color) ?? "";
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'material_color', $model->getMaterialColorMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#feeeed;'],
                            ],
                            [
                                'attribute' => 'goods_num',
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#feeeed;'],
//                                'filter' => Html::activeTextInput($searchModel, 'goods_num', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'finger_hk',
                                'value' => function ($model) {
                                    return Yii::$app->attr->valueName($model->finger_hk) ?? "";
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'finger_hk', $model->getPortNoMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#feeeed;'],
                            ],
                            [
                                'attribute' => 'finger',
                                'value' => function ($model) {
                                    return Yii::$app->attr->valueName($model->finger) ?? "";
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'finger', $model->getFingerMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#feeeed;'],
                            ],
                            [
                                'attribute' => 'length',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#feeeed;'],
                                'filter' => Html::activeTextInput($searchModel, 'length', [
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                            ],
                            [
                                'attribute' => 'product_size',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#feeeed;'],
                                'filter' => Html::activeTextInput($searchModel, 'product_size', [
                                    'class' => 'form-control',
                                    'style' => 'width:100px;'
                                ]),
                            ],
                            [
                                'attribute' => 'xiangkou',
                                'value' => function ($model) {
                                    return Yii::$app->attr->valueName($model->xiangkou) ?? "";
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'xiangkou', $model->getXiangkouMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#feeeed;'],
                            ],
                            [
                                'attribute' => 'kezi',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#feeeed;'],
                                'filter' => Html::activeTextInput($searchModel, 'kezi', [
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                            ],
                            [
                                'attribute' => 'chain_type',
                                'value' => function ($model) {
                                    return Yii::$app->attr->valueName($model->chain_type) ?? "";
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'chain_type', $model->getChainTypeMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#feeeed;'],
                            ],
                            [
                                'attribute' => 'chain_long',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#feeeed;'],
                                'filter' => Html::activeTextInput($searchModel, 'chain_long', [
                                    'class' => 'form-control',
                                    'style' => 'width:100px;'
                                ]),
                            ],
                            [
                                'attribute' => 'cramp_ring',
                                'value' => function ($model) {
                                    return Yii::$app->attr->valueName($model->cramp_ring) ?? "";
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'cramp_ring', $model->getCrampRingMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#feeeed;'],
                            ],
                            [
                                'attribute' => 'talon_head_type',
                                'value' => function ($model) {
                                    return Yii::$app->attr->valueName($model->talon_head_type) ?? "";
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'talon_head_type', $model->getTalonHeadTypeMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#feeeed;'],
                            ],
                            [
                                'attribute' => 'peiliao_way',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    return \addons\Warehouse\common\enums\PeiLiaoWayEnum::getValue($model->peiliao_way) ?? "";
                                },
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#afdfe4;'],
                                'filter' => Html::activeDropDownList($searchModel, 'peiliao_way', $model->getPeiLiaoWayMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                            ],
                            [
                                'attribute' => 'gold_weight',
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#afdfe4;'],
//                                'filter' => Html::activeTextInput($searchModel, 'gold_weight', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'suttle_weight',
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#afdfe4;'],
//                                'filter' => Html::activeTextInput($searchModel, 'suttle_weight', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'gold_loss',
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#afdfe4;'],
//                                'filter' => Html::activeTextInput($searchModel, 'gold_loss', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'lncl_loss_weight',
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#afdfe4;'],
//                                'filter' => Html::activeTextInput($searchModel, 'lncl_loss_weight', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'gold_price',
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#afdfe4;'],
//                                'filter' => Html::activeTextInput($searchModel, 'gold_price', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'gold_amount',
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#afdfe4;'],
//                                'filter' => Html::activeTextInput($searchModel, 'gold_amount', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                            ],
                            [
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
                                'attribute' => 'main_pei_type',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    return \addons\Warehouse\common\enums\PeiShiWayEnum::getValue($model->main_pei_type) ?? "";
                                },
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#afb4db;'],
                                'filter' => Html::activeDropDownList($searchModel, 'main_pei_type', $model->getPeiShiWayMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                            ],
                            [
                                'attribute' => 'main_stone_sn',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#afb4db;'],
                                'filter' => Html::activeTextInput($searchModel, 'main_stone_sn', [
                                    'class' => 'form-control',
                                    'style' => 'width:100px;'
                                ]),
                            ],
                            [
                                'attribute' => 'main_stone',
                                'value' => function ($model) {
                                    return Yii::$app->attr->valueName($model->main_stone) ?? "";
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'main_stone', $model->getMainStoneTypeMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#afb4db;'],
                            ],
                            [
                                'attribute' => 'main_cert_id',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#afb4db;'],
                                'filter' => Html::activeTextInput($searchModel, 'main_cert_id', [
                                    'class' => 'form-control',
                                    'style' => 'width:100px;'
                                ]),
                            ],
                            [
                                'attribute' => 'main_stone_num',
                                'format' => 'raw',
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#afb4db;'],
//                                'filter' => Html::activeTextInput($searchModel, 'main_stone_num', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'main_stone_weight',
                                'format' => 'raw',
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#afb4db;'],
//                                'filter' => Html::activeTextInput($searchModel, 'main_stone_weight', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'main_stone_shape',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    return Yii::$app->attr->valueName($model->main_stone_shape) ?? "";
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'main_stone_shape', $model->getMainStoneShapeMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:100px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#afb4db;'],
                            ],
                            [
                                'attribute' => 'main_stone_color',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    return Yii::$app->attr->valueName($model->main_stone_color) ?? "";
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'main_stone_color', $model->getMainStoneColorMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#afb4db;'],
                            ],
                            [
                                'attribute' => 'main_stone_clarity',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    return Yii::$app->attr->valueName($model->main_stone_clarity) ?? "";
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'main_stone_clarity', $model->getMainStoneClarityMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#afb4db;'],
                            ],
                            [
                                'attribute' => 'main_stone_cut',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    return Yii::$app->attr->valueName($model->main_stone_cut) ?? "";
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'main_stone_cut', $model->getMainStoneCutMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#afb4db;'],
                            ],
                            [
                                'attribute' => 'main_stone_colour',
                                'value' => function ($model) {
                                    return Yii::$app->attr->valueName($model->main_stone_colour) ?? "";
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'main_stone_colour', $model->getMainStoneColourMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#afb4db;'],
                            ],
                            [
                                'attribute' => 'main_stone_size',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#afb4db;'],
                                'filter' => Html::activeTextInput($searchModel, 'main_stone_size', [
                                    'class' => 'form-control',
                                    'style' => 'width:100px;'
                                ]),
                            ],
                            [
                                'attribute' => 'main_stone_price',
                                'format' => 'raw',
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#afb4db;'],
//                                'filter' => Html::activeTextInput($searchModel, 'main_stone_price', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'main_stone_amount',
                                'format' => 'raw',
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#afb4db;'],
//                                'filter' => Html::activeTextInput($searchModel, 'main_stone_amount', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'second_pei_type',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    return \addons\Warehouse\common\enums\PeiShiWayEnum::getValue($model->second_pei_type) ?? "";
                                },
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#dec674;'],
                                'filter' => Html::activeDropDownList($searchModel, 'second_pei_type', $model->getPeiShiWayMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                            ],
                            [
                                'attribute' => 'second_stone_sn1',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#dec674;'],
                                'filter' => Html::activeTextInput($searchModel, 'second_stone_sn1', [
                                    'class' => 'form-control',
                                    'style' => 'width:100px;'
                                ]),
                            ],
                            [
                                'attribute' => 'second_stone1',
                                'value' => function ($model) {
                                    return Yii::$app->attr->valueName($model->second_stone1) ?? "";
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'second_stone1', $model->getSecondStoneType1Map(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#dec674;'],
                            ],
//                            [
//                                'attribute' => 'second_cert_id1',
//                                'format' => 'raw',
//                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#dec674;'],
//                                'filter' => Html::activeTextInput($searchModel, 'second_cert_id1', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:100px;'
//                                ]),
//                            ],
                            [
                                'attribute' => 'second_stone_num1',
                                'format' => 'raw',
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#dec674;'],
//                                'filter' => Html::activeTextInput($searchModel, 'second_stone_num1', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'second_stone_weight1',
                                'format' => 'raw',
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#dec674;'],
//                                'filter' => Html::activeTextInput($searchModel, 'second_stone_weight1', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'second_stone_shape1',
                                'value' => function ($model) {
                                    return Yii::$app->attr->valueName($model->second_stone_shape1) ?? "";
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'second_stone_shape1', $model->getSecondStoneShape1Map(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#dec674;'],
                            ],
                            [
                                'attribute' => 'second_stone_color1',
                                'value' => function ($model) {
                                    return Yii::$app->attr->valueName($model->second_stone_color1) ?? "";
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'second_stone_color1', $model->getSecondStoneColor1Map(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#dec674;'],
                            ],
                            [
                                'attribute' => 'second_stone_clarity1',
                                'value' => function ($model) {
                                    return Yii::$app->attr->valueName($model->second_stone_clarity1) ?? "";
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'second_stone_clarity1', $model->getSecondStoneClarity1Map(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#dec674;'],
                            ],
                            [
                                'attribute' => 'second_stone_colour1',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    return Yii::$app->attr->valueName($model->second_stone_colour1) ?? "";
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'second_stone_colour1', $model->getSecondStoneColour1Map(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#dec674;'],
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
                            [
                                'attribute' => 'second_stone_price1',
                                'format' => 'raw',
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#dec674;'],
//                                'filter' => Html::activeTextInput($searchModel, 'second_stone_price1', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'second_stone_amount1',
                                'format' => 'raw',
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#dec674;'],
//                                'filter' => Html::activeTextInput($searchModel, 'second_stone_amount1', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'second_pei_type2',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    return \addons\Warehouse\common\enums\PeiShiWayEnum::getValue($model->second_pei_type2) ?? "";
                                },
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#84bf96;'],
                                'filter' => Html::activeDropDownList($searchModel, 'second_pei_type2', $model->getPeiShiWayMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                            ],
                            [
                                'attribute' => 'second_stone_sn2',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#84bf96;'],
                                'filter' => Html::activeTextInput($searchModel, 'second_stone_sn2', [
                                    'class' => 'form-control',
                                    'style' => 'width:100px;'
                                ]),
                            ],
                            [
                                'attribute' => 'second_stone2',
                                'value' => function ($model) {
                                    return Yii::$app->attr->valueName($model->second_stone2) ?? "";
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'second_stone2', $model->getSecondStoneType2Map(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#84bf96;'],
                            ],
                            [
                                'attribute' => 'second_cert_id2',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#84bf96;'],
                                'filter' => Html::activeTextInput($searchModel, 'second_cert_id2', [
                                    'class' => 'form-control',
                                    'style' => 'width:100px;'
                                ]),
                            ],
                            [
                                'attribute' => 'second_stone_num2',
                                'format' => 'raw',
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#84bf96;'],
//                                'filter' => Html::activeTextInput($searchModel, 'second_stone_num2', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'second_stone_weight2',
                                'format' => 'raw',
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#84bf96;'],
//                                'filter' => Html::activeTextInput($searchModel, 'second_stone_weight2', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'second_stone_shape2',
                                'value' => function ($model) {
                                    return Yii::$app->attr->valueName($model->second_stone_shape2) ?? "";
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'second_stone_shape2', $model->getSecondStoneShape2Map(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#84bf96;'],
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
                                    return Yii::$app->attr->valueName($model->second_stone_colour2) ?? "";
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
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#84bf96;'],
                                'filter' => Html::activeTextInput($searchModel, 'second_stone_size2', [
                                    'class' => 'form-control',
                                    'style' => 'width:100px;'
                                ]),
                            ],
                            [
                                'attribute' => 'second_stone_price2',
                                'format' => 'raw',
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#84bf96;'],
//                                'filter' => Html::activeTextInput($searchModel, 'second_stone_price2', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'second_stone_amount2',
                                'format' => 'raw',
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#84bf96;'],
//                                'filter' => Html::activeTextInput($searchModel, 'second_stone_amount2', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                            ],
                            /*[
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
                                'attribute' => 'second_stone3',
                                'value' => function ($model) {
                                    return Yii::$app->attr->valueName($model->second_stone3) ?? "";
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'second_stone3', $model->getSecondStoneType3Map(), [
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
                                'attribute' => 'stone_remark',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#f8aba6;'],
                                'filter' => Html::activeTextInput($searchModel, 'stone_remark', [
                                    'class' => 'form-control',
                                    'style' => 'width:160px;'
                                ]),
                            ],
                            [
                                'attribute' => 'peishi_fee',
                                'format' => 'raw',
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#f8aba6;'],
//                                'filter' => Html::activeTextInput($searchModel, 'peishi_fee', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'peishi_gong_fee',
                                'format' => 'raw',
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#f8aba6;'],
//                                'filter' => Html::activeTextInput($searchModel, 'peishi_gong_fee', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'parts_way',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    return \addons\Warehouse\common\enums\PeiJianWayEnum::getValue($model->parts_way) ?? "";
                                },
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#cde6c7;'],
                                'filter' => Html::activeDropDownList($searchModel, 'parts_way', $model->getPeiJianWayMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                            ],
                            [
                                'attribute' => 'parts_type',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    return Yii::$app->attr->valueName($model->parts_type) ?? "";
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'parts_type', $model->getPartsTypeMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#cde6c7;'],
                            ],
                            [
                                'attribute' => 'parts_material',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    return Yii::$app->attr->valueName($model->parts_material) ?? "";
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'parts_material', $model->getPartsMaterialMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#cde6c7;'],
                            ],
                            [
                                'attribute' => 'parts_num',
                                'format' => 'raw',
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#cde6c7;'],
//                                'filter' => Html::activeTextInput($searchModel, 'parts_num', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'parts_gold_weight',
                                'format' => 'raw',
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#cde6c7;'],
//                                'filter' => Html::activeTextInput($searchModel, 'parts_gold_weight', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'parts_price',
                                'format' => 'raw',
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#cde6c7;'],
//                                'filter' => Html::activeTextInput($searchModel, 'parts_price', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'parts_amount',
                                'format' => 'raw',
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#cde6c7;'],
//                                'filter' => Html::activeTextInput($searchModel, 'parts_amount', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'parts_fee',
                                'format' => 'raw',
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#cde6c7;'],
//                                'filter' => Html::activeTextInput($searchModel, 'parts_fee', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'gong_fee',
                                'format' => 'raw',
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
//                                'filter' => Html::activeTextInput($searchModel, 'gong_fee', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'basic_gong_fee',
                                'format' => 'raw',
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
//                                'filter' => Html::activeTextInput($searchModel, 'basic_gong_fee', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'xiangqian_craft',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    return Yii::$app->attr->valueName($model->xiangqian_craft) ?? "";
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'xiangqian_craft', $model->getXiangqianCraftMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
                            ],
                            [
                                'attribute' => 'xianqian_price',
                                'format' => 'raw',
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
//                                'filter' => Html::activeTextInput($searchModel, xianqian_price, [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'xianqian_fee',
                                'format' => 'raw',
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
//                                'filter' => Html::activeTextInput($searchModel, 'xianqian_fee', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'biaomiangongyi',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    return Yii::$app->attr->valueName($model->biaomiangongyi) ?? "";
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'biaomiangongyi', $model->getFaceCraftMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:100px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
                            ],
                            [
                                'attribute' => 'biaomiangongyi_fee',
                                'format' => 'raw',
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
//                                'filter' => Html::activeTextInput($searchModel, 'biaomiangongyi_fee', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'fense_fee',
                                'format' => 'raw',
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
//                                'filter' => Html::activeTextInput($searchModel, 'fense_fee', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'penlasha_fee',
                                'format' => 'raw',
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
//                                'filter' => Html::activeTextInput($searchModel, 'penlasha_fee', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'bukou_fee',
                                'format' => 'raw',
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
//                                'filter' => Html::activeTextInput($searchModel, 'bukou_fee', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'templet_fee',
                                'format' => 'raw',
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
//                                'filter' => Html::activeTextInput($searchModel, 'templet_fee', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'cert_fee',
                                'format' => 'raw',
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
//                                'filter' => Html::activeTextInput($searchModel, 'cert_fee', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'other_fee',
                                'format' => 'raw',
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
//                                'filter' => Html::activeTextInput($searchModel, 'other_fee', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'total_gong_fee',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
                                'filter' => Html::activeTextInput($searchModel, 'total_gong_fee', [
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                            ],
                            [
                                'attribute' => 'factory_cost',
                                'format' => 'raw',
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
//                                'filter' => Html::activeTextInput($searchModel, 'factory_cost', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:100px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'cost_price',
                                'format' => 'raw',
                                'filter' => false,
                                'visible' => \common\helpers\Auth::verify(\common\enums\SpecialAuthEnum::VIEW_CAIGOU_PRICE),
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
//                                'filter' => Html::activeTextInput($searchModel, 'cost_price', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:100px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'markup_rate',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#9b95c9;'],
                                'filter' => Html::activeTextInput($searchModel, 'markup_rate', [
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                            ],
                            [
                                'attribute' => 'factory_mo',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#9b95c9;'],
                                'filter' => Html::activeTextInput($searchModel, 'factory_mo', [
                                    'class' => 'form-control',
                                    'style' => 'width:100px;'
                                ]),
                            ],
                            [
                                'attribute' => 'order_sn',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#9b95c9;'],
                                'filter' => Html::activeTextInput($searchModel, 'order_sn', [
                                    'class' => 'form-control',
                                    'style' => 'width:100px;'
                                ]),
                            ],
                            [
                                'attribute' => 'style_sex',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#9b95c9;'],
                                'value' => function ($model) {
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
                                'value' => function ($model, $key, $index, $column) {
                                    return \addons\Style\common\enums\JintuoTypeEnum::getValue($model->is_inlay) ?? "";
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'jintuo_type', $model->getJietuoTypeMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:100px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#9b95c9;'],
                            ],
                            [
                                'attribute' => 'qiban_type',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#9b95c9;'],
                                'value' => function ($model) {
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
                                'value' => function ($model) {
                                    return \addons\Style\common\enums\InlayEnum::getValue($model->is_inlay) ?? "";
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'is_inlay', $model->getIsInlayMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#9b95c9;'],
                            ],
                            [
                                'attribute' => 'barcode',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#9b95c9;'],
                                'filter' => Html::activeTextInput($searchModel, 'produce_sn', [
                                    'class' => 'form-control',
                                    'style' => 'width:100px;'
                                ]),
                            ],
                            [
                                'attribute' => 'goods_remark',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#9b95c9;'],
                                'filter' => Html::activeTextInput($searchModel, 'goods_remark', [
                                    'class' => 'form-control',
                                    'style' => 'width:150px;'
                                ]),
                            ],
                            [
                                'attribute' => 'market_price',
                                'format' => 'raw',
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#9b95c9;'],
//                                'filter' => Html::activeTextInput($searchModel, 'market_price', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:100px;'
//                                ]),
                            ],
                            [
                                'class' => 'yii\grid\ActionColumn',
                                'header' => '操作',
                                'contentOptions' => ['style' => ['white-space' => 'nowrap']],
                                'template' => '{edit} {delete}',
                                'buttons' => [
                                    'edit' => function ($url, $model, $key) use ($receipt) {
                                        if ($receipt->receipt_status == ReceiptStatusEnum::SAVE) {
                                            return Html::edit(['edit', 'id' => $model->id, 'receipt_id' => $receipt->id], '编辑', [
                                                'class' => 'btn btn-primary btn-xs openIframe',
                                                'data-width' => '90%',
                                                'data-height' => '90%',
                                                'data-offset' => '20px',
                                            ]);
                                        }
                                    },
                                    'delete' => function ($url, $model, $key) use ($receipt) {
                                        if ($receipt->receipt_status == ReceiptStatusEnum::SAVE) {
                                            return Html::delete(['delete', 'id' => $model->id], '删除', [
                                                'class' => 'btn btn-danger btn-xs',
                                            ]);
                                        }
                                    },
                                ],
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#9b95c9;'],
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
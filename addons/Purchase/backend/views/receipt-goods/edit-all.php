<?php

use addons\Style\common\enums\AttrIdEnum;
use addons\Warehouse\common\enums\BillStatusEnum;
use common\helpers\Html;
use common\helpers\Url;
use kartik\select2\Select2;
use yii\grid\GridView;
use kartik\daterange\DateRangePicker;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('receipt_goods', '采购收货单详情');
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box-body nav-tabs-custom">
    <h2 class="page-header"><?php echo $this->title; ?> - <?php echo $receipt->receipt_no ?></h2>
    <?php echo Html::menuTab($tabList, $tab) ?>
    <div class="box-tools" style="float:right;margin-top:-40px; margin-right: 20px;">
        <?php
        if ($receipt->receipt_status == \addons\Purchase\common\enums\ReceiptStatusEnum::SAVE) {
            echo Html::create(['add', 'receipt_id' => $receipt->id], '添加货品', [
                'class' => 'btn btn-primary btn-xs openIframe',
                'data-width' => '90%',
                'data-height' => '90%',
                'data-offset' => '20px',
            ]);
            echo '&nbsp;';
            echo Html::tag('span', '批量删除', ["class" => "btn btn-danger btn-xs jsBatchStatus", "data-grid" => "grid", "data-url" => Url::to(['batch-delete']),]);
            echo '&nbsp;';
            echo Html::tag('span', '刷新价格', ["class" => "btn btn-warning btn-xs jsBatchStatus", "data-grid" => "grid", "data-url" => Url::to(['update-price']),]);
            echo '&nbsp;';
            echo Html::a('返回列表', ['receipt-goods/index', 'receipt_id' => $receipt->id], ['class' => 'btn btn-white btn-xs']);
        }
        ?>
    </div>
    <div class="tab-content">
        <div class="col-xs-12" style="padding-left: 0px;padding-right: 0px;">
            <div class="box">
                <div class="box-body table-responsive">
                    <?php echo Html::batchButtons(false) ?>
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
                                        if ($receipt->receipt_status == BillStatusEnum::SAVE) {
                                            return Html::edit(['edit', 'id' => $model->id, 'receipt_id' => $receipt->id], '编辑', [
                                                'class' => 'btn btn-primary btn-xs openIframe',
                                                'data-width' => '90%',
                                                'data-height' => '90%',
                                                'data-offset' => '20px',
                                            ]);
                                        }
                                    },
                                    'delete' => function ($url, $model, $key) use ($receipt) {
                                        if ($receipt->receipt_status == BillStatusEnum::SAVE) {
                                            return Html::delete(['delete', 'id' => $model->id], '删除', [
                                                'class' => 'btn btn-danger btn-xs',
                                            ]);
                                        }
                                    },
                                ],
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
                                'attribute' => 'style_cate_id',
                                /*'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxSelect($model,'style_cate_id', \Yii::$app->styleService->styleCate->getDropDown(), ['data-id'=>$model->id, 'prompt'=>'请选择']);
                                },*/
                                'value' => "cate.name",
                                'filter' => Html::activeDropDownList($searchModel, 'style_cate_id', \Yii::$app->styleService->styleCate->getDropDown(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:100px;'
                                ]),
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#feeeed;'],
                            ],
                            [
                                'attribute' => 'product_type_id',
                                /*'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxSelect($model,'product_type_id', \Yii::$app->styleService->productType->getDropDown(), ['data-id'=>$model->id, 'prompt'=>'请选择']);
                                },*/
                                'value' => "type.name",
                                'filter' => Html::activeDropDownList($searchModel, 'product_type_id', Yii::$app->styleService->productType->getDropDown(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:100px;'
                                ]),
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#feeeed;'],
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
                                'attribute' => 'qiban_type',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#feeeed;'],
                                'value' => function ($model) {
                                    return \addons\Style\common\enums\QibanTypeEnum::getValue($model->qiban_type);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'qiban_type', $model->getQibanTypeMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                            ],
                            [
                                'attribute' => 'goods_name',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'goods_name', 'style' => 'background-color:#feeeed;'],
                                'value' => function ($model, $key, $index, $column) {
                                    return Html::ajaxInput('goods_name', $model->goods_name, ['data-id' => $model->id]);
                                },
                                'filter' => Html::activeTextInput($searchModel, 'goods_name', [
                                    'class' => 'form-control goods_name',
                                    'style' => 'width:200px;'
                                ]),
                            ],
                            /*[
                                'attribute' => 'order_sn',
                                'headerOptions' => ['class' => 'col-md-1', 'attr-name' => 'order_sn'],
                                'format' => 'raw',
                                //'value' => function ($model, $key, $index, $column){
                                //    return  Html::ajaxInput('order_sn', $model->order_sn, ['data-id'=>$model->id]);
                                //},
                                'filter' => Html::activeTextInput($searchModel, 'order_sn', [
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                            ],
                            [
                                'attribute' => 'produce_sn',
                                'headerOptions' => ['class' => 'col-md-1', 'attr-name' => 'produce_sn'],
                                'format' => 'raw',
                                //'value' => function ($model, $key, $index, $column){
                                //    return  Html::ajaxInput('produce_sn', $model->produce_sn, ['data-id'=>$model->id]);
                                //},
                                'filter' => Html::activeTextInput($searchModel, 'produce_sn', [
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                            ],*/
                            [
                                'class' => 'yii\grid\CheckboxColumn',
                                'name' => 'id',  //设置每行数据的复选框属性
                            ],
                            /*[
                                'attribute' => 'material',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    if(!empty($model->style_sn)){
                                        $data = \Yii::$app->styleService->styleAttribute->getAttrValueListByStyle($model->style_sn,AttrIdEnum::MATERIAL);
                                    }else{
                                        $data = \Yii::$app->attr->valueMap(AttrIdEnum::MATERIAL);
                                    }
                                    return Html::ajaxSelect($model, 'material', $data, ['data-id' => $model->id, 'prompt' => '请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'material', Yii::$app->attr->valueMap(\addons\Style\common\enums\AttrIdEnum::MATERIAL), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1 batch_select_full', 'attr-name' => 'material', 'attr-id' => AttrIdEnum::MATERIAL],
                            ],*/
                            [
                                'attribute' => 'material_type',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    return Html::ajaxSelect($model, 'material_type', $model->getMaterialTypeDrop($model), ['data-id' => $model->id, 'prompt' => '请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'material_type', $model->getMaterialTypeMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1 batch_select_full', 'attr-name' => 'material_type', 'attr-id' => AttrIdEnum::MATERIAL_TYPE, 'style' => 'background-color:#feeeed;'],
                            ],
                            [
                                'attribute' => 'material_color',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    return Html::ajaxSelect($model, 'material_color', $model->getMaterialColorDrop($model), ['data-id' => $model->id, 'prompt' => '请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'material_color', $model->getMaterialColorMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1 batch_select_full', 'attr-name' => 'material_color', 'attr-id' => AttrIdEnum::MATERIAL_COLOR, 'style' => 'background-color:#feeeed;'],
                            ],
                            [
                                'attribute' => 'goods_num',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#feeeed;'],
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'goods_num', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'finger_hk',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    return Html::ajaxSelect($model, 'finger_hk', $model->getPortNoDrop($model), ['data-id' => $model->id, 'prompt' => '请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'finger_hk', $model->getPortNoMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:100px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1 batch_select_full', 'attr-name' => 'finger_hk', 'attr-id' => AttrIdEnum::PORT_NO, 'style' => 'background-color:#feeeed;'],
                            ],
                            [
                                'attribute' => 'finger',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    return Html::ajaxSelect($model, 'finger', $model->getFingerDrop($model), ['data-id' => $model->id, 'prompt' => '请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'finger', $model->getFingerMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:100px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1 batch_select_full', 'attr-name' => 'finger', 'attr-id' => AttrIdEnum::FINGER, 'style' => 'background-color:#feeeed;'],
                            ],
                            [
                                'attribute' => 'length',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    return Html::ajaxInput('length', $model->length, ['data-id' => $model->id]);
                                },
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'length', 'style' => 'background-color:#feeeed;'],
                                'filter' => Html::activeTextInput($searchModel, 'length', [
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                            ],
                            [
                                'attribute' => 'product_size',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'product_size', 'style' => 'background-color:#feeeed;'],
                                'value' => function ($model, $key, $index, $column) {
                                    return Html::ajaxInput('product_size', $model->product_size, ['data-id' => $model->id]);
                                },
                                'filter' => Html::activeTextInput($searchModel, 'product_size', [
                                    'class' => 'form-control',
                                    'style' => 'width:100px;'
                                ]),
                            ],
                            [
                                'attribute' => 'xiangkou',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    return Html::ajaxSelect($model, 'xiangkou', $model->getXiangkouDrop($model), ['data-id' => $model->id, 'prompt' => '请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'xiangkou', $model->getXiangkouMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:100px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1 batch_select_full', 'attr-name' => 'xiangkou', 'attr-id' => AttrIdEnum::XIANGKOU, 'style' => 'background-color:#feeeed;'],
                            ],
                            [
                                'class' => 'yii\grid\CheckboxColumn',
                                'name' => 'id',  //设置每行数据的复选框属性
                            ],
                            [
                                'attribute' => 'goods_name',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#feeeed;'],
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'goods_name', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:200px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'kezi',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'kezi', 'style' => 'background-color:#feeeed;'],
                                'value' => function ($model, $key, $index, $column) {
                                    return Html::ajaxInput('kezi', $model->kezi, ['data-id' => $model->id]);
                                },
                                'filter' => Html::activeTextInput($searchModel, 'kezi', [
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                            ],
                            [
                                'attribute' => 'chain_type',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    return Html::ajaxSelect($model, 'chain_type', $model->getChainTypeDrop($model), ['data-id' => $model->id, 'prompt' => '请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'chain_type', $model->getChainTypeMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1 batch_select_full', 'attr-name' => 'chain_type', 'attr-id' => AttrIdEnum::CHAIN_TYPE, 'style' => 'background-color:#feeeed;'],
                            ],
                            [
                                'attribute' => 'chain_long',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'chain_long', 'style' => 'background-color:#feeeed;'],
                                'value' => function ($model, $key, $index, $column) {
                                    return Html::ajaxInput('chain_long', $model->chain_long, ['data-id' => $model->id]);
                                },
                                'filter' => Html::activeTextInput($searchModel, 'chain_long', [
                                    'class' => 'form-control',
                                    'style' => 'width:100px;'
                                ]),
                            ],
                            [
                                'attribute' => 'cramp_ring',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    return Html::ajaxSelect($model, 'cramp_ring', $model->getCrampRingDrop($model), ['data-id' => $model->id, 'prompt' => '请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'cramp_ring', $model->getCrampRingMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1 batch_select_full', 'attr-name' => 'cramp_ring', 'attr-id' => AttrIdEnum::CHAIN_BUCKLE, 'style' => 'background-color:#feeeed;'],
                            ],
                            [
                                'attribute' => 'talon_head_type',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    return Html::ajaxSelect($model, 'talon_head_type', $model->getTalonHeadTypeDrop($model), ['data-id' => $model->id, 'prompt' => '请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'talon_head_type', $model->getTalonHeadTypeMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1 batch_select_full', 'attr-name' => 'talon_head_type', 'attr-id' => AttrIdEnum::TALON_HEAD_TYPE, 'style' => 'background-color:#feeeed;'],
                            ],
                            [
                                'attribute' => 'gold_weight',
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'gold_weight', 'style' => 'background-color:#afdfe4;'],
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    return Html::ajaxInput('gold_weight', $model->gold_weight, ['data-id' => $model->id, 'onfocus' => 'rfClearVal(this)', 'data-type' => 'number']);
                                },
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'gold_weight', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'suttle_weight',
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'suttle_weight', 'style' => 'background-color:#afdfe4;'],
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    return Html::ajaxInput('suttle_weight', $model->suttle_weight, ['data-id' => $model->id, 'onfocus' => 'rfClearVal(this)', 'data-type' => 'number']);
                                },
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'suttle_weight', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                            ],
                            [
                                'class' => 'yii\grid\CheckboxColumn',
                                'name' => 'id',  //设置每行数据的复选框属性
                            ],
                            [
                                'attribute' => 'goods_name',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#afdfe4;'],
                                'filter' => Html::activeTextInput($searchModel, 'goods_name', [
                                    'class' => 'form-control',
                                    'style' => 'width:200px;'
                                ]),
                            ],
                            [
                                'attribute' => 'gold_loss',
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'gold_loss', 'style' => 'background-color:#afdfe4;'],
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    return Html::ajaxInput('gold_loss', $model->gold_loss, ['data-id' => $model->id, 'onfocus' => 'rfClearVal(this)', 'data-type' => 'number']);
                                },
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'gold_loss', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'gold_price',
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'gold_price', 'style' => 'background-color:#afdfe4;'],
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    return Html::ajaxInput('gold_price', $model->gold_price, ['data-id' => $model->id, 'onfocus' => 'rfClearVal(this)', 'data-type' => 'number']);
                                },
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'gold_price', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                            ],
                            /*[
                                'attribute' => 'gold_amount',
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'gold_amount'],
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    return Html::ajaxInput('gold_amount', $model->gold_amount, ['data-id' => $model->id, 'onfocus' => 'rfClearVal(this)', 'data-type' => 'number']);
                                },
                                'filter' => Html::activeTextInput($searchModel, 'gold_amount', [
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                            ],
                            [
                                'attribute' => 'gross_weight',
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'gross_weight'],
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    return Html::ajaxInput('gross_weight', $model->gross_weight, ['data-id' => $model->id, 'onfocus' => 'rfClearVal(this)', 'data-type' => 'number']);
                                },
                                'filter' => Html::activeTextInput($searchModel, 'gross_weight', [
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                            ],*/
//                            [
//                                'attribute' => 'cert_id',
//                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#afdfe4;'],
//                                'format' => 'raw',
//                                'value' => function ($model, $key, $index, $column) {
//                                    return Html::ajaxInput('cert_id', $model->cert_id, ['data-id' => $model->id]);
//                                },
//                                'filter' => Html::activeTextInput($searchModel, 'cert_id', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:100px;'
//                                ]),
//                            ],
//                            [
//                                'attribute' => 'cert_type',
//                                'format' => 'raw',
//                                'value' => function ($model, $key, $index, $column) {
//                                    return Html::ajaxSelect($model, 'cert_type', $model->getCertTypeDrop($model), ['data-id' => $model->id, 'prompt' => '请选择']);
//                                },
//                                'filter' => Html::activeDropDownList($searchModel, 'cert_type', $model->getCertTypeMap(), [
//                                    'prompt' => '全部',
//                                    'class' => 'form-control',
//                                    'style' => 'width:100px;'
//                                ]),
//                                'headerOptions' => ['class' => 'col-md-1 batch_select_full', 'attr-name' => 'finger', 'attr-id' => AttrIdEnum::DIA_CERT_TYPE, 'style' => 'background-color:#afdfe4;'],
//                            ],
                            [
                                'class' => 'yii\grid\CheckboxColumn',
                                'name' => 'id',  //设置每行数据的复选框属性
                            ],
                            [
                                'attribute' => 'goods_name',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#afb4db;'],
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'goods_name', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:200px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'main_stone_sn',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    return Html::ajaxInput('main_stone_sn', $model->main_stone_sn, ['data-id' => $model->id]);
                                },
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'main_stone_sn', 'style' => 'background-color:#afb4db;'],
                                'filter' => Html::activeTextInput($searchModel, 'main_stone_sn', [
                                    'class' => 'form-control',
                                    'style' => 'width:100px;'
                                ]),
                            ],
                            [
                                'attribute' => 'main_stone',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    return Html::ajaxSelect($model, 'main_stone', $model->getMainStoneTypeDrop($model), ['data-id' => $model->id, 'prompt' => '请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'main_stone', $model->getMainStoneTypeMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1 batch_select_full', 'attr-name' => 'main_stone', 'attr-id' => AttrIdEnum::MAIN_STONE_TYPE, 'style' => 'background-color:#afb4db;'],
                            ],
                            [
                                'attribute' => 'main_cert_id',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    return Html::ajaxInput('main_cert_id', $model->main_cert_id, ['data-id' => $model->id]);
                                },
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'main_cert_id', 'style' => 'background-color:#afb4db;'],
                                'filter' => Html::activeTextInput($searchModel, 'main_cert_id', [
                                    'class' => 'form-control',
                                    'style' => 'width:100px;'
                                ]),
                            ],
                            [
                                'attribute' => 'main_stone_num',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    return Html::ajaxInput('main_stone_num', $model->main_stone_num, ['data-id' => $model->id, 'onfocus' => 'rfClearVal(this)', 'data-type' => 'number']);
                                },
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'main_stone_num', 'style' => 'background-color:#afb4db;'],
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'main_stone_num', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'main_stone_weight',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    return Html::ajaxInput('main_stone_weight', $model->main_stone_weight, ['data-id' => $model->id, 'onfocus' => 'rfClearVal(this)', 'data-type' => 'number']);
                                },
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'main_stone_weight', 'style' => 'background-color:#afb4db;'],
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'main_stone_weight', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'main_stone_shape',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    return Html::ajaxSelect($model, 'main_stone_shape', $model->getMainStoneShapeDrop($model), ['data-id' => $model->id, 'prompt' => '请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'main_stone_shape', $model->getMainStoneShapeMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1 batch_select_full', 'attr-name' => 'main_stone_shape', 'attr-id' => AttrIdEnum::MAIN_STONE_SHAPE, 'style' => 'background-color:#afb4db;'],
                            ],
                            [
                                'attribute' => 'main_stone_color',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    return Html::ajaxSelect($model, 'main_stone_color', $model->getMainStoneColorDrop($model), ['data-id' => $model->id, 'prompt' => '请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'main_stone_color', $model->getMainStoneColorMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1 batch_select_full', 'attr-name' => 'main_stone_color', 'attr-id' => AttrIdEnum::MAIN_STONE_COLOR, 'style' => 'background-color:#afb4db;'],
                            ],
                            [
                                'attribute' => 'main_stone_clarity',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    return Html::ajaxSelect($model, 'main_stone_clarity', $model->getMainStoneClarityDrop($model), ['data-id' => $model->id, 'prompt' => '请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'main_stone_clarity', $model->getMainStoneClarityMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1 batch_select_full', 'attr-name' => 'main_stone_clarity', 'attr-id' => AttrIdEnum::MAIN_STONE_CLARITY, 'style' => 'background-color:#afb4db;'],
                            ],
                            [
                                'attribute' => 'main_stone_cut',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    return Html::ajaxSelect($model, 'main_stone_cut', $model->getMainStoneCutDrop($model), ['data-id' => $model->id, 'prompt' => '请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'main_stone_cut', $model->getMainStoneCutMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1 batch_select_full', 'attr-name' => 'main_stone_cut', 'attr-id' => AttrIdEnum::MAIN_STONE_CUT, 'style' => 'background-color:#afb4db;'],
                            ],
                            [
                                'attribute' => 'main_stone_colour',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    return Html::ajaxSelect($model, 'main_stone_colour', $model->getMainStoneColourDrop($model), ['data-id' => $model->id, 'prompt' => '请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'main_stone_colour', $model->getMainStoneColourMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1 batch_select_full', 'attr-name' => 'main_stone_colour', 'attr-id' => AttrIdEnum::MAIN_STONE_COLOUR, 'style' => 'background-color:#afb4db;'],
                            ],
                            [
                                'attribute' => 'main_stone_size',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    return Html::ajaxInput('main_stone_size', $model->main_stone_size, ['data-id' => $model->id]);
                                },
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#afb4db;'],
                                'filter' => Html::activeTextInput($searchModel, 'main_stone_size', [
                                    'class' => 'form-control',
                                    'style' => 'width:100px;'
                                ]),
                            ],
                            [
                                'attribute' => 'main_stone_price',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    return Html::ajaxInput('main_stone_price', $model->main_stone_price, ['data-id' => $model->id, 'onfocus' => 'rfClearVal(this)', 'data-type' => 'number']);
                                },
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'main_stone_price', 'style' => 'background-color:#afb4db;'],
                                'filter' => Html::activeTextInput($searchModel, 'main_stone_price', [
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                            ],
                            [
                                'class' => 'yii\grid\CheckboxColumn',
                                'name' => 'id',  //设置每行数据的复选框属性
                            ],
                            [
                                'attribute' => 'goods_name',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#dec674;'],
                                'filter' => Html::activeTextInput($searchModel, 'goods_name', [
                                    'class' => 'form-control',
                                    'style' => 'width:200px;'
                                ]),
                            ],
                            [
                                'attribute' => 'second_stone_sn1',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    return Html::ajaxInput('second_stone_sn1', $model->second_stone_sn1, ['data-id' => $model->id]);
                                },
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'second_stone_sn1', 'style' => 'background-color:#dec674;'],
                                'filter' => Html::activeTextInput($searchModel, 'second_stone_sn1', [
                                    'class' => 'form-control',
                                    'style' => 'width:100px;'
                                ]),
                            ],
                            [
                                'attribute' => 'second_stone1',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    return Html::ajaxSelect($model, 'second_stone1', $model->getSecondStoneType1Drop($model), ['data-id' => $model->id, 'prompt' => '请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'second_stone1', $model->getSecondStoneType1Map(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1 batch_select_full', 'attr-name' => 'second_stone1', 'attr-id' => AttrIdEnum::SIDE_STONE1_TYPE, 'style' => 'background-color:#dec674;'],
                            ],
//                            [
//                                'attribute' => 'second_cert_id1',
//                                'format' => 'raw',
//                                'value' => function ($model, $key, $index, $column) {
//                                    return Html::ajaxInput('second_cert_id1', $model->second_cert_id1, ['data-id' => $model->id]);
//                                },
//                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'second_cert_id1', 'style' => 'background-color:#dec674;'],
//                                'filter' => Html::activeTextInput($searchModel, 'second_cert_id1', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:100px;'
//                                ]),
//                            ],
                            [
                                'attribute' => 'second_stone_num1',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    return Html::ajaxInput('second_stone_num1', $model->second_stone_num1, ['data-id' => $model->id, 'onfocus' => 'rfClearVal(this)', 'data-type' => 'number']);
                                },
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'second_stone_num1', 'style' => 'background-color:#dec674;'],
//                                'filter' => Html::activeTextInput($searchModel, 'second_stone_num1', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'second_stone_weight1',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    return Html::ajaxInput('second_stone_weight1', $model->second_stone_weight1, ['data-id' => $model->id, 'onfocus' => 'rfClearVal(this)', 'data-type' => 'number']);
                                },
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'second_stone_weight1', 'style' => 'background-color:#dec674;'],
//                                'filter' => Html::activeTextInput($searchModel, 'second_stone_weight1', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'second_stone_shape1',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    return Html::ajaxSelect($model, 'second_stone_shape1', $model->getSecondStoneShape1Drop($model), ['data-id' => $model->id, 'prompt' => '请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'second_stone_shape1', $model->getSecondStoneShape1Map(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1 batch_select_full', 'attr-name' => 'second_stone_shape1', 'attr-id' => AttrIdEnum::SIDE_STONE1_SHAPE, 'style' => 'background-color:#dec674;'],
                            ],
                            [
                                'attribute' => 'second_stone_color1',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    return Html::ajaxSelect($model, 'second_stone_color1', $model->getSecondStoneColor1Drop($model), ['data-id' => $model->id, 'prompt' => '请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'second_stone_color1', $model->getSecondStoneColor1Map(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1 batch_select_full', 'attr-name' => 'second_stone_color1', 'attr-id' => AttrIdEnum::SIDE_STONE1_COLOR, 'style' => 'background-color:#dec674;'],
                            ],
                            [
                                'attribute' => 'second_stone_clarity1',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    return Html::ajaxSelect($model, 'second_stone_clarity1', $model->getSecondStoneClarity1Drop($model), ['data-id' => $model->id, 'prompt' => '请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'second_stone_clarity1', $model->getSecondStoneClarity1Map(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1 batch_select_full', 'attr-name' => 'second_stone_clarity1', 'attr-id' => AttrIdEnum::SIDE_STONE1_CLARITY, 'style' => 'background-color:#dec674;'],
                            ],
                            [
                                'attribute' => 'second_stone_colour1',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    return Html::ajaxSelect($model, 'second_stone_colour1', $model->getSecondStoneColour1Drop($model), ['data-id' => $model->id, 'prompt' => '请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'second_stone_colour1', $model->getSecondStoneColour1Map(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1 batch_select_full', 'attr-name' => 'second_stone_colour1', 'attr-id' => AttrIdEnum::SIDE_STONE1_COLOUR, 'style' => 'background-color:#dec674;'],
                            ],
//                            [
//                                'attribute' => 'second_stone_size1',
//                                'format' => 'raw',
//                                'value' => function ($model, $key, $index, $column) {
//                                    return Html::ajaxInput('second_stone_size1', $model->second_stone_size1, ['data-id' => $model->id]);
//                                },
//                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#dec674;'],
//                                'filter' => Html::activeTextInput($searchModel, 'second_stone_size1', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:100px;'
//                                ]),
//                            ],
                            [
                                'attribute' => 'second_stone_price1',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    return Html::ajaxInput('second_stone_price1', $model->second_stone_price1, ['data-id' => $model->id, 'onfocus' => 'rfClearVal(this)', 'data-type' => 'number']);
                                },
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'second_stone_price1', 'style' => 'background-color:#dec674;'],
                                'filter' => Html::activeTextInput($searchModel, 'second_stone_price1', [
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                            ],
                            [
                                'class' => 'yii\grid\CheckboxColumn',
                                'name' => 'id',  //设置每行数据的复选框属性
                            ],
                            [
                                'attribute' => 'goods_name',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#84bf96;'],
                                'filter' => Html::activeTextInput($searchModel, 'goods_name', [
                                    'class' => 'form-control',
                                    'style' => 'width:200px;'
                                ]),
                            ],
                            [
                                'attribute' => 'second_stone_sn2',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    return Html::ajaxInput('second_stone_sn2', $model->second_stone_sn2, ['data-id' => $model->id]);
                                },
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'second_stone_sn2', 'style' => 'background-color:#84bf96;'],
                                'filter' => Html::activeTextInput($searchModel, 'second_stone_sn2', [
                                    'class' => 'form-control',
                                    'style' => 'width:100px;'
                                ]),
                            ],
                            [
                                'attribute' => 'second_stone2',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    return Html::ajaxSelect($model, 'second_stone2', $model->getSecondStoneType2Drop($model), ['data-id' => $model->id, 'prompt' => '请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'second_stone2', $model->getSecondStoneType2Map(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1 batch_select_full', 'attr-name' => 'second_stone2', 'attr-id' => AttrIdEnum::SIDE_STONE2_TYPE, 'style' => 'background-color:#84bf96;'],
                            ],
                            [
                                'attribute' => 'second_cert_id2',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    return Html::ajaxInput('second_cert_id2', $model->second_cert_id2, ['data-id' => $model->id]);
                                },
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'second_cert_id2', 'style' => 'background-color:#84bf96;'],
                                'filter' => Html::activeTextInput($searchModel, 'second_cert_id2', [
                                    'class' => 'form-control',
                                    'style' => 'width:100px;'
                                ]),
                            ],
                            [
                                'attribute' => 'second_stone_num2',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    return Html::ajaxInput('second_stone_num2', $model->second_stone_num2, ['data-id' => $model->id, 'onfocus' => 'rfClearVal(this)', 'data-type' => 'number']);
                                },
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'second_stone_num2', 'style' => 'background-color:#84bf96;'],
//                                'filter' => Html::activeTextInput($searchModel, 'second_stone_num2', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'second_stone_weight2',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    return Html::ajaxInput('second_stone_weight2', $model->second_stone_weight2, ['data-id' => $model->id, 'onfocus' => 'rfClearVal(this)', 'data-type' => 'number']);
                                },
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'second_stone_weight2', 'style' => 'background-color:#84bf96;'],
//                                'filter' => Html::activeTextInput($searchModel, 'second_stone_weight2', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'second_stone_shape2',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    return Html::ajaxSelect($model, 'second_stone_shape2', $model->getSecondStoneShape2Drop($model), ['data-id' => $model->id, 'prompt' => '请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'second_stone_shape2', $model->getSecondStoneShape2Map(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1 batch_select_full', 'attr-name' => 'second_stone_shape2', 'attr-id' => AttrIdEnum::SIDE_STONE2_SHAPE, 'style' => 'background-color:#84bf96;'],
                            ],
                            [
                                'attribute' => 'second_stone_color2',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    return Html::ajaxSelect($model, 'second_stone_color2', $model->getSecondStoneColor2Drop($model), ['data-id' => $model->id, 'prompt' => '请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'second_stone_color2', $model->getSecondStoneColor2Map(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1 batch_select_full', 'attr-name' => 'second_stone_color2', 'attr-id' => AttrIdEnum::SIDE_STONE2_COLOR, 'style' => 'background-color:#84bf96;'],
                            ],
                            [
                                'attribute' => 'second_stone_clarity2',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    return Html::ajaxSelect($model, 'second_stone_clarity2', $model->getSecondStoneClarity2Drop($model), ['data-id' => $model->id, 'prompt' => '请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'second_stone_clarity2', $model->getSecondStoneClarity2Map(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1 batch_select_full', 'attr-name' => 'second_stone_clarity2', 'attr-id' => AttrIdEnum::SIDE_STONE2_CLARITY, 'style' => 'background-color:#84bf96;'],
                            ],
                            /*[
                                'attribute' => 'second_stone_colour2',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    if(!empty($model->style_sn)){
                                        $data = \Yii::$app->styleService->styleAttribute->getAttrValueListByStyle($model->style_sn,AttrIdEnum::SIDE_STONE2_COLOUR);
                                    }else{
                                        $data = \Yii::$app->attr->valueMap(AttrIdEnum::SIDE_STONE2_COLOUR);
                                    }
                                    return Html::ajaxSelect($model, 'second_stone_colour2', $data, ['data-id' => $model->id, 'prompt' => '请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'second_stone_colour2', Yii::$app->attr->valueMap(\addons\Style\common\enums\AttrIdEnum::SIDE_STONE2_COLOUR), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1 batch_select_full', 'attr-name' => 'second_stone_colour2', 'attr-id' => AttrIdEnum::SIDE_STONE2_COLOUR],
                            ],*/
                            [
                                'attribute' => 'second_stone_size2',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    return Html::ajaxInput('second_stone_size2', $model->second_stone_size2, ['data-id' => $model->id]);
                                },
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#84bf96;'],
                                'filter' => Html::activeTextInput($searchModel, 'second_stone_size2', [
                                    'class' => 'form-control',
                                    'style' => 'width:100px;'
                                ]),
                            ],
                            [
                                'attribute' => 'second_stone_price2',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    return Html::ajaxInput('second_stone_price2', $model->second_stone_price2, ['data-id' => $model->id, 'onfocus' => 'rfClearVal(this)', 'data-type' => 'number']);
                                },
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'second_stone_price2', 'style' => 'background-color:#84bf96;'],
//                                'filter' => Html::activeTextInput($searchModel, 'second_stone_price2', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                            ],
                            /*[
                                'class' => 'yii\grid\CheckboxColumn',
                                'name' => 'id',  //设置每行数据的复选框属性
                            ],
                            [
                                'attribute' => 'goods_name',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#f8aba6;'],
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'goods_name', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:200px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'second_stone3',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    return Html::ajaxSelect($model, 'second_stone3', $model->getSecondStoneType3Drop($model), ['data-id' => $model->id, 'prompt' => '请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'second_stone3', $model->getSecondStoneType3Map(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1 batch_select_full', 'attr-name' => 'second_stone3', 'attr-id' => AttrIdEnum::SIDE_STONE3_TYPE, 'style' => 'background-color:#f8aba6;'],
                            ],
                            [
                                'attribute' => 'second_stone_num3',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    return Html::ajaxInput('second_stone_num3', $model->second_stone_num3, ['data-id' => $model->id, 'onfocus' => 'rfClearVal(this)', 'data-type' => 'number']);
                                },
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'second_stone_num3', 'style' => 'background-color:#f8aba6;'],
//                                'filter' => Html::activeTextInput($searchModel, 'second_stone_num3', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'second_stone_weight3',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    return Html::ajaxInput('second_stone_weight3', $model->second_stone_weight3, ['data-id' => $model->id, 'onfocus' => 'rfClearVal(this)', 'data-type' => 'number']);
                                },
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'second_stone_weight3', 'style' => 'background-color:#f8aba6;'],
//                                'filter' => Html::activeTextInput($searchModel, 'second_stone_weight3', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'second_stone_price3',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    return Html::ajaxInput('second_stone_price3', $model->second_stone_price3, ['data-id' => $model->id, 'onfocus' => 'rfClearVal(this)', 'data-type' => 'number']);
                                },
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'second_stone_price3', 'style' => 'background-color:#f8aba6;'],
//                                'filter' => Html::activeTextInput($searchModel, 'second_stone_price3', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                            ],*/
                            [
                                'attribute' => 'stone_remark',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    return Html::ajaxInput('stone_remark', $model->stone_remark, ['data-id' => $model->id]);
                                },
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#f8aba6;'],
                                'filter' => Html::activeTextInput($searchModel, 'stone_remark', [
                                    'class' => 'form-control',
                                    'style' => 'width:160px;'
                                ]),
                            ],
                            [
                                'attribute' => 'peishi_fee',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    return Html::ajaxInput('peishi_fee', $model->peishi_fee, ['data-id' => $model->id, 'onfocus' => 'rfClearVal(this)', 'data-type' => 'number']);
                                },
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'peishi_fee', 'style' => 'background-color:#f8aba6;'],
//                                'filter' => Html::activeTextInput($searchModel, 'peishi_fee', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'peishi_gong_fee',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    return Html::ajaxInput('peishi_gong_fee', $model->peishi_gong_fee, ['data-id' => $model->id, 'onfocus' => 'rfClearVal(this)', 'data-type' => 'number']);
                                },
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'peishi_gong_fee', 'style' => 'background-color:#f8aba6;'],
//                                'filter' => Html::activeTextInput($searchModel, 'peishi_gong_fee', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                            ],
                            [
                                'class' => 'yii\grid\CheckboxColumn',
                                'name' => 'id',  //设置每行数据的复选框属性
                            ],
                            [
                                'attribute' => 'goods_name',
                                'format' => 'raw',
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#cde6c7;'],
//                                'filter' => Html::activeTextInput($searchModel, 'goods_name', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:200px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'parts_way',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#cde6c7;'],
                                'value' => function ($model) {
                                    return Html::ajaxSelect($model, 'parts_way', $model->getPeiJianWayMap(), ['data-id' => $model->id, 'prompt' => '请选择']);
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
                                'value' => function ($model, $key, $index, $column) {
                                    return Html::ajaxSelect($model, 'parts_type', $model->getPartsTypeDrop($model), ['data-id' => $model->id, 'prompt' => '请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'parts_type', $model->getPartsTypeMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1 batch_select_full', 'attr-name' => 'parts_type', 'attr-id' => AttrIdEnum::MAT_PARTS_TYPE, 'style' => 'background-color:#cde6c7;'],
                            ],
                            [
                                'attribute' => 'parts_material',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    return Html::ajaxSelect($model, 'parts_material', $model->getPartsMaterialDrop($model), ['data-id' => $model->id, 'prompt' => '请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'parts_material', $model->getPartsMaterialMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1 batch_select_full', 'attr-name' => 'parts_material', 'attr-id' => AttrIdEnum::MATERIAL_TYPE, 'style' => 'background-color:#cde6c7;'],
                            ],
                            [
                                'attribute' => 'parts_num',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    return Html::ajaxInput('parts_num', $model->parts_num, ['data-id' => $model->id, 'onfocus' => 'rfClearVal(this)', 'data-type' => 'number']);
                                },
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'parts_num', 'style' => 'background-color:#cde6c7;'],
//                                'filter' => Html::activeTextInput($searchModel, 'parts_num', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'parts_gold_weight',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    return Html::ajaxInput('parts_gold_weight', $model->parts_gold_weight, ['data-id' => $model->id, 'onfocus' => 'rfClearVal(this)', 'data-type' => 'number']);
                                },
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'parts_gold_weight', 'style' => 'background-color:#cde6c7;'],
//                                'filter' => Html::activeTextInput($searchModel, 'parts_gold_weight', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'parts_price',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    return Html::ajaxInput('parts_price', $model->parts_price, ['data-id' => $model->id, 'onfocus' => 'rfClearVal(this)', 'data-type' => 'number']);
                                },
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'parts_price', 'style' => 'background-color:#cde6c7;'],
//                                'filter' => Html::activeTextInput($searchModel, 'parts_price', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                            ],
                            /*[
                                'attribute' => 'parts_amount',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    return Html::ajaxInput('parts_amount', $model->parts_amount, ['data-id' => $model->id, 'onfocus' => 'rfClearVal(this)', 'data-type' => 'number']);
                                },
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'parts_amount'],
                                'filter' => Html::activeTextInput($searchModel, 'parts_amount', [
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                            ],*/
                            [
                                'attribute' => 'parts_fee',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    return Html::ajaxInput('parts_fee', $model->parts_fee, ['data-id' => $model->id, 'onfocus' => 'rfClearVal(this)', 'data-type' => 'number']);
                                },
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'parts_fee', 'style' => 'background-color:#cde6c7;'],
//                                'filter' => Html::activeTextInput($searchModel, 'parts_fee', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                            ],
                            [
                                'class' => 'yii\grid\CheckboxColumn',
                                'name' => 'id',  //设置每行数据的复选框属性
                            ],
                            [
                                'attribute' => 'goods_name',
                                'format' => 'raw',
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
//                                'filter' => Html::activeTextInput($searchModel, 'goods_name', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:200px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'gong_fee',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    return Html::ajaxInput('gong_fee', $model->gong_fee, ['data-id' => $model->id, 'onfocus' => 'rfClearVal(this)', 'data-type' => 'number']);
                                },
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'gong_fee', 'style' => 'background-color:#b7ba6b;'],
//                                'filter' => Html::activeTextInput($searchModel, 'gong_fee', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'xiangqian_craft',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    return Html::ajaxSelect($model, 'xiangqian_craft', $model->getXiangqianCraftDrop($model), ['data-id' => $model->id, 'prompt' => '请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'xiangqian_craft', $model->getXiangqianCraftMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1 batch_select_full', 'attr-name' => 'xiangqian_craft', 'attr-id' => AttrIdEnum::XIANGQIAN_CRAFT, 'style' => 'background-color:#b7ba6b;'],
                            ],
                            [
                                'attribute' => 'xianqian_price',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    return Html::ajaxInput('xianqian_price', $model->xianqian_price, ['data-id' => $model->id, 'onfocus' => 'rfClearVal(this)', 'data-type' => 'number']);
                                },
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'xianqian_price', 'style' => 'background-color:#b7ba6b;'],
//                                'filter' => Html::activeTextInput($searchModel, 'xianqian_price', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'biaomiangongyi',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    return Html::ajaxSelect($model, 'biaomiangongyi', $model->getFaceCraftDrop($model), ['data-id' => $model->id, 'prompt' => '请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'biaomiangongyi', $model->getFaceCraftMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:100px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1 batch_select_full', 'attr-name' => 'biaomiangongyi', 'attr-id' => AttrIdEnum::FACEWORK, 'style' => 'background-color:#b7ba6b;'],
                            ],
                            [
                                'attribute' => 'biaomiangongyi_fee',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    return Html::ajaxInput('biaomiangongyi_fee', $model->biaomiangongyi_fee, ['data-id' => $model->id, 'onfocus' => 'rfClearVal(this)', 'data-type' => 'number']);
                                },
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'biaomiangongyi_fee', 'style' => 'background-color:#b7ba6b;'],
//                                'filter' => Html::activeTextInput($searchModel, 'biaomiangongyi_fee', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'fense_fee',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    return Html::ajaxInput('fense_fee', $model->fense_fee, ['data-id' => $model->id, 'onfocus' => 'rfClearVal(this)', 'data-type' => 'number']);
                                },
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'fense_fee', 'style' => 'background-color:#b7ba6b;'],
//                                'filter' => Html::activeTextInput($searchModel, 'fense_fee', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'penlasha_fee',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    return Html::ajaxInput('penlasha_fee', $model->penlasha_fee, ['data-id' => $model->id, 'onfocus' => 'rfClearVal(this)', 'data-type' => 'number']);
                                },
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'penlasha_fee', 'style' => 'background-color:#b7ba6b;'],
//                                'filter' => Html::activeTextInput($searchModel, 'penlasha_fee', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'bukou_fee',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    return Html::ajaxInput('bukou_fee', $model->bukou_fee, ['data-id' => $model->id, 'onfocus' => 'rfClearVal(this)', 'data-type' => 'number']);
                                },
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'bukou_fee', 'style' => 'background-color:#b7ba6b;'],
//                                'filter' => Html::activeTextInput($searchModel, 'bukou_fee', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                            ],
                            [
                                'class' => 'yii\grid\CheckboxColumn',
                                'name' => 'id',  //设置每行数据的复选框属性
                            ],
                            [
                                'attribute' => 'goods_name',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'goods_name', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:200px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'templet_fee',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    return Html::ajaxInput('templet_fee', $model->templet_fee, ['data-id' => $model->id, 'onfocus' => 'rfClearVal(this)', 'data-type' => 'number']);
                                },
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'templet_fee', 'style' => 'background-color:#b7ba6b;'],
//                                'filter' => Html::activeTextInput($searchModel, 'templet_fee', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'cert_fee',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    return Html::ajaxInput('cert_fee', $model->cert_fee, ['data-id' => $model->id, 'onfocus' => 'rfClearVal(this)', 'data-type' => 'number']);
                                },
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'cert_fee', 'style' => 'background-color:#b7ba6b;'],
//                                'filter' => Html::activeTextInput($searchModel, 'cert_fee', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'other_fee',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    return Html::ajaxInput('other_fee', $model->other_fee, ['data-id' => $model->id, 'onfocus' => 'rfClearVal(this)', 'data-type' => 'number']);
                                },
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'other_fee', 'style' => 'background-color:#b7ba6b;'],
//                                'filter' => Html::activeTextInput($searchModel, 'other_fee', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'factory_cost',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    return Html::ajaxInput('factory_cost', $model->factory_cost, ['data-id' => $model->id, 'onfocus' => 'rfClearVal(this)', 'data-type' => 'number']);
                                },
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'factory_cost', 'style' => 'background-color:#b7ba6b;'],
//                                'filter' => Html::activeTextInput($searchModel, 'factory_cost', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:100px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'cost_price',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    return Html::ajaxInput('cost_price', $model->cost_price, ['data-id' => $model->id, 'onfocus' => 'rfClearVal(this)', 'data-type' => 'number']);
                                },
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'cost_price', 'style' => 'background-color:#b7ba6b;'],
//                                'filter' => Html::activeTextInput($searchModel, 'cost_price', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:100px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'markup_rate',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    return Html::ajaxInput('markup_rate', $model->markup_rate, ['data-id' => $model->id]);
                                },
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'markup_rate', 'style' => 'background-color:#b7ba6b;'],
                                'filter' => Html::activeTextInput($searchModel, 'markup_rate', [
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                            ],
                            [
                                'attribute' => 'style_sex',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
                                'value' => function ($model) {
                                    return \addons\Style\common\enums\StyleSexEnum::getValue($model->style_sex);
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
                                    return Html::ajaxSelect($model, 'jintuo_type', $model->getJietuoTypeMap(), ['data-id' => $model->id, 'prompt' => '请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'jintuo_type', $model->getJietuoTypeMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:100px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
                            ],
                            [
                                'attribute' => 'qiban_sn',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
                                'filter' => Html::activeTextInput($searchModel, 'qiban_sn', [
                                    'class' => 'form-control',
                                    'style' => 'width:100px;'
                                ]),
                            ],
                            [
                                'attribute' => 'is_inlay',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    //return \addons\Style\common\enums\InlayEnum::getValue($model->is_inlay);
                                    return Html::ajaxSelect($model, 'is_inlay', $model->getIsInlayMap(), ['data-id' => $model->id, 'prompt' => '请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'is_inlay', $model->getIsInlayMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
                            ],
//                            [
//                                'attribute'=>'total_gong_fee',
//                                'format' => 'raw',
//                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'total_gong_fee'],
//                                'value' => function ($model, $key, $index, $column){
//                                    return  Html::ajaxInput('total_gong_fee', $model->total_gong_fee, ['data-id'=>$model->id]);
//                                },
//                                'filter' => Html::activeTextInput($searchModel, 'total_gong_fee', [
//                                    'class' => 'form-control',
//                                    'style'=> 'width:80px;'
//                                ]),
//                            ],
                            [
                                'attribute' => 'barcode',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
                                'value' => function ($model, $key, $index, $column) {
                                    return Html::ajaxInput('barcode', $model->barcode, ['data-id' => $model->id]);
                                },
                                'filter' => Html::activeTextInput($searchModel, 'produce_sn', [
                                    'class' => 'form-control',
                                    'style' => 'width:100px;'
                                ]),
                            ],
                            [
                                'attribute' => 'goods_remark',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
                                'value' => function ($model, $key, $index, $column) {
                                    return Html::ajaxInput('goods_remark', $model->goods_remark, ['data-id' => $model->id]);
                                },
                                'filter' => Html::activeTextInput($searchModel, 'goods_remark', [
                                    'class' => 'form-control',
                                    'style' => 'width:150px;'
                                ]),
                            ],
                            [
                                'attribute' => 'market_price',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    return Html::ajaxInput('market_price', $model->market_price, ['data-id' => $model->id, 'onfocus' => 'rfClearVal(this)', 'data-type' => 'number']);
                                },
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'market_price', 'style' => 'background-color:#b7ba6b;'],
//                                'filter' => Html::activeTextInput($searchModel, 'market_price', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:100px;'
//                                ]),
                            ],
                            [
                                'class' => 'yii\grid\CheckboxColumn',
                                'name' => 'id',  //设置每行数据的复选框属性
                            ],
                            [
                                'class' => 'yii\grid\ActionColumn',
                                'header' => '操作',
                                'contentOptions' => ['style' => ['white-space' => 'nowrap']],
                                'template' => '{edit} {delete}',
                                'buttons' => [
                                    'edit' => function ($url, $model, $key) use ($receipt) {
                                        if ($receipt->receipt_status == \addons\Purchase\common\enums\ReceiptStatusEnum::SAVE) {
                                            return Html::edit(['edit', 'id' => $model->id, 'receipt_id' => $receipt->id], '编辑', [
                                                'class' => 'btn btn-primary btn-xs openIframe',
                                                'data-width' => '90%',
                                                'data-height' => '90%',
                                                'data-offset' => '20px',
                                            ]);
                                        }
                                    },
                                    'delete' => function ($url, $model, $key) use ($receipt) {
                                        if ($receipt->receipt_status == \addons\Purchase\common\enums\ReceiptStatusEnum::SAVE) {
                                            return Html::delete(['delete', 'id' => $model->id], '删除', [
                                                'class' => 'btn btn-danger btn-xs',
                                            ]);
                                        }
                                    },
                                ],
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
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
<script type="text/javascript">
    $(function () {
        $(".batch_full > a").after('&nbsp;<?= Html::batchFullButton(['batch-edit'], "批量填充"); ?>');
        $(".batch_select_full > a").after('&nbsp;<?= Html::batchFullButton(['batch-edit', 'check' => 1], "批量填充", ['input_type' => 'select']); ?>');
    });

    function rfClearVal(obj) {
        var val = $(obj).val();
        if (val <= 0) {
            $(obj).val("");
        }
    }
</script>
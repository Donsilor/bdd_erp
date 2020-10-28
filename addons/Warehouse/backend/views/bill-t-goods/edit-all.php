<?php

use common\helpers\Html;
use common\helpers\ImageHelper;
use common\helpers\Url;
use yii\grid\GridView;
use addons\Style\common\enums\AttrIdEnum;
use addons\Warehouse\common\enums\BillStatusEnum;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $searchModel yii\data\ActiveDataProvider */
/* @var $tabList yii\data\ActiveDataProvider */
/* @var $tab yii\data\ActiveDataProvider */
/* @var $bill yii\data\ActiveDataProvider */

$this->title = Yii::t('bill_t_goods', '其它入库单明细');
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$goods_type = $bill->billL->goods_type ?? 0;
?>
<style>
    select.form-control {
        font-size: 12px;
    }
</style>
<div class="box-body nav-tabs-custom">
    <h2 class="page-header"><?= $this->title; ?> - <?= $bill->bill_no ?> <i class="fa fa-copy"
                                                                            onclick="copy(<?= $bill->bill_no ?>)"></i>
        - <?= \addons\Warehouse\common\enums\BillStatusEnum::getValue($bill->bill_status) ?></h2>
    <?php echo Html::menuTab($tabList, $tab) ?>
    <div class="box-tools" style="float:right;margin-top:-40px; margin-right: 20px;">
        <?php
        if ($bill->bill_status == \addons\Warehouse\common\enums\BillStatusEnum::SAVE) {
            echo Html::a('返回明细', ['bill-t-goods/index', 'bill_id' => $bill->id], ['class' => 'btn btn-white btn-xs']);
            echo '&nbsp;';
            echo Html::create(['ajax-edit', 'bill_id' => $bill->id], '新增货品', [
                'class' => 'btn btn-primary btn-xs',
                'data-toggle' => 'modal',
                'data-target' => '#ajaxModal',
            ]);
            echo '&nbsp;';
        }
        echo Html::a('单据打印', ['bill-t/print', 'id' => $bill->id], [
            'target' => '_blank',
            'class' => 'btn btn-info btn-xs',
        ]);
        echo '&nbsp;';
        if ($bill->bill_status == \addons\Warehouse\common\enums\BillStatusEnum::SAVE) {
            echo Html::edit(['ajax-upload', 'bill_id' => $bill->id], '批量导入', [
                'class' => 'btn btn-success btn-xs',
                'data-toggle' => 'modal',
                'data-target' => '#ajaxModal',
            ]);
            echo '&nbsp;';
            echo Html::tag('span', '价格刷新', ["class" => "btn btn-warning btn-xs jsBatchUpdate", "data-grid" => "grid", "data-url" => Url::to(['update-price']),]);
            echo '&nbsp;';
            echo Html::tag('span', '批量删除', ["class" => "btn btn-danger btn-xs jsBatchUpdate", "data-grid" => "grid", "data-url" => Url::to(['batch-delete']),]);
        }
        ?>
    </div>
    <div class="tab-content">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-body table-responsive">
                    <?php echo Html::batchButtons(false) ?>
                    <span style="font-size:16px">
                        <!--<span style="font-weight:bold;">明细汇总：</span>-->
                        货品总数：<span style="color:green;"><?= $bill->goods_num ?></span>
                        折足总重(g)：<span
                                style="color:green;"><?= round($bill->billL->total_pure_gold ?? 0, 2) ?? 0 ?></span>
                        工厂总成本：<span
                                style="color:green;"><?= round($bill->billL->total_factory_cost ?? 0, 2) ?? 0 ?></span>
                        公司总成本：<span style="color:green;"><?= round($bill->total_cost ?? 0, 2) ?? 0 ?></span>
                    </span>
                    <span>
                        <?php
                        echo Html::batchButtons(false);
                        echo '&nbsp;';
                        echo Html::hidden($bill->billL->show_all ?? 0, '全部', ['data-id' => $bill->id, 'data-name' => 'show_all', 'data-text' => '全部']);
                        echo '&nbsp;';
                        echo Html::hidden($bill->billL->show_basic ?? 0, '基本', ['data-id' => $bill->id, 'data-name' => 'show_basic', 'data-text' => '基本']);
                        echo '&nbsp;';
                        echo Html::hidden($bill->billL->show_attr ?? 0, '属性', ['data-id' => $bill->id, 'data-name' => 'show_attr', 'data-text' => '属性']);
                        echo '&nbsp;';
                        echo Html::hidden($bill->billL->show_gold ?? 0, '金料', ['data-id' => $bill->id, 'data-name' => 'show_gold', 'data-text' => '金料']);
                        echo '&nbsp;';
                        if ($goods_type != \addons\Warehouse\common\enums\GoodsTypeEnum::PlainGold) {
                            echo Html::hidden($bill->billL->show_main_stone ?? 0, '主石', ['data-id' => $bill->id, 'data-name' => 'show_main_stone', 'data-text' => '主石']);
                            echo '&nbsp;';
                            echo Html::hidden($bill->billL->show_second_stone1 ?? 0, '副石1', ['data-id' => $bill->id, 'data-name' => 'show_second_stone1', 'data-text' => '副石1']);
                            echo '&nbsp;';
                            echo Html::hidden($bill->billL->show_second_stone2 ?? 0, '副石2', ['data-id' => $bill->id, 'data-name' => 'show_second_stone2', 'data-text' => '副石2']);
                            echo '&nbsp;';
                            echo Html::hidden($bill->billL->show_second_stone3 ?? 0, '副石3', ['data-id' => $bill->id, 'data-name' => 'show_second_stone3', 'data-text' => '副石3']);
                            echo '&nbsp;';
                            echo Html::hidden($bill->billL->show_parts ?? 0, '配件', ['data-id' => $bill->id, 'data-name' => 'show_parts', 'data-text' => '配件']);
                            echo '&nbsp;';
                        }
                        echo Html::hidden($bill->billL->show_fee ?? 0, '工费', ['data-id' => $bill->id, 'data-name' => 'show_fee', 'data-text' => '工费']);
                        echo '&nbsp;';
                        echo Html::hidden($bill->billL->show_price ?? 0, '价格', ['data-id' => $bill->id, 'data-name' => 'show_price', 'data-text' => '价格']);
                        ?>
                    </span>
                    <span style="color:red;">（Ctrl+F键可快速查找字段名)</span>
                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        //'tableOptions' => ['class' => 'table table-hover'],
                        'options' => ['style' => 'white-space:nowrap;font-size:12px;'],
                        'rowOptions' => function ($model, $key, $index) {
                            if ($index % 2 === 0) {
                                return ['style' => 'background:#fffef9'];
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
                                'headerOptions' => ['class' => 'col-md-1', 'id' => 'front', 'style' => 'background-color:#feeeed;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#feeeed;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = "汇总：";
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
                                'template' => '{delete} {edit} {show}',
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
                                    'show' => function ($url, $model, $key) use ($bill) {
                                        return Html::edit(['show', 'id' => $model->id, 'bill_id' => $bill->id], '查看', [
                                            'class' => 'btn btn-warning btn-xs openIframe',
                                            'data-width' => '90%',
                                            'data-height' => '90%',
                                            'data-offset' => '20px',
                                        ]);
                                    },
                                ],
                                'visible' => $model->isVisible($bill, 'front_operation'),
                            ],
                            [
                                'attribute' => 'goods_image',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#feeeed;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#feeeed;'],
                                'value' => function ($model) {
                                    return ImageHelper::fancyBox($model->goods_image, 30, 30);
                                },
                                'filter' => false,
                                'visible' => $model->isVisible($bill, 'goods_image'),
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
                                    'style' => 'width:80px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'style_cate_id'),
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
                                    'style' => 'width:80px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'product_type_id'),
                            ],
                            [
                                'label' => '货号手填',
                                'attribute' => 'auto_goods_id',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#feeeed;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#feeeed;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = "货号手填";
                                    //return \common\enums\ConfirmEnum::getValue($model->auto_goods_id);
                                    return Html::ajaxSelect($model, 'auto_goods_id', \common\enums\ConfirmEnum::getMap(), ['data-id' => $model->id]);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'auto_goods_id', \common\enums\ConfirmEnum::getMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:60px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'auto_goods_id'),
                            ],
                            [
                                'attribute' => 'goods_id',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#feeeed;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#feeeed;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('goods_id');
                                    if ($model->auto_goods_id) {
                                        return Html::ajaxInput('goods_id', $model->goods_id, ['data-id' => $model->id, 'class' => 'form-control goods_trim']);
                                    } else {
                                        return $model->goods_id ?? "";
                                    }
                                },
                                'filter' => Html::activeTextInput($searchModel, 'goods_id', [
                                    'class' => 'form-control',
                                    'style' => 'width:120px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'goods_id'),
                            ],
                            [
                                'attribute' => 'style_sn',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#feeeed;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#feeeed;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('style_sn');
                                    if (false) {//!empty($model->style_sn) && !empty($model->id)
                                        return Html::a($model->style_sn, ['/style/view', 'id' => $model->id, 'returnUrl' => Url::getReturnUrl()], ['style' => "text-decoration:underline;color:#3c8dbc", 'id' => $model->style_sn]) . ' <i class="fa fa-copy" onclick="copy(\'' . $model->style_sn . '\')"></i>';
                                    } else {
                                        if ($model->style_sn) {
                                            return "<span id='{$model->style_sn}_{$model->id}'>" . $model->style_sn . "</span>" . ' <i class="fa fa-copy" onclick="copy(\'' . $model->style_sn . '_' . $model->id . '\')"></i>';
                                        }
                                        return $model->style_sn ?? "";
                                    }
                                },
                                'filter' => Html::activeTextInput($searchModel, 'style_sn', [
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'style_sn'),
                            ],
                            [
                                'attribute' => 'goods_name',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'goods_name', 'style' => 'background-color:#feeeed;'],
                                'footerOptions' => ['class' => 'col-md-1 batch_full2', 'attr-name' => 'goods_name', 'style' => 'background-color:#feeeed;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('goods_name');
                                    return Html::ajaxInput('goods_name', $model->goods_name, ['data-id' => $model->id]);
                                },
                                'filter' => Html::activeTextInput($searchModel, 'goods_name', [
                                    'class' => 'form-control goods_name',
                                    'style' => 'width:130px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'goods_name'),
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
                                    'style' => 'width:80px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'qiban_sn'),
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
                            ],
                            [
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
                                'headerOptions' => ['class' => 'col-md-1 batch_select_full', 'attr-name' => 'material_type', 'attr-id' => AttrIdEnum::MATERIAL_TYPE, 'style' => 'background-color:#feeeed;'],
                                'footerOptions' => ['class' => 'col-md-1 batch_select_full2', 'attr-name' => 'material_type', 'attr-id' => AttrIdEnum::MATERIAL_TYPE, 'style' => 'background-color:#feeeed;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('material_type');
                                    return Html::ajaxSelect($model, 'material_type', $model->getMaterialTypeDrop($model), ['data-id' => $model->id, 'prompt' => '请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'material_type', $model->getMaterialTypeMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'material_type'),
                            ],
                            [
                                'attribute' => 'material_color',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1 batch_select_full', 'attr-name' => 'material_color', 'attr-id' => AttrIdEnum::MATERIAL_COLOR, 'style' => 'background-color:#feeeed;'],
                                'footerOptions' => ['class' => 'col-md-1 batch_select_full2', 'attr-name' => 'material_color', 'attr-id' => AttrIdEnum::MATERIAL_COLOR, 'style' => 'background-color:#feeeed;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('material_color');
                                    return Html::ajaxSelect($model, 'material_color', $model->getMaterialColorDrop($model), ['data-id' => $model->id, 'prompt' => '请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'material_color', $model->getMaterialColorMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'material_color'),
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
                                'visible' => $model->isVisible($bill, 'goods_num'),
                            ],
                            [
                                'class' => 'yii\grid\CheckboxColumn',
                                'name' => 'id',  //设置每行数据的复选框属性
                                'visible' => $model->isVisible($bill, 'id'),
                            ],
                            [
                                'attribute' => 'finger_hk',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1 batch_select_full', 'attr-name' => 'finger_hk', 'attr-id' => AttrIdEnum::FINGER_HK, 'style' => 'background-color:#F5DEB3;'],
                                'footerOptions' => ['class' => 'col-md-1 batch_select_full2', 'attr-name' => 'finger_hk', 'attr-id' => AttrIdEnum::FINGER_HK, 'style' => 'background-color:#F5DEB3;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('finger_hk');
                                    return Html::ajaxSelect($model, 'finger_hk', $model->getFingerHkDrop($model), ['data-id' => $model->id, 'prompt' => '请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'finger_hk', $model->getFingerHkMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:100px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'finger_hk'),
                            ],
                            [
                                'attribute' => 'finger',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1 batch_select_full', 'attr-name' => 'finger', 'attr-id' => AttrIdEnum::FINGER, 'style' => 'background-color:#F5DEB3;'],
                                'footerOptions' => ['class' => 'col-md-1 batch_select_full2', 'attr-name' => 'finger', 'attr-id' => AttrIdEnum::FINGER, 'style' => 'background-color:#F5DEB3;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('finger');
                                    return Html::ajaxSelect($model, 'finger', $model->getFingerDrop($model), ['data-id' => $model->id, 'prompt' => '请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'finger', $model->getFingerMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:100px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'finger'),
                            ],
                            [
                                'attribute' => 'length',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'length', 'style' => 'background-color:#F5DEB3;'],
                                'footerOptions' => ['class' => 'col-md-1 batch_full2', 'attr-name' => 'length', 'style' => 'background-color:#F5DEB3;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('length');
                                    return Html::ajaxInput('length', $model->length, ['data-id' => $model->id]);
                                },
                                'filter' => Html::activeTextInput($searchModel, 'length', [
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'length'),
                            ],
                            [
                                'attribute' => 'xiangkou',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1 batch_select_full', 'id' => 'attr', 'attr-name' => 'xiangkou', 'attr-id' => AttrIdEnum::XIANGKOU, 'style' => 'background-color:#F5DEB3;'],
                                'footerOptions' => ['class' => 'col-md-1 batch_select_full2', 'attr-name' => 'xiangkou', 'attr-id' => AttrIdEnum::XIANGKOU, 'style' => 'background-color:#F5DEB3;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('xiangkou');
                                    return Html::ajaxSelect($model, 'xiangkou', $model->getXiangkouDrop($model), ['data-id' => $model->id, 'prompt' => '请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'xiangkou', $model->getXiangkouMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:100px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'xiangkou'),
                            ],
                            [
                                'attribute' => 'product_size',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'product_size', 'style' => 'background-color:#F5DEB3;'],
                                'footerOptions' => ['class' => 'col-md-1 batch_full2', 'attr-name' => 'product_size', 'style' => 'background-color:#F5DEB3;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('product_size');
                                    return Html::ajaxInput('product_size', $model->product_size, ['data-id' => $model->id]);
                                },
                                'filter' => Html::activeTextInput($searchModel, 'product_size', [
                                    'class' => 'form-control',
                                    'style' => 'width:100px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'product_size'),
                            ],
                            [
                                'attribute' => 'kezi',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'kezi', 'style' => 'background-color:#F5DEB3;'],
                                'footerOptions' => ['class' => 'col-md-1 batch_full2', 'attr-name' => 'kezi', 'style' => 'background-color:#F5DEB3;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('kezi');
                                    return Html::ajaxInput('kezi', $model->kezi, ['data-id' => $model->id]);
                                },
                                'filter' => Html::activeTextInput($searchModel, 'kezi', [
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'kezi'),
                            ],
//                            [
//                                'attribute' => 'chain_type',
//                                'format' => 'raw',
//                                'headerOptions' => ['class' => 'col-md-1 batch_select_full', 'attr-name' => 'chain_type', 'attr-id' => AttrIdEnum::CHAIN_TYPE, 'style' => 'background-color:#F5DEB3;'],
//                                'footerOptions' => ['class' => 'col-md-1 batch_select_full2', 'attr-name' => 'chain_type', 'attr-id' => AttrIdEnum::CHAIN_TYPE, 'style' => 'background-color:#F5DEB3;'],
//                                'value' => function ($model, $key, $index, $widget) {
//                                    $widget->footer = $model->getAttributeLabel('chain_type');
//                                    return Html::ajaxSelect($model, 'chain_type', $model->getChainTypeDrop($model), ['data-id' => $model->id, 'prompt' => '请选择']);
//                                },
//                                'filter' => Html::activeDropDownList($searchModel, 'chain_type', $model->getChainTypeMap(), [
//                                    'prompt' => '全部',
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
//                                'visible' => $model->isVisible($bill, 'chain_type'),
//                            ],
//                            [
//                                'attribute' => 'chain_long',
//                                'format' => 'raw',
//                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'chain_long', 'style' => 'background-color:#afdfe4;'],
//                                'value' => function ($model, $key, $index, $column) {
//                                    return Html::ajaxInput('chain_long', $model->chain_long, ['data-id' => $model->id]);
//                                },
//                                'filter' => Html::activeTextInput($searchModel, 'chain_long', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:100px;'
//                                ]),
//                            ],
//                            [
//                                'attribute' => 'cramp_ring',
//                                'format' => 'raw',
//                                'headerOptions' => ['class' => 'col-md-1 batch_select_full', 'attr-name' => 'cramp_ring', 'attr-id' => AttrIdEnum::CHAIN_BUCKLE, 'style' => 'background-color:#F5DEB3;'],
//                                'footerOptions' => ['class' => 'col-md-1 batch_select_full2', 'attr-name' => 'cramp_ring', 'attr-id' => AttrIdEnum::CHAIN_BUCKLE, 'style' => 'background-color:#F5DEB3;'],
//                                'value' => function ($model, $key, $index, $widget) {
//                                    $widget->footer = $model->getAttributeLabel('cramp_ring');
//                                    return Html::ajaxSelect($model, 'cramp_ring', $model->getCrampRingDrop($model), ['data-id' => $model->id, 'prompt' => '请选择']);
//                                },
//                                'filter' => Html::activeDropDownList($searchModel, 'cramp_ring', $model->getCrampRingMap(), [
//                                    'prompt' => '全部',
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
//                                'visible' => $model->isVisible($bill, 'cramp_ring'),
//                            ],
                            [
                                'attribute' => 'talon_head_type',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1 batch_select_full', 'attr-name' => 'talon_head_type', 'attr-id' => AttrIdEnum::TALON_HEAD_TYPE, 'style' => 'background-color:#F5DEB3;'],
                                'footerOptions' => ['class' => 'col-md-1 batch_select_full2', 'attr-name' => 'talon_head_type', 'attr-id' => AttrIdEnum::TALON_HEAD_TYPE, 'style' => 'background-color:#F5DEB3;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('talon_head_type');
                                    return Html::ajaxSelect($model, 'talon_head_type', $model->getTalonHeadTypeDrop($model), ['data-id' => $model->id, 'prompt' => '请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'talon_head_type', $model->getTalonHeadTypeMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'talon_head_type'),
                            ],
                            [
                                'class' => 'yii\grid\CheckboxColumn',
                                'name' => 'id',  //设置每行数据的复选框属性
                                'visible' => $model->isVisible($bill, 'id'),
                            ],
                            [
                                'attribute' => 'goods_name',
                                //'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#afdfe4;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#afdfe4;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('goods_name');
                                    return $model->goods_name ?? "";
                                },
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'goods_name', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:200px;'
//                                ]),
                                'visible' => $model->isVisible($bill, 'goods_name1'),
                            ],
                            [
                                'attribute' => 'peiliao_way',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1 batch_select_full', 'attr-name' => 'peiliao_way', 'style' => 'background-color:#afdfe4;'],
                                'footerOptions' => ['class' => 'col-md-1 batch_select_full2', 'attr-name' => 'peiliao_way', 'style' => 'background-color:#afdfe4;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('peiliao_way');
                                    return Html::ajaxSelect($model, 'peiliao_way', \addons\Warehouse\common\enums\PeiLiaoWayEnum::getMap(), ['data-id' => $model->id]);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'peiliao_way', \addons\Warehouse\common\enums\PeiLiaoWayEnum::getMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'peiliao_way'),
                            ],
                            [
                                'attribute' => 'suttle_weight',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'id' => 'suttle_weight', 'attr-name' => 'suttle_weight', 'style' => 'background-color:#afdfe4;'],
                                'footerOptions' => ['class' => 'col-md-1 batch_full2', 'attr-name' => 'suttle_weight', 'style' => 'background-color:#afdfe4;'],
                                'value' => function ($model, $key, $index, $widget) use ($total) {
                                    $widget->footer = $model->getFooterValues('suttle_weight', $total, "0.000");
                                    return Html::ajaxInput('suttle_weight', $model->suttle_weight, ['data-id' => $model->id, 'onfocus' => 'rfClearVal(this)', 'data-type' => 'number']);
                                },
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'suttle_weight', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                                'visible' => $model->isVisible($bill, 'suttle_weight'),
                            ],
                            [
                                'attribute' => 'gold_weight',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'gold_weight', 'style' => 'background-color:#afdfe4;'],
                                'footerOptions' => ['class' => 'col-md-1 batch_full2', 'attr-name' => 'gold_weight', 'style' => 'background-color:#afdfe4;'],
                                'value' => function ($model, $key, $index, $widget) use ($total) {
                                    $widget->footer = $model->getFooterValues('gold_weight', $total, "0.000");
                                    return Html::ajaxInput('gold_weight', $model->gold_weight, ['data-id' => $model->id, 'onfocus' => 'rfClearVal(this)', 'data-type' => 'number']);
                                },
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'gold_weight', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                                'visible' => $model->isVisible($bill, 'gold_weight'),
                            ],
                            [
                                'attribute' => 'gold_loss',
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'gold_loss', 'style' => 'background-color:#afdfe4;'],
                                'footerOptions' => ['class' => 'col-md-1 batch_full2', 'attr-name' => 'gold_loss', 'style' => 'background-color:#afdfe4;'],
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('gold_loss');
                                    return Html::ajaxInput('gold_loss', $model->gold_loss, ['data-id' => $model->id, 'onfocus' => 'rfClearVal(this)', 'data-type' => 'number']);
                                },
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'gold_loss', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                                'visible' => $model->isVisible($bill, 'gold_loss'),
                            ],
                            [
                                'attribute' => 'lncl_loss_weight',
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'id' => 'gold', 'attr-name' => 'lncl_loss_weight', 'style' => 'background-color:#afdfe4;'],
                                'footerOptions' => ['class' => 'col-md-1 batch_full2', 'attr-name' => 'lncl_loss_weight', 'style' => 'background-color:#afdfe4;'],
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $widget) use ($total) {
                                    $widget->footer = $model->getFooterValues('lncl_loss_weight', $total, "0.000");
                                    return Html::ajaxInput('lncl_loss_weight', $model->lncl_loss_weight, ['data-id' => $model->id, 'onfocus' => 'rfClearVal(this)', 'data-type' => 'number']);
                                },
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'lncl_loss_weight', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                                'visible' => $model->isVisible($bill, 'lncl_loss_weight'),
                            ],
                            [
                                'attribute' => 'gold_price',
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'gold_price', 'style' => 'background-color:#afdfe4;'],
                                'footerOptions' => ['class' => 'col-md-1 batch_full2', 'attr-name' => 'gold_price', 'style' => 'background-color:#afdfe4;'],
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('gold_price');
                                    return Html::ajaxInput('gold_price', $model->gold_price, ['data-id' => $model->id, 'onfocus' => 'rfClearVal(this)', 'data-type' => 'number']);
                                },
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'gold_price', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                                'visible' => $model->isVisible($bill, 'gold_price'),
                            ],
                            [
                                'attribute' => 'gold_amount',
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'gold_amount', 'style' => 'background-color:#afdfe4;'],
                                'footerOptions' => ['class' => 'col-md-1 batch_full2', 'attr-name' => 'gold_amount', 'style' => 'background-color:#afdfe4;'],
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $widget) use ($total) {
                                    $widget->footer = $model->getFooterValues('gold_amount', $total, "0.000");
                                    return Html::ajaxInput('gold_amount', $model->gold_amount, ['data-id' => $model->id, 'onfocus' => 'rfClearVal(this)', 'data-type' => 'number']);
                                },
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'gold_amount', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                                'visible' => $model->isVisible($bill, 'gold_amount'),
                            ],
                            [
                                'attribute' => 'pure_gold_rate',
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'gold_loss', 'style' => 'background-color:#afdfe4;'],
                                'footerOptions' => ['class' => 'col-md-1 batch_full2', 'attr-name' => 'gold_loss', 'style' => 'background-color:#afdfe4;'],
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('pure_gold_rate');
                                    return Html::ajaxInput('pure_gold_rate', $model->pure_gold_rate, ['data-id' => $model->id, 'onfocus' => 'rfClearVal(this)', 'data-type' => 'number']);
                                },
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'pure_gold_rate', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                                'visible' => $model->isVisible($bill, 'pure_gold_rate'),
                            ],
                            [
                                'attribute' => 'pure_gold',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'pure_gold', 'style' => 'background-color:#afdfe4;'],
                                'footerOptions' => ['class' => 'col-md-1 batch_full2', 'attr-name' => 'pure_gold', 'style' => 'background-color:#afdfe4;'],
                                'value' => function ($model, $key, $index, $widget) use ($total) {
                                    $widget->footer = $model->getFooterValues('pure_gold', $total, "0.000");
                                    return Html::ajaxInput('pure_gold', $model->pure_gold, ['data-id' => $model->id, 'onfocus' => 'rfClearVal(this)', 'data-type' => 'number']);
                                },
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'pure_gold', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                                'visible' => $model->isVisible($bill, 'pure_gold'),
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
                            /*[
                                'attribute' => 'cert_id',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#afdfe4;'],
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    return Html::ajaxInput('cert_id', $model->cert_id, ['data-id' => $model->id]);
                                },
                                'filter' => Html::activeTextInput($searchModel, 'cert_id', [
                                    'class' => 'form-control',
                                    'style' => 'width:100px;'
                                ]),
                            ],
                            [
                                'attribute' => 'cert_type',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    return Html::ajaxSelect($model, 'cert_type', $model->getCertTypeDrop($model), ['data-id' => $model->id, 'prompt' => '请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'cert_type', $model->getCertTypeMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:100px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1 batch_select_full', 'attr-name' => 'finger', 'attr-id' => AttrIdEnum::DIA_CERT_TYPE, 'style' => 'background-color:#afdfe4;'],
                            ],
                            [
                                'attribute' => 'diamond_cert_id',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    return Html::ajaxInput('diamond_cert_id', $model->diamond_cert_id, ['data-id' => $model->id]);
                                },
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#d5c59f;'],
                                'filter' => Html::activeTextInput($searchModel, 'diamond_cert_id', [
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                            ],
                            [
                                'attribute' => 'diamond_cert_type',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    return Html::ajaxSelect($model, 'diamond_cert_type', $model->getDiamondCertTypeDrop($model), ['data-id' => $model->id, 'prompt' => '请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'diamond_cert_type', $model->getDiamondCertTypeMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:100px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1 batch_select_full', 'attr-name' => 'diamond_cert_type', 'attr-id' => AttrIdEnum::DIA_CERT_TYPE, 'style' => 'background-color:#d5c59f;'],
                            ],
                            [
                                'attribute' => 'diamond_carat',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    return Html::ajaxInput('diamond_carat', $model->diamond_carat, ['data-id' => $model->id, 'onfocus' => 'rfClearVal(this)', 'data-type' => 'number']);
                                },
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'diamond_carat', 'style' => 'background-color:#d5c59f;'],
                                'filter' => Html::activeTextInput($searchModel, 'diamond_carat', [
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
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#d5c59f;'],
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'goods_name', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:200px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'diamond_shape',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    return Html::ajaxSelect($model, 'diamond_shape', $model->getDiamondShapeDrop($model), ['data-id' => $model->id, 'prompt' => '请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'diamond_shape', $model->getDiamondShapeMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1 batch_select_full', 'attr-name' => 'diamond_shape', 'attr-id' => AttrIdEnum::DIA_SHAPE, 'style' => 'background-color:#d5c59f;'],
                            ],
                            [
                                'attribute' => 'diamond_color',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    return Html::ajaxSelect($model, 'diamond_color', $model->getDiamondColorDrop($model), ['data-id' => $model->id, 'prompt' => '请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'diamond_color', $model->getDiamondColorMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1 batch_select_full', 'attr-name' => 'diamond_color', 'attr-id' => AttrIdEnum::DIA_COLOR, 'style' => 'background-color:#d5c59f;'],
                            ],
                            [
                                'attribute' => 'diamond_clarity',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    return Html::ajaxSelect($model, 'diamond_clarity', $model->getDiamondClarityDrop($model), ['data-id' => $model->id, 'prompt' => '请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'diamond_clarity', $model->getDiamondClarityMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1 batch_select_full', 'attr-name' => 'diamond_clarity', 'attr-id' => AttrIdEnum::DIA_CLARITY, 'style' => 'background-color:#d5c59f;'],
                            ],
                            [
                                'attribute' => 'diamond_cut',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    return Html::ajaxSelect($model, 'diamond_cut', $model->getDiamondCutDrop($model), ['data-id' => $model->id, 'prompt' => '请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'diamond_cut', $model->getDiamondCutMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1 batch_select_full', 'attr-name' => 'diamond_cut', 'attr-id' => AttrIdEnum::DIA_CUT, 'style' => 'background-color:#d5c59f;'],
                            ],
                            [
                                'attribute' => 'diamond_polish',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    return Html::ajaxSelect($model, 'diamond_polish', $model->getDiamondPolishDrop($model), ['data-id' => $model->id, 'prompt' => '请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'diamond_polish', $model->getDiamondPolishMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1 batch_select_full', 'attr-name' => 'diamond_polish', 'attr-id' => AttrIdEnum::DIA_POLISH, 'style' => 'background-color:#d5c59f;'],
                            ],
                            [
                                'attribute' => 'diamond_symmetry',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    return Html::ajaxSelect($model, 'diamond_symmetry', $model->getDiamondSymmetryDrop($model), ['data-id' => $model->id, 'prompt' => '请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'diamond_symmetry', $model->getDiamondSymmetryMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1 batch_select_full', 'attr-name' => 'diamond_symmetry', 'attr-id' => AttrIdEnum::DIA_SYMMETRY, 'style' => 'background-color:#d5c59f;'],
                            ],
                            [
                                'attribute' => 'diamond_fluorescence',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    return Html::ajaxSelect($model, 'diamond_fluorescence', $model->getDiamondFluorescenceDrop($model), ['data-id' => $model->id, 'prompt' => '请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'diamond_fluorescence', $model->getDiamondFluorescenceMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1 batch_select_full', 'attr-name' => 'diamond_fluorescence', 'attr-id' => AttrIdEnum::DIA_FLUORESCENCE, 'style' => 'background-color:#d5c59f;'],
                            ],
                            [
                                'attribute' => 'diamond_discount',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    return Html::ajaxInput('diamond_discount', $model->diamond_discount, ['data-id' => $model->id]);
                                },
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'diamond_discount', 'style' => 'background-color:#d5c59f;'],
//                                'filter' => Html::activeTextInput($searchModel, 'diamond_discount', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                            ],*/
                            [
                                'class' => 'yii\grid\CheckboxColumn',
                                'name' => 'id',  //设置每行数据的复选框属性
                                'visible' => $model->isVisible($bill, 'id'),
                            ],
                            [
                                'attribute' => 'goods_name',
                                //'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#afb4db;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#afb4db;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('goods_name');
                                    return $model->goods_name ?? "";
                                },
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'goods_name', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:200px;'
//                                ]),
                                'visible' => $model->isVisible($bill, 'goods_name2'),
                            ],
                            [
                                'attribute' => 'main_pei_type',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1 batch_select_full', 'attr-name' => 'main_pei_type', 'style' => 'background-color:#afb4db;'],
                                'footerOptions' => ['class' => 'col-md-1 batch_select_full2', 'attr-name' => 'main_pei_type', 'style' => 'background-color:#afb4db;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('main_pei_type');
                                    return Html::ajaxSelect($model, 'main_pei_type', \addons\Warehouse\common\enums\PeiShiWayEnum::getMap(), ['data-id' => $model->id]);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'main_pei_type', \addons\Warehouse\common\enums\PeiShiWayEnum::getMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'main_pei_type'),
                            ],
                            [
                                'attribute' => 'main_stone_sn',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'main_stone_sn', 'style' => 'background-color:#afb4db;'],
                                'footerOptions' => ['class' => 'col-md-1 batch_full2', 'attr-name' => 'main_stone_sn', 'style' => 'background-color:#afb4db;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('main_stone_sn');
                                    return Html::ajaxInput('main_stone_sn', $model->main_stone_sn, ['data-id' => $model->id]);
                                },
                                'filter' => Html::activeTextInput($searchModel, 'main_stone_sn', [
                                    'class' => 'form-control',
                                    'style' => 'width:100px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'main_stone_sn'),
                            ],
                            [
                                'attribute' => 'main_stone_type',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1 batch_select_full', 'attr-name' => 'main_stone_type', 'attr-id' => AttrIdEnum::MAIN_STONE_TYPE, 'style' => 'background-color:#afb4db;'],
                                'footerOptions' => ['class' => 'col-md-1 batch_select_full2', 'attr-name' => 'main_stone_type', 'attr-id' => AttrIdEnum::MAIN_STONE_TYPE, 'style' => 'background-color:#afb4db;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('main_stone_type');
                                    return Html::ajaxSelect($model, 'main_stone_type', $model->getMainStoneTypeDrop($model), ['data-id' => $model->id, 'prompt' => '请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'main_stone_type', $model->getMainStoneTypeMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'main_stone_type'),
                            ],
                            [
                                'attribute' => 'main_stone_num',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'main_stone_num', 'style' => 'background-color:#afb4db;'],
                                'footerOptions' => ['class' => 'col-md-1 batch_full2', 'attr-name' => 'main_stone_num', 'style' => 'background-color:#afb4db;'],
                                'value' => function ($model, $key, $index, $widget) use ($total) {
                                    $widget->footer = $model->getFooterValues('main_stone_num', $total);
                                    return Html::ajaxInput('main_stone_num', $model->main_stone_num, ['data-id' => $model->id, 'onfocus' => 'rfClearVal(this)', 'data-type' => 'number']);
                                },
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'main_stone_num', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                                'visible' => $model->isVisible($bill, 'main_stone_num'),
                            ],
                            [
                                'attribute' => 'main_stone_weight',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'main_stone_weight', 'style' => 'background-color:#afb4db;'],
                                'footerOptions' => ['class' => 'col-md-1 batch_full2', 'attr-name' => 'main_stone_weight', 'style' => 'background-color:#afb4db;'],
                                'value' => function ($model, $key, $index, $widget) use ($total) {
                                    $widget->footer = $model->getFooterValues('main_stone_weight', $total, "0.000", 3);
                                    return Html::ajaxInput('main_stone_weight', $model->main_stone_weight, ['data-id' => $model->id, 'onfocus' => 'rfClearVal(this)', 'data-type' => 'number']);
                                },
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'main_stone_weight', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                                'visible' => $model->isVisible($bill, 'main_stone_weight'),
                            ],
                            [
                                'attribute' => 'main_stone_price',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'id' => 'main_stone', 'attr-name' => 'main_stone_price', 'style' => 'background-color:#afb4db;'],
                                'footerOptions' => ['class' => 'col-md-1 batch_full2', 'attr-name' => 'main_stone_price', 'style' => 'background-color:#afb4db;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('main_stone_price');
                                    return Html::ajaxInput('main_stone_price', $model->main_stone_price, ['data-id' => $model->id, 'onfocus' => 'rfClearVal(this)', 'data-type' => 'number']);
                                },
                                'filter' => Html::activeTextInput($searchModel, 'main_stone_price', [
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'main_stone_price'),
                            ],
                            [
                                'attribute' => 'main_stone_amount',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'main_stone_amount', 'style' => 'background-color:#afb4db;'],
                                'footerOptions' => ['class' => 'col-md-1 batch_full2', 'attr-name' => 'main_stone_amount', 'style' => 'background-color:#afb4db;'],
                                'value' => function ($model, $key, $index, $widget) use ($total) {
                                    $widget->footer = $model->getFooterValues('main_stone_amount', $total, "0.000");
                                    return Html::ajaxInput('main_stone_amount', $model->main_stone_amount, ['data-id' => $model->id, 'onfocus' => 'rfClearVal(this)', 'data-type' => 'number']);
                                },
                                'filter' => Html::activeTextInput($searchModel, 'main_stone_amount', [
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'main_stone_amount'),
                            ],
                            [
                                'attribute' => 'main_stone_shape',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1 batch_select_full', 'attr-name' => 'main_stone_shape', 'attr-id' => AttrIdEnum::MAIN_STONE_SHAPE, 'style' => 'background-color:#afb4db;'],
                                'footerOptions' => ['class' => 'col-md-1 batch_select_full2', 'attr-name' => 'main_stone_shape', 'attr-id' => AttrIdEnum::MAIN_STONE_SHAPE, 'style' => 'background-color:#afb4db;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('main_stone_shape');
                                    return Html::ajaxSelect($model, 'main_stone_shape', $model->getMainStoneShapeDrop($model), ['data-id' => $model->id, 'prompt' => '请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'main_stone_shape', $model->getMainStoneShapeMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'main_stone_shape'),
                            ],
                            [
                                'attribute' => 'main_stone_color',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1 batch_select_full', 'attr-name' => 'main_stone_color', 'attr-id' => AttrIdEnum::MAIN_STONE_COLOR, 'style' => 'background-color:#afb4db;'],
                                'footerOptions' => ['class' => 'col-md-1 batch_select_full2', 'attr-name' => 'main_stone_color', 'attr-id' => AttrIdEnum::MAIN_STONE_COLOR, 'style' => 'background-color:#afb4db;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('main_stone_color');
                                    return Html::ajaxSelect($model, 'main_stone_color', $model->getMainStoneColorDrop($model), ['data-id' => $model->id, 'prompt' => '请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'main_stone_color', $model->getMainStoneColorMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'main_stone_color'),
                            ],
                            [
                                'attribute' => 'main_stone_clarity',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1 batch_select_full', 'attr-name' => 'main_stone_clarity', 'attr-id' => AttrIdEnum::MAIN_STONE_CLARITY, 'style' => 'background-color:#afb4db;'],
                                'footerOptions' => ['class' => 'col-md-1 batch_select_full2', 'attr-name' => 'main_stone_clarity', 'attr-id' => AttrIdEnum::MAIN_STONE_CLARITY, 'style' => 'background-color:#afb4db;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('main_stone_clarity');
                                    return Html::ajaxSelect($model, 'main_stone_clarity', $model->getMainStoneClarityDrop($model), ['data-id' => $model->id, 'prompt' => '请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'main_stone_clarity', $model->getMainStoneClarityMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'main_stone_clarity'),
                            ],
                            [
                                'attribute' => 'main_stone_cut',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1 batch_select_full', 'attr-name' => 'main_stone_cut', 'attr-id' => AttrIdEnum::MAIN_STONE_CUT, 'style' => 'background-color:#afb4db;'],
                                'footerOptions' => ['class' => 'col-md-1 batch_select_full2', 'attr-name' => 'main_stone_cut', 'attr-id' => AttrIdEnum::MAIN_STONE_CUT, 'style' => 'background-color:#afb4db;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('main_stone_cut');
                                    return Html::ajaxSelect($model, 'main_stone_cut', $model->getMainStoneCutDrop($model), ['data-id' => $model->id, 'prompt' => '请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'main_stone_cut', $model->getMainStoneCutMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'main_stone_cut'),
                            ],
                            [
                                'attribute' => 'main_stone_polish',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1 batch_select_full', 'attr-name' => 'main_stone_polish', 'attr-id' => AttrIdEnum::MAIN_STONE_POLISH, 'style' => 'background-color:#afb4db;'],
                                'footerOptions' => ['class' => 'col-md-1 batch_select_full2', 'attr-name' => 'main_stone_polish', 'attr-id' => AttrIdEnum::MAIN_STONE_POLISH, 'style' => 'background-color:#afb4db;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('main_stone_polish');
                                    return Html::ajaxSelect($model, 'main_stone_polish', $model->getMainStonePolishDrop($model), ['data-id' => $model->id, 'prompt' => '请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'main_stone_polish', $model->getMainStonePolishMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'main_stone_polish'),
                            ],
                            [
                                'attribute' => 'main_stone_symmetry',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1 batch_select_full', 'attr-name' => 'main_stone_symmetry', 'attr-id' => AttrIdEnum::MAIN_STONE_SYMMETRY, 'style' => 'background-color:#afb4db;'],
                                'footerOptions' => ['class' => 'col-md-1 batch_select_full2', 'attr-name' => 'main_stone_symmetry', 'attr-id' => AttrIdEnum::MAIN_STONE_SYMMETRY, 'style' => 'background-color:#afb4db;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('main_stone_symmetry');
                                    return Html::ajaxSelect($model, 'main_stone_symmetry', $model->getMainStoneSymmetryDrop($model), ['data-id' => $model->id, 'prompt' => '请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'main_stone_symmetry', $model->getMainStoneSymmetryMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'main_stone_symmetry'),
                            ],
                            [
                                'attribute' => 'main_stone_fluorescence',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1 batch_select_full', 'attr-name' => 'main_stone_fluorescence', 'attr-id' => AttrIdEnum::MAIN_STONE_FLUORESCENCE, 'style' => 'background-color:#afb4db;'],
                                'footerOptions' => ['class' => 'col-md-1 batch_select_full2', 'attr-name' => 'main_stone_fluorescence', 'attr-id' => AttrIdEnum::MAIN_STONE_FLUORESCENCE, 'style' => 'background-color:#afb4db;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('main_stone_fluorescence');
                                    return Html::ajaxSelect($model, 'main_stone_fluorescence', $model->getMainStoneFluorescenceDrop($model), ['data-id' => $model->id, 'prompt' => '请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'main_stone_fluorescence', $model->getMainStoneFluorescenceMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'main_stone_fluorescence'),
                            ],
                            [
                                'attribute' => 'main_stone_colour',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1 batch_select_full', 'attr-name' => 'main_stone_colour', 'attr-id' => AttrIdEnum::MAIN_STONE_COLOUR, 'style' => 'background-color:#afb4db;'],
                                'footerOptions' => ['class' => 'col-md-1 batch_select_full2', 'attr-name' => 'main_stone_colour', 'attr-id' => AttrIdEnum::MAIN_STONE_COLOUR, 'style' => 'background-color:#afb4db;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('main_stone_colour');
                                    return Html::ajaxSelect($model, 'main_stone_colour', $model->getMainStoneColourDrop($model), ['data-id' => $model->id, 'prompt' => '请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'main_stone_colour', $model->getMainStoneColourMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'main_stone_colour'),
                            ],
//                            [
//                                'attribute' => 'main_stone_size',
//                                'format' => 'raw',
//                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#afb4db;'],
//                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#afb4db;'],
//                                'value' => function ($model, $key, $index, $widget) {
//                                    $widget->footer = $model->getAttributeLabel('main_stone_size');
//                                    return Html::ajaxInput('main_stone_size', $model->main_stone_size, ['data-id' => $model->id]);
//                                },
//                                'filter' => Html::activeTextInput($searchModel, 'main_stone_size', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:100px;'
//                                ]),
//                            ],
                            [
                                'class' => 'yii\grid\CheckboxColumn',
                                'name' => 'id',  //设置每行数据的复选框属性
                                'visible' => $model->isVisible($bill, 'id'),
                            ],
                            [
                                'attribute' => 'goods_name',
                                //'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#dec674;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#dec674;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('goods_name');
                                    return $model->goods_name ?? "";
                                },
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'goods_name', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:200px;'
//                                ]),
                                'visible' => $model->isVisible($bill, 'goods_name3'),
                            ],
                            [
                                'attribute' => 'second_pei_type',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1 batch_select_full', 'attr-name' => 'second_pei_type', 'style' => 'background-color:#dec674;'],
                                'footerOptions' => ['class' => 'col-md-1 batch_select_full2', 'attr-name' => 'second_pei_type', 'style' => 'background-color:#dec674;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('second_pei_type');
                                    return Html::ajaxSelect($model, 'second_pei_type', \addons\Warehouse\common\enums\PeiShiWayEnum::getMap(), ['data-id' => $model->id]);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'second_pei_type', \addons\Warehouse\common\enums\PeiShiWayEnum::getMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'second_pei_type'),
                            ],
                            [
                                'attribute' => 'second_stone_type1',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1 batch_select_full', 'attr-name' => 'second_stone_type1', 'attr-id' => AttrIdEnum::SIDE_STONE1_TYPE, 'style' => 'background-color:#dec674;'],
                                'footerOptions' => ['class' => 'col-md-1 batch_select_full2', 'attr-name' => 'second_stone_type1', 'attr-id' => AttrIdEnum::SIDE_STONE1_TYPE, 'style' => 'background-color:#dec674;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('second_stone_type1');
                                    return Html::ajaxSelect($model, 'second_stone_type1', $model->getSecondStoneType1Drop($model), ['data-id' => $model->id, 'prompt' => '请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'second_stone_type1', $model->getSecondStoneType1Map(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'second_stone_type1'),
                            ],
                            [
                                'attribute' => 'second_stone_sn1',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'second_stone_sn1', 'style' => 'background-color:#dec674;'],
                                'footerOptions' => ['class' => 'col-md-1 batch_full2', 'attr-name' => 'second_stone_sn1', 'style' => 'background-color:#dec674;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('second_stone_sn1');
                                    return Html::ajaxInput('second_stone_sn1', $model->second_stone_sn1, ['data-id' => $model->id]);
                                },
                                'filter' => Html::activeTextInput($searchModel, 'second_stone_sn1', [
                                    'class' => 'form-control',
                                    'style' => 'width:100px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'second_stone_sn1'),
                            ],
                            [
                                'attribute' => 'second_stone_num1',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'second_stone_num1', 'style' => 'background-color:#dec674;'],
                                'footerOptions' => ['class' => 'col-md-1 batch_full2', 'attr-name' => 'second_stone_num1', 'style' => 'background-color:#dec674;'],
                                'value' => function ($model, $key, $index, $widget) use ($total) {
                                    $widget->footer = $model->getFooterValues('second_stone_num1', $total);
                                    return Html::ajaxInput('second_stone_num1', $model->second_stone_num1, ['data-id' => $model->id, 'onfocus' => 'rfClearVal(this)', 'data-type' => 'number']);
                                },
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'second_stone_num1', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                                'visible' => $model->isVisible($bill, 'second_stone_num1'),
                            ],
                            [
                                'attribute' => 'second_stone_weight1',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'second_stone_weight1', 'style' => 'background-color:#dec674;'],
                                'footerOptions' => ['class' => 'col-md-1 batch_full2', 'attr-name' => 'second_stone_weight1', 'style' => 'background-color:#dec674;'],
                                'value' => function ($model, $key, $index, $widget) use ($total) {
                                    $widget->footer = $model->getFooterValues('second_stone_weight1', $total, "0.000", 3);
                                    return Html::ajaxInput('second_stone_weight1', $model->second_stone_weight1, ['data-id' => $model->id, 'onfocus' => 'rfClearVal(this)', 'data-type' => 'number']);
                                },
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'second_stone_weight1', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                                'visible' => $model->isVisible($bill, 'second_stone_weight1'),
                            ],
                            [
                                'attribute' => 'second_stone_price1',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'id' => 'second_stone1', 'attr-name' => 'second_stone_price1', 'style' => 'background-color:#dec674;'],
                                'footerOptions' => ['class' => 'col-md-1 batch_full2', 'attr-name' => 'second_stone_price1', 'style' => 'background-color:#dec674;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('second_stone_price1');
                                    return Html::ajaxInput('second_stone_price1', $model->second_stone_price1, ['data-id' => $model->id, 'onfocus' => 'rfClearVal(this)', 'data-type' => 'number']);
                                },
                                'filter' => Html::activeTextInput($searchModel, 'second_stone_price1', [
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'second_stone_price1'),
                            ],
                            [
                                'attribute' => 'second_stone_amount1',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'second_stone_amount1', 'style' => 'background-color:#dec674;'],
                                'footerOptions' => ['class' => 'col-md-1 batch_full2', 'attr-name' => 'second_stone_amount1', 'style' => 'background-color:#dec674;'],
                                'value' => function ($model, $key, $index, $widget) use ($total) {
                                    $widget->footer = $model->getFooterValues('second_stone_amount1', $total, "0.000");
                                    return Html::ajaxInput('second_stone_amount1', $model->second_stone_amount1, ['data-id' => $model->id, 'onfocus' => 'rfClearVal(this)', 'data-type' => 'number']);
                                },
                                'filter' => Html::activeTextInput($searchModel, 'second_stone_amount1', [
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'second_stone_amount1'),
                            ],
                            [
                                'attribute' => 'second_stone_shape1',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1 batch_select_full', 'attr-name' => 'second_stone_shape1', 'attr-id' => AttrIdEnum::SIDE_STONE1_SHAPE, 'style' => 'background-color:#dec674;'],
                                'footerOptions' => ['class' => 'col-md-1 batch_select_full2', 'attr-name' => 'second_stone_shape1', 'attr-id' => AttrIdEnum::SIDE_STONE1_SHAPE, 'style' => 'background-color:#dec674;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('second_stone_price1');
                                    return Html::ajaxSelect($model, 'second_stone_shape1', $model->getSecondStoneShape1Drop($model), ['data-id' => $model->id, 'prompt' => '请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'second_stone_shape1', $model->getSecondStoneShape1Map(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'second_stone_shape1'),
                            ],
                            [
                                'attribute' => 'second_stone_color1',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1 batch_select_full', 'attr-name' => 'second_stone_color1', 'attr-id' => AttrIdEnum::SIDE_STONE1_COLOR, 'style' => 'background-color:#dec674;'],
                                'footerOptions' => ['class' => 'col-md-1 batch_select_full2', 'attr-name' => 'second_stone_color1', 'attr-id' => AttrIdEnum::SIDE_STONE1_COLOR, 'style' => 'background-color:#dec674;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('second_stone_color1');
                                    return Html::ajaxSelect($model, 'second_stone_color1', $model->getSecondStoneColor1Drop($model), ['data-id' => $model->id, 'prompt' => '请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'second_stone_color1', $model->getSecondStoneColor1Map(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'second_stone_color1'),
                            ],
                            [
                                'attribute' => 'second_stone_clarity1',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1 batch_select_full', 'attr-name' => 'second_stone_clarity1', 'attr-id' => AttrIdEnum::SIDE_STONE1_CLARITY, 'style' => 'background-color:#dec674;'],
                                'footerOptions' => ['class' => 'col-md-1 batch_select_full2', 'attr-name' => 'second_stone_clarity1', 'attr-id' => AttrIdEnum::SIDE_STONE1_CLARITY, 'style' => 'background-color:#dec674;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('second_stone_clarity1');
                                    return Html::ajaxSelect($model, 'second_stone_clarity1', $model->getSecondStoneClarity1Drop($model), ['data-id' => $model->id, 'prompt' => '请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'second_stone_clarity1', $model->getSecondStoneClarity1Map(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'second_stone_clarity1'),
                            ],
                            [
                                'attribute' => 'second_stone_cut1',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1 batch_select_full', 'attr-name' => 'second_stone_cut1', 'attr-id' => AttrIdEnum::SIDE_STONE1_CUT, 'style' => 'background-color:#dec674;'],
                                'footerOptions' => ['class' => 'col-md-1 batch_select_full2', 'attr-name' => 'second_stone_cut1', 'attr-id' => AttrIdEnum::SIDE_STONE1_CUT, 'style' => 'background-color:#dec674;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('second_stone_cut1');
                                    return Html::ajaxSelect($model, 'second_stone_cut1', $model->getSecondStoneCut1Drop($model), ['data-id' => $model->id, 'prompt' => '请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'second_stone_cut1', $model->getSecondStoneCut1Map(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'second_stone_cut1'),
                            ],
                            [
                                'attribute' => 'second_stone_colour1',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1 batch_select_full', 'attr-name' => 'second_stone_colour1', 'attr-id' => AttrIdEnum::SIDE_STONE1_COLOUR, 'style' => 'background-color:#dec674;'],
                                'footerOptions' => ['class' => 'col-md-1 batch_select_full2', 'attr-name' => 'second_stone_colour1', 'attr-id' => AttrIdEnum::SIDE_STONE1_COLOUR, 'style' => 'background-color:#dec674;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('second_stone_colour1');
                                    return Html::ajaxSelect($model, 'second_stone_colour1', $model->getSecondStoneColour1Drop($model), ['data-id' => $model->id, 'prompt' => '请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'second_stone_colour1', $model->getSecondStoneColour1Map(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'second_stone_colour1'),
                            ],
                            /*[
                                'attribute' => 'second_stone_size1',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    return Html::ajaxInput('second_stone_size1', $model->second_stone_size1, ['data-id' => $model->id]);
                                },
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#dec674;'],
                                'filter' => Html::activeTextInput($searchModel, 'second_stone_size1', [
                                    'class' => 'form-control',
                                    'style' => 'width:100px;'
                                ]),
                            ],*/
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
//                            [
//                                'attribute' => 'second_stone_type1',
//                                'format' => 'raw',
//                                'value' => function ($model, $key, $index, $column) {
//                                    return Html::ajaxSelect($model, 'second_stone_type1', $model->getSecondStoneType1Drop($model), ['data-id' => $model->id, 'prompt' => '请选择']);
//                                },
//                                'filter' => Html::activeDropDownList($searchModel, 'second_stone_type1', $model->getSecondStoneType1Map(), [
//                                    'prompt' => '全部',
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
//                                'headerOptions' => ['class' => 'col-md-1 batch_select_full', 'attr-name' => 'second_stone_type1', 'attr-id' => AttrIdEnum::SIDE_STONE1_TYPE, 'style' => 'background-color:#dec674;'],
//                            ],
                            [
                                'class' => 'yii\grid\CheckboxColumn',
                                'name' => 'id',  //设置每行数据的复选框属性
                                'visible' => $model->isVisible($bill, 'id'),
                            ],
                            [
                                'attribute' => 'goods_name',
                                //'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#84bf96;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#84bf96;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('goods_name');
                                    return $model->goods_name ?? "";
                                },
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'goods_name', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:200px;'
//                                ]),
                                'visible' => $model->isVisible($bill, 'goods_name4'),
                            ],
                            [
                                'attribute' => 'second_pei_type2',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1 batch_select_full', 'attr-name' => 'second_pei_type2', 'style' => 'background-color:#84bf96;'],
                                'footerOptions' => ['class' => 'col-md-1 batch_select_full2', 'attr-name' => 'second_pei_type2', 'style' => 'background-color:#84bf96;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('second_pei_type2');
                                    return Html::ajaxSelect($model, 'second_pei_type2', \addons\Warehouse\common\enums\PeiShiWayEnum::getMap(), ['data-id' => $model->id]);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'second_pei_type2', \addons\Warehouse\common\enums\PeiShiWayEnum::getMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'second_pei_type2'),
                            ],
                            [
                                'attribute' => 'second_stone_type2',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1 batch_select_full', 'attr-name' => 'second_stone_type2', 'attr-id' => AttrIdEnum::SIDE_STONE2_TYPE, 'style' => 'background-color:#84bf96;'],
                                'footerOptions' => ['class' => 'col-md-1 batch_select_full2', 'attr-name' => 'second_stone_type2', 'attr-id' => AttrIdEnum::SIDE_STONE2_TYPE, 'style' => 'background-color:#84bf96;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('second_stone_type2');
                                    return Html::ajaxSelect($model, 'second_stone_type2', $model->getSecondStoneType2Drop($model), ['data-id' => $model->id, 'prompt' => '请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'second_stone_type2', $model->getSecondStoneType2Map(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'second_stone_type2'),
                            ],
                            [
                                'attribute' => 'second_stone_sn2',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'second_stone_sn2', 'style' => 'background-color:#84bf96;'],
                                'footerOptions' => ['class' => 'col-md-1 batch_full2', 'attr-name' => 'second_stone_sn2', 'style' => 'background-color:#84bf96;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('second_stone_sn2');
                                    return Html::ajaxInput('second_stone_sn2', $model->second_stone_sn2, ['data-id' => $model->id]);
                                },
                                'filter' => Html::activeTextInput($searchModel, 'second_stone_sn2', [
                                    'class' => 'form-control',
                                    'style' => 'width:100px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'second_stone_sn2'),
                            ],
//                            [
//                                'attribute' => 'second_cert_id2',
//                                'format' => 'raw',
//                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'second_cert_id2', 'style' => 'background-color:#84bf96;'],
//                                'footerOptions' => ['class' => 'col-md-1 batch_full2', 'attr-name' => 'second_cert_id2', 'style' => 'background-color:#84bf96;'],
//                                'value' => function ($model, $key, $index, $widget) {
//                                    $widget->footer = $model->getAttributeLabel('second_cert_id2');
//                                    return Html::ajaxInput('second_cert_id2', $model->second_cert_id2, ['data-id' => $model->id]);
//                                },
//                                'filter' => Html::activeTextInput($searchModel, 'second_cert_id2', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:100px;'
//                                ]),
//                                'visible' => $model->isVisible($bill, 'material_type'),
//                            ],
                            [
                                'attribute' => 'second_stone_num2',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'second_stone_num2', 'style' => 'background-color:#84bf96;'],
                                'footerOptions' => ['class' => 'col-md-1 batch_full2', 'attr-name' => 'second_stone_num2', 'style' => 'background-color:#84bf96;'],
                                'value' => function ($model, $key, $index, $widget) use ($total) {
                                    $widget->footer = $model->getFooterValues('second_stone_num2', $total);
                                    return Html::ajaxInput('second_stone_num2', $model->second_stone_num2, ['data-id' => $model->id, 'onfocus' => 'rfClearVal(this)', 'data-type' => 'number']);
                                },
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'second_stone_num2', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                                'visible' => $model->isVisible($bill, 'second_stone_num2'),
                            ],
                            [
                                'attribute' => 'second_stone_weight2',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'id' => 'second_stone2', 'attr-name' => 'second_stone_weight2', 'style' => 'background-color:#84bf96;'],
                                'footerOptions' => ['class' => 'col-md-1 batch_full2', 'attr-name' => 'second_stone_weight2', 'style' => 'background-color:#84bf96;'],
                                'value' => function ($model, $key, $index, $widget) use ($total) {
                                    $widget->footer = $model->getFooterValues('second_stone_weight2', $total, "0.000", 3);
                                    return Html::ajaxInput('second_stone_weight2', $model->second_stone_weight2, ['data-id' => $model->id, 'onfocus' => 'rfClearVal(this)', 'data-type' => 'number']);
                                },
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'second_stone_weight2', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                                'visible' => $model->isVisible($bill, 'second_stone_weight2'),
                            ],
                            [
                                'attribute' => 'second_stone_color2',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1 batch_select_full', 'attr-name' => 'second_stone_color2', 'attr-id' => AttrIdEnum::SIDE_STONE2_COLOR, 'style' => 'background-color:#84bf96;'],
                                'footerOptions' => ['class' => 'col-md-1 batch_select_full2', 'attr-name' => 'second_stone_color2', 'attr-id' => AttrIdEnum::SIDE_STONE2_COLOR, 'style' => 'background-color:#84bf96;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('second_stone_color2');
                                    return Html::ajaxSelect($model, 'second_stone_color2', $model->getSecondStoneColor2Drop($model), ['data-id' => $model->id, 'prompt' => '请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'second_stone_color2', $model->getSecondStoneColor2Map(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'second_stone_color2'),
                            ],
                            [
                                'attribute' => 'second_stone_clarity2',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1 batch_select_full', 'attr-name' => 'second_stone_clarity2', 'attr-id' => AttrIdEnum::SIDE_STONE2_CLARITY, 'style' => 'background-color:#84bf96;'],
                                'footerOptions' => ['class' => 'col-md-1 batch_select_full2', 'attr-name' => 'second_stone_clarity2', 'attr-id' => AttrIdEnum::SIDE_STONE2_CLARITY, 'style' => 'background-color:#84bf96;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('second_stone_clarity2');
                                    return Html::ajaxSelect($model, 'second_stone_clarity2', $model->getSecondStoneClarity2Drop($model), ['data-id' => $model->id, 'prompt' => '请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'second_stone_clarity2', $model->getSecondStoneClarity2Map(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'second_stone_clarity2'),
                            ],
                            [
                                'attribute' => 'second_stone_price2',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'second_stone_price2', 'style' => 'background-color:#84bf96;'],
                                'footerOptions' => ['class' => 'col-md-1 batch_full2', 'attr-name' => 'second_stone_price2', 'style' => 'background-color:#84bf96;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('second_stone_price2');
                                    return Html::ajaxInput('second_stone_price2', $model->second_stone_price2, ['data-id' => $model->id, 'onfocus' => 'rfClearVal(this)', 'data-type' => 'number']);
                                },
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'second_stone_price2', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                                'visible' => $model->isVisible($bill, 'second_stone_price2'),
                            ],
                            [
                                'attribute' => 'second_stone_amount2',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'second_stone_amount2', 'style' => 'background-color:#84bf96;'],
                                'footerOptions' => ['class' => 'col-md-1 batch_full2', 'attr-name' => 'second_stone_amount2', 'style' => 'background-color:#84bf96;'],
                                'value' => function ($model, $key, $index, $widget) use ($total) {
                                    $widget->footer = $model->getFooterValues('second_stone_amount2', $total, "0.000");
                                    return Html::ajaxInput('second_stone_amount2', $model->second_stone_amount2, ['data-id' => $model->id, 'onfocus' => 'rfClearVal(this)', 'data-type' => 'number']);
                                },
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'second_stone_amount2', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                                'visible' => $model->isVisible($bill, 'second_stone_amount2'),
                            ],
                            /*[
                                'attribute' => 'second_stone_shape2',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1 batch_select_full', 'attr-name' => 'second_stone_shape2', 'attr-id' => AttrIdEnum::SIDE_STONE2_SHAPE, 'style' => 'background-color:#84bf96;'],
                                'footerOptions' => ['class' => 'col-md-1 batch_select_full2', 'attr-name' => 'second_stone_shape2', 'attr-id' => AttrIdEnum::SIDE_STONE2_SHAPE, 'style' => 'background-color:#84bf96;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('second_stone_shape2');
                                    return Html::ajaxSelect($model, 'second_stone_shape2', $model->getSecondStoneShape2Drop($model), ['data-id' => $model->id, 'prompt' => '请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'second_stone_shape2', $model->getSecondStoneShape2Map(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
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
                            [
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
                            ],
                            [
                                'attribute' => 'second_stone_size2',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#84bf96;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#84bf96;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('second_stone_size2');
                                    return Html::ajaxInput('second_stone_size2', $model->second_stone_size2, ['data-id' => $model->id]);
                                },
                                'filter' => Html::activeTextInput($searchModel, 'second_stone_size2', [
                                    'class' => 'form-control',
                                    'style' => 'width:100px;'
                                ]),
                            ],
                            [
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
                                'attribute' => 'second_stone_type3',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column) {
                                    return Html::ajaxSelect($model, 'second_stone_type3', $model->getSecondStoneType3Drop($model), ['data-id' => $model->id, 'prompt' => '请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'second_stone_type3', $model->getSecondStoneType3Map(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1 batch_select_full', 'attr-name' => 'second_stone_type3', 'attr-id' => AttrIdEnum::SIDE_STONE3_TYPE, 'style' => 'background-color:#f8aba6;'],
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
                                'class' => 'yii\grid\CheckboxColumn',
                                'name' => 'id',  //设置每行数据的复选框属性
                                'visible' => $model->isVisible($bill, 'id'),
                            ],
                            [
                                'attribute' => 'goods_name',
                                //'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#6495ED;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#6495ED;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('goods_name');
                                    return $model->goods_name ?? "";
                                },
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'goods_name', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:200px;'
//                                ]),
                                'visible' => $model->isVisible($bill, 'goods_name5'),
                            ],
                            [
                                'attribute' => 'second_pei_type3',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1 batch_select_full', 'attr-name' => 'second_pei_type3', 'style' => 'background-color:#6495ED;'],
                                'footerOptions' => ['class' => 'col-md-1 batch_select_full2', 'attr-name' => 'second_pei_type3', 'style' => 'background-color:#6495ED;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('second_pei_type3');
                                    return Html::ajaxSelect($model, 'second_pei_type3', \addons\Warehouse\common\enums\PeiShiWayEnum::getMap(), ['data-id' => $model->id]);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'second_pei_type3', \addons\Warehouse\common\enums\PeiShiWayEnum::getMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'second_pei_type3'),
                            ],
                            [
                                'attribute' => 'second_stone_type3',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1 batch_select_full', 'attr-name' => 'second_stone_type3', 'attr-id' => AttrIdEnum::SIDE_STONE3_TYPE, 'style' => 'background-color:#6495ED;'],
                                'footerOptions' => ['class' => 'col-md-1 batch_select_full2', 'attr-name' => 'second_stone_type3', 'attr-id' => AttrIdEnum::SIDE_STONE3_TYPE, 'style' => 'background-color:#6495ED;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('second_stone_type3');
                                    return Html::ajaxSelect($model, 'second_stone_type3', $model->getSecondStoneType3Drop($model), ['data-id' => $model->id, 'prompt' => '请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'second_stone_type3', $model->getSecondStoneType3Map(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'second_stone_type3'),
                            ],
                            [
                                'attribute' => 'second_stone_sn3',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'second_stone_sn3', 'style' => 'background-color:#6495ED;'],
                                'footerOptions' => ['class' => 'col-md-1 batch_full2', 'attr-name' => 'second_stone_sn3', 'style' => 'background-color:#6495ED;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('second_stone_sn3');
                                    return Html::ajaxInput('second_stone_sn3', $model->second_stone_sn3, ['data-id' => $model->id]);
                                },
                                'filter' => Html::activeTextInput($searchModel, 'second_stone_sn3', [
                                    'class' => 'form-control',
                                    'style' => 'width:100px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'second_stone_sn3'),
                            ],
                            [
                                'attribute' => 'second_stone_num3',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'second_stone_num3', 'style' => 'background-color:#6495ED;'],
                                'footerOptions' => ['class' => 'col-md-1 batch_full2', 'attr-name' => 'second_stone_num3', 'style' => 'background-color:#6495ED;'],
                                'value' => function ($model, $key, $index, $widget) use ($total) {
                                    $widget->footer = $model->getFooterValues('second_stone_num3', $total);
                                    return Html::ajaxInput('second_stone_num3', $model->second_stone_num3, ['data-id' => $model->id, 'onfocus' => 'rfClearVal(this)', 'data-type' => 'number']);
                                },
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'second_stone_num3', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                                'visible' => $model->isVisible($bill, 'second_stone_num3'),
                            ],
                            [
                                'attribute' => 'second_stone_weight3',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'id' => 'second_stone3', 'attr-name' => 'second_stone_weight3', 'style' => 'background-color:#6495ED;'],
                                'footerOptions' => ['class' => 'col-md-1 batch_full2', 'attr-name' => 'second_stone_weight3', 'style' => 'background-color:#6495ED;'],
                                'value' => function ($model, $key, $index, $widget) use ($total) {
                                    $widget->footer = $model->getFooterValues('second_stone_weight3', $total, "0.000", 3);
                                    return Html::ajaxInput('second_stone_weight3', $model->second_stone_weight3, ['data-id' => $model->id, 'onfocus' => 'rfClearVal(this)', 'data-type' => 'number']);
                                },
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'second_stone_weight3', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                                'visible' => $model->isVisible($bill, 'second_stone_weight3'),
                            ],
                            [
                                'attribute' => 'second_stone_color3',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1 batch_select_full', 'attr-name' => 'second_stone_color3', 'attr-id' => AttrIdEnum::SIDE_STONE3_COLOR, 'style' => 'background-color:#6495ED;'],
                                'footerOptions' => ['class' => 'col-md-1 batch_select_full2', 'attr-name' => 'second_stone_color3', 'attr-id' => AttrIdEnum::SIDE_STONE3_COLOR, 'style' => 'background-color:#6495ED;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('second_stone_color3');
                                    return Html::ajaxSelect($model, 'second_stone_color3', $model->getSecondStoneColor3Drop($model), ['data-id' => $model->id, 'prompt' => '请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'second_stone_color3', $model->getSecondStoneColor3Map(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'second_stone_color3'),
                            ],
                            [
                                'attribute' => 'second_stone_clarity3',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1 batch_select_full', 'attr-name' => 'second_stone_clarity3', 'attr-id' => AttrIdEnum::SIDE_STONE3_CLARITY, 'style' => 'background-color:#6495ED;'],
                                'footerOptions' => ['class' => 'col-md-1 batch_select_full2', 'attr-name' => 'second_stone_clarity3', 'attr-id' => AttrIdEnum::SIDE_STONE3_CLARITY, 'style' => 'background-color:#6495ED;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('second_stone_clarity3');
                                    return Html::ajaxSelect($model, 'second_stone_clarity3', $model->getSecondStoneClarity3Drop($model), ['data-id' => $model->id, 'prompt' => '请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'second_stone_clarity3', $model->getSecondStoneClarity3Map(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'second_stone_clarity3'),
                            ],
                            [
                                'attribute' => 'second_stone_price3',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'second_stone_price3', 'style' => 'background-color:#6495ED;'],
                                'footerOptions' => ['class' => 'col-md-1 batch_full2', 'attr-name' => 'second_stone_price3', 'style' => 'background-color:#6495ED;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('second_stone_price3');
                                    return Html::ajaxInput('second_stone_price3', $model->second_stone_price3, ['data-id' => $model->id, 'onfocus' => 'rfClearVal(this)', 'data-type' => 'number']);
                                },
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'second_stone_price3', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                                'visible' => $model->isVisible($bill, 'second_stone_price3'),
                            ],
                            [
                                'attribute' => 'second_stone_amount3',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'second_stone_amount3', 'style' => 'background-color:#6495ED;'],
                                'footerOptions' => ['class' => 'col-md-1 batch_full2', 'attr-name' => 'second_stone_amount3', 'style' => 'background-color:#6495ED;'],
                                'value' => function ($model, $key, $index, $widget) use ($total) {
                                    $widget->footer = $model->getFooterValues('second_stone_amount3', $total, "0.000");
                                    return Html::ajaxInput('second_stone_amount3', $model->second_stone_amount3, ['data-id' => $model->id, 'onfocus' => 'rfClearVal(this)', 'data-type' => 'number']);
                                },
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'second_stone_amount3', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                                'visible' => $model->isVisible($bill, 'second_stone_amount3'),
                            ],
                            [
                                'attribute' => 'stone_remark',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#6495ED;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#6495ED;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('stone_remark');
                                    return Html::ajaxInput('stone_remark', $model->stone_remark, ['data-id' => $model->id]);
                                },
                                'filter' => Html::activeTextInput($searchModel, 'stone_remark', [
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'stone_remark'),
                            ],
                            [
                                'class' => 'yii\grid\CheckboxColumn',
                                'name' => 'id',  //设置每行数据的复选框属性
                                'visible' => $model->isVisible($bill, 'id'),
                            ],
                            [
                                'attribute' => 'goods_name',
                                //'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#cde6c7;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#cde6c7;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('goods_name');
                                    return $model->goods_name ?? "";
                                },
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'goods_name', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:200px;'
//                                ]),
                                'visible' => $model->isVisible($bill, 'goods_name6'),
                            ],
                            [
                                'attribute' => 'parts_way',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1 batch_select_full', 'id' => 'parts', 'attr-name' => 'parts_way', 'style' => 'background-color:#cde6c7;'],
                                'footerOptions' => ['class' => 'col-md-1 batch_select_full2', 'attr-name' => 'parts_way', 'style' => 'background-color:#cde6c7;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('parts_way');
                                    return Html::ajaxSelect($model, 'parts_way', $model->getPeiJianWayMap(), ['data-id' => $model->id]);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'parts_way', $model->getPeiJianWayMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'parts_way'),
                            ],
                            [
                                'attribute' => 'parts_type',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1 batch_select_full', 'attr-name' => 'parts_type', 'attr-id' => AttrIdEnum::MAT_PARTS_TYPE, 'style' => 'background-color:#cde6c7;'],
                                'footerOptions' => ['class' => 'col-md-1 batch_select_full2', 'attr-name' => 'parts_type', 'attr-id' => AttrIdEnum::MAT_PARTS_TYPE, 'style' => 'background-color:#cde6c7;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('parts_type');
                                    return Html::ajaxSelect($model, 'parts_type', $model->getPartsTypeDrop($model), ['data-id' => $model->id, 'prompt' => '请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'parts_type', $model->getPartsTypeMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'parts_type'),
                            ],
                            [
                                'attribute' => 'parts_material',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1 batch_select_full', 'attr-name' => 'parts_material', 'attr-id' => AttrIdEnum::MATERIAL_TYPE, 'style' => 'background-color:#cde6c7;'],
                                'footerOptions' => ['class' => 'col-md-1 batch_select_full2', 'attr-name' => 'parts_material', 'attr-id' => AttrIdEnum::MATERIAL_TYPE, 'style' => 'background-color:#cde6c7;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('parts_material');
                                    return Html::ajaxSelect($model, 'parts_material', $model->getPartsMaterialDrop($model), ['data-id' => $model->id, 'prompt' => '请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'parts_material', $model->getPartsMaterialMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'parts_material'),
                            ],
                            [
                                'attribute' => 'parts_num',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'id' => 'parts', 'attr-name' => 'parts_num', 'style' => 'background-color:#cde6c7;'],
                                'footerOptions' => ['class' => 'col-md-1 batch_full2', 'attr-name' => 'parts_num', 'style' => 'background-color:#cde6c7;'],
                                'value' => function ($model, $key, $index, $widget) use ($total) {
                                    $widget->footer = $model->getFooterValues('parts_num', $total);
                                    return Html::ajaxInput('parts_num', $model->parts_num, ['data-id' => $model->id, 'onfocus' => 'rfClearVal(this)', 'data-type' => 'number']);
                                },
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'parts_num', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                                'visible' => $model->isVisible($bill, 'parts_num'),
                            ],
                            [
                                'attribute' => 'parts_gold_weight',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'parts_gold_weight', 'style' => 'background-color:#cde6c7;'],
                                'footerOptions' => ['class' => 'col-md-1 batch_full2', 'attr-name' => 'parts_gold_weight', 'style' => 'background-color:#cde6c7;'],
                                'value' => function ($model, $key, $index, $widget) use ($total) {
                                    $widget->footer = $model->getFooterValues('parts_gold_weight', $total);
                                    return Html::ajaxInput('parts_gold_weight', $model->parts_gold_weight, ['data-id' => $model->id, 'onfocus' => 'rfClearVal(this)', 'data-type' => 'number']);
                                },
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'parts_gold_weight', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                                'visible' => $model->isVisible($bill, 'parts_gold_weight'),
                            ],
                            [
                                'attribute' => 'parts_price',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'parts_price', 'style' => 'background-color:#cde6c7;'],
                                'footerOptions' => ['class' => 'col-md-1 batch_full2', 'attr-name' => 'parts_price', 'style' => 'background-color:#cde6c7;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('parts_price');
                                    return Html::ajaxInput('parts_price', $model->parts_price, ['data-id' => $model->id, 'onfocus' => 'rfClearVal(this)', 'data-type' => 'number']);
                                },
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'parts_price', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                                'visible' => $model->isVisible($bill, 'parts_price'),
                            ],
                            [
                                'attribute' => 'parts_amount',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'parts_amount', 'style' => 'background-color:#cde6c7;'],
                                'footerOptions' => ['class' => 'col-md-1 batch_full2', 'attr-name' => 'parts_amount', 'style' => 'background-color:#cde6c7;'],
                                'value' => function ($model, $key, $index, $widget) use ($total) {
                                    $widget->footer = $model->getFooterValues('parts_amount', $total, "0.000");
                                    return Html::ajaxInput('parts_amount', $model->parts_amount, ['data-id' => $model->id, 'onfocus' => 'rfClearVal(this)', 'data-type' => 'number']);
                                },
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'parts_amount', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                                'visible' => $model->isVisible($bill, 'parts_amount'),
                            ],
                            [
                                'class' => 'yii\grid\CheckboxColumn',
                                'name' => 'id',  //设置每行数据的复选框属性
                                'visible' => $model->isVisible($bill, 'id'),
                            ],
                            [
                                'attribute' => 'goods_name',
                                //'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#FFA500;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#FFA500;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('goods_name');
                                    return $model->goods_name ?? "";
                                },
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'goods_name', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:200px;'
//                                ]),
                                'visible' => $model->isVisible($bill, 'goods_name7'),
                            ],
                            [
                                'attribute' => 'gong_fee',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'gong_fee', 'style' => 'background-color:#FFA500;'],
                                'footerOptions' => ['class' => 'col-md-1 batch_full2', 'attr-name' => 'gong_fee', 'style' => 'background-color:#FFA500;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('gong_fee');
                                    return Html::ajaxInput('gong_fee', $model->gong_fee, ['data-id' => $model->id, 'onfocus' => 'rfClearVal(this)', 'data-type' => 'number']);
                                },
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'gong_fee', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                                'visible' => $model->isVisible($bill, 'gong_fee'),
                            ],
                            [
                                'attribute' => 'piece_fee',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'piece_fee', 'style' => 'background-color:#FFA500;'],
                                'footerOptions' => ['class' => 'col-md-1 batch_full2', 'attr-name' => 'piece_fee', 'style' => 'background-color:#FFA500;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('piece_fee');
                                    return Html::ajaxInput('piece_fee', $model->piece_fee, ['data-id' => $model->id, 'onfocus' => 'rfClearVal(this)', 'data-type' => 'number']);
                                },
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'piece_fee', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                                'visible' => $model->isVisible($bill, 'piece_fee'),
                            ],
                            [
                                'attribute' => 'basic_gong_fee',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'piece_fee', 'style' => 'background-color:#FFA500;'],
                                'footerOptions' => ['class' => 'col-md-1 batch_full2', 'attr-name' => 'piece_fee', 'style' => 'background-color:#FFA500;'],
                                'value' => function ($model, $key, $index, $widget) use ($total) {
                                    $widget->footer = $model->getFooterValues('basic_gong_fee', $total, "0.00");
                                    return Html::ajaxInput('basic_gong_fee', $model->basic_gong_fee, ['data-id' => $model->id, 'onfocus' => 'rfClearVal(this)', 'data-type' => 'number']);
                                },
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'basic_gong_fee', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                                'visible' => $model->isVisible($bill, 'basic_gong_fee'),
                            ],
//                            [
//                                'attribute' => 'peishi_num',
//                                'format' => 'raw',
//                                'value' => function ($model, $key, $index, $column) {
//                                    return Html::ajaxInput('peishi_num', $model->peishi_num, ['data-id' => $model->id, 'onfocus' => 'rfClearVal(this)', 'data-type' => 'number']);
//                                },
//                                'filter' => false,
//                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'peishi_num', 'style' => 'background-color:#FFA500;'],
////                                'filter' => Html::activeTextInput($searchModel, 'peishi_num', [
////                                    'class' => 'form-control',
////                                    'style' => 'width:80px;'
////                                ]),
//                            ],
                            [
                                'attribute' => 'peishi_weight',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'peishi_weight', 'style' => 'background-color:#FFA500;'],
                                'footerOptions' => ['class' => 'col-md-1 batch_full2', 'attr-name' => 'peishi_weight', 'style' => 'background-color:#FFA500;'],
                                'value' => function ($model, $key, $index, $widget) use ($total) {
                                    $widget->footer = $model->getFooterValues('peishi_weight', $total, "0.000");
                                    return Html::ajaxInput('peishi_weight', $model->peishi_weight, ['data-id' => $model->id, 'onfocus' => 'rfClearVal(this)', 'data-type' => 'number']);
                                },
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'peishi_weight', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                                'visible' => $model->isVisible($bill, 'peishi_weight'),
                            ],
                            [
                                'attribute' => 'peishi_gong_fee',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'peishi_gong_fee', 'style' => 'background-color:#FFA500;'],
                                'footerOptions' => ['class' => 'col-md-1 batch_full2', 'attr-name' => 'peishi_gong_fee', 'style' => 'background-color:#FFA500;'],
                                'value' => function ($model, $key, $index, $widget) use ($total) {
                                    $widget->footer = $model->getFooterValues('peishi_gong_fee', $total, "0.00");
                                    return Html::ajaxInput('peishi_gong_fee', $model->peishi_gong_fee, ['data-id' => $model->id, 'onfocus' => 'rfClearVal(this)', 'data-type' => 'number']);
                                },
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'peishi_gong_fee', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                                'visible' => $model->isVisible($bill, 'peishi_gong_fee'),
                            ],
                            [
                                'attribute' => 'peishi_fee',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'id' => 'fee1', 'attr-name' => 'peishi_fee', 'style' => 'background-color:#FFA500;'],
                                'footerOptions' => ['class' => 'col-md-1 batch_full2', 'attr-name' => 'peishi_fee', 'style' => 'background-color:#FFA500;'],
                                'value' => function ($model, $key, $index, $widget) use ($total) {
                                    $widget->footer = $model->getFooterValues('peishi_fee', $total, "0.00");
                                    return Html::ajaxInput('peishi_fee', $model->peishi_fee, ['data-id' => $model->id, 'onfocus' => 'rfClearVal(this)', 'data-type' => 'number']);
                                },
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'peishi_fee', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                                'visible' => $model->isVisible($bill, 'peishi_fee'),
                            ],
                            [
                                'attribute' => 'parts_fee',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'parts_fee', 'style' => 'background-color:#FFA500;'],
                                'footerOptions' => ['class' => 'col-md-1 batch_full2', 'attr-name' => 'parts_fee', 'style' => 'background-color:#FFA500;'],
                                'value' => function ($model, $key, $index, $widget) use ($total) {
                                    $widget->footer = $model->getFooterValues('parts_fee', $total, "0.00");
                                    return Html::ajaxInput('parts_fee', $model->parts_fee, ['data-id' => $model->id, 'onfocus' => 'rfClearVal(this)', 'data-type' => 'number']);
                                },
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'parts_fee', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                                'visible' => $model->isVisible($bill, 'parts_fee'),
                            ],
                            [
                                'attribute' => 'xiangqian_craft',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1 batch_select_full', 'attr-name' => 'xiangqian_craft', 'attr-id' => AttrIdEnum::XIANGQIAN_CRAFT, 'style' => 'background-color:#FFA500;'],
                                'footerOptions' => ['class' => 'col-md-1 batch_select_full2', 'attr-name' => 'xiangqian_craft', 'attr-id' => AttrIdEnum::XIANGQIAN_CRAFT, 'style' => 'background-color:#FFA500;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('xiangqian_craft');
                                    return Html::ajaxSelect($model, 'xiangqian_craft', $model->getXiangqianCraftDrop($model), ['data-id' => $model->id, 'prompt' => '请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'xiangqian_craft', $model->getXiangqianCraftMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'xiangqian_craft'),
                            ],
                            [
                                'attribute' => 'second_stone_fee1',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'second_stone_fee1', 'style' => 'background-color:#FFA500;'],
                                'footerOptions' => ['class' => 'col-md-1 batch_full2', 'attr-name' => 'second_stone_fee1', 'style' => 'background-color:#FFA500;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('second_stone_fee1');
                                    return Html::ajaxInput('second_stone_fee1', $model->second_stone_fee1, ['data-id' => $model->id, 'onfocus' => 'rfClearVal(this)', 'data-type' => 'number']);
                                },
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'second_stone_fee1', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                                'visible' => $model->isVisible($bill, 'second_stone_fee1'),
                            ],
                            [
                                'attribute' => 'second_stone_fee2',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'second_stone_fee2', 'style' => 'background-color:#FFA500;'],
                                'footerOptions' => ['class' => 'col-md-1 batch_full2', 'attr-name' => 'second_stone_fee2', 'style' => 'background-color:#FFA500;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('second_stone_fee2');
                                    return Html::ajaxInput('second_stone_fee2', $model->second_stone_fee2, ['data-id' => $model->id, 'onfocus' => 'rfClearVal(this)', 'data-type' => 'number']);
                                },
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'second_stone_fee2', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                                'visible' => $model->isVisible($bill, 'second_stone_fee2'),
                            ],
                            [
                                'attribute' => 'second_stone_fee3',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'second_stone_fee3', 'style' => 'background-color:#FFA500;'],
                                'footerOptions' => ['class' => 'col-md-1 batch_full2', 'attr-name' => 'second_stone_fee3', 'style' => 'background-color:#FFA500;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('second_stone_fee3');
                                    return Html::ajaxInput('second_stone_fee3', $model->second_stone_fee3, ['data-id' => $model->id, 'onfocus' => 'rfClearVal(this)', 'data-type' => 'number']);
                                },
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'second_stone_fee3', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                                'visible' => $model->isVisible($bill, 'second_stone_fee3'),
                            ],
                            [
                                'attribute' => 'xianqian_fee',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'xianqian_fee', 'style' => 'background-color:#FFA500;'],
                                'footerOptions' => ['class' => 'col-md-1 batch_full2', 'attr-name' => 'xianqian_fee', 'style' => 'background-color:#FFA500;'],
                                'value' => function ($model, $key, $index, $widget) use ($total) {
                                    $widget->footer = $model->getFooterValues('xianqian_fee', $total, "0.000");
                                    return Html::ajaxInput('xianqian_fee', $model->xianqian_fee, ['data-id' => $model->id, 'onfocus' => 'rfClearVal(this)', 'data-type' => 'number']);
                                },
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'xianqian_fee', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                                'visible' => $model->isVisible($bill, 'xianqian_fee'),
                            ],
//                            [
//                                'attribute' => 'xianqian_price',
//                                'format' => 'raw',
//                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'xianqian_price', 'style' => 'background-color:#FFA500;'],
//                                'footerOptions' => ['class' => 'col-md-1 batch_full2', 'attr-name' => 'xianqian_price', 'style' => 'background-color:#FFA500;'],
//                                'value' => function ($model, $key, $index, $widget) {
//                                    $widget->footer = $model->getAttributeLabel('xianqian_price');
//                                    return Html::ajaxInput('xianqian_price', $model->xianqian_price, ['data-id' => $model->id, 'onfocus' => 'rfClearVal(this)', 'data-type' => 'number']);
//                                },
//                                'filter' => false,
////                                'filter' => Html::activeTextInput($searchModel, 'xianqian_price', [
////                                    'class' => 'form-control',
////                                    'style' => 'width:80px;'
////                                ]),
//                            ],
//                            [
//                                'attribute' => 'biaomiangongyi',
//                                'format' => 'raw',
//                                'headerOptions' => ['class' => 'col-md-1 batch_select_full', 'attr-name' => 'biaomiangongyi', 'attr-id' => AttrIdEnum::FACEWORK, 'style' => 'background-color:#FFA500;'],
//                                'footerOptions' => ['class' => 'col-md-1 batch_select_full2', 'attr-name' => 'biaomiangongyi', 'attr-id' => AttrIdEnum::FACEWORK, 'style' => 'background-color:#FFA500;'],
//                                'value' => function ($model, $key, $index, $widget) {
//                                    $widget->footer = $model->getAttributeLabel('biaomiangongyi');
//                                    return Html::ajaxSelect($model, 'biaomiangongyi', $model->getFaceCraftDrop($model), ['data-id' => $model->id, 'prompt' => '请选择']);
//                                },
//                                'filter' => Html::activeDropDownList($searchModel, 'biaomiangongyi', $model->getFaceCraftMap(), [
//                                    'prompt' => '全部',
//                                    'class' => 'form-control',
//                                    'style' => 'width:100px;'
//                                ]),
//                            ],
                            [
                                'class' => 'yii\grid\CheckboxColumn',
                                'name' => 'id',  //设置每行数据的复选框属性
                                'visible' => $model->isVisible($bill, 'id'),
                            ],
                            [
                                'attribute' => 'biaomiangongyi_fee',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'biaomiangongyi_fee', 'style' => 'background-color:#E6E6FA;'],
                                'footerOptions' => ['class' => 'col-md-1 batch_full2', 'attr-name' => 'biaomiangongyi_fee', 'style' => 'background-color:#E6E6FA;'],
                                'value' => function ($model, $key, $index, $widget) use ($total) {
                                    $widget->footer = $model->getFooterValues('biaomiangongyi_fee', $total, "0.00");
                                    return Html::ajaxInput('biaomiangongyi_fee', $model->biaomiangongyi_fee, ['data-id' => $model->id, 'onfocus' => 'rfClearVal(this)', 'data-type' => 'number']);
                                },
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'biaomiangongyi_fee', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                                'visible' => $model->isVisible($bill, 'biaomiangongyi_fee'),
                            ],
                            [
                                'attribute' => 'fense_fee',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'fense_fee', 'style' => 'background-color:#E6E6FA;'],
                                'footerOptions' => ['class' => 'col-md-1 batch_full2', 'attr-name' => 'fense_fee', 'style' => 'background-color:#E6E6FA;'],
                                'value' => function ($model, $key, $index, $widget) use ($total) {
                                    $widget->footer = $model->getFooterValues('fense_fee', $total, "0.00");
                                    return Html::ajaxInput('fense_fee', $model->fense_fee, ['data-id' => $model->id, 'onfocus' => 'rfClearVal(this)', 'data-type' => 'number']);
                                },
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'fense_fee', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                                'visible' => $model->isVisible($bill, 'fense_fee'),
                            ],
                            [
                                'attribute' => 'penlasha_fee',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'penlasha_fee', 'style' => 'background-color:#E6E6FA;'],
                                'footerOptions' => ['class' => 'col-md-1 batch_full2', 'attr-name' => 'penlasha_fee', 'style' => 'background-color:#E6E6FA;'],
                                'value' => function ($model, $key, $index, $widget) use ($total) {
                                    $widget->footer = $model->getFooterValues('penlasha_fee', $total, "0.00");
                                    return Html::ajaxInput('penlasha_fee', $model->penlasha_fee, ['data-id' => $model->id, 'onfocus' => 'rfClearVal(this)', 'data-type' => 'number']);
                                },
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'penlasha_fee', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                                'visible' => $model->isVisible($bill, 'penlasha_fee'),
                            ],
                            [
                                'attribute' => 'lasha_fee',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'lasha_fee', 'style' => 'background-color:#E6E6FA;'],
                                'footerOptions' => ['class' => 'col-md-1 batch_full2', 'attr-name' => 'lasha_fee', 'style' => 'background-color:#E6E6FA;'],
                                'value' => function ($model, $key, $index, $widget) use ($total) {
                                    $widget->footer = $model->getFooterValues('lasha_fee', $total, "0.00");
                                    return Html::ajaxInput('lasha_fee', $model->lasha_fee, ['data-id' => $model->id, 'onfocus' => 'rfClearVal(this)', 'data-type' => 'number']);
                                },
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'lasha_fee', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                                'visible' => $model->isVisible($bill, 'lasha_fee'),
                            ],
                            [
                                'attribute' => 'bukou_fee',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'bukou_fee', 'style' => 'background-color:#E6E6FA;'],
                                'footerOptions' => ['class' => 'col-md-1 batch_full2', 'attr-name' => 'bukou_fee', 'style' => 'background-color:#E6E6FA;'],
                                'value' => function ($model, $key, $index, $widget) use ($total) {
                                    $widget->footer = $model->getFooterValues('bukou_fee', $total, "0.00");
                                    return Html::ajaxInput('bukou_fee', $model->bukou_fee, ['data-id' => $model->id, 'onfocus' => 'rfClearVal(this)', 'data-type' => 'number']);
                                },
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'bukou_fee', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                                'visible' => $model->isVisible($bill, 'bukou_fee'),
                            ],
                            [
                                'attribute' => 'templet_fee',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'id' => 'fee2', 'attr-name' => 'templet_fee', 'style' => 'background-color:#E6E6FA;'],
                                'footerOptions' => ['class' => 'col-md-1 batch_full2', 'attr-name' => 'templet_fee', 'style' => 'background-color:#E6E6FA;'],
                                'value' => function ($model, $key, $index, $widget) use ($total) {
                                    $widget->footer = $model->getFooterValues('templet_fee', $total, "0.00");
                                    return Html::ajaxInput('templet_fee', $model->templet_fee, ['data-id' => $model->id, 'onfocus' => 'rfClearVal(this)', 'data-type' => 'number']);
                                },
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'templet_fee', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                                'visible' => $model->isVisible($bill, 'templet_fee'),
                            ],
                            [
                                'attribute' => 'tax_fee',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'tax_fee', 'style' => 'background-color:#E6E6FA;'],
                                'footerOptions' => ['class' => 'col-md-1 batch_full2', 'attr-name' => 'tax_fee', 'style' => 'background-color:#E6E6FA;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('tax_fee');
                                    return Html::ajaxInput('tax_fee', $model->tax_fee, ['data-id' => $model->id, 'onfocus' => 'rfClearVal(this)', 'data-type' => 'number']);
                                },
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'tax_fee', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                                'visible' => $model->isVisible($bill, 'tax_fee'),
                            ],
                            [
                                'attribute' => 'tax_amount',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'tax_amount', 'style' => 'background-color:#E6E6FA;'],
                                'footerOptions' => ['class' => 'col-md-1 batch_full2', 'attr-name' => 'tax_amount', 'style' => 'background-color:#E6E6FA;'],
                                'value' => function ($model, $key, $index, $widget) use ($total) {
                                    $widget->footer = $model->getFooterValues('tax_amount', $total, "0.00");
                                    return Html::ajaxInput('tax_amount', $model->tax_amount, ['data-id' => $model->id, 'onfocus' => 'rfClearVal(this)', 'data-type' => 'number']);
                                },
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'tax_amount', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                                'visible' => $model->isVisible($bill, 'tax_amount'),
                            ],
                            [
                                'attribute' => 'cert_fee',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'cert_fee', 'style' => 'background-color:#E6E6FA;'],
                                'footerOptions' => ['class' => 'col-md-1 batch_full2', 'attr-name' => 'cert_fee', 'style' => 'background-color:#E6E6FA;'],
                                'value' => function ($model, $key, $index, $widget) use ($total) {
                                    $widget->footer = $model->getFooterValues('cert_fee', $total, "0.00");
                                    return Html::ajaxInput('cert_fee', $model->cert_fee, ['data-id' => $model->id, 'onfocus' => 'rfClearVal(this)', 'data-type' => 'number']);
                                },
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'cert_fee', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                                'visible' => $model->isVisible($bill, 'cert_fee'),
                            ],
                            [
                                'attribute' => 'other_fee',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'other_fee', 'style' => 'background-color:#E6E6FA;'],
                                'footerOptions' => ['class' => 'col-md-1 batch_full2', 'attr-name' => 'other_fee', 'style' => 'background-color:#E6E6FA;'],
                                'value' => function ($model, $key, $index, $widget) use ($total) {
                                    $widget->footer = $model->getFooterValues('other_fee', $total, "0.00");
                                    return Html::ajaxInput('other_fee', $model->other_fee, ['data-id' => $model->id, 'onfocus' => 'rfClearVal(this)', 'data-type' => 'number']);
                                },
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'other_fee', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                                'visible' => $model->isVisible($bill, 'other_fee'),
                            ],
                            [
                                'class' => 'yii\grid\CheckboxColumn',
                                'name' => 'id',  //设置每行数据的复选框属性
                                'visible' => $model->isVisible($bill, 'id'),
                            ],
                            [
                                'attribute' => 'goods_name',
                                //'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('goods_name');
                                    return $model->goods_name ?? "";
                                },
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'goods_name', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:200px;'
//                                ]),
                                'visible' => $model->isVisible($bill, 'goods_name8'),
                            ],
                            [
                                'attribute' => 'factory_cost',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'factory_cost', 'style' => 'background-color:#b7ba6b;'],
                                'footerOptions' => ['class' => 'col-md-1 batch_full2', 'attr-name' => 'factory_cost', 'style' => 'background-color:#b7ba6b;'],
                                'value' => function ($model, $key, $index, $widget) use ($total) {
                                    $widget->footer = $model->getFooterValues('factory_cost', $total, "0.00");
                                    return Html::ajaxInput('factory_cost', $model->factory_cost, ['data-id' => $model->id]);
                                },
                                'filter' => Html::activeTextInput($searchModel, 'factory_cost', [
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'factory_cost'),
                            ],
                            [
                                'attribute' => 'markup_rate',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'markup_rate', 'style' => 'background-color:#b7ba6b;'],
                                'footerOptions' => ['class' => 'col-md-1 batch_full2', 'attr-name' => 'markup_rate', 'style' => 'background-color:#b7ba6b;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('markup_rate');
                                    return Html::ajaxInput('markup_rate', $model->markup_rate, ['data-id' => $model->id]);
                                },
                                'filter' => Html::activeTextInput($searchModel, 'markup_rate', [
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'markup_rate'),
                            ],
                            [
                                'label' => '成本手填',
                                'attribute' => 'is_auto_price',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = "成本手填";
                                    return Html::ajaxSelect($model, 'is_auto_price', \common\enums\ConfirmEnum::getMap(), ['data-id' => $model->id]);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'is_auto_price', \common\enums\ConfirmEnum::getMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:60px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'is_auto_price'),
                            ],
                            [
                                'attribute' => 'cost_price',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
                                'contentOptions' => ['style' => 'color:red'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
                                'value' => function ($model, $key, $index, $widget) use ($total) {
                                    $widget->footer = $model->getFooterValues('cost_price', $total, "0.00");
                                    if ($model->is_auto_price) {
                                        return Html::ajaxInput('cost_price', $model->cost_price, ['data-id' => $model->id, 'onfocus' => 'rfClearVal(this)', 'data-type' => 'number']);
                                    } else {
                                        return $model->cost_price ?? "0.00";
                                    }
                                },
                                'visible' => \common\helpers\Auth::verify(\common\enums\SpecialAuthEnum::VIEW_CAIGOU_PRICE),
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'cost_price', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:100px;'
//                                ]),
                            ],
//                            [
//                                'attribute' => 'market_price',
//                                'format' => 'raw',
//                                'value' => function ($model, $key, $index, $column) {
//                                    return Html::ajaxInput('market_price', $model->market_price, ['data-id' => $model->id, 'onfocus' => 'rfClearVal(this)', 'data-type' => 'number']);
//                                },
//                                'filter' => false,
//                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'market_price', 'style' => 'background-color:#b7ba6b;'],
////                                'filter' => Html::activeTextInput($searchModel, 'market_price', [
////                                    'class' => 'form-control',
////                                    'style' => 'width:100px;'
////                                ]),
//                            ],
//                            [
//                                'attribute' => 'style_sex',
//                                'format' => 'raw',
//                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
//                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
//                                'value' => function ($model, $key, $index, $widget) {
//                                    $widget->footer = $model->getAttributeLabel('style_sex');
//                                    return \addons\Style\common\enums\StyleSexEnum::getValue($model->style_sex);
//                                },
//                                'filter' => Html::activeDropDownList($searchModel, 'style_sex', $model->getStyleSexMap(), [
//                                    'prompt' => '全部',
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
//                            ],
                            [
                                'attribute' => 'jintuo_type',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1 batch_select_full', 'id' => 'price', 'id' => 'parts', 'attr-name' => 'jintuo_type', 'style' => 'background-color:#b7ba6b;'],
                                'footerOptions' => ['class' => 'col-md-1 batch_select_full2', 'attr-name' => 'jintuo_type', 'style' => 'background-color:#b7ba6b;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('jintuo_type');
                                    return Html::ajaxSelect($model, 'jintuo_type', $model->getJietuoTypeMap(), ['data-id' => $model->id, 'prompt' => '请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'jintuo_type', $model->getJietuoTypeMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:60px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'jintuo_type'),
                            ],
//                            [
//                                'attribute' => 'qiban_type',
//                                'format' => 'raw',
//                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
//                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
//                                'value' => function ($model, $key, $index, $widget) {
//                                    $widget->footer = $model->getAttributeLabel('qiban_type');
//                                    return \addons\Style\common\enums\QibanTypeEnum::getValue($model->qiban_type);
//                                },
//                                'filter' => Html::activeDropDownList($searchModel, 'qiban_type', $model->getQibanTypeMap(), [
//                                    'prompt' => '全部',
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
//                            ],
//                            [
//                                'attribute' => 'is_inlay',
//                                'format' => 'raw',
//                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
//                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
//                                'value' => function ($model, $key, $index, $widget) {
//                                    $widget->footer = $model->getAttributeLabel('is_inlay');
//                                    //return \addons\Style\common\enums\InlayEnum::getValue($model->is_inlay);
//                                    return Html::ajaxSelect($model, 'is_inlay', $model->getIsInlayMap(), ['data-id' => $model->id, 'prompt' => '请选择']);
//                                },
//                                'filter' => Html::activeDropDownList($searchModel, 'is_inlay', $model->getIsInlayMap(), [
//                                    'prompt' => '全部',
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
//                            ],
                            [
                                'attribute' => 'main_cert_type',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1 batch_select_full', 'attr-name' => 'main_cert_type', 'attr-id' => AttrIdEnum::DIA_CERT_TYPE, 'style' => 'background-color:#b7ba6b;'],
                                'footerOptions' => ['class' => 'col-md-1 batch_select_full2', 'attr-name' => 'main_cert_type', 'attr-id' => AttrIdEnum::DIA_CERT_TYPE, 'style' => 'background-color:#b7ba6b;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('main_cert_type');
                                    return Html::ajaxSelect($model, 'main_cert_type', $model->getMainCertTypeDrop($model), ['data-id' => $model->id, 'prompt' => '请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'main_cert_type', $model->getMainCertTypeMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:80px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'main_cert_type'),
                            ],
                            [
                                'attribute' => 'main_cert_id',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'main_cert_id', 'style' => 'background-color:#b7ba6b;'],
                                'footerOptions' => ['class' => 'col-md-1 batch_full2', 'attr-name' => 'main_cert_id', 'style' => 'background-color:#b7ba6b;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('main_cert_id');
                                    return Html::ajaxInput('main_cert_id', $model->main_cert_id, ['data-id' => $model->id]);
                                },
                                'filter' => Html::activeTextInput($searchModel, 'main_cert_id', [
                                    'class' => 'form-control',
                                    'style' => 'width:100px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'main_cert_id'),
                            ],
                            [
                                'label' => '是否多件',
                                'attribute' => 'is_wholesale',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = "是否批发";
                                    return \addons\Warehouse\common\enums\IsWholeSaleEnum::getValue($model->is_wholesale);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'is_wholesale', \addons\Warehouse\common\enums\IsWholeSaleEnum::getMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:60px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'is_wholesale'),
                            ],
                            [
                                'attribute' => 'factory_mo',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'factory_mo', 'style' => 'background-color:#b7ba6b;'],
                                'footerOptions' => ['class' => 'col-md-1 batch_full2', 'attr-name' => 'factory_mo', 'style' => 'background-color:#b7ba6b;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('factory_mo');
                                    return Html::ajaxInput('factory_mo', $model->factory_mo, ['data-id' => $model->id]);
                                },
                                'filter' => Html::activeTextInput($searchModel, 'factory_mo', [
                                    'class' => 'form-control',
                                    'style' => 'width:100px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'factory_mo'),
                            ],
                            [
                                'attribute' => 'order_sn',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'order_sn', 'style' => 'background-color:#b7ba6b;'],
                                'footerOptions' => ['class' => 'col-md-1 batch_full2', 'attr-name' => 'order_sn', 'style' => 'background-color:#b7ba6b;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('order_sn');
                                    return Html::ajaxInput('order_sn', $model->order_sn, ['data-id' => $model->id]);
                                },
                                'filter' => Html::activeTextInput($searchModel, 'order_sn', [
                                    'class' => 'form-control',
                                    'style' => 'width:100px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'order_sn'),
                            ],
                            [
                                'attribute' => 'remark',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'id' => 'end', 'style' => 'background-color:#b7ba6b;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('remark');
                                    return Html::ajaxInput('remark', $model->remark, ['data-id' => $model->id]);
                                },
                                'filter' => Html::activeTextInput($searchModel, 'remark', [
                                    'class' => 'form-control',
                                    'style' => 'width:160px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'remark'),
                            ],
                            [
                                'class' => 'yii\grid\ActionColumn',
                                'header' => '操作',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
                                'contentOptions' => ['style' => ['white-space' => 'nowrap']],
                                'template' => '{edit} {show} {delete}',
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
                                    'show' => function ($url, $model, $key) use ($bill) {
                                        return Html::edit(['show', 'id' => $model->id, 'bill_id' => $bill->id], '查看', [
                                            'class' => 'btn btn-warning btn-xs openIframe',
                                            'data-width' => '90%',
                                            'data-height' => '90%',
                                            'data-offset' => '20px',
                                        ]);
                                    },
                                ],
                            ],
                            [
                                'class' => 'yii\grid\CheckboxColumn',
                                'name' => 'id',  //设置每行数据的复选框属性
                                'visible' => $model->isVisible($bill, 'id'),
                            ],
                        ]
                    ]); ?>
                </div>
                <span class="navbar-fixed-bottom text-center" style="font-size:10px; margin-bottom:10px">
                    <a id="mao_front" style="color: #d3d7d4;" href="#front">开头</a>
                    <a id="mao_attr" style="color: #d3d7d4;" href="#attr">属性</a>
                    <a id="mao_gold" style="color: #d3d7d4;" href="#gold">金料</a>
                    <a id="mao_main_stone" style="color: #d3d7d4;" href="#main_stone">主石</a>
                    <a id="mao_second_stone1" style="color: #d3d7d4;" href="#second_stone1">副石1</a>
                    <a id="mao_second_stone2" style="color: #d3d7d4;" href="#second_stone2">副石2</a>
                    <a id="mao_second_stone3" style="color: #d3d7d4;" href="#second_stone3">副石3</a>
                    <a id="mao_parts" style="color: #d3d7d4;" href="#parts">配件</a>
                    <a id="mao_fee1" style="color: #d3d7d4;" href="#fee1">工费1</a>
                    <a id="mao_fee2" style="color: #d3d7d4;" href="#fee2">工费2</a>
                    <a id="mao_price" style="color: #d3d7d4;" href="#price">价格</a>
                    <a id="mao_end" style="color: #d3d7d4;" href="#end">结尾</a>
                </span>
            </div>
        </div>
        <!-- box end -->
    </div>
    <!-- tab-content end -->
</div>
<script type="text/javascript">
    $(function () {
        //批量填充(文本)
        $(".batch_full > a").after('&nbsp;<?= Html::batchFullButton(['batch-edit'], ""); ?>');
        $(".batch_full2").append('&nbsp;<?= Html::batchFullButton(['batch-edit'], ""); ?>');
        //批量填充(下拉)
        $(".batch_select_full > a").after('&nbsp;<?= Html::batchFullButton(['batch-edit', 'check' => 1], "", ['input_type' => 'select']); ?>');
        $(".batch_select_full2").append('&nbsp;<?= Html::batchFullButton(['batch-edit', 'check' => 1], "", ['input_type' => 'select']); ?>');
        //默认全选
        $("input[name='id[]']").trigger("click");
    });

    function rfClearVal(obj) {
        var val = $(obj).val();
        if (val <= 0) {
            $(obj).val("");
        }
    }

    // 显示状态 status 1:隐藏;0显示;
    function rfHidden(obj) {
        let id = $(obj).attr('data-id');
        let name = $(obj).attr('data-name');
        let url = $(obj).attr('data-url');
        let text = $(obj).attr('data-text');
        let status = 0;
        self = $(obj);
        if (self.hasClass("btn-success")) {
            status = 1;
        }
        if (!url) {
            url = "<?= Url::to(['ajax-hidden'])?>";
        }
        $.ajax({
            type: "get",
            url: url,
            dataType: "json",
            data: {
                id: id,
                name: name,
                value: status,
            },
            success: function (data) {
                if (parseInt(data.code) === 200) {
                    if (self.hasClass("btn-success")) {
                        self.removeClass("btn-success").addClass("btn-default");
                        self.attr("data-toggle", 'tooltip');
                        self.attr("data-original-title", '点击隐藏');
                        self.text(text);
                    } else {
                        self.removeClass("btn-default").addClass("btn-success");
                        self.attr("data-toggle", 'tooltip');
                        self.attr("data-original-title", '点击显示');
                        self.text(text);
                    }
                    window.location.reload();
                } else {
                    rfAffirm(data.message);
                }
            }
        });
    }

    $(".jsBatchUpdate").click(function () {
        let grid = $(this).attr('data-grid');
        let url = $(this).attr('data-url');
        let status = $(this).attr('data-value');
        let text = $(this).text();
        let ids = $("#" + grid).yiiGridView("getSelectedRows");
        if (!url) {
            url = "<?= Url::to(['ajax-batch-update'])?>";
        }
        if (ids == "" || !ids) {
            rfInfo('未选中数据！', '');
            return false;
        }
        appConfirm("确定要" + text + "吗?", '', function (code) {
            switch (code) {
                case "defeat":
                    $.ajax({
                        type: "post",
                        url: url,
                        dataType: "json",
                        data: {
                            ids: ids,
                            status: status
                        },
                        success: function (data) {
                            if (parseInt(data.code) !== 200) {
                                rfAffirm(data.message);
                            } else {
                                //rfAffirm(data.message);
                                window.location.reload();
                            }
                        }
                    });
                    break;
                default:
            }
        })
    });
</script>
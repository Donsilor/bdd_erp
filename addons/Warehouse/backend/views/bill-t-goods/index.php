<?php

use common\helpers\Url;
use common\helpers\Html;
use common\helpers\ImageHelper;
use addons\Warehouse\common\enums\BillStatusEnum;
use kartik\select2\Select2;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('bill_t_goods', '其它入库单明细');
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$params = Yii::$app->request->queryParams;
$params = $params ? "&" . http_build_query($params) : '';

$goods_type = $bill->billL->goods_type ?? 0;
?>
<style>
    select.form-control {
        font-size: 12px;
    }
</style>
<div class="box-body nav-tabs-custom">
    <h2 class="page-header"><?= $this->title; ?> - <span id="bill_no"><?= $bill->bill_no ?></span> <i class="fa fa-copy"
                                                                                                      onclick="copy('bill_no')"></i>
        - <?= \addons\Warehouse\common\enums\BillStatusEnum::getValue($bill->bill_status) ?></h2>
    <?php echo Html::menuTab($tabList, $tab) ?>
    <div class="box-tools" style="float:right;margin-top:-40px; margin-right: 20px;">
        <?php
        //        echo Html::a('返回列表', ['bill-t/index'], ['class' => 'btn btn-white btn-xs']);
        //        echo '&nbsp;';
        if ($bill->bill_status == \addons\Warehouse\common\enums\BillStatusEnum::SAVE) {
            echo Html::create(['ajax-edit', 'bill_id' => $bill->id], '新增货品', [
                'class' => 'btn btn-primary btn-xs',
                'data-toggle' => 'modal',
                'data-target' => '#ajaxModal',
            ]);
            echo '&nbsp;';
            echo Html::edit(['edit-all', 'bill_id' => $bill->id], '批量编辑', ['class' => 'btn btn-info btn-xs']);
            echo '&nbsp;';
        }
        if ($bill->bill_status == \addons\Warehouse\common\enums\BillStatusEnum::CONFIRM) {
            echo Html::batchPopButton(['create-pay', 'bill_id' => $bill->id, 'check' => 1], '分批结算', [
                'class' => 'btn btn-primary btn-xs',
                'data-width' => '60%',
                'data-height' => '60%',
                'data-offset' => '20px',
            ]);
            echo '&nbsp;';
        }
        echo Html::a('单据打印', ['bill-t/print', 'id' => $bill->id], ['target' => '_blank', 'class' => 'btn btn-info btn-xs',]);
        //, 'onclick' => 'rfTwiceAffirm(this,"打印单据", "确定打印吗？");return false;'
        echo '&nbsp;';
        if ($bill->bill_status == \addons\Warehouse\common\enums\BillStatusEnum::SAVE) {
            echo Html::edit(['ajax-upload', 'bill_id' => $bill->id], '批量导入', [
                'class' => 'btn btn-success btn-xs',
                'data-toggle' => 'modal',
                'data-target' => '#ajaxModal',
            ]);
            echo '&nbsp;';
        }
        echo Html::button('明细导出', ['class' => 'btn btn-success btn-xs', 'onclick' => 'batchExport()',]);
        echo '&nbsp;';
        if ($goods_type == \addons\Warehouse\common\enums\GoodsTypeEnum::SeikoStone) {
            echo Html::tag('span', '价格刷新', ["class" => "btn btn-warning btn-xs jsBatchUpdate", "data-grid" => "grid", "data-url" => Url::to(['update-price']),]);
            echo '&nbsp;';
        }
        if ($bill->bill_status == \addons\Warehouse\common\enums\BillStatusEnum::SAVE) {
            echo Html::tag('span', '批量删除', ["class" => "btn btn-danger btn-xs jsBatchUpdate", "data-grid" => "grid", "data-url" => Url::to(['batch-delete']),]);
        }
        ?>
    </div>
    <div class="tab-content">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-body table-responsive">
                    <span style="font-size:16px">
                        <!--<span style="font-weight:bold;">明细汇总：</span>-->
                        货品总数：<span style="color:green;"><?= $bill->goods_num ?? 0 ?></span>
                        折足总重：<span style="color:green;"><?= $total['factory_gold_weight'] ?? 0 ?></span>
                        工厂总成本：<span style="color:green;"><?= $bill->billL->total_factory_cost ?? 0 ?></span>
                        公司总成本：<span style="color:green;"><?= $bill->total_cost ?? 0?></span>
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
                    <span style="color:red;">（Ctrl+F键可快速查找字段名）</span>
                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        //'tableOptions' => ['class' => 'table table-hover'],
                        'options' => ['style' => 'white-space:nowrap;font-size:12px;'],
                        'rowOptions' => function ($model, $key, $index) {
                            if ($index % 2 === 0) {
                                return ['style' => 'background:#fffef9;'];
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
                                    'style' => 'width:60px;'
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
                                    'style' => 'width:60px;'
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
                                    return \common\enums\ConfirmEnum::getValue($model->auto_goods_id);
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
                                'headerOptions' => ['id' => 'batch_copy_goods_id', 'class' => 'col-md-1', 'style' => 'background-color:#feeeed;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#feeeed;'],
                                'value' => function ($model, $key, $index, $widget) use ($bill) {
                                    $widget->footer = $model->getAttributeLabel('goods_id');
                                    if ($model->goods_id) {
                                        //if($bill->bill_status == BillStatusEnum::CONFIRM){
                                        //    $model->goods_id = Html::a($model->goods_id, ['view', 'goods_id' => $model->goods_id, 'returnUrl' => Url::getReturnUrl()], ['class' => 'openContab', 'style' => "text-decoration:underline;color:#3c8dbc", 'id' => $model->goods_id]) . ' <i class="fa fa-copy" onclick="copy(\'' . $model->goods_id . '\')"></i>';
                                        //}else{
                                        $model->goods_id = '<span id="goods_' . $model->goods_id . '">' . $model->goods_id . '</span> <i class="fa fa-copy" onclick="copy(\'goods_' . $model->goods_id . '\')"></i>';
                                        //}

                                    }
                                    return $model->goods_id ?? "";
                                },
                                'filter' => Html::activeTextInput($searchModel, 'goods_id', [
                                    'class' => 'form-control',
                                    'style' => 'width:100px;'
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
                                        return Html::a($model->style_sn, ['/style/style/view', 'id' => $model->id, 'returnUrl' => Url::getReturnUrl()], ['style' => "text-decoration:underline;color:#3c8dbc", 'id' => $model->style_sn . '_' . $model->id]) . ' <i class="fa fa-copy" onclick="copy(\'' . $model->style_sn . '_' . $model->id . '\')"></i>';
                                    } else {
                                        if ($model->style_sn) {
                                            return "<span id='{$model->style_sn}_{$model->id}'>" . $model->style_sn . "</span>" . ' <i class="fa fa-copy" onclick="copy(\'' . $model->style_sn . '_' . $model->id . '\')"></i>';
                                        }
                                        return $model->style_sn ?? "";
                                    }
                                },
                                'filter' => Html::activeTextInput($searchModel, 'style_sn', [
                                    'class' => 'form-control',
                                    'style' => 'width:100px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'style_sn'),
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
                                    'style' => 'width:90px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'goods_name'),
                            ],
                            [
                                'attribute' => 'to_warehouse_id',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#feeeed;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#feeeed;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('to_warehouse_id');
                                    return $model->toWarehouse->name ?? "";
                                },
                                //'value' => "toWarehouse.name",
                                'filter' => Select2::widget([
                                    'name' => 'SearchModel[to_warehouse_id]',
                                    'value' => $searchModel->to_warehouse_id,
                                    'data' => Yii::$app->warehouseService->warehouse::getDropDown(),
                                    'options' => ['placeholder' => "请选择"],
                                    'pluginOptions' => [
                                        'allowClear' => true,
                                    ],
                                ]),
                                'visible' => $model->isVisible($bill, 'to_warehouse_id'),
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
                                    'style' => 'width:60px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'material_type'),
                            ],
                            [
                                'attribute' => 'goods_num',
                                'headerOptions' => ['style' => 'background-color:#feeeed;'],
                                'footerOptions' => ['style' => 'background-color:#feeeed;'],
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
                                'attribute' => 'finger_hk',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#feeeed;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#feeeed;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('finger_hk');
                                    return Yii::$app->attr->valueName($model->finger_hk) ?? "";
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'finger_hk', $model->getFingerHkMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:60px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'finger_hk'),
                            ],
                            [
                                'attribute' => 'finger',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#feeeed;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#feeeed;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('finger');
                                    return Yii::$app->attr->valueName($model->finger) ?? "";
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'finger', $model->getFingerMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:60px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'finger'),
                            ],
                            [
                                'attribute' => 'xiangkou',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#feeeed;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#feeeed;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('xiangkou');
                                    return Yii::$app->attr->valueName($model->xiangkou) ?? "";
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'xiangkou', $model->getXiangkouMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:60px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'xiangkou'),
                            ],
                            [
                                'attribute' => 'length',
                                //'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#feeeed;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#feeeed;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('length');
                                    return $model->length ?? "";
                                },
                                'filter' => Html::activeTextInput($searchModel, 'length', [
                                    'class' => 'form-control',
                                    'style' => 'width:60px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'length'),
                            ],
                            [
                                'attribute' => 'kezi',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#feeeed;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#feeeed;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('kezi');
                                    return $model->kezi ?? "";
                                },
                                'filter' => Html::activeTextInput($searchModel, 'kezi', [
                                    'class' => 'form-control',
                                    'style' => 'width:40px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'kezi'),
                            ],
//                            [
//                                'attribute' => 'chain_type',
//                                'format' => 'raw',
//                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#feeeed;'],
//                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#feeeed;'],
//                                'value' => function ($model, $key, $index, $widget) {
//                                    $widget->footer = $model->getAttributeLabel('chain_type');
//                                    return Yii::$app->attr->valueName($model->chain_type) ?? "";
//                                },
//                                'filter' => Html::activeDropDownList($searchModel, 'chain_type', $model->getChainTypeMap(), [
//                                    'prompt' => '全部',
//                                    'class' => 'form-control',
//                                    'style' => 'width:60px;'
//                                ]),
//                                'visible' => $model->isVisible($bill, 'chain_type'),
//                            ],
//                            [
//                                'attribute' => 'chain_long',
//                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#afdfe4'],
//                                'filter' => Html::activeTextInput($searchModel, 'chain_long', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:100px;'
//                                ]),
//                            ],
//                            [
//                                'attribute' => 'cramp_ring',
//                                'format' => 'raw',
//                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#feeeed;'],
//                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#feeeed;'],
//                                'value' => function ($model, $key, $index, $widget) {
//                                    $widget->footer = $model->getAttributeLabel('cramp_ring');
//                                    return Yii::$app->attr->valueName($model->cramp_ring) ?? "";
//                                },
//                                'filter' => Html::activeDropDownList($searchModel, 'cramp_ring', $model->getCrampRingMap(), [
//                                    'prompt' => '全部',
//                                    'class' => 'form-control',
//                                    'style' => 'width:60px;'
//                                ]),
//                                'visible' => $model->isVisible($bill, 'cramp_ring'),
//                            ],
                            [
                                'attribute' => 'talon_head_type',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#feeeed;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#feeeed;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('talon_head_type');
                                    return Yii::$app->attr->valueName($model->talon_head_type) ?? "";
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'talon_head_type', $model->getTalonHeadTypeMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:60px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'talon_head_type'),
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
                                    'style' => 'width:60px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'peiliao_way'),
                            ],
                            [
                                'attribute' => 'suttle_weight',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#FFD700;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#FFD700;'],
                                'value' => function ($model, $key, $index, $widget) use ($total) {
                                    $widget->footer = $model->getFooterValues('suttle_weight', $total, "0.000");
                                    return round($model->suttle_weight, 2) ?? "0.000";
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
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#FFD700;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#FFD700;'],
                                'value' => function ($model, $key, $index, $widget) use ($total) {
                                    $widget->footer = $model->getFooterValues('gold_weight', $total, "0.000");
                                    return round($model->gold_weight, 2) ?? "0.000";
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
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#FFD700;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#FFD700;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('gold_loss');
                                    return round($model->gold_loss, 2) ?? "0";
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
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#FFD700;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#FFD700;'],
                                'value' => function ($model, $key, $index, $widget) use ($total) {
                                    $widget->footer = $model->getFooterValues('lncl_loss_weight', $total, "0.000");
                                    return round($model->lncl_loss_weight, 2) ?? "0.000";
                                },
//                                'filter' => Html::activeTextInput($searchModel, 'lncl_loss_weight', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                                'visible' => $model->isVisible($bill, 'lncl_loss_weight'),
                            ],
                            [
                                'attribute' => 'gold_price',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#FFD700;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#FFD700;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('gold_price');
                                    return round($model->gold_price, 2) ?? "";
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
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#FFD700;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#FFD700;'],
                                'value' => function ($model, $key, $index, $widget) use ($total) {
                                    $widget->footer = $model->getFooterValues('gold_amount', $total, "0.000");
                                    return round($model->gold_amount, 2) ?? "0.000";
                                },
//                                'filter' => Html::activeTextInput($searchModel, 'gold_amount', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                                'visible' => $model->isVisible($bill, 'gold_amount'),
                            ],
                            [
                                'attribute' => 'pure_gold_rate',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#FFD700;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#FFD700;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('pure_gold_rate');
                                    return round($model->pure_gold_rate, 2) ?? "0";
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
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#FFD700;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#FFD700;'],
                                'value' => function ($model, $key, $index, $widget) use ($total) {
                                    $widget->footer = $model->getFooterValues('pure_gold', $total, "0.000");
                                    return round($model->pure_gold, 2) ?? "0.000";
                                },
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'pure_gold', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                                'visible' => $model->isVisible($bill, 'pure_gold'),
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
                                    //'style' => 'width:80px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'main_pei_type'),
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
                                    //'style' => 'width:60px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'main_stone_sn'),
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
                                    //'style' => 'width:60px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'main_stone_type'),
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
                                'visible' => $model->isVisible($bill, 'main_stone_num'),
                            ],
                            [
                                'attribute' => 'main_stone_weight',
                                //'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#afb4db;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#afb4db;'],
                                'value' => function ($model, $key, $index, $widget) use ($total) {
                                    $widget->footer = $model->getFooterValues('main_stone_weight', $total, "0.000", 3);
                                    return round($model->main_stone_weight, 3) ?? "0.000";
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
                                //'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#afb4db;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#afb4db;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('main_stone_price');
                                    return round($model->main_stone_price, 2) ?? "0.00";
                                },
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'main_stone_price', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                                'visible' => $model->isVisible($bill, 'main_stone_price'),
                            ],
                            [
                                'attribute' => 'main_stone_amount',
                                //'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#afb4db;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#afb4db;'],
                                'value' => function ($model, $key, $index, $widget) use ($total) {
                                    $widget->footer = $model->getFooterValues('main_stone_amount', $total, "0.000");
                                    return round($model->main_stone_amount, 2) ?? "0.00";
                                },
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'main_stone_amount', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                                'visible' => $model->isVisible($bill, 'main_stone_amount'),
                            ],
                            [
                                'attribute' => 'main_stone_shape',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#afb4db;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#afb4db;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('main_stone_shape');
                                    return Yii::$app->attr->valueName($model->main_stone_shape) ?? "";
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'main_stone_shape', $model->getMainStoneShapeMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    //'style' => 'width:60px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'main_stone_shape'),
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
                                    //'style' => 'width:60px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'main_stone_color'),
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
                                    //'style' => 'width:60px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'main_stone_clarity'),
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
                                    //'style' => 'width:60px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'main_stone_cut'),
                            ],
                            [
                                'attribute' => 'main_stone_polish',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#afb4db;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#afb4db;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('main_stone_polish');
                                    return Yii::$app->attr->valueName($model->main_stone_polish) ?? "";
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'main_stone_polish', $model->getMainStonePolishMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    //'style' => 'width:60px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'main_stone_polish'),
                            ],
                            [
                                'attribute' => 'main_stone_symmetry',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#afb4db;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#afb4db;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('main_stone_symmetry');
                                    return Yii::$app->attr->valueName($model->main_stone_symmetry) ?? "";
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'main_stone_symmetry', $model->getMainStoneSymmetryMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    //'style' => 'width:60px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'main_stone_symmetry'),
                            ],
                            [
                                'attribute' => 'main_stone_fluorescence',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#afb4db;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#afb4db;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('main_stone_fluorescence');
                                    return Yii::$app->attr->valueName($model->main_stone_fluorescence) ?? "";
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'main_stone_fluorescence', $model->getMainStoneFluorescenceMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    //'style' => 'width:60px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'main_stone_fluorescence'),
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
                                    //'style' => 'width:60px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'main_stone_colour'),
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
                                    //'style' => 'width:60px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'main_cert_id'),
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
                                    //'style' => 'width:80px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'main_cert_type'),
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
                                    //'style' => 'width:80px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'second_pei_type'),
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
                                    //'style' => 'width:60px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'second_stone_type1'),
                            ],
                            [
                                'attribute' => 'second_stone_sn1',
                                //'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#dec674;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#dec674;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('second_stone_sn1');
                                    return $model->second_stone_sn1 ?? "";
                                },
                                'filter' => Html::activeTextInput($searchModel, 'second_stone_sn1', [
                                    'class' => 'form-control',
                                    //'style' => 'width:60px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'second_stone_sn1'),
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
                                'visible' => $model->isVisible($bill, 'second_stone_num1'),
                            ],
                            [
                                'attribute' => 'second_stone_weight1',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#dec674;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#dec674;'],
                                'value' => function ($model, $key, $index, $widget) use ($total) {
                                    $widget->footer = $model->getFooterValues('second_stone_weight1', $total, "0.000", 3);
                                    return round($model->second_stone_weight1, 3) ?? "0.000";
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
                                //'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#dec674;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#dec674;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('second_stone_price1');
                                    return round($model->second_stone_price1, 2) ?? "0.00";
                                },
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'second_stone_price1', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                                'visible' => $model->isVisible($bill, 'second_stone_price1'),
                            ],
                            [
                                'attribute' => 'second_stone_amount1',
                                //'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#dec674;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#dec674;'],
                                'value' => function ($model, $key, $index, $widget) use ($total) {
                                    $widget->footer = $model->getFooterValues('second_stone_amount1', $total, "0.00");
                                    return round($model->second_stone_amount1, 2) ?? "0.00";
                                },
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'second_stone_amount1', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                                'visible' => $model->isVisible($bill, 'second_stone_amount1'),
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
                                    //'style' => 'width:60px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'second_stone_shape1'),
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
                                    //'style' => 'width:60px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'second_stone_color1'),
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
                                    //'style' => 'width:60px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'second_stone_clarity1'),
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
                                    //'style' => 'width:60px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'second_stone_cut1'),
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
                                    //'style' => 'width:60px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'second_stone_colour1'),
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
                                    //'style' => 'width:80px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'second_pei_type2'),
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
                                    //'style' => 'width:60px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'second_stone_type2'),
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
                                    //'style' => 'width:60px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'second_stone_sn2'),
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
                                'visible' => $model->isVisible($bill, 'second_stone_num2'),
                            ],
                            [
                                'attribute' => 'second_stone_weight2',
                                //'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#84bf96;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#84bf96;'],
                                'value' => function ($model, $key, $index, $widget) use ($total) {
                                    $widget->footer = $model->getFooterValues('second_stone_weight2', $total, "0.000", 3);
                                    return round($model->second_stone_weight2, 3) ?? "0.000";
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
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#84bf96;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#84bf96;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('second_stone_color2');
                                    return Yii::$app->attr->valueName($model->second_stone_color2) ?? "";
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'second_stone_color2', $model->getSecondStoneColor2Map(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    //'style' => 'width:60px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'second_stone_color2'),
                            ],
                            [
                                'attribute' => 'second_stone_clarity2',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#84bf96;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#84bf96;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('second_stone_clarity2');
                                    return Yii::$app->attr->valueName($model->second_stone_clarity2) ?? "";
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'second_stone_clarity2', $model->getSecondStoneClarity2Map(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    //'style' => 'width:60px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'second_stone_clarity2'),
                            ],
                            [
                                'attribute' => 'second_stone_price2',
                                //'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#84bf96;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#84bf96;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('second_stone_price2');
                                    return round($model->second_stone_price2, 2) ?? "0.00";
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
                                //'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#84bf96;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#84bf96;'],
                                'value' => function ($model, $key, $index, $widget) use ($total) {
                                    $widget->footer = $model->getFooterValues('second_stone_amount2', $total, "0.00");
                                    return round($model->second_stone_amount2, 2) ?? "0.00";
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
                            ],*/
                            [
                                'attribute' => 'second_pei_type3',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#6495ED;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#6495ED;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('second_pei_type3');
                                    return \addons\Warehouse\common\enums\PeiShiWayEnum::getValue($model->second_pei_type3) ?? "";
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'second_pei_type3', $model->getPeiShiWayMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    //'style' => 'width:80px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'second_pei_type3'),
                            ],
                            [
                                'attribute' => 'second_stone_type3',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#6495ED;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#6495ED;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('second_stone_type3');
                                    return Yii::$app->attr->valueName($model->second_stone_type3) ?? "";
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'second_stone_type3', $model->getSecondStoneType3Map(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    //'style' => 'width:60px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'second_stone_type3'),
                            ],
                            [
                                'attribute' => 'second_stone_sn3',
                                //'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#6495ED;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#6495ED;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('second_stone_sn3');
                                    return $model->second_stone_sn3 ?? "";
                                },
                                'filter' => Html::activeTextInput($searchModel, 'second_stone_sn3', [
                                    'class' => 'form-control',
                                    //'style' => 'width:60px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'second_stone_sn3'),
                            ],
                            [
                                'attribute' => 'second_stone_num3',
                                //'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#6495ED;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#6495ED;'],
                                'value' => function ($model, $key, $index, $widget) use ($total) {
                                    $widget->footer = $model->getFooterValues('second_stone_num3', $total);
                                    return $model->second_stone_num3 ?? 0;
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
                                //'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#6495ED;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#6495ED;'],
                                'value' => function ($model, $key, $index, $widget) use ($total) {
                                    $widget->footer = $model->getFooterValues('second_stone_weight3', $total, "0.000", 3);
                                    return round($model->second_stone_weight3, 3) ?? "0.000";
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
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#6495ED;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#6495ED;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('second_stone_color3');
                                    return Yii::$app->attr->valueName($model->second_stone_color3) ?? "";
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'second_stone_color3', $model->getSecondStoneColor3Map(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    //'style' => 'width:60px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'second_stone_color3'),
                            ],
                            [
                                'attribute' => 'second_stone_clarity3',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#6495ED;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#6495ED;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('second_stone_clarity3');
                                    return Yii::$app->attr->valueName($model->second_stone_clarity3) ?? "";
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'second_stone_clarity3', $model->getSecondStoneClarity3Map(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    //'style' => 'width:60px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'second_stone_clarity3'),
                            ],
                            [
                                'attribute' => 'second_stone_price3',
                                //'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#6495ED;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#6495ED;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('second_stone_price3');
                                    return round($model->second_stone_price3, 2) ?? "0.00";
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
                                //'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#6495ED;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#6495ED;'],
                                'value' => function ($model, $key, $index, $widget) use ($total) {
                                    $widget->footer = $model->getFooterValues('second_stone_amount3', $total, "0.00");
                                    return round($model->second_stone_amount3, 2) ?? "0.00";
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
                                //'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#6495ED;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#6495ED;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('stone_remark');
                                    return $model->stone_remark ?? "";
                                },
                                'filter' => Html::activeTextInput($searchModel, 'stone_remark', [
                                    'class' => 'form-control',
                                    //'style' => 'width:160px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'stone_remark'),
                            ],
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
                                    //'style' => 'width:60px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'parts_way'),
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
                                    //'style' => 'width:60px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'parts_type'),
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
                                    //'style' => 'width:60px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'parts_material'),
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
                                'visible' => $model->isVisible($bill, 'parts_num'),
                            ],
                            [
                                'attribute' => 'parts_gold_weight',
                                //'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#cde6c7;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#cde6c7;'],
                                'value' => function ($model, $key, $index, $widget) use ($total) {
                                    $widget->footer = $model->getFooterValues('parts_gold_weight', $total, "0.000");
                                    return round($model->parts_gold_weight, 2) ?? "0.000";
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
                                //'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#cde6c7;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#cde6c7;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('parts_price');
                                    return round($model->parts_price, 2) ?? "0.00";
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
                                //'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#cde6c7;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#cde6c7;'],
                                'value' => function ($model, $key, $index, $widget) use ($total) {
                                    $widget->footer = $model->getFooterValues('parts_amount', $total, "0.00");
                                    return round($model->parts_amount, 2) ?? "0.00";
                                },
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'parts_amount', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                                'visible' => $model->isVisible($bill, 'parts_amount'),
                            ],
                            [
                                'attribute' => 'gong_fee',
                                //'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('gong_fee');
                                    return round($model->gong_fee, 2) ?? "0.00";
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
                                //'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('piece_fee');
                                    return round($model->piece_fee, 2) ?? "0.00";
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
                                //'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
                                'value' => function ($model, $key, $index, $widget) use ($total) {
                                    $widget->footer = $model->getFooterValues('basic_gong_fee', $total, "0.00");
                                    return round($model->basic_gong_fee, 2) ?? "0.00";
                                },
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'basic_gong_fee', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                                'visible' => $model->isVisible($bill, 'basic_gong_fee'),
                            ],
                            [
                                'attribute' => 'peishi_weight',
                                //'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
                                'value' => function ($model, $key, $index, $widget) use ($total) {
                                    $widget->footer = $model->getFooterValues('peishi_weight', $total, "0.000", 3);
                                    return round($model->peishi_weight, 3) ?? "0.000";
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
                                //'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
                                'value' => function ($model, $key, $index, $widget) use ($total) {
                                    $widget->footer = $model->getFooterValues('peishi_gong_fee', $total, "0.00");
                                    return round($model->peishi_gong_fee, 2) ?? "0.00";
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
                                //'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
                                'value' => function ($model, $key, $index, $widget) use ($total) {
                                    $widget->footer = $model->getFooterValues('peishi_fee', $total, "0.00");
                                    return round($model->peishi_fee, 2) ?? "0.00";
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
                                //'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
                                'value' => function ($model, $key, $index, $widget) use ($total) {
                                    $widget->footer = $model->getFooterValues('parts_fee', $total, "0.00");
                                    return round($model->parts_fee, 2) ?? "0.000";
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
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('xiangqian_craft');
                                    return Yii::$app->attr->valueName($model->xiangqian_craft) ?? "";
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'xiangqian_craft', $model->getXiangqianCraftMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    //'style' => 'width:60px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'xiangqian_craft'),
                            ],
//                            [
//                                'attribute' => 'xianqian_price',
//                                //'format' => 'raw',
//                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
//                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
//                                'value' => function ($model, $key, $index, $widget) {
//                                    $widget->footer = $model->getAttributeLabel('xianqian_price');
//                                    return $model->xianqian_price ?? "0.00";
//                                },
//                                'filter' => false,
////                                'filter' => Html::activeTextInput($searchModel, xianqian_price, [
////                                    'class' => 'form-control',
////                                    'style' => 'width:80px;'
////                                ]),
//                            ],
                            [
                                'attribute' => 'second_stone_fee1',
                                //'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('second_stone_fee1');
                                    return round($model->second_stone_fee1, 2) ?? "0.00";
                                },
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, second_stone_fee1, [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                                'visible' => $model->isVisible($bill, 'second_stone_fee1'),
                            ],
                            [
                                'attribute' => 'second_stone_fee2',
                                //'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('second_stone_fee2');
                                    return round($model->second_stone_fee2, 2) ?? "0.00";
                                },
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, second_stone_fee2, [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                                'visible' => $model->isVisible($bill, 'second_stone_fee2'),
                            ],
                            [
                                'attribute' => 'second_stone_fee3',
                                //'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('second_stone_fee3');
                                    return round($model->second_stone_fee3, 2) ?? "0.00";
                                },
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, second_stone_fee3, [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                                'visible' => $model->isVisible($bill, 'second_stone_fee3'),
                            ],
                            [
                                'attribute' => 'xianqian_fee',
                                //'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
                                'value' => function ($model, $key, $index, $widget) use ($total) {
                                    $widget->footer = $model->getFooterValues('xianqian_fee', $total, "0.00");
                                    return round($model->xianqian_fee, 2) ?? "0.00";
                                },
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'xianqian_fee', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                                'visible' => $model->isVisible($bill, 'xianqian_fee'),
                            ],
                            [
                                'attribute' => 'biaomiangongyi',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('biaomiangongyi');
                                    if (!empty($model->biaomiangongyi)) {
                                        $biaomiangongyi = explode(',', $model->biaomiangongyi);
                                        $biaomiangongyi = array_filter($biaomiangongyi);
                                        $arr = [];
                                        foreach ($biaomiangongyi as $item) {
                                            $arr[] = \Yii::$app->attr->valueName($item);
                                        }
                                        return implode(",", $arr) ?? "";
                                    }
                                    return "";
                                },
                                'filter' => false,
//                                'filter' => Html::activeDropDownList($searchModel, 'biaomiangongyi', $model->getFaceCraftMap(), [
//                                    'prompt' => '全部',
//                                    'class' => 'form-control',
//                                    'style' => 'width:100px;'
//                                ]),
                                'visible' => $model->isVisible($bill, 'biaomiangongyi'),
                            ],
                            [
                                'attribute' => 'biaomiangongyi_fee',
                                //'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
                                'value' => function ($model, $key, $index, $widget) use ($total) {
                                    $widget->footer = $model->getFooterValues('biaomiangongyi_fee', $total, "0.00");
                                    return round($model->biaomiangongyi_fee, 2) ?? "0.00";
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
                                //'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
                                'value' => function ($model, $key, $index, $widget) use ($total) {
                                    $widget->footer = $model->getFooterValues('fense_fee', $total, "0.00");
                                    return round($model->fense_fee, 2) ?? "0.00";
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
                                //'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
                                'value' => function ($model, $key, $index, $widget) use ($total) {
                                    $widget->footer = $model->getFooterValues('penlasha_fee', $total, "0.00");
                                    return round($model->penlasha_fee, 2) ?? "0.00";
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
                                //'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
                                'value' => function ($model, $key, $index, $widget) use ($total) {
                                    $widget->footer = $model->getFooterValues('lasha_fee', $total, "0.00");
                                    return round($model->lasha_fee, 2) ?? "0.00";
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
                                //'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
                                'value' => function ($model, $key, $index, $widget) use ($total) {
                                    $widget->footer = $model->getFooterValues('bukou_fee', $total, "0.00");
                                    return round($model->bukou_fee, 2) ?? "0.00";
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
                                //'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
                                'value' => function ($model, $key, $index, $widget) use ($total) {
                                    $widget->footer = $model->getFooterValues('templet_fee', $total, "0.00");
                                    return round($model->templet_fee, 2) ?? "0.00";
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
                                //'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('tax_fee');
                                    return round($model->tax_fee, 2) ?? "0.00";
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
                                //'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
                                'value' => function ($model, $key, $index, $widget) use ($total) {
                                    $widget->footer = $model->getFooterValues('tax_amount', $total, "0.00");
                                    return round($model->tax_amount, 2) ?? "0.00";
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
                                //'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
                                'value' => function ($model, $key, $index, $widget) use ($total) {
                                    $widget->footer = $model->getFooterValues('cert_fee', $total, "0.00");
                                    return round($model->cert_fee, 2) ?? "0.00";
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
                                //'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
                                'value' => function ($model, $key, $index, $widget) use ($total) {
                                    $widget->footer = $model->getFooterValues('other_fee', $total, "0.00");
                                    return round($model->other_fee, 2) ?? "0.00";
                                },
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'other_fee', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                                'visible' => $model->isVisible($bill, 'other_fee'),
                            ],
                            [
                                'attribute' => 'pure_gold',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#b7ba6b;'],
                                'value' => function ($model, $key, $index, $widget) use ($total) {
                                    $widget->footer = $model->getFooterValues('pure_gold', $total, "0.000");
                                    return round($model->pure_gold, 2) ?? "0.000";
                                },
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'pure_gold', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                                'visible' => $model->isVisible($bill, 'pure_gold'),
                            ],
                            [
                                'attribute' => 'factory_cost',
                                //'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#9b95c9;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#9b95c9;'],
                                'value' => function ($model, $key, $index, $widget) use ($total) {
                                    $widget->footer = $model->getFooterValues('factory_cost', $total, "0.00");
                                    return round($model->factory_cost, 2) ?? "0.00";
                                },
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'factory_cost', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:100px;'
//                                ]),
                                'visible' => $model->isVisible($bill, 'factory_cost'),
                            ],
                            [
                                'attribute' => 'factory_gold_weight',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#9b95c9;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#9b95c9;'],
                                'value' => function ($model, $key, $index, $widget) use ($total) {
                                    $widget->footer = $model->getFooterValues('factory_gold_weight', $total, "0.000");
                                    return round($model->factory_gold_weight, 2) ?? "0.000";
                                },
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'pure_gold', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                                'visible' => $model->isVisible($bill, 'factory_gold_weight'),
                            ],
                            [
                                'label' => '成本手填',
                                'attribute' => 'is_auto_price',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#9b95c9;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#9b95c9;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = "成本手填";
                                    return \common\enums\ConfirmEnum::getValue($model->is_auto_price);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'is_auto_price', \common\enums\ConfirmEnum::getMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    //'style' => 'width:60px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'is_auto_price'),
                            ],
                            [
                                'attribute' => 'cost_price',
                                //'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#9b95c9;'],
                                'contentOptions' => ['style' => 'color:red'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#9b95c9;'],
                                'value' => function ($model, $key, $index, $widget) use ($total) {
                                    $widget->footer = $model->getFooterValues('cost_price', $total, "0.00");
                                    return round($model->cost_price, 2) ?? "0.00";
                                },
                                'visible' => \common\helpers\Auth::verify(\common\enums\SpecialAuthEnum::VIEW_CAIGOU_PRICE),
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'cost_price', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:100px;'
//                                ]),
                            ],
                            [
                                'attribute' => 'cost_amount',
                                //'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#9b95c9;'],
                                'contentOptions' => ['style' => 'color:red'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#9b95c9;'],
                                'value' => function ($model, $key, $index, $widget) use ($total) {
                                    $widget->footer = $model->getFooterValues('cost_amount', $total, "0.00");
                                    return round($model->cost_amount, 2) ?? "0.00";
                                },
                                'visible' => \common\helpers\Auth::verify(\common\enums\SpecialAuthEnum::VIEW_CAIGOU_PRICE),
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'cost_amount', [
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
                                    return round($model->markup_rate, 2) ?? "";
                                },
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'markup_rate', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:80px;'
//                                ]),
                                'visible' => $model->isVisible($bill, 'markup_rate'),
                            ],
                            [
                                'attribute' => 'market_price',
                                //'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#9b95c9;'],
                                'contentOptions' => ['style' => 'color:green'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#9b95c9;'],
                                'value' => function ($model, $key, $index, $widget) use ($total) {
                                    $widget->footer = $model->getFooterValues('market_price', $total, "0.00");
                                    return round($model->market_price, 2) ?? "0.00";
                                },
                                'filter' => false,
//                                'filter' => Html::activeTextInput($searchModel, 'market_price', [
//                                    'class' => 'form-control',
//                                    'style' => 'width:100px;'
//                                ]),
                                'visible' => $model->isVisible($bill, 'market_price'),
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
                                    //'style' => 'width:60px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'style_sex'),
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
                                    //'style' => 'width:60px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'jintuo_type'),
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
                                    //'style' => 'width:60px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'qiban_type'),
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
                                    //'style' => 'width:60px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'is_inlay'),
                            ],
                            [
                                'attribute' => 'pay_status',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#9b95c9;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#9b95c9;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('pay_status');
                                    return \addons\Warehouse\common\enums\IsSettleAccountsEnum::getValue($model->pay_status) ?? "";
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'pay_status', $model->getPayStatusMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    //'style' => 'width:60px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'pay_status'),
                            ],
                            [
                                'label' => '是否多件',
                                'attribute' => 'is_wholesale',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#9b95c9;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#9b95c9;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = "是否批发";
                                    return \addons\Warehouse\common\enums\IsWholeSaleEnum::getValue($model->is_wholesale);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'is_wholesale', \addons\Warehouse\common\enums\IsWholeSaleEnum::getMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    //'style' => 'width:60px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'is_wholesale'),
                            ],
                            [
                                'attribute' => 'material_color',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#9b95c9;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#9b95c9;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('material_color');
                                    return Yii::$app->attr->valueName($model->material_color) ?? "";
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'material_color', $model->getMaterialColorMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    //'style' => 'width:60px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'material_color'),
                            ],
                            [
                                'attribute' => 'product_size',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#9b95c9;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#9b95c9;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('product_size');
                                    return $model->product_size ?? "";
                                },
                                'filter' => Html::activeTextInput($searchModel, 'product_size', [
                                    'class' => 'form-control',
                                    'style' => 'width:60px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'product_size'),
                            ],
                            [
                                'attribute' => 'qiban_sn',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#9b95c9;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#9b95c9;'],
                                'value' => function ($model, $key, $index, $widget) {
                                    $widget->footer = $model->getAttributeLabel('qiban_sn');
                                    return $model->qiban_sn ?? "";
                                },
                                'filter' => Html::activeTextInput($searchModel, 'qiban_sn', [
                                    'class' => 'form-control',
                                    //'style' => 'width:100px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'qiban_sn'),
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
                                    //'style' => 'width:60px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'factory_mo'),
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
                                    //'style' => 'width:60px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'order_sn'),
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
                                    //'style' => 'width:160px;'
                                ]),
                                'visible' => $model->isVisible($bill, 'remark'),
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
        //默认全选
        $("input[name='id[]']").trigger("click");

        //批量复制货号
        var button = '<span id="goods_ids" style="position: absolute; left: -1000000000px;"><?= $goods_ids ?></span><div class="btn btn-default btn-xs" onclick="copy(\'goods_ids\')"><i class="fa fa-copy"></i></div>';
        $("#batch_copy_goods_id > a").after(button);
    });

    function batchExport() {
        appConfirm("确定要导出明细吗?", '', function (code) {
            if (code !== "defeat") {
                return;
            }
            window.location.href = "<?= \common\helpers\Url::buildUrl('../bill-t/export', [], ['ids'])?>?ids=<?php echo $bill->id ?>";
        });
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
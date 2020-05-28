<?php

use common\helpers\Html;
use common\helpers\Url;
use kartik\select2\Select2;
use yii\grid\GridView;
use kartik\daterange\DateRangePicker;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('purchase_receipt_goods', '采购收货单详情');
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box-body nav-tabs-custom">
    <h2 class="page-header"><?php echo $this->title; ?> - <?php echo $receipt->receipt_no?></h2>
    <?php echo Html::menuTab($tabList,$tab)?>
    <div class="tab-content">
        <div class="col-xs-12" style="padding-left: 0px;padding-right: 0px;">
            <div class="box">
                <div class="box-body table-responsive">
                    <?php echo Html::batchButtons(false)?>
                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'tableOptions' => ['class' => 'table table-hover'],
                        'options' => ['style'=>'width:200%;'],
                        'showFooter' => false,//显示footer行
                        'id'=>'grid',
                        'columns' => [
                            [
                                'class' => 'yii\grid\SerialColumn',
                                'visible' => false,
                            ],
                            [
                                'class'=>'yii\grid\CheckboxColumn',
                                'name'=>'id',  //设置每行数据的复选框属性

                            ],
                            [
                                'attribute'=>'id',
                                'headerOptions' => [],
                                'filter' => Html::activeTextInput($searchModel, 'id', [
                                    'class' => 'form-control',
                                ]),
                            ],
                            [
                                'attribute'=>'purchase_sn',
                                'headerOptions' => ['class' => 'col-md-1'],
                                'filter' => Html::activeTextInput($searchModel, 'purchase_sn', [
                                    'class' => 'form-control',
                                ]),
                            ],
                            [
                                'attribute'=>'produce_sn',
                                'headerOptions' => ['class' => 'col-md-1'],
                                'filter' => Html::activeTextInput($searchModel, 'produce_sn', [
                                    'class' => 'form-control',
                                ]),
                            ],
                            [
                                'attribute'=>'barcode',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1'],
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxUpdate('barcode', $model->barcode);
                                },
                            ],
                            [
                                'attribute'=>'goods_name',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1'],
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxUpdate('goods_name', $model->goods_name);
                                },
                            ],
                            [
                                'attribute'=>'goods_num',
                                'headerOptions' => [],
                                'filter' => Html::activeTextInput($searchModel, 'goods_num', [
                                    'class' => 'form-control',
                                ]),
                            ],
                            [
                                'attribute'=>'style_sn',
                                'headerOptions' => ['class' => 'col-md-1'],
                                'filter' => Html::activeTextInput($searchModel, 'style_sn', [
                                    'class' => 'form-control',
                                ]),
                            ],
                            [
                                'attribute'=>'factory_mo',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1'],
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxUpdate('factory_mo', $model->factory_mo);
                                },
                            ],
                            [
                                'label' => '款式分类',
                                'attribute' => 'cate.name',
                                'value' => "cate.name",
                                'filter' => Html::activeDropDownList($searchModel, 'style_cate_id', \Yii::$app->styleService->styleCate->getDropDown(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                ]),
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'label' => '产品线',
                                'attribute' => 'type.name',
                                'value' => "type.name",
                                'filter' => Html::activeDropDownList($searchModel, 'product_type_id',Yii::$app->styleService->productType->getDropDown(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                ]),
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'attribute'=>'finger',
                                'headerOptions' => ['class' => 'col-md-1'],
                                'filter' => Html::activeTextInput($searchModel, 'finger', [
                                    'class' => 'form-control',
                                ]),
                            ],
                            [
                                'attribute'=>'xiangkou',
                                'headerOptions' => ['class' => 'col-md-1'],
                                'filter' => Html::activeTextInput($searchModel, 'xiangkou', [
                                    'class' => 'form-control',
                                ]),
                            ],
                            [
                                'label' => '主成色',
                                'attribute' => 'material',
                                'value' => "material",
                                'filter' => Html::activeDropDownList($searchModel, 'material',\Yii::$app->attr->valueMap(\addons\Purchase\common\enums\ReceiptGoodsAttrEnum::MATERIAL), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                ]),
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'attribute'=>'gold_weight',
                                'headerOptions' => ['class' => 'col-md-1'],
                                'filter' => Html::activeTextInput($searchModel, 'xiangkou', [
                                    'class' => 'form-control',
                                ]),
                            ],
                            [
                                'attribute'=>'gold_price',
                                'headerOptions' => ['class' => 'col-md-1'],
                                'filter' => Html::activeTextInput($searchModel, 'gold_price', [
                                    'class' => 'form-control',
                                ]),
                            ],
                            [
                                'attribute'=>'gold_loss',
                                'headerOptions' => ['class' => 'col-md-1'],
                                'filter' => Html::activeTextInput($searchModel, 'gold_loss', [
                                    'class' => 'form-control',
                                ]),
                            ],
                            [
                                'label' => '金托类型',
                                'attribute' => 'jintuo_type',
                                'value' => "jintuo_type",
                                'filter' => Html::activeDropDownList($searchModel, 'jintuo_type',\addons\Style\common\enums\JintuoTypeEnum::getMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                ]),
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'attribute'=>'gross_weight',
                                'headerOptions' => ['class' => 'col-md-1'],
                                'filter' => Html::activeTextInput($searchModel, 'gross_weight', [
                                    'class' => 'form-control',
                                ]),
                            ],
                            [
                                'attribute'=>'suttle_weight',
                                'headerOptions' => ['class' => 'col-md-1'],
                                'filter' => Html::activeTextInput($searchModel, 'suttle_weight', [
                                    'class' => 'form-control',
                                ]),
                            ],
                            [
                                'attribute'=>'cost_price',
                                'headerOptions' => ['class' => 'col-md-1'],
                                'filter' => Html::activeTextInput($searchModel, 'cost_price', [
                                    'class' => 'form-control',
                                ]),
                            ],
                            [
                                'attribute'=>'market_price',
                                'headerOptions' => ['class' => 'col-md-1'],
                                'filter' => Html::activeTextInput($searchModel, 'market_price', [
                                    'class' => 'form-control',
                                ]),
                            ],
                            [
                                'attribute'=>'sale_price',
                                'headerOptions' => ['class' => 'col-md-1'],
                                'filter' => Html::activeTextInput($searchModel, 'sale_price', [
                                    'class' => 'form-control',
                                ]),
                            ],
                            [
                                'attribute'=>'cert_id',
                                'headerOptions' => ['class' => 'col-md-1'],
                                'filter' => Html::activeTextInput($searchModel, 'cert_id', [
                                    'class' => 'form-control',
                                ]),
                            ],
                            [
                                'label' => '主石',
                                'attribute' => 'main_stone',
                                'value' => "main_stone",
                                'filter' => Html::activeDropDownList($searchModel, 'main_stone',\Yii::$app->attr->valueMap(\addons\Purchase\common\enums\ReceiptGoodsAttrEnum::MAIN_STONE), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                ]),
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'attribute'=>'main_stone_num',
                                'headerOptions' => ['class' => 'col-md-1'],
                                'filter' => Html::activeTextInput($searchModel, 'main_stone_num', [
                                    'class' => 'form-control',
                                ]),
                            ],
                            [
                                'attribute'=>'main_stone_weight',
                                'headerOptions' => ['class' => 'col-md-1'],
                                'filter' => Html::activeTextInput($searchModel, 'main_stone_weight', [
                                    'class' => 'form-control',
                                ]),
                            ],
                            [
                                'label' => '主石颜色',
                                'attribute' => 'main_stone_color',
                                'value' => "main_stone_color",
                                'filter' => Html::activeDropDownList($searchModel, 'main_stone_color',\Yii::$app->attr->valueMap(\addons\Purchase\common\enums\ReceiptGoodsAttrEnum::MAIN_STONE_COLOR), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                ]),
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'label' => '主石净度',
                                'attribute' => 'main_stone_clarity',
                                'value' => "main_stone_clarity",
                                'filter' => Html::activeDropDownList($searchModel, 'main_stone_clarity',\Yii::$app->attr->valueMap(\addons\Purchase\common\enums\ReceiptGoodsAttrEnum::MAIN_STONE_CLARITY), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                ]),
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'attribute'=>'main_stone_price',
                                'headerOptions' => ['class' => 'col-md-1'],
                                'filter' => Html::activeTextInput($searchModel, 'main_stone_price', [
                                    'class' => 'form-control',
                                ]),
                            ],
                            [
                                'label' => '副石1',
                                'attribute' => 'second_stone1',
                                'value' => "second_stone1",
                                'filter' => Html::activeDropDownList($searchModel, 'second_stone1',\Yii::$app->attr->valueMap(\addons\Purchase\common\enums\ReceiptGoodsAttrEnum::SECOND_STONE), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                ]),
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'attribute'=>'second_stone_weight1',
                                'headerOptions' => ['class' => 'col-md-1'],
                                'filter' => Html::activeTextInput($searchModel, 'second_stone_weight1', [
                                    'class' => 'form-control',
                                ]),
                            ],
                            [
                                'attribute'=>'second_stone_price1',
                                'headerOptions' => ['class' => 'col-md-1'],
                                'filter' => Html::activeTextInput($searchModel, 'second_stone_price1', [
                                    'class' => 'form-control',
                                ]),
                            ],
                            [
                                'label' => '副石2',
                                'attribute' => 'second_stone2',
                                'value' => "second_stone2",
                                'filter' => Html::activeDropDownList($searchModel, 'second_stone2',\Yii::$app->attr->valueMap(\addons\Purchase\common\enums\ReceiptGoodsAttrEnum::SECOND_STONE), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                ]),
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'attribute'=>'second_stone_weight2',
                                'headerOptions' => ['class' => 'col-md-1'],
                                'filter' => Html::activeTextInput($searchModel, 'second_stone_weight2', [
                                    'class' => 'form-control',
                                ]),
                            ],
                            [
                                'attribute'=>'second_stone_price2',
                                'headerOptions' => ['class' => 'col-md-1'],
                                'filter' => Html::activeTextInput($searchModel, 'second_stone_price2', [
                                    'class' => 'form-control',
                                ]),
                            ],
                            [
                                'attribute'=>'markup_rate',
                                'headerOptions' => ['class' => 'col-md-1'],
                                'filter' => Html::activeTextInput($searchModel, 'markup_rate', [
                                    'class' => 'form-control',
                                ]),
                            ],
                            [
                                'attribute'=>'gong_fee',
                                'headerOptions' => ['class' => 'col-md-1'],
                                'filter' => Html::activeTextInput($searchModel, 'gong_fee', [
                                    'class' => 'form-control',
                                ]),
                            ],
                            [
                                'attribute'=>'parts_weight',
                                'headerOptions' => ['class' => 'col-md-1'],
                                'filter' => Html::activeTextInput($searchModel, 'parts_weight', [
                                    'class' => 'form-control',
                                ]),
                            ],
                            [
                                'attribute'=>'parts_price',
                                'headerOptions' => ['class' => 'col-md-1'],
                                'filter' => Html::activeTextInput($searchModel, 'parts_price', [
                                    'class' => 'form-control',
                                ]),
                            ],
                            [
                                'attribute'=>'parts_fee',
                                'headerOptions' => ['class' => 'col-md-1'],
                                'filter' => Html::activeTextInput($searchModel, 'parts_fee', [
                                    'class' => 'form-control',
                                ]),
                            ],
                            [
                                'attribute'=>'xianqian_fee',
                                'headerOptions' => ['class' => 'col-md-1'],
                                'filter' => Html::activeTextInput($searchModel, 'xianqian_fee', [
                                    'class' => 'form-control',
                                ]),
                            ],
                            [
                                'label' => '表面工艺',
                                'attribute' => 'biaomiangongyi',
                                'value' => "biaomiangongyi",
                                'filter' => Html::activeDropDownList($searchModel, 'biaomiangongyi',\Yii::$app->attr->valueMap(\addons\Purchase\common\enums\ReceiptGoodsAttrEnum::BIAOMIANGONGYI), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                ]),
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'attribute'=>'biaomiangongyi_fee',
                                'headerOptions' => ['class' => 'col-md-1'],
                                'filter' => Html::activeTextInput($searchModel, 'biaomiangongyi_fee', [
                                    'class' => 'form-control',
                                ]),
                            ],
                            [
                                'attribute'=>'fense_fee',
                                'headerOptions' => ['class' => 'col-md-1'],
                                'filter' => Html::activeTextInput($searchModel, 'fense_fee', [
                                    'class' => 'form-control',
                                ]),
                            ],
                            [
                                'attribute'=>'bukou_fee',
                                'headerOptions' => ['class' => 'col-md-1'],
                                'filter' => Html::activeTextInput($searchModel, 'bukou_fee', [
                                    'class' => 'form-control',
                                ]),
                            ],
                            [
                                'attribute'=>'cert_fee',
                                'headerOptions' => ['class' => 'col-md-1'],
                                'filter' => Html::activeTextInput($searchModel, 'cert_fee', [
                                    'class' => 'form-control',
                                ]),
                            ],
                            [
                                'attribute'=>'extra_stone_fee',
                                'headerOptions' => ['class' => 'col-md-1'],
                                'filter' => Html::activeTextInput($searchModel, 'extra_stone_fee', [
                                    'class' => 'form-control',
                                ]),
                            ],
                            [
                                'attribute'=>'tax_fee',
                                'headerOptions' => ['class' => 'col-md-1'],
                                'filter' => Html::activeTextInput($searchModel, 'tax_fee', [
                                    'class' => 'form-control',
                                ]),
                            ],
                            [
                                'attribute'=>'other_fee',
                                'headerOptions' => ['class' => 'col-md-1'],
                                'filter' => Html::activeTextInput($searchModel, 'other_fee', [
                                    'class' => 'form-control',
                                ]),
                            ],
                            [
                                'attribute'=>'goods_remark',
                                'headerOptions' => ['class' => 'col-md-1'],
                                'filter' => Html::activeTextInput($searchModel, 'goods_remark', [
                                    'class' => 'form-control',
                                ]),
                            ],
                        ]
                    ]); ?>
                </div>
            </div>
        </div>
        <!-- box end -->
    </div>
    <!-- tab-content end -->
</div>
<?php


use addons\Purchase\common\enums\ReceiptStatusEnum;
use addons\Style\common\enums\AttrIdEnum;
use addons\Warehouse\common\enums\BillStatusEnum;
use common\helpers\Html;
use common\helpers\Url;
use yii\grid\GridView;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $searchModel yii\data\ActiveDataProvider */
/* @var $tabList yii\data\ActiveDataProvider */
/* @var $tab yii\data\ActiveDataProvider */
/* @var $bill yii\data\ActiveDataProvider */

$this->title = Yii::t('parts_bill_l_goods', '配件入库单明细');
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box-body nav-tabs-custom">
    <h2 class="page-header"><?= $this->title; ?> - <?= $bill->bill_no?> - <?= \addons\Warehouse\common\enums\PartsBillStatusEnum::getValue($bill->bill_status)?></h2>
    <?php echo Html::menuTab($tabList,$tab)?>
    <div class="box-tools" style="float:right;margin-top:-40px; margin-right: 20px;">
        <?php
        if($bill->bill_status == \addons\Warehouse\common\enums\PartsBillStatusEnum::SAVE) {
            echo Html::a('返回列表', ['parts-bill-l-goods/index', 'bill_id' => $bill->id], ['class' => 'btn btn-info btn-xs']);
        }
        ?>
    </div>
    <div class="tab-content">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-body table-responsive">
                    <?php echo Html::batchButtons(false)?>
                    <span class="summary" style="font-size:16px">
                        <!--<span style="font-weight:bold;">明细汇总：</span>-->
                        配件总数：<span style="color:green;"><?= $bill->total_num?></span>
                        配件总重：<span style="color:green;"><?= $bill->total_weight?>(g)</span>
                        配件总额：<span style="color:green;"><?= $bill->total_cost?></span>
                    </span>
                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'tableOptions' => ['class' => 'table table-hover'],
                        'options' => ['style'=>'white-space:nowrap;'],
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
                                'attribute'=>'parts_name',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxInput('parts_name', $model->parts_name, ['data-id'=>$model->id]);
                                },
                                'filter' => Html::activeTextInput($searchModel, 'parts_name', [
                                    'class' => 'form-control',
                                    'style'=> 'width:200px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-2'],
                            ],
                            [
                                'attribute' => 'parts_type',
                                'format' => 'raw',
                                'value' => function ($model){
                                    return Yii::$app->attr->valueName($model->parts_type);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'parts_type',Yii::$app->attr->valueMap(AttrIdEnum::MAT_PARTS_TYPE), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:100px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'attribute' => 'material_type',
                                'value' => function ($model){
                                    return Yii::$app->attr->valueName($model->material_type);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'material_type',Yii::$app->attr->valueMap(AttrIdEnum::MATERIAL_TYPE), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:100px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'attribute'=>'parts_sn',
                                'filter' => Html::activeTextInput($searchModel, 'parts_sn', [
                                    'class' => 'form-control',
                                    'style'=> 'width:100px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'attribute'=>'style_sn',
                                'filter' => Html::activeTextInput($searchModel, 'style_sn', [
                                    'class' => 'form-control',
                                    'style'=> 'width:100px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'attribute' => 'color',
                                'value' => function ($model){
                                    return Yii::$app->attr->valueName($model->color);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'color',Yii::$app->attr->valueMap(AttrIdEnum::MATERIAL_COLOR), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:100px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'attribute' => 'shape',
                                'value' => function ($model){
                                    return Yii::$app->attr->valueName($model->shape);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'shape',Yii::$app->attr->valueMap(AttrIdEnum::MAT_PARTS_SHAPE), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:100px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'attribute' => 'size',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxInput('size', $model->size, ['data-id'=>$model->id]);
                                },
                                'filter' => Html::activeTextInput($searchModel, 'size', [
                                    'class' => 'form-control',
                                    'style'=> 'width:100px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'attribute' => 'chain_type',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxSelect($model,'chain_type', Yii::$app->attr->valueMap(AttrIdEnum::CHAIN_TYPE), ['data-id'=>$model->id, 'prompt'=>'请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'chain_type',Yii::$app->attr->valueMap(\addons\Style\common\enums\AttrIdEnum::CHAIN_TYPE), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:100px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1','attr-name'=>'chain_type','attr-id'=>AttrIdEnum::CHAIN_TYPE],
                            ],
                            [
                                'attribute' => 'cramp_ring',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxSelect($model,'cramp_ring', Yii::$app->attr->valueMap(AttrIdEnum::CHAIN_BUCKLE), ['data-id'=>$model->id, 'prompt'=>'请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'cramp_ring',Yii::$app->attr->valueMap(\addons\Style\common\enums\AttrIdEnum::CHAIN_BUCKLE), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:100px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1','attr-name'=>'cramp_ring','attr-id'=>AttrIdEnum::CHAIN_BUCKLE],
                            ],
                            [
                                'attribute' => 'parts_num',
                                'format' => 'raw',
                                //'value' => function ($model, $key, $index, $column){
                                //    return  Html::ajaxInput('parts_num', $model->parts_num, ['data-id'=>$model->id]);
                                //},
                                'filter' => Html::activeTextInput($searchModel, 'parts_num', [
                                    'class' => 'form-control',
                                ]),
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'attribute' => 'parts_weight',
                                'format' => 'raw',
                                //'value' => function ($model, $key, $index, $column){
                                //    return  Html::ajaxInput('parts_weight', $model->parts_weight, ['data-id'=>$model->id]);
                                //},
                                'filter' => Html::activeTextInput($searchModel, 'parts_weight', [
                                    'class' => 'form-control',
                                ]),
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'attribute' => 'parts_price',
                                'format' => 'raw',
                                //'value' => function ($model, $key, $index, $column){
                                //    return  Html::ajaxInput('parts_price', $model->parts_price, ['data-id'=>$model->id]);
                                //},
                                'filter' => Html::activeTextInput($searchModel, 'parts_price', [
                                    'class' => 'form-control',
                                ]),
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'attribute' => 'cost_price',
                                'format' => 'raw',
                                //'value' => function ($model, $key, $index, $column){
                                //    return  Html::ajaxInput('cost_price', $model->cost_price, ['data-id'=>$model->id]);
                                //},
                                'filter' => Html::activeTextInput($searchModel, 'cost_price', [
                                    'class' => 'form-control',
                                ]),
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'attribute' => 'remark',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxInput('remark', $model->remark, ['data-id'=>$model->id]);
                                },
                                'filter' => Html::activeTextInput($searchModel, 'remark', [
                                    'class' => 'form-control',
                                    'style'=> 'width:200px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-2'],
                            ],
                            [
                                'class' => 'yii\grid\ActionColumn',
                                'header' => '操作',
                                'contentOptions' => ['style' => ['white-space' => 'nowrap']],
                                'template' => '{edit} {delete}',
                                'buttons' => [
                                    'edit' => function($url, $model, $key) use($bill){
                                        if($bill->bill_status == \addons\Warehouse\common\enums\PartsBillStatusEnum::SAVE) {
                                            return Html::edit(['ajax-edit', 'id' => $model->id, 'returnUrl' => Url::getReturnUrl()], '编辑', [
                                                'class' => 'btn btn-info btn-xs',
                                                'data-toggle' => 'modal',
                                                'data-target' => '#ajaxModalLg',
                                            ]);
                                        }
                                    },
                                    'delete' => function($url, $model, $key) use($bill) {
                                        if($bill->bill_status == \addons\Warehouse\common\enums\PartsBillStatusEnum::SAVE){
                                            return Html::delete(['delete', 'id' => $model->id],'删除', [
                                                'class' => 'btn btn-danger btn-xs',
                                            ]);
                                        }
                                    },
                                ],
                                'headerOptions' => [],
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
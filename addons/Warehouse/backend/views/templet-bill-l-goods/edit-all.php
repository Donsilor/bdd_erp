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

$this->title = Yii::t('templet_bill_l_goods', '样板入库单明细');
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box-body nav-tabs-custom">
    <h2 class="page-header"><?= $this->title; ?> - <?= $bill->bill_no?> - <?= \addons\Warehouse\common\enums\TempletBillStatusEnum::getValue($bill->bill_status)?></h2>
    <?php echo Html::menuTab($tabList,$tab)?>
    <div class="box-tools" style="float:right;margin-top:-40px; margin-right: 20px;">
        <?php
        if($bill->bill_status == \addons\Warehouse\common\enums\TempletBillStatusEnum::SAVE) {
            echo Html::a('返回列表', ['templet-bill-l-goods/index', 'bill_id' => $bill->id], ['class' => 'btn btn-info btn-xs']);
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
                        样板总重：<span style="color:green;"><?= $bill->total_weight?>/克</span>
                        样板总额：<span style="color:green;"><?= $bill->total_cost?></span>
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
                                'attribute'=>'batch_sn',
                                'filter' => true,
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'attribute'=>'goods_name',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxInput('goods_name', $model->goods_name, ['data-id'=>$model->id]);
                                },
                                'filter' => Html::activeTextInput($searchModel, 'goods_name', [
                                    'class' => 'form-control',
                                    'style'=> 'width:200px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-2'],
                            ],
                            [
                                'attribute'=>'style_sn',
                                'filter' => true,
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'label' => '款式图片',
                                'value' => function ($model) {
                                    return \common\helpers\ImageHelper::fancyBox(Yii::$app->warehouseService->templet->getStyleImage($model),90,90);
                                },
                                'filter' => false,
                                'format' => 'raw',
                                'headerOptions' => ['width'=>'90'],
                            ],
                            [
                                'label' => '商品图片',
                                'value' => function ($model) {
                                    return \common\helpers\ImageHelper::fancyBox($model->goods_image,60,60);
                                },
                                'filter' => false,
                                'format' => 'raw',
                                'headerOptions' => ['width'=>'90'],
                            ],
                            [
                                'attribute' => 'layout_type',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxSelect($model,'layout_type', \addons\Warehouse\common\enums\LayoutTypeEnum::getMap(), ['data-id'=>$model->id, 'prompt'=>'请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'layout_type',\addons\Warehouse\common\enums\LayoutTypeEnum::getMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:100px;'

                                ]),
                                'headerOptions' => ['class' => 'col-md-1','style'=>'width:100px;'],
                            ],
                            [
                                'attribute'=>'goods_num',
                                'filter' => Html::activeTextInput($searchModel, 'goods_num', [
                                    'class' => 'form-control',
                                ]),
                                'headerOptions' => ['width'=>'100'],
                            ],
                            [
                                'attribute'=>'suttle_weight',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    return $model->suttle_weight ?? '';
                                },
                                'filter' => Html::activeTextInput($searchModel, 'suttle_weight', [
                                    'class' => 'form-control',
                                ]),
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'attribute'=>'stone_weight',
                                'filter' => Html::activeTextInput($searchModel, 'stone_weight', [
                                    'class' => 'form-control',
                                ]),
                                'headerOptions' => ['width'=>'100'],
                            ],
                            [
                                'attribute' => 'finger_hk',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxSelect($model,'finger_hk', \Yii::$app->attr->valueMap(AttrIdEnum::PORT_NO), ['data-id'=>$model->id, 'prompt'=>'请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'finger_hk',Yii::$app->attr->valueMap(AttrIdEnum::PORT_NO), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                                'headerOptions' => [],
                            ],
                            [
                                'attribute' => 'finger',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxSelect($model,'finger', \Yii::$app->attr->valueMap(AttrIdEnum::FINGER), ['data-id'=>$model->id, 'prompt'=>'请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'finger', \Yii::$app->attr->valueMap(AttrIdEnum::FINGER), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                                'headerOptions' => [],
                            ],
                            [
                                'attribute'=>'goods_size',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxInput('goods_size', $model->goods_size, ['data-id'=>$model->id]);
                                },
                                'filter' => Html::activeTextInput($searchModel, 'goods_size', [
                                    'class' => 'form-control',
                                    'style'=> 'width:200px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'attribute'=>'stone_size',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxInput('stone_size', $model->stone_size, ['data-id'=>$model->id]);
                                },
                                'filter' => Html::activeTextInput($searchModel, 'stone_size', [
                                    'class' => 'form-control',
                                    'style'=> 'width:200px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'attribute'=>'cost_price',
                                'filter' => Html::activeTextInput($searchModel, 'cost_price', [
                                    'class' => 'form-control',
                                ]),
                                'headerOptions' => ['width' => '120'],
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
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'class' => 'yii\grid\ActionColumn',
                                'header' => '操作',
                                'contentOptions' => ['style' => ['white-space' => 'nowrap']],
                                'template' => '{edit} {delete}',
                                'buttons' => [
                                    'edit' => function($url, $model, $key) use($bill){
                                        if($bill->bill_status == \addons\Warehouse\common\enums\GoldBillStatusEnum::SAVE) {
                                            return Html::edit(['ajax-edit', 'id' => $model->id, 'bill_id' => $model->bill_id, 'returnUrl' => Url::getReturnUrl()], '编辑', [
                                                'class' => 'btn btn-info btn-xs',
                                                'data-toggle' => 'modal',
                                                'data-target' => '#ajaxModalLg',
                                            ]);
                                        }
                                    },
                                    'delete' => function($url, $model, $key) use($bill) {
                                        if($bill->bill_status == BillStatusEnum::SAVE){
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
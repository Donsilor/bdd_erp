<?php


use addons\Purchase\common\enums\ReceiptStatusEnum;
use addons\Style\common\enums\AttrIdEnum;
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

$this->title = Yii::t('gold_receipt_goods', '金料收货单详情');
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box-body nav-tabs-custom">
    <h2 class="page-header"><?php echo $this->title; ?> - <?php echo $receipt->receipt_no?></h2>
    <?php echo Html::menuTab($tabList,$tab)?>
    <div class="box-tools" style="float:right;margin-top:-40px; margin-right: 20px;">
        <?php
        if($receipt->receipt_status == ReceiptStatusEnum::SAVE) {
            echo Html::edit(['edit-all', 'receipt_id' => $receipt->id], '批量编辑', ['class'=>'btn btn-info btn-xs']);
        }
        if($receipt->receipt_status == ReceiptStatusEnum::CONFIRM) {
            echo Html::batchPopButton(['warehouse','check'=>1],'批量入库', [
                'class'=>'btn btn-success btn-xs',
                'data-width'=>'40%',
                'data-height'=>'60%',
                'data-offset'=>'20px',
            ]);
        }
        ?>
    </div>
    <div class="tab-content">
        <div class="col-xs-12" style="padding-left: 0px;padding-right: 0px;">
            <div class="box">
                <div class="box-body table-responsive">
                    <?php echo Html::batchButtons(false)?>
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
                                'attribute'=>'xuhao',
                                'headerOptions' => [],
                                'filter' => Html::activeTextInput($searchModel, 'xuhao', [
                                    'class' => 'form-control',
                                    'style'=> 'width:60px;'
                                ]),
                            ],
                            [
                                'attribute'=>'purchase_sn',
                                'headerOptions' => ['class' => 'col-md-1'],
                                'filter' => Html::activeTextInput($searchModel, 'purchase_sn', [
                                    'class' => 'form-control',
                                    'style'=> 'width:120px;'
                                ]),
                            ],
                            [
                                'attribute'=>'goods_name',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1'],
                                'filter' => Html::activeTextInput($searchModel, 'goods_name', [
                                    'class' => 'form-control',
                                    'style'=> 'width:200px;'
                                ]),
                            ],
                            [
                                'attribute'=>'goods_sn',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1'],
                                'filter' => Html::activeTextInput($searchModel, 'goods_sn', [
                                    'class' => 'form-control',
                                    'style'=> 'width:100px;'
                                ]),
                            ],
                            [
                                'attribute' => 'material_type',
                                'value' => function ($model){
                                    return Yii::$app->attr->valueName($model->material_type);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'material_type',Yii::$app->attr->valueMap(AttrIdEnum::MAT_GOLD_TYPE), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:100px;'
                                ]),
                                'headerOptions' => [],
                            ],
                            [
                                'attribute'=>'goods_weight',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1'],
                                'filter' => Html::activeTextInput($searchModel, 'goods_weight', [
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                            ],
                            [
                                'attribute'=>'gold_price',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1'],
                                'filter' => Html::activeTextInput($searchModel, 'gold_price', [
                                    'class' => 'form-control',
                                    'style'=> 'width:100px;'
                                ]),
                            ],
                            [
                                'attribute'=>'cost_price',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1'],
                                'filter' => Html::activeTextInput($searchModel, 'cost_price', [
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                            ],
                            [
                                'attribute'=>'incl_tax_price',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1'],
                                'filter' => Html::activeTextInput($searchModel, 'incl_tax_price', [
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                            ],
                            [
                                'attribute' => 'goods_status',
                                'value' => function ($model){
                                    return \addons\Purchase\common\enums\ReceiptGoodsStatusEnum::getValue($model->goods_status);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'goods_status',\addons\Purchase\common\enums\ReceiptGoodsStatusEnum::getMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:100px;',
                                ]),
                                'format' => 'raw',
                                'headerOptions' => ['width'=>'100'],
                            ],
                            [
                                'attribute'=>'goods_remark',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1'],
                                'filter' => Html::activeTextInput($searchModel, 'goods_remark', [
                                    'class' => 'form-control',
                                    'style'=> 'width:150px;'
                                ]),
                            ],
                            [
                                'class' => 'yii\grid\ActionColumn',
                                'header' => '操作',
                                'contentOptions' => ['style' => ['white-space' => 'nowrap']],
                                'template' => '{edit} {delete}',
                                'buttons' => [
                                    'edit' => function($url, $model, $key) use($receipt){
                                        if($receipt->receipt_status == ReceiptStatusEnum::SAVE) {
                                            return Html::edit(['ajax-edit', 'id' => $model->id, 'returnUrl' => Url::getReturnUrl()], '编辑', [
                                                'class' => 'btn btn-info btn-xs',
                                                'data-toggle' => 'modal',
                                                'data-target' => '#ajaxModalLg',
                                            ]);
                                        }
                                    },
                                    'delete' => function($url, $model, $key) use($receipt) {
                                        if($receipt->receipt_status == ReceiptStatusEnum::SAVE){
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
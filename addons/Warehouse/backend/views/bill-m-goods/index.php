<?php

use common\helpers\Html;
use common\helpers\Url;
use kartik\select2\Select2;
use yii\grid\GridView;
use kartik\daterange\DateRangePicker;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('warehouse_bill_m', '调拨单列表');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="box-body nav-tabs-custom">
    <h2 class="page-header"><?php echo $this->title; ?> - <?php echo $billInfo->bill_no?></h2>
    <?php echo Html::menuTab($tabList,$tab)?>
    <div style="float:right;margin-top:-40px;margin-right: 20px;">
        <?php
        if($billInfo->bill_status == \addons\Warehouse\common\enums\BillStatusEnum::SAVE){
            echo Html::create(['edit', 'bill_id' => $billInfo->id], '新增货品', [
                'class' => 'btn btn-primary btn-xs openIframe',
                'data-width'=>'90%',
                'data-height'=>'90%',
                'data-offset'=>'20px',
            ]);
        }
        echo '&nbsp;';
        echo Html::a('导出', ['bill-m/export?ids='.$billInfo->id],[
            'class'=>'btn btn-success btn-xs'
        ]);
        ?>
    </div>
    <div class="tab-content" style="padding-right: 10px;">
        <div class="row col-xs-12" style="padding-left: 0px;padding-right: 0px;">
            <div class="box">
                <div class="box-body table-responsive" style="padding-left: 0px;padding-right: 0px;">
                    <?php echo Html::batchButtons(false)?>
                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'tableOptions' => ['class' => 'table table-hover'],
                        'options' => ['style'=>' width:140%;white-space:nowrap;'],
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
                                'attribute' => 'id',
                                'filter' => false,
                                'format' => 'raw',
                            ],
                            [
                                'attribute'=>'goods_id',
                                'filter' => true,
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'attribute' => 'style_sn',
                                'headerOptions' => ['class' => 'col-md-1'],
                                'filter' => true,
                            ],
                            [
                                'attribute' => 'goods_name',
                                'filter' => true,
                                'headerOptions' => ['class' => 'col-md-2'],
                            ],
                            [
                                'attribute'=>'goods.goods_status',
                                'value' => function($model){
                                    return \addons\Warehouse\common\enums\GoodsStatusEnum::getValue($model->goods->goods_status ?? '');
                                },
                                'filter' => true,
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'attribute' => 'goods.style_cate_id',
                                'value' => 'goods.styleCate.name',
                                'filter' => true,
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'attribute' => 'goods.product_type_id',
                                'value' => 'goods.productType.name',
                                'filter' => true,
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'attribute' => 'put_in_type',
                                'headerOptions' => ['class' => 'col-md-1'],
                                'value' => function ($model){
                                    return \addons\Warehouse\common\enums\PutInTypeEnum::getValue($model->put_in_type);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'put_in_type',\addons\Warehouse\common\enums\PutInTypeEnum::getMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',

                                ]),
                            ],
                            [
                                'attribute' => 'from_warehouse_id',
                                'value' =>"fromWarehouse.name",
                                'filter'=>Select2::widget([
                                    'name'=>'SearchModel[from_warehouse_id]',
                                    'value'=>$searchModel->from_warehouse_id,
                                    'data'=>Yii::$app->warehouseService->warehouse::getDropDown(),
                                    'options' => ['placeholder' =>"请选择"],
                                    'pluginOptions' => [
                                        'allowClear' => true,

                                    ],
                                ]),
                                'headerOptions' => ['class' => 'col-md-2'],
                            ],
                            [
                                'attribute' => 'to_warehouse_id',
                                'value' =>"toWarehouse.name",
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'attribute' => 'material',
                                'value' => function($model){
                                    return Yii::$app->attr->valueName($model->material);
                                },
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'attribute' => 'gold_weight',
                                'filter' => false,
                            ],

                            [
                                'attribute' => 'goods.main_stone_type',
                                'filter' => false,
                            ],
                            [
                                'attribute' => 'goods.diamond_carat',
                                'filter' => false,
                            ],
                            [
                                'attribute' => 'goods.main_stone_num',
                                'filter' => false,
                            ],
                            [
                                'attribute' => 'goods.second_stone_weight1',
                                'filter' => false,
                            ],
                            [
                                'attribute' => 'goods.second_stone_num1',
                                'filter' => false,
                            ],
                            [
                                'attribute' => 'goods.finger',
                                'filter' => false,
                            ],
                            [
                                'attribute' => 'cost_price',
                                'visible' => \common\helpers\Auth::verify(\common\enums\SpecialAuthEnum::VIEW_CAIGOU_PRICE),
                                'filter' => false,
                            ],


                            [
                                'class' => 'yii\grid\ActionColumn',
                                'header' => '操作',
                                'template' => '{delete}',
                                'buttons' => [
                                    'delete' => function($url, $model, $key) use($billInfo){
                                        if($billInfo->audit_status == \common\enums\AuditStatusEnum::PENDING){
                                            return Html::delete(['delete', 'id' => $model->id]);
                                        }

                                    },
                                ],
                                'headerOptions' => ['class' => 'col-md-3'],
                            ]
                        ]
                    ]); ?>
                </div>
            </div>
        </div>
    </div>
</div>

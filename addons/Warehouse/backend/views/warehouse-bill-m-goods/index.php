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
    <div style="float:right;margin-top:-40px">
        <?php
        if($billInfo->audit_status == \common\enums\AuditStatusEnum::PENDING){
            echo Html::create(['edit', 'bill_id' => $billInfo->id], '新增货品', [
                'class' => 'btn btn-primary btn-xs openIframe',
                'data-width'=>'90%',
                'data-height'=>'90%',
                'data-offset'=>'20px',
            ]);
        }
        ?>
    </div>
    <div class="tab-content">
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
                                'attribute' => 'goods_num',
                                'filter' => false,
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
                                'label'=>'出库仓库',
                                'attribute' => 'from_warehouse_id',
                                'value' =>"fromWarehouse.name",
                                'filter'=>Select2::widget([
                                    'name'=>'SearchModel[from_warehouse_id]',
                                    'value'=>$searchModel->to_warehouse_id,
                                    'data'=>Yii::$app->warehouseService->warehouse::getDropDown(),
                                    'options' => ['placeholder' =>"请选择"],
                                    'pluginOptions' => [
                                        'allowClear' => true,

                                    ],
                                ]),
                                'headerOptions' => ['class' => 'col-md-2'],
                            ],
                            [
                                'label'=>'入库仓库',
                                'attribute' => 'to_warehouse_id',
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'attribute' => 'material',
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'attribute' => 'gold_weight',
                                'filter' => false,
                            ],
                            [
                                'attribute' => 'gold_loss',
                                'filter' => false,
                            ],
                            [
                                'attribute' => 'diamond_carat',
                                'filter' => false,
                            ],
                            [
                                'attribute' => 'diamond_color',
                                'filter' => false,
                            ],
                            [
                                'attribute' => 'diamond_clarity',
                                'filter' => false,
                            ],
                            [
                                'attribute' => 'diamond_cert_id',
                                'filter' => false,
                            ],
                            [
                                'attribute' => 'cost_price',
                                'filter' => false,
                            ],
                            [
                                'attribute' => 'market_price',
                                'filter' => false,
                            ],
                            [
                                'attribute'=>'created_at',
                                'filter' => DateRangePicker::widget([    // 日期组件
                                    'model' => $searchModel,
                                    'attribute' => 'created_at',
                                    'value' => $searchModel->created_at,
                                    'options' => ['readonly' => false,'class'=>'form-control','style'=>'background-color:#fff;width:150px;'],
                                    'pluginOptions' => [
                                        'format' => 'yyyy-mm-dd',
                                        'locale' => [
                                            'separator' => '/',
                                        ],
                                        'endDate' => date('Y-m-d',time()),
                                        'todayHighlight' => true,
                                        'autoclose' => true,
                                        'todayBtn' => 'linked',
                                        'clearBtn' => true,


                                    ],

                                ]),
                                'value'=>function($model){
                                    return Yii::$app->formatter->asDatetime($model->updated_at);
                                }

                            ],

                            [
                                'class' => 'yii\grid\ActionColumn',
                                'header' => '操作',
                                'template' => '{delete}',
                                'buttons' => [
                                    'delete' => function($url, $model, $key){
                                        return Html::delete(['delete', 'id' => $model->id]);
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

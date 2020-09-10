<?php


use common\helpers\Html;
use yii\grid\GridView;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $searchModel yii\data\ActiveDataProvider */
/* @var $tabList yii\data\ActiveDataProvider */
/* @var $tab yii\data\ActiveDataProvider */
/* @var $bill yii\data\ActiveDataProvider */

$this->title = Yii::t('bill_b_goods', '其它出库单明细');
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box-body nav-tabs-custom">
    <h2 class="page-header"><?php echo $this->title; ?> - <?php echo $bill->bill_no?> - <?= \addons\Warehouse\common\enums\BillStatusEnum::getValue($bill->bill_status)?></h2>
    <?php echo Html::menuTab($tabList,$tab)?>
    <div class="box-tools" style="float:right;margin-top:-40px; margin-right: 20px;">
        <?php
        if($bill->bill_status == \addons\Warehouse\common\enums\BillStatusEnum::SAVE) {
            echo Html::create(['add', 'bill_id' => $bill->id], '新增货品', [
                'class' => 'btn btn-primary btn-xs openIframe',
                'data-width'=>'90%',
                'data-height'=>'90%',
                'data-offset'=>'20px',
            ]);
            echo '&nbsp;';
            echo Html::a('返回列表', ['bill-c-goods/index', 'bill_id' => $bill->id], ['class' => 'btn btn-info btn-xs']);
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
                        'options' => ['style'=>' width:120%;white-space:nowrap;'],
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
                                    'style'=> 'width:60px;'
                                ]),
                            ],
                            [
                                'attribute'=>'goods_id',
                                'headerOptions' => ['class' => 'col-md-1'],
                                'filter' => Html::activeTextInput($searchModel, 'goods_id', [
                                    'class' => 'form-control',
                                    'style'=> 'width:120px;'
                                ]),
                            ],
                            [
                                'attribute'=>'style_sn',
                                'headerOptions' => ['class' => 'col-md-1'],
                                'filter' => Html::activeTextInput($searchModel, 'style_sn', [
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
                                    'style'=> 'width:260px;'
                                ]),
                            ],
                            [
                                'attribute'=>'goods.goods_status',
                                'value' => function($model){
                                    return \addons\Warehouse\common\enums\GoodsStatusEnum::getValue($model->goods->goods_status);
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
                                'attribute' => 'gold_loss',
                                'value' => function($model){
                                    return $model->gold_loss;
                                },
                                'format' => 'raw',
                                'filter' => false,
                            ],
                            [
                                'attribute' => 'diamond_carat',
                                'filter' => false,
                            ],
                            [
                                'attribute' => 'diamond_color',
                                'value' => function($model){
                                    return Yii::$app->attr->valueName($model->diamond_color);
                                },
                                'filter' => false,
                            ],
                            [
                                'attribute' => 'diamond_clarity',
                                'value' => function($model){
                                    return Yii::$app->attr->valueName($model->diamond_clarity);
                                },
                                'filter' => false,
                            ],
                            [
                                'attribute' => 'diamond_cert_id',
                                'filter' => false,
                            ],
                            [
                                'attribute' => 'cost_price',
                                'value' => function($model){
                                    return Html::ajaxInput('cost_price',$model->cost_price);
                                },
                                'filter' => false,
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1'],

                            ],
                            [
                                'attribute' => 'market_price',
                                'value' => function($model){
                                    return Html::ajaxInput('cost_price',$model->cost_price);
                                },
                                'filter' => false,
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'attribute'=>'created_at',
                                'filter' => \kartik\daterange\DateRangePicker::widget([    // 日期组件
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
                                    'delete' => function($url, $model, $key) use($bill){
                                        if($bill->audit_status == \common\enums\AuditStatusEnum::PENDING){
                                            return Html::delete(['delete', 'id' => $model->id]);
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
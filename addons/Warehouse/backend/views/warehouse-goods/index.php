<?php

use common\helpers\Html;
use common\helpers\Url;
use yii\grid\GridView;
use kartik\daterange\DateRangePicker;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('warehouse_goods', '商品管理');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title"><?= Html::encode($this->title) ?></h3>
            </div>
            <div class="box-body table-responsive">
                <?php echo Html::batchButtons(false)?>
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'tableOptions' => ['class' => 'table table-hover'],
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
                            'headerOptions' => ['width'=>'30'],
                        ],
                        [
                            'attribute' => 'goods_id',
                            'value'=>function($model) {
                                return Html::a($model->goods_id, ['view', 'id' => $model->id,'returnUrl'=>Url::getReturnUrl()], ['style'=>"text-decoration:underline;color:#3c8dbc"]);
                            },
                            'filter' => Html::activeTextInput($searchModel, 'goods_id', [
                                'class' => 'form-control',
                                'style'=> 'width:100px;'
                            ]),
                            'format' => 'raw',
                        ],

                        [
                            'attribute'=>'goods_name',
                            'filter' => Html::activeTextInput($searchModel, 'goods_name', [
                                'class' => 'form-control',
                                'style'=> 'width:150px;'
                            ]),
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'style_sn',
                            'filter' => Html::activeTextInput($searchModel, 'style_sn', [
                                'class' => 'form-control',
                                'style'=> 'width:100px;'
                            ]),
                            'headerOptions' => [],
                        ],


                        [
                            'attribute' => 'product_type_id',
                            'format' => 'raw',
                            'headerOptions' => ['class' => 'col-md-1'],
                            'value' => 'productType.name',
                            'filter' => Html::activeDropDownList($searchModel, 'product_type_id',Yii::$app->styleService->productType::getDropDown(), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style'=> 'width:80px;'

                            ]),
                        ],
                        [
                            'attribute' => 'style_cate_id',
                            'format' => 'raw',
                            'headerOptions' => ['class' => 'col-md-1'],
                            'value' => 'styleCate.name',
                            'filter' => Html::activeDropDownList($searchModel, 'style_cate_id',Yii::$app->styleService->styleCate::getDropDown(), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style'=> 'width:80px;'

                            ]),
                        ],


                        [
                            'attribute' => 'goods_status',
                            'format' => 'raw',
                            'headerOptions' => ['class' => 'col-md-1'],
                            'value' => function ($model){
                                return \common\enums\StatusEnum::getValue($model->goods_status);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'goods_status',\addons\Warehouse\common\enums\GoodsStatusEnum::getMap(), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style'=> 'width:80px;'

                            ]),
                        ],

                        [
                            'attribute' => 'supplier_id',
                            'format' => 'raw',
                            'headerOptions' => ['class' => 'col-md-1'],
                            'value' => 'supplier.supplier_name',
                            'filter' => Html::activeDropDownList($searchModel, 'supplier_id',Yii::$app->supplyService->supplier->getDropDown(), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style'=> 'width:200px;'

                            ]),
                        ],

                        [
                            'attribute' => 'put_in_type',
                            'format' => 'raw',
                            'headerOptions' => ['class' => 'col-md-1'],
                            'value' => function ($model){
                                return \addons\Warehouse\common\enums\PutInTypeEnum::getValue($model->put_in_type);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'put_in_type',\addons\Warehouse\common\enums\PutInTypeEnum::getMap(), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style'=> 'width:80px;'

                            ]),
                        ],

//                        [
//                            'attribute'=>'company_id',
//                            'filter' => Html::activeTextInput($searchModel, 'company_id', [
//                                'class' => 'form-control',
//                                'style'=> 'width:100px;'
//                            ]),
//                            'headerOptions' => [],
//                        ],

                        [
                            'attribute' => 'warehouse_id',
                            'format' => 'raw',
                            'headerOptions' => ['class' => 'col-md-1'],
                            'value' => 'warehouse.name',
                            'filter' => Html::activeDropDownList($searchModel, 'warehouse_id',Yii::$app->warehouseService->warehouse::getDropDown(), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style'=> 'width:80px;'

                            ]),
                        ],
                        [
                            'attribute'=>'gold_weight',
                            'filter' => Html::activeTextInput($searchModel, 'gold_weight', [
                                'class' => 'form-control',
                                'style'=> 'width:60px;'
                            ]),
                            'headerOptions' => [],
                        ],

                        [
                            'attribute'=>'gold_loss',
                            'filter' => Html::activeTextInput($searchModel, 'gold_loss', [
                                'class' => 'form-control',
                                'style'=> 'width:100px;'
                            ]),
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'gross_weight',
                            'filter' => Html::activeTextInput($searchModel, 'gross_weight', [
                                'class' => 'form-control',
                                'style'=> 'width:100px;'
                            ]),
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'finger',
                            'filter' => Html::activeTextInput($searchModel, 'finger', [
                                'class' => 'form-control',
                                'style'=> 'width:100px;'
                            ]),
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'order_sn',
                            'filter' => Html::activeTextInput($searchModel, 'order_sn', [
                                'class' => 'form-control',
                                'style'=> 'width:100px;'
                            ]),
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'produce_sn',
                            'filter' => Html::activeTextInput($searchModel, 'produce_sn', [
                                'class' => 'form-control',
                                'style'=> 'width:100px;'
                            ]),
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'cert_type',
                            'filter' => Html::activeTextInput($searchModel, 'cert_type', [
                                'class' => 'form-control',
                                'style'=> 'width:100px;'
                            ]),
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'cert_id',
                            'filter' => Html::activeTextInput($searchModel, 'cert_id', [
                                'class' => 'form-control',
                                'style'=> 'width:100px;'
                            ]),
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'goods_num',
                            'filter' => Html::activeTextInput($searchModel, 'goods_num', [
                                'class' => 'form-control',
                                'style'=> 'width:100px;'
                            ]),
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'material',
                            'filter' => Html::activeTextInput($searchModel, 'material', [
                                'class' => 'form-control',
                                'style'=> 'width:100px;'
                            ]),
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'material_type',
                            'filter' => Html::activeTextInput($searchModel, 'material_type', [
                                'class' => 'form-control',
                                'style'=> 'width:100px;'
                            ]),
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'material_color',
                            'filter' => Html::activeTextInput($searchModel, 'material_color', [
                                'class' => 'form-control',
                                'style'=> 'width:100px;'
                            ]),
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'diamond_carat',
                            'filter' => Html::activeTextInput($searchModel, 'diamond_carat', [
                                'class' => 'form-control',
                                'style'=> 'width:100px;'
                            ]),
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'diamond_clarity',
                            'filter' => Html::activeTextInput($searchModel, 'diamond_clarity', [
                                'class' => 'form-control',
                                'style'=> 'width:100px;'
                            ]),
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'diamond_cut',
                            'filter' => Html::activeTextInput($searchModel, 'diamond_cut', [
                                'class' => 'form-control',
                                'style'=> 'width:100px;'
                            ]),
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'diamond_polish',
                            'filter' => Html::activeTextInput($searchModel, 'diamond_polish', [
                                'class' => 'form-control',
                                'style'=> 'width:100px;'
                            ]),
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'diamond_symmetry',
                            'filter' => Html::activeTextInput($searchModel, 'diamond_symmetry', [
                                'class' => 'form-control',
                                'style'=> 'width:100px;'
                            ]),
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'diamond_fluorescence',
                            'filter' => Html::activeTextInput($searchModel, 'diamond_fluorescence', [
                                'class' => 'form-control',
                                'style'=> 'width:100px;'
                            ]),
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'diamond_discount',
                            'filter' => Html::activeTextInput($searchModel, 'diamond_discount', [
                                'class' => 'form-control',
                                'style'=> 'width:100px;'
                            ]),
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'diamond_cert_type',
                            'filter' => Html::activeTextInput($searchModel, 'diamond_cert_type', [
                                'class' => 'form-control',
                                'style'=> 'width:100px;'
                            ]),
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'diamond_cert_id',
                            'filter' => Html::activeTextInput($searchModel, 'diamond_cert_id', [
                                'class' => 'form-control',
                                'style'=> 'width:100px;'
                            ]),
                            'headerOptions' => [],
                        ],
                        [
                            'attribute' => 'jintuo_type',
                            'format' => 'raw',
                            'headerOptions' => ['class' => 'col-md-1'],
                            'value' => function ($model){
                                return \addons\Style\common\enums\JintuoTypeEnum::getValue($model->jintuo_type);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'jintuo_type',\addons\Style\common\enums\JintuoTypeEnum::getMap(), [
                                'prompt' => '全部',
                                'class' => 'form-control',

                            ]),
                        ],
                        [
                            'attribute'=>'market_price',
                            'filter' => Html::activeTextInput($searchModel, 'market_price', [
                                'class' => 'form-control',
                                'style'=> 'width:100px;'
                            ]),
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'xiangkou',
                            'filter' => Html::activeTextInput($searchModel, 'xiangkou', [
                                'class' => 'form-control',
                                'style'=> 'width:100px;'
                            ]),
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'length',
                            'filter' => Html::activeTextInput($searchModel, 'length', [
                                'class' => 'form-control',
                                'style'=> 'width:100px;'
                            ]),
                            'headerOptions' => [],
                        ],
                        [
                            'attribute' => 'weixiu_status',
                            'format' => 'raw',
                            'headerOptions' => ['class' => 'col-md-1'],
                            'value' => function ($model){
                                return \addons\Warehouse\common\enums\WeixiuStatusEnum::getValue($model->weixiu_status);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'weixiu_status',\addons\Warehouse\common\enums\WeixiuStatusEnum::getMap(), [
                                'prompt' => '全部',
                                'class' => 'form-control',

                            ]),
                        ],
                        [
                            'attribute' => 'weixiu_warehouse_id',
                            'format' => 'raw',
                            'headerOptions' => ['class' => 'col-md-1'],
                            'value' => 'weixiuWarehouse.name',
                            'filter' => Html::activeDropDownList($searchModel, 'weixiu_warehouse_id',Yii::$app->warehouseService->warehouse::getDropDown(), [
                                'prompt' => '全部',
                                'class' => 'form-control',

                            ]),
                        ],
                        [
                            'attribute'=>'parts_gold_weight',
                            'filter' => Html::activeTextInput($searchModel, 'parts_gold_weight', [
                                'class' => 'form-control',
                                'style'=> 'width:100px;'
                            ]),
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'parts_num',
                            'filter' => Html::activeTextInput($searchModel, 'parts_num', [
                                'class' => 'form-control',
                                'style'=> 'width:100px;'
                            ]),
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'main_stone_type',
                            'filter' => Html::activeTextInput($searchModel, 'main_stone_type', [
                                'class' => 'form-control',
                                'style'=> 'width:100px;'
                            ]),
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'main_stone_num',
                            'filter' => Html::activeTextInput($searchModel, 'main_stone_num', [
                                'class' => 'form-control',
                                'style'=> 'width:100px;'
                            ]),
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'second_stone_type1',
                            'filter' => Html::activeTextInput($searchModel, 'second_stone_type1', [
                                'class' => 'form-control',
                                'style'=> 'width:100px;'
                            ]),
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'second_stone_num1',
                            'filter' => Html::activeTextInput($searchModel, 'second_stone_num1', [
                                'class' => 'form-control',
                                'style'=> 'width:100px;'
                            ]),
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'second_stone_weight1',
                            'filter' => Html::activeTextInput($searchModel, 'second_stone_weight1', [
                                'class' => 'form-control',
                                'style'=> 'width:100px;'
                            ]),
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'second_stone_price1',
                            'filter' => Html::activeTextInput($searchModel, 'second_stone_price1', [
                                'class' => 'form-control',
                                'style'=> 'width:100px;'
                            ]),
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'second_stone_color1',
                            'filter' => Html::activeTextInput($searchModel, 'second_stone_color1', [
                                'class' => 'form-control',
                                'style'=> 'width:100px;'
                            ]),
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'second_stone_clarity1',
                            'filter' => Html::activeTextInput($searchModel, 'second_stone_clarity1', [
                                'class' => 'form-control',
                                'style'=> 'width:100px;'
                            ]),
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'second_stone_shape1',
                            'filter' => Html::activeTextInput($searchModel, 'second_stone_shape1', [
                                'class' => 'form-control',
                                'style'=> 'width:100px;'
                            ]),
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'second_stone_type2',
                            'filter' => Html::activeTextInput($searchModel, 'second_stone_type2', [
                                'class' => 'form-control',
                                'style'=> 'width:100px;'
                            ]),
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'second_stone_num2',
                            'filter' => Html::activeTextInput($searchModel, 'second_stone_num2', [
                                'class' => 'form-control',
                                'style'=> 'width:100px;'
                            ]),
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'second_stone_weight2',
                            'filter' => Html::activeTextInput($searchModel, 'second_stone_weight2', [
                                'class' => 'form-control',
                                'style'=> 'width:100px;'
                            ]),
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'second_stone_price2',
                            'filter' => Html::activeTextInput($searchModel, 'second_stone_price2', [
                                'class' => 'form-control',
                                'style'=> 'width:100px;'
                            ]),
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'member.username',
                            'filter' => Html::activeTextInput($searchModel, 'member.username', [
                                'class' => 'form-control',
                                'style'=> 'width:100px;'
                            ]),
                            'headerOptions' => [],
                        ],

                        [
                            'attribute'=>'updated_at',
                            'filter' => DateRangePicker::widget([    // 日期组件
                                'model' => $searchModel,
                                'attribute' => 'updated_at',
                                'value' => $searchModel->created_at,
                                'options' => ['readonly' => false,'class'=>'form-control','style'=>'background-color:#fff;width:200px;'],
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
                            'template' => '{edit} ',
                            'buttons' => [
                                'edit' => function($url, $model, $key){
                                    return Html::edit(['ajax-edit','id' => $model->id,'returnUrl' => Url::getReturnUrl()], '编辑', [
                                        'data-toggle' => 'modal',
                                        'data-target' => '#ajaxModalLg',
                                    ]);
                                },

                                'delete' => function($url, $model, $key){
                                    return Html::delete(['delete', 'id' => $model->id]);
                                },
                            ],

                        ]
                    ]
                ]); ?>
            </div>
        </div>
    </div>
</div>
<?php

use common\helpers\Html;
use common\helpers\Url;
use yii\grid\GridView;
use kartik\daterange\DateRangePicker;
use addons\Style\common\enums\AttrIdEnum;

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
                    'options' => ['style'=>' width:100%;white-space:nowrap;' ],
                    'showFooter' => false,//显示footer行
                    'id'=>'grid',
                    'columns' => [
                        [
                            'class' => 'yii\grid\SerialColumn',
                            'visible' => false,
                        ],
//                        [
//                            'class'=>'yii\grid\CheckboxColumn',
//                            'name'=>'id',  //设置每行数据的复选框属性
//                            'headerOptions' => ['width'=>'30'],
//                        ],
                        [
                            'label' => '商品图片',
                            'value' => function ($model) {
                                return \common\helpers\ImageHelper::fancyBox($model->goods_image,90,90);
                            },
                            'filter' => false,
                            'format' => 'raw',
                            'headerOptions' => ['width'=>'90'],
                        ],
                        [
                            'attribute' => 'goods_id',
                            'value'=>function($model) {
                                if(preg_match("/^9/is", $model->goods_id)){
                                    $model->goods_id = Yii::$app->warehouseService->warehouseGoods->createGoodsId($model);
                                }                                
                                return Html::a($model->goods_id, ['view', 'id' => $model->id,'returnUrl'=>Url::getReturnUrl()], ['style'=>"text-decoration:underline;color:#3c8dbc"]);
                            },
                            'filter' => Html::activeTextInput($searchModel, 'goods_id', [
                                'class' => 'form-control',
                                'style'=> 'width:130px;'
                            ]),
                            'format' => 'raw',
                        ],

                        [
                            'attribute'=>'goods_name',
                            'format' => 'raw',
                            'value' => function ($model, $key, $index, $column){
                                return  $model->goods_name;
                            },
                            'filter' => Html::activeTextInput($searchModel, 'goods_name', [
                                'class' => 'form-control',
                                'style'=> 'width:200px;'
                            ]),
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'style_sn',
                            'filter' => Html::activeTextInput($searchModel, 'style_sn', [
                                'class' => 'form-control',
                                'style'=> 'width:150px;'
                            ]),
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'goods_num',
                            'filter' => Html::activeTextInput($searchModel, 'goods_num', [
                                'class' => 'form-control',
                                'style'=> 'width:60px;'
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
                                'style'=> 'width:120px;'

                            ]),
                        ],
                        [
                            'attribute' => 'product_type_id',
                            'format' => 'raw',
                            'headerOptions' => ['class' => 'col-md-1'],
                            'value' => function($model){
                                return $model->productType->name ?? '';
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'product_type_id',Yii::$app->styleService->productType::getDropDown(), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style'=> 'width:120px;'

                            ]),
                        ],

                        [
                            'attribute' => 'productType.is_inlay',
                            'format' => 'raw',
                            'headerOptions' => ['class' => 'col-md-1'],
                            'value' => function ($model){
                                return \addons\Style\common\enums\InlayEnum::getValue($model->productType->is_inlay);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'productType.is_inlay',\addons\Style\common\enums\InlayEnum::getMap(), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style'=> 'width:80px;'
                            ]),
                        ],
                        [
                            'attribute' => 'style_channel_id',
                            'format' => 'raw',
                            'headerOptions' => ['class' => 'col-md-1'],
                            'value' => function($model){
                                return $model->channel->name ?? '';
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'style_channel_id',Yii::$app->styleService->styleChannel->getDropDown(), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style'=> 'width:120px;'

                            ]),
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
                                    'style'=> 'width:80px;'
                            ]),
                        ],

                        [
                            'attribute' => 'goods_status',
                            'format' => 'raw',
                            'headerOptions' => ['class' => 'col-md-1'],
                            'value' => function ($model){
                                return \addons\Warehouse\common\enums\GoodsStatusEnum::getValue($model->goods_status);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'goods_status',\addons\Warehouse\common\enums\GoodsStatusEnum::getMap(), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style'=> 'width:100px;'

                            ]),
                        ],
                        [
                            'attribute' => 'warehouse_id',
                            'value' =>"warehouse.name",
                            'filter'=>\kartik\select2\Select2::widget([
                                'name'=>'SearchModel[warehouse_id]',
                                'value'=>$searchModel->warehouse_id,
                                'data'=>Yii::$app->warehouseService->warehouse::getDropDown(),
                                'options' => ['placeholder' =>"请选择"],
                                'pluginOptions' => [
                                    'allowClear' => true,
                                    'width' => 200
                                ],
                                'pluginLoading'=>false


                            ]),
                            'headerOptions' => ['class' => 'col-md-2'],
                            'format' => 'raw',

                        ],
                        [
                            'attribute' => 'material',
                            'value' => function($model){
                                return Yii::$app->attr->valueName($model->material);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'material',Yii::$app->attr->valueMap(AttrIdEnum::MATERIAL), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style'=> 'width:80px;'
                            ]),
                            'headerOptions' => [],
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
                            'attribute'=>'parts_num',
                            'filter' => false,

                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'parts_gold_weight',
                            'filter' => false,
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'gross_weight',
                            'value'=>'gross_weight',
                            'filter' => false,
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
                            'attribute'=>'product_size',
                            'value'=>function($model){
                                return $model->product_size;
                            },
                            'filter' => false,
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'main_stone_type',
                            'value' => function($model){
                                return Yii::$app->attr->valueName($model->main_stone_type);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'main_stone_type',Yii::$app->attr->valueMap(AttrIdEnum::MAIN_STONE_TYPE), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style'=> 'width:80px;'
                            ]),
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'main_stone_size',
                            'value'=>function($model){
                                return $model->main_stone_size;
                            },
                            'filter' => false,
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'main_stone_price',
                            'filter' => Html::activeTextInput($searchModel, 'main_stone_price', [
                                'class' => 'form-control',
                                'style'=> 'width:100px;'
                            ]),
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'second_stone_type1',
                            'value' => function($model){
                                return Yii::$app->attr->valueName($model->second_stone_type1);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'second_stone_type1',Yii::$app->attr->valueMap(AttrIdEnum::SIDE_STONE1_TYPE), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style'=> 'width:80px;'
                            ]),
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'second_stone_size1',
                            'value'=>function($model){
                                return $model->second_stone_size1;
                            },
                            'filter' => false,
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
                            'attribute'=>'remark',
                            'filter' => false,
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
                            'attribute'=>'cert_id',
                            'filter' => Html::activeTextInput($searchModel, 'cert_id', [
                                'class' => 'form-control',
                                'style'=> 'width:100px;'
                            ]),
                            'headerOptions' => [],
                        ],

                        [
                            'attribute'=>'cert_type',
                            'value' => function($model){
                                return Yii::$app->attr->valueName($model->cert_type);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'cert_type',Yii::$app->attr->valueMap(AttrIdEnum::DIA_CERT_TYPE), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style'=> 'width:80px;'
                            ]),
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'cost_price',
                            'filter' => Html::activeTextInput($searchModel, 'cost_price', [
                                'class' => 'form-control',
                                'style'=> 'width:100px;'
                            ]),
                            'headerOptions' => [],
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
                            'label' => '首次入库时间',
                            'attribute'=>'created_at',
                            'filter' => DateRangePicker::widget([    // 日期组件
                                'model' => $searchModel,
                                'attribute' => 'created_at',
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
                                return Yii::$app->formatter->asDate($model->created_at);
                            }

                        ],
                        [
                            'label'=>'库龄',
                            'value'=>function($model){
                                return '需确认规则';
                            },
                            'filter' => false,
                            'headerOptions' => [],
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
                                'style'=> 'width:100px;'

                            ]),
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
                                'style'=> 'width:80px;'
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
                                'style'=> 'width:150px;'
                            ]),
                        ],
//                        [
//                            'class' => 'yii\grid\ActionColumn',
//                            'header' => '操作',
//                            'template' => '{edit} {ajax-apply} {apply-view}',
//                            'buttons' => [
//                                'edit' => function($url, $model, $key){
//                                    if(\Yii::$app->warehouseService->warehouseGoods->editStatus($model)) {
//                                        return Html::edit(['edit', 'id' => $model->id, 'returnUrl' => Url::getReturnUrl()], '编辑', [
//                                            'class' => 'btn btn-primary btn-sm openIframe',
//                                            'data-width' => '90%',
//                                            'data-height' => '90%',
//                                            'data-offset' => '20px',
//                                        ]);
//                                    }
//                                },
//
//                                'ajax-apply' => function($url, $model, $key){
//                                    if(\Yii::$app->warehouseService->warehouseGoods->applyStatus($model)){
//                                        return Html::edit(['ajax-apply','id'=>$model->id], '提审', [
//                                            'class'=>'btn btn-success btn-sm',
//                                            'onclick' => 'rfTwiceAffirm(this,"提交审核", "确定提交吗？");return false;',
//                                        ]);
//                                    }
//                                },
//
//                                'apply-view' => function($url, $model, $key){
//                                    if($model->audit_status == \common\enums\AuditStatusEnum::PENDING){
//                                        return Html::edit(['apply-view','id'=>$model->id], '查看审批', [
//                                            'class'=>'btn btn-danger btn-sm',
//                                        ]);
//                                    }
//                                },
//                                'delete' => function($url, $model, $key){
//                                    return Html::delete(['delete', 'id' => $model->id]);
//                                },
//                            ],
//
//                        ]
                    ]
                ]); ?>
            </div>
        </div>
    </div>
</div>

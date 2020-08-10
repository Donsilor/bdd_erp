<?php

use yii\grid\GridView;
use yii\widgets\ActiveForm;
use common\helpers\Html;
use common\helpers\Url;

use addons\Style\common\enums\AttrIdEnum;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

//$this->title = Yii::t('goods', 'flows');
//$this->params['breadcrumbs'][] = $this->title;
?>
<!--<div class="row">-->
<!--    <div class="col-xs-12">-->
<!--        <div class="box">-->
<!--            <div class="box-body table-responsive">-->
<!--                <div class="col-xs-1">-->
<!--                    --><?//= $searchModel->model->getAttributeLabel('style_sn') ?><!--:-->
<!--                </div>-->
<!--                <div class="col-xs-3">-->
<!--                    --><?//= Html::activeTextInput($searchModel, 'style_sn', [
//                        'class' => 'form-control',
//                    ]);
//                    ?>
<!--                </div>-->
<!--                <div class="col-xs-1">-->
<!--                    --><?//= Html::button('查询',['class'=>'btn btn-info btn-sm','onclick'=>"searchGift()"]) ?>
<!--                </div>-->
<!--            </div>-->
<!--        </div>-->
<!--    </div>-->
<!--</div>-->

<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-body table-responsive">
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'tableOptions' => ['class' => 'table table-hover'],
                    'options' => ['style'=>'white-space:nowrap;width:120%'],
                    'showFooter' => false,//显示footer行
                    'id'=>'grid',
                    'columns' => [
                        [
                            'class' => 'yii\grid\SerialColumn',
                            'visible' => false,
                        ],
                        [
                            'class'=>'yii\grid\RadioButtonColumn',
                            'name'=>'id',  //设置每行数据的复选框属性
                            'headerOptions' => ['width'=>'30'],
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
                            'attribute'=>'style_sn',
                            'filter' => Html::activeTextInput($searchModel, 'style_sn', [
                                'class' => 'form-control',
                                'style'=> 'width:150px;'
                            ]),
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'qiban_sn',
                            'filter' => Html::activeTextInput($searchModel, 'qiban_sn', [
                                'class' => 'form-control',
                                'style'=> 'width:150px;'
                            ]),
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'qiban_type',
                            'value'=> function($model){
                                return \addons\Style\common\enums\QibanTypeEnum::getValue($model->qiban_type);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'qiban_type',\addons\Style\common\enums\QibanTypeEnum::getMap(), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style'=> 'width:100px;'

                            ]),
                            'headerOptions' => [],
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
                            'attribute' => 'goods_source',
                            'format' => 'raw',
                            'headerOptions' => ['class' => 'col-md-1'],
                            'value' => function ($model){
                                return \addons\Warehouse\common\enums\GoodSourceEnum::getValue($model->goods_source);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'goods_source',\addons\Warehouse\common\enums\GoodSourceEnum::getMap(), [
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
                            'attribute' => 'material_type',
                            'value' => function($model){
                                return Yii::$app->attr->valueName($model->material_type);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'material_type',Yii::$app->attr->valueMap(AttrIdEnum::MATERIAL_TYPE), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style'=> 'width:80px;'
                            ]),
                            'headerOptions' => [],
                        ],
                        [
                            'attribute' => 'material_color',
                            'value' => function($model){
                                return Yii::$app->attr->valueName($model->material_color);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'material_color',Yii::$app->attr->valueMap(AttrIdEnum::MATERIAL_COLOR), [
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
                            'attribute'=>'suttle_weight',
                            'filter' => Html::activeTextInput($searchModel, 'suttle_weight', [
                                'class' => 'form-control',
                                'style'=> 'width:60px;'
                            ]),
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'gold_price',
                            'filter' => Html::activeTextInput($searchModel, 'gold_price', [
                                'class' => 'form-control',
                                'style'=> 'width:60px;'
                            ]),
                            'headerOptions' => [],
                        ],

                        [
                            'attribute'=>'gold_amount',
                            'filter' => Html::activeTextInput($searchModel, 'gold_amount', [
                                'class' => 'form-control',
                                'style'=> 'width:60px;'
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
                            'attribute'=>'goods_num',
                            'filter' => Html::activeTextInput($searchModel, 'goods_num', [
                                'class' => 'form-control',
                                'style'=> 'width:60px;'
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
                            'attribute' => 'finger',
                            'value' => function($model){
                                return Yii::$app->attr->valueName($model->finger);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'finger',Yii::$app->attr->valueMap(AttrIdEnum::FINGER), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style'=> 'width:80px;'
                            ]),
                            'headerOptions' => [],
                        ],
                        [
                            'attribute' => 'finger_hk',
                            'value' => function($model){
                                return Yii::$app->attr->valueName($model->finger_hk);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'finger_hk',Yii::$app->attr->valueMap(AttrIdEnum::PORT_NO), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style'=> 'width:80px;'
                            ]),
                            'headerOptions' => [],
                        ],
                        [
                            'attribute' => 'xiangkou',
                            'value' => function($model){
                                return Yii::$app->attr->valueName($model->xiangkou);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'xiangkou',Yii::$app->attr->valueMap(AttrIdEnum::XIANGKOU), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style'=> 'width:80px;'
                            ]),
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
                            'attribute'=>'main_stone_num',
                            'value'=>function($model){
                                return $model->main_stone_num;
                            },
                            'filter' => false,
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'diamond_carat',
                            'value'=>function($model){
                                return $model->diamond_carat;
                            },
                            'filter' => false,
                            'headerOptions' => [],
                        ],
                        [
                            'label'=>'主石规格（颜色/净度/切工/抛光/荧光）',
                            'value'=>function($model){
                                return Yii::$app->attr->valueName($model->diamond_color).'/'.
                                    Yii::$app->attr->valueName($model->diamond_clarity).'/'.
                                    Yii::$app->attr->valueName($model->diamond_cut).'/'.
                                    Yii::$app->attr->valueName($model->diamond_polish).'/'.
                                    Yii::$app->attr->valueName($model->diamond_fluorescence);
                            },
                            'filter' => false,
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
                            'attribute'=>'second_stone_weight1',
                            'value'=>function($model){
                                return $model->second_stone_weight1;
                            },
                            'filter' => false,
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'second_stone_num1',
                            'value'=>function($model){
                                return $model->second_stone_num1;
                            },
                            'filter' => false,
                            'headerOptions' => [],
                        ],
                        [
                            'label'=>'副石规格（颜色/净度）',
                            'value'=>function($model){
                                return $model->second_stone_color1.'/'.
                                    $model->second_stone_clarity1;
                            },
                            'filter' => false,
                            'headerOptions' => [],
                        ],

                        [
                            'label'=>'石料规格',
                            'value'=>function($model){
                                return $model->main_stone_size;
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
                            'attribute' => 'style_channel_id',
                            'format' => 'raw',
                            'headerOptions' => ['class' => 'col-md-1'],
                            'value' => function($model){
                                return $model->channel->name ?? '';
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'style_channel_id',Yii::$app->salesService->saleChannel->getDropDown(), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style'=> 'width:120px;'

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
                            'attribute' => 'supplier_id',
                            'value' =>"supplier.supplier_name",
                            'filter'=>\kartik\select2\Select2::widget([
                                'name'=>'SearchModel[supplier_id]',
                                'value'=>$searchModel->supplier_id,
                                'data'=>Yii::$app->supplyService->supplier->getDropDown(),
                                'options' => ['placeholder' =>"请选择"],
                                'pluginOptions' => [
                                    'allowClear' => true,
                                    'width' => 200
                                ],
                            ]),
                            'format' => 'raw',
                            'headerOptions' => ['class' => 'col-md-2'],
                        ],
                        [
                            'label' => '首次入库时间',
                            'attribute'=>'created_at',
                            'filter' => \kartik\daterange\DateRangePicker::widget([    // 日期组件
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

                    ]
                ]); ?>
            </div>

        </div>
        <?php $form = ActiveForm::begin([
            'id' => $model->formName(),
            'enableAjaxValidation' => false,
            'validationUrl' => Url::to(['select-gift', 'order_id' => $order_id]),
        ]); ?>
        <input type="hidden" name="stock_id" id="stock_id">
        <?php ActiveForm::end(); ?>
    </div>
</div>
<script type="text/javascript">
    function searchGift(){
        let val = $("#searchmodel-style_sn").val();
        let name = $("#searchmodel-style_sn").attr('name');
        $(".filters input[name='" + name + "']").val(val).trigger('change');
    }

    $('input[name="id"]').change(function() {
        if($(this).prop("checked")) {
            var id = $(this).val();
            $("#stock_id").val(id)
        }
    });
</script>
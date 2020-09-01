<?php

use common\helpers\Html;
use common\helpers\Url;
use kartik\select2\Select2;
use yii\grid\GridView;
use kartik\daterange\DateRangePicker;
use addons\Style\common\enums\AttrIdEnum;
use addons\Warehouse\common\enums\GoodsStatusEnum;

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
                <h6 style="color:red">*有款起版的商品（既有款号又有起版号的），下订单只能用起版号下单</h6>
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
                            'attribute'=>'goods_num',
                            'filter' => Html::activeTextInput($searchModel, 'goods_num', [
                                'class' => 'form-control',
                                'style'=> 'width:60px;'
                            ]),
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
                            'attribute'=>'length',
                            'value'=>function($model){
                                return $model->length;
                            },
                            'filter' => false,
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
                            'attribute'=>'kezi',
                            'value'=>function($model){
                                return $model->kezi;
                            },
                            'filter' => false,
                            'headerOptions' => [],
                        ],
                        [
                            'attribute' => 'chain_type',
                            'value' => function($model){
                                return Yii::$app->attr->valueName($model->chain_type);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'chain_type',Yii::$app->attr->valueMap(AttrIdEnum::CHAIN_TYPE), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style'=> 'width:80px;'
                            ]),
                            'headerOptions' => [],
                        ],
                        [
                            'attribute' => 'cramp_ring',
                            'value' => function($model){
                                return Yii::$app->attr->valueName($model->cramp_ring);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'cramp_ring',Yii::$app->attr->valueMap(AttrIdEnum::CHAIN_BUCKLE), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style'=> 'width:80px;'
                            ]),
                            'headerOptions' => [],
                        ],
                        [
                            'attribute' => 'talon_head_type',
                            'value' => function($model){
                                return Yii::$app->attr->valueName($model->talon_head_type);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'talon_head_type',Yii::$app->attr->valueMap(AttrIdEnum::TALON_HEAD_TYPE), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style'=> 'width:80px;'
                            ]),
                            'headerOptions' => [],
                        ],
                        [
                            'attribute' => 'peiliao_way',
                            'value' => function($model){
                                return \addons\Warehouse\common\enums\PeiLiaoWayEnum::getValue($model->peiliao_way);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'peiliao_way',\addons\Warehouse\common\enums\PeiLiaoWayEnum::getMap(), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style'=> 'width:80px;'
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
                                'style'=> 'width:60px;'
                            ]),
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'gross_weight',
                            'filter' => Html::activeTextInput($searchModel, 'gross_weight', [
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

                        /***主石信息开始**/
                        [
                            'attribute' => 'main_peishi_type',
                            'value' => function($model){
                                return \addons\Supply\common\enums\PeishiTypeEnum::getValue($model->main_peishi_type);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'main_peishi_type',\addons\Supply\common\enums\PeishiTypeEnum::getMap(), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style'=> 'width:80px;'
                            ]),
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'main_stone_sn',
                            'filter' => Html::activeTextInput($searchModel, 'main_stone_sn', [
                                'class' => 'form-control',
                                'style'=> 'width:60px;'
                            ]),
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'main_peishi_way',
                            'value' => function($model){
                                return \addons\Warehouse\common\enums\PeiShiWayEnum::getValue($model->main_peishi_way);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'main_peishi_way',\addons\Warehouse\common\enums\PeiShiWayEnum::getMap(), [
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
                            'attribute'=>'diamond_shape',
                            'value' => function($model){
                                return Yii::$app->attr->valueName($model->diamond_shape);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'diamond_shape',Yii::$app->attr->valueMap(AttrIdEnum::DIA_SHAPE), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style'=> 'width:80px;'
                            ]),
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
                            'attribute'=>'main_stone_price',
                            'filter' => Html::activeTextInput($searchModel, 'main_stone_price', [
                                'class' => 'form-control',
                                'style'=> 'width:100px;'
                            ]),
                            'headerOptions' => [],
                        ],
                        [
                            'label'=>'主石成本',
                            'value' => function($model){
                                return round($model->diamond_carat * $model->main_stone_price,2);
                            },
                            'filter' => false,
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'diamond_color',
                            'value' => function($model){
                                return Yii::$app->attr->valueName($model->diamond_color);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'diamond_color',Yii::$app->attr->valueMap(AttrIdEnum::DIA_COLOR), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style'=> 'width:80px;'
                            ]),
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'diamond_clarity',
                            'value' => function($model){
                                return Yii::$app->attr->valueName($model->diamond_clarity);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'diamond_clarity',Yii::$app->attr->valueMap(AttrIdEnum::DIA_CLARITY), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style'=> 'width:80px;'
                            ]),
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'diamond_cut',
                            'value' => function($model){
                                return Yii::$app->attr->valueName($model->diamond_cut);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'diamond_cut',Yii::$app->attr->valueMap(AttrIdEnum::DIA_CUT), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style'=> 'width:80px;'
                            ]),
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'main_stone_colour',
                            'value' => function($model){
                                return Yii::$app->attr->valueName($model->main_stone_colour);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'main_stone_colour',Yii::$app->attr->valueMap(AttrIdEnum::DIA_COLOUR), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style'=> 'width:80px;'
                            ]),
                            'headerOptions' => [],
                        ],

                        /***副石1开始**/
                        [
                            'attribute' => 'second_peishi_type1',
                            'value' => function($model){
                                return \addons\Supply\common\enums\PeishiTypeEnum::getValue($model->second_peishi_type1);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'second_peishi_type1',\addons\Supply\common\enums\PeishiTypeEnum::getMap(), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style'=> 'width:80px;'
                            ]),
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'second_stone_sn1',
                            'filter' => Html::activeTextInput($searchModel, 'second_stone_sn1', [
                                'class' => 'form-control',
                                'style'=> 'width:60px;'
                            ]),
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'second_peishi_way1',
                            'value' => function($model){
                                return \addons\Warehouse\common\enums\PeiShiWayEnum::getValue($model->second_peishi_way1);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'second_peishi_way1',\addons\Warehouse\common\enums\PeiShiWayEnum::getMap(), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style'=> 'width:80px;'
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
                            'attribute'=>'second_stone_num1',
                            'value'=>function($model){
                                return $model->second_stone_num1;
                            },
                            'filter' => false,
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'second_stone_shape1',
                            'value' => function($model){
                                return Yii::$app->attr->valueName($model->second_stone_shape1);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'second_stone_shape1',Yii::$app->attr->valueMap(AttrIdEnum::SIDE_STONE1_SHAPE), [
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
                            'attribute'=>'second_stone_price1',
                            'filter' => Html::activeTextInput($searchModel, 'second_stone_price1', [
                                'class' => 'form-control',
                                'style'=> 'width:100px;'
                            ]),
                            'headerOptions' => [],
                        ],
                        [
                            'label'=>'副石1成本',
                            'value' => function($model){
                                return round($model->second_stone_weight1 * $model->second_stone_price1,2);
                            },
                            'filter' => false,
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'second_stone_color1',
                            'value' => function($model){
                                return Yii::$app->attr->valueName($model->second_stone_color1);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'second_stone_color1',Yii::$app->attr->valueMap(AttrIdEnum::SIDE_STONE1_COLOR), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style'=> 'width:80px;'
                            ]),
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'second_stone_clarity1',
                            'value' => function($model){
                                return Yii::$app->attr->valueName($model->second_stone_clarity1);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'second_stone_clarity1',Yii::$app->attr->valueMap(AttrIdEnum::SIDE_STONE1_CLARITY), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style'=> 'width:80px;'
                            ]),
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'second_stone_colour1',
                            'value' => function($model){
                                return Yii::$app->attr->valueName($model->second_stone_colour1);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'second_stone_colour1',Yii::$app->attr->valueMap(AttrIdEnum::SIDE_STONE1_SECAI), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style'=> 'width:80px;'
                            ]),
                            'headerOptions' => [],
                        ],
                        /***副石2开始**/
                        [
                            'attribute' => 'second_peishi_type2',
                            'value' => function($model){
                                return \addons\Supply\common\enums\PeishiTypeEnum::getValue($model->second_peishi_type2);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'second_peishi_type2',\addons\Supply\common\enums\PeishiTypeEnum::getMap(), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style'=> 'width:80px;'
                            ]),
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'second_peishi_way2',
                            'value' => function($model){
                                return \addons\Warehouse\common\enums\PeiShiWayEnum::getValue($model->second_peishi_way2);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'second_peishi_way2',\addons\Warehouse\common\enums\PeiShiWayEnum::getMap(), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style'=> 'width:80px;'
                            ]),
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'second_stone_sn2',
                            'filter' => Html::activeTextInput($searchModel, 'second_stone_sn2', [
                                'class' => 'form-control',
                                'style'=> 'width:60px;'
                            ]),
                            'headerOptions' => [],
                        ],

                        [
                            'attribute'=>'second_stone_type2',
                            'value' => function($model){
                                return Yii::$app->attr->valueName($model->second_stone_type2);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'second_stone_type2',Yii::$app->attr->valueMap(AttrIdEnum::SIDE_STONE2_TYPE), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style'=> 'width:80px;'
                            ]),
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'second_stone_num2',
                            'value'=>function($model){
                                return $model->second_stone_num2;
                            },
                            'filter' => false,
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'second_stone_shape2',
                            'value' => function($model){
                                return Yii::$app->attr->valueName($model->second_stone_shape2);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'second_stone_shape2',Yii::$app->attr->valueMap(AttrIdEnum::SIDE_STONE2_SHAPE), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style'=> 'width:80px;'
                            ]),
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'second_stone_weight2',
                            'value'=>function($model){
                                return $model->second_stone_weight2;
                            },
                            'filter' => false,
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
                            'label'=>'副石2成本',
                            'value' => function($model){
                                return round($model->second_stone_weight2 * $model->second_stone_price2,2);
                            },
                            'filter' => false,
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'second_stone_color2',
                            'value' => function($model){
                                return Yii::$app->attr->valueName($model->second_stone_color2);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'second_stone_color2',Yii::$app->attr->valueMap(AttrIdEnum::SIDE_STONE2_COLOR), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style'=> 'width:80px;'
                            ]),
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'second_stone_clarity2',
                            'value' => function($model){
                                return Yii::$app->attr->valueName($model->second_stone_clarity2);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'second_stone_clarity2',Yii::$app->attr->valueMap(AttrIdEnum::SIDE_STONE2_CLARITY), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style'=> 'width:80px;'
                            ]),
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'second_stone_colour2',
                            'value' => function($model){
                                return Yii::$app->attr->valueName($model->second_stone_colour2);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'second_stone_colour2',Yii::$app->attr->valueMap(AttrIdEnum::SIDE_STONE2_SECAI), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style'=> 'width:80px;'
                            ]),
                            'headerOptions' => [],
                        ],
                         /**副石2结束**/
                        [
                            'attribute'=>'peijian_way',
                            'value' => function($model){
                                return \addons\Warehouse\common\enums\PeiShiWayEnum::getValue($model->peijian_way);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'peijian_way',\addons\Warehouse\common\enums\PeiJianWayEnum::getMap(), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style'=> 'width:80px;'
                            ]),
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'shiliao_remark',
                            'filter' => false,
                            'headerOptions' => [],
                        ],

                        [
                            'attribute'=>'peijian_cate',
                            'value' => function($model){
                                return \addons\Warehouse\common\enums\PeiJianCateEnum::getValue($model->peijian_cate);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'peijian_cate',\addons\Warehouse\common\enums\PeiJianCateEnum::getMap(), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style'=> 'width:80px;'
                            ]),
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'parts_material',
                            'filter' => false,
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
                            'attribute'=>'parts_price',
                            'filter' => false,
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'parts_amount',
                            'value' => function($model){
                                return $model->parts_amount;
//                                return round($model->parts_gold_weight * $model->parts_price,2);
                            },
                            'filter' => false,
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'parts_fee',
                            'filter' => false,
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'ke_gong_fee',
                            'filter' => false,
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'gong_fee',
                            'filter' => false,
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'peishi_fee',
                            'filter' => false,
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'peishi_amount',
                            'filter' => false,
                            'headerOptions' => [],
                        ],

                        [
                            'attribute'=>'total_gong_fee',
                            'filter' => false,
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'xianqian_price',
                            'filter' => false,
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'xianqian_fee',
                            'filter' => false,
                            'headerOptions' => [],
                        ],

                        [
                            'attribute'=>'biaomiangongyi',
                            'value' => function($model){
                                return Yii::$app->attr->valueName($model->biaomiangongyi);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'biaomiangongyi',Yii::$app->attr->valueMap(AttrIdEnum::FACEWORK), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style'=> 'width:80px;'
                            ]),
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'biaomiangongyi_fee',
                            'filter' => false,
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'fense_fee',
                            'filter' => false,
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'penrasa_fee',
                            'filter' => false,
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'bukou_fee',
                            'filter' => false,
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'edition_fee',
                            'filter' => false,
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'cert_fee',
                            'filter' => false,
                            'headerOptions' => [],
                        ],

                        [
                            'attribute'=>'factory_cost',
                            'filter' => false,
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'cost_price',
                            'filter' => false,
                            'visible' => \common\helpers\Auth::verify(\common\enums\SpecialAuthEnum::VIEW_CAIGOU_PRICE),
                            'headerOptions' => [],
                        ],

                        [
                            'attribute'=>'diamond_cert_type',
                            'value' => function($model){
                                return Yii::$app->attr->valueName($model->diamond_cert_type);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'diamond_cert_type',Yii::$app->attr->valueMap(AttrIdEnum::DIA_CERT_TYPE), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style'=> 'width:80px;'
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
                            'attribute'=>'markup_rate',
                            'filter' => Html::activeTextInput($searchModel, 'markup_rate', [
                                'class' => 'form-control',
                                'style'=> 'width:100px;'
                            ]),
                            'headerOptions' => [],
                        ],



                        [
                            'attribute'=>'outbound_cost',
                            'value'=> function($model){
                                if($model->goods_status == GoodsStatusEnum::IN_SALE || $model->goods_status == GoodsStatusEnum::HAS_SOLD){
                                    return $model->outbound_cost;
                                }else{
                                    return Yii::$app->warehouseService->warehouseGoods->getOutboundCost($model->goods_id);

                                }

                            },
                            'filter' => Html::activeTextInput($searchModel, 'outbound_cost', [
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
                            'filter'=>Select2::widget([
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
                            'attribute' => 'style_sex',
                            'format' => 'raw',
                            'headerOptions' => ['class' => 'col-md-1'],
                            'value' => function ($model){
                                return \addons\Style\common\enums\StyleSexEnum::getValue($model->style_sex);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'style_sex',\addons\Style\common\enums\StyleSexEnum::getMap(), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style'=> 'width:80px;'
                            ]),
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
                                if($model->goods_status == GoodsStatusEnum::IN_SALE || $model->goods_status == GoodsStatusEnum::HAS_SOLD){
                                    return Yii::$app->formatter->asDuration(bcsub ($model->sales_time,$model->created_at));
                                }else{
                                    return Yii::$app->formatter->asDuration(bcsub (time(),$model->created_at));
                                }

                            },
                            'filter' => false,
                            'headerOptions' => [],
                        ],

                    ]
                ]); ?>
            </div>
        </div>
    </div>
</div>

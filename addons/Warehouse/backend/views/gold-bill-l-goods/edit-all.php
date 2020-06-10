<?php


use addons\Style\common\enums\AttrIdEnum;
use addons\Warehouse\common\enums\BillStatusEnum;
use common\helpers\Html;
use yii\grid\GridView;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $searchModel yii\data\ActiveDataProvider */
/* @var $tabList yii\data\ActiveDataProvider */
/* @var $tab yii\data\ActiveDataProvider */
/* @var $bill yii\data\ActiveDataProvider */

$this->title = Yii::t('gold_bill_l_goods', '收货单详情');
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box-body nav-tabs-custom">
    <h2 class="page-header"><?php echo $this->title; ?> - <?php echo $bill->bill_no?></h2>
    <?php echo Html::menuTab($tabList,$tab)?>
    <div class="box-tools" style="float:right;margin-top:-40px; margin-right: 20px;">
        <?php
        if($bill->bill_status == \addons\Warehouse\common\enums\BillStatusEnum::SAVE) {
            echo Html::a('返回列表', ['gold-bill-l-goods/index', 'bill_id' => $bill->id], ['class' => 'btn btn-info btn-xs']);
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
                                'attribute'=>'goods_name',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1'],
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxInput('goods_name', $model->goods_name, ['data-id'=>$model->id]);
                                },
                                'filter' => Html::activeTextInput($searchModel, 'goods_name', [
                                    'class' => 'form-control',
                                    'style'=> 'width:200px;'
                                ]),
                            ],
                            [
                                'attribute'=>'style_sn',
                                'headerOptions' => ['class' => 'col-md-1'],
                                'filter' => Html::activeTextInput($searchModel, 'style_sn', [
                                    'class' => 'form-control',
                                    'style'=> 'width:100px;'
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
                                'attribute'=>'gold_weight',
                                'headerOptions' => [],
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxInput('gold_weight', $model->gold_weight, ['data-id'=>$model->id]);
                                },
                                'filter' => Html::activeTextInput($searchModel, 'gold_weight', [
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                            ],
                            [
                                'attribute'=>'gold_loss',
                                'headerOptions' => [],
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxInput('gold_loss', $model->gold_loss, ['data-id'=>$model->id]);
                                },
                                'filter' => Html::activeTextInput($searchModel, 'gold_loss', [
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                            ],
                            [
                                'attribute'=>'gross_weight',
                                'headerOptions' => [],
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxInput('gross_weight', $model->gross_weight, ['data-id'=>$model->id]);
                                },
                                'filter' => Html::activeTextInput($searchModel, 'gross_weight', [
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                            ],
                            [
                                'attribute'=>'xiangkou',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxInput('xiangkou', $model->xiangkou, ['data-id'=>$model->id]);
                                },
                                'headerOptions' => ['class' => 'col-md-1'],
                                'filter' => Html::activeTextInput($searchModel, 'xiangkou', [
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                            ],
                            [
                                'attribute'=>'finger',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxInput('finger', $model->finger, ['data-id'=>$model->id]);
                                },
                                'headerOptions' => ['class' => 'col-md-1'],
                                'filter' => Html::activeTextInput($searchModel, 'finger', [
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                            ],
                            [
                                'attribute'=>'cert_type',
                                'headerOptions' => [],
                                'filter' => Html::activeTextInput($searchModel, 'cert_type', [
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                            ],
                            [
                                'attribute'=>'cert_id',
                                'headerOptions' => [],
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxInput('cert_id', $model->cert_id, ['data-id'=>$model->id]);
                                },
                                'filter' => Html::activeTextInput($searchModel, 'cert_id', [
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                            ],
                            [
                                'attribute'=>'goods_num',
                                'headerOptions' => [],
                                'filter' => Html::activeTextInput($searchModel, 'goods_num', [
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                            ],
                            [
                                'attribute' => 'material',
                                'value' => function($model){
                                    return Yii::$app->attr->valueName($model->material);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'material',Yii::$app->attr->valueMap(\addons\Style\common\enums\AttrIdEnum::MATERIAL), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                                'headerOptions' => [],
                            ],
                            [
                                'attribute' => 'material_type',
                                'value' => function($model){
                                    return Yii::$app->attr->valueName($model->material_type);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'material_type',Yii::$app->attr->valueMap(\addons\Style\common\enums\AttrIdEnum::MATERIAL_TYPE), [
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
                                'filter' => Html::activeDropDownList($searchModel, 'material_color',Yii::$app->attr->valueMap(\addons\Style\common\enums\AttrIdEnum::MATERIAL_COLOR), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                                'headerOptions' => [],
                            ],
                            [
                                'attribute'=>'diamond_carat',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1'],
                                'filter' => Html::activeTextInput($searchModel, 'diamond_carat', [
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                            ],
                            [
                                'attribute' => 'diamond_color',
                                'value' => function($model){
                                    return Yii::$app->attr->valueName($model->diamond_color);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'diamond_color',Yii::$app->attr->valueMap(\addons\Style\common\enums\AttrIdEnum::DIA_COLOR), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                                'headerOptions' => [],
                            ],
                            [
                                'attribute' => 'diamond_shape',
                                'value' => function($model){
                                    return Yii::$app->attr->valueName($model->diamond_shape);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'diamond_shape',Yii::$app->attr->valueMap(\addons\Style\common\enums\AttrIdEnum::DIA_SHAPE), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                                'headerOptions' => [],
                            ],
                            [
                                'attribute' => 'diamond_clarity',
                                'value' => function($model){
                                    return Yii::$app->attr->valueName($model->diamond_clarity);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'diamond_clarity',Yii::$app->attr->valueMap(\addons\Style\common\enums\AttrIdEnum::DIA_CLARITY), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                                'headerOptions' => [],
                            ],
                            [
                                'attribute' => 'diamond_cut',
                                'value' => function($model){
                                    return Yii::$app->attr->valueName($model->diamond_cut);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'diamond_cut',Yii::$app->attr->valueMap(\addons\Style\common\enums\AttrIdEnum::DIA_CUT), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                                'headerOptions' => [],
                            ],
                            [
                                'attribute' => 'diamond_polish',
                                'value' => function($model){
                                    return Yii::$app->attr->valueName($model->diamond_polish);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'diamond_polish',Yii::$app->attr->valueMap(\addons\Style\common\enums\AttrIdEnum::DIA_POLISH), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                                'headerOptions' => [],
                            ],
                            [
                                'attribute' => 'diamond_symmetry',
                                'value' => function($model){
                                    return Yii::$app->attr->valueName($model->diamond_symmetry);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'diamond_symmetry',Yii::$app->attr->valueMap(\addons\Style\common\enums\AttrIdEnum::DIA_SYMMETRY), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                                'headerOptions' => [],
                            ],
                            [
                                'attribute' => 'diamond_fluorescence',
                                'value' => function($model){
                                    return Yii::$app->attr->valueName($model->diamond_fluorescence);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'diamond_fluorescence',Yii::$app->attr->valueMap(\addons\Style\common\enums\AttrIdEnum::DIA_FLUORESCENCE), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                                'headerOptions' => [],
                            ],
                            [
                                'attribute'=>'diamond_discount',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxInput('diamond_discount', $model->diamond_discount, ['data-id'=>$model->id]);
                                },
                                'headerOptions' => ['class' => 'col-md-1'],
                                'filter' => Html::activeTextInput($searchModel, 'diamond_discount', [
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                            ],
                            [
                                'attribute' => 'diamond_cert_type',
                                'value' => function($model){
                                    return Yii::$app->attr->valueName($model->diamond_cert_type);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'diamond_cert_type',Yii::$app->attr->valueMap(\addons\Style\common\enums\AttrIdEnum::DIA_CERT_TYPE), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:100px;'
                                ]),
                                'headerOptions' => [],
                            ],
                            [
                                'attribute'=>'diamond_cert_id',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxInput('diamond_cert_id', $model->diamond_cert_id, ['data-id'=>$model->id]);
                                },
                                'headerOptions' => ['class' => 'col-md-1'],
                                'filter' => Html::activeTextInput($searchModel, 'diamond_cert_id', [
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                            ],
                            [
                                'attribute' => 'jintuo_type',
                                'value' => function ($model){
                                    return \addons\Style\common\enums\JintuoTypeEnum::getValue($model->jintuo_type);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'jintuo_type',\addons\Style\common\enums\JintuoTypeEnum::getMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:100px;'
                                ]),
                                'headerOptions' => [],
                            ],
                            [
                                'attribute'=>'cost_price',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxInput('cost_price', $model->cost_price, ['data-id'=>$model->id]);
                                },
                                'headerOptions' => ['class' => 'col-md-1'],
                                'filter' => Html::activeTextInput($searchModel, 'cost_price', [
                                    'class' => 'form-control',
                                    'style'=> 'width:100px;'
                                ]),
                            ],
                            [
                                'attribute'=>'market_price',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxInput('market_price', $model->market_price, ['data-id'=>$model->id]);
                                },
                                'headerOptions' => ['class' => 'col-md-1'],
                                'filter' => Html::activeTextInput($searchModel, 'market_price', [
                                    'class' => 'form-control',
                                    'style'=> 'width:100px;'
                                ]),
                            ],
                            [
                                'attribute'=>'length',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxInput('length', $model->length, ['data-id'=>$model->id]);
                                },
                                'headerOptions' => ['class' => 'col-md-1'],
                                'filter' => Html::activeTextInput($searchModel, 'length', [
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                            ],
                            [
                                'attribute'=>'parts_gold_weight',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxInput('parts_gold_weight', $model->parts_gold_weight, ['data-id'=>$model->id]);
                                },
                                'headerOptions' => ['class' => 'col-md-1'],
                                'filter' => Html::activeTextInput($searchModel, 'parts_gold_weight', [
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                            ],
                            [
                                'attribute'=>'parts_num',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxInput('parts_num', $model->parts_num, ['data-id'=>$model->id]);
                                },
                                'headerOptions' => ['class' => 'col-md-1'],
                                'filter' => Html::activeTextInput($searchModel, 'parts_num', [
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                            ],
                            [
                                'attribute' => 'main_stone_type',
                                'value' => function($model){
                                    return Yii::$app->attr->valueName($model->main_stone_type);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'main_stone_type',Yii::$app->attr->valueMap(\addons\Style\common\enums\AttrIdEnum::MAIN_STONE_TYPE), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                                'headerOptions' => [],
                            ],
                            [
                                'attribute'=>'main_stone_num',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxInput('main_stone_num', $model->main_stone_num, ['data-id'=>$model->id]);
                                },
                                'headerOptions' => ['class' => 'col-md-1'],
                                'filter' => Html::activeTextInput($searchModel, 'main_stone_num', [
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                            ],
                            [
                                'attribute'=>'main_stone_price',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxInput('main_stone_price', $model->main_stone_price, ['data-id'=>$model->id]);
                                },
                                'headerOptions' => ['class' => 'col-md-1'],
                                'filter' => Html::activeTextInput($searchModel, 'main_stone_price', [
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                            ],
                            [
                                'attribute' => 'second_stone_type1',
                                'value' => function($model){
                                    return Yii::$app->attr->valueName($model->second_stone_type1);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'second_stone_type1',Yii::$app->attr->valueMap(\addons\Style\common\enums\AttrIdEnum::SIDE_STONE1_TYPE), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                                'headerOptions' => [],
                            ],
                            [
                                'attribute'=>'second_stone_num1',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxInput('second_stone_num1', $model->second_stone_num1, ['data-id'=>$model->id]);
                                },
                                'headerOptions' => ['class' => 'col-md-1'],
                                'filter' => Html::activeTextInput($searchModel, 'second_stone_num1', [
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                            ],
                            [
                                'attribute'=>'second_stone_weight1',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxInput('second_stone_weight1', $model->second_stone_weight1, ['data-id'=>$model->id]);
                                },
                                'headerOptions' => ['class' => 'col-md-1'],
                                'filter' => Html::activeTextInput($searchModel, 'second_stone_weight1', [
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                            ],
                            [
                                'attribute'=>'second_stone_price1',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxInput('second_stone_price1', $model->second_stone_price1, ['data-id'=>$model->id]);
                                },
                                'headerOptions' => ['class' => 'col-md-1'],
                                'filter' => Html::activeTextInput($searchModel, 'second_stone_price1', [
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                            ],
                            [
                                'attribute' => 'second_stone_color1',
                                'value' => function($model){
                                    return Yii::$app->attr->valueName($model->second_stone_color1);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'second_stone_color1',Yii::$app->attr->valueMap(\addons\Style\common\enums\AttrIdEnum::SIDE_STONE1_COLOR), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                                'headerOptions' => [],
                            ],
                            [
                                'attribute' => 'second_stone_clarity1',
                                'value' => function($model){
                                    return Yii::$app->attr->valueName($model->second_stone_clarity1);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'second_stone_clarity1',Yii::$app->attr->valueMap(\addons\Style\common\enums\AttrIdEnum::SIDE_STONE1_CLARITY), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                                'headerOptions' => [],
                            ],
                            [
                                'attribute' => 'second_stone_shape1',
                                'value' => function($model){
                                    return Yii::$app->attr->valueName($model->second_stone_shape1);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'second_stone_shape1',Yii::$app->attr->valueMap(\addons\Style\common\enums\AttrIdEnum::DIA_SHAPE), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                                'headerOptions' => [],
                            ],
                            [
                                'attribute' => 'second_stone_type2',
                                'value' => function($model){
                                    return Yii::$app->attr->valueName($model->second_stone_type2);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'second_stone_type2',Yii::$app->attr->valueMap(\addons\Style\common\enums\AttrIdEnum::SIDE_STONE2_TYPE), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                                'headerOptions' => [],
                            ],
                            [
                                'attribute'=>'second_stone_num2',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxInput('second_stone_num2', $model->second_stone_num2, ['data-id'=>$model->id]);
                                },
                                'headerOptions' => ['class' => 'col-md-1'],
                                'filter' => Html::activeTextInput($searchModel, 'second_stone_num2', [
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                            ],
                            [
                                'attribute'=>'second_stone_weight2',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxInput('second_stone_weight2', $model->second_stone_weight2, ['data-id'=>$model->id]);
                                },
                                'headerOptions' => ['class' => 'col-md-1'],
                                'filter' => Html::activeTextInput($searchModel, 'second_stone_weight2', [
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                            ],
                            [
                                'attribute'=>'second_stone_price2',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxInput('second_stone_price2', $model->second_stone_price2, ['data-id'=>$model->id]);
                                },
                                'headerOptions' => ['class' => 'col-md-1'],
                                'filter' => Html::activeTextInput($searchModel, 'second_stone_price2', [
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                            ],
                            [
                                'attribute'=>'remark',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxInput('remark', $model->remark, ['data-id'=>$model->id]);
                                },
                                'headerOptions' => ['class' => 'col-md-1'],
                                'filter' => Html::activeTextInput($searchModel, 'remark', [
                                    'class' => 'form-control',
                                    'style'=> 'width:160px;'
                                ]),
                            ],
                            [
                                'class' => 'yii\grid\ActionColumn',
                                'header' => '操作',
                                'template' => '{delete}',
                                'buttons' => [
                                    'delete' => function($url, $model, $key) use($bill) {
                                        if($bill->bill_status == BillStatusEnum::SAVE){
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
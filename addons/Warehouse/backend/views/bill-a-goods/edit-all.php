<?php

use common\helpers\Html;
use yii\grid\GridView;
use addons\Style\common\enums\AttrIdEnum;


/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $searchModel yii\data\ActiveDataProvider */
/* @var $tabList yii\data\ActiveDataProvider */
/* @var $tab yii\data\ActiveDataProvider */
/* @var $bill yii\data\ActiveDataProvider */

$this->title = Yii::t('bill_a_goods', '调整单明细');
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box-body nav-tabs-custom">
    <h2 class="page-header"><?= $this->title; ?> - <?= $bill->bill_no?> - <?= \addons\Warehouse\common\enums\BillStatusEnum::getValue($bill->bill_status)?></h2>
    <?php echo Html::menuTab($tabList,$tab)?>
    <div class="box-tools" style="float:right;margin-top:-40px; margin-right: 20px;">
        <?php
        if($bill->bill_status == \addons\Warehouse\common\enums\BillStatusEnum::SAVE) {
            echo Html::create(['ajax-edit', 'bill_id' => $bill->id], '新增货品', [
                'class' => 'btn btn-primary btn-xs',
                'data-toggle' => 'modal',
                'data-target' => '#ajaxModal',
            ]);
            echo '&nbsp;';
            echo Html::a('返回列表', ['bill-a-goods/index', 'bill_id' => $bill->id], ['class' => 'btn btn-white btn-xs']);
        }
        ?>
    </div>
    <div class="tab-content">
        <div class="col-xs-12">
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
                                'attribute'=>'goods_id',
                                'headerOptions' => ['class' => 'col-md-1'],
                                'filter' => Html::activeTextInput($searchModel, 'goods_id', [
                                    'class' => 'form-control',
                                    'style'=> 'width:100px;'
                                ]),
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
                                'attribute'=>'goods.style_sn',
                                'headerOptions' => ['class' => 'col-md-1'],
                                'filter' => Html::activeTextInput($searchModel, 'goods.style_sn', [
                                    'class' => 'form-control',
                                    'style'=> 'width:100px;'
                                ]),
                            ],
                            [
                                'attribute' => 'goods.product_type_id',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1'],
                                'value' => function($model){
                                    return $model->productType->name ?? '';
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'goods.product_type_id',Yii::$app->styleService->productType::getDropDown(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:120px;'

                                ]),
                            ],
                            [
                                'attribute' => 'goods.style_cate_id',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1'],
                                'value' => 'goods.styleCate.name',
                                'filter' => Html::activeDropDownList($searchModel, 'goods.style_cate_id',Yii::$app->styleService->styleCate::getDropDown(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:120px;'

                                ]),
                            ],
                            [
                                'attribute' => 'goods.style_sex',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1'],
                                'value' => function ($model){
                                    return \addons\Style\common\enums\StyleSexEnum::getValue($model->goods->style_sex);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'goods.style_sex',\addons\Style\common\enums\StyleSexEnum::getMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                            ],
                            [
                                'attribute' => 'goods.jintuo_type',
                                'value' => function ($model){
                                    return \addons\Style\common\enums\JintuoTypeEnum::getValue($model->goods->jintuo_type);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'goods.jintuo_type',\addons\Style\common\enums\JintuoTypeEnum::getMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:100px;'
                                ]),
                                'headerOptions' => [],
                            ],
                            [
                                'attribute'=>'goods.produce_sn',
                                'headerOptions' => [],
                                'filter' => Html::activeTextInput($searchModel, 'goods.produce_sn', [
                                    'class' => 'form-control',
                                    'style'=> 'width:120px;'
                                ]),
                            ],
                            [
                                'attribute' => 'goods.material',
                                'value' => function($model){
                                    return Yii::$app->attr->valueName($model->goods->material);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'goods.material',Yii::$app->attr->valueMap(\addons\Style\common\enums\AttrIdEnum::MATERIAL), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                                'headerOptions' => [],
                            ],
                            [
                                'attribute'=>'gold_weight',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxInput('gold_weight', $model->gold_weight, ['data-id'=>$model->id]);
                                },
                                'filter' => Html::activeTextInput($searchModel, 'gold_weight', [
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'gold_weight'],
                            ],
                            [
                                'attribute'=>'gold_loss',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxInput('gold_loss', $model->gold_loss, ['data-id'=>$model->id]);
                                },
                                'filter' => Html::activeTextInput($searchModel, 'gold_loss', [
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'gold_loss'],
                            ],
                            [
                                'attribute'=>'suttle_weight',
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'suttle_weight'],
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxInput('suttle_weight', $model->suttle_weight, ['data-id'=>$model->id]);
                                },
                                'filter' => Html::activeTextInput($searchModel, 'suttle_weight', [
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                            ],
                            [
                                'attribute'=>'gold_price',
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'gold_price'],
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxInput('gold_price', $model->gold_price, ['data-id'=>$model->id]);
                                },
                                'filter' => Html::activeTextInput($searchModel, 'gold_price', [
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                            ],
                            [
                                'attribute'=>'gold_amount',
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'gold_amount'],
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxInput('gold_amount', $model->gold_amount, ['data-id'=>$model->id]);
                                },
                                'filter' => Html::activeTextInput($searchModel, 'gold_amount', [
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                            ],

                            [
                                'attribute' => 'xiangkou',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxSelect($model,'xiangkou', Yii::$app->styleService->styleAttribute->getAttrValueListByStyle($model->goods->style_sn,AttrIdEnum::XIANGKOU), ['data-id'=>$model->id, 'prompt'=>'请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'xiangkou',Yii::$app->attr->valueMap(\addons\Style\common\enums\AttrIdEnum::XIANGKOU), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1 batch_select_full','attr-name'=>'xiangkou','attr-id'=>AttrIdEnum::XIANGKOU],
                            ],
                            [
                                'attribute' => 'finger',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxSelect($model,'finger', Yii::$app->styleService->styleAttribute->getAttrValueListByStyle($model->goods->style_sn,AttrIdEnum::FINGER), ['data-id'=>$model->id, 'prompt'=>'请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'finger',Yii::$app->attr->valueMap(\addons\Style\common\enums\AttrIdEnum::FINGER), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1 batch_select_full','attr-name'=>'finger','attr-id'=>AttrIdEnum::FINGER],

                            ],


                            [
                                'attribute'=>'product_size',
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'product_size'],
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxInput('product_size', $model->product_size, ['data-id'=>$model->id]);
                                },
                                'filter' => Html::activeTextInput($searchModel, 'product_size', [
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                            ],
                            [
                                'attribute'=>'main_stone_sn',
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'main_stone_sn'],
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxInput('main_stone_sn', $model->main_stone_sn, ['data-id'=>$model->id]);
                                },
                                'filter' => Html::activeTextInput($searchModel, 'main_stone_sn', [
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                            ],
                            [
                                'attribute' => 'main_stone_type',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxSelect($model,'main_stone_type', Yii::$app->styleService->styleAttribute->getAttrValueListByStyle($model->goods->style_sn,AttrIdEnum::MAIN_STONE_TYPE), ['data-id'=>$model->id, 'prompt'=>'请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'main_stone_type',Yii::$app->attr->valueMap(\addons\Style\common\enums\AttrIdEnum::MAIN_STONE_TYPE), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1 batch_select_full','attr-name'=>'main_stone_type','attr-id'=>AttrIdEnum::MAIN_STONE_TYPE],
                            ],
                            [
                                'attribute'=>'main_stone_num',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxInput('main_stone_num', $model->main_stone_num, ['data-id'=>$model->id]);
                                },
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'main_stone_num'],
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
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'main_stone_price'],
                                'filter' => Html::activeTextInput($searchModel, 'main_stone_price', [
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                            ],
                            [
                                'attribute'=>'diamond_carat',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxInput('diamond_carat', $model->diamond_carat, ['data-id'=>$model->id]);
                                },
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'diamond_carat'],
                                'filter' => Html::activeTextInput($searchModel, 'diamond_carat', [
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                            ],
                            [
                                'attribute' => 'diamond_color',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxSelect($model,'diamond_color', Yii::$app->styleService->styleAttribute->getAttrValueListByStyle($model->goods->style_sn,AttrIdEnum::DIA_COLOR), ['data-id'=>$model->id, 'prompt'=>'请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'diamond_color',Yii::$app->attr->valueMap(\addons\Style\common\enums\AttrIdEnum::DIA_COLOR), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1 batch_select_full','attr-name'=>'diamond_color','attr-id'=>AttrIdEnum::DIA_COLOR],
                            ],
                            [
                                'attribute' => 'diamond_shape',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxSelect($model,'diamond_shape', Yii::$app->styleService->styleAttribute->getAttrValueListByStyle($model->goods->style_sn,AttrIdEnum::DIA_SHAPE), ['data-id'=>$model->id, 'prompt'=>'请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'diamond_shape',Yii::$app->attr->valueMap(\addons\Style\common\enums\AttrIdEnum::DIA_SHAPE), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1 batch_select_full','attr-name'=>'diamond_shape','attr-id'=>AttrIdEnum::DIA_SHAPE],
                            ],
                            [
                                'attribute' => 'diamond_clarity',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxSelect($model,'diamond_clarity', Yii::$app->styleService->styleAttribute->getAttrValueListByStyle($model->goods->style_sn,AttrIdEnum::DIA_CLARITY), ['data-id'=>$model->id, 'prompt'=>'请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'diamond_clarity',Yii::$app->attr->valueMap(\addons\Style\common\enums\AttrIdEnum::DIA_CLARITY), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1 batch_select_full','attr-name'=>'diamond_clarity','attr-id'=>AttrIdEnum::DIA_CLARITY],
                            ],
                            [
                                'attribute' => 'diamond_cut',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxSelect($model,'diamond_cut', Yii::$app->styleService->styleAttribute->getAttrValueListByStyle($model->goods->style_sn,AttrIdEnum::DIA_CUT), ['data-id'=>$model->id, 'prompt'=>'请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'diamond_cut',Yii::$app->attr->valueMap(\addons\Style\common\enums\AttrIdEnum::DIA_CUT), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1 batch_select_full','attr-name'=>'diamond_cut','attr-id'=>AttrIdEnum::DIA_CUT],
                            ],
                            [
                                'attribute' => 'diamond_polish',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxSelect($model,'diamond_polish', Yii::$app->styleService->styleAttribute->getAttrValueListByStyle($model->goods->style_sn,AttrIdEnum::DIA_POLISH), ['data-id'=>$model->id, 'prompt'=>'请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'diamond_polish',Yii::$app->attr->valueMap(\addons\Style\common\enums\AttrIdEnum::DIA_POLISH), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1 batch_select_full','attr-name'=>'diamond_polish','attr-id'=>AttrIdEnum::DIA_POLISH],
                            ],
                            [
                                'attribute' => 'diamond_symmetry',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxSelect($model,'diamond_symmetry', Yii::$app->styleService->styleAttribute->getAttrValueListByStyle($model->goods->style_sn,AttrIdEnum::DIA_SYMMETRY), ['data-id'=>$model->id, 'prompt'=>'请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'diamond_symmetry',Yii::$app->attr->valueMap(\addons\Style\common\enums\AttrIdEnum::DIA_SYMMETRY), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1 batch_select_full','attr-name'=>'diamond_symmetry','attr-id'=>AttrIdEnum::DIA_SYMMETRY],
                            ],
                            [
                                'attribute' => 'diamond_fluorescence',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxSelect($model,'diamond_fluorescence', Yii::$app->styleService->styleAttribute->getAttrValueListByStyle($model->goods->style_sn,AttrIdEnum::DIA_FLUORESCENCE), ['data-id'=>$model->id, 'prompt'=>'请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'diamond_fluorescence',Yii::$app->attr->valueMap(\addons\Style\common\enums\AttrIdEnum::DIA_FLUORESCENCE), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1 batch_select_full','attr-name'=>'diamond_fluorescence','attr-id'=>AttrIdEnum::DIA_FLUORESCENCE],
                            ],

                            [
                                'attribute' => 'diamond_cert_type',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxSelect($model,'diamond_cert_type', Yii::$app->styleService->styleAttribute->getAttrValueListByStyle($model->goods->style_sn,AttrIdEnum::DIA_CERT_TYPE), ['data-id'=>$model->id, 'prompt'=>'请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'diamond_cert_type',Yii::$app->attr->valueMap(\addons\Style\common\enums\AttrIdEnum::DIA_CERT_TYPE), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:100px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1 batch_select_full','attr-name'=>'diamond_cert_type','attr-id'=>AttrIdEnum::DIA_CERT_TYPE],
                            ],
                            [
                                'attribute'=>'diamond_cert_id',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxInput('diamond_cert_id', $model->diamond_cert_id, ['data-id'=>$model->id]);
                                },
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'diamond_cert_id'],
                                'filter' => Html::activeTextInput($searchModel, 'diamond_cert_id', [
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                            ],


                            [
                                'attribute'=>'second_stone_sn1',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxInput('second_stone_sn1', $model->second_stone_sn1, ['data-id'=>$model->id]);
                                },
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'second_stone_sn1'],
                                'filter' => Html::activeTextInput($searchModel, 'second_stone_sn1', [
                                    'class' => 'form-control',
                                    'style'=> 'width:100px;'
                                ]),
                            ],

                            [
                                'attribute' => 'second_stone_type1',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxSelect($model,'second_stone_type1', Yii::$app->styleService->styleAttribute->getAttrValueListByStyle($model->goods->style_sn,AttrIdEnum::SIDE_STONE1_TYPE), ['data-id'=>$model->id, 'prompt'=>'请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'second_stone_type1',Yii::$app->attr->valueMap(\addons\Style\common\enums\AttrIdEnum::SIDE_STONE1_TYPE), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1 batch_select_full','attr-name'=>'second_stone_type1','attr-id'=>AttrIdEnum::SIDE_STONE1_TYPE],
                            ],
                            [
                                'attribute'=>'second_stone_num1',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxInput('second_stone_num1', $model->second_stone_num1, ['data-id'=>$model->id]);
                                },
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'second_stone_num1'],
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
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'second_stone_weight1'],
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
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'second_stone_price1'],
                                'filter' => Html::activeTextInput($searchModel, 'second_stone_price1', [
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                            ],
                            [
                                'attribute' => 'second_stone_color1',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxSelect($model,'second_stone_color1', Yii::$app->styleService->styleAttribute->getAttrValueListByStyle($model->goods->style_sn,AttrIdEnum::SIDE_STONE1_COLOR), ['data-id'=>$model->id, 'prompt'=>'请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'second_stone_color1',Yii::$app->attr->valueMap(\addons\Style\common\enums\AttrIdEnum::SIDE_STONE1_COLOR), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1 batch_select_full','attr-name'=>'second_stone_color1','attr-id'=>AttrIdEnum::SIDE_STONE1_COLOR],
                            ],
                            [
                                'attribute' => 'second_stone_clarity1',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxSelect($model,'second_stone_clarity1', Yii::$app->styleService->styleAttribute->getAttrValueListByStyle($model->goods->style_sn,AttrIdEnum::SIDE_STONE1_CLARITY), ['data-id'=>$model->id, 'prompt'=>'请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'second_stone_clarity1',Yii::$app->attr->valueMap(\addons\Style\common\enums\AttrIdEnum::SIDE_STONE1_CLARITY), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1 batch_select_full','attr-name'=>'second_stone_clarity1','attr-id'=>AttrIdEnum::SIDE_STONE1_CLARITY],
                            ],
                            [
                                'attribute' => 'second_stone_shape1',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxSelect($model,'second_stone_shape1', Yii::$app->styleService->styleAttribute->getAttrValueListByStyle($model->goods->style_sn,AttrIdEnum::DIA_SHAPE), ['data-id'=>$model->id, 'prompt'=>'请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'second_stone_shape1',Yii::$app->attr->valueMap(\addons\Style\common\enums\AttrIdEnum::DIA_SHAPE), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1 batch_select_full','attr-name'=>'second_stone_shape1','attr-id'=>AttrIdEnum::DIA_SHAPE],
                            ],
                            [
                                'attribute' => 'second_stone_type2',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxSelect($model,'second_stone_type2', Yii::$app->styleService->styleAttribute->getAttrValueListByStyle($model->goods->style_sn,AttrIdEnum::SIDE_STONE2_TYPE), ['data-id'=>$model->id, 'prompt'=>'请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'second_stone_type2',Yii::$app->attr->valueMap(\addons\Style\common\enums\AttrIdEnum::SIDE_STONE2_TYPE), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1 batch_select_full','attr-name'=>'second_stone_type2','attr-id'=>AttrIdEnum::SIDE_STONE2_TYPE],
                            ],
                            [
                                'attribute'=>'second_stone_num2',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxInput('second_stone_num2', $model->second_stone_num2, ['data-id'=>$model->id]);
                                },
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'second_stone_num2'],
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
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'second_stone_weight2'],
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
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'second_stone_price2'],
                                'filter' => Html::activeTextInput($searchModel, 'second_stone_price2', [
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                            ],

                            [
                                'attribute'=>'cost_price',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxInput('cost_price', $model->cost_price, ['data-id'=>$model->id]);
                                },
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'cost_price'],
                                'filter' => Html::activeTextInput($searchModel, 'cost_price', [
                                    'class' => 'form-control',
                                    'style'=> 'width:100px;'
                                ]),
                            ],


                            [
                                'attribute'=>'parts_gold_weight',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxInput('parts_gold_weight', $model->parts_gold_weight, ['data-id'=>$model->id]);
                                },
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'parts_gold_weight'],
                                'filter' => Html::activeTextInput($searchModel, 'parts_gold_weight', [
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                            ],


                            [
                                'attribute'=>'parts_price',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxInput('parts_price', $model->parts_price, ['data-id'=>$model->id]);
                                },
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'parts_price'],
                                'filter' => Html::activeTextInput($searchModel, 'parts_price', [
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                            ],
                            [
                                'attribute'=>'gong_fee',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxInput('gong_fee', $model->gong_fee, ['data-id'=>$model->id]);
                                },
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'gong_fee'],
                                'filter' => Html::activeTextInput($searchModel, 'gong_fee', [
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                            ],
                            [
                                'attribute'=>'bukou_fee',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxInput('bukou_fee', $model->bukou_fee, ['data-id'=>$model->id]);
                                },
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'bukou_fee'],
                                'filter' => Html::activeTextInput($searchModel, 'bukou_fee', [
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                            ],
                            [
                                'attribute'=>'xianqian_fee',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxInput('xianqian_fee', $model->xianqian_fee, ['data-id'=>$model->id]);
                                },
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'xianqian_fee'],
                                'filter' => Html::activeTextInput($searchModel, 'xianqian_fee', [
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                            ],
                            [
                                'attribute'=>'cert_fee',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxInput('cert_fee', $model->cert_fee, ['data-id'=>$model->id]);
                                },
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'cert_fee'],
                                'filter' => Html::activeTextInput($searchModel, 'cert_fee', [
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                            ],


                            [
                                'attribute'=>'biaomiangongyi_fee',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxInput('biaomiangongyi_fee', $model->biaomiangongyi_fee, ['data-id'=>$model->id]);
                                },
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'biaomiangongyi_fee'],
                                'filter' => Html::activeTextInput($searchModel, 'biaomiangongyi_fee', [
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                            ],
                            [
                                'class'=>'yii\grid\CheckboxColumn',
                                'name'=>'id',  //设置每行数据的复选框属性
                            ],

                        ]
                    ]); ?>
                </div>
            </div>
        </div>
        <!-- box end -->
    </div>
    <!-- tab-content end -->
</div>
<script type="text/javascript">
    $(function(){
        $(".batch_full > a").after('&nbsp;<?= Html::batchFullButton(['batch-edit'],"批量填充"); ?>');
        $(".batch_select_full > a").after('&nbsp;<?= Html::batchFullButton(['batch-edit','check'=>1],"批量填充", ['input_type'=>'select']); ?>');
    });
</script>
<?php

use addons\Purchase\common\enums\ReceiptStatusEnum;
use addons\Style\common\enums\AttrIdEnum;
use common\helpers\Html;
use common\helpers\Url;
use kartik\select2\Select2;
use yii\grid\GridView;
use kartik\daterange\DateRangePicker;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('gift_receipt_goods', '赠品收货单详情');
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box-body nav-tabs-custom">
    <h2 class="page-header"><?php echo $this->title; ?> - <?php echo $receipt->receipt_no?> - <?= ReceiptStatusEnum::getValue($receipt->receipt_status)??""; ?></h2>
    <?php echo Html::menuTab($tabList,$tab)?>
    <div class="box-tools" style="float:right;margin-top:-40px; margin-right: 20px;">
        <?php
        if($receipt->receipt_status == \addons\Warehouse\common\enums\BillStatusEnum::SAVE) {
            echo Html::a('返回列表', ['gift-receipt-goods/index', 'receipt_id' => $receipt->id], ['class' => 'btn btn-white btn-xs']);
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
                                'label' => '商品图片',
                                'value' => function ($model) {
                                    return \common\helpers\ImageHelper::fancyBox(Yii::$app->purchaseService->gift->getStyleImage($model),90,90);
                                },
                                'filter' => false,
                                'format' => 'raw',
                                'headerOptions' => ['width'=>'90'],
                            ],
                            [
                                'attribute'=>'goods_name',
                                'filter' => Html::activeTextInput($searchModel, 'goods_name', [
                                    'class' => 'form-control',
                                    'style'=> 'width:200px;'
                                ]),
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxInput('goods_name', $model->goods_name, ['data-id'=>$model->id]);
                                },
                                'format' => 'raw',
                                'headerOptions' => ['width'=>'300'],
                            ],
                            [
                                'attribute'=>'goods_sn',
                                'filter' => Html::activeTextInput($searchModel, 'goods_sn', [
                                    'class' => 'form-control',
                                ]),
                                'value' => function ($model) {
                                    $str = $model->goods_sn;
                                    return $str;
                                },
                                'format' => 'raw',
                                'headerOptions' => ['width'=>'100'],
                            ],
                            [
                                'attribute' => 'style_cate_id',
                                'value' => function ($model){
                                    return $model->cate->name ??'';
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'style_cate_id',Yii::$app->styleService->styleCate->getDropDown(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=>'width:100px'
                                ]),
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
//                            [
//                                'attribute' => 'product_type_id',
//                                'value' => function($model){
//                                    return $model->type->name ?? '';
//                                },
//                                'filter' => Html::activeDropDownList($searchModel, 'product_type_id',Yii::$app->styleService->productType->getDropDown(), [
//                                    'prompt' => '全部',
//                                    'class' => 'form-control',
//                                    'style'=>'width:100px'
//                                ]),
//                                'format' => 'raw',
//                                'headerOptions' => ['class' => 'col-md-1'],
//                            ],
                            [
                                'attribute' => 'style_sex',
                                'format' => 'raw',
                                'value' => function ($model){
                                    return \addons\Style\common\enums\StyleSexEnum::getValue($model->style_sex);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'style_sex',\addons\Style\common\enums\StyleSexEnum::getMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'attribute' => 'material_type',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    //return Yii::$app->attr->valueName($model->material_type)??"";
                                    return  Html::ajaxSelect($model,'material_type', Yii::$app->attr->valueMap(AttrIdEnum::MATERIAL_TYPE), ['data-id'=>$model->id, 'prompt'=>'请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'material_type',Yii::$app->attr->valueMap(\addons\Style\common\enums\AttrIdEnum::MATERIAL_TYPE), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:100px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'attribute' => 'material_color',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    //return Yii::$app->attr->valueName($model->material_color)??"";
                                    return  Html::ajaxSelect($model,'material_color', Yii::$app->attr->valueMap(AttrIdEnum::MATERIAL_COLOR), ['data-id'=>$model->id, 'prompt'=>'请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'material_color',Yii::$app->attr->valueMap(\addons\Style\common\enums\AttrIdEnum::MATERIAL_COLOR), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:100px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'attribute' => 'finger_hk',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    //return Yii::$app->attr->valueName($model->finger_hk)??"";
                                    return  Html::ajaxSelect($model,'finger_hk', Yii::$app->attr->valueMap(AttrIdEnum::PORT_NO), ['data-id'=>$model->id, 'prompt'=>'请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'finger_hk',Yii::$app->attr->valueMap(AttrIdEnum::PORT_NO), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                                'headerOptions' => [],
                            ],
                            [
                                'attribute' => 'finger',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    //return Yii::$app->attr->valueName($model->finger)??"";
                                    return  Html::ajaxSelect($model,'finger', Yii::$app->attr->valueMap(AttrIdEnum::FINGER), ['data-id'=>$model->id, 'prompt'=>'请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'finger',Yii::$app->attr->valueMap(AttrIdEnum::FINGER), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                                'headerOptions' => [],
                            ],
                            [
                                'attribute'=>'chain_length',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxInput('chain_length', $model->chain_length, ['data-id'=>$model->id]);
                                },
                                'filter' => Html::activeTextInput($searchModel, 'chain_length', [
                                    'class' => 'form-control',
                                    'style'=> 'width:100px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'attribute' => 'main_stone_type',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    //return Yii::$app->attr->valueName($model->main_stone_type)??"";
                                    return  Html::ajaxSelect($model,'main_stone_type', Yii::$app->attr->valueMap(AttrIdEnum::MAIN_STONE_TYPE), ['data-id'=>$model->id, 'prompt'=>'请选择']);
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
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'attribute'=>'goods_size',
                                'filter' => Html::activeTextInput($searchModel, 'goods_size', [
                                    'class' => 'form-control',
                                ]),
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxInput('goods_size', $model->goods_size, ['data-id'=>$model->id]);
                                },
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'attribute'=>'goods_num',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxInput('goods_num', $model->goods_num, ['data-id'=>$model->id]);
                                },
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'attribute' => 'goods_weight',
                                'value' => function ($model) {
                                    return $model->goods_weight ;
                                },
                                'filter' => false,
                                'headerOptions' => ['width'=>'150'],
                            ],
                            [
                                'attribute'=>'gold_price',
                                'filter' => false,
                                'value' => function ($model) {
                                    return $model->gold_price ;
                                },
                                'headerOptions' => ['width'=>'150'],
                            ],
                            [
                                'attribute'=>'cost_price',
                                'filter' => false,
                                'value' => function ($model) {
                                    return $model->cost_price ;
                                },
                                'headerOptions' => ['width'=>'150'],
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
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxInput('goods_remark', $model->goods_remark, ['data-id'=>$model->id]);
                                },
                                'filter' => Html::activeTextInput($searchModel, 'goods_remark', [
                                    'class' => 'form-control',
                                    'style'=> 'width:150px;'
                                ]),
                            ],
                            [
                                'class' => 'yii\grid\ActionColumn',
                                'header' => '操作',
                                'contentOptions' => ['style' => ['white-space' => 'nowrap']],
                                'template' => '{edit}',
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
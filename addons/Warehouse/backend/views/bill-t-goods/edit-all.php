<?php


use addons\Warehouse\common\enums\BillStatusEnum;
use common\helpers\Html;
use yii\grid\GridView;
use addons\Style\common\enums\AttrIdEnum;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $searchModel yii\data\ActiveDataProvider */
/* @var $tabList yii\data\ActiveDataProvider */
/* @var $tab yii\data\ActiveDataProvider */
/* @var $bill yii\data\ActiveDataProvider */

$this->title = Yii::t('bill_t_goods', '其他入库单明细');
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
            echo Html::a('返回列表', ['bill-t-goods/index', 'bill_id' => $bill->id], ['class' => 'btn btn-white btn-xs']);
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
                                'class' => 'yii\grid\ActionColumn',
                                'header' => '操作',
                                'contentOptions' => ['style' => ['white-space' => 'nowrap']],
                                'template' => '{ajax-edit} {delete}',
                                'buttons' => [
                                    'ajax-edit' => function($url, $model, $key) use($bill) {
                                        if($bill->bill_status == BillStatusEnum::SAVE){
                                            return Html::edit(['edit', 'id' => $model->id, 'bill_id' => $bill->id], '编辑', [
                                                'class' => 'btn btn-primary btn-xs openIframe',
                                                'data-width' => '90%',
                                                'data-height' => '90%',
                                                'data-offset' => '20px',
                                            ]);
                                        }
                                    },
                                    'delete' => function($url, $model, $key) use($bill) {
                                        if($bill->bill_status == BillStatusEnum::SAVE){
                                            return Html::delete(['delete', 'id' => $model->id],'删除', [
                                                'class' => 'btn btn-danger btn-xs',
                                            ]);
                                        }
                                    },
                                ],
                                'headerOptions' => [],
                            ],
                            [
                                'attribute'=>'goods_name',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'goods_name'],
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxInput('goods_name', $model->goods_name, ['data-id'=>$model->id]);
                                },
                                'filter' => Html::activeTextInput($searchModel, 'goods_name', [
                                    'class' => 'form-control goods_name',
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
                                'attribute'=>'qiban_sn',
                                'headerOptions' => ['class' => 'col-md-1'],
                                'filter' => Html::activeTextInput($searchModel, 'qiban_sn', [
                                    'class' => 'form-control',
                                    'style'=> 'width:100px;'
                                ]),
                            ],
                            [
                                'attribute' => 'qiban_type',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1'],
                                'value' => function ($model){
                                    return \addons\Style\common\enums\QibanTypeEnum::getValue($model->qiban_type);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'qiban_type',\addons\Style\common\enums\QibanTypeEnum::getMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                            ],
                            [
                                'attribute'=>'order_sn',
                                'headerOptions' => ['class' => 'col-md-1', 'attr-name' => 'order_sn'],
                                'format' => 'raw',
                                //'value' => function ($model, $key, $index, $column){
                                //    return  Html::ajaxInput('order_sn', $model->order_sn, ['data-id'=>$model->id]);
                                //},
                                'filter' => Html::activeTextInput($searchModel, 'order_sn', [
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                            ],
                            [
                                'attribute'=>'produce_sn',
                                'headerOptions' => ['class' => 'col-md-1', 'attr-name' => 'produce_sn'],
                                'format' => 'raw',
                                //'value' => function ($model, $key, $index, $column){
                                //    return  Html::ajaxInput('produce_sn', $model->produce_sn, ['data-id'=>$model->id]);
                                //},
                                'filter' => Html::activeTextInput($searchModel, 'produce_sn', [
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                            ],
                            [
                                'attribute'=>'gold_weight',
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'gold_weight'],
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
                                'attribute'=>'gold_loss',
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'gold_loss'],
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
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'gross_weight'],
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
                                'attribute' => 'xiangkou',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxSelect($model,'xiangkou', Yii::$app->attr->valueMap(AttrIdEnum::XIANGKOU), ['data-id'=>$model->id, 'prompt'=>'请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'xiangkou',Yii::$app->attr->valueMap(AttrIdEnum::XIANGKOU), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:100px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1 batch_select_full','attr-name'=>'xiangkou','attr-id'=>AttrIdEnum::XIANGKOU],
                            ],
                            [
                                'attribute' => 'finger',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxSelect($model,'finger', Yii::$app->attr->valueMap(AttrIdEnum::FINGER), ['data-id'=>$model->id, 'prompt'=>'请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'finger',Yii::$app->attr->valueMap(AttrIdEnum::FINGER), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:100px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1 batch_select_full','attr-name'=>'finger','attr-id'=>AttrIdEnum::FINGER],
                            ],
                            [
                                'attribute' => 'cert_type',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxSelect($model,'cert_type', Yii::$app->attr->valueMap(AttrIdEnum::DIA_CERT_TYPE), ['data-id'=>$model->id, 'prompt'=>'请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'cert_type',Yii::$app->attr->valueMap(AttrIdEnum::DIA_CERT_TYPE), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:100px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1 batch_select_full','attr-name'=>'finger','attr-id'=>AttrIdEnum::DIA_CERT_TYPE],
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
                                    'style'=> 'width:100px;'
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
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxSelect($model,'material', Yii::$app->attr->valueMap(\addons\Style\common\enums\AttrIdEnum::MATERIAL), ['data-id'=>$model->id, 'prompt'=>'请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'material',Yii::$app->attr->valueMap(\addons\Style\common\enums\AttrIdEnum::MATERIAL), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1 batch_select_full','attr-name'=>'material','attr-id'=>AttrIdEnum::MATERIAL],
                            ],
                            [
                                'attribute' => 'material_type',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxSelect($model,'material_type', Yii::$app->attr->valueMap(\addons\Style\common\enums\AttrIdEnum::MATERIAL_TYPE), ['data-id'=>$model->id, 'prompt'=>'请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'material_type',Yii::$app->attr->valueMap(\addons\Style\common\enums\AttrIdEnum::MATERIAL_TYPE), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1 batch_select_full','attr-name'=>'material_type','attr-id'=>AttrIdEnum::MATERIAL_TYPE],
                            ],
                            [
                                'attribute' => 'material_color',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxSelect($model,'material_color', Yii::$app->attr->valueMap(\addons\Style\common\enums\AttrIdEnum::MATERIAL_COLOR), ['data-id'=>$model->id, 'prompt'=>'请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'material_color',Yii::$app->attr->valueMap(\addons\Style\common\enums\AttrIdEnum::MATERIAL_COLOR), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1 batch_select_full','attr-name'=>'material_color','attr-id'=>AttrIdEnum::MATERIAL_COLOR],
                            ],
                            [
                                'attribute' => 'is_inlay',
                                'format' => 'raw',
                                'value' => function ($model){
                                    //return \addons\Style\common\enums\InlayEnum::getValue($model->is_inlay);
                                    return  Html::ajaxSelect($model,'is_inlay', \addons\Style\common\enums\InlayEnum::getMap(), ['data-id'=>$model->id, 'prompt'=>'请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'is_inlay',\addons\Style\common\enums\InlayEnum::getMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                                'headerOptions' => [],
                            ],
                            [
                                'attribute'=>'kezi',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1'],
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxInput('kezi', $model->kezi, ['data-id'=>$model->id]);
                                },
                                'filter' => Html::activeTextInput($searchModel, 'kezi', [
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                            ],
                            [
                                'attribute'=>'product_size',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1'],
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxInput('product_size', $model->product_size, ['data-id'=>$model->id]);
                                },
                                'filter' => Html::activeTextInput($searchModel, 'product_size', [
                                    'class' => 'form-control',
                                    'style'=> 'width:100px;'
                                ]),
                            ],
                            [
                                'attribute'=>'chain_long',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1'],
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxInput('chain_long', $model->chain_long, ['data-id'=>$model->id]);
                                },
                                'filter' => Html::activeTextInput($searchModel, 'chain_long', [
                                    'class' => 'form-control',
                                    'style'=> 'width:100px;'
                                ]),
                            ],
                            [
                                'attribute' => 'chain_type',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxSelect($model,'chain_type', Yii::$app->attr->valueMap(AttrIdEnum::CHAIN_TYPE), ['data-id'=>$model->id, 'prompt'=>'请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'chain_type',Yii::$app->attr->valueMap(\addons\Style\common\enums\AttrIdEnum::CHAIN_TYPE), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                                'headerOptions' => [],
                            ],
                            [
                                'attribute' => 'cramp_ring',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxSelect($model,'cramp_ring', Yii::$app->attr->valueMap(AttrIdEnum::CHAIN_BUCKLE), ['data-id'=>$model->id, 'prompt'=>'请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'cramp_ring',Yii::$app->attr->valueMap(\addons\Style\common\enums\AttrIdEnum::CHAIN_BUCKLE), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                                'headerOptions' => [],
                            ],
                            [
                                'attribute' => 'talon_head_type',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxSelect($model,'talon_head_type', Yii::$app->attr->valueMap(AttrIdEnum::TALON_HEAD_TYPE), ['data-id'=>$model->id, 'prompt'=>'请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'talon_head_type',Yii::$app->attr->valueMap(\addons\Style\common\enums\AttrIdEnum::TALON_HEAD_TYPE), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                                'headerOptions' => [],
                            ],
                            [
                                'attribute' => 'xiangqian_craft',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxSelect($model,'xiangqian_craft', Yii::$app->attr->valueMap(AttrIdEnum::XIANGQIAN_CRAFT), ['data-id'=>$model->id, 'prompt'=>'请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'xiangqian_craft',Yii::$app->attr->valueMap(\addons\Style\common\enums\AttrIdEnum::XIANGQIAN_CRAFT), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                                'headerOptions' => [],
                            ],
                            [
                                'attribute' => 'biaomiangongyi',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxSelect($model,'biaomiangongyi', Yii::$app->attr->valueMap(AttrIdEnum::FACEWORK), ['data-id'=>$model->id, 'prompt'=>'请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'biaomiangongyi',Yii::$app->attr->valueMap(AttrIdEnum::FACEWORK), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:100px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1 batch_select_full','attr-name'=>'biaomiangongyi','attr-id'=>AttrIdEnum::FACEWORK],
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
                                    return  Html::ajaxSelect($model,'diamond_color', Yii::$app->attr->valueMap(\addons\Style\common\enums\AttrIdEnum::DIA_COLOR), ['data-id'=>$model->id, 'prompt'=>'请选择']);
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
                                    return  Html::ajaxSelect($model,'diamond_shape', Yii::$app->attr->valueMap(\addons\Style\common\enums\AttrIdEnum::DIA_SHAPE), ['data-id'=>$model->id, 'prompt'=>'请选择']);
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
                                    return  Html::ajaxSelect($model,'diamond_clarity', Yii::$app->attr->valueMap(\addons\Style\common\enums\AttrIdEnum::DIA_SHAPE), ['data-id'=>$model->id, 'prompt'=>'请选择']);
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
                                    return  Html::ajaxSelect($model,'diamond_cut', Yii::$app->attr->valueMap(\addons\Style\common\enums\AttrIdEnum::DIA_CUT), ['data-id'=>$model->id, 'prompt'=>'请选择']);
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
                                    return  Html::ajaxSelect($model,'diamond_polish', Yii::$app->attr->valueMap(\addons\Style\common\enums\AttrIdEnum::DIA_POLISH), ['data-id'=>$model->id, 'prompt'=>'请选择']);
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
                                    return  Html::ajaxSelect($model,'diamond_symmetry', Yii::$app->attr->valueMap(\addons\Style\common\enums\AttrIdEnum::DIA_SYMMETRY), ['data-id'=>$model->id, 'prompt'=>'请选择']);
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
                                    return  Html::ajaxSelect($model,'diamond_fluorescence', Yii::$app->attr->valueMap(\addons\Style\common\enums\AttrIdEnum::DIA_FLUORESCENCE), ['data-id'=>$model->id, 'prompt'=>'请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'diamond_fluorescence',Yii::$app->attr->valueMap(\addons\Style\common\enums\AttrIdEnum::DIA_FLUORESCENCE), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1 batch_select_full','attr-name'=>'diamond_fluorescence','attr-id'=>AttrIdEnum::DIA_FLUORESCENCE],
                            ],
                            [
                                'attribute'=>'diamond_discount',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxInput('diamond_discount', $model->diamond_discount, ['data-id'=>$model->id]);
                                },
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'diamond_discount'],
                                'filter' => Html::activeTextInput($searchModel, 'diamond_discount', [
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                            ],
                            [
                                'attribute' => 'diamond_cert_type',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxSelect($model,'diamond_cert_type', Yii::$app->attr->valueMap(\addons\Style\common\enums\AttrIdEnum::DIA_CERT_TYPE), ['data-id'=>$model->id, 'prompt'=>'请选择']);
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
                                'headerOptions' => ['class' => 'col-md-1'],
                                'filter' => Html::activeTextInput($searchModel, 'diamond_cert_id', [
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                            ],
                            [
                                'attribute' => 'main_stone_colour',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxSelect($model,'main_stone_colour', Yii::$app->attr->valueMap(AttrIdEnum::MAIN_STONE_COLOUR), ['data-id'=>$model->id, 'prompt'=>'请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'main_stone_colour',Yii::$app->attr->valueMap(\addons\Style\common\enums\AttrIdEnum::MAIN_STONE_COLOUR), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                                'headerOptions' => [],
                            ],
                            [
                                'attribute'=>'main_stone_size',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxInput('main_stone_size', $model->main_stone_size, ['data-id'=>$model->id]);
                                },
                                'headerOptions' => ['class' => 'col-md-1'],
                                'filter' => Html::activeTextInput($searchModel, 'main_stone_size', [
                                    'class' => 'form-control',
                                    'style'=> 'width:100px;'
                                ]),
                            ],
                            [
                                'attribute' => 'jintuo_type',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxSelect($model,'jintuo_type', \addons\Style\common\enums\JintuoTypeEnum::getMap(), ['data-id'=>$model->id, 'prompt'=>'请选择']);
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
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'cost_price'],
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
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'market_price'],
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
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'length'],
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
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'parts_gold_weight'],
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
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'parts_num'],
                                'filter' => Html::activeTextInput($searchModel, 'parts_num', [
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                            ],
                            [
                                'attribute'=>'parts_fee',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'parts_fee'],
                                'filter' => Html::activeTextInput($searchModel, 'parts_fee', [
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                            ],
                            [
                                'attribute'=>'parts_price',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'parts_price'],
                                'filter' => Html::activeTextInput($searchModel, 'parts_price', [
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                            ],
                            [
                                'attribute' => 'main_stone_type',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxSelect($model,'main_stone_type', Yii::$app->attr->valueMap(\addons\Style\common\enums\AttrIdEnum::MAIN_STONE_TYPE), ['data-id'=>$model->id, 'prompt'=>'请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'main_stone_type',Yii::$app->attr->valueMap(\addons\Style\common\enums\AttrIdEnum::MAIN_STONE_TYPE), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1 batch_select_full','attr-name'=>'main_stone_type','attr-id'=>AttrIdEnum::MAIN_STONE_TYPE],
                            ],
                            [
                                'attribute'=>'main_stone_sn',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1'],
                                'filter' => Html::activeTextInput($searchModel, 'main_stone_sn', [
                                    'class' => 'form-control',
                                    'style'=> 'width:100px;'
                                ]),
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
                                'attribute'=>'second_cert_id1',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxInput('second_cert_id1', $model->second_cert_id1, ['data-id'=>$model->id]);
                                },
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'second_cert_id1'],
                                'filter' => Html::activeTextInput($searchModel, 'second_cert_id1', [
                                    'class' => 'form-control',
                                    'style'=> 'width:100px;'
                                ]),
                            ],
                            [
                                'attribute' => 'second_stone_type1',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxSelect($model,'second_stone_type1', Yii::$app->attr->valueMap(\addons\Style\common\enums\AttrIdEnum::SIDE_STONE1_TYPE), ['data-id'=>$model->id, 'prompt'=>'请选择']);
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
                                    return  Html::ajaxSelect($model,'second_stone_color1', Yii::$app->attr->valueMap(\addons\Style\common\enums\AttrIdEnum::SIDE_STONE1_COLOR), ['data-id'=>$model->id, 'prompt'=>'请选择']);
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
                                    return  Html::ajaxSelect($model,'second_stone_clarity1', Yii::$app->attr->valueMap(\addons\Style\common\enums\AttrIdEnum::SIDE_STONE1_CLARITY), ['data-id'=>$model->id, 'prompt'=>'请选择']);
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
                                    return  Html::ajaxSelect($model,'second_stone_shape1', Yii::$app->attr->valueMap(\addons\Style\common\enums\AttrIdEnum::DIA_SHAPE), ['data-id'=>$model->id, 'prompt'=>'请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'second_stone_shape1',Yii::$app->attr->valueMap(\addons\Style\common\enums\AttrIdEnum::DIA_SHAPE), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1 batch_select_full','attr-name'=>'second_stone_shape1','attr-id'=>AttrIdEnum::DIA_SHAPE],
                            ],
                            [
                                'attribute' => 'second_stone_color1',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxSelect($model,'second_stone_color1', Yii::$app->attr->valueMap(AttrIdEnum::SIDE_STONE1_COLOR), ['data-id'=>$model->id, 'prompt'=>'请选择']);
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
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxSelect($model,'second_stone_clarity1', Yii::$app->attr->valueMap(AttrIdEnum::SIDE_STONE1_CLARITY), ['data-id'=>$model->id, 'prompt'=>'请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'second_stone_clarity1',Yii::$app->attr->valueMap(\addons\Style\common\enums\AttrIdEnum::SIDE_STONE1_CLARITY), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                                'headerOptions' => [],
                            ],
                            [
                                'attribute'=>'second_stone_size1',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxInput('second_stone_size1', $model->second_stone_size1, ['data-id'=>$model->id]);
                                },
                                'headerOptions' => ['class' => 'col-md-1'],
                                'filter' => Html::activeTextInput($searchModel, 'second_stone_size1', [
                                    'class' => 'form-control',
                                    'style'=> 'width:100px;'
                                ]),
                            ],
                            [
                                'attribute' => 'second_stone_type2',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxSelect($model,'second_stone_type2', Yii::$app->attr->valueMap(\addons\Style\common\enums\AttrIdEnum::SIDE_STONE2_TYPE), ['data-id'=>$model->id, 'prompt'=>'请选择']);
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
                                'attribute' => 'second_stone_shape2',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxSelect($model,'second_stone_shape2', Yii::$app->attr->valueMap(AttrIdEnum::SIDE_STONE2_SHAPE), ['data-id'=>$model->id, 'prompt'=>'请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'second_stone_shape2',Yii::$app->attr->valueMap(\addons\Style\common\enums\AttrIdEnum::SIDE_STONE2_SHAPE), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                                'headerOptions' => [],
                            ],
                            [
                                'attribute' => 'second_stone_color2',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxSelect($model,'second_stone_color2', Yii::$app->attr->valueMap(AttrIdEnum::SIDE_STONE1_COLOR), ['data-id'=>$model->id, 'prompt'=>'请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'second_stone_color2',Yii::$app->attr->valueMap(\addons\Style\common\enums\AttrIdEnum::SIDE_STONE1_COLOR), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                                'headerOptions' => [],
                            ],
                            [
                                'attribute' => 'second_stone_clarity2',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxSelect($model,'second_stone_clarity2', Yii::$app->attr->valueMap(AttrIdEnum::SIDE_STONE1_CLARITY), ['data-id'=>$model->id, 'prompt'=>'请选择']);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'second_stone_clarity2',Yii::$app->attr->valueMap(\addons\Style\common\enums\AttrIdEnum::SIDE_STONE1_CLARITY), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                                'headerOptions' => [],
                            ],
                            [
                                'attribute'=>'second_stone_size2',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxInput('second_stone_size2', $model->second_stone_size2, ['data-id'=>$model->id]);
                                },
                                'headerOptions' => ['class' => 'col-md-1'],
                                'filter' => Html::activeTextInput($searchModel, 'second_stone_size2', [
                                    'class' => 'form-control',
                                    'style'=> 'width:100px;'
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
                                'attribute'=>'markup_rate',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxInput('markup_rate', $model->markup_rate, ['data-id'=>$model->id]);
                                },
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'markup_rate'],
                                'filter' => Html::activeTextInput($searchModel, 'markup_rate', [
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                            ],
                            [
                                'attribute'=>'fense_fee',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxInput('fense_fee', $model->fense_fee, ['data-id'=>$model->id]);
                                },
                                'headerOptions' => ['class' => 'col-md-1 batch_full', 'attr-name' => 'fense_fee'],
                                'filter' => Html::activeTextInput($searchModel, 'fense_fee', [
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
                                'class'=>'yii\grid\CheckboxColumn',
                                'name'=>'id',  //设置每行数据的复选框属性
                            ],
                            [
                                'class' => 'yii\grid\ActionColumn',
                                'header' => '操作',
                                'contentOptions' => ['style' => ['white-space' => 'nowrap']],
                                'template' => '{edit} {delete}',
                                'buttons' => [
                                    'edit' => function($url, $model, $key) use($bill) {
                                        if($bill->bill_status == BillStatusEnum::SAVE){
                                            return Html::edit(['edit', 'id' => $model->id, 'bill_id' => $bill->id], '编辑', [
                                                'class' => 'btn btn-primary btn-xs openIframe',
                                                'data-width' => '90%',
                                                'data-height' => '90%',
                                                'data-offset' => '20px',
                                            ]);
                                        }
                                    },
                                    'delete' => function($url, $model, $key) use($bill) {
                                        if($bill->bill_status == BillStatusEnum::SAVE){
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
<script type="text/javascript">
    $(function(){
        $(".batch_full > a").after('&nbsp;<?= Html::batchFullButton(['batch-edit'],"批量填充"); ?>');
        $(".batch_select_full > a").after('&nbsp;<?= Html::batchFullButton(['batch-edit','check'=>1],"批量填充", ['input_type'=>'select']); ?>');
    });
</script>
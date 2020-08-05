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

$this->title = Yii::t('gold_bill_l_goods', '领料单详情');
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
                                'attribute'=>'gold_name',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxInput('gold_name', $model->gold_name, ['data-id'=>$model->id]);
                                },
                                'filter' => Html::activeTextInput($searchModel, 'gold_name', [
                                    'class' => 'form-control',
                                    'style'=> 'width:200px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-2'],
                            ],
                            [
                                'attribute'=>'style_sn',
                                'format' => 'raw',
                                'filter' => Html::activeTextInput($searchModel, 'style_sn', [
                                    'class' => 'form-control',
                                    'style'=> 'width:100px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-2'],
                            ],
                            [
                                'attribute' => 'gold_type',
                                'format' => 'raw',
                                'value' => function ($model){
                                    return Yii::$app->attr->valueName($model->gold_type);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'gold_type',Yii::$app->attr->valueMap(AttrIdEnum::MAT_GOLD_TYPE), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:100px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-2'],
                            ],
                            [
                                'attribute' => 'gold_weight',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxInput('gold_weight', $model->gold_weight, ['data-id'=>$model->id]);
                                },
                                'filter' => Html::activeTextInput($searchModel, 'gold_weight', [
                                    'class' => 'form-control',
                                    'style'=> 'width:200px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-2'],
                            ],
                            [
                                'attribute' => 'gold_price',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxInput('gold_price', $model->gold_price, ['data-id'=>$model->id]);
                                },
                                'filter' => Html::activeTextInput($searchModel, 'gold_price', [
                                    'class' => 'form-control',
                                    'style'=> 'width:200px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-2'],
                            ],
                            [
                                'attribute' => 'cost_price',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxInput('cost_price', $model->cost_price, ['data-id'=>$model->id]);
                                },
                                'filter' => Html::activeTextInput($searchModel, 'cost_price', [
                                    'class' => 'form-control',
                                    'style'=> 'width:200px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-2'],
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
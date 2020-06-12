<?php

use addons\Style\common\enums\AttrIdEnum;
use common\helpers\Html;
use common\helpers\Url;
use kartik\select2\Select2;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use kartik\daterange\DateRangePicker;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('stone_bill_ms_goods', '入库单明细');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="box-body nav-tabs-custom">
    <h2 class="page-header"><?php echo $this->title; ?> - <?php echo $bill->bill_no?></h2>
    <?php echo Html::menuTab($tabList,$tab)?>
    <div style="float:right;margin-top:-40px;margin-right: 20px;">
        <?php
        if($bill->bill_status == \addons\Warehouse\common\enums\BillStatusEnum::SAVE){
            echo Html::edit(['edit-all', 'bill_id' => $bill->id], '编辑货品', ['class'=>'btn btn-info btn-xs']);
        }
        ?>
    </div>
    <div class="tab-content" style="padding-right: 10px;">
        <div class="row col-xs-12" style="padding-left: 0px;padding-right: 0px;">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title"><?= Html::encode($this->title) ?></h3>
                </div>
                <div class="box-body table-responsive" style="padding-left: 0px;padding-right: 0px;">
                    <?php echo Html::batchButtons(false)?>
                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'tableOptions' => ['class' => 'table table-hover'],
                        'options' => ['style'=>' width:120%; white-space:nowrap;'],
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
                            /*[
                                'label' => 'ID',
                                'attribute' => 'id',
                                'filter' => false,
                                'format' => 'raw',
                            ],*/
                            [
                                'label' => '石包',
                                'attribute'=>'stone_name',
                                'filter' => true,
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'label' => '石包类型',
                                'attribute' => 'stone_type',
                                'value' => function ($model){
                                    return Yii::$app->attr->valueName($model->stone_type);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'stone_type',Yii::$app->attr->valueMap(AttrIdEnum::MAT_STONE_TYPE), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:100px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'label' => '石包总数',
                                'attribute' => 'stone_num',
                                'filter' => true,
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'label' => '石包总重量',
                                'attribute' => 'stone_weight',
                                'filter' => true,
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'label' => '每卡采购价格',
                                'attribute' => 'cost_price',
                                'filter' => true,
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'label' => '每卡销售价格',
                                'attribute' => 'sale_price',
                                'filter' => true,
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'label' => '证书号',
                                'attribute' => 'cert_id',
                                'headerOptions' => ['class' => 'col-md-1'],
                                'filter' => true,
                            ],
                            [
                                'label' => '石重',
                                'attribute' => 'carat',
                                'filter' => true,
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'label' => '颜色',
                                'attribute' => 'color',
                                'value' => function($model){
                                    return Yii::$app->attr->valueName($model->color);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'color',Yii::$app->attr->valueMap(AttrIdEnum::DIA_COLOR), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:100px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'label' => '净度',
                                'attribute' => 'clarity',
                                'value' => function($model){
                                    return Yii::$app->attr->valueName($model->clarity);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'clarity',Yii::$app->attr->valueMap(AttrIdEnum::DIA_CLARITY), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:100px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'label' => '切工',
                                'attribute' => 'cut',
                                'value' => function($model){
                                    return Yii::$app->attr->valueName($model->cut);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'cut',Yii::$app->attr->valueMap(AttrIdEnum::DIA_CUT), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:100px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'label' => '抛光',
                                'attribute' => 'polish',
                                'value' => function($model){
                                    return Yii::$app->attr->valueName($model->polish);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'polish',Yii::$app->attr->valueMap(AttrIdEnum::DIA_POLISH), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:100px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'label' => '对称',
                                'attribute' => 'symmetry',
                                'value' => function($model){
                                    return Yii::$app->attr->valueName($model->symmetry);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'symmetry',Yii::$app->attr->valueMap(AttrIdEnum::DIA_SYMMETRY), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:100px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'label' => '荧光',
                                'attribute' => 'fluorescence',
                                'value' => function($model){
                                    return Yii::$app->attr->valueName($model->fluorescence);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'fluorescence',Yii::$app->attr->valueMap(AttrIdEnum::DIA_FLUORESCENCE), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:100px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'class' => 'yii\grid\ActionColumn',
                                'header' => '操作',
                                'template' => '{delete}',
                                'buttons' => [
                                    'delete' => function($url, $model, $key) use($bill){
                                        if($bill->bill_status == \addons\Warehouse\common\enums\BillStatusEnum::SAVE){
                                            return Html::delete(['delete', 'id' => $model->id]);
                                        }

                                    },
                                ],
                                'headerOptions' => ['class' => 'col-md-2'],
                            ]
                        ]
                    ]); ?>
                </div>
            </div>
        </div>
    </div>
</div>

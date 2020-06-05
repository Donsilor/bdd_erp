<?php

use common\helpers\Html;
use common\helpers\Url;
use kartik\select2\Select2;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use kartik\daterange\DateRangePicker;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('stone_bill_ms_detail', '买石单明细');
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
                        'options' => ['style'=>' width:140%; white-space:nowrap;'],
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
                                'attribute' => 'id',
                                'filter' => false,
                                'format' => 'raw',
                            ],
                            [
                                'attribute'=>'shibao',
                                'filter' => true,
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'attribute' => 'cert_id',
                                'headerOptions' => ['class' => 'col-md-1'],
                                'filter' => true,
                            ],
                            [
                                'attribute' => 'carat',
                                'filter' => true,
                                'headerOptions' => ['class' => 'col-md-2'],
                            ],
                            [
                                'attribute' => 'color',
                                'value' => function($model){
                                    return Yii::$app->attr->valueName($model->color);
                                },
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'attribute' => 'clarity',
                                'value' => function($model){
                                    return Yii::$app->attr->valueName($model->clarity);
                                },
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'attribute' => 'cut',
                                'value' => function($model){
                                    return Yii::$app->attr->valueName($model->cut);
                                },
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'attribute' => 'polish',
                                'value' => function($model){
                                    return Yii::$app->attr->valueName($model->cut);
                                },
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'attribute' => 'fluorescence',
                                'value' => function($model){
                                    return Yii::$app->attr->valueName($model->fluorescence);
                                },
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'attribute' => 'fluorescence',
                                'value' => function($model){
                                    return Yii::$app->attr->valueName($model->symmetry);
                                },
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'attribute' => 'stone_num',
                                'filter' => true,
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'attribute' => 'stone_weight',
                                'filter' => true,
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'attribute' => 'purchase_price',
                                'filter' => true,
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'attribute' => 'sale_price',
                                'filter' => true,
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
                                'headerOptions' => ['class' => 'col-md-3'],
                            ]
                        ]
                    ]); ?>
                </div>
            </div>
        </div>
    </div>
</div>

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

$this->title = Yii::t('return_goods', '退款单明细');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="box-body nav-tabs-custom">
    <h2 class="page-header"><?= $this->title; ?> - <?= $return->return_no?> - <?= \addons\Sales\common\enums\CheckStatusEnum::getValue($return->check_status)?></h2>
    <?php echo Html::menuTab($tabList,$tab)?>
    <div style="float:right;margin-top:-40px;margin-right: 20px;">
<!--        --><?php
//        if($bill->bill_status == \addons\Warehouse\common\enums\BillStatusEnum::SAVE){
//            echo Html::edit(['edit-all', 'bill_id' => $bill->id], '编辑货品', ['class'=>'btn btn-info btn-xs']);
//        }
//        ?>
    </div>
    <div class="tab-content">
        <div class="row col-xs-12">
            <div class="box">
                <div class="box-body table-responsive">
                    <?php echo Html::batchButtons(false)?>
<!--                    <span class="summary" style="font-size:16px">-->
<!--                        <span style="font-weight:bold;">明细汇总：</span>-->
<!--                        金料总重：<span style="color:green;">--><?//= $bill->total_weight?><!--/克</span>-->
<!--                        金料总额：<span style="color:green;">--><?//= $bill->total_cost?><!--</span>-->
<!--                    </span>-->
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
                                'attribute' => 'id',
                                'filter' => false,
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'attribute'=>'return_no',
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-2'],
                            ],
                            [
                                'attribute'=>'goods_id',
                                'filter' => true,
                                'headerOptions' => ['class' => 'col-md-2'],
                            ],
                            [
                                'attribute'=>'goods_name',
                                'filter' => true,
                                'headerOptions' => ['class' => 'col-md-2'],
                            ],
                            [
                                'attribute'=>'goods_num',
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'attribute' => 'should_amount',
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
//                            [
//                                'attribute' => 'apply_amount',
//                                'filter' => true,
//                                'headerOptions' => ['class' => 'col-md-1'],
//                            ],
                            [
                                'attribute' => 'real_amount',
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
//                            [
//                                'attribute' => 'incl_tax_price',
//                                'filter' => true,
//                                'headerOptions' => ['class' => 'col-md-1'],
//                            ],
//                            /*[
//                                'attribute' => 'sale_price',
//                                'filter' => true,
//                                'headerOptions' => ['class' => 'col-md-2'],
//                            ],*/
                            [
                                'attribute' => 'remark',
                                'filter' => true,
                                'headerOptions' => ['class' => 'col-md-2'],
                            ],
                            [
                                'class' => 'yii\grid\ActionColumn',
                                'header' => '操作',
                                'contentOptions' => ['style' => ['white-space' => 'nowrap']],
                                'template' => '{edit}',
                                'buttons' => [
                                    'edit' => function($url, $model, $key) use($return){
                                        if($return->audit_status == \common\enums\AuditStatusEnum::SAVE) {
                                            return Html::edit(['ajax-edit', 'id' => $model->id, 'returnUrl' => Url::getReturnUrl()], '编辑', [
                                                'class' => 'btn btn-info btn-xs',
                                                'data-toggle' => 'modal',
                                                'data-target' => '#ajaxModal',
                                            ]);
                                        }
                                    },
                                    'delete' => function($url, $model, $key) use($return){
                                        if($return->audit_status == \common\enums\AuditStatusEnum::SAVE){
                                            return Html::delete(['delete', 'id' => $model->id],'删除', [
                                                'class' => 'btn btn-danger btn-xs',
                                            ]);
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

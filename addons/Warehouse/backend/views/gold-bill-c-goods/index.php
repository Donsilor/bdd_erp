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

$this->title = Yii::t('gold_bill_c_goods', '领料单明细');
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
                <div class="box-body table-responsive">
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
                                'visible' => true,
                                'headerOptions' => ['class' => 'col-md-1','style'=>'width:30px'],
                            ],
                            [
                                'attribute'=>'gold_sn',
                                'filter' => true,
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'attribute'=>'gold_name',
                                'filter' => true,
                                'headerOptions' => ['class' => 'col-md-2'],
                            ],
                            [
                                'attribute'=>'style_sn',
                                'filter' => true,
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'attribute' => 'gold_type',
                                'value' => function ($model){
                                    return Yii::$app->attr->valueName($model->gold_type);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'gold_type',Yii::$app->attr->valueMap(AttrIdEnum::MAT_GOLD_TYPE), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:100px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'attribute' => 'gold_weight',
                                'filter' => true,
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'attribute' => 'gold_price',
                                'filter' => true,
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'attribute' => 'cost_price',
                                'filter' => true,
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'label' => '布产编号',
                                'value' => function ($model){
                                    return $model->produceGold->produce_sn ?? '';
                                },
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'label' => '配料状态',
                                'value' => function ($model){
                                    return \addons\Supply\common\enums\PeiliaoStatusEnum::getValue($model->produceGold->peiliao_status ??0);
                                },
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'class' => 'yii\grid\ActionColumn',
                                'header' => '操作',
                                'template' => '{delete}',
                                'buttons' => [
                                    'delete' => function($url, $model, $key) use($bill){
                                        if($bill->bill_status == \addons\Warehouse\common\enums\GoldBillStatusEnum::SAVE){
                                            return Html::delete(['delete', 'id' => $model->id]);
                                        }

                                    },
                                ],
                                //'headerOptions' => ['class' => 'col-md-1'],
                            ]
                        ]
                    ]); ?>
                </div>
            </div>
        </div>
    </div>
</div>

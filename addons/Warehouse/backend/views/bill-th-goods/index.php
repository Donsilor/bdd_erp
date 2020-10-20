<?php

use common\helpers\Html;
use common\helpers\Url;
use yii\grid\GridView;
use addons\Warehouse\common\enums\BillStatusEnum;
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = Yii::t('bill_c_goods', '其它退货单明细');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box-body nav-tabs-custom">
    <h2 class="page-header"><?php echo $this->title; ?> - <?php echo $bill->bill_no ?> - <?= \addons\Warehouse\common\enums\BillStatusEnum::getValue($bill->bill_status) ?></h2>
    <?php echo Html::menuTab($tabList, $tab) ?>
    <div style="float:right;margin-top:-40px;margin-right: 20px;">
        <?php
        if ($bill->bill_status == BillStatusEnum::SAVE) {
             echo Html::create(['add', 'bill_id' => $bill->id], '商品批量添加', [
                'class' => 'btn btn-primary btn-xs openIframe',
                'data-width' => '90%',
                'data-height' => '90%',
                'data-offset' => '20px',
            ]);
            echo '&nbsp;'; 
            echo Html::edit(['edit-all', 'bill_id' => $bill->id, 'scan' => 1,'returnUrl'=>Yii::$app->request->get('returnUrl')], '商品扫码添加', ['class' => 'btn btn-success btn-xs']);
            echo '&nbsp;';
            echo Html::edit(['edit-all', 'bill_id' => $bill->id,'returnUrl'=>Yii::$app->request->get('returnUrl')], '编辑货品', ['class' => 'btn btn-info btn-xs']);
            echo '&nbsp;';

        }
        ?>
    </div>
    <div class="tab-content">
        <div class="row col-xs-12">
            <div class="box">
                <div class="box-body table-responsive">
                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'tableOptions' => ['class' => 'table table-hover'],
                        //'options' => ['style'=>' width:120%;white-space:nowrap;'],
                        'options' => ['style' => 'white-space:nowrap;font-size:12px;'],
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
                                'attribute' => 'goods_id',
                                'filter' => true,
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'attribute' => 'style_sn',
                                'headerOptions' => ['class' => 'col-md-1'],
                                'filter' => true,
                            ],
                            [
                                'attribute' => 'goods_name',
                                'filter' => true,
                                'headerOptions' => ['class' => 'col-md-2'],
                            ],                            
                            [
                                'attribute' => 'goods_num',
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],                            
                            [
                                'attribute' => 'goods.goods_status',
                                'value' => function ($model) {
                                    return \addons\Warehouse\common\enums\GoodsStatusEnum::getValue($model->goods->goods_status);
                                },
                                'filter' => true,
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],                            
                            [
                                'attribute' => 'to_warehouse_id',
                                'value' => "toWarehouse.name",
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'attribute' => 'material_type',
                                'value' => function ($model) {
                                    return Yii::$app->attr->valueName($model->material_type) ?? "";
                                },
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'attribute' => 'material_color',
                                'value' => function ($model) {
                                    return Yii::$app->attr->valueName($model->material_color) ?? "";
                                },
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'label' => '手寸',
                                'value' => function ($model) {
                                    $finger = '';
                                    if ($model->goods->finger ?? false) {
                                        $finger .= Yii::$app->attr->valueName($model->goods->finger) . '(US)';
                                    }
                                    if ($model->goods->finger_hk ?? false) {
                                        $finger .= ' ' . Yii::$app->attr->valueName($model->goods->finger_hk) . '(HK)';
                                    }
                                    return $finger;
                                },
                                'filter' => false,
                            ],
                            [
                                'label' => '连石重',
                                'value' => function ($model) {
                                    return $model->goods->suttle_weight ?? '';
                                },
                                'filter' => false,
                            ],
                            [
                                'attribute' => 'goods.main_stone_type',
                                'value' => function ($model) {
                                    if ($model->goods->main_stone_type) {
                                        return Yii::$app->attr->valueName($model->goods->main_stone_type) ?? "";
                                    }
                                    return "";
                                },
                                'filter' => false,
                            ],
                            [
                                'attribute' => 'goods.diamond_carat',
                                'filter' => false,
                            ],
                            [
                                'attribute' => 'goods.main_stone_num',
                                'filter' => false,
                            ],                                                     
                            [
                                'attribute' => 'cost_price',
                                'visible' => \common\helpers\Auth::verify(\common\enums\SpecialAuthEnum::VIEW_CAIGOU_PRICE),
                                'filter' => false,
                            ],
                            [
                                'attribute' => 'cost_amount',
                                'value' => function ($model) {
                                    return $model->cost_price * $model->goods_num;
                                },
                                'visible' => \common\helpers\Auth::verify(\common\enums\SpecialAuthEnum::VIEW_CAIGOU_PRICE),
                                'filter' => false,
                            ],
                            [
                                'class' => 'yii\grid\ActionColumn',
                                'header' => '操作',
                                'template' => '{delete}',
                                'buttons' => [
                                    'delete' => function($url, $model, $key) use($bill){
                                        if($bill->bill_status == BillStatusEnum::SAVE){
                                            return Html::delete(['delete', 'id' => $model->id],'删除',['class'=>'btn btn-danger btn-xs']);
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

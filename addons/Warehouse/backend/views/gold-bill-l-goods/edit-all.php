<?php


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
                                'label' => 'ID',
                                'attribute' => 'id',
                                'filter' => false,
                                'format' => 'raw',
                            ],
                            [
                                'label' => '金料名称',
                                'attribute'=>'gold_name',
                                'format' => 'raw',
                                'value' => function($model){
                                    return Html::ajaxInput('gold_name',$model->gold_name);
                                },
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-2'],
                            ],
                            [
                                'label' => '金料类型',
                                'attribute' => 'gold_type',
                                'value' => function ($model){
                                    return \addons\Style\common\enums\MaterialTypeEnum::getValue($model->gold_type);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'gold_type',\addons\Style\common\enums\MaterialTypeEnum::getMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:100px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-2'],
                            ],
                            [
                                'label' => '金料总数',
                                'attribute' => 'gold_num',
                                'format' => 'raw',
                                'value' => function($model){
                                    return Html::ajaxInput('gold_num',$model->gold_num);
                                },
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-2'],
                            ],
                            [
                                'label' => '金料总重量',
                                'attribute' => 'gold_weight',
                                'format' => 'raw',
                                'value' => function($model){
                                    return Html::ajaxInput('gold_weight',$model->gold_weight);
                                },
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-2'],
                            ],
                            [
                                'label' => '成本价',
                                'attribute' => 'cost_price',
                                'format' => 'raw',
                                'value' => function($model){
                                    return Html::ajaxInput('cost_price',$model->cost_price);
                                },
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'label' => '销售价',
                                'attribute' => 'sale_price',
                                'format' => 'raw',
                                'value' => function($model){
                                    return Html::ajaxInput('sale_price',$model->sale_price);
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
                                        if($bill->audit_status == \common\enums\AuditStatusEnum::PENDING){
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
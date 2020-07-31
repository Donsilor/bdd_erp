<?php

use addons\Style\common\enums\AttrIdEnum;
use addons\Warehouse\common\enums\GoldStatusEnum;
use common\helpers\Html;
use common\helpers\Url;
use kartik\select2\Select2;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use kartik\daterange\DateRangePicker;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('parts', '领件信息');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="box-body nav-tabs-custom">
    <h2 class="page-header"><?= $this->title; ?> - <?= $parts->parts_sn?> - <?= \addons\Warehouse\common\enums\PartsStatusEnum::getValue($parts->parts_status)?></h2>
    <?php echo Html::menuTab($tabList,$tab)?>
    <div class="tab-content">
        <div class="row col-xs-12">
            <div class="box">
                <div class="box-body table-responsive">
                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'tableOptions' => ['class' => 'table table-hover'],
                        'showFooter' => false,//显示footer行
                        'id'=>'grid',
                        'columns' => [
                            [
                                'class' => 'yii\grid\SerialColumn',
                                'visible' => true,
                                'headerOptions' => ['class' => 'col-md-1','style'=>'width:30px'],
                            ],
                            [
                                'label' => '领件单号',
                                'value' => function ($model){
                                    return $model->bill->bill_no ?? '';
                                },
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-2'],
                            ],
                            [
                                'label' => '领件时间',
                                'attribute'=>'audit_time',
                                'filter' => false,
                                'value' => function($model){
                                    if($model->bill->audit_time){
                                        return Yii::$app->formatter->asDatetime($model->bill->audit_time) ?? "";
                                    }
                                    return "";
                                },
                            ],
                            [
                                'label' => '布产编号',
                                'value' => function ($model){
                                    return $model->produceParts->produce_sn ?? '';
                                },
                                'filter' => false,
                            ],
                            [
                                'label' => '订单号',
                                'value' => function ($model){
                                    return $model->produceParts->from_order_sn ?? '';
                                },
                                'filter' => false,
                            ],
                            /*[
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
                            ],*/
                            [
                                'label' => '领料克重(g)',
                                'attribute' => 'parts_weight',
                                'filter' => false,
                            ],
                            /*[
                                'attribute' => 'gold_price',
                                'filter' => true,
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'attribute' => 'cost_price',
                                'filter' => true,
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],*/
                            [
                                'label' => '状态',
                                'value' => function ($model){
                                    return \addons\Supply\common\enums\PeijianStatusEnum::getValue($model->produceParts->peijian_status ??0);
                                },
                                'filter' => false,
                            ],
                        ]
                    ]); ?>
                </div>
            </div>
        </div>
    </div>
</div>

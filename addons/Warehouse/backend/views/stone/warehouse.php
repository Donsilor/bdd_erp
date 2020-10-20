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

$this->title = Yii::t('stone', '出入库信息');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="box-body nav-tabs-custom">
    <h2 class="page-header"><?= $this->title; ?> - <?= $stone->stone_sn?> - <?= GoldStatusEnum::getValue($stone->stone_status)?></h2>
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
                                'label' => '出/入库时间',
                                'filter' => false,
                                'value' => function($model){
                                    if($model->created_at){
                                        return Yii::$app->formatter->asDatetime($model->created_at) ?? "";
                                    }
                                    return "";
                                },
                            ],
                            [
                                'label' => '单据类型',
                                'filter' => false,
                                'value' => function($model){
                                    if($model->bill_type){
                                        return \addons\Warehouse\common\enums\StoneBillTypeEnum::getValue($model->bill_type);
                                    }
                                    return "";
                                },
                            ],
                            [
                                'label' => '单据编号',
                                'value' => function ($model){
                                    return $model->bill_no ?? '';
                                },
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-2'],
                            ],

                            [
                                'label' => '单据状态',
                                'filter' => false,
                                'value' => function($model){
                                    if($model->bill->bill_status ?? false){
                                        return \addons\Warehouse\common\enums\StoneBillStatusEnum::getValue($model->bill->bill_status);
                                    }
                                    return "";
                                },
                            ],


                            [
                                'label' => '出/入库石重(g)',
                                'value' => function($model){
                                    switch ($model->bill_type){
                                        case \addons\Warehouse\common\enums\StoneBillTypeEnum::STONE_MS:
                                            $sign = 1;
                                            break;
                                        case \addons\Warehouse\common\enums\StoneBillTypeEnum::STONE_SS:
                                            $sign = -1;
                                            break;
                                        case \addons\Warehouse\common\enums\StoneBillTypeEnum::STONE_HS:
                                            $sign = 1;
                                            break;
                                        case \addons\Warehouse\common\enums\StoneBillTypeEnum::STONE_TS:
                                            $sign = -1;
                                            break;
                                        case \addons\Warehouse\common\enums\StoneBillTypeEnum::STONE_RK:
                                            $sign = 1;
                                            break;
                                        case \addons\Warehouse\common\enums\StoneBillTypeEnum::STONE_CK:
                                            $sign = -1;
                                            break;
                                        default: $sign = 0;
                                    }
                                    return $model->stone_weight * $sign;
                                },
                                'filter' => false,
                            ],
                            [
                                'label' => '出/入库粒数',
                                'value' => function($model){
                                    switch ($model->bill_type){
                                        case \addons\Warehouse\common\enums\StoneBillTypeEnum::STONE_MS:
                                            $sign = 1;
                                            break;
                                        case \addons\Warehouse\common\enums\StoneBillTypeEnum::STONE_SS:
                                            $sign = -1;
                                            break;
                                        case \addons\Warehouse\common\enums\StoneBillTypeEnum::STONE_HS:
                                            $sign = 1;
                                            break;
                                        case \addons\Warehouse\common\enums\StoneBillTypeEnum::STONE_TS:
                                            $sign = -1;
                                            break;
                                        case \addons\Warehouse\common\enums\StoneBillTypeEnum::STONE_RK:
                                            $sign = 1;
                                            break;
                                        case \addons\Warehouse\common\enums\StoneBillTypeEnum::STONE_CK:
                                            $sign = -1;
                                            break;
                                        default: $sign = 0;
                                    }
                                    return $model->stone_num * $sign;
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

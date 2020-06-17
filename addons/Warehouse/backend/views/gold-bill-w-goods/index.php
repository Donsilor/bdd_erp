<?php

use addons\Style\common\enums\AttrIdEnum;
use common\helpers\Html;
use common\helpers\Url;
use yii\grid\GridView;
use addons\Warehouse\common\enums\BillStatusEnum;

$this->title = '盘点单明细';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box-body nav-tabs-custom">
    <h2 class="page-header">盘点单详情 - <?php echo $bill->bill_no?> - <?php echo \addons\Warehouse\common\enums\GoldBillStatusEnum::getValue($bill->bill_status)?></h2>
    <?php echo Html::menuTab($tabList,$tab)?>
    <div class="tab-content">
        <div class="row col-xs-15">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">
                    <?= Html::encode($this->title) ?>
                    <?php //echo Html::checkboxList('colmun','',\Yii::$app->purchaseService->purchaseGoods->listColmuns(1))?>
                    </h3>
                    <div class="box-tools">
                    <?php if($bill->bill_status == \addons\Warehouse\common\enums\GoldBillStatusEnum::SAVE) {?>
                        <?= Html::create(['gold-bill-w/pandian', 'id' => $bill->id,'returnUrl'=>Url::getReturnUrl()], '盘点', []); ?>
                    <?php }?>
                    </div>
               </div>
            <div class="box-body table-responsive">  
                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'tableOptions' => ['class' => 'table table-hover'],
                        'options' => ['style'=>'width:120%;'],
                        'showFooter' => false,//显示footer行
                        'id'=>'grid', 
                        'columns' => [
                            [
                                    'class' => 'yii\grid\SerialColumn',
                                    'visible' => false,
                            ],
                            [
                                    'attribute' => 'id',
                                    'filter' => false,
                                    'format' => 'raw',
                                    'headerOptions' => ['width'=>'80'],
                            ],
                            [
                                'attribute' => 'gold_sn',
                                'filter' => true,
                                'format' => 'raw',
                                'headerOptions' => ['width'=>'100'],
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
                                'headerOptions' => ['width'=>'80'],
                            ],
                            [
                                'attribute'=>'gold_name',
                                'filter' => Html::activeTextInput($searchModel, 'gold_name', [
                                        'class' => 'form-control',
                                ]),
                                'value' => function ($model) {
                                    return $model->gold_name;
                                },
                                'format' => 'raw',
                                'headerOptions' => ['width'=>'160'],
                            ],
                            [
                                    'attribute' => 'style_sn',
                                    'filter' => true,
                                    'format' => 'raw',
                                    'headerOptions' => ['width'=>'120'],
                            ],
                            [
                                'label' => '应盘重量',
                                'attribute' => 'gold_weight',
                                'filter' => true,
                                'headerOptions' => ['width' => '100'],
                            ],
                            [
                                'label' => '实盘重量',
                                'value' => function($model){
                                    return $model->goodsW->actual_weight ?? 0;
                                },
                                'filter' => false,
                                'format' => 'raw',
                                'headerOptions' => ['width' => '100'],
                            ],
                            [
                                'label' => '财务审核状态',
                                'value' => function($model){
                                    if($model->goodsW->fin_status){
                                        return \addons\Warehouse\common\enums\FinAuditStatusEnum::getValue($model->goodsW->fin_status);
                                    }
                                    return "";
                                },
                                'filter' => false,
                                'format' => 'raw',
                                'headerOptions' => ['width' => '120'],
                            ],
                            [
                                'label' => '财务确认人',
                                'value' => function($model){
                                    return $model->goodsW->finer->username ?? "";
                                },
                                'filter' => false,
                                'format' => 'raw',
                                'headerOptions' => ['width' => '100'],
                            ],
                            [
                                'label' => '财务确认时间',
                                'value' => function($model){
                                    if($model->goodsW->fin_check_time){
                                        return Yii::$app->formatter->asDatetime($model->goodsW->fin_check_time) ?? "";
                                    }
                                    return "";
                                },
                                'filter' => false,
                                'format' => 'raw',
                                'headerOptions' => ['width' => '120'],
                            ],
                            [
                                'label' => '财务备注',
                                'value' => function($model){
                                    return $model->goodsW->fin_remark ?? "";
                                },
                                'filter' => false,
                                'format' => 'raw',
                                'headerOptions' => ['width' => '100'],
                            ],
                            [
                                    'label' => '盘点状态',
                                    'attribute' => 'status',
                                    'value' =>function($model){
                                        return \addons\Warehouse\common\enums\PandianStatusEnum::getValue($model->status);
                                    },
                                    'filter'=> Html::activeDropDownList($searchModel, 'status',\addons\Warehouse\common\enums\PandianStatusEnum::getMap(), [
                                            'prompt' => '全部',
                                            'class' => 'form-control',                                            
                                    ]),
                                    'format' => 'raw',
                                    'headerOptions' => ['width'=>'110'],
                            ],
                            [
                                'class' => 'yii\grid\ActionColumn',
                                'header' => '操作',
                                'template' => '{audit}',
                                'buttons' => [
                                    'audit' => function($url, $model, $key){
                                        if($model->goodsW->fin_status == \addons\Warehouse\common\enums\FinAuditStatusEnum::PENDING){
                                            return Html::edit(['ajax-audit','id'=>$model->id], '审核', [
                                                'class'=>'btn btn-success btn-sm',
                                                'data-toggle' => 'modal',
                                                'data-target' => '#ajaxModal',
                                            ]);
                                        }
                                    },
                                ]
                           ]
                      ]
                    ]); ?>
                </div>
            </div>
        <!-- box end -->
        </div>
    </div>
</div>
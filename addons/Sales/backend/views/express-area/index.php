<?php

use common\helpers\Html;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use addons\Warehouse\common\enums\BillStatusEnum;
use addons\Purchase\common\enums\ReceiptStatusEnum;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('express_area', '快递配送区域');
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box-body nav-tabs-custom">
    <h2 class="page-header"><?= $this->title; ?> - <?= $express->name?> - <?= \common\enums\AuditStatusEnum::getValue($express->audit_status)?></h2>
    <?php echo Html::menuTab($tabList,$tab)?>
    <div class="box-tools" style="float:right;margin-top:-40px; margin-right: 20px;">
        <?= Html::create(['ajax-edit', 'express_id'=>$express->id], '创建', [
            'data-toggle' => 'modal',
            'data-target' => '#ajaxModal',
        ]); ?>
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
                                'headerOptions' => ['width'=>'30'],
                            ],
                            [
                                'attribute' => 'id',
                                'filter' => true,
                                'format' => 'raw',
                                'headerOptions' => ['width'=>'80'],
                            ],
                            [
                                'attribute'=>'express.name',
                                'value' => function ($model){
                                    return $model->express->name ?? '';
                                },
                                'filter' => false,
                                'headerOptions' => [],
                            ],
                            [
                                'attribute'=>'delivery_area',
                                'filter' => Html::activeTextInput($searchModel, 'delivery_area', [
                                    'class' => 'form-control',
                                ]),
                                'headerOptions' => [],
                            ],
                            [
                                'attribute'=>'delivery_time',
                                'filter' => Html::activeTextInput($searchModel, 'delivery_time', [
                                    'class' => 'form-control',
                                ]),
                                'headerOptions' => [],
                            ],
                            [
                                'attribute'=>'first_price',
                                'filter' => Html::activeTextInput($searchModel, 'first_price', [
                                    'class' => 'form-control',
                                ]),
                                'headerOptions' => [],
                            ],
                            [
                                'attribute'=>'supply_price',
                                'filter' => Html::activeTextInput($searchModel, 'supply_price', [
                                    'class' => 'form-control',
                                ]),
                                'headerOptions' => [],
                            ],
                            [
                                'attribute'=>'last_first_price',
                                'filter' => Html::activeTextInput($searchModel, 'last_first_price', [
                                    'class' => 'form-control',
                                ]),
                                'headerOptions' => [],
                            ],
                            [
                                'attribute'=>'last_supply_price',
                                'filter' => Html::activeTextInput($searchModel, 'last_supply_price', [
                                    'class' => 'form-control',
                                ]),
                                'headerOptions' => [],
                            ],
                            [
                                'attribute' => 'is_holidays',
                                'value' => function ($model){
                                    return \common\enums\ConfirmEnum::getValue($model->is_holidays);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'is_holidays',\common\enums\ConfirmEnum::getMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                ]),
                                'format' => 'raw',
                                'headerOptions' => [],
                            ],
                            [
                                'attribute'=>'remark',
                                'filter' => Html::activeTextInput($searchModel, 'remark', [
                                    'class' => 'form-control',
                                ]),
                                'headerOptions' => [],
                            ],
                            [
                                'label' => '添加人',
                                'attribute' => 'member.username',
                                'headerOptions' => ['class' => 'col-md-1'],
                                'filter' => Html::activeTextInput($searchModel, 'member.username', [
                                    'class' => 'form-control',
                                ]),

                            ],
                            [
                                'attribute'=>'updated_at',
                                'filter' => \kartik\daterange\DateRangePicker::widget([    // 日期组件
                                    'model' => $searchModel,
                                    'attribute' => 'updated_at',
                                    'value' => $searchModel->created_at,
                                    'options' => ['readonly' => false,'class'=>'form-control','style'=>'background-color:#fff;width:200px;'],
                                    'pluginOptions' => [
                                        'format' => 'yyyy-mm-dd',
                                        'locale' => [
                                            'separator' => '/',
                                        ],
                                        'endDate' => date('Y-m-d',time()),
                                        'todayHighlight' => true,
                                        'autoclose' => true,
                                        'todayBtn' => 'linked',
                                        'clearBtn' => true,
                                    ],
                                ]),
                                'value'=>function($model){
                                    return Yii::$app->formatter->asDatetime($model->updated_at);
                                }
                            ],
                            [
                                'attribute' => 'status',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1'],
                                'value' => function ($model){
                                    return \common\enums\StatusEnum::getValue($model->status);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'status',\common\enums\StatusEnum::getMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                ]),
                            ],
                            [
                                'attribute' => 'sort',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::sort($model->sort,['data-url'=>\common\helpers\Url::to(['ajax-update'])]);
                                },
                                'headerOptions' => ['width' => '80'],
                            ],
                            [
                                'class' => 'yii\grid\ActionColumn',
                                'header' => '操作',
                                'template' => '{edit} {info} {status}',
                                'buttons' => [
                                    'edit' => function($url, $model, $key){
                                        return Html::edit(['ajax-edit','id' => $model->id,'returnUrl' => \common\helpers\Url::getReturnUrl()], '编辑', [
                                            'data-toggle' => 'modal',
                                            'data-target' => '#ajaxModal',
                                        ]);
                                    },
                                    'status' => function($url, $model, $key){
                                        return Html::status($model->status);
                                    },
                                    'delete' => function($url, $model, $key){
                                        return Html::delete(['delete', 'id' => $model->id]);
                                    },
                                ],
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
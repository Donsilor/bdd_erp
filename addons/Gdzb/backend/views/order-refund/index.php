<?php

use common\helpers\Url;
use kartik\daterange\DateRangePicker;
use yii\grid\GridView;
use common\helpers\Html;
use common\helpers\ImageHelper;

$this->title = '退货单';
$this->params['breadcrumbs'][] = ['label' => $this->title];

?>

<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title"><?= Html::encode($this->title) ?></h3>
                <div class="box-tools">
                    <?= Html::create(['edit'], '创建'); ?>
                </div>
            </div>
            <div class="box-body table-responsive">
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    //重新定义分页样式
                    'tableOptions' => ['class' => 'table table-hover rf-table'],
                    'options' => ['style'=>'white-space:nowrap;'],
                    'columns' => [
                        [
                            'class' => 'yii\grid\SerialColumn',
                            'visible' => false, // 不显示#
                        ],
                        [
                            'attribute' => 'id',
                            'headerOptions' => [],
                        ],
                        [
                            'attribute' => 'refund_sn',
                            'format' => 'raw',
                            'value'=>function($model) {
                                return Html::a($model->refund_sn, ['view', 'id' => $model->id,'returnUrl'=>Url::getReturnUrl()], ['style'=>"text-decoration:underline;color:#3c8dbc"]);
                            },
                            'headerOptions' => ['style'=> 'width:100px;'],
                        ],
                        [
                            'attribute' => 'order.order_sn',
                            'format' => 'raw',
                            'value'=>function($model) {
                                return $model->order->order_sn ?? '';
                            },
                            'headerOptions' => ['style'=> 'width:100px;'],
                        ],

                        [
                            'attribute' => 'customer.wechat',
                            'headerOptions' =>  ['style'=> 'width:80px;'],
                        ],
                        [
                            'attribute' => 'refund_status',
                            'format' => 'raw',
                            'headerOptions' => ['class' => 'col-md-1'],
                            'value' => function ($model){
                                return \addons\Gdzb\common\enums\RefundStatusEnum::getValue($model->refund_status);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'refund_status',\addons\Gdzb\common\enums\RefundStatusEnum::getMap(), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style'=> 'width:80px;',
                            ]),
                        ],
                        [
                            'attribute' => 'audit_status',
                            'format' => 'raw',
                            'headerOptions' => ['class' => 'col-md-1'],
                            'value' => function ($model){
                                return \common\enums\AuditStatusEnum::getValue($model->audit_status);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'refund_status',\common\enums\AuditStatusEnum::getMap(), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style'=> 'width:80px;',
                            ]),
                        ],
                        [
                            'attribute' => 'refund_amount',
                            'filter' => true,
                            'headerOptions' => ['style'=> 'width:80px;'],
                        ],
                        [
                            'attribute' => 'refund_num',
                            'filter' => true,
                            'headerOptions' => ['style'=> 'width:80px;'],
                        ],
                        [
                            'attribute' => 'channel_id',
                            'format' => 'raw',
                            'value' => function ($model){
                                return $model->saleChannel->name ?? '';
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'channel_id',\Yii::$app->salesService->saleChannel->getDropDown(), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style'=> 'width:100px;',
                            ]),
                            'headerOptions' => ['class' => 'col-md-1'],
                        ],

                        [
                            'attribute' => 'warehouse_id',
                            'format' => 'raw',
                            'value' => function ($model){
                                return $model->warehouse->name ?? '';
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'warehouse_id',\Yii::$app->warehouseService->warehouse->getDropDown(), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style'=> 'width:100px;',
                            ]),
                            'headerOptions' => ['class' => 'col-md-1'],
                        ],

                        [
                            'attribute'=>'created_at',
                            'filter' => DateRangePicker::widget([    // 日期组件
                                'model' => $searchModel,
                                'attribute' => 'created_at',
                                'value' => $searchModel->created_at,
                                'options' => ['readonly' => false,'class'=>'form-control','style'=>'background-color:#fff;width:80px;'],
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
                                return Yii::$app->formatter->asDate($model->created_at);
                            }
                        ],

                        [
                            'header' => "操作",
                            'class' => 'yii\grid\ActionColumn',
                            'contentOptions' => ['style' => ['white-space' => 'nowrap']],
                            'template' => ' {view} {apply} {ajax-audit}',
                            'buttons' => [
                                'apply' => function($url, $model, $key){
                                    if($model->audit_status == \common\enums\AuditStatusEnum::SAVE){
                                        return Html::edit(['ajax-apply','id'=>$model->id], '提审', [
                                            'class'=>'btn btn-success btn-sm',
                                            'onclick' => 'rfTwiceAffirm(this,"提交审核", "确定提交吗？");return false;',
                                        ]);
                                    }
                                },
                                'ajax-audit' => function($url, $model, $key){
                                    if($model->audit_status == \common\enums\AuditStatusEnum::PENDING ){
                                        return Html::edit(['ajax-audit','id'=>$model->id], '审核', [
                                            'class'=>'btn btn-success btn-sm',
                                            'data-toggle' => 'modal',
                                            'data-target' => '#ajaxModal',
                                        ]);
                                    }
                                },
                                'view' => function ($url, $model, $key) {
                                    return Html::a('查看', ['view', 'id' => $model->id,'returnUrl'=>Url::getReturnUrl()], ['class' => 'btn btn-warning btn-sm']);
                                },
                            ],
                        ],
                    ],
                ]); ?>
            </div>
        </div>
    </div>
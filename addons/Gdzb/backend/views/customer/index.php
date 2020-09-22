<?php

use common\helpers\Url;
use kartik\daterange\DateRangePicker;
use yii\grid\GridView;
use common\helpers\Html;
use common\helpers\ImageHelper;

$this->title = '客户列表';
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
                            'attribute' => 'customer_no',
                            'format' => 'raw',
                            'value'=>function($model) {
                                return $model->customer_no;
                            },
                            'headerOptions' => ['style'=> 'width:100px;'],
                        ],

                        [
                            'attribute' => 'realname',
                            'format' => 'raw',
                            'value'=>function($model) {
                                return Html::a($model->realname, ['view', 'id' => $model->id,'returnUrl'=>Url::getReturnUrl()], ['style'=>"text-decoration:underline;color:#3c8dbc"]);
                            },
                            'filter' => true,
                            'headerOptions' => ['style'=> 'width:80px;'],
                        ],

                        [
                            'attribute' => 'wechat',
                            'headerOptions' =>  ['style'=> 'width:80px;'],
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
                            'attribute' => 'source_id',
                            'format' => 'raw',
                            'value' => function ($model){
                                return $model->source->name ?? '';
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'source_id',\Yii::$app->salesService->sources->getDropDown(), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style'=> 'width:100px;',
                            ]),
                            'headerOptions' => ['class' => 'col-md-1'],
                        ],
                        [
                            'attribute' => 'order_num',
                            'headerOptions' =>  ['style'=> 'width:80px;'],
                        ],
                        [
                            'attribute' => 'order_amount',
                            'headerOptions' =>  ['style'=> 'width:80px;'],
                        ],
                        [
                            'attribute' => 'remark',
                            'headerOptions' =>  ['class' => 'col-md-2'],
                            'filter' => false,
                        ],
                        [
                            'attribute' => 'follower_id',
                            'format' => 'raw',
                            'value' => function ($model){
                                return $model->follower->username ?? '';
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'follower_id',Yii::$app->services->backendMember->getDropDown(), [
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
                            'attribute' => 'status',
                            'format' => 'raw',
                            'headerOptions' => ['class' => 'col-md-1'],
                            'value' => function ($model){
                                return \common\enums\StatusEnum::getValue($model->status);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'status',\common\enums\StatusEnum::getMap(), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style'=> 'width:80px;',
                            ]),
                        ],
                        [
                            'header' => "操作",
                            'class' => 'yii\grid\ActionColumn',
                            'contentOptions' => ['style' => ['white-space' => 'nowrap']],
                            'template' => '{edit} {view} {status}',
                            'buttons' => [
                                'edit' => function ($url, $model, $key) {
                                    return Html::edit(['edit', 'id' => $model->id]);
                                },
                                'view' => function ($url, $model, $key) {
                                    return Html::a('查看', ['view', 'id' => $model->id,'returnUrl'=>Url::getReturnUrl()], ['class' => 'btn btn-warning btn-sm']);
                                },
                                'status' => function ($url, $model, $key) {
                                    return Html::status($model->status);
                                },
                                'destroy' => function ($url, $model, $key) {
                                    return Html::delete(['destroy', 'id' => $model->id]);
                                },
                            ],
                        ],
                    ],
                ]); ?>
            </div>
        </div>
    </div>
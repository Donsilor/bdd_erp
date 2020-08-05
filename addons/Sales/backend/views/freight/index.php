<?php

use common\helpers\Html;
use common\helpers\ImageHelper;
use common\helpers\Url;
use yii\grid\GridView;
use kartik\daterange\DateRangePicker;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('sale_channel', '快递单列表');
$this->params['breadcrumbs'][] = $this->title;

$params = Yii::$app->request->queryParams;
$params = $params ? "&".http_build_query($params) : '';

?>
<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title"><?= Html::encode($this->title) ?></h3>
                <div class="box-tools" >
                    <?= Html::create(['ajax-edit'], '创建', [
                        'data-toggle' => 'modal',
                        'data-target' => '#ajaxModalLg',
                    ]); ?>
                    <a href="<?= Url::to(['index?action=export'.$params])?>" class="blue">导出Excel</a>
                </div>
            </div>
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
                            'attribute'=>'freight_no',
                            'filter' => Html::activeTextInput($searchModel, 'freight_no', [
                                'class' => 'form-control',
                            ]),
                            'headerOptions' => [],
                        ],
                        [
                            'attribute' => 'express_id',
                            'value' => function ($model){
                                return $model->express->name ?? '';
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'express_id',\Yii::$app->salesService->express->getDropDown(), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style'=> 'width:120px;',
                            ]),
                            'format' => 'raw',
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'order_sn',
                            'filter' => Html::activeTextInput($searchModel, 'order_sn', [
                                'class' => 'form-control',
                            ]),
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'consignee',
                            'filter' => Html::activeTextInput($searchModel, 'consignee', [
                                'class' => 'form-control',
                            ]),
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'consignee_mobile',
                            'filter' => Html::activeTextInput($searchModel, 'consignee_mobile', [
                                'class' => 'form-control',
                            ]),
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'consignee_tel',
                            'filter' => Html::activeTextInput($searchModel, 'consignee_tel', [
                                'class' => 'form-control',
                            ]),
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'consignee_address',
                            'filter' => Html::activeTextInput($searchModel, 'consignee_address', [
                                'class' => 'form-control',
                            ]),
                            'headerOptions' => ['class' => 'col-md-1'],
                        ],
                        [
                            'attribute'=>'consigner',
                            'filter' => Html::activeTextInput($searchModel, 'consigner', [
                                'class' => 'form-control',
                            ]),
                            'headerOptions' => [],
                        ],
                        [
                            'attribute' => 'sale_channel_id',
                            'value' => function ($model){
                                return $model->saleChannel->name ?? '';
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'sale_channel_id',Yii::$app->salesService->saleChannel->getDropDown(), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style'=> 'width:120px;'
                            ]),
                            'format' => 'raw',
                            'headerOptions' => [],
                        ],
                        [
                            'attribute' => 'print_status',
                            'value' => function ($model){
                                return \addons\Sales\common\enums\PrintStatusEnum::getValue($model->print_status);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'print_status',\addons\Sales\common\enums\PrintStatusEnum::getMap(), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                            ]),
                            'format' => 'raw',
                            'headerOptions' => ['width'=>'100'],
                        ],
                        [
                            'attribute' => 'print_num',
                            'filter' => false,
                            'headerOptions' => ['width'=>'80'],
                        ],
                        [
                            'attribute'=>'print_time',
                            'filter' => DateRangePicker::widget([    // 日期组件
                                'model' => $searchModel,
                                'attribute' => 'print_time',
                                'value' => $searchModel->print_time,
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
                                return Yii::$app->formatter->asDatetime($model->print_time);
                            }
                        ],
                        [
                            'label' => '添加人',
                            'attribute' => 'creator.username',
                            'filter' => Html::activeTextInput($searchModel, 'creator.username', [
                                'class' => 'form-control',
                            ]),
                            'headerOptions' => ['class' => 'col-md-1'],
                        ],
                        [
                            'label' => '添加时间',
                            'attribute'=>'created_at',
                            'filter' => DateRangePicker::widget([    // 日期组件
                                'model' => $searchModel,
                                'attribute' => 'created_at',
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
                            'attribute' => 'remark',
                            'filter' => true,
                            'headerOptions' => ['width'=>'200'],
                        ],
                        [
                            'class' => 'yii\grid\ActionColumn',
                            'header' => '操作',
                            'template' => '{edit} {delete}',
                            'buttons' => [
                                'edit' => function($url, $model, $key){
                                    return Html::edit(['ajax-edit','id' => $model->id,'returnUrl' => Url::getReturnUrl()], '编辑', [
                                        'data-toggle' => 'modal',
                                        'data-target' => '#ajaxModalLg',
                                    ]);
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
</div>
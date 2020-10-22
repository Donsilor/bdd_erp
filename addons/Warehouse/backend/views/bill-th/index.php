<?php

use common\helpers\Html;
use common\helpers\Url;
use addons\Warehouse\common\enums\BillStatusEnum;
use yii\grid\GridView;
use kartik\daterange\DateRangePicker;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('bill_t', '其它退货单列表');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title"><?= Html::encode($this->title) ?></h3>
                <div class="box-tools">
                    <?= Html::create(['ajax-edit'], '创建', [
                        'data-toggle' => 'modal',
                        'data-target' => '#ajaxModalLg',
                    ]); ?>
                </div>
            </div>
            <div class="box-body table-responsive">
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'tableOptions' => ['class' => 'table table-hover'],
                    'options' => ['style' => 'white-space:nowrap;'],
                    'showFooter' => false,//显示footer行
                    'id' => 'grid',
                    'columns' => [
                        [
                            'class' => 'yii\grid\SerialColumn',
                            'visible' => false,
                        ],
                        [
                            'class' => 'yii\grid\CheckboxColumn',
                            'name' => 'id',  //设置每行数据的复选框属性
                            'headerOptions' => [],
                        ],
                        [
                            'attribute' => 'id',
                            'filter' => false,
                            'format' => 'raw',
                            'headerOptions' => [],
                        ],
                        [
                            'attribute' => 'bill_no',
                            'value' => function ($model) {
                                return Html::a($model->bill_no, ['view', 'id' => $model->id, 'returnUrl' => Url::getReturnUrl()], ['class' => 'openContab', 'style' => "text-decoration:underline;color:#3c8dbc", 'id' => $model->bill_no]) . ' <i class="fa fa-copy" onclick="copy(\'' . $model->bill_no . '\')"></i>';
                            },
                            'filter' => Html::activeTextInput($searchModel, 'bill_no', [
                                'class' => 'form-control',
                            ]),
                            'format' => 'raw',
                            'headerOptions' => ['class' => 'col-md-1'],
                        ],                        
                        [
                            'attribute' => 'goods_num',
                            'filter' => false,
                            'format' => 'raw',
                            'headerOptions' => ['class' => 'col-md-1'],
                        ],
                        [
                            'attribute' => 'total_cost',
                            'filter' => false,
                            'headerOptions' => ['class' => 'col-md-1'],
                        ], 
                        [
                            'attribute' => 'channel_id',
                            'format' => 'raw',
                            'headerOptions' => ['class' => 'col-md-1'],
                            'value' => function ($model) {
                                return $model->channel->name ?? '';
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'item_type', Yii::$app->salesService->saleChannel->getDropDown(), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style' => 'width:100px;'
                            ]),
                        ],
                        [
                            'attribute' => 'salesman_id',
                            'value' => 'salesman.username',
                            'headerOptions' => ['class' => 'col-md-1'],
                            'filter' => Html::activeTextInput($searchModel, 'salesman.username', [
                                'class' => 'form-control',
                            ]),
                        ],                                  
                        [
                            'attribute' => 'audit_status',
                            'format' => 'raw',
                            'headerOptions' => ['class' => 'col-md-1'],
                            'value' => function ($model) {
                                return \common\enums\AuditStatusEnum::getValue($model->audit_status);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'audit_status', \common\enums\AuditStatusEnum::getMap(), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style' => 'width:80px;'
                            ]),
                        ],
                        [
                            'attribute' => 'bill_status',
                            'format' => 'raw',
                            'headerOptions' => ['class' => 'col-md-1'],
                            'value' => function ($model) {
                                return \addons\Warehouse\common\enums\BillStatusEnum::getValue($model->bill_status);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'bill_status', \addons\Warehouse\common\enums\BillStatusEnum::getMap(), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style' => 'width:80px;',
                            ]),
                        ],
                        [
                            'attribute' => 'item_type',
                            'headerOptions' => ['class' => 'col-md-1'],
                            'value' => function ($model) {
                                return addons\Warehouse\common\enums\ReturnTypeEnum::getValue($model->item_type);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'item_type', addons\Warehouse\common\enums\ReturnTypeEnum::getMap(), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style' => 'width:100px;'
                            ]),
                        ],
                        [
                            'attribute' => 'remark',
                            'value' => 'remark',
                            'headerOptions' => ['class' => 'col-md-1'],
                            'filter' => Html::activeTextInput($searchModel, 'remark', [
                                'class' => 'form-control',
                            ]),
                        ],
                        [
                            'attribute' => 'creator_id',
                            'value' => 'creator.username',
                            'headerOptions' => ['class' => 'col-md-1'],
                            'filter' => Html::activeTextInput($searchModel, 'creator.username', [
                                'class' => 'form-control',
                            ]),
                        ],
                        [
                            'attribute' => 'created_at',
                            'filter' => DateRangePicker::widget([    // 日期组件
                                'model' => $searchModel,
                                'attribute' => 'created_at',
                                'value' => $searchModel->created_at,
                                'options' => ['readonly' => false, 'class' => 'form-control', 'style' => 'background-color:#fff;width:150px;'],
                                'pluginOptions' => [
                                    'format' => 'yyyy-mm-dd',
                                    'locale' => [
                                        'separator' => '/',
                                    ],
                                    'endDate' => date('Y-m-d', time()),
                                    'todayHighlight' => true,
                                    'autoclose' => true,
                                    'todayBtn' => 'linked',
                                    'clearBtn' => true,
                                ],
                            ]),
                            'value' => function ($model) {
                                return Yii::$app->formatter->asDate($model->created_at);
                            },
                        ],             
                        [
                            'class' => 'yii\grid\ActionColumn',
                            'header' => '操作',
                            'headerOptions' => ['class' => 'col-md-3'],
                            'contentOptions' => ['style' => ['white-space' => 'nowrap']],
                            'template' => '{edit} {apply} {audit} {goods} {cancel} {delete}',
                            'buttons' => [
                                'edit' => function ($url, $model, $key) {
                                    if ($model->bill_status == BillStatusEnum::SAVE) {
                                        return Html::edit(['ajax-edit', 'id' => $model->id, 'returnUrl' => Url::getReturnUrl()], '编辑', [
                                            'data-toggle' => 'modal',
                                            'data-target' => '#ajaxModalLg',
                                        ]);
                                    }
                                },
                                'apply' => function ($url, $model, $key) {
                                    if ($model->bill_status == BillStatusEnum::SAVE) {
                                        return Html::edit(['ajax-apply', 'id' => $model->id], '提审', [
                                            'class' => 'btn btn-success btn-sm',
                                            'onclick' => 'rfTwiceAffirm(this,"提交审核", "确定提交吗？");return false;',
                                        ]);
                                    }
                                },
                                'audit' => function ($url, $model, $key) {
                                    if ($model->bill_status == BillStatusEnum::PENDING) {
                                        return Html::edit(['ajax-audit', 'id' => $model->id], '审核', [
                                            'class' => 'btn btn-success btn-sm',
                                            'data-toggle' => 'modal',
                                            'data-target' => '#ajaxModal',
                                        ]);
                                    }
                                },
                                'goods' => function ($url, $model, $key) {
                                    return Html::a('明细', ['bill-th-goods/index', 'bill_id' => $model->id, 'returnUrl' => Url::getReturnTab()], ['class' => 'btn btn-info btn-sm openContab','data-title'=>$model->bill_no."(明细)"]);
                                },
                                'cancel' => function ($url, $model, $key) {
                                    if ($model->bill_status == BillStatusEnum::SAVE) {
                                        return Html::delete(['cancel', 'id' => $model->id], '取消', [
                                            'class' => 'btn btn-warning btn-sm',
                                            'onclick' => 'rfTwiceAffirm(this,"取消单据", "确定取消吗？");return false;',
                                        ]);
                                    }
                                },
                                'delete' => function ($url, $model, $key) {
                                    if ($model->bill_status == BillStatusEnum::CANCEL) {
                                        return Html::delete(['delete', 'id' => $model->id], '删除', [
                                            'onclick' => 'rfTwiceAffirm(this,"删除单据", "确定删除吗？");return false;',
                                        ]);
                                    }
                                },
                            ],

                        ]
                    ]
                ]); ?>
            </div>
        </div>
    </div>
</div>
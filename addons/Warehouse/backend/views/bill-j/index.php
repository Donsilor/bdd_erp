<?php

use common\helpers\Html;
use common\helpers\Url;
use kartik\select2\Select2;
use yii\grid\GridView;
use kartik\daterange\DateRangePicker;
use addons\Warehouse\common\enums\BillStatusEnum;
use addons\Warehouse\common\enums\LendStatusEnum;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('bill_j', '借货单列表');
$this->params['breadcrumbs'][] = $this->title;
$params = Yii::$app->request->queryParams;
$params = $params ? "&" . http_build_query($params) : '';
?>
<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title"><?= Html::encode($this->title) ?></h3>
                <div class="box-tools">
                    <?= Html::create(['ajax-edit'], '创建', [
                        'data-toggle' => 'modal',
                        'data-target' => '#ajaxModal',
                    ]); ?>
                    <!--                    --><? //= Html::button('导出', [
                    //                        'class' => 'btn btn-success btn-xs',
                    //                        'onclick' => 'batchExport()',
                    //                    ]); ?>
                </div>
            </div>
            <div class="box-body table-responsive">
                <?php echo Html::batchButtons(false) ?>
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
                            'visible' => true,
                        ],
                        [
                            'class' => 'yii\grid\CheckboxColumn',
                            'name' => 'id',  //设置每行数据的复选框属性
                        ],
                        [
                            'attribute' => 'id',
                            'filter' => false,
                        ],
                        [
                            'attribute' => 'bill_no',
                            'value' => function ($model) {
                                return Html::a($model->bill_no, ['view', 'id' => $model->id, 'returnUrl' => Url::getReturnTab()], ['class' => 'openContab', 'style' => "text-decoration:underline;color:#3c8dbc"]);
                            },
                            'filter' => Html::activeTextInput($searchModel, 'bill_no', [
                                'class' => 'form-control',
                            ]),
                            'format' => 'raw',
                        ],
                        [
                            'attribute' => 'channel_id',
                            'value' => function ($model) {
                                return $model->channel->name ?? '';
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'channel_id', Yii::$app->styleService->styleChannel->getDropDown(), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                            ]),
                            'format' => 'raw',
                        ],
                        [
                            'attribute' => 'lender_id',
                            'value' => "billJ.lender.username",
                            'filter' => \kartik\select2\Select2::widget([
                                'name' => 'SearchModel[lender_id]',
                                'value' => $searchModel->lender_id,
                                'data' => \Yii::$app->services->backendMember->getDropDown(),
                                'options' => ['placeholder' => "请选择"],
                                'pluginOptions' => [
                                    'allowClear' => true,
                                ],
                            ]),
                            'format' => 'raw',
                        ],
                        [
                            'attribute' => 'goods_num',
                            'filter' => false,
                        ],
                        [
                            'attribute' => 'restore_num',
                            'value' => function ($model) {
                                return $model->billJ->restore_num ?? 0;
                            },
                            'filter' => false,
                        ],
                        [
                            'attribute' => 'billJ.lend_status',
                            'format' => 'raw',
                            'value' => function ($model) {
                                return \addons\Warehouse\common\enums\LendStatusEnum::getValue($model->billJ->lend_status);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'billJ.lend_status', \addons\Warehouse\common\enums\LendStatusEnum::getMap(), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style' => 'width:80px;',
                            ]),
                        ],
                        [
                            'attribute' => 'audit_time',
                            'filter' => DateRangePicker::widget([    // 日期组件
                                'model' => $searchModel,
                                'attribute' => 'audit_time',
                                'value' => $searchModel->audit_time,
                                'options' => ['readonly' => false, 'class' => 'form-control', 'style' => 'background-color:#fff;width:100px;'],
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
                                return Yii::$app->formatter->asDate($model->updated_at);
                            }
                        ],
                        [
                            'attribute' => 'est_restore_time',
                            'filter' => DateRangePicker::widget([    // 日期组件
                                'model' => $searchModel,
                                'attribute' => 'est_restore_time',
                                'value' => $searchModel->est_restore_time,
                                'options' => ['readonly' => false, 'class' => 'form-control', 'style' => 'background-color:#fff;width:100px;'],
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
                                return Yii::$app->formatter->asDate($model->billJ->est_restore_time);
                            }
                        ],
                        [
                            'attribute' => 'rel_restore_time',
                            'filter' => DateRangePicker::widget([    // 日期组件
                                'model' => $searchModel,
                                'attribute' => 'rel_restore_time',
                                'value' => $searchModel->rel_restore_time,
                                'options' => ['readonly' => false, 'class' => 'form-control', 'style' => 'background-color:#fff;width:100px;'],
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
                                return Yii::$app->formatter->asDate($model->billJ->rel_restore_time);
                            }
                        ],
                        [
                            'attribute' => 'order_sn',
                            'filter' => Html::activeTextInput($searchModel, 'order_sn', [
                                'class' => 'form-control',
                            ]),
                            'format' => 'raw',
                        ],
                        [
                            'attribute' => 'creator_id',
                            'value' => "creator.username",
                            'filter' => \kartik\select2\Select2::widget([
                                'name' => 'SearchModel[creator_id]',
                                'value' => $searchModel->creator_id,
                                'data' => \Yii::$app->services->backendMember->getDropDown(),
                                'options' => ['placeholder' => "请选择"],
                                'pluginOptions' => [
                                    'allowClear' => true,
                                ],
                            ]),
                            'format' => 'raw',
                        ],
                        [
                            'attribute' => 'created_at',
                            'filter' => DateRangePicker::widget([    // 日期组件
                                'model' => $searchModel,
                                'attribute' => 'created_at',
                                'value' => $searchModel->created_at,
                                'options' => ['readonly' => false, 'class' => 'form-control', 'style' => 'background-color:#fff;width:100px;'],
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
                            }
                        ],
                        [
                            'attribute' => 'bill_status',
                            'format' => 'raw',
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
                            'class' => 'yii\grid\ActionColumn',
                            'header' => '操作',
                            'contentOptions' => ['style' => ['white-space' => 'nowrap']],
                            'template' => '{edit} {apply} {audit} {receive} {restore} {goods} {cancel} {delete}',
                            'buttons' => [
                                'edit' => function ($url, $model, $key) {
                                    if ($model->bill_status == BillStatusEnum::SAVE) {
                                        return Html::edit(['ajax-edit', 'id' => $model->id, 'returnUrl' => Url::getReturnUrl()], '编辑', [
                                            'data-toggle' => 'modal',
                                            'data-target' => '#ajaxModal',
                                        ]);
                                    }
                                },
                                'apply' => function ($url, $model, $key) {
                                    if ($model->bill_status == BillStatusEnum::SAVE) {
                                        return Html::edit(['ajax-apply', 'id' => $model->id], '提审', [
                                            'class' => 'btn btn-success btn-sm',
                                            'onclick' => 'rfTwiceAffirm(this,"提交审核", "确定操作吗？");return false;',
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
                                'receive' => function ($url, $model, $key) {
                                    if ($model->bill_status == BillStatusEnum::CONFIRM
                                        && $model->billJ->lend_status == LendStatusEnum::IN_RECEIVE) {
                                        return Html::edit(['ajax-receive', 'id' => $model->id], '接收', [
                                            'class' => 'btn btn-success btn-sm',
                                            'data-toggle' => 'modal',
                                            'data-target' => '#ajaxModal',
                                        ]);
                                    }
                                },
                                'restore' => function ($url, $model, $key) {
                                    if ($model->bill_status == BillStatusEnum::CONFIRM
                                        && in_array($model->billJ->lend_status, [LendStatusEnum::HAS_LEND, LendStatusEnum::PORTION_RETURN])) {
                                        return Html::a('还货', ['bill-j-goods/index', 'bill_id' => $model->id, 'returnUrl' => Url::getReturnUrl()], ['class' => 'btn btn-primary btn-sm']);
                                    }
                                },
                                'goods' => function ($url, $model, $key) {
                                    return Html::a('明细', ['bill-j-goods/index', 'bill_id' => $model->id, 'returnUrl' => Url::getReturnUrl()], ['class' => 'btn btn-info btn-sm']);
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
<script>
    function batchExport() {
        var ids = $("#grid").yiiGridView("getSelectedRows");
        if (ids.length == 0) {
            var url = "<?= Url::to('index?action=export' . $params);?>";
            rfExport(url)
        } else {
            window.location.href = url = "<?= Url::buildUrl('export', [], ['ids'])?>?ids=" + ids;
        }
    }
</script>
<?php


use common\helpers\Html;
use common\helpers\Url;
use kartik\daterange\DateRangePicker;
use kartik\select2\Select2;
use yii\grid\GridView;
use common\helpers\ImageHelper;
use common\enums\AuditStatusEnum;


/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('warehouse_bill_repair', '维修单');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title"><?= Html::encode($this->title) ?></h3>
                <div class="box-tools">
                    <?= Html::create(['edit']) ?>
                </div>
            </div>
            <div class="box-body table-responsive">
    <?php echo Html::batchButtons(false)?>         
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => ['class' => 'table table-hover'],
        'options' => ['style'=>'width:200%'],
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
                'headerOptions' => [],
            ],
            [
                'attribute' => 'id',
                'value' => 'id',
                'filter' => Html::activeTextInput($searchModel, 'id', [
                    'class' => 'form-control',
                ]),
                'format' => 'raw',
                'headerOptions' => [],
            ],
            [
                'attribute' => 'repair_no',
                'value' => 'repair_no',
                'filter' => Html::activeTextInput($searchModel, 'repair_no', [
                    'class' => 'form-control',
                ]),
                'format' => 'raw',
                'headerOptions' => ['class' => 'col-md-1'],
            ],
            [
                'attribute' => 'order_sn',
                'value' => 'order_sn',
                'filter' => Html::activeTextInput($searchModel, 'order_sn', [
                    'class' => 'form-control',
                ]),
                'format' => 'raw',
                'headerOptions' => ['class' => 'col-md-1'],
            ],
            [
                'attribute' => 'produce_sn',
                'value' => 'produce_sn',
                'filter' => Html::activeTextInput($searchModel, 'produce_sn', [
                    'class' => 'form-control',
                ]),
                'format' => 'raw',
                'headerOptions' => ['class' => 'col-md-1'],
            ],
            [
                'attribute' => 'bill_m_no',
                'value' => 'bill_m_no',
                'filter' => Html::activeTextInput($searchModel, 'bill_m_no', [
                    'class' => 'form-control',
                ]),
                'format' => 'raw',
                'headerOptions' => ['class' => 'col-md-1'],
            ],
            [
                'attribute' => 'goods_id',
                'value' => 'goods_id',
                'filter' => Html::activeTextInput($searchModel, 'goods_id', [
                    'class' => 'form-control',
                ]),
                'format' => 'raw',
                'headerOptions' => ['class' => 'col-md-1'],
            ],
            [
                'attribute' => 'consignee',
                'value' => 'consignee',
                'filter' => Html::activeTextInput($searchModel, 'consignee', [
                    'class' => 'form-control',
                ]),
                'format' => 'raw',
                'headerOptions' => ['class' => 'col-md-1'],
            ],
            [
                'attribute' => 'repair_type',
                'format' => 'raw',
                'headerOptions' => ['class' => 'col-md-1'],
                'value' => function ($model){
                    return \addons\Warehouse\common\enums\RepairTypeEnum::getValue($model->repair_type);
                },
                'filter' => Html::activeDropDownList($searchModel, 'repair_type',\addons\Warehouse\common\enums\RepairTypeEnum::getMap(), [
                    'prompt' => '全部',
                    'class' => 'form-control',
                ]),
            ],
            [
                'attribute' => 'repair_status',
                'format' => 'raw',
                'headerOptions' => ['class' => 'col-md-1'],
                'value' => function ($model){
                    return \addons\Warehouse\common\enums\RepairStatusEnum::getValue($model->repair_status);
                },
                'filter' => Html::activeDropDownList($searchModel, 'repair_status',\addons\Warehouse\common\enums\RepairStatusEnum::getMap(), [
                    'prompt' => '全部',
                    'class' => 'form-control',
                ]),
            ],
            [
                'attribute' => 'supplier_id',
                'value' =>"supplier.supplier_name",
                'filter'=>Select2::widget([
                    'name'=>'SearchModel[supplier_id]',
                    'value'=>$searchModel->supplier_id,
                    'data'=>Yii::$app->supplyService->supplier->getDropDown(),
                    'options' => ['placeholder' =>"请选择",'class' => 'col-md-4', 'style'=> 'width:120px;'],
                    'pluginOptions' => [
                        'allowClear' => true,
                    ],
                ]),
                'format' => 'raw',
                'headerOptions' => ['class' => 'col-md-2'],
            ],
            [
                'label' => '跟单人',
                'attribute' => 'follower.username',
                'headerOptions' => ['class' => 'col-md-1'],
                'filter' => Html::activeTextInput($searchModel, 'follower.username', [
                    'class' => 'form-control',
                ]),
            ],
            [
                'label' => '制单人',
                'attribute' => 'creator.username',
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
                    'value' => '',
                    'options' => ['readonly' => true, 'class' => 'form-control', 'style'=> 'width:120px;'],
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
                    return Yii::$app->formatter->asDatetime($model->created_at);
                },
                'format' => 'raw',
                'headerOptions' => ['class' => 'col-md-1'],
            ],
            [
                'attribute' => 'orders_time',
                'filter' => DateRangePicker::widget([    // 日期组件
                    'model' => $searchModel,
                    'attribute' => 'orders_time',
                    'value' => '',
                    'options' => ['readonly' => true, 'class' => 'form-control', 'style'=> 'width:120px;'],
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
                    return Yii::$app->formatter->asDatetime($model->orders_time);
                },
                'format' => 'raw',
                'headerOptions' => ['class' => 'col-md-2'],
            ],
            [
                'attribute' => 'predict_time',
                'filter' => DateRangePicker::widget([    // 日期组件
                    'model' => $searchModel,
                    'attribute' => 'predict_time',
                    'value' => '',
                    'options' => ['readonly' => true, 'class' => 'form-control', 'style'=> 'width:120px;'],
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
                    return Yii::$app->formatter->asDatetime($model->predict_time);
                },
                'format' => 'raw',
                'headerOptions' => ['class' => 'col-md-2'],
            ],
            [
                'attribute' => 'end_time',
                'filter' => DateRangePicker::widget([    // 日期组件
                    'model' => $searchModel,
                    'attribute' => 'end_time',
                    'value' => '',
                    'options' => ['readonly' => true, 'class' => 'form-control', 'style'=> 'width:120px;'],
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
                    return Yii::$app->formatter->asDatetime($model->end_time);
                },
                'format' => 'raw',
                'headerOptions' => ['class' => 'col-md-2'],
            ],
            [
                'attribute' => 'receiving_time',
                'filter' => DateRangePicker::widget([    // 日期组件
                    'model' => $searchModel,
                    'attribute' => 'receiving_time',
                    'value' => '',
                    'options' => ['readonly' => true, 'class' => 'form-control', 'style'=> 'width:120px;'],
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
                    return Yii::$app->formatter->asDatetime($model->receiving_time);
                },
                'format' => 'raw',
                'headerOptions' => ['class' => 'col-md-2'],
            ],
            [
                'attribute' => 'qc_nopass_time',
                'filter' => DateRangePicker::widget([    // 日期组件
                    'model' => $searchModel,
                    'attribute' => 'qc_nopass_time',
                    'value' => '',
                    'options' => ['readonly' => true, 'class' => 'form-control', 'style'=> 'width:140px;'],
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
                    return Yii::$app->formatter->asDatetime($model->qc_nopass_time);
                },
                'format' => 'raw',
                'headerOptions' => ['class' => 'col-md-2'],
            ],
            [
                'attribute' => 'audit_status',
                'format' => 'raw',
                'headerOptions' => ['class' => 'col-md-1'],
                'value' => function ($model){
                    return \common\enums\AuditStatusEnum::getValue($model->audit_status);
                },
                'filter' => Html::activeDropDownList($searchModel, 'audit_status',\common\enums\AuditStatusEnum::getMap(), [
                    'prompt' => '全部',
                    'class' => 'form-control',
                    'style'=> 'width:120px;',
                ]),
            ],
            [
                'label' => '审核人',
                'attribute' => 'auditor.username',
                'headerOptions' => ['class' => 'col-md-1'],
                'filter' => Html::activeTextInput($searchModel, 'auditor.username', [
                    'class' => 'form-control',
                    'style'=> 'width:120px;',
                ]),
            ],
            [
                'attribute' => 'audit_time',
                'filter' => DateRangePicker::widget([    // 日期组件
                    'model' => $searchModel,
                    'attribute' => 'audit_time',
                    'value' => '',
                    'options' => ['readonly' => true, 'class' => 'form-control', 'style'=> 'width:120px;'],
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
                    return Yii::$app->formatter->asDatetime($model->audit_time);
                },
                'format' => 'raw',
                'headerOptions' => ['class' => 'col-md-1'],
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
                    'style' => 'width:80px;',
                ]),
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => '操作',
                'template' => '{edit} {audit} {status} {delete} ',
                'buttons' => [
                'edit' => function($url, $model, $key){
                        return Html::edit(['edit', 'id' => $model->id, 'returnUrl' => Url::getReturnUrl()]);
                 },
                'audit' => function($url, $model, $key){
                       if($model->audit_status == AuditStatusEnum::PENDING){
                            return Html::edit(['ajax-audit','id'=>$model->id], '审核', [
                                'class'=>'btn btn-success btn-sm',
                                'data-toggle' => 'modal',
                                'data-target' => '#ajaxModal',
                            ]);
                        }
                 },
                 'status' => function($url, $model, $key){
                         if($model->audit_status == AuditStatusEnum::PASS) {
                            return Html::status($model['status']);
                         }
                  },
                 'delete' => function($url, $model, $key){
                    return Html::delete(['delete', 'id' => $model->id]);
                 },
                /*
                'view'=> function($url, $model, $key){
                    return Html::a('预览', \Yii::$app->params['frontBaseUrl'].'/diamond-details/'.$model->id.'?goodId='.$model->id.'&backend=1',['class'=>'btn btn-info btn-sm','target'=>'_blank']);
                    },
                'show_log' => function($url, $model, $key){
                    return Html::linkButton(['goods-log/index','id' => $model->id, 'type_id' => $model->type_id, 'returnUrl' => Url::getReturnUrl()], '日志');
                    },*/
                ],
            ]
    ]
    ]); ?>
            </div>
        </div>
    </div>
</div>

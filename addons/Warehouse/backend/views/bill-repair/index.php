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

$this->title = Yii::t('warehouse_bill_repair', '维修出库单');
$this->params['breadcrumbs'][] = $this->title;
$params = Yii::$app->request->queryParams;
$params = $params ? "&".http_build_query($params) : '';

?>

<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title"><?= Html::encode($this->title) ?></h3>
                <div class="box-tools">
                    <?= Html::create(['edit']) ?>
                    <?= Html::button('导出', [
                        'class'=>'btn btn-success btn-xs',
                        'onclick' => 'batchExport()',
                        ]);
                    ?>
                </div>
            </div>
            <div class="box-body table-responsive">
    <?php echo Html::batchButtons(false)?>         
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => ['class' => 'table table-hover'],
        //'options' => ['style'=>'width:200%'],
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
                'value'=>function($model) {
                    return Html::a($model->repair_no, ['view', 'id' => $model->id,'returnUrl'=>Url::getReturnUrl()], ['style'=>"text-decoration:underline;color:#3c8dbc"]);
                },
                'filter' => Html::activeTextInput($searchModel, 'repair_no', [
                    'class' => 'form-control',
                ]),
                'format' => 'raw',
                'headerOptions' => ['width'=>'300'],
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
                'attribute' => 'produce_sn',
                'value' => 'produce_sn',
                'filter' => Html::activeTextInput($searchModel, 'produce_sn', [
                    'class' => 'form-control',
                ]),
                'format' => 'raw',
                'headerOptions' => ['class' => 'col-md-1'],
            ],
            /*[
                'attribute' => 'bill_m_no',
                'value' => 'bill_m_no',
                'filter' => Html::activeTextInput($searchModel, 'bill_m_no', [
                    'class' => 'form-control',
                ]),
                'format' => 'raw',
                'headerOptions' => ['class' => 'col-md-1'],
            ],*/
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
                'class' => 'yii\grid\ActionColumn',
                'header' => '操作',
                'contentOptions' => ['style' => ['white-space' => 'nowrap']],
                'template' => '{edit} {apply} {audit} {orders} {finish} {receiving} {status} {delete} ',
                'buttons' => [
                'edit' => function($url, $model, $key){
                    if($model->repair_status == \addons\Warehouse\common\enums\RepairStatusEnum::SAVE) {
                        return Html::edit(['edit', 'id' => $model->id, 'returnUrl' => Url::getReturnUrl()]);
                    }
                 },
                'apply' => function($url, $model, $key){
                    if($model->repair_status == \addons\Warehouse\common\enums\RepairStatusEnum::SAVE){
                        return Html::edit(['ajax-apply','id'=>$model->id], '提审', [
                            'class'=>'btn btn-success btn-sm',
                            'onclick' => 'rfTwiceAffirm(this,"提交审核", "确定操作吗？");return false;',
                        ]);
                    }
                },
                'audit' => function($url, $model, $key){
                   if($model->repair_status == \addons\Warehouse\common\enums\RepairStatusEnum::APPLY){
                        return Html::edit(['ajax-audit','id'=>$model->id], '审核', [
                            'class'=>'btn btn-success btn-sm',
                            'data-toggle' => 'modal',
                            'data-target' => '#ajaxModal',
                        ]);
                    }
                },
                'orders' => function($url, $model, $key){
                    if($model->repair_status == \addons\Warehouse\common\enums\RepairStatusEnum::FINISHED){
                        return Html::edit(['ajax-orders','id'=>$model->id], '下单', [
                            'class'=>'btn btn-success btn-sm',
                            'onclick' => 'rfTwiceAffirm(this,"提交下单", "确定操作吗？");return false;',
                        ]);
                    }
                },
                'finish' => function($url, $model, $key){
                    if($model->repair_status == \addons\Warehouse\common\enums\RepairStatusEnum::ORDERS){
                        return Html::edit(['ajax-finish','id'=>$model->id], '完毕', [
                            'class'=>'btn btn-success btn-sm',
                            'onclick' => 'rfTwiceAffirm(this,"提交完毕", "确定操作吗？");return false;',
                        ]);
                    }
                },
                'receiving' => function($url, $model, $key){
                    if($model->repair_status == \addons\Warehouse\common\enums\RepairStatusEnum::FINISH){
                        return Html::edit(['ajax-receiving','id'=>$model->id], '收货', [
                            'class'=>'btn btn-success btn-sm',
                            'onclick' => 'rfTwiceAffirm(this,"提交收货", "确定操作吗？");return false;',
                        ]);
                    }
                },
                'delete' => function($url, $model, $key){
                     if($model->repair_status == \addons\Warehouse\common\enums\RepairStatusEnum::SAVE) {
                         return Html::delete(['delete', 'id' => $model->id], '关闭',[
                             'onclick' => 'rfTwiceAffirm(this,"关闭单据", "确定关闭吗？");return false;',
                         ]);
                    }
                },
                /*'status' => function($url, $model, $key){
                    if($model->audit_status == AuditStatusEnum::PASS) {
                       return Html::status($model['status']);
                    }
                },
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
<script>
    function batchExport() {
        var ids = $("#grid").yiiGridView("getSelectedRows");
        if(ids.length == 0){
            // rfMsg("请选中单据或填写单据ID");
            // return false;
            var url = "<?= Url::to('index?action=export'.$params);?>";
        }else{
            var url = "<?= Url::buildUrl('export',[],['ids'])?>?ids=" + ids;
            window.location.href = url;
        }

        window.location.href = url;
    }

</script>
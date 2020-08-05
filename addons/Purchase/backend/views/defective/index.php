<?php

use common\helpers\Html;
use common\helpers\Url;
use addons\Purchase\common\enums\DefectiveStatusEnum;
use kartik\daterange\DateRangePicker;
use kartik\select2\Select2;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('defective', '不良返厂单');
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
<!--                    --><?//= Html::create(['ajax-edit'], '创建', [
//                        'data-toggle' => 'modal',
//                        'data-target' => '#ajaxModal',
//                    ]); ?>

                    <?= Html::button('导出', [
                        'class'=>'btn btn-success btn-xs',
                        'onclick' => 'batchExport()',
                    ]);?>
                </div>
            </div>
            <div class="box-body table-responsive">
    <?php echo Html::batchButtons(false)?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => ['class' => 'table table-hover'],
        'options' => ['style'=>'white-space:nowrap;'],
        //'options' => ['style'=>' width:120%;'],
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
                'value' => 'id',
                'filter' => Html::activeTextInput($searchModel, 'id', [
                    'class' => 'form-control',
                ]),
                'format' => 'raw',
                'headerOptions' => ['width'=>'60'],
            ],
            [
                'attribute' => 'defective_no',
                'value'=>function($model) {
                    return Html::a($model->defective_no, ['view', 'id' => $model->id, 'returnUrl'=>Url::getReturnUrl()], ['style'=>"text-decoration:underline;color:#3c8dbc"]);
                },
                'filter' => true,
                'format' => 'raw',
                'headerOptions' => ['width'=>'120'],
            ],
            [
                'attribute' => 'supplier_id',
                'value' =>"supplier.supplier_name",
                'filter'=>Select2::widget([
                    'name'=>'SearchModel[supplier_id]',
                    'value'=>$searchModel->supplier_id,
                    'data'=>Yii::$app->supplyService->supplier->getDropDown(),
                    'options' => ['placeholder' =>"请选择",'class' => 'col-md-1'],
                    'pluginOptions' => [
                        'allowClear' => true,
                    ],
                ]),
                'format' => 'raw',
                'headerOptions' => ['class' => 'col-md-2'],
            ],
            [
                'attribute' => 'defective_num',
                'value' => 'defective_num',
                'filter' => Html::activeTextInput($searchModel, 'defective_num', [
                    'class' => 'form-control',
                ]),
                'format' => 'raw',
                'headerOptions' => ['width'=>'60'],
            ],
            [
                'attribute' => 'total_cost',
                'value' => 'total_cost',
                'filter' => Html::activeTextInput($searchModel, 'total_cost', [
                    'class' => 'form-control',
                ]),
                'format' => 'raw',
                'headerOptions' => ['width'=>'100'],
            ],
            [
                'attribute' => 'purchase_sn',
                'value'=>'purchase_sn',
                'filter' => true,
                'format' => 'raw',
                'headerOptions' => ['width'=>'120'],
            ],
            [
                'attribute' => 'receipt_no',
                'value'=>'receipt_no',
                'filter' => true,
                'format' => 'raw',
                'headerOptions' => ['width'=>'120'],
            ],
            [
                'label' => '制单人',
                'attribute' => 'creator.username',
                'filter' => Html::activeTextInput($searchModel, 'creator.username', [
                    'class' => 'form-control',
                ]),
                'headerOptions' => ['width'=>'100'],
            ],
            [
                'attribute' => 'created_at',
                'filter' => DateRangePicker::widget([    // 日期组件
                    'model' => $searchModel,
                    'attribute' => 'created_at',
                    'value' => '',
                    'options' => ['readonly' => true, 'class' => 'form-control',],
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
                'headerOptions' => ['width'=>'200'],
            ],
            /*[
                'label' => '审核人',
                'attribute' => 'auditor.username',
                'headerOptions' => ['class' => 'col-md-1'],
                'filter' => Html::activeTextInput($searchModel, 'auditor.username', [
                    'class' => 'form-control',
                ]),
                'headerOptions' => ['width'=>'100'],

            ],
            [
                'attribute' => 'audit_time',
                'filter' => DateRangePicker::widget([    // 日期组件
                    'model' => $searchModel,
                    'attribute' => 'audit_time',
                    'value' => '',
                    'options' => ['readonly' => true, 'class' => 'form-control',],
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
                'headerOptions' => ['width'=>'200'],
            ],*/
            [
                'attribute' => 'audit_status',
                'value' => function ($model){
                    return \common\enums\AuditStatusEnum::getValue($model->audit_status);
                },
                'filter' => Html::activeDropDownList($searchModel, 'audit_status',\common\enums\AuditStatusEnum::getMap(), [
                    'prompt' => '全部',
                    'class' => 'form-control',
                ]),
                'format' => 'raw',
                'headerOptions' => ['width'=>'100'],
            ],
            [
                'attribute' => 'defective_status',
                'value' => function ($model){
                    return DefectiveStatusEnum::getValue($model->defective_status);
                },
                'filter' => Html::activeDropDownList($searchModel, 'defective_status',DefectiveStatusEnum::getMap(), [
                    'prompt' => '全部',
                    'class' => 'form-control',
                ]),
                'format' => 'raw',
                'headerOptions' => ['width'=>'100'],
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => '操作',
                'template' => '{edit} {ajax-apply} {audit} {goods} {cancel} {delete}',
                'contentOptions' => ['style' => ['white-space' => 'nowrap']],
                'buttons' => [
                'edit' => function($url, $model, $key){
                        if($model->defective_status == DefectiveStatusEnum::SAVE) {
                            return Html::edit(['ajax-edit', 'id' => $model->id, 'returnUrl' => Url::getReturnUrl()], '编辑', [
                                'data-toggle' => 'modal',
                                'data-target' => '#ajaxModal',
                            ]);
                        }
                    },
                'goods' => function($url, $model, $key){
                        return Html::a('单据明细', ['defective-goods/index', 'defective_id' => $model->id,'returnUrl'=>Url::getReturnUrl()], ['class' => 'btn btn-info btn-sm']);
                    },
                'audit' => function($url, $model, $key){
                        if($model->defective_status == DefectiveStatusEnum::PENDING) {
                            return Html::edit(['ajax-audit','id'=>$model->id], '审核', [
                                'class'=>'btn btn-primary btn-sm',
                                'data-toggle' => 'modal',
                                'data-target' => '#ajaxModal',
                            ]);
                        }
                    },
                'ajax-apply' => function($url, $model, $key){
                        if($model->defective_status == DefectiveStatusEnum::SAVE){
                            return Html::edit(['ajax-apply','id'=>$model->id], '提审', [
                                'class'=>'btn btn-success btn-sm',
                                'onclick' => 'rfTwiceAffirm(this,"提交审核", "确定提交吗？");return false;',
                            ]);
                        }
                    },
                'status' => function($url, $model, $key){
                        return Html::status($model['status']);
                    },
                'cancel' => function($url, $model, $key){
                        if($model->defective_status == DefectiveStatusEnum::SAVE) {
                            return Html::delete(['cancel', 'id' => $model->id], '取消', [
                                'class'=>'btn btn-warning btn-sm',
                            ]);
                        }
                    },
                'delete' => function($url, $model, $key){
                        if($model->defective_status == DefectiveStatusEnum::CANCEL) {
                            return Html::delete(['delete', 'id' => $model->id]);
                        }
                    }
                /*'delete' => function($url, $model, $key){
                        return Html::delete(['delete', 'id' => $model->id]);
                    },
                'view'=> function($url, $model, $key){
                    return Html::a('预览', \Yii::$app->params['frontBaseUrl'].'/diamond-details/'.$model->id.'?goodId='.$model->id.'&backend=1',['class'=>'btn btn-info btn-sm','target'=>'_blank']);
                    },
                'show_log' => function($url, $model, $key){
                    return Html::linkButton(['goods-log/index','id' => $model->id, 'type_id' => $model->type_id, 'returnUrl' => Url::getReturnUrl()], '日志');
                    },*/
                ]
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
            var url = "<?= Url::to('index?action=export'.$params);?>";
            rfExport(url)
        }else{
            window.location.href = "<?= Url::buildUrl('export',[],['ids'])?>?ids=" + ids;
        }

    }

</script>

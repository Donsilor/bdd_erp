<?php

use common\enums\WhetherEnum;
use common\helpers\Html;
use common\helpers\Url;
use kartik\daterange\DateRangePicker;
use kartik\select2\Select2;
use yii\grid\GridView;
use addons\Purchase\common\enums\PurchaseStatusEnum;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('receipt', '采购收货单');
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
                    <?= Html::create(['ajax-edit'], '创建', [
                        'data-toggle' => 'modal',
                        'data-target' => '#ajaxModal',
                    ]); ?>
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
                'attribute' => 'receipt_no',
                'value'=>function($model) {
                    return Html::a($model->receipt_no, ['view', 'id' => $model->id,'returnUrl'=>Url::getReturnUrl()], ['style'=>"text-decoration:underline;color:#3c8dbc"]);
                },
                'filter' => Html::activeTextInput($searchModel, 'receipt_no', [
                    'class' => 'form-control',
                ]),
                'format' => 'raw',
                'headerOptions' => ['class' => 'col-md-1'],
            ],
            [
                'attribute' => 'supplier_id',
                'value' =>"supplier.supplier_name",
                'filter'=>Select2::widget([
                    'name'=>'SearchModel[supplier_id]',
                    'value'=>$searchModel->supplier_id,
                    'data'=>Yii::$app->supplyService->supplier->getDropDown(),
                    'options' => ['placeholder' =>"请选择"],
                    'pluginOptions' => [
                        'allowClear' => true,
                    ],
                ]),
                'format' => 'raw',
                'headerOptions' => ['class' => 'col-md-2'],
            ],
            [
                'attribute' => 'put_in_type',
                'format' => 'raw',
                'value' => function ($model){
                    return \addons\Warehouse\common\enums\PutInTypeEnum::getValue($model->put_in_type);
                },
                'filter' => Html::activeDropDownList($searchModel, 'put_in_type',\addons\Warehouse\common\enums\PutInTypeEnum::getMap(), [
                    'prompt' => '全部',
                    'class' => 'form-control',
                ]),
                'headerOptions' => ['class' => 'col-md-1'],
            ],
            [
                'attribute' => 'receipt_num',
                'value' => 'receipt_num',
                'filter' => Html::activeTextInput($searchModel, 'receipt_num', [
                    'class' => 'form-control',
                    'style'=> 'width:60px;'
                ]),
                'format' => 'raw',
                'headerOptions' => [],
            ],
            [
                'attribute' => 'total_cost',
                'value' => 'total_cost',
                'filter' => Html::activeTextInput($searchModel, 'total_cost', [
                    'class' => 'form-control',
                    'style'=> 'width:80px;'
                ]),
                'format' => 'raw',
                'headerOptions' => [],
            ],
            [
                'attribute' => 'delivery_no',
                'value' => 'delivery_no',
                'filter' => Html::activeTextInput($searchModel, 'delivery_no', [
                    'class' => 'form-control',
                    'style'=> 'width:100px;'
                ]),
                'format' => 'raw',
                'headerOptions' => [],
            ],
            [
                'attribute' => 'creator_id',
                'value' => "creator.username",
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
                    'options' => ['readonly' => false, 'class' => 'form-control',],
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
                'headerOptions' => ['class' => 'col-md-2'],
            ],
            /*[
                'attribute' => 'auditor_id',
                'value' => 'auditor.username',
                'headerOptions' => ['class' => 'col-md-1'],
                'filter' => Html::activeTextInput($searchModel, 'auditor.username', [
                    'class' => 'form-control',
                    'style'=> 'width:100px;'
                ]),
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
                'headerOptions' => ['class' => 'col-md-1'],
            ],*/
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
                    'style'=> 'width:100px;'
                ]),
            ],
            [
                'attribute' => 'receipt_status',
                'value' => function ($model){
                    return PurchaseStatusEnum::getValue($model->receipt_status);
                },
                'filter' => Html::activeDropDownList($searchModel, 'receipt_status',PurchaseStatusEnum::getMap(), [
                    'prompt' => '全部',
                    'class' => 'form-control',
                    'style'=> 'width:100px;'
                ]),
                'format' => 'raw',
                'headerOptions' => ['class' => 'col-md-1'],
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => '操作',
                'template' => '{edit} {goods} {apply} {audit} {close} {delete}',
                'contentOptions' => ['style' => ['white-space' => 'nowrap']],
                'buttons' => [
                    'edit' => function($url, $model, $key){
                        if($model->receipt_status == PurchaseStatusEnum::SAVE) {
                            return Html::edit(['ajax-edit', 'id' => $model->id, 'returnUrl' => Url::getReturnUrl()], '编辑', [
                                'data-toggle' => 'modal',
                                'data-target' => '#ajaxModal',
                            ]);
                        }
                    },
                    'apply' => function($url, $model, $key){
                        if($model->receipt_status == PurchaseStatusEnum::SAVE){
                            return Html::edit(['ajax-apply','id'=>$model->id], '提审', [
                                'class'=>'btn btn-success btn-sm',
                                'onclick' => 'rfTwiceAffirm(this,"提交审核", "确定提交吗？");return false;',
                            ]);
                        }
                    },
                    'audit' => function($url, $model, $key){
                        if($model->receipt_status == PurchaseStatusEnum::PENDING) {
                            return Html::edit(['ajax-audit','id'=>$model->id], '审核', [
                                'class'=>'btn btn-success btn-sm',
                                'data-toggle' => 'modal',
                                'data-target' => '#ajaxModal',
                            ]);
                        }
                    },
                    'warehouse' => function($url, $model, $key){
                        if($model->receipt_status == PurchaseStatusEnum::CONFIRM && $model->is_to_warehouse == WhetherEnum::DISABLED) {
                            return Html::edit(['ajax-warehouse','id'=>$model->id], '入库', [
                                'class'=>'btn btn-success btn-sm',
                                'data-toggle' => 'modal',
                                'data-target' => '#ajaxModal',
                            ]);
                        }
                    },
                    'goods' => function($url, $model, $key){
                        return Html::a('明细', ['receipt-goods/index', 'receipt_id' => $model->id,'returnUrl'=>Url::getReturnUrl()], ['class' => 'btn btn-warning btn-sm']);
                    },
                    'close' => function($url, $model, $key){
                        if($model->receipt_status == PurchaseStatusEnum::SAVE) {
                            return Html::delete(['close', 'id' => $model->id],'关闭',[
                                'onclick' => 'rfTwiceAffirm(this,"关闭单据", "确定关闭吗？");return false;',
                            ]);
                        }
                    },
                    'delete' => function($url, $model, $key){
                        if($model->receipt_status == PurchaseStatusEnum::CANCEL) {
                            return Html::delete(['delete', 'id' => $model->id],'删除',[
                                'onclick' => 'rfTwiceAffirm(this,"删除单据", "确定删除吗？");return false;',
                            ]);
                        }
                    },
                ],
                'headerOptions' => ['class' => 'col-md-2'],
            ]
    ]
    ]); ?>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    function batchExport() {
        var ids = $("#grid").yiiGridView("getSelectedRows");
        if(ids.length == 0){
            var url = "<?= Url::to('index?action=export'.$params);?>";
            rfExport(url)
        }else{
            window.location.href = "<?= Url::buildUrl('export',[],['ids'])?>?ids=" + ids;
        }

    }


    function printDetail()
    {
        var valArr = new Array;
        $('input[name="id[]"]:checked').each(function(i){
            valArr[i] = $(this).val();
        });
        if(valArr.length==0){
            rfMsg("您还没有选择任何内容");
            return false;
        }
        var vals = valArr.join(',');
        window.open('/purchase/receipt/print?ids='+vals);
    }
</script>


<?php

use common\helpers\Html;
use common\helpers\Url;
use kartik\select2\Select2;
use yii\grid\GridView;
use kartik\daterange\DateRangePicker;
use common\enums\AuditStatusEnum;
use addons\Warehouse\common\enums\BillStatusEnum;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('warehouse_bill', '盘点单列表');
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
                    'options' => ['style'=>'width:125%;'],
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
                                'attribute'=>'bill_no',
                                'value'=>function ($model){
                                    return Html::a($model->bill_no, ['view', 'id' => $model->id,'returnUrl'=>Url::getReturnUrl()], ['style'=>"text-decoration:underline;color:#3c8dbc"]);
                                },
                                'filter' => Html::activeTextInput($searchModel, 'bill_no', [
                                    'class' => 'form-control',
                                ]),
                                'format' => 'raw',
                                'headerOptions' => ['width'=>'180'],
                        ],
                        [
                                'attribute' => 'bill_type',
                                'format' => 'raw',
                                'headerOptions' => ['width'=>'100'],
                                'value' => function ($model){
                                     return \addons\Warehouse\common\enums\BillTypeEnum::getValue($model->bill_type);
                                },
                                'filter' => false,
                        ],
                        [
                                'label' => '盘点仓库',
                                'attribute' => 'to_warehouse_id',
                                'value' =>"toWarehouse.name",
                                'filter'=>Select2::widget([
                                        'name'=>'SearchModel[to_warehouse_id]',
                                        'value'=>$searchModel->to_warehouse_id,
                                        'data'=>Yii::$app->warehouseService->warehouse->getDropDown(),
                                        'options' => ['placeholder' =>"请选择"],
                                        'pluginOptions' => [
                                             'allowClear' => true,
                                        ],
                                ]),
                                'format' => 'raw',
                                'headerOptions' => ['width'=>'200'],
                        ],      
                        [
                                'label' => '应盘数量',
                                'value' => function($model){
                                    return $model->billW->should_num ?? 0;
                                },
                                'filter' => false,
                                'format' => 'raw',
                                'headerOptions' => ['width' => '100'],
                        ],
                        [
                                'label' => '实盘数量',
                                'value' => function($model){
                                    return $model->billW->actual_num ?? 0;
                                },
                                'filter' => false,                                
                                'format' => 'raw',
                                'headerOptions' => ['width' => '100'],
                        ], 
                        [
                                'label' => '正常数量',
                                'value' => function($model){
                                    return $model->billW->normal_num ?? 0;
                                 },
                                'filter' => false,
                                'format' => 'raw',
                                'headerOptions' => ['width' => '100'],
                        ],
                        [
                                'label' => '盘盈数量',
                                 'value' => function($model){
                                    return $model->billW->profit_num ?? 0;
                                 },
                                'filter' => false,
                                'format' => 'raw',
                                'headerOptions' => ['width' => '100'],
                        ],
                        [
                                'label' => '盘亏数量',
                                'value' => function($model){
                                      return $model->billW->loss_num ?? 0;
                                 },
                                'filter' => false,
                                'format' => 'raw',
                                'headerOptions' => ['width' => '100'],
                        ],                        
                        [
                                'label' => '调整数量',
                                'value' => function($model){
                                    return $model->billW->adjust_num ?? 0;
                                },
                                'filter' => false,
                                'format' => 'raw',
                                'headerOptions' => ['width' => '100'],
                        ],
                        [
                                'label' => '总金额',                                
                                'attribute'=>'total_cost',
                                'filter' => false,
                                'headerOptions' => ['width' => '120'],
                        ],
                        [
                                'label' => '制单人',
                                'attribute' => 'creator_id',
                                'value' => function ($model) {
                                     return $model->creator->username ??'';
                                 },
                                 'headerOptions' => ['width' => '100'],
                                 'filter' => false,

                        ],
                        [
                                'attribute' => 'created_at',
                                'filter' => DateRangePicker::widget([    // 日期组件
                                    'model' => $searchModel,
                                    'attribute' => 'created_at',
                                    'value' => '',
                                    'options' => ['readonly' => false, 'class' => 'form-control'],
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
                                'headerOptions' => ['width'=>'160'],
                        ],
                        [
                                'attribute' => 'audit_status',
                                'format' => 'raw',
                                'headerOptions' => ['width' => '120'],
                                'value' => function ($model){
                                    return AuditStatusEnum::getValue($model->audit_status);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'audit_status',AuditStatusEnum::getMap(), [
                                        'prompt' => '全部',
                                        'class' => 'form-control',
                                        
                                ]),
                        ], 
                        [
                                'attribute' => 'bill_status',
                                'format' => 'raw',
                                'headerOptions' => ['width' => '120'],
                                'value' => function ($model){
                                    return BillStatusEnum::getValue($model->bill_status);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'bill_status',BillStatusEnum::getMap(), [
                                        'prompt' => '全部',
                                        'class' => 'form-control',
                                        
                                ]),
                                ],      
                        [
                                'class' => 'yii\grid\ActionColumn',
                                'header' => '操作',
                                'headerOptions' => ['width'=>'200'],
                                'template' => '{edit} {goods} {audit} {delete}',
                                'buttons' => [
                                   'edit' => function($url, $model, $key){
                                        if($model->bill_status == BillStatusEnum::SAVE){
                                            return Html::edit(['ajax-edit','id' => $model->id,'returnUrl' => Url::getReturnUrl()], '编辑', [
                                                'data-toggle' => 'modal',
                                                'data-target' => '#ajaxModalLg',
                                            ]);
                                        }
                                    }, 
                                    'goods' => function($url, $model, $key){
                                        return Html::edit(['bill-w-goods/index','bill_id' => $model->id,'returnUrl' => Url::getReturnUrl()], '明细',['class'=>'btn btn-warning btn-sm']);
                                    }, 
                                    'audit' => function($url, $model, $key){
                                        if($model->bill_status == BillStatusEnum::PENDING){
                                            return Html::edit(['ajax-audit','id'=>$model->id], '审核', [
                                                    'class'=>'btn btn-success btn-sm',
                                                    'data-toggle' => 'modal',
                                                    'data-target' => '#ajaxModal',
                                            ]);
                                        }
                                    },
                                    'delete' => function($url, $model, $key){
                                        if($model->bill_status == BillStatusEnum::SAVE){
                                            return Html::delete(['delete', 'id' => $model->id], '取消',[
                                                'onclick' => 'rfTwiceAffirm(this,"取消单据", "确定取消吗？");return false;',
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
        if(ids.length == 0){
            var url = "<?= Url::to('index?action=export'.$params);?>";
            rfExport(url)
        }else{
            window.location.href = "<?= Url::buildUrl('export',[],['ids'])?>?ids=" + ids;
        }

    }

</script>
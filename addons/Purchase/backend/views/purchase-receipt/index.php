<?php

use common\enums\BusinessScopeEnum;
use common\helpers\Html;
use common\helpers\Url;
use kartik\daterange\DateRangePicker;
use kartik\select2\Select2;
use yii\grid\GridView;
use common\helpers\ImageHelper;


/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('purchase_receipt', '采购收货单');
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
                        'data-target' => '#ajaxModal',
                    ]); ?>
                </div>
            </div>
            <div class="box-body table-responsive">
    <?php echo Html::batchButtons(false)?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => ['class' => 'table table-hover'],
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
                'attribute' => 'receipt_no',
                'value'=>function($model) {
                    return Html::a($model->receipt_no, ['view', 'id' => $model->id,'returnUrl'=>Url::getReturnUrl()], ['style'=>"text-decoration:underline;color:#3c8dbc"]);
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
                'attribute' => 'receipt_num',
                'value' => 'receipt_num',
                'filter' => Html::activeTextInput($searchModel, 'receipt_num', [
                    'class' => 'form-control',
                ]),
                'format' => 'raw',
                'headerOptions' => ['width'=>'80'],
            ],
            [
                'attribute' => 'total_cost',
                'value' => 'total_cost',
                'filter' => Html::activeTextInput($searchModel, 'total_cost', [
                    'class' => 'form-control',
                ]),
                'format' => 'raw',
                'headerOptions' => ['width'=>'150'],
            ],
            [
                'label' => '制单人',
                'attribute' => 'member.username',
                'headerOptions' => ['class' => 'col-md-1'],
                'filter' => Html::activeTextInput($searchModel, 'member.username', [
                    'class' => 'form-control',
                ]),

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
                'headerOptions' => ['width'=>'160'],
            ],
            [
                'attribute' => 'audit_status',
                'format' => 'raw',
                'headerOptions' => ['class' => 'col-md-1', 'width'=>'60'],
                'value' => function ($model){
                    return \common\enums\AuditStatusEnum::getValue($model->audit_status);
                },
                'filter' => Html::activeDropDownList($searchModel, 'audit_status',\common\enums\AuditStatusEnum::getMap(), [
                    'prompt' => '全部',
                    'class' => 'form-control',
                ]),
            ],
            [
                'attribute' => 'status',
                'format' => 'raw',
                'headerOptions' => ['class' => 'col-md-1', 'width'=>'60'],
                'value' => function ($model){
                    return \common\enums\StatusEnum::getValue($model->status);
                },
                'filter' => Html::activeDropDownList($searchModel, 'status',\common\enums\StatusEnum::getMap(), [
                    'prompt' => '全部',
                    'class' => 'form-control',
                ]),
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => '操作',
                'template' => '{edit} {goods} {audit} {status}',
                'buttons' => [
                'edit' => function($url, $model, $key){
                        return Html::edit(['ajax-edit','id' => $model->id,'returnUrl' => Url::getReturnUrl()],'编辑',[
                            'data-toggle' => 'modal',
                            'data-target' => '#ajaxModal',
                        ]);
                    },
                'goods' => function($url, $model, $key){
                    return Html::a('单据明细', ['purchase-receipt-goods/index', 'receipt_id' => $model->id,'returnUrl'=>Url::getReturnUrl()], ['class' => 'btn btn-warning btn-sm']);
                    },
                'audit' => function($url, $model, $key){
                        if($model->audit_status != 1){
                            return Html::edit(['ajax-audit','id'=>$model->id], '审核', [
                                'class'=>'btn btn-success btn-sm',
                                'data-toggle' => 'modal',
                                'data-target' => '#ajaxModal',
                            ]);
                        }
                    },
                 'status' => function($url, $model, $key){
                        return Html::status($model['status']);
                    },
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

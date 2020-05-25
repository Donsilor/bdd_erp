<?php

use common\helpers\Html;
use common\helpers\Url;
use kartik\select2\Select2;
use yii\grid\GridView;
use kartik\daterange\DateRangePicker;
use common\enums\AuditStatusEnum;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('warehouse_bill', '盘点单列表');
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
                    'options' => ['style'=>'width:110%;'],
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
                                'attribute' => 'from_warehouse_id',
                                'value' =>"fromWarehouse.name",
                                'filter'=>Select2::widget([
                                        'name'=>'SearchModel[from_warehouse_id]',
                                        'value'=>$searchModel->from_warehouse_id,
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
                                'attribute' => 'goods_num',
                                'filter' => false,
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1'],
                        ],
                        [
                                'label' => '实盘数量',
                                'attribute' => 'goods_num',
                                'filter' => false,
                                'value' =>function($model){
                                    return Yii::$app->warehouseService->billW->getPandianCount($model->id);
                                },
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1'],
                        ], 
                                          
                        [
                                'label' => '总金额',                                
                                'attribute'=>'total_cost',
                                'filter' => Html::activeTextInput($searchModel, 'total_cost', [
                                    'class' => 'form-control',
                                ]),
                                'headerOptions' =>['class' => 'col-md-1'],
                        ],
                        [
                                'label' => '制单人',
                                'attribute' => 'creator_id',
                                'value' => function ($model) {
                                     return $model->creator->username ??'';
                                 },
                                'headerOptions' => ['class' => 'col-md-1'],
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
                                'headerOptions' => ['class' => 'col-md-1'],
                                'value' => function ($model){
                                    return AuditStatusEnum::getValue($model->audit_status);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'audit_status',AuditStatusEnum::getMap(), [
                                        'prompt' => '全部',
                                        'class' => 'form-control',
                                        
                                ]),
                        ],                        
                        [
                                'class' => 'yii\grid\ActionColumn',
                                'header' => '操作',
                                'template' => '{edit} {pandian} {audit} {delete}',
                                'buttons' => [
                                    'edit' => function($url, $model, $key){
                                        return Html::edit(['ajax-edit','id' => $model->id,'returnUrl' => Url::getReturnUrl()], '编辑', [
                                            'data-toggle' => 'modal',
                                            'data-target' => '#ajaxModalLg',
                                        ]);
                                    }, 
                                    'pandian' => function($url, $model, $key){
                                        return Html::edit(['pandian','id' => $model->id,'returnUrl' => Url::getReturnUrl()], '盘点',['class'=>'btn btn-warning btn-sm']);
                                    }, 
                                    'audit' => function($url, $model, $key){
                                        if($model->audit_status != AuditStatusEnum::PASS){
                                            return Html::edit(['ajax-audit','id'=>$model->id], '审核', [
                                                    'class'=>'btn btn-success btn-sm',
                                                    'data-toggle' => 'modal',
                                                    'data-target' => '#ajaxModal',
                                            ]);
                                        }
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
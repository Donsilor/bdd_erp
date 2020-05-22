<?php

use common\helpers\Html;
use common\helpers\Url;
use kartik\select2\Select2;
use yii\grid\GridView;
use kartik\daterange\DateRangePicker;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('warehouse_bill', '单据列表');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title"><?= Html::encode($this->title) ?></h3>
            </div>
            <div class="box-body table-responsive">
                <?php echo Html::batchButtons(false)?>
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'tableOptions' => ['class' => 'table table-hover'],
                    'options' => ['style'=>'overflow-x: scroll;'],
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
                            'attribute' => 'bill_no',
                            'value'=>function($model) {
                                return Html::a($model->bill_no, ['view', 'id' => $model->id,'returnUrl'=>Url::getReturnUrl()], ['style'=>"text-decoration:underline;color:#3c8dbc"]);
                            },
                            'filter' => Html::activeTextInput($searchModel, 'bill_no', [
                                'class' => 'form-control',
                                'style'=> 'width:100px;'
                            ]),
                            'format' => 'raw',
                        ],
                        [
                            'attribute' => 'bill_type',
                            'format' => 'raw',
                            'headerOptions' => ['class' => 'col-md-1'],
                            'value' => function ($model){
                                return \addons\Warehouse\common\enums\BillTypeEnum::getValue($model->bill_type);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'bill_type',\addons\Warehouse\common\enums\BillTypeEnum::getMap(), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style' => 'width:80px;',
                            ]),
                        ],
                        [
                            'attribute' => 'audit_status',
                            'format' => 'raw',
                            'headerOptions' => ['class' => 'col-md-1'],
                            'value' => function ($model){
                                return \common\enums\AuditStatusEnum::getValue($model->status);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'audit_status',\common\enums\AuditStatusEnum::getMap(), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style' => 'width:80px;',
                            ]),
                        ],
                        [
                            'attribute' => 'goods_num',
                            'filter' => true,
                            'format' => 'raw',
                            'headerOptions' => ['class' => 'col-md-1'],
                        ],
                        [
                            'attribute' => 'order_type',
                            'format' => 'raw',
                            'headerOptions' => ['class' => 'col-md-1'],
                            'value' => function ($model){
                                return \addons\Warehouse\common\enums\OrderTypeEnum::getValue($model->order_type);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'order_type',\addons\Warehouse\common\enums\OrderTypeEnum::getMap(), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style' => 'width:80px;',
                            ]),
                        ],
                        [
                            'attribute'=>'order_sn',
                            'filter' => Html::activeTextInput($searchModel, 'order_sn', [
                                'class' => 'form-control',
                            ]),
                            'headerOptions' => [],
                        ],
                        [
                            'attribute' => 'from_company_id',
                            'value' =>"supplier.supplier_name",
                            'filter'=>Select2::widget([
                                'name'=>'SearchModel[from_company_id]',
                                'value'=>$searchModel->from_company_id,
                                'data'=>Yii::$app->supplyService->supplier->getDropDown(),
                                'options' => ['placeholder' =>"请选择",'class' => 'col-md-1',
                                    'style' => 'width:160px;'],
                                'pluginOptions' => [
                                    'allowClear' => true,
                                ],
                            ]),
                            'format' => 'raw',
                            'headerOptions' => ['class' => 'col-md-2'],
                        ],
                        [
                            'attribute' => 'from_warehouse_id',
                            'value' =>"supplier.supplier_name",
                            'filter'=>Select2::widget([
                                'name'=>'SearchModel[from_warehouse_id]',
                                'value'=>$searchModel->from_warehouse_id,
                                'data'=>Yii::$app->supplyService->supplier->getDropDown(),
                                'options' => ['placeholder' =>"请选择",'class' => 'col-md-3'],
                                'pluginOptions' => [
                                    'allowClear' => true,
                                ],
                            ]),
                            'format' => 'raw',
                            'headerOptions' => ['style' => 'col-md-3'],
                        ],
                        [
                            'attribute' => 'to_company_id',
                            'value' =>"supplier.supplier_name",
                            'filter'=>Select2::widget([
                                'name'=>'SearchModel[to_company_id]',
                                'value'=>$searchModel->to_company_id,
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
                            'attribute' => 'to_warehouse_id',
                            'value' =>"supplier.supplier_name",
                            'filter'=>Select2::widget([
                                'name'=>'SearchModel[to_warehouse_id]',
                                'value'=>$searchModel->to_warehouse_id,
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
                            'attribute'=>'total_cost',
                            'filter' => Html::activeTextInput($searchModel, 'total_cost', [
                                'class' => 'form-control',
                            ]),
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'total_sale',
                            'filter' => Html::activeTextInput($searchModel, 'total_sale', [
                                'class' => 'form-control',
                            ]),
                            'headerOptions' => [],
                        ],
                        [
                            'label' => '制单人',
                            'attribute' => 'member.username',
                            'headerOptions' => ['class' => 'col-md-1'],
                            'filter' => Html::activeTextInput($searchModel, 'member.username', [
                                'class' => 'form-control',
                                'style' => 'width:100px;',
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
                            'attribute' => 'status',
                            'format' => 'raw',
                            'headerOptions' => ['class' => 'col-md-1'],
                            'value' => function ($model){
                                return \common\enums\StatusEnum::getValue($model->status);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'status',\common\enums\StatusEnum::getMap(), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style' => 'width:160px;',
                            ]),
                        ],
                        [
                            'class' => 'yii\grid\ActionColumn',
                            'header' => '操作',
                            'template' => '{edit} {status} {delete}',
                            'buttons' => [
                                'edit' => function($url, $model, $key){
                                    return Html::edit(['ajax-edit','id' => $model->id,'returnUrl' => Url::getReturnUrl()], '编辑', [
                                        'data-toggle' => 'modal',
                                        'data-target' => '#ajaxModalLg',
                                    ]);
                                },

                                'status' => function($url, $model, $key){
                                    return Html::status($model->status);
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
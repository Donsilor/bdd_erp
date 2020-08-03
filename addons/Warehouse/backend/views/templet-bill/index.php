<?php

use common\helpers\Html;
use common\helpers\Url;
use kartik\select2\Select2;
use yii\grid\GridView;
use kartik\daterange\DateRangePicker;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $searchModel */

$this->title = Yii::t('templet_bill', '出入库列表');
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
                <div class="row">
                    <?php $form = ActiveForm::begin(['action' => ['index'], 'method'=>'get']); ?>
                        <div class="col-xs-3">
                            <?= $form->field($searchModel, 'gold_sn')->textInput(["placeholder"=>"请输入批次号"]) ?>
                        </div>
                        <div class="col-xs-3" style="padding-top: 26px;padding-left: 0px;">
                            <?= Html::submitButton('搜索', ['class' => 'btn btn-primary btn-sm']) ?>
                        </div>
                    <?php ActiveForm::end(); ?>
                </div>
                <?php if(empty($model->gold_sn)){?>
                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'tableOptions' => ['class' => 'table table-hover'],
                        'options' => ['style'=>'white-space:nowrap;'],
                        //'options' => ['style'=>'width:120%;'],
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
                            /*[
                                'attribute' => 'id',
                                'filter' => true,
                                'format' => 'raw',
                                'headerOptions' => ['width'=>'80'],
                            ],*/
                            [
                                'attribute'=>'bill_no',
                                'value'=>function($model) {
                                    return Html::a($model->bill_no, ['view', 'id' => $model->id,'returnUrl'=>Url::getReturnUrl()], ['style'=>"text-decoration:underline;color:#3c8dbc"]);
                                },
                                'filter' => Html::activeTextInput($searchModel, 'bill_no', [
                                    'class' => 'form-control',
                                ]),
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'attribute' => 'bill_type',
                                'value' => function ($model){
                                    return \addons\Warehouse\common\enums\GoldBillTypeEnum::getValue($model->bill_type);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'bill_type',\addons\Warehouse\common\enums\GoldBillTypeEnum::getMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:100px;'

                                ]),
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1','style'=>'width:100px;'],
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
                                        'width' => '200',
                                    ],
                                ]),
                                'format' => 'raw',
                                'headerOptions' => [],
                            ],
                            /*[
                                'attribute' => 'to_warehouse_id',
                                'value' =>"toWarehouse.name",
                                'filter'=>Select2::widget([
                                    'name'=>'SearchModel[to_warehouse_id]',
                                    'value'=>$searchModel->to_warehouse_id,
                                    'data'=>Yii::$app->warehouseService->warehouse::getDropDown(),
                                    'options' => ['placeholder' =>"请选择"],
                                    'pluginOptions' => [
                                        'allowClear' => true,
                                        'width' => '200',
                                    ],
                                ]),
                                'format' => 'raw',
                                'headerOptions' => [],
                            ],
                            [
                                'attribute'=>'total_num',
                                'filter' => Html::activeTextInput($searchModel, 'total_num', [
                                    'class' => 'form-control',
                                ]),
                                'headerOptions' => ['width'=>'120'],
                            ],*/
                            [
                                'attribute'=>'total_weight',
                                'filter' => Html::activeTextInput($searchModel, 'total_weight', [
                                    'class' => 'form-control',
                                ]),
                                'headerOptions' => ['width'=>'120'],
                            ],
                            [
                                'attribute'=>'total_cost',
                                'filter' => Html::activeTextInput($searchModel, 'total_cost', [
                                    'class' => 'form-control',
                                ]),
                                'headerOptions' => ['width'=>'120'],
                            ],
                            [
                                'attribute'=>'delivery_no',
                                'filter' => Html::activeTextInput($searchModel, 'delivery_no', [
                                    'class' => 'form-control',
                                ]),
                                'headerOptions' => ['width'=>'120'],
                            ],
                            [
                                'attribute' => 'creator_id',
                                'value' => 'creator.username',
                                'filter' => Html::activeTextInput($searchModel, 'creator.username', [
                                    'class' => 'form-control',
                                ]),
                                'headerOptions' => ['width'=>'80'],
                            ],
                            [
                                'attribute'=>'created_at',
                                'filter' => DateRangePicker::widget([    // 日期组件
                                    'model' => $searchModel,
                                    'attribute' => 'created_at',
                                    'value' => $searchModel->created_at,
                                    'options' => ['readonly' => false,'class'=>'form-control','style'=>'width:100px;'],
                                    'pluginOptions' => [
                                        'format' => 'yyyy-mm-dd',
                                        'locale' => [
                                            'separator' => '/',
                                        ],
                                        'endDate' => date('Y-m-d',time()),
                                        'todayHighlight' => true,
                                        'autoclose' => true,
                                        'todayBtn' => 'linked',
                                        'clearBtn' => true,
                                    ],
                                ]),
                                'value'=>function($model){
                                    return Yii::$app->formatter->asDatetime($model->created_at);
                                }
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
                                    'style'=> 'width:100px;'
                                ]),
                            ],
                            [
                                'attribute' => 'bill_status',
                                'value' => function ($model){
                                    return \addons\Warehouse\common\enums\BillStatusEnum::getValue($model->bill_status);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'bill_status',\addons\Warehouse\common\enums\BillStatusEnum::getMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:100px;'

                                ]),
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1','style'=>'width:120px;'],
                            ],
                        ]
                    ]); ?>
                <?php }else{ ?>
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
                            /*[
                                'attribute' => 'id',
                                'filter' => true,
                                'format' => 'raw',
                                'headerOptions' => ['width'=>'80'],
                            ],*/
                            [
                                'attribute'=>'gold_sn',
                                'filter' =>false,
                                'headerOptions' => ['width'=>'100'],
                            ],
                            [
                                'attribute' => 'bill_type',
                                'value' => function ($model){
                                    return \addons\Warehouse\common\enums\GoldBillTypeEnum::getValue($model->bill_type);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'bill_type',\addons\Warehouse\common\enums\GoldBillTypeEnum::getMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:100px;'

                                ]),
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1','style'=>'width:100px;'],
                            ],
                            [
                                'attribute'=>'bill_no',
                                'value'=>function($model) {
                                    return Html::a($model->bill_no, ['view', 'id' => $model->bill->id,'returnUrl'=>Url::getReturnUrl()], ['style'=>"text-decoration:underline;color:#3c8dbc"]);
                                },
                                'filter' => Html::activeTextInput($searchModel, 'bill_no', [
                                    'class' => 'form-control',
                                ]),
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'attribute' => 'bill.bill_status',
                                'format' => 'raw',
                                'value' => function ($model){
                                    return \addons\Warehouse\common\enums\BillStatusEnum::getValue($model->bill->bill_status);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'bill.bill_status',\addons\Warehouse\common\enums\BillStatusEnum::getMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                ]),
                                'headerOptions' => ['width'=>'100'],
                            ],
                            /*[
                                'attribute' => 'to_warehouse_id',
                                'value' =>"toWarehouse.name",
                                'filter'=>Select2::widget([
                                    'name'=>'SearchModel[to_warehouse_id]',
                                    'value'=>$searchModel->to_warehouse_id,
                                    'data'=>Yii::$app->warehouseService->warehouse::getDropDown(),
                                    'options' => ['placeholder' =>"请选择"],
                                    'pluginOptions' => [
                                        'allowClear' => true,
                                        'width' => '200',
                                    ],
                                ]),
                                'format' => 'raw',
                                'headerOptions' => [],
                            ],
                            [
                                'attribute'=>'total_num',
                                'filter' => Html::activeTextInput($searchModel, 'total_num', [
                                    'class' => 'form-control',
                                ]),
                                'headerOptions' => ['width'=>'120'],
                            ],*/
                            [
                                'attribute'=>'gold_weight',
                                'filter' => Html::activeTextInput($searchModel, 'gold_weight', [
                                    'class' => 'form-control',
                                ]),
                                'headerOptions' => ['width'=>'120'],
                            ],
                            [
                                'attribute'=>'cost_price',
                                'filter' => Html::activeTextInput($searchModel, 'cost_price', [
                                    'class' => 'form-control',
                                ]),
                                'headerOptions' => ['width'=>'120'],
                            ],
                            [
                                'attribute' => 'bill.supplier_id',
                                'value' =>"bill.supplier.supplier_name",
                                'filter'=>Select2::widget([
                                    'name'=>'SearchModel[supplier_id]',
                                    'value'=>$searchModel->supplier_id,
                                    'data'=>Yii::$app->supplyService->supplier->getDropDown(),
                                    'options' => ['placeholder' =>"请选择",'class' => 'col-md-4', 'style'=> 'width:120px;'],
                                    'pluginOptions' => [
                                        'allowClear' => true,
                                        'width' => '120',
                                    ],
                                ]),
                                'format' => 'raw',
                                'headerOptions' => [],
                            ],
                            [
                                'label' => '制单人',
                                'attribute' => 'bill.creator_id',
                                'value' =>"bill.creator.username",
                                'filter' => false,
                                'headerOptions' => ['width'=>'80'],
                            ],
                            [
                                'attribute' => 'bill.created_at',
                                'filter' => DateRangePicker::widget([    // 日期组件
                                    'model' => $searchModel,
                                    'attribute' => 'bill.created_at',
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
                                    return Yii::$app->formatter->asDatetime($model->bill->created_at);
                                },
                                'format' => 'raw',
                                'headerOptions' => ['width'=>'160'],
                            ],
                            [
                                'label' => '审核人',
                                'attribute' => 'bill.auditor_id',
                                'value' =>"bill.auditor.username",
                                'filter' => false,
                                'headerOptions' => ['width'=>'80'],
                            ],
                            [
                                'attribute' => 'bill.audit_time',
                                'filter' => DateRangePicker::widget([    // 日期组件
                                    'model' => $searchModel,
                                    'attribute' => 'bill.audit_time',
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
                                    return Yii::$app->formatter->asDatetime($model->bill->audit_time);
                                },
                                'format' => 'raw',
                                'headerOptions' => ['width'=>'160'],
                            ],
                            [
                                'attribute' => 'bill.audit_status',
                                'format' => 'raw',
                                'value' => function ($model){
                                    return \common\enums\AuditStatusEnum::getValue($model->bill->audit_status);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'bill.audit_status',\common\enums\AuditStatusEnum::getMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                ]),
                                'headerOptions' => ['width'=>'80'],
                            ],
                        ]
                    ]); ?>
                <?php }?>
            </div>
        </div>
    </div>
</div>
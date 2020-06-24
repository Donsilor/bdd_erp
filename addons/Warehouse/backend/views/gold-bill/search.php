<?php

use addons\Warehouse\common\enums\BillStatusEnum;
use common\helpers\Html;
use common\helpers\Url;
use kartik\select2\Select2;
use yii\grid\GridView;
use kartik\daterange\DateRangePicker;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('gold_bill', '单据列表');
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
                <?php $form = ActiveForm::begin(['action' => ['search'], 'method'=>'get']); ?>
                    <div class="col-xs-3">
                        <?= $form->field($model, 'gold_sn')->textInput(["placeholder"=>"请输入批次号"]) ?>
                    </div>
                    <div class="col-xs-3" style="padding-top: 26px;padding-left: 0px;">
                        <?= Html::submitButton('搜索', ['class' => 'btn btn-primary btn-sm']) ?>
                    </div>
                <?php ActiveForm::end(); ?>
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'tableOptions' => ['class' => 'table table-hover'],
                    'options' => ['style'=>'width:120%;'],
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
                        [
                            'attribute'=>'gold_sn',
                            'filter' =>false,
                            'headerOptions' => ['width'=>'100'],
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
                        /*[
                            'class' => 'yii\grid\ActionColumn',
                            'header' => '操作',
                            'contentOptions' => ['style' => ['white-space' => 'nowrap']],
                            'template' => '{apply} {audit} {goods} {delete}',
                            'buttons' => [
                                'edit' => function($url, $model, $key){
                                    if(in_array($model->bill_status, [BillStatusEnum::SAVE])){
                                        return Html::edit(['ajax-edit', 'id' => $model->id, 'returnUrl' => Url::getReturnUrl()], '编辑', [
                                            'data-toggle' => 'modal',
                                            'data-target' => '#ajaxModalLg',
                                        ]);
                                    }
                                },
                                'apply' => function($url, $model, $key){
                                    if($model->bill_status == BillStatusEnum::SAVE){
                                        return Html::edit(['ajax-apply','id'=>$model->id], '提审', [
                                            'class'=>'btn btn-success btn-sm',
                                            'onclick' => 'rfTwiceAffirm(this,"提交审核", "确定提交吗？");return false;',
                                        ]);
                                    }
                                },
                                'audit' => function($url, $model, $key){
                                    if(in_array($model->bill_status,[BillStatusEnum::PENDING])){
                                        return Html::edit(['ajax-audit','id'=>$model->id], '审核', [
                                            'class'=>'btn btn-success btn-sm',
                                            'data-toggle' => 'modal',
                                            'data-target' => '#ajaxModal',
                                        ]);
                                    }
                                },
                                'goods' => function($url, $model, $key){
                                    return Html::a('明细', ['gold-bill-l-goods/index', 'bill_id' => $model->id,'returnUrl'=>Url::getReturnUrl()], ['class' => 'btn btn-warning btn-sm']);
                                },
                                'delete' => function($url, $model, $key){
                                    if($model->bill_status == BillStatusEnum::SAVE) {
                                        return Html::delete(['delete', 'id' => $model->id],'取消');
                                    }
                                },
                            ],
                        ]*/
                    ]
                ]); ?>
            </div>
        </div>
    </div>
</div>
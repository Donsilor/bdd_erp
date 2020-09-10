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

$this->title = Yii::t('finance_sale', '财务出库单');
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
                    <?= Html::button('导出', [
                        'class'=>'btn btn-success btn-xs',
                        'onclick' => 'batchExport()',
                    ]);?>
                </div>
            </div>
            <div class="box-body table-responsive">
                <?php echo Html::batchButtons(false)?>
                <!--<div class="row">
                    <?php $form = ActiveForm::begin(['action' => ['index'], 'method'=>'get']); ?>
                        <div class="col-xs-3">
                            <?= $form->field($searchModel, 'goods_id')->textInput(["placeholder"=>"请输入货号"]) ?>
                        </div>
                        <div class="col-xs-3">
                            <?= $form->field($searchModel, 'goods_id')->textInput(["placeholder"=>"请输入货号"]) ?>
                        </div>
                        <div class="col-xs-3" style="padding-top: 26px;padding-left: 0px;">
                            <?= Html::submitButton('搜索', ['class' => 'btn btn-primary btn-sm']) ?>
                        </div>
                    <?php ActiveForm::end(); ?>
                </div>-->
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
                                'headerOptions' => ['width'=>'30'],
                            ],
                            /*[
                                'attribute' => 'id',
                                'filter' => false,
                                'format' => 'raw',
                                'headerOptions' => ['width'=>'60'],
                            ],*/
                            [
                                'label' => '出库单号',
                                'attribute' => 'bill_no',
                                'value'=>function($model) {
                                    return $model->bill_no??"";
                                    //return Html::a($model->bill_no, ['view', 'id' => $model->bill->id,'returnUrl'=>Url::getReturnUrl()], ['style'=>"text-decoration:underline;color:#3c8dbc"]);
                                },
                                'filter' => Html::activeTextInput($searchModel, 'bill_no', [
                                    'class' => 'form-control',
                                ]),
                                'format' => 'raw',
                                'headerOptions' => ['width'=>'100'],
                            ],
                            [
                                'label' => '出库时间',
                                'attribute' => 'bill.audit_time',
                                'filter' => DateRangePicker::widget([    // 日期组件
                                    'model' => $searchModel,
                                    'attribute' => 'bill.audit_time',
                                    'value' => '',
                                    'options' => ['readonly' => false, 'class' => 'form-control', 'style'=> 'width:200px;'],
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
                                'label' => '销售渠道',
                                'attribute' => 'bill.channel_id',
                                'value' => function ($model){
                                    return $model->bill->saleChannel->name ?? '';
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'bill.channel_id',Yii::$app->salesService->saleChannel->getDropDown(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:120px;'
                                ]),
                                'format' => 'raw',
                                'headerOptions' => [],
                            ],
                            [
                                'label' => '客户姓名',
                                'attribute'=>'bill.order.customer_name',
                                'value' => function ($model){
                                    return $model->bill->order->customer_name ?? '';
                                },
                                'filter' =>false,
                                'headerOptions' => ['width'=>'120'],
                            ],
                            [
                                'attribute'=>'goods_name',
                                'filter' =>false,
                                'headerOptions' => ['width'=>'120'],
                            ],
                            [
                                'attribute' => 'goods.product_type_id',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1'],
                                'value' => function($model){
                                    return $model->goods->productType->name ?? '';
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'goods.product_type_id',Yii::$app->styleService->productType::getDropDown(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:120px;'
                                ]),
                            ],
                            [
                                'attribute'=>'goods_id',
                                'filter' =>false,
                                'headerOptions' => ['width'=>'120'],
                            ],
                            [
                                'label' => '工厂成本',
                                'attribute'=>'goods.factory_cost',
                                'filter' =>false,
                                'headerOptions' => ['width'=>'120'],
                            ],
                            [
                                'label' => '商品成本价',
                                'attribute'=>'goods.cost_price',
                                'visible' => \common\helpers\Auth::verify(\common\enums\SpecialAuthEnum::VIEW_CAIGOU_PRICE),
                                'filter' =>false,
                                'headerOptions' => ['width'=>'120'],
                            ],
                            [
                                'label' => '商品实际售价',
                                'attribute'=>'sale_price',
                                'filter' =>false,
                                'headerOptions' => ['width'=>'120'],
                            ],
                            [
                                'label' => '支付方式',
                                'attribute' => 'bill.order.payType.name',
                                'format' => 'raw',
                                'value' => function($model){
                                    return $model->bill->order->payType->name ?? '';
                                },
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'label' => '外部订单号',
                                'attribute'=>'bill.order.out_trade_no',
                                'filter' =>false,
                                'headerOptions' => ['width'=>'120'],
                            ],
                            [
                                'label' => '订单销售人员',
                                'attribute'=>'bill.order.follower.username',
                                'filter' =>false,
                                'headerOptions' => ['width'=>'120'],
                            ],
                            /*[
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
                                'attribute' => 'bill.supplier_id',
                                'value' =>"bill.supplier.supplier_name",
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
                                'headerOptions' => ['width'=>'200'],
                            ],
                            [
                                'attribute' => 'bill.to_warehouse_id',
                                'value' =>"bill.toWarehouse.name",
                                'filter'=>Select2::widget([
                                    'name'=>'SearchModel[to_warehouse_id]',
                                    'value'=>$searchModel->to_warehouse_id,
                                    'data'=>Yii::$app->warehouseService->warehouse::getDropDown(),
                                    'options' => ['placeholder' =>"请选择",'class' => 'col-md-4', 'style'=> 'width:120px;'],
                                    'pluginOptions' => [
                                        'allowClear' => true,
                                    ],
                                ]),
                                'format' => 'raw',
                                'headerOptions' => ['width'=>'160'],
                            ],
                            //'from_warehouse_id',
                            [
                                'attribute' => 'bill.from_warehouse_id',
                                'value' =>"bill.fromWarehouse.name",
                                'filter'=>Select2::widget([
                                    'name'=>'SearchModel[from_warehouse_id]',
                                    'value'=>$searchModel->from_warehouse_id,
                                    'data'=>Yii::$app->warehouseService->warehouse::getDropDown(),
                                    'options' => ['placeholder' =>"请选择",'class' => 'col-md-4','style'=> 'width:120px;'],
                                    'pluginOptions' => [
                                        'allowClear' => true,
                                    ],
                                ]),
                                'format' => 'raw',
                                'headerOptions' => ['width'=>'160'],
                            ],
                            //'to_warehouse_id',
                            [
                                'attribute' => 'goods_num',
                                'filter' => false,
                                'format' => 'raw',
                                'headerOptions' => ['width'=>'80'],
                            ],
                            [
                                'attribute'=>'cost_price',
                                'filter' => false,
                                'headerOptions' => ['width'=>'80'],
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
                            ],*/
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
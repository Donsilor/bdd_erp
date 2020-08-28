<?php

use common\helpers\Html;
use common\helpers\Url;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = '订单发货质检';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-sm-12">
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
                        [
                                'attribute' => 'id',
                                'filter' => true,
                                'format' => 'raw',
                                'headerOptions' => ['width'=>'30'],
                        ],
                        [
                                'attribute' => 'order_sn',
                                'value'=>function($model) {
                                    return Html::a($model->order_sn, ['view', 'id' => $model->id,'returnUrl'=>Url::getReturnUrl()], ['style'=>"text-decoration:underline;color:#3c8dbc"]);
                                },
                                'filter' => Html::activeTextInput($searchModel, 'order_sn', [
                                    'class' => 'form-control',
                                    'style'=> 'width:150px;'
                                ]),
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1'],
                        ],
                        [
                                'attribute' => 'customer_name',
                                'value' => 'customer_name',
                                'filter' => Html::activeTextInput($searchModel, 'customer_name', [
                                        'class' => 'form-control',
                                        'style'=> 'width:100px;'
                                ]),
                                'format' => 'raw',
                                'headerOptions' => ['width'=>'100'],
                        ],
                        /* [
                                'attribute' => 'goods_num',
                                'value' => "goods_num",
                                'filter' => false,
                                'format' => 'raw',
                                'headerOptions' => ['width'=>'80'],
                        ], */
                        [
                                'attribute' => 'account.order_amount',
                                'value' => function($model){
                                     return \common\helpers\AmountHelper::outputAmount($model->account->order_amount??0,2,$model->currency);
                                },
                                'filter' => false,
                                'format' => 'raw',
                                'headerOptions' => ['width'=>'100'],
                        ],
                        [
                                'attribute' => 'sale_channel_id',
                                'value' => function ($model){
                                    return $model->saleChannel->name ?? '';
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'sale_channel_id',Yii::$app->salesService->saleChannel->getDropDown(), [
                                        'prompt' => '全部',
                                        'class' => 'form-control',
                                        'style'=> 'width:120px;'
                                ]),
                                'format' => 'raw',
                                'headerOptions' => [],
                        ],
                        [
                                'attribute' => 'order_type',
                                'value' =>function($model){
                                     return \addons\Sales\common\enums\OrderTypeEnum::getValue($model->order_type);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'order_type',\addons\Sales\common\enums\OrderTypeEnum::getMap(), [
                                        'prompt' => '全部',
                                        'class' => 'form-control',
                                        'style'=> 'width:80px;'
                                ]),
                                'format' => 'raw',
                                'headerOptions' => [],
                        ],
                        [
                                'attribute' => 'pay_status',
                                'value' => function ($model){
                                    return \addons\Sales\common\enums\PayStatusEnum::getValue($model->pay_status);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'pay_status',\addons\Sales\common\enums\PayStatusEnum::getMap(), [
                                        'prompt' => '全部',
                                        'class' => 'form-control',
                                ]),
                                'format' => 'raw',
                                'headerOptions' => ['width'=>'100'],
                        ],
                        [
                                'attribute' => 'distribute_status',
                                'value' => function ($model){
                                    return \addons\Sales\common\enums\DistributeStatusEnum::getValue($model->distribute_status);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'distribute_status',\addons\Sales\common\enums\DistributeStatusEnum::getMap(), [
                                        'prompt' => '全部',
                                        'class' => 'form-control',
                                ]),
                                'format' => 'raw',
                                'headerOptions' => ['width'=>'100'],
                       ],
                        [
                                'attribute' => 'delivery_status',
                                'value' => function ($model){
                                    return \addons\Sales\common\enums\DeliveryStatusEnum::getValue($model->delivery_status);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'delivery_status',\addons\Sales\common\enums\DeliveryStatusEnum::getMap(), [
                                        'prompt' => '全部',
                                        'class' => 'form-control',
                                ]),
                                'format' => 'raw',
                                'headerOptions' => ['width'=>'100'],
                        ],
                        [
                                'attribute' => 'order_status',
                                'value' => function ($model){
                                     return \addons\Sales\common\enums\OrderStatusEnum::getValue($model->order_status);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'order_status',\addons\Sales\common\enums\OrderStatusEnum::getMap(), [
                                        'prompt' => '全部',
                                        'class' => 'form-control',
                                ]),
                                'format' => 'raw',
                                'headerOptions' => ['width'=>'100'],
                        ],
                        [
                            'class' => 'yii\grid\ActionColumn',
                            'header' => '操作',
                            'template' => '{view}',
                            'buttons' => [
                                'view' => function ($url, $model, $key) {
                                    if($model->delivery_status == \addons\Sales\common\enums\DeliveryStatusEnum::SAVE){
                                        return Html::a('质检', ['view', 'id' => $model->id,'returnUrl'=>Url::getReturnUrl()], ['class' => 'btn btn-primary btn-sm']);
                                    }else{
                                        return Html::a('查看', ['view', 'id' => $model->id,'returnUrl'=>Url::getReturnUrl()], ['class' => 'btn btn-warning btn-sm']);
                                    }
                                },
                            ]
                        ]
                    ]
                  ]);
                ?>
                </div>
            </div>
        </div>
    </div>
<script>
    function batchExport() {
        var ids = $("#grid").yiiGridView("getSelectedRows");
        if(ids.length == 0){
            var url = "<?= Url::to('index',(['action'=>'export'] + Yii::$app->request->queryParams));?>";
            rfExport(url)
        }else{
            window.location.href = "<?= Url::buildUrl('export',[],['ids'])?>?ids=" + ids;
        }

    }
</script>

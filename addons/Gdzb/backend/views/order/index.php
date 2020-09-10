<?php

use common\helpers\Html;
use common\helpers\Url;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = '订单列表';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-sm-12">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title"><?= Html::encode($this->title) ?></h3>
                <div class="box-tools">
                    <?= Html::create(['ajax-edit'], '创建', [
                        'data-toggle' => 'modal',
                        'data-target' => '#ajaxModalLg',
                    ]); ?>
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
                    'attribute'=>'order_time',
                    'value'=>function($model){
                        return Yii::$app->formatter->asDatetime($model->order_time);
                     },
                    'filter' => \kartik\daterange\DateRangePicker::widget([    // 日期组件
                            'model' => $searchModel,
                            'attribute' => 'order_time',
                            'value' => $searchModel->order_time,
                            'options' => ['readonly' => false,'class'=>'form-control','style'=>'background-color:#fff;width:150px;'],
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
                    'headerOptions' => ['class' => 'col-md-1'],

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
                'attribute' => 'channel_id',
                'value' => function ($model){
                    return $model->saleChannel->name ?? '';
                },
                'filter' => Html::activeDropDownList($searchModel, 'channel_id',Yii::$app->salesService->saleChannel->getDropDown(), [
                    'prompt' => '全部',
                    'class' => 'form-control',
                    'style'=> 'width:120px;'
                ]),
                'format' => 'raw',
                'headerOptions' => [],
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
            [
                    'label' => '联系方式',
                    'attribute' => 'customer_mobile',
                    'value' => function($model){
                          $str = '';
                          $str .= $model->customer_mobile ? $model->customer_mobile."<br/>":'';
                          $str .= $model->customer_email ? $model->customer_email."<br/>":'';
                          return $str;
                    },
                    'filter' => false,
                    'format' => 'raw',
                    'headerOptions' => ['width'=>'80'],
            ],
/*                
            [
                    'attribute' => 'goods_num',
                    'value' => "goods_num",
                    'filter' => false,
                    'format' => 'raw',
                    'headerOptions' => ['width'=>'80'],
            ], */
            [
                    'attribute' => 'order_amount',
                    'value' => function($model){
                         return \common\helpers\AmountHelper::outputAmount($model->order_amount??0,2,$model->currency);
                    },
                    'filter' => false,
                    'format' => 'raw',
                    'headerOptions' => ['width'=>'100'],
            ],
            [
                    'attribute' => 'refund_amount',
                    'value' => function($model){
                        return \common\helpers\AmountHelper::outputAmount($model->refund_amount??0,2,$model->currency);
                    },
                    'filter' => false,
                    'format' => 'raw',
                    'headerOptions' => ['width'=>'100'],
            ],


            [
                    'attribute' => 'pay_status',
                    'value' => function ($model){
                        return \addons\Sales\common\enums\PayStatusEnum::getValue($model->pay_status);
                    },
                    'filter' => Html::activeDropDownList($searchModel, 'pay_status',\addons\Sales\common\enums\PayStatusEnum::getMap(), [
                            'prompt' => '全部',
                            'class' => 'form-control',
                            'style' =>'width:80px'
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
                            'style' =>'width:80px'
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
                            'style' =>'width:80px'
                    ]),
                    'format' => 'raw',
                    'headerOptions' => ['width'=>'100'],
            ],
            [
                'attribute' => 'refund_status',
                'value' => function ($model){
                    return \addons\Sales\common\enums\RefundStatusEnum::getValue($model->refund_status);
                },
                'filter' => Html::activeDropDownList($searchModel, 'refund_status',\addons\Sales\common\enums\RefundStatusEnum::getMap(), [
                    'prompt' => '全部',
                    'class' => 'form-control',
                    'style' =>'width:80px'
                ]),
                'format' => 'raw',
                'headerOptions' => ['width'=>'100'],
            ],

            [
                'attribute' => 'creator_id',
                'value' => 'creator.username',
                'headerOptions' => ['class' => 'col-md-1'],
                'filter' =>Html::activeTextInput($searchModel, 'creator.username', [
                    'class' => 'form-control',

                ]),

            ],
            [
                'attribute'=>'created_at',
                'filter' => \kartik\datetime\DateTimePicker::widget([    // 日期组件
                    'model' => $searchModel,
                    'attribute' => 'created_at',
                    'value' => $searchModel->created_at,
                    'options' => ['readonly' => false,'class'=>'form-control','style'=>'background-color:#fff;width:100px;'],
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
            /* [
                    'attribute' => 'follower_id',
                    'value' => function($model){
                        return $model->follower->username ?? '';
                    },
                    'filter' => false,
                    'format' => 'raw',
                    'headerOptions' => ['width'=>'100'],
            ], */
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => '操作',
                'template' => '{edit} {audit} {ajax-apply} {apply} {follower} {close}',
                'buttons' => [
                    'edit' => function($url, $model, $key){
                     if($model->order_status == \addons\Sales\common\enums\OrderStatusEnum::SAVE) {
                         return Html::edit(['ajax-edit', 'id' => $model->id, 'returnUrl' => Url::getReturnUrl()], '编辑', [
                             'data-toggle' => 'modal',
                             'data-target' => '#ajaxModalLg',
                             'class' => 'btn btn-primary btn-sm',
                         ]);
                     }
                    },
                    'ajax-apply' => function($url, $model, $key){
                        if($model->order_status == \addons\Sales\common\enums\OrderStatusEnum::SAVE){
                            return Html::edit(['ajax-apply','id'=>$model->id], '提审', [
                                'class'=>'btn btn-success btn-sm',
                                'onclick' => 'rfTwiceAffirm(this,"提交审核", "确定提交吗？");return false;',
                            ]);
                        }
                    },
                    'audit' => function($url, $model, $key){
                        $model->getTargetType();
                        if($model->targetType){
                            $isAudit = Yii::$app->services->flowType->isAudit($model->targetType,$model->id);
                        }else{
                            $isAudit = true;
                        }
                        if($model->order_status == \addons\Sales\common\enums\OrderStatusEnum::PENDING && $isAudit) {
                            return Html::edit(['ajax-audit', 'id' => $model->id], '审核', [
                                'class' => 'btn btn-success btn-sm',
                                'data-toggle' => 'modal',
                                'data-target' => '#ajaxModal',
                            ]);
                        }

                    },                    
                    'close' => function($url, $model, $key){
                       
                            return Html::delete(['delete', 'id' => $model->id],'关闭',[
                                'onclick' => 'rfTwiceAffirm(this,"关闭单据", "确定关闭吗？");return false;',
                            ]);

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

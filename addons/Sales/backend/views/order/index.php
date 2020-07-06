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
                    'headerOptions' => ['width'=>'80'],
            ],  
            [
                    'attribute'=>'created_at',
                    'value'=>function($model){
                           return Yii::$app->formatter->asDatetime($model->created_at);
                     },
                    'filter' => \kartik\daterange\DateRangePicker::widget([    // 日期组件
                            'model' => $searchModel,
                            'attribute' => 'created_at',
                            'value' => $searchModel->created_at,
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
                    'attribute' => 'order_type',
                    'value' =>function($model){
                          return \addons\Sales\common\enums\OrderTypeEnum::getValue($model->order_type);
                    },
                    'filter' => Html::activeDropDownList($searchModel, 'order_type',\addons\Sales\common\enums\OrderTypeEnum::getMap(), [
                            'prompt' => '全部',
                            'class' => 'form-control',
                    ]),
                    'format' => 'raw',
                    'headerOptions' => ['class' => 'col-md-1'],
            ],            
            [
                    'attribute' => 'goods_num',
                    'value' => "goods_num",
                    'filter' => false,
                    'format' => 'raw',
                    'headerOptions' => ['width'=>'80'],
            ],
            [
                    'attribute' => 'account.order_amount',
                    'value' => function(){
                         return $model->account->order_amount ?? '';
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
                    ]),
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
                    'attribute' => 'follower_id',
                    'value' => function($model){
                        return $model->follower->username ?? '';
                    },
                    'filter' => false,
                    'format' => 'raw',
                    'headerOptions' => ['width'=>'100'],
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => '操作',
                'template' => '{edit} {audit} {apply} {follower} {close}',
                'buttons' => [
                    'edit' => function($url, $model, $key){
                            return Html::edit(['ajax-edit','id' => $model->id,'returnUrl' => Url::getReturnUrl()],'编辑',[
                                    'data-toggle' => 'modal',
                                    'data-target' => '#ajaxModalLg',
                                    'class'=>'btn btn-primary btn-sm',
                            ]);

                    },                    
                    'audit' => function($url, $model, $key){
                        
                            return Html::edit(['ajax-audit','id'=>$model->id], '审核', [
                                    'class'=>'btn btn-success btn-sm',
                                    'data-toggle' => 'modal',
                                    'data-target' => '#ajaxModal',
                             ]); 

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

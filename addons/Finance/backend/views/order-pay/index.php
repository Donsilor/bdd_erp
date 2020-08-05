<?php

use common\helpers\Html;
use common\helpers\Url;
use yii\grid\GridView;
use addons\Sales\common\enums\PayStatusEnum;
use common\helpers\AmountHelper;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = '订单点款';
$this->params['breadcrumbs'][] = $this->title;
$params = Yii::$app->request->queryParams;
$params = $params ? "&".http_build_query($params) : '';
?>

<div class="row">
    <div class="col-sm-12">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title"><?= Html::encode($this->title) ?></h3>
                <div class="box-tools">

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
                    'label'=>'订单时间',
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
                        return $model->order_sn;
                    },
                    'filter' => Html::activeTextInput($searchModel, 'order_sn', [
                            'class' => 'form-control',
                            'style'=> 'width:150px;'
                    ]),
                    'format' => 'raw',
                    'headerOptions' => ['width'=>'150'],
            ],
            [
                    'attribute' => 'customer_name',
                    'value'=>function($model) {
                        return $model->customer_name;
                    },
                    'filter' => Html::activeTextInput($searchModel, 'customer_name', [
                            'class' => 'form-control',
                            'style'=> 'width:100px;'
                    ]),
                    'format' => 'raw',
                    'headerOptions' => ['width'=>'100'],
            ],
            [
                    'attribute' => 'account.pay_amount',
                    'value' => function ($model){
                        return AmountHelper::outputAmount($model->account->pay_amount,2,$model->account->currency);
                    },
                    'filter' => false,
                    'format' => 'raw',
                    'headerOptions' => ['width'=>'100'],
            ],            
            [
                    'attribute' => 'account.paid_amount',
                    'value' => function ($model){
                        return AmountHelper::outputAmount($model->account->paid_amount,2,$model->account->currency);
                    },
                    'filter' => false,
                    'format' => 'raw',
                    'headerOptions' => ['width'=>'100'],
            ],
            [
                    'attribute' => 'account.unpay_amount',
                    'value' => function ($model){
                        $unpay_amount = $model->account->pay_amount - $model->account->paid_amount;                       
                        $unpay_amount_str = AmountHelper::outputAmount($unpay_amount,2,$model->account->currency);
                        if($unpay_amount > 0) {
                            $unpay_amount_str = "<b style='color:red'>".$unpay_amount_str."</b>";
                        }
                        return $unpay_amount_str;
                    },
                    'filter' => false,
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
                   'attribute' => 'pay_type',
                   'value' => function ($model){
                        return $model->payType->name ?? '未知';
                   },
                   'filter' => Html::activeDropDownList($searchModel, 'order_status',Yii::$app->salesService->payment->getDropDown(), [
                           'prompt' => '全部',
                           'class' => 'form-control',
                           'style' =>'width:100px'
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
                            'style' =>'width:80px'
                    ]),
                    'format' => 'raw',
                    'headerOptions' => ['width'=>'100'],
           ],
           [
                   'attribute' => 'payLogs.pay_sn',
                   'value'=>function($model) {
                           $pay_sn = '';
                           foreach ($model->payLogs ??[] as $payLog){
                               $pay_sn .= $payLog->pay_sn;
                           }
                           return $pay_sn;
                    },
                    'filter' => Html::activeTextInput($searchModel, 'payLogs.pay_sn', [
                            'class' => 'form-control',
                            'style'=> 'width:150px;'
                    ]),
                    'format' => 'raw',
                    'headerOptions' => ['width'=>'150'],
           ],
           [
                   'attribute' => 'payLogs.creator',
                   'value' => function ($model){
                       $creator = '';
                       foreach ($model->payLogs ??[] as $payLog){
                           $creator .= $payLog->creator;
                       }
                       return $creator;
                    },
                    'filter' => false,
                    'format' => 'raw',
                    'headerOptions' => ['width'=>'100'],
           ],
           [
                'class' => 'yii\grid\ActionColumn',
                'header' => '操作',
                'template' => '{edit}',
                'buttons' => [
                    'edit' => function($url, $model, $key){
                           if($model->pay_status == PayStatusEnum::NO_PAY) {
                                return Html::edit(['ajax-edit', 'id' => $model->id, 'returnUrl' => Url::getReturnUrl()], '点款', [
                                    'data-toggle' => 'modal',
                                    'data-target' => '#ajaxModalLg',
                                    'class' => 'btn btn-primary btn-sm',
                                ]);
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
            var url = "<?= Url::to('index?action=export'.$params);?>";
            rfExport(url)
        }else{
            window.location.href = "<?= Url::buildUrl('export',[],['ids'])?>?ids=" + ids;
        }

    }

</script>

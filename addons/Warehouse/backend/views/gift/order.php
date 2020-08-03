<?php

use common\helpers\Html;
use common\helpers\Url;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = Yii::t('customer_order', '订单信息');
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box-body nav-tabs-custom">
    <h2 class="page-header"><?= $this->title; ?> - <?= $gift->gift_sn?> - <?= \addons\Warehouse\common\enums\GiftStatusEnum::getValue($gift->gift_status)?></h2>
    <?php echo Html::menuTab($tabList,$tab)?>
    <div class="box-tools" style="float:right;margin-top:-40px; margin-right: 20px;">
    </div>
    <div class="tab-content">
        <div class="col-xs-12" style="padding-left: 0px;padding-right: 0px;">
            <div class="box">
            <div class="box-body table-responsive">
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'tableOptions' => ['class' => 'table table-hover'],
                    //'options' => ['style'=>'white-space:nowrap;'],
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
                            'headerOptions' => ['width'=>'60'],
                        ],
                        [
                            'attribute' => 'id',
                            'filter' => true,
                            'format' => 'raw',
                            'headerOptions' => ['width'=>'60'],
                        ],
                        [
                            'attribute' => 'order_sn',
                            'value'=>function($model) {
                                return Html::a($model->order_sn, ['order/view', 'id' => $model->id,'returnUrl'=>Url::getReturnUrl()], ['style'=>"text-decoration:underline;color:#3c8dbc"]);
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
                                'style'=> 'width:80px;'
                            ]),
                            'format' => 'raw',
                            'headerOptions' => ['class' => 'col-md-1'],
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
                            'headerOptions' => ['class' => 'col-md-1'],
                        ],
                        [
                            'attribute' => 'account.order_amount',
                            'value' => function($model){
                                return \common\helpers\AmountHelper::outputAmount($model->account->order_amount??0,2,$model->currency);
                            },
                            'filter' => false,
                            'format' => 'raw',
                            'headerOptions' => ['class' => 'col-md-1'],
                        ],
                        [
                            'attribute' => 'account.refund_amount',
                            'value' => function($model){
                                return \common\helpers\AmountHelper::outputAmount($model->account->refund_amount??0,2, $model->currency);
                            },
                            'filter' => false,
                            'format' => 'raw',
                            'headerOptions' => ['class' => 'col-md-1'],
                        ],
                        [
                            'attribute' => 'order_from',
                            'value' => function ($model){
                                return \addons\Sales\common\enums\OrderFromEnum::getValue($model->order_from);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'order_from',\addons\Sales\common\enums\OrderFromEnum::getMap(), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                            ]),
                            'format' => 'raw',
                            'headerOptions' => ['class' => 'col-md-1'],
                        ],
                        [
                            'attribute' => 'out_trade_no',
                            'value'=>"out_trade_no",
                            'filter' => Html::activeTextInput($searchModel, 'out_trade_no', [
                                'class' => 'form-control',
                            ]),
                            'format' => 'raw',
                            'headerOptions' => ['class' => 'col-md-1'],
                        ],
                        [
                            'attribute'=>'created_at',
                            'value'=>function($model){
                                return Yii::$app->formatter->asDatetime($model->created_at);
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
                            'attribute' => 'follower_id',
                            'value' => function($model){
                                return $model->follower->username ?? '';
                            },
                            'filter' => false,
                            'format' => 'raw',
                            'headerOptions' => ['class' => 'col-md-1'],
                        ],
                        [
                            'attribute' => 'order_status',
                            'value' => function ($model){
                                $model->getTargetType();
                                $audit_name_str = '';
                                if($model->targetType){
                                    $audit_name = Yii::$app->services->flowType->getCurrentUsersName($model->targetType,$model->id);
                                    $audit_name_str = $audit_name ? "({$audit_name})" : "";
                                }
                                return \addons\Sales\common\enums\OrderStatusEnum::getValue($model->order_status).$audit_name_str;
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'order_status',\addons\Sales\common\enums\OrderStatusEnum::getMap(), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                            ]),
                            'format' => 'raw',
                            'headerOptions' => ['class' => 'col-md-1'],
                        ],
                    ]
                  ]);
                ?>
            </div>
            </div>
        </div>
        <!-- box end -->
    </div>
    <!-- tab-content end -->
</div>

<?php

use common\helpers\Html;
use common\helpers\Url;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = '订单列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box-body nav-tabs-custom">
    <h2 class="page-header"><?= $this->title ?> - <?= $model->supplier_code?> - <?= \common\enums\AuditStatusEnum::getValue($model->audit_status)?></h2>
    <?php echo Html::menuTab($tabList,$tab)?>
    <div class="row">
        <div class="col-sm-12">
            <div class="box">
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
                        return Html::a($model->order_sn, ['order/view', 'id' => $model->id,'returnUrl'=>Url::getReturnUrl()], ['class'=>'openContab','style'=>"text-decoration:underline;color:#3c8dbc"]);
                    },
                    'filter' => Html::activeTextInput($searchModel, 'order_sn', [
                        'class' => 'form-control',
                        'style'=> 'width:150px;'
                    ]),
                    'format' => 'raw',
                    'headerOptions' => ['class' => 'col-md-1'],
                ],
                [
                        'attribute'=>'order_time',
                        'value'=>function($model){
                            return Yii::$app->formatter->asDate($model->order_time);
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
                    'headerOptions' => ['width'=>'120'],
                ],

                [
                    'label' => '客户信息',
                    'attribute' => 'customer_mobile',
                    'value' => function($model){
                        $str = '';
                        $str .= $model->customer_name ? $model->customer_name."<br/>":'';
                        $str .= $model->customer_mobile ? $model->customer_mobile."<br/>":'';
                        $str .= $model->customer_weixin ? $model->customer_weixin."<br/>":'';
                        return $str;
                    },
                    'filter' => false,
                    'format' => 'raw',
                    'headerOptions' => ['width'=>'80'],
                ],

                [
                    'attribute' => 'goods_num',
                    'value' => function($model){
                        return $model->goods_num??0;
                    },
                    'filter' => false,
                    'format' => 'raw',
                    'headerOptions' => ['width'=>'100'],
                ],

                [
                    'attribute' => 'cost_price',
                    'value' => function($model){
                        return $model->cost_price??0;
                    },
                    'filter' => false,
                    'format' => 'raw',
                    'headerOptions' => ['width'=>'100'],
                ],
                [
                        'attribute' => 'order_amount',
                        'value' => function($model){
                             return $model->order_amount??0;
                        },
                        'filter' => false,
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
                    'template' => '{edit} {audit} {ajax-apply} {apply} {follower} {close}',
                    'buttons' => [
                        'edit' => function($url, $model, $key){
                         if($model->order_status == \addons\Sales\common\enums\OrderStatusEnum::SAVE) {
                             return Html::edit(['order/edit', 'id' => $model->id, 'returnUrl' => Url::getReturnUrl()], '编辑');
                         }
                        },
                        'ajax-apply' => function($url, $model, $key){
                            if($model->order_status == \addons\Sales\common\enums\OrderStatusEnum::SAVE){
                                return Html::edit(['order/ajax-apply','id'=>$model->id], '提审', [
                                    'class'=>'btn btn-success btn-sm',
                                    'onclick' => 'rfTwiceAffirm(this,"提交审核", "确定提交吗？");return false;',
                                ]);
                            }
                        },
                        'audit' => function($url, $model, $key){
                            if($model->order_status == \addons\Sales\common\enums\OrderStatusEnum::PENDING) {
                                return Html::edit(['order/ajax-audit', 'id' => $model->id], '审核', [
                                    'class' => 'btn btn-success btn-sm',
                                    'data-toggle' => 'modal',
                                    'data-target' => '#ajaxModal',
                                ]);
                            }

                        },
                        'close' => function($url, $model, $key){
                            if($model->order_status == \addons\Sales\common\enums\OrderStatusEnum::SAVE) {
                                return Html::delete(['order/delete', 'id' => $model->id], '关闭', [
                                    'onclick' => 'rfTwiceAffirm(this,"关闭单据", "确定关闭吗？");return false;',
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

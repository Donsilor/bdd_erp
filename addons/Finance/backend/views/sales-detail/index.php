<?php

use common\helpers\Html;
use common\helpers\Url;
use yii\grid\GridView;
use common\enums\AuditStatusEnum;
use addons\Purchase\common\enums\PurchaseStatusEnum;
use common\enums\TargetTypeEnum;
use addons\Finance\common\enums\FinanceStatusEnum;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = '财务销售明细单';
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
                    <?= Html::button('导出', [
                        'class'=>'btn btn-success btn-xs',
                        'onclick' => 'batchExport()',
                    ]);?>
                </div>
            </div>
            <div class="box-body table-responsive">  
    <?php //echo Html::batchButtons()?>                  
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
                    'attribute' => 'orde_sn',
                    'value'=>function($model) {
                        return Html::a($model->orde_sn, ['../sales/order/view', 'id' => $model->order_id,'returnUrl'=>Url::getReturnUrl()], ['style'=>"text-decoration:underline;color:#3c8dbc"]);
                    },
                    'filter' => Html::activeTextInput($searchModel, 'orde_sn', [
                            'class' => 'form-control',
                            'style'=> 'width:150px;'
                    ]),
                    'format' => 'raw',
                    //'headerOptions' => ['width'=>'150'],
            ],

            [
                'attribute' => 'dept_id',
                'value'=>function($model) {
                    return $model->department->name ?? '';
                },
                'filter' => Html::activeDropDownList($searchModel, 'dept_id',Yii::$app->services->department->getDropDown(), [
                    'prompt' => '全部',
                    'class' => 'form-control',
                    'style'=> 'width:100px;'
                ]),
                'format' => 'raw',
                //'headerOptions' => ['width'=>'150'],
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
                    'attribute' => 'goods_name',
                    'format' => 'raw',
                    'headerOptions' => ['width'=>'100'],
            ],
            [
                'attribute' => 'product_type_id',
                'value' => function($model){
                    return $model->type->name ?? '';
                },
                'filter' => Html::activeDropDownList($searchModel, 'product_type_id',Yii::$app->styleService->productType->getDropDown(), [
                    'prompt' => '全部',
                    'class' => 'form-control',
                    'style'=>'width:120px;'
                ]),
                'format' => 'raw',
                'headerOptions' => ['width'=>'120'],
            ],

            [
                'attribute' => 'goods_sn',
                'value' => function($model){
                    return $model->goods_sn;
                },
                'filter' => Html::activeTextInput($searchModel, 'goods_sn', [
                    'class' => 'form-control',
                    'style'=> 'width:80px;'
                ]),
                'format' => 'raw',
                'headerOptions' => ['width'=>'80'],
            ],

            [
                'attribute' => 'goods_num',
                'filter' => false,
                'headerOptions' => ['width'=>'100'],
            ],

            [
                'attribute' => 'goods_price',
                'filter' => false,
                'headerOptions' => ['width'=>'100'],
            ],
            [
                'attribute'=>'pay_time',
                'filter' => \kartik\daterange\DateRangePicker::widget([    // 日期组件
                    'model' => $searchModel,
                    'attribute' => 'pay_time',
                    'value' => $searchModel->pay_time,
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
                'value'=>function($model){
                    return Yii::$app->formatter->asDate($model->pay_time);
                }

            ],
            [
                'attribute' => 'sale_price',
                'filter' => false,
                'headerOptions' => ['width'=>'100'],
            ],
            [
                'attribute'=>'delivery_time',
                'filter' => \kartik\daterange\DateRangePicker::widget([    // 日期组件
                    'model' => $searchModel,
                    'attribute' => 'delivery_time',
                    'value' => $searchModel->delivery_time,
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
                'value'=>function($model){
                    return Yii::$app->formatter->asDate($model->delivery_time);
                }

            ],
            [
                'attribute' => 'cost_price',
				'visible' => \common\helpers\Auth::verify(\common\enums\SpecialAuthEnum::VIEW_CAIGOU_PRICE),
                'filter' => false,
                'headerOptions' => ['width'=>'100'],
            ],
            [
                'attribute'=>'refund_time',
                'filter' => \kartik\daterange\DateRangePicker::widget([    // 日期组件
                    'model' => $searchModel,
                    'attribute' => 'refund_time',
                    'value' => $searchModel->refund_time,
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
                'value'=>function($model){
                    return Yii::$app->formatter->asDate($model->refund_time);
                }

            ],
            [
                'attribute' => 'refund_price',
                'filter' => false,
                'headerOptions' => ['width'=>'100'],
            ],
            [
                'attribute'=>'return_time',
                'filter' => \kartik\daterange\DateRangePicker::widget([    // 日期组件
                    'model' => $searchModel,
                    'attribute' => 'return_time',
                    'value' => $searchModel->return_time,
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
                'value'=>function($model){
                    return Yii::$app->formatter->asDate($model->return_time);
                }

            ],
            [
                'attribute' => 'return_by',
                'value' => function($model){
                    return \addons\Sales\common\enums\ReturnByEnum::getValue($model->return_by);
                },
                'filter' => Html::activeDropDownList($searchModel, 'return_by',\addons\Sales\common\enums\ReturnByEnum::getMap(), [
                    'prompt' => '全部',
                    'class' => 'form-control',
                    'style'=>'width:120px;'
                ]),
                'format' => 'raw',
                'headerOptions' => ['width'=>'120'],
            ],
            [
                'attribute' => 'remark',
                'filter' => false,

            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => '操作',
                'template' => '{goods} {edit} {ajax-audit} {close}',
                'buttons' => [
                    'edit' => function($url, $model, $key){
                        return Html::edit(['ajax-edit','id' => $model->id,'returnUrl' => Url::getReturnUrl(), 'id' => $model->id], '编辑', [
                            'data-toggle' => 'modal',
                            'data-target' => '#ajaxModalLg',
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
            var url = "<?= Url::to('index?action=export'.$params);?>";
            rfExport(url)
        }else{
            window.location.href = "<?= Url::buildUrl('export',[],['ids'])?>?ids=" + ids;
        }

    }

</script>

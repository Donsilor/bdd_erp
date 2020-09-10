<?php

use addons\Warehouse\common\enums\BillStatusEnum;
use common\helpers\Html;
use common\helpers\ImageHelper;
use common\helpers\Url;
use yii\grid\GridView;
use kartik\daterange\DateRangePicker;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('index', '退款单列表');
$this->params['breadcrumbs'][] = $this->title;

$params = Yii::$app->request->queryParams;
$params = $params ? "&".http_build_query($params) : '';

?>
<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title"><?= Html::encode($this->title) ?></h3>
<!--                <div class="box-tools" style="right: 100px;">-->
<!--                    --><?//= Html::create(['ajax-edit'], '创建', [
//                        'data-toggle' => 'modal',
//                        'data-target' => '#ajaxModalLg',
//                    ]); ?>
<!--                </div>-->
<!--                <div class="box-tools" >-->
<!--                    <a href="--><?//= Url::to(['index?action=export'.$params])?><!--" class="blue">导出Excel</a>-->
<!--                </div>-->
            </div>
            <div class="box-body table-responsive">
                <?php echo Html::batchButtons(false)?>
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
                            'attribute'=>'return_no',
                            'format' => 'raw',
                            'value'=>function($model) {
                                return Html::a($model->return_no, ['view', 'id' => $model->id,'returnUrl'=>Url::getReturnUrl()], ['style'=>"text-decoration:underline;color:#3c8dbc"]);
                            },
                            'filter' => Html::activeTextInput($searchModel, 'return_no', [
                                'class' => 'form-control',
                            ]),
                            'headerOptions' => ['class' => 'col-md-1'],
                        ],
                        [
                            'attribute'=>'order_sn',
                            'format' => 'raw',
                            'value'=>function($model) {
                                return Html::a($model->order_sn, ['order/view', 'id' => $model->order_id,'returnUrl'=>Url::getReturnUrl()], ['style'=>"text-decoration:underline;color:#3c8dbc"]);
                            },
                            'filter' => Html::activeTextInput($searchModel, 'order_sn', [
                                'class' => 'form-control',
                            ]),
                            'headerOptions' => ['class' => 'col-md-1'],
                        ],
                        [
                            'attribute' => 'return_type',
                            'format' => 'raw',
                            'value' => function ($model){
                                $str = \addons\Sales\common\enums\ReturnTypeEnum::getValue($model->return_type);
                                if($model->return_type == \addons\Sales\common\enums\ReturnTypeEnum::TRANSFER && !empty($model->new_order_sn) && !empty($model->new_order_id)){
                                    $str.="(".Html::a($model->new_order_sn, ['order/view', 'id' => $model->new_order_id,'returnUrl'=>Url::getReturnUrl()], ['style'=>"text-decoration:underline;color:#3c8dbc"]).")";
                                }
                                return $str??"";
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'return_type',\addons\Sales\common\enums\ReturnTypeEnum::getMap(), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style'=> 'width:80px;',
                            ]),
                            'headerOptions' => ['class' => 'col-md-1'],
                        ],
                        [
                            'attribute' => 'return_by',
                            'format' => 'raw',
                            'value' => function ($model){
                                return \addons\Sales\common\enums\ReturnByEnum::getValue($model->return_by);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'return_by',\addons\Sales\common\enums\ReturnByEnum::getMap(), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style'=> 'width:80px;',
                            ]),
                            'headerOptions' => ['class' => 'col-md-1'],
                        ],
                        [
                            'attribute' => 'goods_num',
                            'filter' => false,
                            'headerOptions' => ['width'=>'80'],
                        ],
                        [
                            'attribute'=>'customer_name',
                            'filter' => Html::activeTextInput($searchModel, 'customer_name', [
                                'class' => 'form-control',
                            ]),
                            'headerOptions' => ['class' => 'col-md-1'],
                        ],
                        'customer_mobile',
                        [
                            'attribute'=>'should_amount',
                            'filter' => false,
                            'headerOptions' => ['class' => 'col-md-1'],
                        ],
//                        [
//                            'attribute'=>'apply_amount',
//                            'filter' => false,
//                            'headerOptions' => ['class' => 'col-md-1'],
//                        ],
                        [
                            'attribute'=>'real_amount',
                            'filter' => false,
                            'headerOptions' => ['class' => 'col-md-1'],
                        ],
                        [
                            'attribute'=>'created_at',
                            'filter' => DateRangePicker::widget([    // 日期组件
                                'model' => $searchModel,
                                'attribute' => 'created_at',
                                'value' => $searchModel->created_at,
                                'options' => ['readonly' => false,'class'=>'form-control','style'=>'background-color:#fff;width:160px;'],
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
                            'attribute' => 'creator_id',
                            'value' => 'creator.username',
                            'headerOptions' => ['class' => 'col-md-1'],
                            'filter' => Html::activeTextInput($searchModel, 'creator.username', [
                                'class' => 'form-control',
                            ]),
                        ],
//                        [
//                            'attribute'=>'goods_id',
//                            'filter' => Html::activeTextInput($searchModel, 'goods_id', [
//                                'class' => 'form-control',
//                            ]),
//                            'headerOptions' => ['class' => 'col-md-1'],
//                        ],
                        [
                            'attribute' => 'leader_status',
                            'format' => 'raw',
                            'headerOptions' => ['class' => 'col-md-1'],
                            'value' => function ($model){
                                return \common\enums\AuditStatusEnum::getValue($model->leader_status);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'leader_status',\common\enums\AuditStatusEnum::getMap(), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style'=> 'width:60px;',
                            ]),
                        ],
                        [
                            'attribute' => 'storekeeper_status',
                            'format' => 'raw',
                            'headerOptions' => ['class' => 'col-md-1'],
                            'value' => function ($model){
                                return \common\enums\AuditStatusEnum::getValue($model->storekeeper_status);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'storekeeper_status',\common\enums\AuditStatusEnum::getMap(), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style'=> 'width:60px;',
                            ]),
                        ],
                        [
                            'attribute' => 'finance_status',
                            'format' => 'raw',
                            'headerOptions' => ['class' => 'col-md-1'],
                            'value' => function ($model){
                                return \common\enums\AuditStatusEnum::getValue($model->finance_status);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'finance_status',\common\enums\AuditStatusEnum::getMap(), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style'=> 'width:60px;',
                            ]),
                        ],
                        [
                            'attribute' => 'return_status',
                            'format' => 'raw',
                            'headerOptions' => ['class' => 'col-md-1'],
                            'value' => function ($model){
                                return \addons\Sales\common\enums\ReturnStatusEnum::getValue($model->return_status);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'return_status',\addons\Sales\common\enums\ReturnStatusEnum::getMap(), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style'=> 'width:60px;',
                            ]),
                        ],
                        [
                            'class' => 'yii\grid\ActionColumn',
                            'header' => '操作',
                            'template' => '{edit} {apply} {audit} {view} {cancel}',
                            'buttons' => [
                                'edit' => function($url, $model, $key){
                                    if($model->audit_status == \common\enums\AuditStatusEnum::SAVE){
                                        return Html::edit(['ajax-edit','id' => $model->id,'returnUrl' => Url::getReturnUrl()], '编辑', [
                                            'data-toggle' => 'modal',
                                            'data-target' => '#ajaxModalLg',
                                        ]);
                                    }
                                },
                                'apply' => function($url, $model, $key){
                                    if($model->audit_status == \common\enums\AuditStatusEnum::SAVE){
                                        return Html::edit(['ajax-apply','id'=>$model->id], '提审', [
                                            'class'=>'btn btn-info btn-sm',
                                            'onclick' => 'rfTwiceAffirm(this,"提交审核", "确定提交吗？");return false;',
                                        ]);
                                    }
                                },
//                                'audit' => function($url, $model, $key){
//                                    if($model->audit_status == \common\enums\AuditStatusEnum::PENDING) {
//                                        return Html::edit(['ajax-audit','id'=>$model->id], '审核', [
//                                            'class'=>'btn btn-success btn-sm',
//                                            'data-toggle' => 'modal',
//                                            'data-target' => '#ajaxModal',
//                                        ]);
//                                    }
//                                },
                                'view' => function($url, $model, $key){
                                    return Html::a('查看', ['return-goods/index', 'return_id' => $model->id,'returnUrl'=>Url::getReturnUrl()], ['class' => 'btn btn-warning btn-sm']);
                                },
                                'cancel' => function($url, $model, $key){
                                    if($model->audit_status == \common\enums\AuditStatusEnum::SAVE) {
                                        return Html::delete(['cancel', 'id' => $model->id],'取消', [
                                            //'class'=>'btn btn-info btn-sm',
                                            'onclick' => 'rfTwiceAffirm(this,"取消退款", "确定取消吗？");return false;',
                                        ]);
                                    }
                                },
//                                'status' => function($url, $model, $key){
//                                    if($model->audit_status == \common\enums\AuditStatusEnum::PASS) {
//                                         return Html::status($model->status);
//                                    }
//                                },
//                                'delete' => function($url, $model, $key){
//                                    return Html::delete(['delete', 'id' => $model->id]);
//                                },
                            ],
                        ]
                    ]
                ]); ?>
            </div>
        </div>
    </div>
</div>
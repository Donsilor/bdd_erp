<?php

use common\helpers\Html;
use common\helpers\Url;
use yii\grid\GridView;
use kartik\daterange\DateRangePicker;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('stone_bill_ms', '买石单');
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
                        [
                            'label' => '序号',
                            'attribute' => 'id',
                            'filter' => true,
                            'format' => 'raw',
                            'headerOptions' => ['width'=>'80'],
                        ],
                        [
                            'label' => '单据类型',
                            'attribute'=>'bill_type',
                            'filter' => Html::activeTextInput($searchModel, 'bill_type', [
                                'class' => 'form-control',
                            ]),
                            'headerOptions' => ['width'=>'100'],
                        ],
                        [
                            'label' => '单据状态',
                            'attribute'=>'bill_status',
                            'filter' => Html::activeTextInput($searchModel, 'bill_status', [
                                'class' => 'form-control',
                            ]),
                            'headerOptions' => ['width'=>'100'],
                        ],
                        [
                            'label' => '供应商',
                            'attribute'=>'supplier_id',
                            'filter' => Html::activeTextInput($searchModel, 'supplier_id', [
                                'class' => 'form-control',
                            ]),
                            'headerOptions' => ['width'=>'160'],
                        ],
                        [
                            'label' => '石包总数',
                            'attribute'=>'goods_num',
                            'filter' => Html::activeTextInput($searchModel, 'goods_num', [
                                'class' => 'form-control',
                            ]),
                            'headerOptions' => ['width'=>'100'],
                        ],
                        [
                            'label' => '石包总重量',
                            'attribute'=>'goods_weight',
                            'filter' => Html::activeTextInput($searchModel, 'goods_weight', [
                                'class' => 'form-control',
                            ]),
                            'headerOptions' => ['width'=>'100'],
                        ],
                        [
                            'label' => '石包总价',
                            'attribute'=>'goods_total',
                            'filter' => Html::activeTextInput($searchModel, 'goods_total', [
                                'class' => 'form-control',
                            ]),
                            'headerOptions' => ['width'=>'100'],
                        ],
                        [
                            'label' => '纸质单号',
                            'attribute'=>'send_goods_sn',
                            'filter' => Html::activeTextInput($searchModel, 'send_goods_sn', [
                                'class' => 'form-control',
                            ]),
                            'headerOptions' => ['width'=>'120'],
                        ],
                        [
                            'label' => '制单人',
                            'attribute'=>'creator_id',
                            'filter' => Html::activeTextInput($searchModel, 'creator_id', [
                                'class' => 'form-control',
                            ]),
                            'headerOptions' => ['width'=>'120'],
                        ],
                        [
                            'label' => '制单时间',
                            'attribute'=>'created_at',
                            'filter' => Html::activeTextInput($searchModel, 'created_at', [
                                'class' => 'form-control',
                            ]),
                            'headerOptions' => ['width'=>'160'],
                        ],
                        [
                            'label' => '审核时间',
                            'attribute'=>'audit_time',
                            'filter' => Html::activeTextInput($searchModel, 'audit_time', [
                                'class' => 'form-control',
                            ]),
                            'headerOptions' => ['width'=>'160'],
                        ],
                        [
                            'class' => 'yii\grid\ActionColumn',
                            'header' => '操作',
                            'contentOptions' => ['style' => ['white-space' => 'nowrap']],
                            'template' => '{edit} {audit} {status} {delete}',
                            'buttons' => [
                                'edit' => function($url, $model, $key){
                                    if(in_array($model->audit_status,[\common\enums\AuditStatusEnum::PENDING ,\common\enums\AuditStatusEnum::UNPASS])){
                                        return Html::edit(['ajax-edit', 'id' => $model->id, 'returnUrl' => Url::getReturnUrl()], '编辑', [
                                            'data-toggle' => 'modal',
                                            'data-target' => '#ajaxModalLg',
                                        ]);
                                    }
                                },
                                'audit' => function($url, $model, $key){
                                    if(in_array($model->audit_status,[\common\enums\AuditStatusEnum::PENDING ,\common\enums\AuditStatusEnum::UNPASS])){
                                        return Html::edit(['ajax-audit','id'=>$model->id], '审核', [
                                            'class'=>'btn btn-success btn-sm',
                                            'data-toggle' => 'modal',
                                            'data-target' => '#ajaxModal',
                                        ]);
                                    }
                                },
                                'status' => function($url, $model, $key){
                                    if(in_array($model->audit_status,[\common\enums\AuditStatusEnum::PASS ])) {
                                        return Html::status($model->status);
                                    }
                                },
                                'delete' => function($url, $model, $key){
                                    if($model->audit_status != \common\enums\AuditStatusEnum::PASS) {
                                        return Html::delete(['delete', 'id' => $model->id]);
                                    }
                                },
                            ],
                        ]
                    ]
                ]); ?>
            </div>
        </div>
    </div>
</div>
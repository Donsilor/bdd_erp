<?php

use common\helpers\Html;
use common\helpers\Url;
use yii\grid\GridView;
use kartik\daterange\DateRangePicker;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('stone', '石包列表');
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
                            'label' => '名称',
                            'attribute'=>'shibao',
                            'filter' => Html::activeTextInput($searchModel, 'shibao', [
                                'class' => 'form-control',
                            ]),
                            'headerOptions' => ['width'=>'100'],
                        ],
                        [
                            'label' => '库存数量',
                            'attribute'=>'kucun_cnt',
                            'filter' => Html::activeTextInput($searchModel, 'kucun_cnt', [
                                'class' => 'form-control',
                            ]),
                            'headerOptions' => ['width'=>'80'],
                        ],
                        [
                            'label' => '库存重量',
                            'attribute'=>'kucun_weight',
                            'filter' => Html::activeTextInput($searchModel, 'kucun_weight', [
                                'class' => 'form-control',
                            ]),
                            'headerOptions' => ['width'=>'80'],
                        ],
                        [
                            'label' => '买入',
                            'attribute'=>'MS_cnt',
                            'filter' => Html::activeTextInput($searchModel, 'MS_cnt', [
                                'class' => 'form-control',
                            ]),
                            'headerOptions' => ['width'=>'80'],
                        ],
                        [
                            'label' => '分包转入',
                            'attribute'=>'fenbaoru_cnt',
                            'filter' => Html::activeTextInput($searchModel, 'fenbaoru_cnt', [
                                'class' => 'form-control',
                            ]),
                            'headerOptions' => ['width'=>'80'],
                        ],
                        [
                            'label' => '送出',
                            'attribute'=>'SS_cnt',
                            'filter' => Html::activeTextInput($searchModel, 'SS_cnt', [
                                'class' => 'form-control',
                            ]),
                            'headerOptions' => ['width'=>'80'],
                        ],
                        [
                            'label' => '分包转出',
                            'attribute'=>'fenbaochu_cnt',
                            'filter' => Html::activeTextInput($searchModel, 'fenbaochu_cnt', [
                                'class' => 'form-control',
                            ]),
                            'headerOptions' => ['width'=>'80'],
                        ],
                        [
                            'label' => '还回',
                            'attribute'=>'HS_cnt',
                            'filter' => Html::activeTextInput($searchModel, 'HS_cnt', [
                                'class' => 'form-control',
                            ]),
                            'headerOptions' => ['width'=>'80'],
                        ],
                        [
                            'label' => '退石',
                            'attribute'=>'TS_cnt',
                            'filter' => Html::activeTextInput($searchModel, 'TS_cnt', [
                                'class' => 'form-control',
                            ]),
                            'headerOptions' => ['width'=>'80'],
                        ],
                        [
                            'label' => '退货',
                            'attribute'=>'TH_cnt',
                            'filter' => Html::activeTextInput($searchModel, 'TH_cnt', [
                                'class' => 'form-control',
                            ]),
                            'headerOptions' => ['width'=>'80'],
                        ],
                        [
                            'label' => '遗失',
                            'attribute'=>'YS_cnt',
                            'filter' => Html::activeTextInput($searchModel, 'YS_cnt', [
                                'class' => 'form-control',
                            ]),
                            'headerOptions' => ['width'=>'80'],
                        ],
                        [
                            'label' => '损坏',
                            'attribute'=>'SY_cnt',
                            'filter' => Html::activeTextInput($searchModel, 'SY_cnt', [
                                'class' => 'form-control',
                            ]),
                            'headerOptions' => ['width'=>'80'],
                        ],
                        [
                            'label' => '其他入库',
                            'attribute'=>'RK_cnt',
                            'filter' => Html::activeTextInput($searchModel, 'RK_cnt', [
                                'class' => 'form-control',
                            ]),
                            'headerOptions' => ['width'=>'80'],
                        ],
                        [
                            'label' => '其他出库',
                            'attribute'=>'CK_cnt',
                            'filter' => Html::activeTextInput($searchModel, 'CK_cnt', [
                                'class' => 'form-control',
                            ]),
                            'headerOptions' => ['width'=>'80'],
                        ],
                        [
                            'label' => '每卡采购价格',
                            'attribute'=>'purchase_price',
                            'filter' => Html::activeTextInput($searchModel, 'purchase_price', [
                                'class' => 'form-control',
                            ]),
                            'headerOptions' => ['width' => '120'],
                        ],
                        [
                            'label' => '每卡销售价格',
                            'attribute'=>'sale_price',
                            'filter' => Html::activeTextInput($searchModel, 'sale_price', [
                                'class' => 'form-control',
                            ]),
                            'headerOptions' => ['width' => '120'],
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
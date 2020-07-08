<?php

use common\helpers\Html;
use common\helpers\Url;
use yii\grid\GridView;
use common\enums\AuditStatusEnum;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('warehouse', '仓库管理');
$this->params['breadcrumbs'][] = $this->title;
?>



<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title"><?= Html::encode($this->title) ?></h3>
                <div class="box-tools" style="right: 100px;">
                    <?= Html::create(['ajax-edit'], '创建', [
                        'data-toggle' => 'modal',
                        'data-target' => '#ajaxModalLg',
                    ]); ?>
                </div>

            </div>
            <div class="box-body table-responsive">
                <?php echo Html::batchButtons(false)?>
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'tableOptions' => ['class' => 'table table-hover'],
                    'options' => ['style'=>' width:100%;white-space:nowrap;' ],
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
                            'attribute'=>'name',
                            'filter' => Html::activeTextInput($searchModel, 'name', [
                                'class' => 'form-control',
                            ]),
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'code',
                            'filter' => Html::activeTextInput($searchModel, 'code', [
                                'class' => 'form-control',
                            ]),
                            'headerOptions' => [],
                        ],
                        [
                            'attribute' => 'channel_id',
                            'format' => 'raw',
                            'headerOptions' => ['class' => 'col-md-1'],
                            'value' => function ($model){
                                return $model->channel->name ?? '';
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'channel_id',Yii::$app->styleService->styleChannel->getDropDown(), [
                                'prompt' => '全部',
                                'class' => 'form-control',

                            ]),
                        ],
                        [
                            'attribute' => 'type',
                            'format' => 'raw',
                            'headerOptions' => ['class' => 'col-md-1'],
                            'value' => function ($model){
                                return \addons\Warehouse\common\enums\WarehouseTypeEnum::getValue($model->type);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'type',\addons\Warehouse\common\enums\WarehouseTypeEnum::getMap(), [
                                'prompt' => '全部',
                                'class' => 'form-control',

                            ]),
                        ],

                        [
                            'attribute' => 'sort',
                            'format' => 'raw',
                            'filter' => false,
                            'headerOptions' => ['class' => 'col-md-1'],
                            'value' => function ($model, $key, $index, $column){
                                return  Html::sort($model->sort);
                            }
                            //'filter' => false,
                        ],
                        [
                            'attribute' => 'status',
                            'format' => 'raw',
                            'headerOptions' => ['class' => 'col-md-1'],
                            'value' => function ($model){
                                return \common\enums\StatusEnum::getValue($model->status,'getLockMap');
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'status',\common\enums\StatusEnum::getLockMap(), [
                                'prompt' => '全部',
                                'class' => 'form-control',

                            ]),
                        ],
                        [
                            'attribute' => 'audit_status',
                            'format' => 'raw',
                            'headerOptions' => ['class' => 'col-md-1'],
                            'value' => function ($model){
                                return \common\enums\AuditStatusEnum::getValue($model->audit_status);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'audit_status',\common\enums\AuditStatusEnum::getMap(), [
                                'prompt' => '全部',
                                'class' => 'form-control',

                            ]),
                        ],
                        [
                            'label' => '审核人',
                            'attribute' => 'auditor.username',
                            'headerOptions' => ['class' => 'col-md-1'],
                            'filter' => false,

                        ],
                        [
                            'attribute'=>'audit_time',
                            'filter' => false,
                            'headerOptions' => ['class' => 'col-md-1'],
                            'value'=>function($model){
                                return Yii::$app->formatter->asDate($model->audit_time);
                            }

                        ],

                        [
                            'label' => '操作人',
                            'attribute' => 'creator.username',
                            'headerOptions' => ['class' => 'col-md-1'],
                            'filter' =>false,

                        ],

                        [
                            'attribute'=>'created_at',
                            'filter' => false,
                            'headerOptions' => ['class' => 'col-md-1'],
                            'value'=>function($model){
                                return Yii::$app->formatter->asDate($model->created_at);
                            }


                        ],

                        [
                            'class' => 'yii\grid\ActionColumn',
                            'header' => '操作',
                            'template' => '{edit} {ajax-apply} {audit} {status} {delete}',
                            'buttons' => [
                                'edit' => function($url, $model, $key){
                                    if(in_array($model->audit_status,[AuditStatusEnum::SAVE ,AuditStatusEnum::UNPASS])){
                                        return Html::edit(['ajax-edit', 'id' => $model->id, 'returnUrl' => Url::getReturnUrl()], '编辑', [
                                            'data-toggle' => 'modal',
                                            'data-target' => '#ajaxModalLg',
                                        ]);
                                    }
                                },
                                'ajax-apply' => function($url, $model, $key){
                                    if($model->audit_status == AuditStatusEnum::SAVE || $model->audit_status == AuditStatusEnum::UNPASS){
                                        return Html::edit(['ajax-apply','id'=>$model->id], '提审', [
                                            'class'=>'btn btn-success btn-sm',
                                            'onclick' => 'rfTwiceAffirm(this,"提交审核", "确定提交吗？");return false;',
                                        ]);
                                    }
                                },
                                'audit' => function($url, $model, $key){
                                    if($model->audit_status == AuditStatusEnum::PENDING){
                                        return Html::edit(['ajax-audit','id'=>$model->id], '审核', [
                                            'class'=>'btn btn-success btn-sm',
                                            'data-toggle' => 'modal',
                                            'data-target' => '#ajaxModal',
                                        ]);
                                    }
                                },

                                'status' => function($url, $model, $key){
                                    if($model->audit_status == AuditStatusEnum::PASS) {
                                        return Html::status($model->status);
                                    }
                                },
                                'delete' => function($url, $model, $key){
                                    if($model->audit_status == AuditStatusEnum::SAVE) {
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
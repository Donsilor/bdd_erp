<?php

use common\helpers\Html;
use common\helpers\Url;
use yii\grid\GridView;
use kartik\daterange\DateRangePicker;
use common\enums\AuditStatusEnum;
use addons\Purchase\common\enums\ApplyStatusEnum;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = '采购申请单';
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
                    <?= Html::create(['ajax-edit'], '创建', [
                        'data-toggle' => 'modal',
                        'data-target' => '#ajaxModal',
                    ]); ?>
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
                    'attribute' => 'apply_sn',
                    'value'=>function($model) {
                        return Html::a($model->apply_sn, ['view', 'id' => $model->id,'returnUrl'=>Url::getReturnUrl()], ['style'=>"text-decoration:underline;color:#3c8dbc"]);
                    },
                    'filter' => Html::activeTextInput($searchModel, 'apply_sn', [
                            'class' => 'form-control',
                            'style'=> 'width:150px;'
                    ]),
                    'format' => 'raw',
                    //'headerOptions' => ['width'=>'150'],
            ],
            [
                    'attribute' => 'channel_id',
                    'value'=>function($model) {
                         return $model->channel->name ?? '';
                    },
                    'filter' => Html::activeDropDownList($searchModel, 'channel_id',Yii::$app->styleService->styleChannel->getDropDown(), [
                            'prompt' => '全部',
                            'class' => 'form-control',
                            'style'=> 'width:100px;'
                    ]),
                    'format' => 'raw',
                    //'headerOptions' => ['width'=>'150'],
            ],
            [
                    'attribute' => 'total_num',
                    'value' => "total_num",
                    'filter' => false,
                    'format' => 'raw',
                    'headerOptions' => ['width'=>'80'],
            ],
            [
                    'attribute' => 'total_cost',
                    'value' => 'total_cost',
                    'filter' => false,
                    'format' => 'raw',
                    'headerOptions' => ['width'=>'100'],
            ],            
            [
                    'attribute' => 'remark',
                    'value' => "remark",
                    'filter' => Html::activeTextInput($searchModel, 'apply_sn', [
                            'class' => 'form-control',
                            'style'=> 'width:200px;'
                    ]),
                    'format' => 'raw',
            ],
            [
                'attribute'=>'created_at',
                'filter' => DateRangePicker::widget([    // 日期组件
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
                'value'=>function($model){
                    return Yii::$app->formatter->asDatetime($model->created_at);
                }

            ],
            [
                    'attribute' => 'creator_id',
                    'value' => "creator.username",
                    'filter' => Html::activeTextInput($searchModel, 'creator.username', [
                        'class' => 'form-control',
                        'style'=> 'width:80px;'
                    ]),
                    'format' => 'raw',
                    'headerOptions' => ['width'=>'80'],
            ],
            [
                'attribute'=>'audit_time',
                'filter' => DateRangePicker::widget([    // 日期组件
                    'model' => $searchModel,
                    'attribute' => 'audit_time',
                    'value' => $searchModel->audit_time,
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
                    return Yii::$app->formatter->asDatetime($model->created_at);
                }

            ],

            [
                    'attribute' => 'audit_status',
                    'value' => function ($model){
                        return AuditStatusEnum::getValue($model->audit_status);
                    },
                    'filter' => Html::activeDropDownList($searchModel, 'audit_status',AuditStatusEnum::getMap(), [
                            'prompt' => '全部',
                            'class' => 'form-control',
                            'style'=> 'width:80px;'
                    ]),
                    'format' => 'raw',
                    'headerOptions' => ['width'=>'100'],
            ],
            [
                'attribute' => 'auditor_id',
                'value' => "auditor.username",
                'filter' => Html::activeTextInput($searchModel, 'auditor.username', [
                    'class' => 'form-control',
                    'style'=> 'width:80px;'
                ]),
                'format' => 'raw',
                'headerOptions' => ['width'=>'80'],
            ],
            [
                'attribute' => 'apply_status',                    
                'value' => function ($model){
                    return ApplyStatusEnum::getValue($model->apply_status);
                },
                'filter' => Html::activeDropDownList($searchModel, 'apply_status',ApplyStatusEnum::getMap(), [
                    'prompt' => '全部',
                    'class' => 'form-control',
                    'style'=> 'width:80px;'
                ]),
                'format' => 'raw',
                'headerOptions' => ['width'=>'100'],
            ],            
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => '操作',
                'template' => '{goods} {edit} {audit} {apply} {delete}',
                'buttons' => [
                    'edit' => function($url, $model, $key){
                        if($model->apply_status == ApplyStatusEnum::SAVE){
                            return Html::edit(['ajax-edit','id' => $model->id,'returnUrl' => Url::getReturnUrl()],'编辑',[
                                    'data-toggle' => 'modal',
                                    'data-target' => '#ajaxModal',
                                    'class'=>'btn btn-primary btn-sm',
                            ]);
                        }
                    },                    
                    'audit' => function($url, $model, $key){
                        if($model->apply_status == ApplyStatusEnum::PENDING){
                            return Html::edit(['ajax-audit','id'=>$model->id], '审核', [
                                    'class'=>'btn btn-success btn-sm',
                                    'data-toggle' => 'modal',
                                    'data-target' => '#ajaxModal',
                             ]); 
                        }
                    },
                    'goods' => function($url, $model, $key){
                        return Html::a('商品', ['purchase-apply-goods/index', 'apply_id' => $model->id,'returnUrl'=>Url::getReturnUrl()], ['class' => 'btn btn-warning btn-sm']);
                    },

                    'apply' => function($url, $model, $key){
                        if($model->apply_status == ApplyStatusEnum::SAVE){
                            return Html::edit(['ajax-apply','id'=>$model->id], '提审', [
                                'class'=>'btn btn-success btn-sm',
                                'onclick' => 'rfTwiceAffirm(this,"提交审核", "确定提交吗？");return false;',
                            ]);
                        }
                    },
                    'follower' => function($url, $model, $key){
                        if($model->apply_status <= ApplyStatusEnum::PENDING){
                            return Html::edit(['ajax-follower','id'=>$model->id], '跟单人', [
                                'class'=>'btn btn-info btn-sm',
                                'data-toggle' => 'modal',
                                'data-target' => '#ajaxModal',
                            ]);
                        }
                    },
                    'delete' => function($url, $model, $key){
                        if($model->apply_status == ApplyStatusEnum::SAVE){
                            return Html::delete(['delete', 'id' => $model->id]);
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

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
                        'data-target' => '#ajaxModalLg',
                    ]); ?>
                    <?php
                        echo Html::a('批量生成采购单', ['ajax-purchase'],  [
                            'class'=>'btn btn-success btn-xs',
                            "onclick" => "batchPop2(this);return false;",
                            'data-grid'=>'grid',                            
                            'data-title'=>'批量生成采购单',
                        ]);                          
                    ?> 
                    <?= Html::button('导出', [
                        'class'=>'btn btn-primary btn-xs',
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
                    'filter' =>true,
                    'format' => 'raw',
                    //'headerOptions' => ['width'=>'150'],
            ],
            [
                    'attribute' => 'channel_id',
                    'value'=>function($model) {
                         return $model->channel->name ?? '';
                    },
                    'filter' => Html::activeDropDownList($searchModel, 'channel_id',Yii::$app->salesService->saleChannel->getDropDown(), [
                            'prompt' => '全部',
                            'class' => 'form-control',
                            'style'=> 'width:100px;'
                    ]),
                    'format' => 'raw',
                    //'headerOptions' => ['width'=>'150'],
            ],
            [
                'attribute' => 'purchase_cate',
                'value'=>function($model) {
                    return \addons\Purchase\common\enums\PurchaseCateEnum::getValue($model->purchase_cate);
                },
                'filter' => Html::activeDropDownList($searchModel, 'purchase_cate',\addons\Purchase\common\enums\PurchaseCateEnum::getMap(), [
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
                    return Yii::$app->formatter->asDatetime($model->audit_time);
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
                    $audit_name_str = '';
                    if($model->apply_status == ApplyStatusEnum::PENDING){
                        $audit_name = Yii::$app->services->flowType->getCurrentUsersName(Yii::$app->purchaseService->apply->getTargetYType($model->channel_id),$model->id);
                        $audit_name_str = $audit_name ? "({$audit_name})" : "";
                    }elseif($model->apply_status == ApplyStatusEnum::CONFIRM){
                        $audit_name = Yii::$app->services->flowType->getCurrentUsersName(\common\enums\TargetTypeEnum::PURCHASE_APPLY_S_MENT,$model->id);
                        $audit_name_str = $audit_name ? "({$audit_name})" : "";
                    }
                    return ApplyStatusEnum::getValue($model->apply_status) .$audit_name_str;
                },
                'filter' => Html::activeDropDownList($searchModel, 'apply_status',ApplyStatusEnum::getMap(), [
                    'prompt' => '全部',
                    'class' => 'form-control',
                    'style'=> 'width:120px;'
                ]),
                'format' => 'raw',
                'headerOptions' => ['width'=>'120'],
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
                                    'data-target' => '#ajaxModalLg',
                                    'class'=>'btn btn-primary btn-sm',
                            ]);
                        }
                    },                    
                    'audit' => function($url, $model, $key){
                        $isAudit = Yii::$app->services->flowType->isAudit(Yii::$app->purchaseService->apply->getTargetYType($model->channel_id),$model->id);
                        $isAudit1 = Yii::$app->services->flowType->isAudit(\common\enums\TargetTypeEnum::PURCHASE_APPLY_S_MENT,$model->id);
                        if($model->apply_status == ApplyStatusEnum::PENDING && $isAudit){
                            return Html::edit(['ajax-audit','id'=>$model->id], '审核', [
                                    'class'=>'btn btn-success btn-sm',
                                    'data-toggle' => 'modal',
                                    'data-target' => '#ajaxModal',
                             ]); 
                        }elseif($model->apply_status == ApplyStatusEnum::CONFIRM && $isAudit1){
                            return Html::edit(['final-audit','id'=>$model->id], '审核', [
                                'class'=>'btn btn-success btn-sm',
                                'data-toggle' => 'modal',
                                'data-target' => '#ajaxModal',
                            ]);
                        }elseif($model->apply_status == ApplyStatusEnum::AUDITED && $model->creator_id == \Yii::$app->user->identity->id){
                            return Html::edit(['affirm','id'=>$model->id], '确认', [
                                'class'=>'btn btn-success btn-sm',
                                'onclick' => 'rfTwiceAffirm(this,"提交确认", "确定确认吗？");return false;',
                            ]);
                        }
                    },
                    'goods' => function($url, $model, $key){
                        return Html::a('商品列表', ['purchase-apply-goods/index', 'apply_id' => $model->id,'returnUrl'=>Url::getReturnUrl()], ['class' => 'btn btn-warning btn-sm']);
                    },
                    'apply' => function($url, $model, $key){
                        if($model->apply_status == ApplyStatusEnum::SAVE){
                            return Html::edit(['ajax-apply','id'=>$model->id], '提审', [
                                'class'=>'btn btn-success btn-sm',
                                'onclick' => 'rfTwiceAffirm(this,"提交审核", "确定提交吗？");return false;',
                            ]);
                        }
                    },                    
                    'delete' => function($url, $model, $key){
                        if($model->apply_status == ApplyStatusEnum::SAVE){
                            return Html::delete(['delete', 'id' => $model->id],'取消',[
                                    'onclick' => 'rfTwiceAffirm(this,"取消单据", "确定取消吗？");return false;',
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

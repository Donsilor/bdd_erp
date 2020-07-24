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
$this->title = '银行支付单';
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
<!--                    --><?//= Html::button('导出', [
//                        'class'=>'btn btn-success btn-xs',
//                        'onclick' => 'batchExport()',
//                    ]);?>
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
                    'attribute' => 'finance_no',
                    'value'=>function($model) {
                        return Html::a($model->purchase_sn, ['view', 'id' => $model->id,'returnUrl'=>Url::getReturnUrl()], ['style'=>"text-decoration:underline;color:#3c8dbc"]);
                    },
                    'filter' => Html::activeTextInput($searchModel, 'finance_no', [
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
                    'attribute' => 'pay_amount',
                    'format' => 'raw',
                    'headerOptions' => ['width'=>'100'],
            ],
            [
                    'attribute' => 'currency',
                    'filter' => false,
                    'format' => 'raw',
                    'headerOptions' => ['width'=>'80'],
            ],
            [
                'attribute' => 'finance_status',
                'value' => function ($model){
                    $audit_name = Yii::$app->services->flowType->getCurrentUsersName(TargetTypeEnum::PURCHASE_MENT,$model->id);
                    $audit_name_str = $audit_name ? "({$audit_name})" : "";
                    return PurchaseStatusEnum::getValue($model->finance_status).$audit_name_str;
                },
                'filter' => Html::activeDropDownList($searchModel, 'finance_status',FinanceStatusEnum::getMap(), [
                    'prompt' => '全部',
                    'class' => 'form-control',
                    'style'=> 'width:80px;'
                ]),
                'format' => 'raw',
                'headerOptions' => ['width'=>'100'],
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
                'attribute' => 'creator_id',
                'value' => function($model){
                    return $model->creator->username ?? '';
                },
                'filter' => Html::activeTextInput($searchModel, 'creator.username', [
                    'class' => 'form-control',
                    'style'=> 'width:80px;'
                ]),
                'format' => 'raw',
                'headerOptions' => ['width'=>'80'],
            ],
            [
                    'attribute'=>'created_at',
                    'filter' => \kartik\daterange\DateRangePicker::widget([    // 日期组件
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
                'class' => 'yii\grid\ActionColumn',
                'header' => '操作',
                'template' => '{goods} {edit} {ajax-audit} {apply} {follower} {close}',
                'buttons' => [
                    'edit' => function($url, $model, $key){
                        if($model->purchase_status == PurchaseStatusEnum::SAVE){
                            return Html::edit(['ajax-edit','id' => $model->id,'returnUrl' => Url::getReturnUrl()],'编辑',[
                                    'data-toggle' => 'modal',
                                    'data-target' => '#ajaxModalLg',
                                    'class'=>'btn btn-primary btn-sm',
                            ]);
                        }
                    },                    
                    'ajax-audit' => function($url, $model, $key){
                        $isAudit = Yii::$app->services->flowType->isAudit(TargetTypeEnum::PURCHASE_MENT,$model->id);
                        if($model->purchase_status == PurchaseStatusEnum::PENDING && $isAudit){
                            return Html::edit(['ajax-audit','id'=>$model->id], '审核', [
                                    'class'=>'btn btn-success btn-sm',
                                    'data-toggle' => 'modal',
                                    'data-target' => '#ajaxModalLg',
                             ]); 
                        }
                    },
                    'goods' => function($url, $model, $key){
                        return Html::a('商品列表', ['purchase-goods/index', 'purchase_id' => $model->id,'returnUrl'=>Url::getReturnUrl()], ['class' => 'btn btn-warning btn-sm']);
                    },

                    'apply' => function($url, $model, $key){
                        if($model->purchase_status == PurchaseStatusEnum::SAVE){
                            return Html::edit(['ajax-apply','id'=>$model->id], '提审', [
                                'class'=>'btn btn-success btn-sm',
                                'onclick' => 'rfTwiceAffirm(this,"提交审核", "确定提交吗？");return false;',
                            ]);
                        }
                    },
                    'close' => function($url, $model, $key){
                        if($model->purchase_status == PurchaseStatusEnum::SAVE){
                            return Html::delete(['close', 'id' => $model->id],'关闭',[
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

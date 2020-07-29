<?php

use addons\Warehouse\common\enums\BillStatusEnum;
use common\helpers\Html;
use common\helpers\Url;
use kartik\daterange\DateRangePicker;
use yii\grid\GridView;
use common\enums\AuditStatusEnum;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = Yii::t('supplier', '供应商管理');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title"><?= Html::encode($this->title) ?></h3>
                <div class="box-tools">
                    <?= Html::create(['edit']) ?>
                    <a href="<?= Url::to(['index?action=export'])?>" class="blue">导出Excel</a>
                </div>
            </div>
            <div class="box-body table-responsive">
            <?php echo Html::batchButtons(false)?>
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'tableOptions' => ['class' => 'table table-hover'],
                    'options' => ['style'=>'white-space:nowrap;' ],
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
                            'headerOptions' => ['width'=>'60'],
                        ],
                        [
                            'attribute' => 'supplier_name',
                            'value'=>function($model) {
                                return Html::a($model->supplier_name, ['view', 'id' => $model->id,'returnUrl'=>Url::getReturnUrl()], ['style'=>"text-decoration:underline;color:#3c8dbc"]);
                            },
                            'filter' => Html::activeTextInput($searchModel, 'supplier_name', [
                                'class' => 'form-control',
                            ]),
                            'format' => 'raw',
                            'headerOptions' => ['width'=>'300'],
                        ],
                        [
                            'attribute' => 'goods_type',
                            'headerOptions' => ['class' => 'col-md-1','width'=>'60'],
                            'value' => function ($model){
                                return \addons\Supply\common\enums\GoodsTypeEnum::getValue($model->goods_type);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'goods_type',\addons\Supply\common\enums\GoodsTypeEnum::getMap(), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                            ]),
                        ],
                        [
                            'attribute'=>'business_scope',
                            'value' => function($model){
                                if($model->business_scope){
                                    $scope_key = explode(',', $model->business_scope);
                                    $scope_key = array_filter($scope_key);
                                    $scope_val = \addons\Supply\common\enums\BusinessScopeEnum::getValues($scope_key);
                                    return implode(",",$scope_val);
                                }else{
                                    return '';
                                }
                            },
                            'filter' => false,
                            'headerOptions' => ['width'=>'300'],
                        ],
                        [
                            'attribute' => 'contactor',
                            'value' => 'contactor',
                            'filter' => Html::activeTextInput($searchModel, 'contactor', [
                                'class' => 'form-control',
                            ]),
                            'format' => 'raw',
                            'headerOptions' => ['width'=>'100'],
                        ],
                        [
                            'attribute' => 'mobile',
                            'value' => 'mobile',
                            'filter' => Html::activeTextInput($searchModel, 'mobile', [
                                'class' => 'form-control',
                            ]),
                            'format' => 'raw',
                            'headerOptions' => ['width'=>'150'],
                        ],
                        [
                            'attribute' => 'telephone',
                            'value' => 'telephone',
                            'filter' => Html::activeTextInput($searchModel, 'telephone', [
                                'class' => 'form-control',
                            ]),
                            'format' => 'raw',
                            'headerOptions' => ['width'=>'100'],
                        ],
                        [
                            'attribute' => 'address',
                            'value' => 'address',
                            'filter' => Html::activeTextInput($searchModel, 'address', [
                                'class' => 'form-control',
                            ]),
                            'format' => 'raw',
                            'headerOptions' => ['width'=>'300'],
                        ],
                        [
                            'label' => '创建人',
                            'attribute' => 'creator.username',
                            'filter' => Html::activeTextInput($searchModel, 'creator.username', [
                                'class' => 'form-control',
                            ]),
                            'format' => 'raw',
                            'headerOptions' => ['width'=>'100'],
                        ],
                        [
                            'attribute' => 'created_at',
                            'filter' => DateRangePicker::widget([    // 日期组件
                                'model' => $searchModel,
                                'attribute' => 'created_at',
                                'value' => '',
                                'options' => ['readonly' => true, 'class' => 'form-control','style'=>'background-color:#fff;width:200px;'],
                                'pluginOptions' => [
                                    'format' => 'yyyy-mm-dd',
                                    'locale' => [
                                        'separator' => '/',
                                    ],
                                    'endDate' => date('Y-m-d', time()),
                                    'todayHighlight' => true,
                                    'autoclose' => true,
                                    'todayBtn' => 'linked',
                                    'clearBtn' => true,
                                ],
                            ]),
                            'value' => function ($model) {
                                return Yii::$app->formatter->asDatetime($model->created_at);
                            },
                            'format' => 'raw',
                            'headerOptions' => ['width'=>'200'],
                        ],
                        [
                            'attribute' => 'audit_time',
                            'filter' => DateRangePicker::widget([    // 日期组件
                                'model' => $searchModel,
                                'attribute' => 'audit_time',
                                'value' => '',
                                'options' => ['readonly' => true, 'class' => 'form-control','style'=>'background-color:#fff;width:200px;'],
                                'pluginOptions' => [
                                    'format' => 'yyyy-mm-dd',
                                    'locale' => [
                                        'separator' => '/',
                                    ],
                                    'endDate' => date('Y-m-d', time()),
                                    'todayHighlight' => true,
                                    'autoclose' => true,
                                    'todayBtn' => 'linked',
                                    'clearBtn' => true,
                                ],
                            ]),
                            'value' => function ($model) {
                                return Yii::$app->formatter->asDatetime($model->audit_time);
                            },
                            'format' => 'raw',
                            'headerOptions' => ['width'=>'160'],
                        ],
                        [
                            'attribute' => 'audit_status',
                            'format' => 'raw',
                            'headerOptions' => ['class' => 'col-md-1', 'width'=>'60'],
                            'value' => function ($model){
                                $model->getTargetType();
                                $audit_name_str = '';
                                if($model->targetType){
                                    $audit_name = Yii::$app->services->flowType->getCurrentUsersName($model->targetType,$model->id);
                                    $audit_name_str = $audit_name ? "({$audit_name})" : "";
                                }
                                return \common\enums\AuditStatusEnum::getValue($model->audit_status).$audit_name_str;
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'audit_status',\common\enums\AuditStatusEnum::getMap(), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                            ]),
                        ],

                        [
                            'attribute' => 'supplier_status',
                            'headerOptions' => ['class' => 'col-md-1'],
                            'value' => function ($model){
                                return \addons\Supply\common\enums\SupplierStatusEnum::getValue($model->supplier_status);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'supplier_status',\addons\Supply\common\enums\SupplierStatusEnum::getMap(), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                            ]),
                        ],
                        [
                            'attribute' => 'status',
                            'format' => 'raw',
                            'headerOptions' => ['class' => 'col-md-1', 'width'=>'60'],
                            'value' => function ($model){
                                return \common\enums\StatusEnum::getValue($model->status);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'status',\common\enums\StatusEnum::getMap(), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                            ]),
                        ],
                        [
                            'class' => 'yii\grid\ActionColumn',
                            'header' => '操作',
                            'contentOptions' => ['style' => ['white-space' => 'nowrap']],
                            'template' => '{edit} {apply} {audit} {status} {delete}',
                            'buttons' => [
                            'edit' => function($url, $model, $key){
                                    return Html::edit(['edit', 'id' => $model->id, 'returnUrl' => Url::getReturnUrl()]);
                             },
                            'apply' => function($url, $model, $key){
                                if($model->audit_status == AuditStatusEnum::SAVE){
                                    return Html::edit(['ajax-apply','id'=>$model->id], '提审', [
                                        'class'=>'btn btn-success btn-sm',
                                        'onclick' => 'rfTwiceAffirm(this,"提交审核", "确定提交吗？");return false;',
                                    ]);
                                }
                            },
                            'audit' => function($url, $model, $key){
                                $model->getTargetType();
                                if($model->targetType){
                                    $isAudit = Yii::$app->services->flowType->isAudit($model->targetType,$model->id);
                                }else{
                                    $isAudit = true;
                                }
                                   if($model->audit_status == AuditStatusEnum::PENDING && $isAudit){
                                        return Html::edit(['ajax-audit','id'=>$model->id], '审核', [
                                            'class'=>'btn btn-success btn-sm',
                                            'data-toggle' => 'modal',
                                            'data-target' => '#ajaxModal',
                                        ]);
                                    }
                             },
                             'status' => function($url, $model, $key){
                                if($model->audit_status == AuditStatusEnum::PASS) {
                                     return Html::status($model['status']);
                                 }
                              },
                                'delete' => function ($url, $model, $key) {
                                    if($model->audit_status == AuditStatusEnum::SAVE) {
                                        return Html::delete(['delete', 'id' => $model->id]);
                                    }
                                },
                            ]
                        ]
                    ]
                ]); ?>
            </div>
        </div>
    </div>
</div>

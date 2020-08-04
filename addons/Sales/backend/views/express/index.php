<?php

use common\helpers\Html;
use common\helpers\ImageHelper;
use common\helpers\Url;
use yii\grid\GridView;
use kartik\daterange\DateRangePicker;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('sale_channel', '快递公司');
$this->params['breadcrumbs'][] = $this->title;

$params = Yii::$app->request->queryParams;
$params = $params ? "&".http_build_query($params) : '';

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
                <div class="box-tools" >
                    <a href="<?= Url::to(['index?action=export'.$params])?>" class="blue">导出Excel</a>
                </div>
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
                            'attribute' => 'cover',
                            'value' => function ($model) {
                                return Html::img(ImageHelper::defaultHeaderPortrait(Html::encode($model->cover)),
                                    [
                                        'class' => 'img-rounded rf-img-md img-bordered-sm',
                                    ]);
                            },
                            'filter' => false,
                            'format' => 'raw',
                        ],
                        [
                            'attribute'=>'name',
                            'format' => 'raw',
                            'value'=>function($model) {
                                return Html::a($model->name, ['view', 'id' => $model->id,'returnUrl'=>Url::getReturnUrl()], ['style'=>"text-decoration:underline;color:#3c8dbc"]);
                            },
                            'filter' => Html::activeTextInput($searchModel, 'name', [
                                'class' => 'form-control',
                            ]),
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'express_man',
                            'filter' => Html::activeTextInput($searchModel, 'express_man', [
                                'class' => 'form-control',
                            ]),
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'express_phone',
                            'filter' => Html::activeTextInput($searchModel, 'express_phone', [
                                'class' => 'form-control',
                            ]),
                            'headerOptions' => [],
                        ],
                        [
                            'attribute' => 'settlement_way',
                            'value' => function($model){
                                if($model->settlement_way){
                                    $key = explode(',', $model->settlement_way);
                                    $key = array_filter($key);
                                    $value = \addons\Sales\common\enums\SettlementWayEnum::getValues($key);
                                    return implode(",",$value);
                                }else{
                                    return '';
                                }
                            },
                            'filter' => false,
                            'format' => 'raw',
                            'headerOptions' => ['width'=>'100'],
                        ],
                        [
                            'attribute' => 'delivery_scope',
                            'value' => function($model){
                                if($model->delivery_scope){
                                    $key = explode(',', $model->delivery_scope);
                                    $key = array_filter($key);
                                    $value = \addons\Sales\common\enums\DeliveryScopeEnum::getValues($key);
                                    return implode(",",$value);
                                }else{
                                    return '';
                                }
                            },
                            'filter' => false,
                            'format' => 'raw',
                            'headerOptions' => ['width'=>'100'],
                        ],
                        [
                            'label' => '添加人',
                            'attribute' => 'member.username',
                            'headerOptions' => ['class' => 'col-md-1'],
                            'filter' => Html::activeTextInput($searchModel, 'member.username', [
                                'class' => 'form-control',
                            ]),

                        ],
                        [
                            'attribute'=>'created_at',
                            'filter' => DateRangePicker::widget([    // 日期组件
                                'model' => $searchModel,
                                'attribute' => 'created_at',
                                'value' => $searchModel->created_at,
                                'options' => ['readonly' => false,'class'=>'form-control','style'=>'background-color:#fff;width:200px;'],
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
                            'attribute' => 'status',
                            'format' => 'raw',
                            'headerOptions' => ['class' => 'col-md-1'],
                            'value' => function ($model){
                                return \common\enums\StatusEnum::getValue($model->status);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'status',\common\enums\StatusEnum::getMap(), [
                                'prompt' => '全部',
                                'class' => 'form-control',

                            ]),
                        ],
                        [
                            'attribute' => 'sort',
                            'format' => 'raw',
                            'value' => function ($model, $key, $index, $column){
                                return  Html::sort($model->sort,['data-url'=>Url::to(['ajax-update'])]);
                            },
                            'headerOptions' => ['width' => '80'],
                        ],
                        [
                            'class' => 'yii\grid\ActionColumn',
                            'header' => '操作',
                            'template' => '{edit} {apply} {audit} {view} {status}',
                            'buttons' => [
                                'edit' => function($url, $model, $key){
                                    return Html::edit(['ajax-edit','id' => $model->id,'returnUrl' => Url::getReturnUrl()], '编辑', [
                                        'data-toggle' => 'modal',
                                        'data-target' => '#ajaxModalLg',
                                    ]);
                                },
                                'apply' => function($url, $model, $key){
                                    if($model->audit_status == \common\enums\AuditStatusEnum::SAVE){
                                        return Html::edit(['ajax-apply','id'=>$model->id], '提审', [
                                            'class'=>'btn btn-info btn-sm',
                                            'onclick' => 'rfTwiceAffirm(this,"提交审核", "确定提交吗？");return false;',
                                        ]);
                                    }
                                },
                                'audit' => function($url, $model, $key){
                                    if($model->audit_status == \common\enums\AuditStatusEnum::PENDING) {
                                        return Html::edit(['ajax-audit','id'=>$model->id], '审核', [
                                            'class'=>'btn btn-success btn-sm',
                                            'data-toggle' => 'modal',
                                            'data-target' => '#ajaxModal',
                                        ]);
                                    }
                                },
                                'view' => function($url, $model, $key){
                                    return Html::a('查看', ['view', 'id' => $model->id,'returnUrl'=>Url::getReturnUrl()], ['class' => 'btn btn-warning btn-sm']);
                                },
                                'status' => function($url, $model, $key){
                                    if($model->audit_status == \common\enums\AuditStatusEnum::PASS) {
                                         return Html::status($model->status);
                                    }
                                },
                                'delete' => function($url, $model, $key){
                                    return Html::delete(['delete', 'id' => $model->id]);
                                },
                            ],

                        ]
                    ]
                ]); ?>
            </div>
        </div>
    </div>
</div>
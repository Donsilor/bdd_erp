<?php

use common\helpers\Html;
use common\helpers\Url;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $searchModel */
$this->title = '质检问题列表';
$this->params['breadcrumbs'][] = $this->title;
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
//                        [
//                            'attribute' => 'id',
//                            'filter' => true,
//                            'format' => 'raw',
//                            'headerOptions' => ['width'=>'80'],
//                        ],
                        [
                            'attribute' => 'is_pass',
                            'format' => 'raw',
                            'value' =>function($model){
                                return \addons\Sales\common\enums\IsPassEnum::getValue($model->is_pass);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'is_pass',\addons\Sales\common\enums\IsPassEnum::getMap(), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                            ]),
                            'headerOptions' => ['class' => 'col-md-1'],
                        ],
                        [
                            'attribute' => 'created_at',
                            'filter' => \kartik\daterange\DateRangePicker::widget([    // 日期组件
                                'model' => $searchModel,
                                'attribute' => 'created_at',
                                'value' => '',
                                'options' => ['readonly' => false, 'class' => 'form-control'],
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
                            'headerOptions' => ['class' => 'col-md-2'],
                        ],
                        [
                            'attribute' => 'creator_id',
                            'value' => "creator.username",
                            'filter' => Html::activeTextInput($searchModel, 'creator.username', [
                                'class' => 'form-control',
                            ]),
                            'headerOptions' => ['class' => 'col-md-1'],
                        ],
                        [
                            'attribute' => 'order_sn',
                            'value'=>function($model) {
                                return Html::a($model->order_sn, ['order/view', 'id' => $model->order_id,'returnUrl'=>Url::getReturnUrl()], ['style'=>"text-decoration:underline;color:#3c8dbc"]);
                            },
                            'filter' => Html::activeTextInput($searchModel, 'order_sn', [
                                'class' => 'form-control',
                                'style'=> 'width:150px;'
                            ]),
                            'format' => 'raw',
                            'headerOptions' => ['class' => 'col-md-1'],
                        ],
                        [
                            'attribute' => 'problem_type',
                            'format' => 'raw',
                            'value' =>function($model){
                                return \addons\Sales\common\enums\ProblemTypeEnum::getValue($model->problem_type);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'problem_type',\addons\Sales\common\enums\ProblemTypeEnum::getMap(), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                            ]),
                            'headerOptions' => ['class' => 'col-md-1'],
                        ],
                        [
                            'label' => '质检问题',
                            'attribute' => 'fqc.name',
                            'format' => 'raw',
                            'value' => 'fqc.name',
                            'filter' => Html::activeDropDownList($searchModel, 'problem',\Yii::$app->salesService->fqc->getDropDown(), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                            ]),
                            'headerOptions' => ['class' => 'col-md-1'],
                        ],
                        [
                            'attribute'=>'remark',
                            'filter' => true,
                            'headerOptions' => [],
                        ],
                    ]
                ]); ?>
            </div>
        </div>
    </div>
</div>

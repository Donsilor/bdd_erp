<?php

use common\helpers\Html;
use kartik\daterange\DateRangePicker;
use yii\grid\GridView;

$this->title = '质检问题列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box-body nav-tabs-custom">
    <div class="tab-content">
        <div class="row col-xs-14">
            <div class="box">
                <div class="box-body table-responsive">
                  <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'tableOptions' => ['class' => 'table table-hover'],
                        'showFooter' => false,//显示footer行
                        'id'=>'grid',
                        'columns' => [
                            [
                                'class' => 'yii\grid\SerialColumn',
                                'visible' => false,
                            ],
                            [
                                'attribute' => 'id',
                                'filter' => true,
                                'format' => 'raw',
                                'headerOptions' => ['width'=>'80'],
                            ],
                            [
                                'attribute'=>'order_sn',
                                'value' => function($model) use($order){
                                    return $order->order_sn??"";
                                },
                                'filter' => false,
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
                            [
                                'attribute' => 'creator_id',
                                'value' => "creator.username",
                                'filter' => Html::activeTextInput($searchModel, 'creator.username', [
                                    'class' => 'form-control',
                                ]),
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'attribute' => 'created_at',
                                'filter' => DateRangePicker::widget([    // 日期组件
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
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                        ]
                    ]); ?>
                </div>
            </div>
        <!-- box end -->
        </div>
    </div>
</div>
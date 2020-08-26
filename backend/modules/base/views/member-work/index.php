<?php

use common\helpers\Html;
use common\helpers\Url;
use yii\grid\GridView;
use kartik\daterange\DateRangePicker;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('style_channel', '工作总结');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box-body nav-tabs-custom">
    <h2 class="page-header">款式详情 - <?php echo $style->style_sn?></h2>
    <?php echo Html::menuTab($tabList,$tab)?>
    <div class="tab-content">
        <div class="row col-xs-12">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title"><?= Html::encode($this->title) ?></h3>
                    <div class="box-tools">
                        <?= Html::create(['ajax-edit', 'style_id' => $style_id,'returnUrl' => Url::getReturnUrl()], '创建', [
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
                                'attribute' => 'type',
                                'filter' => false,
                                'value'=>function($model){
                                    return \common\enums\WorksTypeEnum::getValue($model->type);
                                },
                                'headerOptions' => ['width'=>'80'],
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
                                'label' => '添加人',
                                'attribute' => 'member.username',
                                'headerOptions' => ['class' => 'col-md-1'],
                                'filter' => Html::activeTextInput($searchModel, 'member.username', [
                                    'class' => 'form-control',
                                ]),

                            ],
                            [
                                'attribute' => 'title',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1'],
                                'filter' =>false,
                            ],
                            [
                                'attribute' => 'content',
                                'headerOptions' => ['class' => 'col-md-3'],
                                'filter' =>false,
                            ],


                            [
                                'attribute'=>'date',
                                'filter' => DateRangePicker::widget([    // 日期组件
                                    'model' => $searchModel,
                                    'attribute' => 'shipping_time',
                                    'value' => $searchModel->shipping_time,
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
                                    return Yii::$app->formatter->asDatetime($model->updated_at);
                                }

                            ],






                            [
                                'class' => 'yii\grid\ActionColumn',
                                'header' => '操作',
                                'template' => '{edit} {info} {status}',
                                'buttons' => [
                                    'edit' => function($url, $model, $key){
                                        return Html::edit(['ajax-edit','id' => $model->id,'returnUrl' => Url::getReturnUrl(), 'style_id' => $model->style_id ,'returnUrl' => Url::getReturnUrl()], '编辑', [
                                            'data-toggle' => 'modal',
                                            'data-target' => '#ajaxModalLg',
                                        ]);
                                    },

                                    'status' => function($url, $model, $key){
                                        return Html::status($model->status);
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
        <!-- box end -->
        </div>
    </div>
</div>
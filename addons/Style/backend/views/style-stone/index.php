<?php

use common\helpers\Html;
use common\helpers\Url;
use yii\grid\GridView;
use kartik\daterange\DateRangePicker;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('style_channel', '石头信息');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box-body nav-tabs-custom">
    <h2 class="page-header">款式发布 - <?php echo $style->style_sn?></h2>
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
                                'attribute' => 'id',
                                'filter' => true,
                                'format' => 'raw',
                                'headerOptions' => ['width'=>'80'],
                            ],

                            [
                                'label' => '款式编号',
                                'value' => function($model) use($style){
                                    return $style->style_sn;
                                },
                                'filter' => false,
                                'headerOptions' => [],
                            ],
                            [
                                'attribute' => 'position',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1','style'=>'width:100px;'],
                                'value' => function ($model){
                                    return \addons\Style\common\enums\StoneEnum::getValue($model->position,'getPositionMap');
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'position',\addons\Style\common\enums\StoneEnum::getPositionMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',

                                ]),
                            ],
                            [
                                'attribute' => 'stone_type',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1'],
                                'value' => function ($model){
                                    return \addons\Style\common\enums\StoneEnum::getValue($model->stone_type,'getTypeMap');
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'stone_type',\addons\Style\common\enums\StoneEnum::getTypeMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',

                                ]),
                            ],

                            [
                                'label' => '创建时间',
                                'filter' => false,
                                'value' => function($model){
                                    return Yii::$app->formatter->asDatetime($model->created_at);
                                }

                            ],
                            [
                                'label' => '更新时间',
                                'filter' => false,
                                'value' => function($model){
                                    return Yii::$app->formatter->asDatetime($model->updated_at);
                                }

                            ],
                            [
                                'label' => '操作人',
                                'attribute' => 'member.username',
                                'headerOptions' => ['class' => 'col-md-1'],
                                'filter' => Html::activeTextInput($searchModel, 'member.username', [
                                    'class' => 'form-control',
                                ]),

                            ],


                            [
                                'attribute' => 'status',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1','style'=>'width:105px;'],
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
<?php

use common\helpers\Html;
use common\helpers\Url;
use yii\grid\GridView;
use kartik\daterange\DateRangePicker;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('style_channel', '工作总结');
$this->params['breadcrumbs'][] = $this->title;
$params = Yii::$app->request->queryParams;
$params = $params ? "&".http_build_query($params) : '';
?>
<div class="box-body nav-tabs-custom">
    <div class="tab-content">
        <div class="row col-xs-12">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title"><?= Html::encode($this->title) ?></h3>
                    <div class="box-tools">
                        <?= Html::create(['ajax-edit','returnUrl' => Url::getReturnUrl()], '创建', [
                            'data-toggle' => 'modal',
                            'data-target' => '#ajaxModalLg',
                        ]); ?>
                        <?= Html::button('导出', [
                            'class'=>'btn btn-success btn-xs',
                            'onclick' => 'batchExport()',
                        ]);?>
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
                                'attribute' => 'type',
                                'filter' => false,
                                'value'=>function($model){
                                    return \common\enums\WorksTypeEnum::getValue($model->type);
                                },
                                'headerOptions' => ['width'=>'80'],
                            ],
                            [
                                'attribute' => 'creator_id',
                                'value'=>function($model) {
                                    return Html::a($model->member->username, ['view', 'id' => $model->id,'returnUrl'=>Url::getReturnUrl()], ['style'=>"text-decoration:underline;color:#3c8dbc"]);
                                },
                                'headerOptions' => ['class' => 'col-md-1'],
                                'format' => 'raw',
                                'filter' => Html::activeTextInput($searchModel, 'member.username', [
                                    'class' => 'form-control',
                                ]),

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
                                'attribute' => 'title',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-2'],
                                'filter' =>false,
                            ],
                            [
                                'attribute' => 'content',
                                'value'=> function($model){
                                    return $model->content;
                                },
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-5'],
                                'filter' =>false,
                            ],


                            [
                                'attribute'=>'date',
                                'filter' => DateRangePicker::widget([    // 日期组件
                                    'model' => $searchModel,
                                    'attribute' => 'date',
                                    'value' => $searchModel->date,
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
                                    return $model->date;
                                }

                            ],
                            'created_at:datetime',
                            [
                                'class' => 'yii\grid\ActionColumn',
                                'header' => '操作',
                                'template' => '{edit} {info} ',
                                'buttons' => [
                                    'edit' => function($url, $model, $key){
                                        return Html::edit(['ajax-edit','id' => $model->id,'returnUrl' => Url::getReturnUrl()], '编辑', [
                                            'data-toggle' => 'modal',
                                            'data-target' => '#ajaxModalLg',
                                        ]);
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
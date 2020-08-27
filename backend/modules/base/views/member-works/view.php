<?php

use common\helpers\Html;
use addons\Warehouse\common\enums\BillStatusEnum;
use common\enums\AuditStatusEnum;

/* @var $this yii\web\View */
/* @var $model common\models\order\order */
/* @var $form yii\widgets\ActiveForm */

?>
<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title"><i class="fa fa-bars"></i> 个人信息</h3>
            </div>
            <div class="box-body table-responsive" style="padding-left: 0px;padding-right: 0px;">
                <div class="col-xs-6">
                    <div class="box">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <tr>
                                    <td class="col-xs-2 text-right">姓名：</td>
                                    <td><?= $model->username ?? '' ?></td>
                                </tr>
                            </table>
                        </div>

                    </div>
                </div>

                <div class="col-xs-6">
                    <div class="box">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <tr>
                                    <td class="col-xs-2 text-right">所属部门：</td>
                                    <td><?= $model->department->name ??"" ?></td>
                                </tr>

                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>


    <div class="col-xs-12">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title"><i class="fa fa-qrcode"></i> 日报总结</h3>
                <div class="box-tools">
                    <?= Html::create(['ajax-edit','returnUrl' => \common\helpers\Url::getReturnUrl()], '创建', [
                        'data-toggle' => 'modal',
                        'data-target' => '#ajaxModal',
                    ]); ?>

                </div>
            </div>
            <div class="box-body table-responsive">
                <?php echo Html::batchButtons(false)?>
                <?= \yii\grid\GridView::widget([
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
//                        [
//                            'label' => '添加人',
//                            'attribute' => 'member.username',
//                            'headerOptions' => ['class' => 'col-md-1'],
//                            'filter' => Html::activeTextInput($searchModel, 'member.username', [
//                                'class' => 'form-control',
//                            ]),
//
//                        ],
//                        [
//                            'attribute' => 'dept_id',
//                            'value'=>function($model) {
//                                return $model->department->name ?? '';
//                            },
//                            'filter' => Html::activeDropDownList($searchModel, 'dept_id',Yii::$app->services->department->getDropDown(), [
//                                'prompt' => '全部',
//                                'class' => 'form-control',
//                                'style'=> 'width:100px;'
//                            ]),
//                            'format' => 'raw',
//                            //'headerOptions' => ['width'=>'150'],
//                        ],
                        [
                            'attribute' => 'title',
                            'format' => 'raw',
                            'headerOptions' => ['class' => 'col-md-2'],
                            'filter' =>false,
                        ],
                        [
                            'attribute' => 'content',
                            'format' => 'raw',
                            'headerOptions' => ['class' => 'col-md-5'],
                            'filter' =>false,
                        ],


                        [
                            'attribute'=>'date',
                            'filter' => \kartik\daterange\DateRangePicker::widget([    // 日期组件
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
                        [
                            'attribute' => 'created_at',
                            'value' => function($model){
                                return Yii::$app->formatter->asDatetime($model->created_at);
                            },
                            'headerOptions' => ['class' => 'col-md-5'],
                            'filter' =>false,
                        ],
                        [
                            'class' => 'yii\grid\ActionColumn',
                            'header' => '操作',
                            'template' => '{edit} {info} ',
                            'buttons' => [
                                'edit' => function($url, $model, $key){
                                    return Html::edit(['ajax-edit','id' => $model->id,'returnUrl' => \common\helpers\Url::getReturnUrl()], '编辑', [
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
    </div>
</div>

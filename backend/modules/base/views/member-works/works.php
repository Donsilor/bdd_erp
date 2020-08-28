<?php

use common\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\order\order */
/* @var $form yii\widgets\ActiveForm */
?>
<style>
    .modal-open .modal{
        margin: 0;padding: 0;
        width: 100%;
        height: 100%;
        position: fixed;
        left: 0;
        top: 0;
        background: rgba(0,0,0,0.6);
    }
    .modal.in .modal-dialog {
        width: 100%;
        margin: 0;padding: 0;
        position: fixed;
        top: 50%;
        left: 50%;
        -moz-transform: translate(-50%, -50%);
        -ms-transform: translate(-50%, -50%);
        -webkit-transform: translate(-50%, -50%);
        transform: translate(-50%, -50%);
        z-index: 9999999;
    }
    .modal-content {
    }
</style>
<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title"><i class="fa fa-bars"></i> 个人信息</h3>
                <div class="box-tools">
                    <a href="<?= \common\helpers\Url::to(['../site/logout']); ?>" data-method="post"><i class="fa fa fa-sign-out"></i>退出</a>
                </div>
            </div>
            <div class="box-body table-responsive" style="padding-left: 0px;padding-right: 0px;">
                <div class="col-xs-12">
                    <div class="box">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <tr>
                                    <td class="col-xs-2 text-right">姓名：</td>
                                    <td><?= $model->username ?? '' ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-2 text-right">部门：</td>
                                    <td><?= $model->department->name ??"" ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-2 text-right">岗位：</td>
                                    <td><?= $model->assignment->role->title ?? '' ?></td>
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
                <h5 class="box-title"><i class="fa fa-qrcode"></i> 日报总结</h5>
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
                            'format' => 'raw',
                            'value'=>function($model){
                                return Html::a($model->date, ['works-view', 'id' => $model->id,'returnUrl'=>\common\helpers\Url::getReturnUrl()], ['style'=>"text-decoration:underline;color:#3c8dbc"]);
                            }

                        ],
//                        [
//                            'attribute' => 'title',
//                            'value'=>function($model) {
//                                return Html::a($model->title, ['works-view', 'id' => $model->id,'returnUrl'=>\common\helpers\Url::getReturnUrl()], ['style'=>"text-decoration:underline;color:#3c8dbc"]);
//                            },
//                            'headerOptions' => ['class' => 'col-md-1'],
//                            'format' => 'raw',
//                            'filter' => Html::activeTextInput($searchModel, 'member.username', [
//                                'class' => 'form-control',
//                            ]),
//
//                        ],
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

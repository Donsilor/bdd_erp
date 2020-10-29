<?php

use common\helpers\Html;
use common\helpers\Url;
use yii\grid\GridView;
use common\enums\AuditStatusEnum;
use kartik\daterange\DateRangePicker;
use addons\Style\common\enums\QibanTypeEnum;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = '待处理列表';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-sm-12">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title"><?= Html::encode($this->title) ?></h3>
                <div class="box-tools">

                </div>
            </div>
            <div class="box-body table-responsive">
                <?php //echo Html::batchButtons()?>
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'tableOptions' => ['class' => 'table table-hover'],
                    'options' => ['style' => 'white-space:nowrap;'],
                    'showFooter' => false,//显示footer行
                    'id' => 'grid',
                    'columns' => [
                        [
                            'class' => 'yii\grid\SerialColumn',
                            'visible' => true,
                        ],
                        [
                            'class' => 'yii\grid\CheckboxColumn',
                            'name' => 'id',  //设置每行数据的复选框属性
                            'headerOptions' => ['width' => '60'],
                        ],
//                        [
//                            'attribute' => 'id',
//                            'filter' => true,
//                            'format' => 'raw',
//                            'headerOptions' => ['width'=>'80'],
//                        ],
                        [
                            'attribute' => 'pend_module',
                            'value' => function ($model) {
                                return \common\enums\FlowCateEnum::getValue($model->pend_module);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'pend_module', \common\enums\FlowCateEnum::getMap(), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                            ]),
                            'format' => 'raw',
                            'headerOptions' => ['class' => 'col-md-1'],
                        ],
                        [
                            'attribute' => 'oper_type',
                            'value' => function ($model) {
                                return \common\enums\OperTypeEnum::getValue($model->oper_type);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'oper_type', \common\enums\OperTypeEnum::getMap(), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                            ]),
                            'format' => 'raw',
                            'headerOptions' => ['class' => 'col-md-1'],
                        ],
                        [
                            'attribute' => 'oper_sn',
                            'value' => function ($model) {
                                return Html::a($model->oper_sn, ['../' . \common\enums\OperTypeEnum::getUrlValue($model->oper_type), 'id' => $model->oper_id], ['class' => 'openContab', 'style' => "text-decoration:underline;color:#3c8dbc"]);
                            },
                            'filter' => Html::activeTextInput($searchModel, 'oper_sn', [
                                'class' => 'form-control',
                                'style' => 'width:150px;'
                            ]),
                            'format' => 'raw',
                            'headerOptions' => ['class' => 'col-md-1'],
                        ],
                        [
                            'attribute' => 'operor_id',
                            'value' => function ($model) {
                                return $model->operor->username ?? '';
                            },
                            'headerOptions' => ['class' => 'col-md-1'],
                            'filter' => false
                        ],
                        [
                            'attribute' => 'pend_status',
                            'value' => function ($model) {
                                $pend_status = \common\enums\PendStatusEnum::getValue($model->pend_status);
                                if ($model->pend_status == \common\enums\PendStatusEnum::CONFIRM) {
                                    $pend_status = "<span style='color: #00a65a'>{$pend_status}</span>";
                                } else {
                                    $pend_status = "<span style='color: red'>{$pend_status}</span>";
                                }
                                return $pend_status;
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'pend_status', \common\enums\PendStatusEnum::getMap(), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                            ]),
                            'format' => 'raw',
                            'headerOptions' => ['class' => 'col-md-1'],
                        ],
                        [
                            'attribute' => 'creator_id',
                            'value' => function ($model) {
                                return $model->creator->username ?? '';
                            },
                            'headerOptions' => ['class' => 'col-md-1'],
                            'filter' => false
                        ],
                        [
                            'attribute' => 'created_at',
                            'value' => function ($model) {
                                return Yii::$app->formatter->asDatetime($model->created_at);
                            },
                            'filter' => DateRangePicker::widget([    // 日期组件
                                'model' => $searchModel,
                                'attribute' => 'created_at',
                                'value' => $searchModel->created_at,
                                'options' => ['readonly' => false, 'class' => 'form-control', 'style' => 'background-color:#fff;width:150px;'],
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
                        ],
                        [
                            'attribute' => 'pend_time',
                            'value' => function ($model) {
                                return Yii::$app->formatter->asDatetime($model->pend_time);
                            },
                            'filter' => DateRangePicker::widget([    // 日期组件
                                'model' => $searchModel,
                                'attribute' => 'pend_time',
                                'value' => $searchModel->pend_time,
                                'options' => ['readonly' => false, 'class' => 'form-control', 'style' => 'background-color:#fff;width:150px;'],
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
                        ],
                        [
                            'attribute' => 'pend_way',
                            'filter' => true,
                            'headerOptions' => ['class' => 'col-md-1'],
                        ],
                        [
                            'attribute' => 'oper_id',
                            'filter' => true,
                            'headerOptions' => ['width' => '80'],
                        ],
                    ]
                ]);
                ?>
            </div>
        </div>
    </div>
</div>

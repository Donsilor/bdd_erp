<?php
use common\helpers\Html;
use jianyan\treegrid\TreeGrid;
use yii\widgets\ActiveForm;
use common\helpers\Url;

$this->title = '退款原因';
$this->params['breadcrumbs'][] = ['label' => $this->title];

?>

<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title"><?= $this->title; ?></h3>
                <div class="box-tools">
                    <?= Html::create(['ajax-edit'], '创建', [
                        'data-toggle' => 'modal',
                        'data-target' => '#ajaxModalLg',
                    ])?>
                </div>
            </div>

            <div class="box-body table-responsive">
                <div class="row">
                    <div class="col-sm-12">
                        <?php $form = ActiveForm::begin([
                            'action' => Url::to(['index']),
                            'method' => 'get',
                        ]); ?>

                        <div class="col-sm-12">
                            <div class="row">
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" name="title" placeholder="标题或者ID" value="<?= Yii::$app->request->get('title') ?>"/>
                                </div>
                                <div class="col-sm-1">
                                    <select class="form-control" name="status">
                                        <option value="-1">请选择</option>
                                        <option value="1" <?php if($status == 1){ echo 'selected';} ?>>启动</option>
                                        <option value="0" <?php if($status == 0){ echo 'selected';} ?>>禁用</option>
                                    </select>
                                </div>
                                <div class="col-sm-4">
                                    <span class="input-group-btn"><button class="btn btn-white"><i class="fa fa-search"></i> 搜索</button></span>
                                </div>
                            </div>
                        </div>
                        <?php ActiveForm::end(); ?>
                    </div>
                </div>
                <?= TreeGrid::widget([
                    'dataProvider' => $dataProvider,
                    'keyColumnName' => 'id',
                    'parentColumnName' => 'pid',
                    'parentRootValue' => '0', //first parentId value
                    'pluginOptions' => [
//                        'initialState' => 'collapsed',
                    ],
                    'options' => ['class' => 'table table-hover'],

                    'columns' => [



                        [
                            'attribute' => 'name',
                            'format' => 'raw',
                            'value' => function ($model, $key, $index, $column){
                                $str = Html::tag('span', $model->name, [
                                    'class' => 'm-l-sm'
                                ]);

                                $str .= Html::a(' <i class="icon ion-android-add-circle"></i>', ['ajax-edit', 'pid' => $model['id']], [
                                    'data-toggle' => 'modal',
                                    'data-target' => '#ajaxModal',
                                ]);
                                return $str;
                            },

                           ],

                        [
                            'attribute'=>'id',
                            'value'=> 'id',
                            'headerOptions'=>['style'=>'width:50px;'],

                        ],
                        [
                            'attribute' => 'sort',
                            'format' => 'raw',
                            'headerOptions' => ['class' => 'col-md-1'],
                            'value' => function ($model, $key, $index, $column){
                                return  Html::sort($model->sort);
                            }
                        ],
                        [
                            'header' => "操作",
                            'class' => 'yii\grid\ActionColumn',
                            'template'=> '{edit} {status}',
                            'buttons' => [
                                'edit' => function ($url, $model, $key) {
                                    return Html::edit(['ajax-edit','id' => $model->id], '编辑', [
                                        'data-toggle' => 'modal',
                                        'data-target' => '#ajaxModalLg',
                                    ]);
                                },
                                'status' => function ($url, $model, $key) {
                                    return Html::status($model->status);
                                },
                                'delete' => function ($url, $model, $key) {
                                    return Html::delete(['delete','id' => $model->id]);
                                },
                            ],
                        ],
                    ]
                ]); ?>

            </div>

        </div>
    </div>
</div>

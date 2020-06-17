<?php
use common\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '配石列表';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title"><?= Html::encode($this->title) ?></h3>
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
                                'visible' => true,
                            ],
                            [
                                    'attribute' => 'id',
                                    'value'  => 'id',
                                    'filter' => true,
                            ],  
                            [
                                    'attribute' => 'stone_position',
                                    'value'  => function($model) {
                                         return \addons\Style\common\enums\StonePositionEnum::getValue($model->stone_position);
                                    },
                                    'filter' => false,
                                    
                            ],
                            [
                                    'attribute' => 'stone_type',
                                    'value'  => function($model) {
                                        return $model->stone_type ?? '无';
                                    },
                                    'filter' => false,
                                    
                            ],
                            [
                                    'attribute' => 'stone_num',
                                    'value' => 'stone_num',
                                    'filter' => false,

                            ],
                            [
                                    'attribute' => 'shape',
                                    'value' => function($model){
                                        return $model->shape ?? '无';
                                    },
                                    'filter' => false,
                                    
                            ],
                            [
                                    'attribute' => 'color',
                                    'value' => function($model){
                                        return $model->color ?? '无';
                                    },
                                    'filter' => false,                                    
                            ],
                            [
                                    'attribute' => 'clarity',
                                    'value' => function($model){
                                        return $model->clarity ?? '无';
                                    },
                                    'filter' => false,
                                    
                            ],
                            [
                                    'attribute' => 'stone_spec',
                                    'value' => 'stone_spec',
                                    'filter' => false,                                    
                            ],
                            [
                                    'attribute' => 'songshi_user',
                                    'filter' => false,
                            ],
                            [
                                    'attribute' => 'songshi_time',
                                    'value' =>  function($model){
                                        return Yii::$app->formatter->asDatetime($model->songshi_time);
                                    },
                                    'filter' => false,
                            ],
                            [
                                    'attribute' => 'peishi_user',
                                    'filter' => false,
                            ],
                            [
                                    'attribute' => 'peishi_time',
                                    'value' =>  function($model){
                                        return Yii::$app->formatter->asDatetime($model->peishi_time);
                                    },
                                    'filter' => false,
                            ],
                            [
                                'attribute'=>'remark',
                                'filter' => false,
                                'headerOptions' => [],
                            ],
                            [
                                'attribute' => 'created_at',
                                'filter' => false,
                                'value' => function($model){
                                    return Yii::$app->formatter->asDatetime($model->created_at);
                                }

                            ],
                            [
                                'attribute' => 'creator_name',
                                'filter' => false,
                            ],


                        ]
                    ]); ?>
            </div>
        </div>
    </div>
</div>
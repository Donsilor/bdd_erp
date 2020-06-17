<?php
use common\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '配料列表';
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
                                    'attribute' => 'gold_type',
                                    'value'  => function($model) {
                                        return $model->gold_type ?? '无';
                                    },
                                    'filter' => false,
                                    
                            ],
                            [
                                    'attribute' => 'gold_weight',
                                    'value' => 'gold_weight',
                                    'filter' => false,

                            ],               
      
                            [
                                    'attribute' => 'gold_spec',
                                    'value' => 'gold_spec',
                                    'filter' => false,                                    
                            ],                           
                            [
                                'attribute' => 'created_at',
                                'filter' => false,
                                'value' => function($model){
                                    return Yii::$app->formatter->asDatetime($model->created_at);
                                }

                            ],
                            [
                                    'attribute' => 'songliao_user',
                                    'value' => 'songliao_user',
                                    'filter' => false,
                            ],
                            [
                                    'attribute' => 'songliao_time',
                                    'value' =>  function($model){
                                        return Yii::$app->formatter->asDatetime($model->songliao_time);
                                    },
                                    'filter' => false,
                            ],
                            [
                                    'attribute' => 'peiliao_user',
                                    'value' => 'songliao_user',
                                    'filter' => false,
                            ],
                            [
                                    'attribute' => 'peiliao_time',
                                    'value' =>  function($model){
                                        return Yii::$app->formatter->asDatetime($model->peiliao_time);
                                    },
                                    'filter' => false,
                            ],
                            [
                                    'attribute'=>'remark',
                                    'filter' => false,
                            ],                            
                            [
                                'attribute' => 'creator_name',
                                'headerOptions' => ['class' => 'col-md-1'],
                                'filter' => false,

                            ],
                            
                        ]
                    ]); ?>
            </div>
        </div>
    </div>
</div>
<?php
use yii\widgets\ActiveForm;
use common\helpers\Url;
use yii\grid\GridView;
use common\helpers\Html;


$this->title = '批量配石';
$this->params['breadcrumbs'][] = ['label' => 'Curd', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <?php $form = ActiveForm::begin([]); ?>
            <div class="box-body" style="padding:20px 50px">
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
                                    'attribute' => 'peishi_status',
                                    'value' => function ($model){
                                        return \addons\Supply\common\enums\PeishiStatusEnum::getValue($model->peishi_status);
                                    },
                                    'filter' => false,
                                    'format' => 'raw',
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
                                    'attribute' => '配石信息',
                                    'value' => function($model){
                                        return '石包号：111111，数量：120，重量1.6ct<br/>'.'石包号：222222，数量：10，重量1.2ct<br/>';
                                    },
                                    'filter' => false,
                                    'format' => 'raw',
                            ],                             
                            [
                                 'attribute'=>'remark',
                                 'value'=>function($model){
                                       
                                 },
                                 'filter' => false,
                                 'headerOptions' => [],
                            ],
                            [
                                    'class' => 'yii\grid\ActionColumn',
                                    'header' => '操作',
                                    'template' => '{edit}',
                                    'buttons' => [
                                            
                                    ]
                            ]
                        ]
                    ]); ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

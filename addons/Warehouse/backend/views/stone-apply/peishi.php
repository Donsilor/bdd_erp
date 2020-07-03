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
            <div class="box-body" style="padding:20px 20px">
                <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'tableOptions' => ['class' => 'table'],
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
                                    'attribute' => 'stone_weight',
                                    'value' => 'stone_weight',
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
                                    'attribute' => 'secai',
                                    'value' => function($model){
                                        return $model->secai ?? '无';
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
                           /*  [
                                    'attribute' => 'stone_spec',
                                    'value' => 'stone_spec',
                                    'filter' => false,                                    
                            ],  */
                            [
                                    'attribute' => 'remark',
                                    'value' => 'remark',
                                    'filter' => false,
                            ],
                            [
                                    'label' => '配石信息(石包编号/配石数量/配石总重)',
                                    'filter' => false,
                                    'format' => 'raw',
                                    'headerOptions' => ['style'=>'width:400px'],
                                    'value' => function($model) {
                                          return unclead\multipleinput\MultipleInput::widget([
                                                'max'=>3,
                                                'name' => "ProduceStone[{$model->id}][ProduceStoneGoods]",
                                                'value' => $model->stoneGoods ??[],
                                                'columns' => [
                                                        [
                                                                'name' => 'stone_sn',
                                                                'title'=>false,
                                                                'enableError'=>false,
                                                                'options' => [
                                                                        'class' => 'input-priority',
                                                                        'style'=>'width:150px',
                                                                        'placeholder'=>'石包编号',
                                                                ]
                                                        ],
                                                        [
                                                                'name' =>'stone_num',
                                                                'title'=>false,
                                                                'enableError'=>false,
                                                                'options' => [
                                                                        'class' => 'input-priority',
                                                                        'style'=>'width:100px',
                                                                        'placeholder'=>'领石数量',
                                                                ]
                                                        ],
                                                        [
                                                                'name' => "stone_weight",
                                                                'title'=>false,
                                                                'enableError'=>false,
                                                                'options' => [
                                                                        'class' => 'input-priority',
                                                                        'style'=>'width:100px',
                                                                        'placeholder'=>'领石总重',
                                                                ]
                                                        ]
                                                ]
                                          ]);
                                    },
                                    
                            ],
                            [
                                     'attribute'=>'peishi_remark',
                                     'value'=>function($model){
                                            return Html::activeTextarea($model, "[{$model->id}]peishi_remark",['class' => 'form-control']);
                                     },
                                     'filter' => false,                                 
                                     'headerOptions' => [],
                                     'format' => 'raw',
                            ]                            
                        ]
                    ]); ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

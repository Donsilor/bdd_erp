<?php
use yii\widgets\ActiveForm;
use common\helpers\Url;
use yii\grid\GridView;
use common\helpers\Html;

$this->title = '批量配件';
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
                                    'attribute' => 'peijian_status',
                                    'value' => function ($model){
                                        return \addons\Supply\common\enums\PeijianStatusEnum::getValue($model->peijian_status);
                                    },
                                    'filter' => false,
                                    'format' => 'raw',
                            ],
                            [
                                    'attribute' => 'parts_type',
                                    'value'  => function($model) {
                                        return $model->parts_type ?? '无';
                                    },
                                    'filter' => false,
                                    
                            ],
                            [
                                    'attribute' => 'parts_weight',
                                    'value' => 'parts_weight',
                                    'filter' => false,

                            ], 
                            [
                                    'attribute' => 'remark',
                                    'value' => 'remark',
                                    'filter' => false,                                    
                            ], 
                            [
                                    'label' => '配件信息(领件编号/领件总重)',
                                    'filter' => false,
                                    'format' => 'raw',
                                    'headerOptions' => ['style'=>'width:400px'],
                                    'value' => function($model) {
                                          return unclead\multipleinput\MultipleInput::widget([
                                                'max'=>1,
                                                'name' => "ProduceParts[{$model->id}][ProducePartsGoods]",
                                                'value' => $model->partsGoods ??[],
                                                'columns' => [
                                                        [
                                                                'name' => 'parts_sn',
                                                                'title'=>false,
                                                                'enableError'=>false,
                                                                'options' => [
                                                                        'class' => 'input-priority',
                                                                        //'style'=>'width:150px',
                                                                        'placeholder'=>'领件编号',
                                                                ]
                                                        ],
                                                        [
                                                                'name' => "parts_num",
                                                                'title'=>false,
                                                                'enableError'=>false,
                                                                'options' => [
                                                                    'class' => 'input-priority',
                                                                    'style'=>'width:100px',
                                                                    'placeholder'=>'领件数量',
                                                                ]
                                                        ],
                                                        [
                                                                'name' => "parts_weight",
                                                                'title'=>false,
                                                                'enableError'=>false,
                                                                'options' => [
                                                                        'class' => 'input-priority',
                                                                        'style'=>'width:100px',
                                                                        'placeholder'=>'领件总重',
                                                                ]
                                                        ]
                                                ]
                                          ]);
                                    },
                                    
                            ],
                            [
                                     'attribute'=>'peijian_remark',
                                     'value'=>function($model){
                                            return Html::activeTextarea($model, "[{$model->id}]peijian_remark",['class' => 'form-control']);
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

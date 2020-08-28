<?php

use yii\widgets\ActiveForm;
use common\helpers\Url;
use yii\grid\GridView;
use common\helpers\Html;


$this->title = '批量配料';
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
                    'options' => ['style' => 'white-space:nowrap;'],
                    'id' => 'grid',
                    'columns' => [
                        [
                            'class' => 'yii\grid\SerialColumn',
                            'visible' => true,
                        ],
                        [
                            'attribute' => 'id',
                            'value' => 'id',
                            'filter' => true,
                        ],
                        [
                            'attribute' => 'peiliao_status',
                            'value' => function ($model) {
                                return \addons\Supply\common\enums\PeiliaoStatusEnum::getValue($model->peiliao_status);
                            },
                            'filter' => false,
                            'format' => 'raw',
                        ],
                        [
                            'attribute' => 'gold_type',
                            'value' => function ($model) {
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
                            'attribute' => 'remark',
                            'value' => 'remark',
                            'filter' => false,
                        ],
                        [
                            'label' => '配石信息(金料编号/金料总重)',
                            'filter' => false,
                            'format' => 'raw',
                            'headerOptions' => ['style' => 'width:400px'],
                            'value' => function ($model) {
                                return unclead\multipleinput\MultipleInput::widget([
                                    'max' => 1,
                                    'name' => "ProduceGold[{$model->id}][ProduceGoldGoods]",
                                    'value' => $model->goldGoods ?? [],
                                    'columns' => [
                                        [
                                            'name' => 'gold_sn',
                                            'title' => false,
                                            'enableError' => false,
                                            'options' => [
                                                'class' => 'input-priority',
                                                //'style'=>'width:150px',
                                                'placeholder' => '金料编号',
                                            ]
                                        ],
                                        [
                                            'name' => "gold_weight",
                                            'title' => false,
                                            'enableError' => false,
                                            'options' => [
                                                'class' => 'input-priority',
                                                'style' => 'width:100px',
                                                'placeholder' => '领料总重',
                                            ]
                                        ]
                                    ]
                                ]);
                            },

                        ],
                        [
                            'attribute' => 'peiliao_remark',
                            'value' => function ($model) {
                                return Html::activeTextarea($model, "[{$model->id}]peiliao_remark", ['class' => 'form-control']);
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

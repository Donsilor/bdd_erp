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
                <div class="box-tools">
                    <?php
                    echo Html::a('批量配料', ['peiliao', 'check' => 1], [
                        'class' => 'btn btn-primary btn-xs',
                        "onclick" => "batchPop(this);return false;",
                        'data-grid' => 'grid',
                        'data-width' => '90%',
                        'data-height' => '90%',
                        'data-offset' => '20px',
                        'data-title' => '批量配料',
                    ]);
                    echo '&nbsp;';
                    ?>
                    <?php
                    echo Html::a('创建领料单', ['lingliao', 'check' => 1], [
                        'class' => 'btn btn-success btn-xs',
                        "onclick" => "batchPop(this);return false;",
                        'data-grid' => 'grid',
                        'data-width' => '90%',
                        'data-height' => '90%',
                        'data-offset' => '20px',
                        'data-title' => '创建领料单-预览',
                    ]);
                    echo '&nbsp;';
                    ?>
                </div>
            </div>
            <div class="box-body table-responsive">
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'tableOptions' => ['class' => 'table table-hover'],
                    'options' => ['style' => 'white-space:nowrap;'],
                    'showFooter' => false,//显示footer行
                    'id' => 'grid',
                    'columns' => [
                        [
                            'class' => 'yii\grid\CheckboxColumn',
                            'name' => 'id',  //设置每行数据的复选框属性
                            'headerOptions' => ['width' => '30'],
                        ],
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
                            'attribute' => 'created_at',
                            'filter' => false,
                            'value' => function ($model) {
                                return Yii::$app->formatter->asDatetime($model->created_at);
                            }
                        ],
                        [
                            'attribute' => 'from_order_sn',
                            'filter' => Html::activeTextInput($searchModel, 'from_order_sn', [
                                'class' => 'form-control',
                                'style' => 'width:150px'
                            ]),
                            'format' => 'raw',

                        ],
                        [
                            'attribute' => 'from_type',
                            'value' => function ($model) {
                                return \addons\Supply\common\enums\FromTypeEnum::getValue($model->from_type);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'from_type', \addons\Supply\common\enums\FromTypeEnum::getMap(), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style' => 'width:80px;',
                            ]),
                            'format' => 'raw',
                        ],
                        [
                            'attribute' => 'peiliao_status',
                            'value' => function ($model) {
                                return \addons\Supply\common\enums\PeiliaoStatusEnum::getValue($model->peiliao_status);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'peiliao_status', \addons\Supply\common\enums\PeiliaoStatusEnum::getMap(), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style' => 'width:80px;',
                            ]),
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
                            'label' => '领料单号',
                            'attribute' => 'delivery_no',
                            'filter' => Html::activeTextInput($searchModel, 'delivery_no', [
                                'class' => 'form-control',
                                'style' => 'width:150px'
                            ]),
                            'format' => 'raw',

                        ],
                        [
                            'label' => '配料信息(金料编号/金料类型/金重)',
                            'value' => function ($model) {
                                $str = '';
                                foreach ($model->goldGoods ?? [] as $goldGoods) {
                                    $gold_type = Yii::$app->attr->valueName($goldGoods->gold->gold_type ?? '');
                                    $str .= $goldGoods->gold_sn . '/' . $gold_type . '/' . $goldGoods->gold_weight . "g<br/>";
                                }
                                return $str;
                            },
                            'filter' => false,
                            'format' => 'raw',
                        ],
                        [
                            'attribute' => 'peiliao_user',
                            'value' => 'peiliao_user',
                            'filter' => false,
                        ],
                        [
                            'attribute' => 'peiliao_time',
                            'value' => function ($model) {
                                return Yii::$app->formatter->asDatetime($model->peiliao_time);
                            },
                            'filter' => false,
                        ],
                        [
                            'attribute' => 'peiliao_remark',
                            'filter' => false,
                        ],
                        [
                            'attribute' => 'remark',
                            'filter' => false,
                        ],
                    ]
                ]); ?>
            </div>
        </div>
    </div>
</div>
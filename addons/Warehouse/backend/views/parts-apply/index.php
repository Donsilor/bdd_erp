<?php
use common\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '配件列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title"><?= Html::encode($this->title) ?></h3>
                <div class="box-tools">
                    <?php
                        echo Html::a('批量配件', ['peiliao','check'=>1],  [
                            'class'=>'btn btn-primary btn-xs',
                            "onclick" => "batchPop(this);return false;",
                            'data-grid'=>'grid',
                            'data-width'=>'90%',
                            'data-height'=>'90%',
                            'data-offset'=>'20px',
                            'data-title'=>'批量配件',
                        ]);
                        echo '&nbsp;';                        
                    ?>
                    <?php
                        echo Html::a('创建领件单', ['lingliao','check'=>1],  [
                            'class'=>'btn btn-success btn-xs',
                            "onclick" => "batchPop(this);return false;",
                            'data-grid'=>'grid',
                            'data-width'=>'90%',
                            'data-height'=>'90%',
                            'data-offset'=>'20px',
                            'data-title'=>'创建领件单-预览',
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
                        'options' => ['style'=>'white-space:nowrap;'],
                        'showFooter' => false,//显示footer行
                        'id'=>'grid',
                        'columns' => [
                            [
                                    'class'=>'yii\grid\CheckboxColumn',
                                    'name'=>'id',  //设置每行数据的复选框属性
                                    'headerOptions' => ['width'=>'30'],
                            ],
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
                                    'attribute' => 'created_at',
                                    'filter' => false,
                                    'value' => function($model){
                                            return Yii::$app->formatter->asDatetime($model->created_at);
                                    }                            
                            ], 
                            [
                                    'attribute' => 'from_order_sn',
                                    'filter' => Html::activeTextInput($searchModel, 'from_order_sn', [
                                            'class' => 'form-control',
                                            'style' =>'width:150px'
                                    ]),
                                    'format' => 'raw',
                                    
                            ],
                            [
                                    'attribute' => 'from_type',
                                    'value' => function ($model){
                                         return \addons\Supply\common\enums\FromTypeEnum::getValue($model->from_type);
                                    },
                                    'filter' =>Html::activeDropDownList($searchModel, 'from_type',\addons\Supply\common\enums\FromTypeEnum::getMap(), [
                                            'prompt' => '全部',
                                            'class' => 'form-control',
                                            'style' => 'width:80px;',
                                    ]),
                                    'format' => 'raw',
                            ],
                            [
                                    'attribute' => 'peijian_status',
                                    'value' => function ($model){
                                        return \addons\Supply\common\enums\PeijianStatusEnum::getValue($model->peijian_status);
                                    },
                                    'filter' =>Html::activeDropDownList($searchModel, 'peijian_status',\addons\Supply\common\enums\PeijianStatusEnum::getMap(), [
                                            'prompt' => '全部',
                                            'class' => 'form-control',
                                            'style' => 'width:80px;',
                                    ]),
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
                                    'label' => '领件单号',
                                    'attribute' => 'delivery_no',
                                    'filter' => Html::activeTextInput($searchModel, 'delivery_no', [
                                            'class' => 'form-control',
                                            'style' =>'width:150px'
                                    ]),
                                    'format' => 'raw',
                                    
                            ],
                            [
                                    'label' => '配件信息(配件编号/配件类型/配件数量/配件重量)',
                                    'value' => function($model){
                                        $str = '';
                                        foreach ($model->partsGoods ?? [] as $partsGoods){
                                            $parts_type = Yii::$app->attr->valueName($partsGoods->parts->parts_type ??'');
                                            $str .= $partsGoods->parts_sn.'/'.$parts_type.'/'.$partsGoods->parts_weight.'/'.$partsGoods->parts_weight."g<br/>";
                                        }
                                        return $str;
                                    },
                                    'filter' => false,
                                    'format' => 'raw',
                            ],
                            [
                                    'attribute' => 'peijian_user',
                                    'value' => 'peiliao_user',
                                    'filter' => false,
                            ],
                            [
                                    'attribute' => 'peijian_time',
                                    'value' =>  function($model){
                                        return Yii::$app->formatter->asDatetime($model->peijian_time);
                                    },
                                    'filter' => false,
                            ],
                            [
                                    'attribute'=>'peijian_remark',
                                    'filter' => false,
                            ],
                            [
                                    'attribute'=>'remark',
                                    'filter' => false,
                            ],                            
                        ]
                    ]); ?>
            </div>
        </div>
    </div>
</div>
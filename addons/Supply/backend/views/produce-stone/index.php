<?php

use common\helpers\Html;

use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '配石信息';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box-body nav-tabs-custom">
    <h2 class="page-header">布产详情 - <?php echo $produce->produce_sn ?? ''?></h2>
    <?php echo Html::menuTab($tabList,$tab)?>
    <div class="tab-content">
        <div class="row col-xs-16" style="padding-left: 0px;padding-right: 0px;">
            <div class="box">
                <div class="box-body table-responsive" >
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
                                    'attribute' => 'peishi_status',
                                    'value' => function ($model){
                                        return \addons\Supply\common\enums\PeishiStatusEnum::getValue($model->peishi_status);
                                    },
                                    'filter' =>Html::activeDropDownList($searchModel, 'peishi_status',\addons\Supply\common\enums\PeishiStatusEnum::getMap(), [
                                            'prompt' => '全部',
                                            'class' => 'form-control',
                                            'style' => 'width:80px;',
                                    ]),
                                    'format' => 'raw',
                            ],
                            [
                                    'label' => '领石单号',
                                    'attribute' => 'delivery_no',
                                    'filter' => Html::activeTextInput($searchModel, 'delivery_no', [
                                            'class' => 'form-control',
                                            'style' =>'width:150px'
                                    ]),
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
                                    'label' => '配石信息(石头编号/数量/总重)',
                                    'value' => function($model){
                                        $str = '';
                                        foreach ($model->stoneGoods ?? [] as $stone){
                                            $str .=$stone->stone_sn.'/'.$stone->stone_num."/".$stone->stone_weight."ct<br/>";
                                        }
                                        return $str;
                                    },
                                    'filter' => false,
                                    'format' => 'raw',
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


                        ]
                    ]); ?>
                </div>
            </div>
            <!-- box end -->
        </div>
    </div>
</div>
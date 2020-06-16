<?php

use common\helpers\Html;

use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '配料信息';
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
                                    'attribute' => 'produce_sn',
                                    'value'  => 'produce_sn',
                                    'filter' => false,
                            ],
                            [
                                    'attribute' => 'gold_type',
                                    'value'  => function($model) {
                                        return $model->material_type ?? '无';
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
                                'label' => '操作人',
                                'attribute' => 'creator_name',
                                'headerOptions' => ['class' => 'col-md-1'],
                                'filter' => false,

                            ],


                        ]
                    ]); ?>
                </div>
            </div>
            <!-- box end -->
        </div>
    </div>
</div>
<?php

use common\helpers\Html;

use yii\grid\GridView;
use addons\Supply\common\enums\PeishiStatusEnum;

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
                                    'filter' =>false,
                                    'format' => 'raw',
                            ],
                            [
                                    'attribute' => 'is_increase',                                    
                                    'value' => function ($model){
                                        return \common\enums\ConfirmEnum::getValue($model->is_increase);
                                    },
                                    'filter' => false,
                                    'format' => 'raw',
                                    'headerOptions' => ['width' =>'80'],
                                    
                            ],
                            [
                                    'label' => '领石单号',
                                    'attribute' => 'delivery_no',
                                    'filter' => false,
                                    'format' => 'raw',
                                    'headerOptions' => ['class' =>'col-md-1'],
                                    
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

                            [
                                    'class' => 'yii\grid\ActionColumn',
                                    'header' => '操作',
                                    'template' => '{reset}',
                                    'buttons' => [
                                        'reset' =>function($url, $model, $key){
                                             if($model->peishi_status == PeishiStatusEnum::TO_LINGSHI) {
                                                 return Html::edit(['ajax-reset','id'=>$model->id], '重置配石', [
                                                        'class'=>'btn btn-primary btn-xs',
                                                        'style'=>"margin-left:5px",
                                                        'onclick' => 'rfTwiceAffirm(this,"重置配石","确定重置配石状态吗？");return false;',
                                                ]);  
                                             }
                                         }                                            
                                    ]
                                            

                            ]
                        ]
                    ]); ?>
                </div>
                <div class="box-footer text-center">
                  <?php 
                  if($produce->peishi_status == PeishiStatusEnum::TO_LINGSHI) {
                     echo Html::edit(['ajax-confirm','produce_id'=>$produce->id], '确认领石', [
                        'class'=>'btn btn-primary btn-ms',
                        'style'=>"margin-left:5px",
                        'onclick' => 'rfTwiceAffirm(this,"确认领石","确定操作吗？");return false;',
                    ]);                     
                  }
                 ?>
                </div>
            </div>
            <!-- box end -->
        </div>
    </div>
</div>
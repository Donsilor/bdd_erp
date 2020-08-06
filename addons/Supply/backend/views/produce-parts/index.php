<?php

use common\helpers\Html;

use yii\grid\GridView;
use addons\Supply\common\enums\PeiliaoStatusEnum;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '配件信息';
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
                                    'attribute' => 'peijian_status',
                                    'value' => function ($model){
                                        return \addons\Supply\common\enums\PeiliaoStatusEnum::getValue($model->peijian_status);
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
                                            foreach ($model->goldGoods ?? [] as $goldGoods){
                                                $gold_type = Yii::$app->attr->valueName($goldGoods->gold->gold_type ??'');
                                                $str .= $goldGoods->gold_sn.'/'.$gold_type.'/'.$goldGoods->gold_num.'/'.$goldGoods->gold_weight."g<br/>";
                                            }
                                            return $str;
                                    },
                                    'filter' => false,
                                    'format' => 'raw',
                            ],
                            [
                                    'attribute' => 'peijian_user',
                                    'value' => 'peijian_user',
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
                                    'attribute'=>'remark',
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
                                    'class' => 'yii\grid\ActionColumn',
                                    'header' => '操作',
                                    'template' => '{reset}',
                                    'buttons' => [
                                        'reset' =>function($url, $model, $key){
                                            if($model->peijian_status == \addons\Supply\common\enums\PeijianStatusEnum::TO_LINGJIAN) {
                                                return Html::edit(['ajax-reset','id'=>$model->id], '重置配件', [
                                                        'class'=>'btn btn-primary btn-xs',
                                                        'style'=>"margin-left:5px",
                                                        'onclick' => 'rfTwiceAffirm(this,"重置配件","确定重置配件状态吗？");return false;',
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
                  if($produce->peijian_status == \addons\Supply\common\enums\PeijianStatusEnum::TO_LINGJIAN) {
                     echo Html::edit(['ajax-confirm','produce_id'=>$produce->id], '确认领件', [
                        'class'=>'btn btn-primary btn-ms',
                        'style'=>"margin-left:5px",
                        'onclick' => 'rfTwiceAffirm(this,"确认领件","确定操作吗？");return false;',
                    ]);                     
                  }
                 ?>
                </div>
            </div>
            <!-- box end -->            
        </div>
    </div>
</div>
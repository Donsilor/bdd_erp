<?php

use common\helpers\Html;

use yii\grid\GridView;
use addons\Supply\common\enums\PeiliaoStatusEnum;

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
                                    'attribute' => 'peiliao_status',
                                    'value' => function ($model){
                                        return \addons\Supply\common\enums\PeiliaoStatusEnum::getValue($model->peiliao_status);
                                    },
                                    'filter' =>Html::activeDropDownList($searchModel, 'peiliao_status',\addons\Supply\common\enums\PeiliaoStatusEnum::getMap(), [
                                            'prompt' => '全部',
                                            'class' => 'form-control',
                                            'style' => 'width:80px;',
                                    ]),
                                    'format' => 'raw',
                            ],
                            [
                                    'attribute' => 'gold_type',
                                    'value'  => function($model) {
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
                                            'style' =>'width:150px'
                                    ]),
                                    'format' => 'raw',
                                    
                            ],
                            [
                                    'label' => '配料信息(金料编号/金料类型/金重)',
                                    'value' => function($model){
                                            $str = '';
                                            foreach ($model->goldGoods ?? [] as $goldGoods){
                                                $gold_type = Yii::$app->attr->valueName($goldGoods->gold->gold_type ??'');
                                                $str .= $goldGoods->gold_sn.'/'.$gold_type.'/'.$goldGoods->gold_weight."g<br/>";
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
                                    'value' =>  function($model){
                                            return Yii::$app->formatter->asDatetime($model->peiliao_time);
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
                                            if($model->peiliao_status == PeiliaoStatusEnum::TO_LINGLIAO) {
                                                return Html::edit(['ajax-reset','id'=>$model->id], '重置配料', [
                                                        'class'=>'btn btn-primary btn-xs',
                                                        'style'=>"margin-left:5px",
                                                        'onclick' => 'rfTwiceAffirm(this,"重置配料","确定重置配料状态吗？");return false;',
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
                  if($produce->peiliao_status == PeiliaoStatusEnum::TO_LINGLIAO) {
                     echo Html::edit(['ajax-confirm','produce_id'=>$produce->id], '确认领料', [
                        'class'=>'btn btn-primary btn-ms',
                        'style'=>"margin-left:5px",
                        'onclick' => 'rfTwiceAffirm(this,"确认领料","确定操作吗？");return false;',
                    ]);                     
                  }
                 ?>
                </div>
            </div>
            <!-- box end -->            
        </div>
    </div>
</div>
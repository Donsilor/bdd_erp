<?php

use common\helpers\Html;
use common\helpers\Url;
use yii\grid\GridView;

use addons\Supply\common\enums\BuChanEnum;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = '布产列表';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-sm-12">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title"><?= Html::encode($this->title) ?></h3>
                <div class="box-tools">
                </div>
            </div>
            <div class="box-body table-responsive">  
    <?php //echo Html::batchButtons()?>                  
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => ['class' => 'table table-hover'],
        'showFooter' => true,//显示footer行
        'id'=>'grid',            
        'columns' => [
            [
                    'class' => 'yii\grid\SerialColumn',
                    'visible' => false,
            ],
            [
                    'class'=>'yii\grid\CheckboxColumn',
                    'name'=>'id',  //设置每行数据的复选框属性
                    'headerOptions' => ['width'=>'30'],
            ],
            [
                    'attribute' => 'id',
                    'filter' => true,
                    'format' => 'raw',
                    'headerOptions' => ['width'=>'80'],
            ],  
            [
                    'attribute' => 'produce_sn',
                    'value'=>function($model) {
                        return Html::a($model->produce_sn, ['view', 'id' => $model->id,'returnUrl'=>Url::getReturnUrl()], ['style'=>"text-decoration:underline;color:#3c8dbc"]);
                    },
                    'filter' => true,
                    'format' => 'raw',
                    'headerOptions' => ['width'=>'130'],
            ],
            [
                    'attribute' => 'from_type',
                    'value' => function ($model){
                        return \addons\Supply\common\enums\FromTypeEnum::getValue($model->from_type);
                    },
                    'filter' =>Html::activeDropDownList($searchModel, 'from_type',\addons\Supply\common\enums\FromTypeEnum::getMap(), [
                        'prompt' => '全部',
                        'class' => 'form-control',
                    ]),
                    'format' => 'raw',
                    'headerOptions' => ['width'=>'100'],
            ],
            [
                    'attribute' => 'from_order_sn',
                    'filter' => true,
                    'format' => 'raw',
                    'headerOptions' => ['width'=>'80'],
            ],
            [
                'attribute' => 'style_sn',
                'value' => "style_sn",
                'filter' => true,
                'format' => 'raw',

            ],
            [
                'attribute' => 'from_detail_id',
                'value' => "purchaseGoods.goods_name",
                'filter' => true,
                'format' => 'raw',

            ],
            [
                'attribute' => 'goods_num',
                'filter' => false
            ],
            [
                'attribute' => 'bc_status',
                'value' => function ($model){
                    return \addons\Supply\common\enums\BuChanEnum::getValue($model->bc_status);
                },
                'filter' => Html::activeDropDownList($searchModel, 'bc_status',\addons\Supply\common\enums\BuChanEnum::getMap(), [
                    'prompt' => '全部',
                    'class' => 'form-control',
                ]),
                'format' => 'raw',
                'headerOptions' => ['width'=>'100'],
            ],
            [
                'attribute' => 'qiban_type',
                'value' => function ($model){
                    return \addons\Style\common\enums\QibanTypeEnum::getValue($model->qiban_type);
                },
                'filter' => Html::activeDropDownList($searchModel, 'qiban_type',\addons\Style\common\enums\QibanTypeEnum::getMap(), [
                    'prompt' => '全部',
                    'class' => 'form-control',
                ]),
                'format' => 'raw',
                'headerOptions' => ['width'=>'100'],
            ],
            [
                'attribute' => 'jintuo_type',
                'value' => function ($model){
                    return \addons\Style\common\enums\JintuoTypeEnum::getValue($model->jintuo_type);
                },
                'filter' => Html::activeDropDownList($searchModel, 'qiban_type',\common\enums\JinTuoEnum::getMap(), [
                    'prompt' => '全部',
                    'class' => 'form-control',
                ]),
                'format' => 'raw',
                'headerOptions' => ['width'=>'100'],
            ],
            [
                'attribute' => 'type.name',
                'value' => "type.name",
                'filter' => Html::activeDropDownList($searchModel, 'product_type_id',Yii::$app->styleService->productType->getDropDown(), [
                    'prompt' => '全部',
                    'class' => 'form-control',
                ]),
                'format' => 'raw',
                'headerOptions' => ['width'=>'100'],
            ],
            [
                'attribute' => 'cate.name',
                'value' => 'cate.name',
                'filter' => Html::activeDropDownList($searchModel, 'style_cate_id',Yii::$app->styleService->styleCate->getDropDown(), [
                    'prompt' => '全部',
                    'class' => 'form-control',
                ]),
                'format' => 'raw',
                'headerOptions' => ['width'=>'100'],
            ],
            [
                'label' => '跟单人',
                'filter' => Html::activeTextInput($searchModel, 'follower.username', [
                    'class' => 'form-control',
                ]),
                'value' => function ($model) {
                    return $model->follower ? $model->follower->username : null;
                },
                'format' => 'raw',
                'headerOptions' => ['width'=>'150'],
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'header' => '操作',
                'template' => ' {action} {status}',
                'buttons' => [
                    'action' => function($url, $model, $key){
                        $buttonHtml = '';
                        switch ($model->bc_status){
                            case BuChanEnum::INITIALIZATION:
                                $buttonHtml .= Html::edit(['to-factory','id'=>$model->id ,'returnUrl'=>Url::getReturnUrl()], '分配工厂', [
                                    'class'=>'btn btn-primary btn-sm',
                                    'style'=>"margin-left:5px",
                                    'data-toggle' => 'modal',
                                    'data-target' => '#ajaxModal',
                                ]);
                                break;
                            case BuChanEnum::TO_CONFIRMED:
                                $buttonHtml .= Html::edit(['to-confirmed','id'=>$model->id ,'returnUrl'=>Url::getReturnUrl()], '确认分配', [
                                    'class'=>'btn btn-info btn-sm',
                                    'style'=>"margin-left:5px",
                                    'onclick' => 'rfTwiceAffirm(this,"确认分配","确定操作吗？");return false;',

                                ]);
                                break;
                            case BuChanEnum::ASSIGNED:
                                $buttonHtml .= Html::edit(['to-produce','id'=>$model->id ,'returnUrl'=>Url::getReturnUrl()], '开始生产', [
                                    'class'=>'btn btn-danger btn-sm',
                                    'style'=>"margin-left:5px",
                                    'onclick' => 'rfTwiceAffirm(this,"开始生产","确定操作吗？");return false;',

                                ]);
                                break;
                            case BuChanEnum::IN_PRODUCTION :
                                ;
                            case BuChanEnum::PARTIALLY_SHIPPED:
                                $buttonHtml .= Html::edit(['produce-shipment','id'=>$model->id ,'returnUrl'=>Url::getReturnUrl()], '生产出厂', [
                                    'class'=>'btn btn-success btn-sm',
                                    'style'=>"margin-left:5px",
                                    'data-toggle' => 'modal',
                                    'data-target' => '#ajaxModalLg',
                                ]);
                                break;

                            default:
                                $buttonHtml .= '';
                        }
                        return $buttonHtml;
                    },                    
                ]
            ]
        ]
      ]);
    ?>
            </div>
        </div>
    </div>
</div>

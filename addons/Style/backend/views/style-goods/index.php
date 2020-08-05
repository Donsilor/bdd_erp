<?php

use common\helpers\Html;
use common\helpers\Url;
use yii\grid\GridView;
use common\helpers\ImageHelper;
use addons\Style\common\enums\AttrIdEnum;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('goods', '商品列表');
$this->params['breadcrumbs'][] = $this->title;
$materialDropdownList = array_column(Yii::$app->attr->valueList(AttrIdEnum::MATERIAL),'name','id');
?>

<div class="row">
    <div class="col-sm-12">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title"><?= Html::encode($this->title) ?></h3>
                <div class="box-tools">
<!--                    --><?//= Html::create(['edit-lang']) ?>
                </div>
            </div>
            <div class="box-body table-responsive">
    <?php //echo Html::batchButtons(true)?>      
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
                'headerOptions' => ['width'=>'100'],
            ],
            [
                'label' =>'商品图片',    
                'attribute' => 'style.style_image',
                'value' => function ($model) {
                    return ImageHelper::fancyBox($model->style->style_image);
                },
                'filter' => false,
                'format' => 'raw',
                'headerOptions' => ['width'=>'80'],
            ],
            [
                    'attribute' => 'goods_sn',
                    'filter' => true,
                    'value' => function($model){
                        return Html::a($model->goods_sn, ['view', 'id' => $model->id,'returnUrl'=>Url::getReturnUrl()], ['style'=>"text-decoration:underline;color:#3c8dbc"]);
                    },
                    'format' => 'raw',
                    'headerOptions' => ['width'=>'200'],
            ],
            [
                    'attribute'=>'style.style_name',
                    'filter' => Html::activeTextInput($searchModel, 'style.style_name', [
                            'class' => 'form-control',
                    ]),
                    'value' => function ($model) {
                         return $model->style->style_name;
                    },
                    'headerOptions' => ['width'=>'300'],
            ], 
            [
                    'attribute'=>'style_sn',
                    'filter' => Html::activeTextInput($searchModel, 'style_sn', [
                            'class' => 'form-control',
                    ]),
                    'value' => function ($model) {
                          return $model->style_sn;
                    },
                    'headerOptions' => ['width'=>'120'],
            ],
            [
                    'label' => '款式分类',
                    'attribute' => 'style.style_cate_id',
                    'value' => "cate.name",
                    'filter' => Html::activeDropDownList($searchModel, 'style.style_cate_id',Yii::$app->styleService->styleCate->getDropDown(), [
                            'prompt' => '全部',
                            'class' => 'form-control',
                    ]),
                    'format' => 'raw',
                    'headerOptions' => ['class' => 'col-md-1'],
            ],
            [
                    'label' => '产品线',
                    'attribute' => 'style.product_type_id',
                    'value' => "type.name",
                    'filter' => Html::activeDropDownList($searchModel, 'style.product_type_id',Yii::$app->styleService->productType->getDropDown(), [
                            'prompt' => '全部',
                            'class' => 'form-control',
                    ]),
                    'format' => 'raw',
                    'headerOptions' => ['class' => 'col-md-1'],
            ],  
            [
                    'attribute' => 'material',
                    'value' => function ($model){
                        if($model->material){
                            return Yii::$app->attr->valueName($model->material);
                        }
                    },
                    'filter' => Html::activeDropDownList($searchModel, 'material',$materialDropdownList, [
                            'prompt' => '全部',
                            'class' => 'form-control',
                    ]),
                    'format' => 'raw',
                    'headerOptions' => ['class' => 'col-md-1'],
            ],
            [
                    'attribute' => 'finger',
                    'value' => 'finger',
                    'filter' => true,
                    'format' => 'raw',
                    'headerOptions' => ['width'=>'100'],
            ],
            [
                    'label' => '镶口',
                    'attribute' => 'xiangkou',
                    'value' => 'xiangkou',
                    'filter' => true,
                    'format' => 'raw',
                    'headerOptions' => ['width'=>'100'],
            ],
            [
                    'attribute'=>'cost_price',
                    'filter' => Html::activeTextInput($searchModel, 'cost_price', [
                            'class' => 'form-control',
                    ]),
                    'value' => function ($model) {
                        return $model->cost_price ;
                    },
                    'headerOptions' => ['width'=>'120'],
            ],
            [
                'attribute' => 'status',                
                'value' => function ($model){
                    return \common\enums\StatusEnum::getValue($model->status);
                },
                'filter' => Html::activeDropDownList($searchModel, 'status',\common\enums\StatusEnum::getMap(), [
                    'prompt' => '全部',
                    'class' => 'form-control',
                ]),
                'format' => 'raw',
                'headerOptions' => ['width' => '100'],
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => '操作',
                'template' => '{edit}',
                'buttons' => [
                    'edit' => function($url, $model, $key){
                        return Html::edit(['edit-all','style_id' => $model->style_id,'returnUrl' => Url::getReturnUrl()]);
                    },
                    'status' => function($url, $model, $key){
                            return Html::status($model['status']);
                    },
                ]
           ]
      ]
    ]); ?>
            </div>
        </div>
    </div>
</div>

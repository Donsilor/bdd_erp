<?php

use common\helpers\Html;
use common\helpers\Url;
use yii\grid\GridView;
use common\helpers\ImageHelper;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('goods', '款式商品管理');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-sm-12">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title"><?= Html::encode($this->title) ?></h3>
                <div class="box-tools">
                    <?= Html::create(['edit-lang']) ?>
                </div>
            </div>
            <div class="box-body table-responsive">
    <?php echo Html::batchButtons(true)?> <br/><br/>        
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
                'attribute' => 'goods_image',
                'value' => function ($model) {
                    return ImageHelper::fancyBox($model->goods_image);
                },
                'filter' => false,
                'format' => 'raw',
                'headerOptions' => ['width'=>'80'],
            ],
            [
                    'attribute' => 'goods_sn',
                    'filter' => true,
                    'format' => 'raw',
                    'headerOptions' => ['width'=>'150'],
            ],
            [
                    'attribute'=>'goods_name',
                    'filter' => Html::activeTextInput($searchModel, 'goods_name', [
                            'class' => 'form-control',
                    ]),
                    'value' => function ($model) {
                        return $model->goods_name;
                    },
                    'headerOptions' => ['width'=>'300'],
            ],  
            [
                    'label' => '款式分类',
                    'attribute' => 'style_cate_id',
                    'value' => "cate.name",
                    'filter' => Html::activeDropDownList($searchModel, 'style_cate_id',Yii::$app->styleService->styleCate->getGrpDropDown(), [
                            'prompt' => '全部',
                            'class' => 'form-control',
                    ]),
                    'format' => 'raw',
                    'headerOptions' => ['class' => 'col-md-1'],
            ],
            [
                    'label' => '产品线',
                    'attribute' => 'product_type_id',
                    'value' => "type.name",
                    'filter' => Html::activeDropDownList($searchModel, 'product_type_id',Yii::$app->styleService->productType->getGrpDropDown(), [
                            'prompt' => '全部',
                            'class' => 'form-control',
                    ]),
                    'format' => 'raw',
                    'headerOptions' => ['class' => 'col-md-1'],
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
                    'attribute'=>'销售价',
                    'filter' => Html::activeTextInput($searchModel, 'sale_price', [
                            'class' => 'form-control',
                    ]),
                    'value' => function ($model) {
                        return $model->sale_price ;
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
                'template' => '{status}',
                'buttons' => [
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

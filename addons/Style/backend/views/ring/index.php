<?php

use common\helpers\Html;
use common\helpers\Url;
use yii\grid\GridView;
use common\helpers\ImageHelper;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('goods_ring', '对戒');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title"><?= Html::encode($this->title) ?></h3>
                <div class="box-tools">
                    <?= Html::create(['edit-lang']) ?>
                </div>
            </div>
            <div class="box-body table-responsive">
    <?php echo Html::batchButtons(false)?>         
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => ['class' => 'table table-hover'],
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
                'value' => 'id',
                'filter' => Html::activeTextInput($searchModel, 'id', [
                    'class' => 'form-control',
                ]),
                'format' => 'raw',
                'headerOptions' => ['width'=>'80'], 
            ],
            [                 
                'attribute' => 'ring_images',
                'value' => function ($model) {
                    if(!empty($model->ring_images)){
                        $ring_images = explode(',', $model->ring_images);
                        $ring_image = $ring_images[0];
                    }else{
                        $ring_image = '';
                    }
                    return ImageHelper::fancyBox($ring_image);
                },
                'filter' => false,
                'format' => 'raw',
                'headerOptions' => ['width'=>'80'],
            ],
            [
                'attribute' => 'lang.ring_name',
                'value' => 'lang.ring_name',
                'filter' => Html::activeTextInput($searchModel, 'ring_name', [
                    'class' => 'form-control',
                ]),
                'format' => 'raw',
            ],

            [
                'headerOptions' => ['class' => 'col-md-1'],
                'attribute' => 'ring_sn',
                'filter' => Html::activeTextInput($searchModel, 'ring_sn', [
                    'class' => 'form-control',
                 ]),
                'format' => 'raw',
            ],
            [
                'headerOptions' => ['class' => 'col-md-1'],
                'attribute' => '产品线',
                'value'=>function($model){
                    return '对戒';
                }
            ],
            [
                'headerOptions' => ['class' => 'col-md-1'],
                'attribute' => 'sale_price',
                'filter' => Html::activeTextInput($searchModel, 'sale_price', [
                    'class' => 'form-control',
                ]),
                'format' => 'raw',
            ],

            [
                'attribute'  => '库存',
                'value' => function($model){
                    return Yii::$app->services->goodsStyle->getRingStorage($model->id);
                }
            ],


            //'ring_image',
            //'qr_code',
            //'ring_salenum',
            //'ring_style',
            //'sale_price',
            [
                'attribute' => 'status',
                'format' => 'raw',
                'headerOptions' => ['class' => 'col-md-1'],
                'value' => function ($model){
                    return \common\enums\FrameEnum::getValue($model->status);
                },
                'filter' => Html::activeDropDownList($searchModel, 'status',\common\enums\FrameEnum::getMap(), [
                    'prompt' => '全部',
                    'class' => 'form-control',

                ]),
            ],



            //'updated_at',
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => '操作',
                'template' => '{edit} {status} {view}',
                'buttons' => [
                'edit' => function($url, $model, $key){
                        return Html::edit(['edit-lang', 'id' => $model->id,'returnUrl' => Url::getReturnUrl()]);
                },
               'status' => function($url, $model, $key){
                        return Html::status($model['status']);
                  },
                'delete' => function($url, $model, $key){
                        return Html::delete(['delete', 'id' => $model->id]);
                },
                'view'=> function($url, $model, $key){
                    return Html::a('预览', \Yii::$app->params['frontBaseUrl'].'/ring/wedding-rings/'.$model->id.'?goodId='.$model->id.'&ringType=pair&backend=1',['class'=>'btn btn-info btn-sm','target'=>'_blank']);
                },
                ]
            ]
    ]
    ]); ?>
            </div>
        </div>
    </div>
</div>

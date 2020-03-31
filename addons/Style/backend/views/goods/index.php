<?php

use common\helpers\Html;
use common\helpers\Url;
use yii\grid\GridView;
use common\helpers\ImageHelper;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('goods', $typeModel['type_name'].'商品列表');
$style_title = Yii::t('goods', $typeModel['type_name'].'管理');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-sm-12">
        <div class="nav-tabs-custom">

            <ul class="nav nav-tabs">
                <li><a href="<?= Url::to(['style/index?type_id='.Yii::$app->request->get('type_id',0)]) ?>"> <?= Html::encode($style_title) ?></a></li>
                <li class="active"><a href="<?= Url::to(['goods/index?type_id='.Yii::$app->request->get('type_id',0)]) ?>"> <?= Html::encode($this->title) ?></a></li>
                <li class="pull-right">
                    <div class="box-header box-tools"> <?= Html::create(['style/edit-lang','type_id'=>Yii::$app->request->get('type_id',0)]) ?></div>
                </li>
            </ul>
            <div class="box-body table-responsive">
    <?php echo Html::batchButtons(false)?>         
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => ['class' => 'table table-hover'],
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn',
                'visible' => false,
            ],

            [
                'attribute' => 'id',
                'filter' => true,
                'format' => 'raw',
                'headerOptions' => ['width'=>'100'],
            ],
            //'style_id',
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
                'attribute'=>'styleLang.style_name',
                'filter' => Html::activeTextInput($searchModel, 'styleLang.style_name', [
                    'class' => 'form-control',
                ]),
                'value' => function ($model) {
                    return $model->styleLang['style_name'];
                },
                'headerOptions' => ['width'=>'200'],
            ],
            'goods_sn',
            [
                'attribute'=>'style.style_sn',
                'filter' => Html::activeTextInput($searchModel, 'style.style_sn', [
                    'class' => 'form-control',
                ]),
                'value' => function ($model) {
                    return $model->style['style_sn'];
                },
                'headerOptions' => ['width'=>'100'],
            ],
            [
                'attribute'=>'销售地区',
                'filter' => Html::activeDropDownList($searchModel, 'markup.area_id',\common\enums\AreaEnum::getMap(), [
                    'prompt' => '全部',
                    'class' => 'form-control',
                ]),
                'value' => function ($model) {
                return \common\enums\AreaEnum::getValue($model->markup['area_id']);
                },
                'headerOptions' => ['width'=>'100'],
            ],
            [
                'attribute'=>'地区销售价',
                'filter' => Html::activeTextInput($searchModel, 'markup.sale_price', [
                    'class' => 'form-control',
                ]),
                'value' => function ($model) {
                    return $model->markup->sale_price;
                    //return $model->markup['sale_price'] .'/'.$model->sale_price;
                },
                'headerOptions' => ['width'=>'100'],
            ],

            [
                'attribute'=>'基础价格',
                'filter' =>false,
                'value' => function ($model) {
                    //return $model->markup->styleMarkup['sale_price'] .'/'.$model->style->sale_price;
                    return $model->sale_price ;
                },
                'headerOptions' => ['width'=>'100'],
            ],
            [
                'attribute'=>'markup.styleMarkup.markup_rate',
                'filter' => false,
                'value' => function ($model) {
                    return $model->markup->styleMarkup['markup_rate'];
                },
                'headerOptions' => ['width'=>'100'],
            ],

            [
                'attribute'=>'markup.styleMarkup.markup_value',
                'filter' => false,
                'value' => function ($model) {
                    return $model->markup->styleMarkup['markup_value'];
                },
                'headerOptions' => ['width'=>'100'],
            ],

            [
                'attribute'=>'地区状态',
                'filter' => Html::activeDropDownList($searchModel, 'markup.status',\common\enums\StatusEnum::getMap(), [
                    'prompt' => '全部',
                    'class' => 'form-control',
                ]),
                'value' => function ($model) {
                    return \common\enums\StatusEnum::getValue($model->markup['status']);
                },
                'headerOptions' => ['width'=>'100'],
            ],

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

            [
                'class' => 'yii\grid\ActionColumn',
                'header' => '操作',
                'template' => '{edit} {view}',
                'buttons' => [
                'edit' => function($url, $model, $key){
                        return Html::edit(['style/edit-lang', 'id' => $model->style_id,'type_id'=>$model->style_id,'returnUrl' => Url::getReturnUrl()]);
                },
               /* 'status' => function($url, $model, $key){
                        return Html::status($model['status']);
                  }, */
                'delete' => function($url, $model, $key){
                        return Html::delete(['delete', 'id' => $model->id]);
                },
                    'view'=> function($url, $model, $key){
                        if($model->type_id == 2){
                            return Html::a('预览', \Yii::$app->params['frontBaseUrl'].'/ring/wedding-rings/'.$model->style_id.'?goodId='.$model->style_id.'&ringType=single&backend=1',['class'=>'btn btn-info btn-sm','target'=>'_blank']);
                        }elseif ($model->type_id == 12){
                            return Html::a('预览', \Yii::$app->params['frontBaseUrl'].'/ring/engagement-rings/'.$model->style_id.'?goodId='.$model->style_id.'&ringType=engagement&backend=1',['class'=>'btn btn-info btn-sm','target'=>'_blank']);
                        }elseif ($model->type_id == 4){
                            return Html::a('预览', \Yii::$app->params['frontBaseUrl'].'/jewellery/necklace/'.$model->style_id.'?goodId='.$model->style_id.'&backend=1',['class'=>'btn btn-info btn-sm','target'=>'_blank']);
                        }elseif ($model->type_id == 5){
                            return Html::a('预览', \Yii::$app->params['frontBaseUrl'].'/jewellery/pendant/'.$model->style_id.'?goodId='.$model->style_id.'&backend=1',['class'=>'btn btn-info btn-sm','target'=>'_blank']);
                        }elseif ($model->type_id == 6){
                            return Html::a('预览', \Yii::$app->params['frontBaseUrl'].'/jewellery/studEarring/'.$model->style_id.'?goodId='.$model->style_id.'&backend=1',['class'=>'btn btn-info btn-sm','target'=>'_blank']);
                        }elseif ($model->type_id == 7){
                            return Html::a('预览', \Yii::$app->params['frontBaseUrl'].'/jewellery/earring/'.$model->style_id.'?goodId='.$model->style_id.'&backend=1',['class'=>'btn btn-info btn-sm','target'=>'_blank']);
                        }elseif ($model->type_id == 8){
                            return Html::a('预览', \Yii::$app->params['frontBaseUrl'].'/jewellery/braceletLine/'.$model->style_id.'?goodId='.$model->style_id.'&backend=1',['class'=>'btn btn-info btn-sm','target'=>'_blank']);
                        }elseif ($model->type_id == 9){
                            return Html::a('预览', \Yii::$app->params['frontBaseUrl'].'/jewellery/bracelet/'.$model->style_id.'?goodId='.$model->style_id.'&backend=1',['class'=>'btn btn-info btn-sm','target'=>'_blank']);
                        }

                    }
                ]
            ]
    ]
    ]); ?>
            </div>
        </div>
    </div>
</div>

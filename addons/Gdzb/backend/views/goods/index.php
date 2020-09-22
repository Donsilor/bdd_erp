<?php

use yii\grid\GridView;
use common\helpers\Html;

$this->title = '商品列表';
$this->params['breadcrumbs'][] = ['label' => $this->title];

?>

<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title"><?= Html::encode($this->title) ?></h3>
                <div class="box-tools">
                </div>
            </div>
            <div class="box-body table-responsive">
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'tableOptions' => ['class' => 'table table-hover'],
                    'options' => ['id' => 'order-goods', 'style' => ' width:100%;white-space:nowrap;'],
                    'columns' => [
                        [
                            'class' => 'yii\grid\SerialColumn',
                            'visible' => false,
                        ],
                        [
                            'class' => 'yii\grid\CheckboxColumn',
                            'name' => 'id',  //设置每行数据的复选框属性
                            'headerOptions' => ['width' => '30'],
                        ],
                        'id',
                        [
                            'attribute' => 'goods_sn',
                            'value' => 'goods_sn',
                            'headerOptions' => ['class' => 'col-md-1'],
                        ],
                        [
                            'attribute' => 'goods_image',
                            'value' => function ($model) {
                                $goods_image = $model->goods_image ? explode(',', $model->goods_image) : [];
                                $goods_image = $goods_image ? $goods_image[0] : '';
                                return common\helpers\ImageHelper::fancyBox($goods_image);
                            },
                            'filter' => false,
                            'format' => 'raw',
                            'headerOptions' => ['width' => '80'],
                        ],

                        [
                            'attribute' => 'goods_name',
                            'value' => function ($model) {
                                return "<div style='width:200px;white-space:pre-wrap;'>" . $model->goods_name . "</div>";
                            },
                            'format' => 'raw',
                        ],

                        [
                            'attribute' => 'style_cate_id',
                            'value' => function ($model) {
                                return $model->cate->name ?? '';
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'style_cate_id',Yii::$app->styleService->styleCate->getDropDown(), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style'=> 'width:120px;'
                            ]),
                            'format' => 'raw',
                        ],
                        [
                            'attribute' => 'product_type_id',
                            'value' => function ($model) {
                                return $model->type->name ?? '';
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'product_type_id',Yii::$app->styleService->productType->getDropDown(), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style'=> 'width:120px;'
                            ]),
                            'format' => 'raw',
                        ],
                        [
                            'attribute' => 'goods_status',
                            'value' => function ($model) {
                                return \addons\Warehouse\common\enums\GoodsStatusEnum::getValue($model->goods_status);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'goods_status',\addons\Warehouse\common\enums\GoodsStatusEnum::getMap(), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style' =>'width:80px'
                            ]),
                            'format' => 'raw',
                        ],
                        [
                            'attribute' => 'warehouse_id',
                            'value' => function ($model) {
                                return $model->warehouse->name ?? '';
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'warehouse_id',Yii::$app->warehouseService->warehouse->getDropDown(), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style'=> 'width:120px;'
                            ]),
                            'format' => 'raw',
                        ],
                        'goods_size',
                        [
                            'attribute' => 'goods_price',
                            'value' => function ($model) {
                                return $model->goods_price;
                            }
                        ],
                    ]
                ]); ?>
            </div>
        </div>
    </div>
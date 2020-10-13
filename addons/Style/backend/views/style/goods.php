<?php

use yii\grid\GridView;
use common\helpers\Url;
use common\helpers\Html;
use addons\Style\common\enums\AttrIdEnum;
use kartik\daterange\DateRangePicker;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('style_channel', '款式库存');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box-body nav-tabs-custom">
    <h2 class="page-header"><?= $this->title ?> - <?php echo $style->style_sn?></h2>
    <?= Html::menuTab($tabList, $tab)?>
    <div class="tab-content">
        <div class="row col-xs-12">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title"><?= Html::encode($this->title) ?></h3>
                </div>
                <div class="box-body table-responsive">
                    <?php echo Html::batchButtons(false)?>
                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'tableOptions' => ['class' => 'table table-hover'],
                        'options' => ['style'=>'white-space:nowrap;' ],
                        'showFooter' => false,//显示footer行
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
                                'attribute' => 'goods_id',
                                'value'=>function($model) {
                                    if(preg_match("/^9/is", $model->goods_id)){
                                        $model->goods_id = Yii::$app->warehouseService->warehouseGoods->createGoodsId($model);
                                    }
                                    return Html::a($model->goods_id, ['../warehouse/warehouse-goods/view', 'id' => $model->id,'returnUrl'=>Url::getReturnUrl()], ['class'=>'openContab','style'=>"text-decoration:underline;color:#3c8dbc",'id'=>"goods_".$model->goods_id]).' <i class="fa fa-copy" onclick="copy(\''. "goods_".$model->goods_id .'\')"></i>';
                                },
                                'filter' => Html::activeTextInput($searchModel, 'goods_id', [
                                    'class' => 'form-control',
                                    'style'=> 'width:120px;'
                                ]),
                                'format' => 'raw',
                            ],
                            'style_sn',
                            [
                                'attribute' => 'style_cate_id',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1'],
                                'value' => 'styleCate.name',
                                'filter' => Html::activeDropDownList($searchModel, 'style_cate_id',Yii::$app->styleService->styleCate::getDropDown(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'

                                ]),
                            ],
                            [
                                'attribute' => 'product_type_id',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1'],
                                'value' => function($model){
                                    return $model->productType->name ?? '';
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'product_type_id',Yii::$app->styleService->productType::getDropDown(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'

                                ]),
                            ],
                            [
                                'attribute'=>'goods_name',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    return  $model->goods_name;
                                },
                                'filter' => Html::activeTextInput($searchModel, 'goods_name', [
                                    'class' => 'form-control',
                                    'style'=> 'width:200px;'
                                ]),
                                'headerOptions' => [],
                            ],
                            [
                                'attribute' => 'goods_status',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1'],
                                'value' => function ($model){
                                    return \addons\Warehouse\common\enums\GoodsStatusEnum::getValue($model->goods_status);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'goods_status',\addons\Warehouse\common\enums\GoodsStatusEnum::getMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'

                                ]),
                            ],
                            [
                                'attribute' => 'jintuo_type',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1'],
                                'value' => function ($model){
                                    return \addons\Style\common\enums\JintuoTypeEnum::getValue($model->jintuo_type);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'jintuo_type',\addons\Style\common\enums\JintuoTypeEnum::getMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                            ],

                            [
                                'attribute' => 'material_type',
                                'value' => function($model){
                                    return Yii::$app->attr->valueName($model->material_type);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'material_type',Yii::$app->attr->valueMap(AttrIdEnum::MATERIAL_TYPE), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                                'headerOptions' => [],
                            ],
                            [
                                'attribute' => 'material_color',
                                'value' => function($model){
                                    return Yii::$app->attr->valueName($model->material_color);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'material_color',Yii::$app->attr->valueMap(AttrIdEnum::MATERIAL_COLOR), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                                'headerOptions' => [],
                            ],
                            [
                                'attribute'=>'stock_num',
                                'filter' => false,
                                'headerOptions' => [],
                            ],
                            [
                                'attribute'=>'goods_num',
                                'filter' => false,
                                'headerOptions' => [],
                            ],
                            [
                                'label' => '手寸',
                                'attribute' => 'finger',
                                'headerOptions' => [],
                                'value' => function ($model) {
                                    $finger = "";
                                    if ($model->finger) {
                                        $finger .= Yii::$app->attr->valueName($model->finger) . "(美号)" ?? "";
                                    }
                                    if ($model->finger_hk) {
                                        $finger .= Yii::$app->attr->valueName($model->finger_hk) . "(港号)" ?? "";
                                    }
                                    return $finger ?? "";
                                },
                                'filter' => false,
                            ],
                            [
                                'label' => '入库时间',
                                'attribute'=>'created_at',
                                'filter' => DateRangePicker::widget([    // 日期组件
                                    'model' => $searchModel,
                                    'attribute' => 'created_at',
                                    'value' => $searchModel->created_at,
                                    'options' => ['readonly' => false,'class'=>'form-control','style'=>'background-color:#fff;width:150px;'],
                                    'pluginOptions' => [
                                        'format' => 'yyyy-mm-dd',
                                        'locale' => [
                                            'separator' => '/',
                                        ],
                                        'endDate' => date('Y-m-d',time()),
                                        'todayHighlight' => true,
                                        'autoclose' => true,
                                        'todayBtn' => 'linked',
                                        'clearBtn' => true,
                                    ],
                                ]),
                                'value'=>function($model){
                                    return Yii::$app->formatter->asDatetime($model->created_at);
                                }
                            ],
                        ]
                    ]); ?>
                </div>
            </div>
           <!-- box end --> 
        </div>
    </div>
</div>
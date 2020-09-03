<?php

use yii\grid\GridView;
use yii\widgets\ActiveForm;
use common\helpers\Html;
use common\helpers\Url;

use addons\Style\common\enums\AttrIdEnum;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

//$this->title = Yii::t('goods', 'flows');
//$this->params['breadcrumbs'][] = $this->title;
?>
<!--<div class="row">-->
<!--    <div class="col-xs-12">-->
<!--        <div class="box">-->
<!--            <div class="box-body table-responsive">-->
<!--                <div class="col-xs-1">-->
<!--                    --><?//= $searchModel->model->getAttributeLabel('style_sn') ?><!--:-->
<!--                </div>-->
<!--                <div class="col-xs-3">-->
<!--                    --><?//= Html::activeTextInput($searchModel, 'style_sn', [
//                        'class' => 'form-control',
//                    ]);
//                    ?>
<!--                </div>-->
<!--                <div class="col-xs-1">-->
<!--                    --><?//= Html::button('查询',['class'=>'btn btn-info btn-sm','onclick'=>"searchGift()"]) ?>
<!--                </div>-->
<!--            </div>-->
<!--        </div>-->
<!--    </div>-->
<!--</div>-->

<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-body table-responsive">
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'tableOptions' => ['class' => 'table table-hover'],
                    'options' => ['style'=>'white-space:nowrap;width:120%'],
                    'showFooter' => false,//显示footer行
                    'id'=>'grid',
                    'columns' => [
                        [
                            'class' => 'yii\grid\SerialColumn',
                            'visible' => false,
                        ],
                        [
                            'class'=>'yii\grid\RadioButtonColumn',
                            'name'=>'id',  //设置每行数据的复选框属性
                            'headerOptions' => ['width'=>'30'],
                        ],
                        [
                            'attribute'=>'gift_sn',
                            'format' => 'raw',
                            'value'=>function($model) {
                                return Html::a($model->gift_sn, ['view', 'id' => $model->id,'returnUrl'=>Url::getReturnUrl()], ['style'=>"text-decoration:underline;color:#3c8dbc"]);
                            },
                            'filter' => Html::activeTextInput($searchModel, 'gift_sn', [
                                'class' => 'form-control',
                            ]),
                            'headerOptions' => ['class' => 'col-md-1']
                        ],
                        [
                            'attribute'=>'gift_name',
                            'filter' => Html::activeTextInput($searchModel, 'gift_name', [
                                'class' => 'form-control',
                            ]),
                            'headerOptions' => ['class' => 'col-md-1']
                        ],
                        [
                            'label' => '商品图片',
                            'value' => function ($model) {
                                return \common\helpers\ImageHelper::fancyBox(Yii::$app->warehouseService->gift->getStyleImage($model),90,90);
                            },
                            'filter' => false,
                            'format' => 'raw',
                            'headerOptions' => ['class' => 'col-md-1']
                        ],
                        [
                            'attribute'=>'style_sn',
                            'filter' => Html::activeTextInput($searchModel, 'style_sn', [
                                'class' => 'form-control',
                            ]),
                            'value' => function ($model) {
                                $str = $model->style_sn;
                                return $str;
                            },
                            'format' => 'raw',
                            'headerOptions' => ['class' => 'col-md-1']
                        ],
                        [
                            'attribute' => 'style_cate_id',
                            'value' => function ($model){
                                return $model->cate->name ??'';
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'style_cate_id',Yii::$app->styleService->styleCate->getDropDown(), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style'=>'width:100px'
                            ]),
                            'format' => 'raw',
                            'headerOptions' => ['class' => 'col-md-1'],
                        ],
                        [
                            'attribute' => 'product_type_id',
                            'value' => function($model){
                                return $model->type->name ?? '';
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'product_type_id',Yii::$app->styleService->productType->getDropDown(), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style'=>'width:100px'
                            ]),
                            'format' => 'raw',
                            'headerOptions' => ['class' => 'col-md-1'],
                        ],
                        [
                            'attribute' => 'style_sex',
                            'format' => 'raw',
                            'value' => function ($model){
                                return \addons\Style\common\enums\StyleSexEnum::getValue($model->style_sex);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'style_sex',\addons\Style\common\enums\StyleSexEnum::getMap(), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style'=> 'width:80px;'
                            ]),
                            'headerOptions' => ['class' => 'col-md-1'],
                        ],
                        [
                            'attribute' => 'material_type',
                            'value' => function ($model){
                                return Yii::$app->attr->valueName($model->material_type)??"";
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'material_type',Yii::$app->attr->valueMap(AttrIdEnum::MATERIAL_TYPE), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                            ]),
                            'headerOptions' => ['class' => 'col-md-1'],
                        ],
                        [
                            'attribute' => 'material_color',
                            'value' => function ($model){
                                return Yii::$app->attr->valueName($model->material_color)??"";
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'material_color',Yii::$app->attr->valueMap(AttrIdEnum::MATERIAL_COLOR), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                            ]),
                            'headerOptions' => ['class' => 'col-md-1']
                        ],
                        [
                            'attribute' => 'finger_hk',
                            'value' => function($model){
                                return Yii::$app->attr->valueName($model->finger_hk);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'finger_hk',Yii::$app->attr->valueMap(AttrIdEnum::PORT_NO), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style'=> 'width:80px;'
                            ]),
                            'headerOptions' => ['class' => 'col-md-1']
                        ],
                        [
                            'attribute' => 'finger',
                            'value' => function($model){
                                return Yii::$app->attr->valueName($model->finger);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'finger',Yii::$app->attr->valueMap(AttrIdEnum::FINGER), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style'=> 'width:80px;'
                            ]),
                            'headerOptions' => ['class' => 'col-md-1']
                        ],
                        [
                            'attribute'=>'chain_length',
                            'format' => 'raw',
                            'value' => function ($model) {
                                return $model->chain_length ?? '';
                            },
                            'filter' => Html::activeTextInput($searchModel, 'chain_length', [
                                'class' => 'form-control',
                            ]),
                            'headerOptions' => ['class' => 'col-md-1'],
                        ],
                        [
                            'attribute' => 'main_stone_type',
                            'value' => function($model){
                                return Yii::$app->attr->valueName($model->main_stone_type);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'main_stone_type',Yii::$app->attr->valueMap(\addons\Style\common\enums\AttrIdEnum::MAIN_STONE_TYPE), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style'=> 'width:80px;'
                            ]),
                            'headerOptions' => ['class' => 'col-md-1']
                        ],


                        [
                            'attribute'=>'gift_num',
                            'filter' => Html::activeTextInput($searchModel, 'gift_num', [
                                'class' => 'form-control',
                            ]),
                            'headerOptions' => ['class' => 'col-md-1']
                        ],
                        [
                            'attribute'=>'cost_price',
                            'filter' => Html::activeTextInput($searchModel, 'cost_price', [
                                'class' => 'form-control',
                            ]),
                            'headerOptions' => ['class' => 'col-md-1']
                        ],
                        [
                            'attribute'=>'sale_price',
                            'filter' => Html::activeTextInput($searchModel, 'sale_price', [
                                'class' => 'form-control',
                            ]),
                            'headerOptions' => ['class' => 'col-md-1']
                        ],

                    ]
                ]); ?>
            </div>

        </div>
        <?php $form = ActiveForm::begin([
            'id' => $model->formName(),
            'enableAjaxValidation' => false,
            'validationUrl' => Url::to(['select-gift', 'order_id' => $order_id]),
        ]); ?>
        <input type="hidden" name="gift_id" id="gift_id">
        <?php ActiveForm::end(); ?>
    </div>
</div>
<script type="text/javascript">
    function searchGift(){
        let val = $("#searchmodel-style_sn").val();
        let name = $("#searchmodel-style_sn").attr('name');
        $(".filters input[name='" + name + "']").val(val).trigger('change');
    }

    $('input[name="id"]').change(function() {
        if($(this).prop("checked")) {
            var id = $(this).val();
            $("#gift_id").val(id)
        }
    });
</script>
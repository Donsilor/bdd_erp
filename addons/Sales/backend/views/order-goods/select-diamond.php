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
                            'attribute' => 'goods_image',
                            'value' => function ($model) {
                                return \common\helpers\ImageHelper::fancyBox($model->goods_image);
                            },
                            'filter' => false,
                            'format' => 'raw',
                            'headerOptions' => ['class' => 'col-md-1'],
                        ],
                        [
                            'attribute' => 'goods_name',
                            'value' => function($model){
                                return Html::a($model->goods_name,['view','id' => $model->id,'returnUrl'=>Url::getReturnUrl()] ,['style'=>"text-decoration:underline;color:#3c8dbc"]);
                            },
                            'filter' => Html::activeTextInput($searchModel, 'goods_name', [
                                'class' => 'form-control',
                            ]),
                            'format' => 'raw',
                            'headerOptions' => ['class' => 'col-md-2'],
                        ],
                        [
                            'attribute' => 'goods_sn',
                            'value' => 'goods_sn',
                            'filter' => Html::activeTextInput($searchModel, 'goods_sn', [
                                'class' => 'form-control',
                            ]),
                            'format' => 'raw',
                            'headerOptions' => ['class' => 'col-md-1'],
                        ],
                        [
                            'attribute' => 'cert_type',
                            'format' => 'raw',
                            'headerOptions' => ['class' => 'col-md-1'],
                            'value' => function ($model){
                                return Yii::$app->attr->valueName($model->cert_type);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'cert_type',Yii::$app->attr->valueMap(AttrIdEnum::DIA_CERT_TYPE), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                            ]),
                        ],
                        [
                            'attribute' => 'cert_id',
                            'value' => 'cert_id',
                            'filter' => Html::activeTextInput($searchModel, 'cert_id', [
                                'class' => 'form-control',
                                'style' =>'width:100px'
                            ]),
                            'format' => 'raw',
                            'headerOptions' => ['class' => 'col-md-1'],
                        ],

                        [
                            'attribute' => 'sale_price',
                            'filter' => true,
                            'format' => 'raw',
                            'headerOptions' => ['class' => 'col-md-1'],
                        ],
                        //'cost_price',
                        [
                            'attribute' => 'carat',
                            'filter' => true,
                            'headerOptions' => ['class' => 'col-md-1'],
                            'format' => 'raw',
                        ],
                        [
                            'attribute' => 'shape',
                            'format' => 'raw',
                            'headerOptions' => ['class' => 'col-md-1','style'=>'min-width:80px;'],
                            'value' => function ($model){
                                return Yii::$app->attr->valueName($model->shape);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'shape',Yii::$app->attr->valueMap(AttrIdEnum::DIA_SHAPE), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                            ]),
                        ],
                        [
                            'attribute' => 'color',
                            'format' => 'raw',
                            'headerOptions' => ['class' => 'col-md-1','style'=>'min-width:80px;'],
                            'value' => function ($model){
                                return Yii::$app->attr->valueName($model->color);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'color',Yii::$app->attr->valueMap(AttrIdEnum::DIA_COLOR), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                            ]),
                        ],
                        [
                            'attribute' => 'clarity',
                            'headerOptions' => ['class' => 'col-md-1','style'=>'min-width:80px;'],
                            'value' => function ($model){
                                return Yii::$app->attr->valueName($model->clarity);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'clarity',Yii::$app->attr->valueMap(AttrIdEnum::DIA_CLARITY), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                            ]),
                        ],
                        [
                            'attribute' => 'cut',
                            'headerOptions' => ['class' => 'col-md-1','style'=>'min-width:80px;'],
                            'value' => function ($model){
                                return Yii::$app->attr->valueName($model->cut);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'cut',Yii::$app->attr->valueMap(AttrIdEnum::DIA_CUT), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                            ]),
                        ],
                        [
                            'attribute' => 'polish',
                            'format' => 'raw',
                            'headerOptions' => ['class' => 'col-md-1','style'=>'min-width:80px;'],
                            'value' => function ($model){
                                return Yii::$app->attr->valueName($model->polish);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'polish',Yii::$app->attr->valueMap(AttrIdEnum::DIA_POLISH), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                            ]),
                        ],
                        [
                            'attribute' => 'fluorescence',
                            'format' => 'raw',
                            'headerOptions' => ['class' => 'col-md-1','style'=>'min-width:80px;'],
                            'value' => function ($model){
                                return Yii::$app->attr->valueName($model->fluorescence);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'fluorescence',Yii::$app->attr->valueMap(AttrIdEnum::DIA_FLUORESCENCE), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                            ]),
                        ],
                        [
                            'attribute' => 'symmetry',
                            'format' => 'raw',
                            'headerOptions' => ['class' => 'col-md-1','style'=>'min-width:80px;'],
                            'value' => function ($model){
                                return Yii::$app->attr->valueName($model->symmetry);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'symmetry',Yii::$app->attr->valueMap(AttrIdEnum::DIA_SYMMETRY), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                            ]),
                        ],
                        [
                            'attribute' => 'is_stock',
                            'format' => 'raw',
                            'headerOptions' => ['class' => 'col-md-1'],
                            'value' => function ($model){
                                return \addons\Sales\common\enums\IsStockEnum::getValue($model->is_stock);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'is_stock',\addons\Sales\common\enums\IsStockEnum::getMap(), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                            ]),
                        ],

                    ]
                ]); ?>
            </div>

        </div>
        <?php $form = ActiveForm::begin([
            'id' => $model->formName(),
            'enableAjaxValidation' => false,
            'validationUrl' => Url::to(['select-diamond', 'order_id' => $order_id]),
        ]); ?>
        <input type="hidden" name="diamon_id" id="diamon_id">
        <?php ActiveForm::end(); ?>
    </div>
</div>
<script type="text/javascript">
    $('input[name="id"]').change(function() {
        if($(this).prop("checked")) {
            var id = $(this).val();
            $("#diamon_id").val(id)
        }
    });
</script>
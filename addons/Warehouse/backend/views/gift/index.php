<?php

use addons\Style\common\enums\AttrIdEnum;
use common\helpers\Html;
use common\helpers\Url;
use kartik\select2\Select2;
use yii\grid\GridView;
use kartik\daterange\DateRangePicker;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('gift', '赠品库存');
$this->params['breadcrumbs'][] = $this->title;
$params = Yii::$app->request->queryParams;
$params = $params ? "&".http_build_query($params) : '';
?>

<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title"><?= Html::encode($this->title) ?></h3>
                <div class="box-tools">
                    <?= Html::button('导出', [
                        'class'=>'btn btn-success btn-xs',
                        'onclick' => 'batchExport()',
                    ]);?>
                </div>
            </div>
            <div class="box-body table-responsive">
                <?php echo Html::batchButtons(false)?>
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'tableOptions' => ['class' => 'table table-hover'],
                    'options' => ['style'=>'white-space:nowrap;'],
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
                        /*[
                            'attribute' => 'id',
                            'filter' => true,
                            'format' => 'raw',
                            'headerOptions' => ['width'=>'100'],
                        ],*/
                        [
                            'attribute'=>'gift_sn',
                            'format' => 'raw',
                            'value'=>function($model) {
                                return Html::a($model->gift_sn, ['view', 'id' => $model->id,'returnUrl'=>Url::getReturnUrl()], ['style'=>"text-decoration:underline;color:#3c8dbc"]);
                            },
                            'filter' => Html::activeTextInput($searchModel, 'gift_sn', [
                                'class' => 'form-control',
                            ]),
                            'headerOptions' => ['width'=>'100'],
                        ],
                        [
                            'attribute'=>'gift_name',
                            'filter' => Html::activeTextInput($searchModel, 'gift_name', [
                                'class' => 'form-control',
                            ]),
                            'headerOptions' => ['width'=>'200'],
                        ],
                        [
                            'label' => '商品图片',
                            'value' => function ($model) {
                                return \common\helpers\ImageHelper::fancyBox(Yii::$app->warehouseService->gift->getStyleImage($model),90,90);
                            },
                            'filter' => false,
                            'format' => 'raw',
                            'headerOptions' => ['width'=>'90'],
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
                            'headerOptions' => ['width'=>'100'],
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
//                        [
//                            'attribute' => 'product_type_id',
//                            'value' => function($model){
//                                return $model->type->name ?? '';
//                            },
//                            'filter' => Html::activeDropDownList($searchModel, 'product_type_id',Yii::$app->styleService->productType->getDropDown(), [
//                                'prompt' => '全部',
//                                'class' => 'form-control',
//                                'style'=>'width:100px'
//                            ]),
//                            'format' => 'raw',
//                            'headerOptions' => ['class' => 'col-md-1'],
//                        ],
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
                            'headerOptions' => ['width'=>'100'],
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
                            'headerOptions' => ['width'=>'100'],
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
                            'headerOptions' => [],
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
                            'headerOptions' => [],
                        ],
//                        [
//                            'attribute'=>'chain_length',
//                            'format' => 'raw',
//                            'value' => function ($model) {
//                                return $model->chain_length ?? '';
//                            },
//                            'filter' => Html::activeTextInput($searchModel, 'chain_length', [
//                                'class' => 'form-control',
//                            ]),
//                            'headerOptions' => ['class' => 'col-md-1'],
//                        ],
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
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'main_stone_num',
                            'format' => 'raw',
                            'value' => function ($model) {
                                return $model->main_stone_num ?? '';
                            },
                            'filter' => false,
                            'headerOptions' => ['class' => 'col-md-1'],
                        ],
                        [
                            'attribute'=>'gift_size',
                            'filter' => Html::activeTextInput($searchModel, 'gift_size', [
                                'class' => 'form-control',
                            ]),
                            'value' => function ($model) {
                                $str = $model->gift_size;
                                return $str;
                            },
                            'format' => 'raw',
                            'headerOptions' => ['class' => 'col-md-1'],
                        ],
                        [
                            'attribute'=>'first_num',
                            'filter' => Html::activeTextInput($searchModel, 'first_num', [
                                'class' => 'form-control',
                            ]),
                            'headerOptions' => ['width'=>'100'],
                            'contentOptions' => ['style'=>'color:green'],
                        ],
                        [
                            'attribute'=>'gift_num',
                            'filter' => Html::activeTextInput($searchModel, 'gift_num', [
                                'class' => 'form-control',
                            ]),
                            'headerOptions' => ['width'=>'100'],
                            'contentOptions' => ['style'=>'color:red'],
                        ],
                        [
                            'attribute'=>'gift_weight',
                            'filter' => Html::activeTextInput($searchModel, 'gift_weight', [
                                'class' => 'form-control',
                            ]),
                            'headerOptions' => ['width'=>'100'],
                        ],
//                        [
//                            'attribute'=>'gold_price',
//                            'filter' => Html::activeTextInput($searchModel, 'gold_price', [
//                                'class' => 'form-control',
//                            ]),
//                            'headerOptions' => ['width' => '120'],
//                        ],
                        [
                            'attribute'=>'cost_price',
                            'visible' => \common\helpers\Auth::verify(\common\enums\SpecialAuthEnum::VIEW_CAIGOU_PRICE),
                            'filter' => Html::activeTextInput($searchModel, 'cost_price', [
                                'class' => 'form-control',
                            ]),
                            'headerOptions' => ['width' => '120'],
                        ],
                        [
                            'attribute' => 'remark',
                            'filter' => Html::activeTextInput($searchModel, 'remark', [
                                'class' => 'form-control',
                            ]),
                            'value' => function ($model) {
                                return $model->remark??"";
                            },
                            'headerOptions' => ['width'=>'100'],
                        ],
                        [
                            'attribute' => 'creator_id',
                            'value' => 'creator.username',
                            'filter' => Html::activeTextInput($searchModel, 'creator.username', [
                                'class' => 'form-control',
                            ]),
                            'headerOptions' => ['width' => '100'],
                        ],
                        /*[
                            'attribute' => 'supplier_id',
                            'value' =>"supplier.supplier_name",
                            'filter'=>Select2::widget([
                                'name'=>'SearchModel[supplier_id]',
                                'value'=>$searchModel->supplier_id,
                                'data'=>Yii::$app->supplyService->supplier->getDropDown(),
                                'options' => ['placeholder' =>"请选择"],
                                'pluginOptions' => [
                                    'allowClear' => true,
                                    'width' => '200',
                                ],
                            ]),
                            'format' => 'raw',
                            'headerOptions' => [],
                        ],*/
                        [
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
                        [
                            'class' => 'yii\grid\ActionColumn',
                            'header' => '操作',
                            'template' => '{view}',
                            'buttons' => [
                                'view' => function($url, $model, $key){
                                    return Html::a('查看', ['view', 'id' => $model->id,'returnUrl'=>Url::getReturnUrl()], ['class' => 'btn btn-warning btn-sm']);
                                },
                            ],
                        ],
                    ]
                ]); ?>
            </div>
        </div>
    </div>
</div>
<script>
    function batchExport() {
        var ids = $("#grid").yiiGridView("getSelectedRows");
        if(ids.length == 0){
            var url = "<?= Url::to('index?action=export'.$params);?>";
            rfExport(url)
        }else{
            window.location.href = "<?= Url::buildUrl('export',[],['ids'])?>?ids=" + ids;
        }
    }
</script>
<?php

use common\helpers\Html;
use common\helpers\Url;
use kartik\select2\Select2;
use yii\grid\GridView;
use kartik\daterange\DateRangePicker;
use addons\Style\common\enums\AttrIdEnum;
use addons\Warehouse\common\enums\GoodsStatusEnum;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('warehouse_goods', '商品管理');
$this->params['breadcrumbs'][] = $this->title;
$params = Yii::$app->request->queryParams;
$params = $params ? "&".http_build_query($params) : '';
?>

<div class="row">
    <div class="col-sm-12">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title"><?= Html::encode($this->title) ?></h3>
                <h6 style="color:red">*有款起版的商品（既有款号又有起版号的），下订单只能用起版号下单</h6>
            </div>
            <div >
                <?php
                $get = Yii::$app->request->get();
                if(isset($get['SearchModel'])){
                    $url = \common\helpers\ArrayHelper::merge([0 => 'index'], ['SearchModel'=>$get['SearchModel']]);
                }else{
                    $url = ['index'];
                }
                ?>
                <?= Html::beginForm(Url::to($url), 'get') ?>
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">筛选查询</h4>
                        </div>
                        <div class="modal-body" id="select">
                            <div class="row">
                                <div class="col-lg-3">
                                    <div class="form-group field-cate-sort">
                                        <div class="col-sm-4 text-right">
                                            <label class="control-label" for="cate-sort">条码号：</label>
                                        </div>
                                        <div class="col-sm-8">
                                            <?= Html::textInput('goods_id', $search->goods_id, ['class' => 'form-control','placeholder'=>'多个以空格或者英文逗号隔开']) ?>
                                            <div class="help-block"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group field-cate-sort">
                                        <div class="col-sm-4 text-right">
                                            <label class="control-label" for="cate-sort">商品名称：</label>
                                        </div>
                                        <div class="col-sm-8">
                                            <?= Html::textInput('goods_name', $search->goods_name, ['class' => 'form-control','placeholder'=>'模糊搜索']) ?>
                                            <div class="help-block"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-3">
                                    <div class="form-group field-cate-sort">
                                        <div class="col-sm-4 text-right">
                                            <label class="control-label" for="cate-sort">金托类型：</label>
                                        </div>
                                        <div class="col-sm-8">
                                            <?= \kartik\select2\Select2::widget([
                                                'name'=>'jintuo_type',
                                                'value'=>$search->jintuo_type,
                                                'data'=>\addons\Style\common\enums\JintuoTypeEnum::getMap(),
                                                'options' => ['placeholder' =>"请选择",'multiple'=>false,'style'=>"width:180px"],
                                                'pluginOptions' => [
                                                    'allowClear' => true,
                                                ],
                                            ])
                                            ?>
                                            <div class="help-block"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-3">
                                    <div class="form-group field-cate-sort">
                                        <div class="col-sm-4 text-right">
                                            <label class="control-label" for="cate-sort">供应商：</label>
                                        </div>
                                        <div class="col-sm-8">
                                            <?= \kartik\select2\Select2::widget([
                                                'name'=>'supplier_id',
                                                'value'=>$search->supplier_id,
                                                'data'=>Yii::$app->supplyService->supplier->getDropDown(),
                                                'options' => ['placeholder' =>"请选择",'multiple'=>false,'style'=>"width:180px"],
                                                'pluginOptions' => [
                                                    'allowClear' => true,
                                                ],
                                            ])
                                            ?>
                                            <div class="help-block"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">

                                <div class="col-lg-3">
                                    <div class="form-group field-cate-sort">
                                        <div class="col-sm-4 text-right">
                                            <label class="control-label" for="cate-sort">款号/起版号：</label>
                                        </div>
                                        <div class="col-sm-8">
                                            <?= Html::textInput('goods_sn', $search->goods_sn, ['class' => 'form-control','placeholder'=>'款号或者起版号搜索']) ?>
                                            <div class="help-block"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group field-cate-sort">
                                        <div class="col-sm-4 text-right">
                                            <label class="control-label" for="cate-sort">商品状态：</label>
                                        </div>
                                        <div class="col-sm-8">
                                            <?= \kartik\select2\Select2::widget([
                                                'name'=>'goods_status',
                                                'value'=>$search->goods_status,
                                                'data'=>\addons\Warehouse\common\enums\GoodsStatusEnum::getMap(),
                                                'options' => ['placeholder' =>"请选择",'multiple'=>false,'style'=>"width:180px"],
                                                'pluginOptions' => [
                                                    'allowClear' => true,
                                                ],
                                            ])
                                            ?>
                                            <div class="help-block"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group field-cate-sort">
                                        <div class="col-sm-4 text-right">
                                            <label class="control-label" for="cate-sort">材质：</label>
                                        </div>
                                        <div class="col-sm-8">
                                            <?= \kartik\select2\Select2::widget([
                                                'name'=>'material_type',
                                                'value'=>$search->material_type,
                                                'data'=>Yii::$app->attr->valueMap(AttrIdEnum::MATERIAL_TYPE),
                                                'options' => ['placeholder' =>"请选择",'multiple'=>false,'style'=>"width:180px"],
                                                'pluginOptions' => [
                                                    'allowClear' => true,
                                                ],
                                            ])
                                            ?>

                                            <div class="help-block"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-3">
                                    <div class="form-group field-cate-sort">
                                        <div class="col-sm-4 text-right">
                                            <label class="control-label" for="cate-sort">库存来源：</label>
                                        </div>
                                        <div class="col-sm-8">
                                            <?= \kartik\select2\Select2::widget([
                                                'name'=>'goods_source',
                                                'value'=>$search->goods_source,
                                                'data'=>\addons\Warehouse\common\enums\GoodSourceEnum::getMap(),
                                                'options' => ['placeholder' =>"请选择",'multiple'=>false,'style'=>"width:180px"],
                                                'pluginOptions' => [
                                                    'allowClear' => true,
                                                ],
                                            ])
                                            ?>
                                            <div class="help-block"></div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="row">

                                <div class="col-lg-3">
                                    <div class="form-group field-cate-sort">
                                        <div class="col-sm-4 text-right">
                                            <label class="control-label" for="cate-sort">连石重：</label>
                                        </div>
                                        <div class="col-sm-8 ">
                                            <div class="col-lg-12 input-group">
                                                <div class="input-group">
                                                    <?= Html::textInput('min_suttle_weight', $search->min_suttle_weight, ['class' => 'form-control', 'placeholder' => '最低石重']) ?>
                                                    <span class="input-group-addon" style="border-color: #fff">-</span>
                                                    <?= Html::textInput('max_suttle_weight', $search->max_suttle_weight, ['class' => 'form-control', 'placeholder' => '最高石重']) ?>
                                                </div>
                                                <div class="help-block"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group field-cate-sort">
                                        <div class="col-sm-4 text-right">
                                            <label class="control-label" for="cate-sort">主石重：</label>
                                        </div>
                                        <div class="col-sm-8 ">
                                            <div class="col-lg-12 input-group">
                                                <div class="input-group">
                                                    <?= Html::textInput('min_diamond_carat', $search->min_diamond_carat, ['class' => 'form-control', 'placeholder' => '最低主石价']) ?>
                                                    <span class="input-group-addon" style="border-color: #fff">-</span>
                                                    <?= Html::textInput('max_diamond_carat', $search->max_diamond_carat, ['class' => 'form-control', 'placeholder' => '最高主石价']) ?>
                                                </div>
                                                <div class="help-block"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group field-cate-sort">
                                        <div class="col-sm-4 text-right">
                                            <label class="control-label" for="cate-sort">主石类型：</label>
                                        </div>
                                        <div class="col-sm-8">
                                            <?= Html::dropDownList('main_stone_type', $search->main_stone_type, Yii::$app->attr->valueMap(AttrIdEnum::MAIN_STONE_TYPE), [
                                                'class' => 'form-control',
                                                'prompt' => '全部',
                                            ]) ?>
                                            <div class="help-block"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group field-cate-sort">
                                        <div class="col-sm-4 text-right">
                                            <label class="control-label" for="cate-sort">所属渠道：</label>
                                        </div>
                                        <div class="col-sm-8">
                                            <?= \kartik\select2\Select2::widget([
                                                'name'=>'style_channel_id',
                                                'value'=>$search->style_channel_id,
                                                'data'=>Yii::$app->salesService->saleChannel->getDropDown(),
                                                'options' => ['placeholder' =>"请选择",'multiple'=>true,'style'=>"width:180px"],
                                                'pluginOptions' => [
                                                    'allowClear' => true,
                                                ],
                                            ])
                                            ?>
                                            <div class="help-block"></div>
                                        </div>
                                    </div>
                                </div>

                            </div>



                            <div class="row">
                                <div class="col-lg-3">
                                    <div class="form-group field-cate-sort">
                                        <div class="col-sm-4 text-right">
                                            <label class="control-label" for="cate-sort">商品分类：</label>
                                        </div>
                                        <div class="col-sm-8">
                                            <?= \kartik\select2\Select2::widget([
                                                'name'=>'style_cate_id',
                                                'value'=>$search->style_cate_id,
                                                'data'=>Yii::$app->styleService->styleCate::getDropDown(),
                                                'options' => ['placeholder' =>"请选择（可多选）",'multiple'=>true,'style'=>"width:180px"],
                                                'pluginOptions' => [
                                                    'allowClear' => true,
                                                ],
                                            ])
                                            ?>
                                            <div class="help-block"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group field-cate-sort">
                                        <div class="col-sm-4 text-right">
                                            <label class="control-label" for="cate-sort">产品线：</label>
                                        </div>
                                        <div class="col-sm-8">
                                            <?= \kartik\select2\Select2::widget([
                                                'name'=>'product_type_id',
                                                'value'=>$search->product_type_id,
                                                'data'=>Yii::$app->styleService->productType::getDropDown(),
                                                'options' => ['placeholder' =>"请选择（可多选）",'multiple'=>true,'style'=>"width:180px"],
                                                'pluginOptions' => [
                                                    'allowClear' => true,
                                                ],
                                            ])
                                            ?>
                                            <div class="help-block"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group field-cate-sort">
                                        <div class="col-sm-4 text-right">
                                            <label class="control-label" for="cate-sort">仓库：</label>
                                        </div>
                                        <div class="col-sm-8">
                                            <?= \kartik\select2\Select2::widget([
                                                'name'=>'warehouse_id',
                                                'value'=>$search->warehouse_id,
                                                'data'=>Yii::$app->warehouseService->warehouse::getDropDown(),
                                                'options' => ['placeholder' =>"请选择（可多选）",'multiple'=>true,'style'=>"width:180px"],
                                                'pluginOptions' => [
                                                    'allowClear' => true,
                                                ],
                                            ])
                                            ?>
                                            <div class="help-block"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>





                        </div>
                        <div class="box-footer text-center">
                            <button type="reset" class="btn btn-white btn-sm" onclick="cleardd()">重置</button>
                            <button class="btn btn-primary btn-sm">确定</button>
                        </div>
                    </div>
                    <?= Html::endForm() ?>
            </div>
            <div class="box-body table-responsive">
                <?php
                    echo Html::a('批量出库', ['bill-c/quick'], [
                        'class' => 'btn btn-success btn-sm',
                        "onclick" => "batchPop(this);return false;",
                        'data-grid' => 'grid',
                        'data-width' => '50%',
                        'data-height' => '80%',
                        'data-offset' => '20px',
                        'data-title' => '快捷出库',
                    ]);
                    echo '&nbsp;';

                    echo Html::button('导出', [
                        'class'=>'btn btn-success btn-sm',
                        'onclick' => 'batchExport()',
                    ]);
                ?>
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
//                    'filterModel' => $searchModel,
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
                            'label' => '商品图片',
                            'value' => function ($model) {
                                return \common\helpers\ImageHelper::fancyBox($model->goods_image,50,50);
                            },
                            'filter' => false,
                            'format' => 'raw',
                            'headerOptions' => ['width'=>'60'],
                        ],
                        [
                            'attribute' => 'goods_id',
                            'value'=>function($model) {
                                if(preg_match("/^9/is", $model->goods_id)){
                                    $model->goods_id = Yii::$app->warehouseService->warehouseGoods->createGoodsId($model);
                                }                                
                                return Html::a($model->goods_id, ['view', 'id' => $model->id,'returnUrl'=>Url::getReturnUrl()], ['style'=>"text-decoration:underline;color:#3c8dbc",'id'=>"goods_".$model->goods_id]).' <i class="fa fa-copy" onclick="copy(\''. "goods_".$model->goods_id .'\')"></i>';
                            },
                            'filter' => Html::activeTextInput($searchModel, 'goods_id', [
                                'class' => 'form-control',
                                'style'=> 'width:120px;'
                            ]),
                            'format' => 'raw',
                        ],
                        [
                            'attribute' => 'style_sn',
                            'value' => function($model){
                                return "<span id='{$model->style_sn}_{$model->id}'>".$model->style_sn."</span>".' <i class="fa fa-copy" onclick="copy(\''. $model->style_sn.'_'.$model->id .'\')"></i>';
                            },
                            'filter' => Html::activeTextInput($searchModel, 'style_sn', [
                                'class' => 'form-control',
                                'style'=> 'width:80px;'
                            ]),
                            'headerOptions' => [],
                            'format' => 'raw',
                        ],
                        [
                            'attribute'=>'qiban_sn',
                            'value' => function($model){
                                if($model->qiban_sn){
                                    return "<span id='{$model->qiban_sn}'>".$model->qiban_sn."</span>".' <i class="fa fa-copy" onclick="copy(\''. $model->qiban_sn .'\')"></i>';
                                }else{
                                    return '';
                                }

                            },
                            'filter' => Html::activeTextInput($searchModel, 'qiban_sn', [
                                'class' => 'form-control',
                                'style'=> 'width:100px;'
                            ]),
                            'format' => 'raw',
                            'headerOptions' => [],
                        ],
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
                            'attribute'=>'goods_num',
                            'filter' => Html::activeTextInput($searchModel, 'goods_num', [
                                'class' => 'form-control',
                                'style'=> 'width:60px;'
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
                            'attribute'=>'length',
                            'value'=>function($model){
                                return $model->length;
                            },
                            'filter' => false,
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'product_size',
                            'value'=>function($model){
                                return $model->product_size;
                            },
                            'filter' => false,
                            'headerOptions' => [],
                        ],
                        [
                            'attribute' => 'xiangkou',
                            'value' => function($model){
                                return Yii::$app->attr->valueName($model->xiangkou);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'xiangkou',Yii::$app->attr->valueMap(AttrIdEnum::XIANGKOU), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style'=> 'width:80px;'
                            ]),
                            'headerOptions' => [],
                        ],

                        [
                            'attribute' => 'chain_type',
                            'value' => function($model){
                                return Yii::$app->attr->valueName($model->chain_type);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'chain_type',Yii::$app->attr->valueMap(AttrIdEnum::CHAIN_TYPE), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style'=> 'width:80px;'
                            ]),
                            'headerOptions' => [],
                        ],
                        [
                            'attribute' => 'cramp_ring',
                            'value' => function($model){
                                return Yii::$app->attr->valueName($model->cramp_ring);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'cramp_ring',Yii::$app->attr->valueMap(AttrIdEnum::CHAIN_BUCKLE), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style'=> 'width:80px;'
                            ]),
                            'headerOptions' => [],
                        ],


                        [
                            'attribute'=>'suttle_weight',
                            'filter' => Html::activeTextInput($searchModel, 'suttle_weight', [
                                'class' => 'form-control',
                                'style'=> 'width:60px;'
                            ]),
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'gold_weight',
                            'filter' => Html::activeTextInput($searchModel, 'gold_weight', [
                                'class' => 'form-control',
                                'style'=> 'width:60px;'
                            ]),
                            'headerOptions' => [],
                        ],


                        [
                            'attribute'=>'pure_gold',
                            'filter' => Html::activeTextInput($searchModel, 'pure_gold', [
                                'class' => 'form-control',
                                'style'=> 'width:60px;'
                            ]),
                            'headerOptions' => [],
                        ],


                        [
                            'attribute'=>'gold_amount',
                            'filter' => Html::activeTextInput($searchModel, 'gold_amount', [
                                'class' => 'form-control',
                                'style'=> 'width:60px;'
                            ]),
                            'headerOptions' => [],
                        ],

                        /***主石信息开始**/

                        [
                            'attribute'=>'main_stone_sn',
                            'filter' => Html::activeTextInput($searchModel, 'main_stone_sn', [
                                'class' => 'form-control',
                                'style'=> 'width:60px;'
                            ]),
                            'headerOptions' => [],
                        ],

                        [
                            'attribute'=>'main_stone_type',
                            'value' => function($model){
                                return Yii::$app->attr->valueName($model->main_stone_type);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'main_stone_type',Yii::$app->attr->valueMap(AttrIdEnum::MAIN_STONE_TYPE), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style'=> 'width:80px;'
                            ]),
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'main_stone_num',
                            'value'=>function($model){
                                return $model->main_stone_num;
                            },
                            'filter' => false,
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'diamond_shape',
                            'value' => function($model){
                                return Yii::$app->attr->valueName($model->diamond_shape);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'diamond_shape',Yii::$app->attr->valueMap(AttrIdEnum::DIA_SHAPE), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style'=> 'width:80px;'
                            ]),
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'diamond_carat',
                            'value'=>function($model){
                                return $model->diamond_carat;
                            },
                            'filter' => false,
                            'headerOptions' => [],
                        ],

                        [
                            'label'=>'主石成本',
                            'value' => function($model){
                                return round($model->diamond_carat * $model->main_stone_price,2);
                            },
                            'filter' => false,
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'diamond_color',
                            'value' => function($model){
                                return Yii::$app->attr->valueName($model->diamond_color);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'diamond_color',Yii::$app->attr->valueMap(AttrIdEnum::DIA_COLOR), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style'=> 'width:80px;'
                            ]),
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'diamond_clarity',
                            'value' => function($model){
                                return Yii::$app->attr->valueName($model->diamond_clarity);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'diamond_clarity',Yii::$app->attr->valueMap(AttrIdEnum::DIA_CLARITY), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style'=> 'width:80px;'
                            ]),
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'diamond_cut',
                            'value' => function($model){
                                return Yii::$app->attr->valueName($model->diamond_cut);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'diamond_cut',Yii::$app->attr->valueMap(AttrIdEnum::DIA_CUT), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style'=> 'width:80px;'
                            ]),
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'main_stone_colour',
                            'value' => function($model){
                                return Yii::$app->attr->valueName($model->main_stone_colour);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'main_stone_colour',Yii::$app->attr->valueMap(AttrIdEnum::DIA_COLOUR), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style'=> 'width:80px;'
                            ]),
                            'headerOptions' => [],
                        ],

//                        /***副石1开始**/
//                        [
//                            'attribute'=>'second_stone_sn1',
//                            'filter' => Html::activeTextInput($searchModel, 'second_stone_sn1', [
//                                'class' => 'form-control',
//                                'style'=> 'width:60px;'
//                            ]),
//                            'headerOptions' => [],
//                        ],
//                        [
//                            'attribute'=>'second_peishi_way1',
//                            'value' => function($model){
//                                return \addons\Warehouse\common\enums\PeiShiWayEnum::getValue($model->second_peishi_way1);
//                            },
//                            'filter' => Html::activeDropDownList($searchModel, 'second_peishi_way1',\addons\Warehouse\common\enums\PeiShiWayEnum::getMap(), [
//                                'prompt' => '全部',
//                                'class' => 'form-control',
//                                'style'=> 'width:80px;'
//                            ]),
//                            'headerOptions' => [],
//                        ],
//                        [
//                            'attribute'=>'second_stone_type1',
//                            'value' => function($model){
//                                return Yii::$app->attr->valueName($model->second_stone_type1);
//                            },
//                            'filter' => Html::activeDropDownList($searchModel, 'second_stone_type1',Yii::$app->attr->valueMap(AttrIdEnum::SIDE_STONE1_TYPE), [
//                                'prompt' => '全部',
//                                'class' => 'form-control',
//                                'style'=> 'width:80px;'
//                            ]),
//                            'headerOptions' => [],
//                        ],
//                        [
//                            'attribute'=>'second_stone_num1',
//                            'value'=>function($model){
//                                return $model->second_stone_num1;
//                            },
//                            'filter' => false,
//                            'headerOptions' => [],
//                        ],
//                        [
//                            'attribute'=>'second_stone_shape1',
//                            'value' => function($model){
//                                return Yii::$app->attr->valueName($model->second_stone_shape1);
//                            },
//                            'filter' => Html::activeDropDownList($searchModel, 'second_stone_shape1',Yii::$app->attr->valueMap(AttrIdEnum::SIDE_STONE1_SHAPE), [
//                                'prompt' => '全部',
//                                'class' => 'form-control',
//                                'style'=> 'width:80px;'
//                            ]),
//                            'headerOptions' => [],
//                        ],
//                        [
//                            'attribute'=>'second_stone_weight1',
//                            'value'=>function($model){
//                                return $model->second_stone_weight1;
//                            },
//                            'filter' => false,
//                            'headerOptions' => [],
//                        ],
//                        [
//                            'attribute'=>'second_stone_price1',
//                            'filter' => Html::activeTextInput($searchModel, 'second_stone_price1', [
//                                'class' => 'form-control',
//                                'style'=> 'width:100px;'
//                            ]),
//                            'headerOptions' => [],
//                        ],
//                        [
//                            'label'=>'副石1成本',
//                            'value' => function($model){
//                                return $model->second_stone_cost1;
//                            },
//                            'filter' => false,
//                            'headerOptions' => [],
//                        ],
//                        [
//                            'attribute'=>'second_stone_color1',
//                            'value' => function($model){
//                                return Yii::$app->attr->valueName($model->second_stone_color1);
//                            },
//                            'filter' => Html::activeDropDownList($searchModel, 'second_stone_color1',Yii::$app->attr->valueMap(AttrIdEnum::SIDE_STONE1_COLOR), [
//                                'prompt' => '全部',
//                                'class' => 'form-control',
//                                'style'=> 'width:80px;'
//                            ]),
//                            'headerOptions' => [],
//                        ],
//                        [
//                            'attribute'=>'second_stone_clarity1',
//                            'value' => function($model){
//                                return Yii::$app->attr->valueName($model->second_stone_clarity1);
//                            },
//                            'filter' => Html::activeDropDownList($searchModel, 'second_stone_clarity1',Yii::$app->attr->valueMap(AttrIdEnum::SIDE_STONE1_CLARITY), [
//                                'prompt' => '全部',
//                                'class' => 'form-control',
//                                'style'=> 'width:80px;'
//                            ]),
//                            'headerOptions' => [],
//                        ],
//                        [
//                            'attribute'=>'second_stone_colour1',
//                            'value' => function($model){
//                                return Yii::$app->attr->valueName($model->second_stone_colour1);
//                            },
//                            'filter' => Html::activeDropDownList($searchModel, 'second_stone_colour1',Yii::$app->attr->valueMap(AttrIdEnum::SIDE_STONE1_SECAI), [
//                                'prompt' => '全部',
//                                'class' => 'form-control',
//                                'style'=> 'width:80px;'
//                            ]),
//                            'headerOptions' => [],
//                        ],
//                        /***副石2开始**/
//
//                        [
//                            'attribute'=>'second_stone_sn2',
//                            'filter' => Html::activeTextInput($searchModel, 'second_stone_sn2', [
//                                'class' => 'form-control',
//                                'style'=> 'width:60px;'
//                            ]),
//                            'headerOptions' => [],
//                        ],
//                        [
//                            'attribute'=>'second_peishi_way2',
//                            'value' => function($model){
//                                return \addons\Warehouse\common\enums\PeiShiWayEnum::getValue($model->second_peishi_way2);
//                            },
//                            'filter' => Html::activeDropDownList($searchModel, 'second_peishi_way2',\addons\Warehouse\common\enums\PeiShiWayEnum::getMap(), [
//                                'prompt' => '全部',
//                                'class' => 'form-control',
//                                'style'=> 'width:80px;'
//                            ]),
//                            'headerOptions' => [],
//                        ],
//                        [
//                            'attribute'=>'second_stone_type2',
//                            'value' => function($model){
//                                return Yii::$app->attr->valueName($model->second_stone_type2);
//                            },
//                            'filter' => Html::activeDropDownList($searchModel, 'second_stone_type2',Yii::$app->attr->valueMap(AttrIdEnum::SIDE_STONE2_TYPE), [
//                                'prompt' => '全部',
//                                'class' => 'form-control',
//                                'style'=> 'width:80px;'
//                            ]),
//                            'headerOptions' => [],
//                        ],
//                        [
//                            'attribute'=>'second_stone_num2',
//                            'value'=>function($model){
//                                return $model->second_stone_num2;
//                            },
//                            'filter' => false,
//                            'headerOptions' => [],
//                        ],
//                        [
//                            'attribute'=>'second_stone_shape2',
//                            'value' => function($model){
//                                return Yii::$app->attr->valueName($model->second_stone_shape2);
//                            },
//                            'filter' => Html::activeDropDownList($searchModel, 'second_stone_shape2',Yii::$app->attr->valueMap(AttrIdEnum::SIDE_STONE2_SHAPE), [
//                                'prompt' => '全部',
//                                'class' => 'form-control',
//                                'style'=> 'width:80px;'
//                            ]),
//                            'headerOptions' => [],
//                        ],
//                        [
//                            'attribute'=>'second_stone_weight2',
//                            'value'=>function($model){
//                                return $model->second_stone_weight2;
//                            },
//                            'filter' => false,
//                            'headerOptions' => [],
//                        ],
//                        [
//                            'attribute'=>'second_stone_price2',
//                            'filter' => Html::activeTextInput($searchModel, 'second_stone_price2', [
//                                'class' => 'form-control',
//                                'style'=> 'width:100px;'
//                            ]),
//                            'headerOptions' => [],
//                        ],
//                        [
//                            'label'=>'副石2成本',
//                            'value' => function($model){
//                                return $model->second_stone_cost2;
//                            },
//                            'filter' => false,
//                            'headerOptions' => [],
//                        ],
//                        [
//                            'attribute'=>'second_stone_color2',
//                            'value' => function($model){
//                                return Yii::$app->attr->valueName($model->second_stone_color2);
//                            },
//                            'filter' => Html::activeDropDownList($searchModel, 'second_stone_color2',Yii::$app->attr->valueMap(AttrIdEnum::SIDE_STONE2_COLOR), [
//                                'prompt' => '全部',
//                                'class' => 'form-control',
//                                'style'=> 'width:80px;'
//                            ]),
//                            'headerOptions' => [],
//                        ],
//                        [
//                            'attribute'=>'second_stone_clarity2',
//                            'value' => function($model){
//                                return Yii::$app->attr->valueName($model->second_stone_clarity2);
//                            },
//                            'filter' => Html::activeDropDownList($searchModel, 'second_stone_clarity2',Yii::$app->attr->valueMap(AttrIdEnum::SIDE_STONE2_CLARITY), [
//                                'prompt' => '全部',
//                                'class' => 'form-control',
//                                'style'=> 'width:80px;'
//                            ]),
//                            'headerOptions' => [],
//                        ],
//                        [
//                            'attribute'=>'second_stone_colour2',
//                            'value' => function($model){
//                                return Yii::$app->attr->valueName($model->second_stone_colour2);
//                            },
//                            'filter' => Html::activeDropDownList($searchModel, 'second_stone_colour2',Yii::$app->attr->valueMap(AttrIdEnum::SIDE_STONE2_SECAI), [
//                                'prompt' => '全部',
//                                'class' => 'form-control',
//                                'style'=> 'width:80px;'
//                            ]),
//                            'headerOptions' => [],
//                        ],
//                         /**副石2结束**/
//
//                        /***副石3开始**/
//                        [
//                            'attribute'=>'second_stone_sn3',
//                            'filter' => Html::activeTextInput($searchModel, 'second_stone_sn3', [
//                                'class' => 'form-control',
//                                'style'=> 'width:60px;'
//                            ]),
//                            'headerOptions' => [],
//                        ],
//                        [
//                            'attribute'=>'second_peishi_way3',
//                            'value' => function($model){
//                                return \addons\Warehouse\common\enums\PeiShiWayEnum::getValue($model->second_peishi_way3);
//                            },
//                            'filter' => Html::activeDropDownList($searchModel, 'second_peishi_way3',\addons\Warehouse\common\enums\PeiShiWayEnum::getMap(), [
//                                'prompt' => '全部',
//                                'class' => 'form-control',
//                                'style'=> 'width:80px;'
//                            ]),
//                            'headerOptions' => [],
//                        ],
//
//
//                        [
//                            'attribute'=>'second_stone_type3',
//                            'value' => function($model){
//                                return Yii::$app->attr->valueName($model->second_stone_type3);
//                            },
//                            'filter' => Html::activeDropDownList($searchModel, 'second_stone_type3',Yii::$app->attr->valueMap(AttrIdEnum::SIDE_STONE3_TYPE), [
//                                'prompt' => '全部',
//                                'class' => 'form-control',
//                                'style'=> 'width:80px;'
//                            ]),
//                            'headerOptions' => [],
//                        ],
//                        [
//                            'attribute'=>'second_stone_num3',
//                            'value'=>function($model){
//                                return $model->second_stone_num3;
//                            },
//                            'filter' => false,
//                            'headerOptions' => [],
//                        ],
//                        [
//                            'attribute'=>'second_stone_weight3',
//                            'value'=>function($model){
//                                return $model->second_stone_weight3;
//                            },
//                            'filter' => false,
//                            'headerOptions' => [],
//                        ],
//                        [
//                            'attribute'=>'second_stone_price3',
//                            'filter' => Html::activeTextInput($searchModel, 'second_stone_price3', [
//                                'class' => 'form-control',
//                                'style'=> 'width:100px;'
//                            ]),
//                            'headerOptions' => [],
//                        ],
//                        [
//                            'label'=>'副石3成本',
//                            'value' => function($model){
//                                return $model->second_stone_cost3;
//                            },
//                            'filter' => false,
//                            'headerOptions' => [],
//                        ],
//
//                        /**副石3结束**/


                        [
                            'attribute'=>'peijian_type',
                            'value' => function($model){
                                return Yii::$app->attr->valueName($model->peijian_type);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'peijian_type',Yii::$app->attr->valueMap(AttrIdEnum::MAT_PARTS_TYPE), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style'=> 'width:80px;'
                            ]),
                            'headerOptions' => [],
                        ],
//                        [
//                            'attribute'=>'parts_material',
//                            'filter' => false,
//                            'headerOptions' => [],
//                        ],
//                        [
//                            'attribute'=>'parts_num',
//                            'filter' => false,
//                            'headerOptions' => [],
//                        ],
//
//                        [
//                            'attribute'=>'parts_gold_weight',
//                            'filter' => false,
//                            'headerOptions' => [],
//                        ],
//                        [
//                            'attribute'=>'parts_price',
//                            'filter' => false,
//                            'headerOptions' => [],
//                        ],
//                        [
//                            'attribute'=>'parts_amount',
//                            'value' => function($model){
//                                return $model->parts_amount;
////                                return round($model->parts_gold_weight * $model->parts_price,2);
//                            },
//                            'filter' => false,
//                            'headerOptions' => [],
//                        ],
//                        [
//                            'attribute'=>'parts_fee',
//                            'filter' => false,
//                            'headerOptions' => [],
//                        ],
//                        [
//                            'attribute'=>'ke_gong_fee',
//                            'filter' => false,
//                            'headerOptions' => [],
//                        ],
//                        [
//                            'attribute'=>'gong_fee',
//                            'filter' => false,
//                            'headerOptions' => [],
//                        ],
//                        [
//                            'attribute'=>'peishi_fee',
//                            'filter' => false,
//                            'headerOptions' => [],
//                        ],
//                        [
//                            'attribute'=>'peishi_amount',
//                            'filter' => false,
//                            'headerOptions' => [],
//                        ],
//
//                        [
//                            'attribute'=>'total_gong_fee',
//                            'filter' => false,
//                            'headerOptions' => [],
//                        ],
//                        [
//                            'attribute'=>'xianqian_price',
//                            'filter' => false,
//                            'headerOptions' => [],
//                        ],

                        [
                            'attribute'=>'biaomiangongyi',
                            'value' => function($model){
                                return Yii::$app->attr->valueName($model->biaomiangongyi);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'biaomiangongyi',Yii::$app->attr->valueMap(AttrIdEnum::FACEWORK), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style'=> 'width:80px;'
                            ]),
                            'headerOptions' => [],
                        ],
//                        [
//                            'attribute'=>'biaomiangongyi_fee',
//                            'filter' => false,
//                            'headerOptions' => [],
//                        ],
//                        [
//                            'attribute'=>'fense_fee',
//                            'filter' => false,
//                            'headerOptions' => [],
//                        ],
//                        [
//                            'attribute'=>'penrasa_fee',
//                            'filter' => false,
//                            'headerOptions' => [],
//                        ],
//                        [
//                            'attribute'=>'lasha_fee',
//                            'filter' => false,
//                            'headerOptions' => [],
//                        ],
//                        [
//                            'attribute'=>'piece_fee',
//                            'filter' => false,
//                            'headerOptions' => [],
//                        ],
//                        [
//                            'attribute'=>'bukou_fee',
//                            'filter' => false,
//                            'headerOptions' => [],
//                        ],
//                        [
//                            'attribute'=>'edition_fee',
//                            'filter' => false,
//                            'headerOptions' => [],
//                        ],
//                        [
//                            'attribute'=>'cert_fee',
//                            'filter' => false,
//                            'headerOptions' => [],
//                        ],

                        [
                            'attribute'=>'factory_cost',
                            'filter' => false,
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'cost_price',
                            'filter' => false,
                            'visible' => \common\helpers\Auth::verify(\common\enums\SpecialAuthEnum::VIEW_CAIGOU_PRICE),
                            'headerOptions' => [],
                        ],

                        [
                            'attribute'=>'diamond_cert_type',
                            'value' => function($model){
                                return Yii::$app->attr->valueName($model->diamond_cert_type);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'diamond_cert_type',Yii::$app->attr->valueMap(AttrIdEnum::DIA_CERT_TYPE), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style'=> 'width:80px;'
                            ]),
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'diamond_cert_id',
                            'filter' => Html::activeTextInput($searchModel, 'diamond_cert_id', [
                                'class' => 'form-control',
                                'style'=> 'width:100px;'
                            ]),
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'chuku_price',
                            'value'=> function($model){                                
                                 return $model->getChukuPrice();                                
                            },
                            'filter' => Html::activeTextInput($searchModel, 'chuku_price', [
                                'class' => 'form-control',
                                'style'=> 'width:100px;'
                            ]),
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'market_price',
                            'filter' => Html::activeTextInput($searchModel, 'market_price', [
                                'class' => 'form-control',
                                'style'=> 'width:100px;'
                            ]),
                            'headerOptions' => [],
                        ],

//                        [
//                            'attribute' => 'put_in_type',
//                            'format' => 'raw',
//                            'headerOptions' => ['class' => 'col-md-1'],
//                            'value' => function ($model){
//                                return \addons\Warehouse\common\enums\PutInTypeEnum::getValue($model->put_in_type);
//                            },
//                            'filter' => Html::activeDropDownList($searchModel, 'put_in_type',\addons\Warehouse\common\enums\PutInTypeEnum::getMap(), [
//                                'prompt' => '全部',
//                                'class' => 'form-control',
//                                'style'=> 'width:100px;'
//
//                            ]),
//                        ],

//                        [
//                            'attribute' => 'style_channel_id',
//                            'format' => 'raw',
//                            'headerOptions' => ['class' => 'col-md-1'],
//                            'value' => function($model){
//                                return $model->channel->name ?? '';
//                            },
//                            'filter' => Html::activeDropDownList($searchModel, 'style_channel_id',Yii::$app->salesService->saleChannel->getDropDown(), [
//                                'prompt' => '全部',
//                                'class' => 'form-control',
//                                'style'=> 'width:120px;'
//
//                            ]),
//                        ],

                        [
                            'attribute' => 'warehouse_id',
                            'value' =>"warehouse.name",
                            'filter'=>\kartik\select2\Select2::widget([
                                'name'=>'SearchModel[warehouse_id]',
                                'value'=>$searchModel->warehouse_id,
                                'data'=>Yii::$app->warehouseService->warehouse::getDropDown(),
                                'options' => ['placeholder' =>"请选择"],
                                'pluginOptions' => [
                                    'allowClear' => true,
                                    'width' => 200
                                ],
                                'pluginLoading'=>false


                            ]),
                            'headerOptions' => ['class' => 'col-md-2'],
                            'format' => 'raw',

                        ],
//                        [
//                            'attribute' => 'supplier_id',
//                            'value' =>"supplier.supplier_name",
//                            'filter'=>Select2::widget([
//                                'name'=>'SearchModel[supplier_id]',
//                                'value'=>$searchModel->supplier_id,
//                                'data'=>Yii::$app->supplyService->supplier->getDropDown(),
//                                'options' => ['placeholder' =>"请选择"],
//                                'pluginOptions' => [
//                                    'allowClear' => true,
//                                    'width' => 200
//                                ],
//                            ]),
//                            'format' => 'raw',
//                            'headerOptions' => ['class' => 'col-md-2'],
//                        ],
//                        [
//                            'attribute' => 'goods_source',
//                            'format' => 'raw',
//                            'headerOptions' => ['class' => 'col-md-1'],
//                            'value' => function ($model){
//                                return \addons\Warehouse\common\enums\GoodSourceEnum::getValue($model->goods_source);
//                            },
//                            'filter' => Html::activeDropDownList($searchModel, 'goods_source',\addons\Warehouse\common\enums\GoodSourceEnum::getMap(), [
//                                'prompt' => '全部',
//                                'class' => 'form-control',
//                                'style'=> 'width:100px;'
//
//                            ]),
//                        ],

                        [
                            'attribute' => 'weixiu_status',
                            'format' => 'raw',
                            'headerOptions' => ['class' => 'col-md-1'],
                            'value' => function ($model){
                                return \addons\Warehouse\common\enums\WeixiuStatusEnum::getValue($model->weixiu_status);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'weixiu_status',\addons\Warehouse\common\enums\WeixiuStatusEnum::getMap(), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style'=> 'width:80px;'
                            ]),
                        ],
//                        [
//                            'attribute' => 'style_sex',
//                            'format' => 'raw',
//                            'headerOptions' => ['class' => 'col-md-1'],
//                            'value' => function ($model){
//                                return \addons\Style\common\enums\StyleSexEnum::getValue($model->style_sex);
//                            },
//                            'filter' => Html::activeDropDownList($searchModel, 'style_sex',\addons\Style\common\enums\StyleSexEnum::getMap(), [
//                                'prompt' => '全部',
//                                'class' => 'form-control',
//                                'style'=> 'width:80px;'
//                            ]),
//                        ],
                        [
                            'attribute'=>'qiban_type',
                            'value'=> function($model){
                                return \addons\Style\common\enums\QibanTypeEnum::getValue($model->qiban_type);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'qiban_type',\addons\Style\common\enums\QibanTypeEnum::getMap(), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style'=> 'width:100px;'

                            ]),
                            'headerOptions' => [],
                        ],
                        [
                            'label' => '首次入库时间',
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
                            'attribute'=>'chuku_time',
                            'filter' => DateRangePicker::widget([    // 日期组件
                                    'model' => $searchModel,
                                    'attribute' => 'chuku_time',
                                    'value' => $searchModel->chuku_time,
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
                                return Yii::$app->formatter->asDatetime($model->chuku_time);
                            }
                        
                        ],
                        [
                            'label'=>'库龄',
                            'value'=>function($model){
                                 $date = Yii::$app->formatter->asDuration($model->getGoodsAge());
                                 $date_arr = explode(',',$date);
                                 $date_str = '';
                                 foreach ($date_arr as $k => $v){
                                     if($k > 2) break;
                                     $date_str .= $v;
                                 }
                                 return $date_str;
                            },
                            'filter' => false,
                            'headerOptions' => [],
                        ],

                    ]
                ]); ?>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    function cleardd() {
        $('#select select').prop('selectedIndex', 0);
    }

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

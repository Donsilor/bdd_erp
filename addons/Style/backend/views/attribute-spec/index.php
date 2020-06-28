<?php

use common\helpers\Html;
use common\helpers\Url;
use yii\grid\GridView;
use addons\Style\common\enums\AttrModuleEnum;


/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('goods_attribute', '产品规格管理');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title"><?= Html::encode($this->title) ?></h3>
                <div class="box-tools">
                    <?= Html::create(['ajax-edit'], '创建', [
                        'data-toggle' => 'modal',
                        'data-target' => '#ajaxModalLg',
                    ]); ?>
                </div>
            </div>
            <div class="box-body table-responsive">
    <?php echo Html::batchButtons(false)?>        
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => ['class' => 'table table-hover'],
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
                'attribute'=>'attr_id',
                'label' =>'属性ID',
                'value' =>'attr_id',
                'filter' => Html::activeTextInput($searchModel, 'attr_id', [
                        'class' => 'form-control',
                ]),
                'headerOptions' => ['width'=>'80'],
            ],
            [
                'attribute'=>'attr.attr_name',
                'value' =>'attr.attr_name',
                'filter' => Html::activeTextInput($searchModel, 'attr.attr_name', [
                        'class' => 'form-control',
                        //'style' =>'width:100px'
                ]),

                'headerOptions' => ['class' => 'col-md-1'],
            ],
            [
                'attribute'=>'modules',
                'value' => function($model){
                    $modules = $model->modules ? explode(',',$model->modules ) : [];
                    foreach ($modules as & $module) {
                        $module = AttrModuleEnum::getValue($module);
                    }
                    return implode(",",$modules);
                },                  
                'filter' => Html::activeDropDownList($searchModel, 'modules',AttrModuleEnum::getMap(), [
                        'prompt' => '全部',
                        'class' => 'form-control',
                ]),
                'contentOptions' => [/*'style' => 'word-break:break-all;'*/],
            ],
            [
                'attribute' => 'cate.name',
                'headerOptions' => ['class' => 'col-md-1'],
                'filter' => Html::activeDropDownList($searchModel, 'style_cate_id', Yii::$app->styleService->styleCate->getDropDown(), [
                        'prompt' => '全部',
                        'class' => 'form-control',
                ]),
            ],
            [
                    'attribute' => 'is_inlay',
                    'format' => 'raw',
                    'headerOptions' => ['class' => 'col-md-1'],
                    'value' => function ($model){
                        return \addons\Style\common\enums\InlayEnum::getValue($model->is_inlay);
                    },
                    'filter' => Html::activeDropDownList($searchModel, 'is_inlay',\addons\Style\common\enums\InlayEnum::getMap(), [
                            'prompt' => '全部',
                            'class' => 'form-control'
                    ]),
            ], 
            [
                'attribute' => 'attr_type',
                'format' => 'raw',
                'headerOptions' => ['class' => 'col-md-1'],
                'value' => function ($model){
                    return \addons\Style\common\enums\AttrTypeEnum::getValue($model->attr_type);
                },
                'filter' => Html::activeDropDownList($searchModel, 'attr_type',\addons\Style\common\enums\AttrTypeEnum::getMap(), [
                        'prompt' => '全部',
                        'class' => 'form-control'
                ]),
            ],
           
            [
                'attribute' => 'input_type',
                'format' => 'raw',
                'headerOptions' => ['class' => 'col-md-1'],
                'value' => function ($model){
                    return \common\enums\InputTypeEnum::getValue($model->input_type);
                },
                'filter' => Html::activeDropDownList($searchModel, 'input_type',\common\enums\InputTypeEnum::getMap(), [
                        'prompt' => '全部',
                        'class' => 'form-control'
                ]),
            ],
            [
                'attribute' => 'is_require',
                'format' => 'raw',
                'headerOptions' => ['class' => 'col-md-1'],
                'value' => function ($model){
                    return \common\enums\ConfirmEnum::getValue($model->is_require);
                },
                'filter' => Html::activeDropDownList($searchModel, 'is_require',\common\enums\ConfirmEnum::getMap(), [
                        'prompt' => '全部',
                        'class' => 'form-control'
                ]),
            ],
            [
                'attribute' => 'status',
                'format' => 'raw',
                'headerOptions' => ['class' => 'col-md-1'],
                'value' => function ($model){
                    return \common\enums\StatusEnum::getValue($model->status);
                },
                'filter' => Html::activeDropDownList($searchModel, 'status',\common\enums\StatusEnum::getMap(), [
                        'prompt' => '全部',
                        'class' => 'form-control',
                        
                ]),
            ],
            [
                'attribute' => 'sort',
                'format' => 'raw',
                'value' => function ($model, $key, $index, $column){
                    return  Html::sort($model->sort,['data-url'=>Url::to(['ajax-update'])]);
                },
                'headerOptions' => ['width' => '80'],
            ],            
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => '操作',
                'template' => '{edit} {info} {status}',
                'buttons' => [
                'edit' => function($url, $model, $key){
                        return Html::edit(['ajax-edit','id' => $model->id,'returnUrl' => Url::getReturnUrl()], '编辑', [
                                'data-toggle' => 'modal',
                                'data-target' => '#ajaxModalLg',
                        ]); 
                },
                'info' => function($url, $model, $key){
                     return Html::edit(['edit', 'id' => $model->id,'returnUrl' => Url::getReturnUrl()],'详情',[
                             'class'=>'btn btn-info btn-sm'
                     ]);                
                },
               'status' => function($url, $model, $key){
                        return Html::status($model->status);
                },
                'delete' => function($url, $model, $key){
                        return Html::delete(['delete', 'id' => $model->id]);
                },
            ],            
  
       ]
    ]
    ]); ?>
            </div>
        </div>
        <p style="color:red;">
         *注：1.是否镶嵌：镶嵌-为镶嵌类商品需要填写的属性：eg:主石和副石信息 &nbsp;  
         2.属性：镶嵌属性，选择是否镶嵌才生效 【此属性应用于：所有商品相关，eg：添加无款起版/有款起版/有款采购】
        </p>
    </div>
</div>
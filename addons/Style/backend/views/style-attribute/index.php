<?php

use common\helpers\Html;
use yii\grid\GridView;
use common\enums\AuditStatusEnum;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '款式属性';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box-body nav-tabs-custom">
    <h2 class="page-header">款式详情 - <?php echo $style->style_sn?></h2>
    <?php echo Html::menuTab($tabList,$tab)?>
    <div class="tab-content">
        <div class="row col-xs-12">
                <div class="box">
                    <div class="box-header" style="border-bottom:1px solid #eee">
                        <h3 class="box-title"><?= Html::encode($this->title) ?></h3>
                        <div class="box-tools">
                            <?php
                                    echo Html::create(['edit', 'style_id' => $style->id], '编辑属性', [
                                        'class' => 'btn btn-primary btn-xs openIframe',
                                        'data-width' => '90%',
                                        'data-height' => '90%',
                                        'data-offset' => '20px',
                                    ]);

                            ?>
                        </div>
                    </div>
                    <div class="box-body table-responsive" >
                        <?= GridView::widget([
                            'dataProvider' => $dataProvider,
                            'filterModel' => $searchModel,
                            'tableOptions' => ['class' => 'table table-hover'],
                            'showFooter' => false,//显示footer行
                            'id'=>'grid',
                            'columns' => [
                                [
                                    'class' => 'yii\grid\SerialColumn',
                                    'visible' => true,
                                    'headerOptions' => ['width' => '50'],
                                ],
                                [
                                    'label' => '属性ID',
                                    'attribute'=>'attr_id',
                                    'filter' => false,
                                    'value' => function($model){
                                         return $model->attr_id;
                                    },
                                    'headerOptions' => ['width' => '60'],
                                ],
                                [
                                    'label' => '款式编号',
                                    'attribute' => 'style_sn',
                                    'value' => function($model) use($style){
                                        return $style->style_sn;
                                     },
                                    'filter' => false,
                                    'format' => 'raw',
                                    'headerOptions' => ['width'=>'120'],
                                ],
                                [
                                    'label' => '产品线',
                                    'attribute' => 'product_type',
                                    'format' => 'raw',
                                    'headerOptions' => ['class' => 'col-md-1'],
                                    'value' => function($model) use($style){
                                        return $style->type->name ?? '';
                                     },
                                     'filter' => false,
                                ],   
                                [
                                    'label' => '款式分类',
                                    'attribute'=>'style_cate',
                                    'filter' => false,
                                    'value' => function($model) use($style){
                                        return $style->cate->name ?? '';
                                     },
                                     'headerOptions' => ['class' => 'col-md-1'],
                                ],                                                                     
                                [
                                    'label'=>'显示方式',
                                    'attribute'=>'style_cate',
                                    'value'=>function($model) {
                                        return \common\enums\InputTypeEnum::getValue($model->input_type);
                                    },
                                    'filter' => Html::activeDropDownList($searchModel, 'input_type',\common\enums\InputTypeEnum::getMap(), [
                                            'prompt' => '全部',
                                            'class' => 'form-control'
                                    ]),
                                    'headerOptions' => ['class' => 'col-md-1'],
                                    
                                ],  
                                [
                                    'label' => '属性类型',
                                    'attribute'=>'attr_type',
                                    'value' => function($model){
                                        return \addons\Style\common\enums\AttrTypeEnum::getValue($model->attr_type);
                                    },
                                    'filter' => Html::activeDropDownList($searchModel, 'attr_type',\addons\Style\common\enums\AttrTypeEnum::getMap(), [
                                            'prompt' => '全部',
                                            'class' => 'form-control'
                                    ]),
                                    'headerOptions' => ['class' => 'col-md-1'],
                                ],
                                [
                                    'label' => '属性',
                                    'attribute'=>'attr_id',
                                    'filter' => false,
                                    'value' => function($model){
                                        return $model->attr->attr_name ?? '';
                                    },
                                    'headerOptions' => ['class' => 'col-md-1'],
                               ],
                               [
                                   'label' => '属性值',
                                   'attribute'=>'attr_values',
                                   'filter' => false,
                                   'value' => function($model){
                                        if($model->input_type == \common\enums\InputTypeEnum::INPUT_TEXT) {
                                           $attrValues = $model->attr_values;
                                        }else{
                                           $attrValues = Yii::$app->styleService->attribute->getValuesByValueIds($model->attr_values);
                                           $attrValues = implode("，",$attrValues);
                                        }
                                        return $attrValues;
                                    },
                                    'headerOptions' => [],
                               ],                                                                     
                            ]
                        ]); ?>
                    </div>
              </div>
            <!-- box end -->
        </div>
    </div>
</div>
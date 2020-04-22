<?php

use common\helpers\Html;
use common\helpers\Url;
use yii\grid\GridView;
use kartik\daterange\DateRangePicker;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '起版属性';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box-body nav-tabs-custom">
    <h2 class="page-header">起版详情 - <?php echo $qiban->qiban_sn?></h2>
    <ul class="nav nav-tabs">
        <li class=""><a href="<?=Url::to(['qiban/view','id'=>$qiban->id])?>" >基础信息</a></li>
        <li class="active"><a href="<?=Url::to(['qiban-attribute/index','qiban_id'=>$qiban->id])?>" >起版属性</a></li>
    </ul>
    <div class="tab-content">
        <div class="row col-xs-12">
                <div class="box">
                    <div class="box-header" style="border-bottom:1px solid #eee">
                        <h3 class="box-title"><?= Html::encode($this->title) ?></h3>
                        <div class="box-tools">
                            <?= Html::create(['ajax-edit', 'qiban_id' => $qiban->id,'returnUrl' => $returnUrl], '编辑属性',[
                                    'data-toggle' => 'modal',
                                    'data-target' => '#ajaxModalLg',
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
                                    'visible' => false,
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
                                    'value' => function($model) use($qiban){
                                        return $qiban->qiban_sn;
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
                                    'value' => function($model) use($qiban){
                                        return $qiban->type->name ?? '';
                                     },
                                     'filter' => false,
                                ],   
                                [
                                    'label' => '款式分类',
                                    'attribute'=>'style_cate',
                                    'filter' => false,
                                    'value' => function($model) use($qiban){
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
                                        return $model->attr->attr_name;
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
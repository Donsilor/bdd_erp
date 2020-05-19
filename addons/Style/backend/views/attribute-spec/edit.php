<?php

use common\helpers\Html;
use yii\widgets\ActiveForm;
use yii\grid\GridView;
use common\helpers\Url;

/* @var $this yii\web\View */
/* @var $model addons\Style\common\models\Attribute */
/* @var $form yii\widgets\ActiveForm */

$this->title = Yii::t('attribute', 'Attribute');
$this->params['breadcrumbs'][] = ['label' => Yii::t('attribute', 'Attributes'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
 
 
$model->attr_values = $model->attr_values?explode(",",$model->attr_values):[];
?>
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">基本信息</h3>
            </div>                
            <div class="box-body" style="margin-left:30px">
                <?php                 
                $form = ActiveForm::begin([
                        'fieldConfig' => [
                                //'template' => "<div class='col-sm-2 text-right'>{label}</div><div class='col-sm-10'>{input}\n{hint}\n{error}</div>",
                        ],
                ]);
                ?>
                <?= $form->field($model, 'style_cate_id')->widget(kartik\select2\Select2::class, [
 			        'data' => Yii::$app->styleService->styleCate->getGrpDropDown(),
                    'options' => ['placeholder' => '请选择'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]);?>
                <?= $form->field($model, 'attr_id')->widget(kartik\select2\Select2::class, [
                        'data' => Yii::$app->styleService->attribute->getDropDown(),
                        'options' => ['placeholder' => '请选择'],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                ]);?>
                <?php 
                $attr_values = [];
                if ($model->attr_id){
                    $attr_values = \Yii::$app->styleService->attribute->getValuesByAttrId($model->attr_id);
                }
                ?>
    			<div id="box-attributespec-attr_values" style="<?php echo empty($attr_values)?'display:none':''?>">
    				<?= $form->field($model, 'attr_values')->checkboxList($attr_values,['prompt'=>'请选择']);?>
    			</div>
    			
    			<?= $form->field($model, 'attr_type')->widget(kartik\select2\Select2::class, [
    			        'data' => \addons\Style\common\enums\AttrTypeEnum::getRemarkMap(),
                        'options' => [],
                        'pluginOptions' => [
                            'allowClear' => false
                        ],
                ]);?> 
                <div class="row">
                    <div class="col-lg-3">
                        <?= $form->field($model, 'input_type')->radioList(common\enums\InputTypeEnum::getMap()) ?>
                    </div>
                    <div class="col-lg-3">
                        <?= $form->field($model, 'is_require')->radioList(common\enums\ConfirmEnum::getMap())?>
                    </div>
                    <div class="col-lg-3">
                         <?= $form->field($model, 'is_inlay')->radioList(addons\Style\common\enums\InlayEnum::getMap())?>
                    </div>

                </div>
                <?= $form->field($model, 'status')->radioList(common\enums\StatusEnum::getMap())?>
                <?= $form->field($model, 'sort')->textInput() ?>
                <div class="form-group">
                    <div class="col-sm-12 text-center">
                        <button class="btn btn-primary" type="submit">保存</button>
                        <span class="btn btn-white" onclick="history.go(-1)">返回</span>
                    </div>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>


<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">规格值列表</h3>
                <div class="box-tools">

                </div>
            </div>                
     <div class="box-body">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => ['class' => 'table table-hover'],
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn',
                'visible' => false,
            ],
            [
                'label'=>'ID',    
                'attribute'=>'attr_value_id',                
            ],
            [
                'attribute'=>'attr_value_id',
                'value'=>function($searchModel){
                    return \Yii::$app->attr->valueName($searchModel->attr_value_id);
                 }
            ],
            [
                'attribute'=>'attr_id',
                'value'=>function($searchModel){
                    return \Yii::$app->attr->attrName($searchModel->attr_id);
                }
            ],
            [
                'attribute'=>'URL',
                'value'=>function($searchModel) use($model){
                    return '/goods-list/?style_cate_id='.$model->style_cate_id."&attr_id=".$searchModel->attr_value_id;
                },
                'visible'=>true
            ],
            /* [
                'attribute' => 'sort',
                'format' => 'raw',
                'headerOptions' => ['class' => 'col-md-1'],
                'value' => function ($model, $key, $index, $column){
                    return  Html::sort($model->sort,['data-url'=>Url::to(['attribute-spec-value/ajax-update'])]);
                }
            ],
            [
                'attribute' => 'status',
                'format' => 'raw',
                'headerOptions' => ['class' => 'col-md-1'],
                'value' => function ($searchModel){
                    return \common\enums\StatusEnum::getValue($searchModel->status);
                }
            ],       */      
            [
                'attribute'=>'updated_at',
                 'value' => function ($searchModel) {
                    return Yii::$app->formatter->asDatetime($searchModel->updated_at);
                },
                'format' => 'raw',
            ]
    ]
    ]); ?>		
               
        </div>
    </div>
</div>
</div>
<script>
$("#attributespec-attr_id").change(function(){

	$("#box-attributespec-attr_values").hide();

	var attr_id = $(this).val();	
	if(attr_id){
        $.post("<?php echo Url::to(['ajax-attr-values'])?>",{'id':'<?= $model->id ?>','attr_id':attr_id},function(data){
            if(data) {        
                 $("#attributespec-attr_values").html(data); 
                 $("#box-attributespec-attr_values").show();
            }
        });
	}
});
</script>

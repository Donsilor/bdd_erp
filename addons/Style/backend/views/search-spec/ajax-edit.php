<?php

use yii\widgets\ActiveForm;
use common\helpers\Url;
use yii\base\Widget;

$form = ActiveForm::begin([
    'id' => $model->formName(),
    'enableAjaxValidation' => true,
    'validationUrl' => Url::to(['ajax-edit', 'id' => $model['id']]),
    'fieldConfig' => [
        'template' => "<div class='col-sm-2 text-right'>{label}</div><div class='col-sm-10'>{input}\n{hint}\n{error}</div>",
    ]
]);
$model->attr_values = $model->attr_values?explode(",",$model->attr_values):[];
?>

<div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
        <h4 class="modal-title">基本信息</h4>
</div>
    <div class="modal-body">
 			<?= $form->field($model, 'type_id')->widget(kartik\select2\Select2::class, [
 			        'data' => Yii::$app->services->goodsType->getGrpDropDown(),
                    'options' => ['placeholder' => '请选择'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
            ]);?>
            <?= $form->field($model, 'attr_id')->widget(kartik\select2\Select2::class, [
                    'data' => Yii::$app->services->goodsAttribute->getDropDown(1,2),
                    'options' => ['placeholder' => '请选择'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
            ]);?>
			<div id="box-searchspec-attr_values" style="<?php echo empty($attrValues)?'display:none':''?>">
				<?= $form->field($model, 'attr_values')->checkboxList($attrValues,['prompt'=>'请选择']);?>
			</div>     
            <?= $form->field($model, 'search_type')->radioList(common\enums\SearchTypeEnum::getMap()) ?>
            <?= $form->field($model, 'status')->radioList(common\enums\StatusEnum::getMap())?>
            <?= $form->field($model, 'sort')->textInput() ?> 
                   
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
        <button class="btn btn-primary" type="submit">保存</button>
    </div>
<?php ActiveForm::end(); ?>

<script>
$("#searchspec-attr_id").change(function(){    
	$("#box-searchspec-attr_values").hide();
	var attr_id = $(this).val();	
	if(attr_id){
        $.post("<?php echo Url::to(['ajax-attr-values'])?>",{'id':'<?= $model->id ?>','attr_id':attr_id},function(data){
            if(data) {        
                 $("#searchspec-attr_values").html(data); 
                 $("#box-searchspec-attr_values").show();
            }
        });
	}
});
</script>
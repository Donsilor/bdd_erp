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
$model->modules = $model->modules ? explode(",",$model->modules) : \addons\Style\common\enums\AttrModuleEnum::getKeys();
?>

    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
        <h4 class="modal-title">基本信息</h4>
    </div>
    <div class="modal-body">
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
            <?= $form->field($model, 'modules')->checkboxList(addons\Style\common\enums\AttrModuleEnum::getMap())?>
            <?= $form->field($model, 'input_type')->radioList(common\enums\InputTypeEnum::getMap()) ?>
            <?= $form->field($model, 'is_require')->radioList(common\enums\ConfirmEnum::getMap())?>
            <?= $form->field($model, 'is_inlay')->radioList(addons\Style\common\enums\InlayEnum::getMap())?>
            <?= $form->field($model, 'status')->radioList(common\enums\StatusEnum::getMap())?>
            <?= $form->field($model, 'sort')->textInput() ?> 
                   
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
        <button class="btn btn-primary" type="submit">保存</button>
    </div>
<?php ActiveForm::end(); ?>

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
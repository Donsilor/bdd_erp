<?php
use yii\widgets\ActiveForm;

$this->title = $model->isNewRecord ? '创建' : '编辑';
$this->params['breadcrumbs'][] = ['label' => 'Curd', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <?php $form = ActiveForm::begin([]); ?>
            <div class="box-body">
             <?php 
                 $cate_id = Yii::$app->request->get("cate_id");
                 $_cate_id = Yii::$app->request->get("_cate_id",$cate_id);
                 $model->style_cate_id = $model->style_cate_id ?? $_cate_id;
                ?> 
                 <div class="row">
                 <div class="col-lg-4">         
        			<?= $form->field($model, 'style_cate_id')->dropDownList(\Yii::$app->styleService->styleCate->getGrpDropDown($cate_id),[
        			        'prompt' => '请选择',
        			        'onchange'=>"location.href='?_cate_id='+this.value+'&type_id={$cate_id}'",
        			        'disabled'=>$model->isNewRecord?null:'disabled',
        			]) ?> 
    			</div>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

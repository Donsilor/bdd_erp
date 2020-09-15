<?php
use yii\widgets\ActiveForm;
use common\helpers\Url;
use addons\Style\common\enums\AttrTypeEnum;
use addons\Style\common\forms\StyleAttrForm;
use addons\Style\common\enums\AttrModuleEnum;

$this->title = '编辑';
$this->params['breadcrumbs'][] = ['label' => 'Curd', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$model = $model ?? new StyleAttrForm();
?>

<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <?php $form = ActiveForm::begin([]); ?>
            <div class="box-body" style="padding:20px 50px">
                 <?php               
                    //$attr_list_all = $model->getAttrList(AttrModuleEnum::STYLE);
                    $attr_list_all = \Yii::$app->styleService->attribute->module(AttrModuleEnum::STYLE)->getAttrTypeListByCateId($model->style_cate_id,null,$model->is_inlay);
                    foreach ($attr_list_all as $attr_type=>$attr_list){  
                        ?>
                        <div class="box-header with-border">
                        	<h3 class="box-title" style="font-weight: bold"><?= AttrTypeEnum::getValue($attr_type)?><span style="font-size:12px;color: red">（*点击属性标题即可全选）</span></h3>
                    	</div> 
                        <div class="box-body" style="margin-left:10px;">
                        <?php                          
                          if($attr_type ==1 && $model->style_image) {
                              $_attr_list = [];
                              foreach ($attr_list as $k=>$attr){
                                  if($k == 2) {
                                      $_attr_list[] = $model->style_image;
                                  }
                                  $_attr_list[] = $attr;
                              }
                              $attr_list = $_attr_list;
                          } 
                          foreach ($attr_list as $k=>$attr){ 
                              if($attr_type == 1 && $k==2 && $model->style_image) {
                                  $input = common\helpers\ImageHelper::fancyBox($model->style_image,150,150);
                              }else{
                                  $attr_field = $attr['is_require'] == 1?'attr_require':'attr_custom';                                  
                                  $attr_field_name = "{$attr_field}[{$attr['id']}]";
                                  switch ($attr['input_type']){
                                      case common\enums\InputTypeEnum::INPUT_TEXT :{
                                          $attr_field = 'attr_custom';
                                          $attr_field_name = "{$attr_field}[{$attr['id']}]"; 
                                          $input = $form->field($model,$attr_field_name)->textInput()->label($attr['attr_name']);
                                          break;
                                      }
                                      case common\enums\InputTypeEnum::INPUT_RADIO :{
                                          //通用属性值列表
                                          $attr_values = Yii::$app->styleService->attribute->getValuesByAttrId($attr['id']);   
                                          $input = $form->field($model,$attr_field_name)->radioList($attr_values)->label($attr['attr_name']);
                                          break;
                                      }
                                      case common\enums\InputTypeEnum::INPUT_MUlTI :{
                                          //通用属性值列表
                                          $attr_values = Yii::$app->styleService->attribute->getValuesByAttrId($attr['id']);   
                                          $input = $form->field($model,$attr_field_name)->checkboxList($attr_values)->label($attr['attr_name']);
                                          break;
                                      }
                                      case common\enums\InputTypeEnum::INPUT_MUlTI_RANGE :{
                                          //通用属性值列表
                                          $attr_values = Yii::$app->styleService->attribute->getValuesByAttrId($attr['id']);   
                                          $input = $form->field($model,$attr_field_name)->checkboxList($attr_values)->label($attr['attr_name']);
                                          break;
                                      }
                                      default:{
                                          //通用属性值列表
                                          $attr_values = Yii::$app->styleService->attribute->getValuesByAttrId($attr['id']);   
                                          $input = $form->field($model,$attr_field_name)->dropDownList($attr_values,['prompt'=>'请选择'])->label($attr['attr_name']);
                                          break;
                                      }
                                  }//end switch
                           }
                           $collLg = 4;
                        ?>
                        <?php if ($k % 3 ==0){ ?><div class="row"><?php }?>
        						<div class="col-lg-<?=$collLg?>"><?= $input ?></div>
                        <?php if(($k+1) % 3 == 0 || ($k+1) == count($attr_list)){?></div><?php }?>
                      <?php 
                          }//end foreach $attr_list  
                      ?>
                       </div>
                       <!-- ./box-body -->   
                  <?php         
                    }//end foreach $attr_list_all
                ?>  
            </div>   
            <?php ActiveForm::end(); ?>
        </div>
    </div>
  </div>
<script type="text/javascript">
$(function(){ 
	$(document).on("click",'.control-label',function(){
         var checked = false; 
		 if(!$(this).hasClass('checked')){
			 checked = true;
			 $(this).addClass('checked');
		 }else{
			 $(this).removeClass('checked');
		 }

         $(this).parent().find("input[type*='checkbox']").prop("checked",checked);
	});
});
</script>

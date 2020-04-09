<?php

use common\helpers\Html;
use yii\widgets\ActiveForm;
use common\helpers\Url;
use addons\Style\common\models\Goods;
use addons\Style\common\enums\AttrTypeEnum;

/* @var $this yii\web\View */
/* @var $model addons\Style\common\models\Style */
/* @var $form yii\widgets\ActiveForm */

$this->title = "款式属性";
$this->params['breadcrumbs'][] = ['label' => Yii::t('goods', 'Styles'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?php $form = ActiveForm::begin([
        'id' => $model->formName(),
        'enableAjaxValidation' => true,
        'validationUrl' => Url::to(['edit', 'id' => $model->style_id, 'returnUrl'=>$returnUrl]),       
]); ?>
<div class="box-body nav-tabs-custom">
     <h2 class="page-header">款式详情 - <?php echo $model->style_sn?></h2>
     <?php echo Html::menuTab($tabList,$tab)?>
     <div class="box-body col-lg-12">
       <?php               
        $attr_list_all = \Yii::$app->styleService->attribute->getAttrListByTypeId($model->style_cate_id);
        foreach ($attr_list_all as $attr_type=>$attr_list){
            if($attr_type == AttrTypeEnum::TYPE_SALE){ 
                continue;
            }
            ?>
            <div class="box-header with-border">
            	<h3 class="box-title"><?= AttrTypeEnum::getValue($attr_type)?></h3>
        	</div>
            <div class="box-body" style="margin-left:10px">
              <?php                        
                  foreach ($attr_list as $k=>$attr){ 
                      $attr_field = $attr['is_require'] == 1?'attr_require':'attr_custom';                                  
                      $attr_field_name = "{$attr_field}[{$attr['id']}]";                                  
                      //通用属性值列表
                      $attr_values = Yii::$app->styleService->attribute->getValuesByAttrId($attr['id']);                                  
                      switch ($attr['input_type']){
                          case common\enums\InputTypeEnum::INPUT_TEXT :{
                              $input = $form->field($model,$attr_field_name)->textInput()->label($attr['attr_name']);
                              break;
                          }
                          case common\enums\InputTypeEnum::INPUT_RADIO :{
                              $input = $form->field($model,$attr_field_name)->radioList($attr_values)->label($attr['attr_name']);
                              break;
                          }
                          case common\enums\InputTypeEnum::INPUT_MUlTI :{
                              $input = $form->field($model,$attr_field_name)->checkboxList($attr_values)->label($attr['attr_name']);
                              break;
                          }
                          default:{
                              $input = $form->field($model,$attr_field_name)->dropDownList($attr_values,['prompt'=>'请选择'])->label($attr['attr_name']);
                              break;
                          }
                      }//end switch
            
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
 <!-- ./box-body -->         
    
    <div class="modal-footer">
        <div class="col-sm-10 text-center">
            <button class="btn btn-primary" type="submit">保存</button>
            <span class="btn btn-white" onclick="window.location.href='<?php echo $returnUrl;?>'">返回</span>
        </div>
	</div>
</div>
<?php ActiveForm::end(); ?>

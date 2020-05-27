<?php
use yii\widgets\ActiveForm;
use common\helpers\Html;
use common\helpers\Url;
use addons\Style\common\enums\AttrTypeEnum;
use addons\Purchase\common\enums\PurchaseGoodsTypeEnum;
use addons\Style\common\enums\StyleSexEnum;

$this->title = '申请编辑';
$this->params['breadcrumbs'][] = ['label' => 'Curd', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <?php $form = ActiveForm::begin([]); ?>
            <div class="box-body" style="padding:20px 50px">
                 <?= $form->field($model, 'purchase_id')->hiddenInput()->label(false) ?>  
		         <div class="row">
    			     <div class="col-lg-4">         
            			<?= $form->field($model, 'goods_sn')->textInput(['disabled'=>'disabled']) ?> 
        			 </div>
    			     <div class="col-lg-4">
                        <?= $form->field($model, 'goods_type')->dropDownList(PurchaseGoodsTypeEnum::getMap(),['disabled'=>true]) ?> 
        			 </div>  
    			     <div class="col-lg-4">
        			 	<?= $form->field($model, 'style_sex')->dropDownList(StyleSexEnum::getMap(),['disabled'=>true]) ?>
        			 </div>        			               			
        			 <div class="col-lg-4">       			 
        			 	<?= $form->field($model, 'style_cate_id')->dropDownList(Yii::$app->styleService->styleCate->getDropDown(),['disabled'=>true]) ?>
        			 </div>
        			 <div class="col-lg-4">
        			 	<?= $form->field($model, 'product_type_id')->dropDownList(Yii::$app->styleService->productType->getDropDown(),['disabled'=>true]) ?>
        			 </div> 
        			 <div class="col-lg-4">
        			 	<?= $form->field($model, 'jintuo_type')->dropDownList(\addons\Style\common\enums\JintuoTypeEnum::getMap(),['disabled'=>true]) ?>
        			 </div>           			 
    			 </div> 
    			 <div class="row">
        			 <div class="col-lg-4">
                            <?= $form->field($model, 'goods_name')->textInput() ?> 
        			 </div>
        			 <div class="col-lg-4">
        			 	<?= $form->field($model, 'goods_num')->textInput() ?>
        			 </div>
        			 <div class="col-lg-4">
        			 	<?= $form->field($model, 'cost_price')->textInput() ?>
        			 </div> 
    			 </div>
            	<?php
            	  $attr_list = $model->getAttrList();
            	  foreach ($attr_list as $k=>$attr){ 
                      $attr_id  = $attr['attr_id'];//属性ID                      
                      $attr_values = $attr['attr_values'];//属性值
                      $is_require = $attr['is_require'];                     
                      $attr_name = \Yii::$app->attr->attrName($attr_id);//属性名称
                      
                      $_field = $is_require == 1 ? 'attr_require':'attr_custom';
                      $field = "{$_field}[{$attr_id}]";
                      switch ($attr['input_type']){
                          case common\enums\InputTypeEnum::INPUT_TEXT :{
                              $input = $form->field($model,$field)->textInput()->label($attr_name);
                              break;
                          }  
                          case common\enums\InputTypeEnum::INPUT_MUlTI_RANGE: {
                              $input = $form->field($model,$field)->textInput()->label($attr_name);
                              break;
                          }
                          default:{                               
                              if($attr_values == '') {
                                  $attr_values = Yii::$app->styleService->attribute->getValuesByAttrId($attr_id);
                              }else {
                                  $attr_values = Yii::$app->styleService->attribute->getValuesByValueIds($attr_values);
                              }
                              $input = $form->field($model,$field)->dropDownList($attr_values,['prompt'=>'请选择'])->label($attr_name);
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
               <!-- ./box-body -->
                   <div style="margin: 0px 0 20px 0;">
                        <h3 class="box-title"> 其他信息</h3>
                    </div>
                    <div class="row">
                        <div class="col-lg-4">
                            <?= $form->field($model, 'main_stone_price')->textInput() ?>
                        </div>
                        <div class="col-lg-4">
                            <?= $form->field($model, 'second_stone_price1')->textInput() ?>
                        </div>
                        <div class="col-lg-4">
                            <?= $form->field($model, 'second_stone_price2')->textInput() ?>
                        </div>                        
                    </div>
                    <div class="row">                        
                        <div class="col-lg-4">
                            <?= $form->field($model, 'gold_price')->textInput() ?>
                        </div>
                        <div class="col-lg-4">
                            <?= $form->field($model, 'gold_cost_price')->textInput() ?>
                        </div>
                        <div class="col-lg-4">
                            <?= $form->field($model, 'gold_loss')->textInput() ?>
                        </div>
                        
                    </div>
                    <div class="row">
                        <div class="col-lg-4">
                            <?= $form->field($model, 'jiagong_fee')->textInput() ?>
                        </div>
                        <div class="col-lg-4">
                            <?= $form->field($model, 'xiangqian_fee')->textInput() ?>
                        </div>
                        <div class="col-lg-4">
                            <?= $form->field($model, 'gong_fee')->textInput() ?>
                        </div>                        
                    </div>
                    <div class="row">
                        <div class="col-lg-4">
                            <?= $form->field($model, 'gaitu_fee')->textInput() ?>
                        </div>
                        <div class="col-lg-4">
                            <?= $form->field($model, 'penla_fee')->textInput() ?>
                        </div>  
                        <div class="col-lg-4">
                            <?= $form->field($model, 'unit_cost_price')->textInput() ?>
                        </div>                      
                    </div>
                    <div class="row">                        
                        <div class="col-lg-4">
                            <?= $form->field($model, 'factory_cost_price')->textInput() ?>
                        </div>
                    </div>
                <div class="row">
                    <div class="col-lg-4">
                        <?= $form->field($model, 'stone_info')->textarea() ?>
                    </div>                   
                    <div class="col-lg-4">
                        <?= $form->field($model, 'parts_info')->textarea() ?>
                    </div>
                     <div class="col-lg-4">
                		<?= $form->field($model, 'remark')->textarea() ?>
                	</div>
            	</div> 
            	
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
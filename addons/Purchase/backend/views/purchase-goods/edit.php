<?php
use yii\widgets\ActiveForm;
use common\helpers\Html;
use common\helpers\Url;
use addons\Style\common\enums\AttrTypeEnum;
use addons\Purchase\common\enums\PurchaseGoodsTypeEnum;
use addons\Style\common\enums\StyleSexEnum;

$this->title = $model->isNewRecord ? '创建' : '编辑';
$this->params['breadcrumbs'][] = ['label' => 'Curd', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-lg-14">
        <div class="box">
            <?php $form = ActiveForm::begin([]); ?>
            <div class="box-body" style="padding:20px 50px">
                 <?= $form->field($model, 'purchase_id')->hiddenInput()->label(false) ?>  
                 <div class="row"> 
    			     <?php if($model->style_id) {?> 
    			         <?php if($model->isNewRecord) {?>      			    
            			 <div class="col-lg-3">         
                			<?= $form->field($model, 'style_sn')->textInput() ?> 
            			 </div>
            			 <div class="col-lg-1">
                            <?= Html::button('查询',['class'=>'btn btn-info btn-sm','style'=>'margin-top:25px;','onclick'=>"searchGoods()"]) ?>
        			     </div>
        			     <?php }else{?>
        			     <div class="col-lg-4">         
                			<?= $form->field($model, 'style_sn')->textInput(['disabled'=>true]) ?> 
            			 </div>
        			     <?php }?>
        			     <div class="col-lg-4">
                                <?= $form->field($model, 'goods_type')->dropDownList(PurchaseGoodsTypeEnum::getMap(),['disabled'=>true]) ?> 
            			 </div>
            			 <div class="col-lg-4">
                                <?= $form->field($model, 'goods_name')->textInput() ?> 
            			 </div> 
            			
            			 <div class="col-lg-4">       			 
            			 	<?= $form->field($model, 'style_cate_id')->dropDownList(Yii::$app->styleService->styleCate->getDropDown(),['disabled'=>true]) ?>
            			 </div>
            			 <div class="col-lg-4">
            			 	<?= $form->field($model, 'product_type_id')->dropDownList(Yii::$app->styleService->productType->getDropDown(),['disabled'=>true]) ?>
            			 </div> 
            			 <div class="col-lg-4">
            			 	<?= $form->field($model, 'style_sex')->dropDownList(StyleSexEnum::getMap(),['disabled'=>true]) ?>
            			 </div>
            			 <div class="col-lg-4">
            			 	<?= $form->field($model, 'goods_num')->textInput() ?>
            			 </div>
            			 <div class="col-lg-4">
            			 	<?= $form->field($model, 'cost_price')->textInput() ?>
            			 </div> 
            			               			 
        			 <?php }else{?>
            			 <div class="col-lg-6">         
                			<?= $form->field($model, 'style_sn')->textInput() ?> 
            			 </div>
            			 <div class="col-lg-1">
                            <?= Html::button('查询',['class'=>'btn btn-info btn-sm','style'=>'margin-top:25px;','onclick'=>"searchGoods()"]) ?>
        			     </div>
        			 <?php }?>        			 
    			 </div>
            	<?php               
                $attr_list_all = \Yii::$app->styleService->attribute->getAttrListByCateId($model->style_cate_id,null,1);
                foreach ($attr_list_all as $attr_type=>$attr_list){
                    if(in_array($attr_type ,[AttrTypeEnum::TYPE_SALE,AttrTypeEnum::TYPE_EXTEND])){
                        continue;
                    }
                    ?>        
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
            
                    <!-- ./box-body -->
                    <?php 
                }//end foreach $attr_list_all
                ?> 
                <div class="row">
                    <div class="col-lg-8">
                		<?= $form->field($model, 'remark')->textarea() ?>
                	</div>
            	</div> 
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
<script type="text/javascript">
function searchGoods() {
   var style_sn = $.trim($("#purchasegoodsform-style_sn").val());
   if(!style_sn) {
        alert("请输入款号或起版号");
        return false;
   }
   var url = "<?= Url::buildUrl(\Yii::$app->request->url,[],['style_sn','search'])?>&search=1&style_sn="+style_sn;
   window.location.href = url;
}
</script>

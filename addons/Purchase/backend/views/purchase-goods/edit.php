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
    <div class="col-lg-12">
        <div class="box">
            <?php $form = ActiveForm::begin([]); ?>
            <div class="box-body" style="padding:20px 50px">
                 <?= $form->field($model, 'purchase_id')->hiddenInput()->label(false) ?>  

			     <?php if($model->style_id) {?> 
			         <div class="row">
    			         <?php if($model->isNewRecord) {?>      			    
            			 <div class="col-lg-3">         
                			<?= $form->field($model, 'style_sn')->textInput() ?> 
            			 </div>
            			 <div class="col-lg-1">
                            <?= Html::button('查询',['class'=>'btn btn-info btn-sm','style'=>'margin-top:27px;','onclick'=>"searchGoods()"]) ?>
        			     </div>
        			     <?php }else{?>
        			     <div class="col-lg-4">         
                			<?= $form->field($model, 'style_sn')->textInput(['disabled'=>'disabled']) ?> 
            			 </div>
        			     <?php }?>
        			     <div class="col-lg-4">
            			 	<?= $form->field($model, 'style_sex')->dropDownList(StyleSexEnum::getMap(),['disabled'=>true]) ?>
            			 </div>
        			     <div class="col-lg-4">
                                <?= $form->field($model, 'goods_type')->dropDownList(PurchaseGoodsTypeEnum::getMap(),['disabled'=>true]) ?> 
            			 </div>            			
            			 <div class="col-lg-4">       			 
            			 	<?= $form->field($model, 'style_cate_id')->dropDownList(Yii::$app->styleService->styleCate->getDropDown(),['disabled'=>true]) ?>
            			 </div>
            			 <div class="col-lg-4">
            			 	<?= $form->field($model, 'product_type_id')->dropDownList(Yii::$app->styleService->productType->getDropDown(),['disabled'=>true]) ?>
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
    			<?php }else{?>
        			<div class="row">
            			 <div class="col-lg-4">         
                			<?= $form->field($model, 'style_sn')->textInput() ?> 
            			 </div>
            			 <div class="col-lg-1">
                            <?= Html::button('查询',['class'=>'btn btn-info btn-sm','style'=>'margin-top:27px;','onclick'=>"searchGoods()"]) ?>
        			     </div>
        			</div>
    			<?php }?>        			 

            	<?php   
            	  if($model->goods_type == PurchaseGoodsTypeEnum::STYLE) {
                        $attr_list = \Yii::$app->styleService->styleAttribute->getStyleAttrList($model->style_id);
            	  }else{
            	        $attr_list = \Yii::$app->styleService->qibanAttribute->getQibanAttrList($model->style_id);
            	  }
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
                <?php if($model->style_id) {?>
                <div class="row">
                    <div class="col-lg-8">
                		<?= $form->field($model, 'remark')->textarea() ?>
                	</div>
            	</div> 
            	<?php }?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
<script type="text/javascript">
function searchGoods() {
   var style_sn = $.trim($("#purchasegoodsform-style_sn").val());
   if(!style_sn) {
	    rfMsg("请输入款号或起版号");
        return false;
   }
   var url = "<?= Url::buildUrl(\Yii::$app->request->url,[],['style_sn','search'])?>&search=1&style_sn="+style_sn;
   window.location.href = url;
}
</script>

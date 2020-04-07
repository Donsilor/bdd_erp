<?php

use common\helpers\Html;
use yii\widgets\ActiveForm;
use common\widgets\langbox\LangBox;
use yii\base\Widget;

use common\helpers\Url;
use common\enums\StatusEnum;
use common\helpers\AmountHelper;
use common\enums\AreaEnum;
use addons\Style\common\models\Goods;
use addons\Style\common\enums\AttrTypeEnum;

/* @var $this yii\web\View */
/* @var $model addons\Style\common\models\Style */
/* @var $form yii\widgets\ActiveForm */

$this->title = Yii::t('goods', 'Style');
$this->params['breadcrumbs'][] = ['label' => Yii::t('goods', 'Styles'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<?php $form = ActiveForm::begin([
        'id' => $model->formName(),
        'enableAjaxValidation' => true,
        'validationUrl' => Url::to(['ajax-edit', 'id' => $model->id]),       
]); ?>
<div class="box-body nav-tabs-custom">
     <h2 class="page-header">款式发布</h2>
     <?php echo Html::menuTab($tabList,$tab)?>         
      <div class="row nav-tabs-custom tab-pane tab0 active">
            <ul class="nav nav-tabs pull-right">
              <li class="pull-left header"><i class="fa fa-th"></i> <?= $tabList[$tab]['name']??'';?></li>
            </ul>
            <div class="box-body col-lg-12">
               <?php               
                $attr_list_all = \Yii::$app->styleService->attribute->getAttrListByTypeId($model->type_id);
                if(!isset($attr_list_all[AttrTypeEnum::TYPE_SALE])){
                    $attr_list_all[AttrTypeEnum::TYPE_SALE] = [];
                }
                foreach ($attr_list_all as $attr_type=>$attr_list){
                    ?>
                    <div class="box-header with-border">
                    	<h3 class="box-title"><?= AttrTypeEnum::getValue($attr_type)?></h3>
                	</div>
                    <div class="box-body" style="margin-left:10px">
                      <?php
                      //如果是销售属性
                      if($attr_type == AttrTypeEnum::TYPE_SALE){
                          ?>
                            <div class="row">
                                <div class="col-lg-4"><?= $form->field($model, 'sale_price')->textInput(['maxlength'=>true]) ?></div>
                                <div class="col-lg-4"><?=  $form->field($model, 'cost_price')->textInput(['maxlength'=>true]) ?></div>
                                <div class="col-lg-4"><?= $form->field($model, 'market_price')->textInput(['maxlength'=>true]) ?></div>
                            </div> 
   							<div class="row">
   							    <div class="col-lg-4"><?=  $form->field($model, 'goods_storage')->textInput(['maxlength'=>true]) ?></div>
   							    
                            </div> 
                          <?php 
                          $data = [];                          
                          foreach ($attr_list as $k=>$attr){   
                              $values = Yii::$app->styleService->attribute->getValuesByAttrId($attr['id']);
                              $data[] = [
                                  'id'=>$attr['id'],
                                  'name'=>$attr['attr_name'],
                                  'value'=>Yii::$app->styleService->attribute->getValuesByAttrId($attr['id']),
                                  'current'=>$model->style_spec['a'][$attr['id']]??[]
                              ];   
                          }
                         
                          if(!empty($data)){
                             echo common\widgets\skutable\SkuTable::widget(['form' => $form,'model' => $model,'data' =>$data,'name'=>'Style[style_spec]']);
                             ?>
                             <script type="text/javascript">
                                 $(function(){  
                                  	$('form#Style').on('submit', function (e) {
                                		var r = checkSkuInputData();
                                    	if(!r){
                                        	e.preventDefault();
                                    	}
                                    });
                                 });
                             </script>
                             <?php 
                          }
                      }else{                              
                              foreach ($attr_list as $k=>$attr){ 
                                  $attr_field = $attr['is_require']==1?'attr_require':'attr_custom';                                  
                                  $attr_field_name = "{$attr_field}[{$attr['id']}]";                                  
                                  $model->{$attr_field} = $model->style_attr;//$style_attr[$attr['id']]??'';
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
                      ?>
                           <?php 
                           $collLg = 4;
                           /* if($attr_type == common\enums\AttrTypeEnum::TYPE_SERVER){
                                $collLg = 12;
                           } */?>
                              <?php if ($k % 3 ==0){ ?><div class="row"><?php }?>
    							<div class="col-lg-<?=$collLg?>"><?= $input ?></div>
                              <?php if(($k+1) % 3 == 0 || ($k+1) == count($attr_list)){?></div><?php }?>
                      <?php 
                              }//end foreach $attr_list
                              $show_storage = empty($attr_list)?true:false; 
                       }?>
                    </div>
                    <!-- ./box-body -->
                    <?php 
                }//end foreach $attr_list_all
                ?>  
           </div>  
      	 <!-- ./box-body -->          
      </div>    
    </div>
    <div class="modal-footer">
        <div class="col-sm-10 text-center">
            <button class="btn btn-primary" type="submit">保存</button>
            <span class="btn btn-white" onclick="history.go(-1)">返回</span>
        </div>
	</div>
</div>

<?php ActiveForm::end(); ?>
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
	//批量商品编码复制
	$(document).on("click",'.batch-goods_sn',function(){
		var hasEdit = false;
		var fromValue = $("#style-style_sn").val();
		if(fromValue ==""){
             alert("<?= Yii::t("goods","请先填写款式编号")?>");
             return false;
		}
		$("#skuTable tr[class*='sku_table_tr']").each(function(){
			var skuValue = $(this).find(".setsku-goods_sn").val();
        	if(skuValue != '' && skuValue != fromValue){
        		hasEdit = true;
        		return ;
        	}
        });
        if(hasEdit === true){
           	 if(!confirm("<?= Yii::t("goods","商品编码已修改过,是否覆盖")?>?")){
               	return false;
           	 }
        }
    	$("#skuTable tr[class*='sku_table_tr']").each(function(){
        	if($(this).find(".setsku-status").val() == 1){
        		$(this).find(".setsku-goods_sn").val(fromValue);
        	}
        });

	});

	$(document).on("click",'.batch-market_price',function(){
		var hasEdit = false;
		var fromValue = $("#style-market_price").val();
		if(fromValue ==""){
             alert("<?= Yii::t("goods","请先填写市场价")?>");
             return false;
		}
		$("#skuTable tr[class*='sku_table_tr']").each(function(){
			var skuValue = $(this).find(".setsku-market_price").val();
        	if(skuValue != '' && skuValue != fromValue){
        		hasEdit = true;
        		return ;
        	}
        });
        if(hasEdit === true){
           	 if(!confirm("<?= Yii::t("goods","市场价已修改过,是否覆盖")?>?")){
               	return false;
           	 }
        }
    	$("#skuTable tr[class*='sku_table_tr']").each(function(){
        	if($(this).find(".setsku-status").val() == 1){
        		$(this).find(".setsku-market_price").val(fromValue);
        	}
        });
	});
	//销售价批量填充
	$(document).on("click",'.batch-sale_price',function(){
		var hasEdit = false;
		var fromValue = $("#style-sale_price").val();
		if(fromValue ==""){
             alert("<?= Yii::t("goods","请先填写销售价")?>");
             return false;
		}
		$("#skuTable tr[class*='sku_table_tr']").each(function(){
			var skuValue = $(this).find(".setsku-sale_price").val();
        	if(skuValue != '' && skuValue != fromValue){
        		hasEdit = true;
        		return ;
        	}
        });
        if(hasEdit === true){
           	 if(!confirm("<?= Yii::t("goods","销售价已修改过,是否覆盖")?>?")){
               	return false;
           	 }
        }
    	$("#skuTable tr[class*='sku_table_tr']").each(function(){
        	if($(this).find(".setsku-status").val() == 1){
        		$(this).find(".setsku-sale_price").val(fromValue);
        	}
        });
	});
	//成本价批量填充
	$(document).on("click",'.batch-cost_price',function(){
		var hasEdit = false;
		var fromValue = $("#style-cost_price").val();
		if(fromValue ==""){
             alert("<?= Yii::t("goods","请先填写成本价")?>");
             return false;
		}
		$("#skuTable tr[class*='sku_table_tr']").each(function(){
			var skuValue = $(this).find(".setsku-cost_price").val();
        	if(skuValue != '' && skuValue != fromValue){
        		hasEdit = true;
        		return ;
        	}
        });
        if(hasEdit === true){
           	 if(!confirm("<?= Yii::t("goods","销售价已修改过,是否覆盖")?>?")){
               	return false;
           	 }
        }
    	$("#skuTable tr[class*='sku_table_tr']").each(function(){
        	if($(this).find(".setsku-status").val() == 1){
        		$(this).find(".setsku-cost_price").val(fromValue);
        	}
        });
	});
	//库存批量填充
	$(document).on("click",'.batch-goods_storage',function(){
		var hasEdit = false;
		var fromValue = $("#style-goods_storage").val();		
		if(fromValue = prompt("<?= Yii::t("goods","请输入库存数量")?>","10")){
			var r = /^\+?[1-9][0-9]*$/;
			if(!r.test(fromValue)) {
                 alert("<?= Yii::t("goods","库存数量不合法")?>");
                 return false;
			}
		}else {
            return false; 
		}
		$("#skuTable tr[class*='sku_table_tr']").each(function(){
			var skuValue = $(this).find(".setsku-goods_storage").val();
        	if(skuValue != '' && skuValue != fromValue){
        		hasEdit = true;
        		return ;
        	}
        });
        if(hasEdit === true){
           	 if(!confirm("<?= Yii::t("goods","商品库存已修改过,是否覆盖")?>?")){
               	return false;
           	 }
        }
    	$("#skuTable tr[class*='sku_table_tr']").each(function(){
        	if($(this).find(".setsku-status").val() == 1){
        		$(this).find(".setsku-goods_storage").val(fromValue);
        	}
        });
        goodsStroageSum();
	});
	$(document).on("blur",'.setsku-goods_storage',function(){
    	goodsStroageSum();
	});
	$(document).on("click",'.sku-status',function(){
    	goodsStroageSum();
	});	
	function goodsStroageSum(){
		var total = 0;
		$("#skuTable tr[class*='sku_table_tr']").each(function(){
        	if($(this).find(".setsku-status").val() == 1){
        		var storage = $(this).find(".setsku-goods_storage").val();
        		if(parseInt(storage)){
        			total += parseInt(storage);
        		}
        	}
        }); 
		$("#style-goods_storage").val(total).attr('readonly',true);
        return total; 
	}

});
</script>
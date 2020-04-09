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
        'validationUrl' => Url::to(['edit-info', 'id' => $model->id,'returnUrl'=>$returnUrl]),       
]); ?>
<div class="box-body nav-tabs-custom">
     <h2 class="page-header">款式详情 - <?php echo $model->style_sn?></h2>
     <?php echo Html::menuTab($tabList,$tab)?>
     <div class="tab-content">     
       <div class="row nav-tabs-custom">
            <div class="box-header with-border">
                 <h3 class="box-title">基础信息</h3>
            </div>
            <div class="box-body col-sm-10" style="margin-left:9px">                
       			<div class="row">
                    <div class="col-lg-6"><?= $form->field($model, 'style_sn')->textInput(['disabled'=>$model->isNewRecord?null:'disabled'])?></div>
                    <div class="col-lg-6"><?= $form->field($model, 'style_name')->textInput()?></div>
                </div>
    			<div class="row">
                    <div class="col-lg-6">
                    <?= $form->field($model, 'style_cate_id')->widget(\kartik\select2\Select2::class, [
                        'data' => \Yii::$app->styleService->styleCate->getGrpDropDown(),
                        'options' => ['placeholder' => '请选择'],
                        'pluginOptions' => [
                            'allowClear' => false,
                            'disabled'=>$model->isNewRecord?null:'disabled'
                        ],
                    ]);?>
                    </div>
                    <div class="col-lg-6">
                    <?= $form->field($model, 'product_type_id')->widget(\kartik\select2\Select2::class, [
                        'data' => \Yii::$app->styleService->productType->getGrpDropDown(),
                        'options' => ['placeholder' => '请选择'],
                        'pluginOptions' => [
                            'allowClear' => false,
                            'disabled'=>$model->isNewRecord?null:'disabled'
                        ],
                    ]);?>                
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                    <?= $form->field($model, 'style_source_id')->widget(\kartik\select2\Select2::class, [
                        'data' => \Yii::$app->styleService->styleSource->getdropDown(),
                        'options' => ['placeholder' => '请选择'],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ]);?>
                    </div>
                    <div class="col-lg-6">
                    <?= $form->field($model, 'style_channel_id')->widget(\kartik\select2\Select2::class, [
                        'data' => \Yii::$app->styleService->styleChannel->getdropDown(),
                        'options' => ['placeholder' => '请选择'],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ]);?>                
                    </div>
                </div>
       		    <div class="row">
                    <div class="col-lg-6"><?= $form->field($model, 'style_sex')->radioList(\addons\Style\common\enums\StyleSexEnum::getMap())?></div>
                    <div class="col-lg-6"><?= $form->field($model, 'is_made')->radioList(\common\enums\ConfirmEnum::getMap())?></div>
                </div>
          
                <?= $form->field($model, 'remark')->textArea(['options'=>['maxlength' => true]])?>
          
      
         </div>
        <!-- ./box-body -->
      </div>          
      
    </div>
    <div class="modal-footer">
        <div class="col-sm-10 text-center">
            <button class="btn btn-primary" type="submit">保存</button>
            <span class="btn btn-white" onclick="window.location.href='<?php echo $returnUrl;?>'">返回</span>
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
	/*
	//基础信息销售价计算
	function salePriceCalc(){
		var priceList = [];
		var minPrice = 0;
		var hasOne = false;	
		$("#skuTable tr[class*='sku_table_tr']").each(function(){			
        	if($(this).find(".setsku-status").val() == 1 && (salePrice = $(this).find(".setsku-sale_price").val())){
        		priceList.push(salePrice);
        	}
        }); 
        if(!priceList){
        	$("#style-sale_price").val().attr('readonly',false);
            return minPrice;
        }
        priceList.sort(function(v1,v2){return v1-v2;});  
        minPrice = priceList[0];  
		$("#style-sale_price").val(minPrice).attr('readonly',true);
        return minPrice; 
	}
	//基础信息销售价计算
	function marketPriceCalc(){
		var priceList = [];
		var maxPrice = 0;
		$("#skuTable tr[class*='sku_table_tr']").each(function(){			
        	if($(this).find(".setsku-status").val() == 1 && (price = $(this).find(".setsku-market_price").val())){
        		priceList.push(price);
        	}
        }); 
        if(!priceList){
        	$("#style-market_price").val().attr('readonly',false);
            return minPrice;
        }
        priceList.sort(function(v1,v2){return v2-v1;});  
        minPrice = priceList[0];  
		//$("#style-market_price").val(minPrice).attr('readonly',true);
        return minPrice; 
	} */

});
</script>
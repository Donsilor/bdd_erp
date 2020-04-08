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

$this->title = "商品编辑";
$this->params['breadcrumbs'][] = ['label' => Yii::t('goods', 'Styles'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?php $form = ActiveForm::begin([
        'id' => $model->formName(),
        'enableAjaxValidation' => true,
        'validationUrl' => Url::to(['ajax-edit', 'id' => $model->style_id]),       
]); ?>
<div class="box-body nav-tabs-custom">
     <h2 class="page-header">款式发布</h2>
     <?php echo Html::menuTab($tabList,$tab)?>
     <div class="tab-content">     
       <div class="row nav-tabs-custom tab-pane tab0 active">
            <ul class="nav nav-tabs pull-right">
              <li class="pull-left header"><i class="fa fa-th"></i> <?= $tabList[$tab]['name']??'';?></li>
            </ul>
            <div class="box-body col-lg-14">
               <?php               
                $attr_list_all = \Yii::$app->styleService->attribute->getAttrListByTypeId($model->style_cate_id);
                foreach ($attr_list_all as $attr_type=>$attr_list){
                    if($attr_type != AttrTypeEnum::TYPE_SALE){
                        continue;
                    }
                    ?>
                    <div class="box-header with-border">
                    	<h3 class="box-title"><?= AttrTypeEnum::getValue($attr_type)?></h3>
                	</div>
                    <div class="box-body" style="margin-left:10px">   
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
                             echo common\widgets\skutable\SkuTable::widget(['form' => $form,'model' => $model,'data' =>$data,'name'=>'StyleGoods']);
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
                       ?>
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
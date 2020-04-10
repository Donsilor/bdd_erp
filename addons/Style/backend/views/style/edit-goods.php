<?php

use common\helpers\Html;
use yii\widgets\ActiveForm;
use yii\base\Widget;

use common\helpers\Url;
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
        'validationUrl' => Url::to(['edit-goods', 'id' => $model->style_id,'returnUrl'=>$returnUrl]),       
]); ?>
<div class="box-body nav-tabs-custom">
     <h2 class="page-header">款式详情 - <?php echo $model->style_sn?></h2>
     <?php echo Html::menuTab($tabList,$tab)?>
    <div class="box-body">
       <?php   
        $inputs = \Yii::$app->styleService->styleGoods->getSKuTableInputs($model->style_cate_id);
        $attr_list_all = \Yii::$app->styleService->attribute->getAttrListByCateId($model->style_cate_id,AttrTypeEnum::TYPE_SALE);
        foreach ($attr_list_all as $attr_type=>$attr_list){
            ?>
            <div class="box-header with-border">
                  <h3 class="box-title"><?= AttrTypeEnum::getValue($attr_type)?></h3>
            </div>
            <div class="box-body">   
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
                      echo common\widgets\skutable\SkuTable::widget(['form' => $form,'model' => $model,'inputs'=>$inputs,'data' =>$data,'name'=>'StyleGoodsForm[style_spec]']);
                     ?>
                     <script type="text/javascript">
                         $(function(){  
                          	$('form#StyleGoodsForm').on('submit', function (e) {
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
	//成本价批量填充
	$(document).on("click",'.batch-cost_price',function(){
		batchFillDouble('cost_price','成本价','');
	});
	//副石1重量
	$(document).on("click",'.batch-second_stone_weight1',function(){
		batchFillDouble('second_stone_weight1','副石1重量','');
	});
	//副石2重量
	$(document).on("click",'.batch-second_stone_weight2',function(){
		batchFillDouble('second_stone_weight2','副石2重量','');
	});
	//副石1数量
	$(document).on("click",'.batch-second_stone_num1',function(){
		batchFillInteger('second_stone_num1','副石1数量','');
	});
	//副石2数量
	$(document).on("click",'.batch-second_stone_num2',function(){
		batchFillInteger('second_stone_num2','副石2数量','');
	});
    //18K标准金重
	$(document).on("click",'.batch-g18k_weight',function(){
		batchFillDouble('g18k_weight','18K标准金重','');
	});
	//18K上下公差
	$(document).on("click",'.batch-g18k_diff',function(){
		batchFillDouble('g18k_diff','18K上下公差','');
	});
	//PT950标准金重
	$(document).on("click",'.batch-pt950_weight',function(){
		batchFillDouble('pt950_weight','PT950标准金重','');
	});
	//PT950上下公差
	$(document).on("click",'.batch-pt950_diff',function(){
		batchFillDouble('pt950_diff','PT950上下公差','');
	});
	//银标准金重
	$(document).on("click",'.batch-silver_weight',function(){
		batchFillDouble('silver_weight','银标准金重','');
	});
	//银上下公差
	$(document).on("click",'.batch-silver_diff',function(){
		batchFillDouble('silver_diff','银上下公差','');
	});
	//改圈范围
	$(document).on("click",'.batch-finger_range',function(){
		batchFillDouble('finger_range','改圈范围','');
	});
	   
	//批量填充整数类型文本框
	function batchFillInteger(inputName,title,defaultValue){
		var hasEdit = false;	
		if(fromValue = prompt("请输入【"+title+"】(大于等于0的整数)",defaultValue)){
			var r = /^\+?[1-9][0-9]*$/;
			if(!r.test(fromValue)) {
                 alert("【"+title+"】不合法!");
                 return false;
			}
		}else {
            return false; 
		}
		$("#skuTable tr[class*='sku_table_tr']").each(function(){
			var skuValue = $(this).find(".setsku-"+inputName).val();
        	if(skuValue != '' && skuValue != fromValue){
        		hasEdit = true;
        		return ;
        	}
        });
        if(hasEdit === true){
           	 if(!confirm("【"+title+"】已修改过,是否覆盖?")){
               	return false;
           	 }
        }
    	$("#skuTable tr[class*='sku_table_tr']").each(function(){
        	if($(this).find(".setsku-status").val() == 1){
        		$(this).find(".setsku-"+inputName).val(fromValue);
        	}
        });    
    }
    //批量填充数字类型文本框
	function batchFillDouble(inputName,title,defaultValue){
		var hasEdit = false;
		if(fromValue = prompt("请输入【"+title+"】(大于等于0的数字)",defaultValue)){
			var r = /^\d+(\.\d+)?$/;
			if(!r.test(fromValue)) {
				 alert("【"+title+"】不合法!");
                 return false;
			}
		}else {
            return false; 
		}
		$("#skuTable tr[class*='sku_table_tr']").each(function(){
			var skuValue = $(this).find(".setsku-"+inputName).val();
        	if(skuValue != '' && skuValue != fromValue){
        		hasEdit = true;
        		return ;
        	}
        });
        if(hasEdit === true){
           	 if(!confirm("【"+title+"】已修改过,是否覆盖?")){
               	return false;
           	 }
        }
    	$("#skuTable tr[class*='sku_table_tr']").each(function(){
        	if($(this).find(".setsku-status").val() == 1){
        		$(this).find(".setsku-"+inputName).val(fromValue);
        	}
        });    
    }
});
</script>
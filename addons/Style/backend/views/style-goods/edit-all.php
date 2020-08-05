<?php

use common\helpers\Html;
use yii\widgets\ActiveForm;
use yii\base\Widget;

use common\helpers\Url;
use addons\Style\common\models\Goods;
use addons\Style\common\enums\AttrTypeEnum;
use addons\Style\common\forms\StyleGoodsForm;
use common\enums\AuditStatusEnum;

/* @var $this yii\web\View */
/* @var $model addons\Style\common\models\Style */
/* @var $form yii\widgets\ActiveForm */

$this->title = "商品编辑";
$this->params['breadcrumbs'][] = ['label' => Yii::t('goods', 'Styles'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$model = $model ?? new StyleGoodsForm();
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
            <div class="box-header with-border">
                  <h3 class="box-title"><?= AttrTypeEnum::getValue(AttrTypeEnum::TYPE_SALE)?></h3>
            </div>
            <div class="box-body">   
                <?php 
                  $inputs = $model->getSKuTableInputs();
                  $data = array();                          
                  foreach ($model->getSaleAttrList() as $k=>$attr){   
                      $data[] = [
                          'id'=>$attr['attr_id'],
                          'name'=>Yii::$app->attr->attrName($attr['attr_id']),
                          'value'=>Yii::$app->styleService->attribute->getValuesByValueIds($attr['attr_values']),
                          'current'=>$model->style_spec['a'][$attr['attr_id']]??[]
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
	<?php 
	foreach ($inputs as $input) {
	    if(empty($input['batch'])) continue;
	    ?>
		//<?= $input['title']?>批量填充
		$(document).on("click",'.batch-<?= $input['name']?>',function(){
			<?php if($input['dtype']=='double') {?>
			batchFillDouble('<?= $input['name']?>','<?= $input['title']?>','');
			<?php }else if($input['dtype']=='integer'){?>
			batchFillInteger('<?= $input['name']?>','<?= $input['title']?>','');
			<?php }?>
		});
	    <?php 	    
	}?>	   
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


    $(document).on("click",'.sku_type li',function(){
        var checked = false;
        if(!$(this).hasClass('checked')){
            checked = true;
            $(this).addClass('checked');
        }else{
            $(this).removeClass('checked');
        }

        $(this).parent().next().find("input[type*='checkbox']").prop("checked",checked);
    });
});
</script>
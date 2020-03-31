<?php 
  $newLangModel = $model->langModel();
  foreach (\Yii::$app->params['languages'] as $lang_key=>$lang_name){
     $is_new = true;    
  ?>                        		
    <?php foreach ($model->langs as $langModel) {?>
        <?php if($lang_key == $langModel->language){?>
        	<!-- 编辑-->
            <div class="tab-pane<?php echo Yii::$app->language==$lang_key?" active":"" ?>" id="<?= $tab.'_'.$lang_key?>">
			<?php 
			foreach ($fields as $attribute =>$val){
			    if($val['type'] == "widget") {
			        echo $form->field($langModel,"[{$lang_key}]".$attribute)->{$val['type']}($val['class'],$val['options']);
	    	    }else{				    
	    	        echo $form->field($langModel,"[{$lang_key}]".$attribute)->{$val['type']}($val['options'])->label($val['label']) ;
			    }
            }?>
          	</div>
          	<!-- /.tab-pane -->
        	<?php $is_new = false; break;?>
        <?php }?>
    <?php }?>
    <?php if($is_new == true){?>
    <!-- 新增 -->
    <div class="tab-pane<?php echo Yii::$app->language==$lang_key?" active":"" ?>" id="<?= $tab.'_'.$lang_key?>">
        <?php 
        foreach ($fields as $attribute =>$val){
            if($val['type'] == "widget") {
                echo $form->field($newLangModel,"[{$lang_key}]".$attribute)->{$val['type']}($val['class'],$val['options'])->label($val['label']);
            }else{
                echo $form->field($newLangModel,"[{$lang_key}]".$attribute)->{$val['type']}($val['options'])->label($val['label']) ;
            }
        }
        ?>
		
    </div>
    <!-- /.tab-pane -->
    <?php }?>                         
<?php }?>


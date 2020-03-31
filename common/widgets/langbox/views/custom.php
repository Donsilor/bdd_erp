<?php
use common\helpers\Html;
?>
<?php echo Html::langTab($tab,$title)?> 
 <div class="nav-tabs-custom">          
<div class="tab-content">  
 	<?php $newLangModel = $model->langModel();?>
  		<?php 
  		  foreach (\Yii::$app->params['languages'] as $lang_key=>$lang_name){
  		     $is_new = true;    
  		  ?>                        		
  		    <?php foreach ($model->langs as $langModel) {?>
                <?php if($lang_key == $langModel->language){?>
                	<!-- 编辑-->
                    <div class="tab-pane<?php echo Yii::$app->language==$lang_key?" active":"" ?>" id="<?= $tab.'_'.$lang_key?>">
					<?php foreach ($fields as $attribute =>$val){?>
						<?= $form->field($langModel,$attribute)->{$val['type']}(Html::langInputOptions($langModel,$lang_key,$attribute,$val['options']))->label($val['label'][0],$val['label'][1]) ?>
                  	<?php }?>
                  	</div>
                  	<!-- /.tab-pane -->
                	<?php $is_new = false; break;?>
                <?php }?>
            <?php }?>
            <?php if($is_new == true){?>
            <!-- 新增 -->
            <div class="tab-pane<?php echo Yii::$app->language==$lang_key?" active":"" ?>" id="<?= $tab.'_'.$lang_key?>">
				<?php foreach ($fields as $attribute =>$val){?>
					<?= $form->field($newLangModel,$attribute)->{$val['type']}(Html::langInputOptions($newLangModel,$lang_key,$attribute,$val['options']))->label($val['label'][0],$val['label'][1]) ?>
                <?php }?>
            </div>
            <!-- /.tab-pane -->
            <?php }?>                         
        <?php }?>
</div>
</div>
<?php
use common\helpers\Html;

$col =  12 / $level;
?>

<div class="row">
    <?php if ($level >= 1){ ?>
        <div class="col-lg-<?= $col; ?>">
            <?= $form->field($model, $countryName)->widget(\kartik\select2\Select2::class, [
                'data' => Yii::$app->services->country->getProvinceMapByPid([1,2,3,4,5,6]),
                'options' => [
                        'placeholder' => '-- 请选择国家--',
                        'onchange' => 'widget_country(this, 1,"' . Html::getInputId($model, $provinceName) . '","' . Html::getInputId($model, $cityName) . '")'
                ],                        
                'pluginOptions' => [
                        'allowClear' => false,
                ],
           ]);?>   
        </div>
    <?php }?>
    <?php if ($level >= 2){ ?>
        <div class="col-lg-<?= $col; ?>">
                <?= $form->field($model, $provinceName)->widget(\kartik\select2\Select2::class, [
                        'data' => Yii::$app->services->country->getProvinceMapByPid($model->$countryName),
                    'options' => [
                            'placeholder' => '-- 请选择省份 --',
                            'onchange' => 'widget_country(this,2,"' . Html::getInputId($model, $cityName) . '","'.Html::getInputId($model, $cityName) . '")',
                    ],                        
                    'pluginOptions' => [
                            'allowClear' => false,
                    ],
               ]);?>   
        </div>
    <?php }?>
    <?php if ($level >= 3){ ?>
        <div class="col-lg-<?= $col; ?>">
            <?= $form->field($model, $cityName)->widget(\kartik\select2\Select2::class, [
                    'data' => Yii::$app->services->country->getProvinceMapByPid($model->$provinceName),
                    'options' => [
                            'placeholder' => '-- 请选择城市 --',
                    ],                        
                    'pluginOptions' => [
                            'allowClear' => false,
                    ],
               ]);?>   
        </div>
    <?php }?>
</div>

<script>
    function widget_country(obj, type_id, provinceId, cityId) {
        $(".form-group.field-" + cityId).hide();
        var pid = $(obj).val();
        $.ajax({
            type :"get",
            url : "<?= $url; ?>",
            dataType : "json",
            data : {type_id:type_id, pid:pid},
            success: function(data){
                if (type_id == 2) {
                    $(".form-group.field-"+cityId).show();
                }

                $("select#"+provinceId+"").html(data);
            }
        });
    }
</script>
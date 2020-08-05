<?php
use yii\helpers\Html;
use yii\helpers\Url;
use common\helpers\ImageHelper;
use common\helpers\StringHelper;
?>

<div class="rf-row">
    <div class="col-sm-12">
        <div class="upload-list">
            <ul id="<?= $boxId; ?>" data-name="<?= $name?>" data-boxId="<?= $boxId?>" data-multiple="<?= $config['pick']['multiple'] ?>">
                <?php foreach ($value as $vo){ ?>
                    <li>
                        <?= Html::hiddenInput($name, $vo)?>
                        <div class="img-box">
                            <?php if ($type == 'images' || ImageHelper::isImg($vo)) {?>
                                <a href="<?= trim($vo) ?>" data-fancybox="rfUploadImg">
                                    <div class="bg-cover" style="background-image: url(<?= $vo?>);"></div>
                                </a>
                            <?php } else { ?>
                                <i class="fa fa-file-o"></i>
                                <i class="upload-extend"><?= StringHelper::clipping($vo) ?></i>
                                <div class="bottom-bar"><a href="<?= $vo ?>" target="_blank">预览</a></div>
                            <?php } ?>
                        </div>
                    </li>
                <?php } ?>
                <li class="upload-box <?php if(!empty($value) && $config['pick']['multiple'] == false){?>hide<?php } ?>">
                    <i class="fa fa-cloud-upload"></i>
                    <?php if ($themeConfig['select'] === true) {?>
                        <div class="upload-box-bg hide befor-upload">
                            <a class="first" href="<?= Url::to(['/file/selector', 'boxId' => $boxId, 'upload_type' => $type, 'multiple' => $config['pick']['multiple'], 'upload_drive' => $config['formData']['drive']])?>" data-toggle='modal' data-target='#ajaxModalMax'>选择文件</a>
                            <a class="second upload-box-immediately">立即上传</a>
                        </div>
                    <?php } ?>
                    <div class="upload-box-bg hide">
                        <div class="upload-progress first">
                            <span class="badge bg-green">0%</span>
                        </div>
                        <a class="second cancel">取消上传</a>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</div>


<!--隐藏上传组件-->
<div class="hidden" id="upload-<?= $boxId; ?>">
    <div class="upload-album-<?= $boxId; ?>"></div>
</div>



<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span>
    </button>
    <h4 class="modal-title">基本信息</h4>
</div>
<div class="modal-body">
    <div class="col-sm-12">
        <?php if ($model->goods_image) { ?>
            <?= \common\helpers\ImageHelper::fancyBox($model->goods_image, 500, 400); ?>
        <?php } else { ?>
            <?= "无图片" ?>
        <?php } ?>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
</div>

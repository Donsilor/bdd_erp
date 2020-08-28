<?php

use common\helpers\Html;
use addons\Warehouse\common\enums\BillStatusEnum;
use common\enums\AuditStatusEnum;

/* @var $this yii\web\View */
/* @var $model common\models\order\order */
/* @var $form yii\widgets\ActiveForm */
?>
<style>

</style>
<div class="row">
    <div class="col-xs-12">
        <div class="box" id="text">
            <div class="box-header">
                <h5 class="box-title"><?= $model->title ?></h5>
            </div>
            <div class="box-body table-responsive" style="padding-left: 0px;padding-right: 0px;">
                <div class="col-xs-12">
                    <div class="box">
                        <div class="table-responsive">
                            <div class="work_content">
                                <?= nl2br($model->content) ?>
                            </div>

                        </div>


                    </div>
                </div>

            </div>
        </div>
        <div class="footer">
            <button onclick="copyText()">复制</button>
        </div>
    </div>


</div>
<script type="text/javascript">
    function copyText() {
        copy("text")
    }
    /**
     * 一键粘贴
     * @param  {String} id [需要粘贴的内容]
     * @param  {String} attr [需要 copy 的属性，默认是 innerText，主要用途例如赋值 a 标签上的 href 链接]
     *
     * range + selection
     *
     * 1.创建一个 range
     * 2.把内容放入 range
     * 3.把 range 放入 selection
     *
     * 注意：参数 attr 不能是自定义属性
     * 注意：对于 user-select: none 的元素无效
     * 注意：当 id 为 false 且 attr 不会空，会直接复制 attr 的内容
     */
    function copy (id, attr = null) {
        let target = null;
        if (attr) {
            target = document.createElement('div');
            target.id = 'tempTarget';
            target.style.opacity = '0';
            if (id) {
                let curNode = document.querySelector('#' + id);
                target.innerText = curNode[attr];
            } else {
                target.innerText = attr;
            }
            document.body.appendChild(target);
        } else {
            target = document.querySelector('#' + id);
        }

        try {
            let range = document.createRange();
            range.selectNode(target);
            window.getSelection().removeAllRanges();
            window.getSelection().addRange(range);
            document.execCommand('copy');
            window.getSelection().removeAllRanges();
            rfMsg('复制成功');
            console.log('复制成功')
        } catch (e) {
            console.log('复制失败')
        }

        if (attr) {
            // remove temp target
            target.parentElement.removeChild(target);
        }
    }
</script>
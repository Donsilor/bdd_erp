<?php

use common\helpers\Url;

$this->title = '首页';
$this->params['breadcrumbs'][] = ['label' => $this->title];
?>
<?= \common\helpers\Html::cssFile('@web/backend/resources/css/panel.css') ?>
<div class="row">
    <div class="bg-box clf">
        <div class="bg-empty"></div>
        <div class="bg-color clf">
            <!-- 待处理 -->
            <div class="pending fl">
                <!-- 标题 -->
                <div class="title clf">
                    <span class="point fl"></span>
                    <span class="tit-text fl">待处理</span>
                </div>
                <div class="padding-layer">
                    <div class="pending-head clf">
                        <div class="total fl">待处理（<span><?= $pend['num'] ?? 0 ?></span>条）</div>
                        <a class="more clf fr openContab" href="<?= Url::buildUrl('../base/pend/pending', [], []) ?>">
                            <span id="more" class="fl">more</span>
                            <div class="more-icon fl">
                                <span></span>
                                <span></span>
                            </div>
                        </a>
                    </div>
                    <div class="pending-content">
                        <?php foreach ($pend['list'] ?? [] as $time => $list) {
                            if ($list && is_array($list)) { ?>
                                <div class="pending-list">
                                    <div class="date-box">
                                        <div class="date"><?= $time ?? "" ?></div>
                                        <div class="date date-start"></div>
                                    </div>
                                    <div class="order-box">
                                        <div>
                                            <?php foreach ($list ?? [] as $p) {
                                                $url = Url::buildUrl('../' . \common\enums\OperTypeEnum::getUrlValue($p['oper_type']), [], ['id']) . '?id=' . $p['oper_id'];
                                                ?>
                                                <div class="order-child <?php if ($p['pend_status'] ?? 0) {
                                                    echo 'finish';
                                                } ?>">
                                                    <div class="order-l">
                                                        <div class="order-date"><?= date('Y/m/d H:i', $p['created_at'] ?? 0) ?? "" ?></div>
                                                        <div class="order-text">
                                                            <?= '[' . \common\enums\OperTypeEnum::getValue($p['oper_type'] ?? "") . ']：' ?? "" ?>
                                                            <span><a class="openContab"
                                                                     style="text-decoration:underline;"
                                                                     href="<?= $url; ?>"><?= $p['oper_sn'] ?? "立即处理" ?></a></span>，需审核请及时处理！
                                                        </div>
                                                    </div>
                                                    <div class="order-state">
                                                        <?= '【' . \common\enums\PendStatusEnum::getValue($p['pend_status'] ?? 0) . "】" ?? "" ?>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        </div>
                                        <div class="point"></div>
                                        <div class="line"></div>
                                    </div>
                                </div>
                            <?php }
                        } ?>
                    </div>
                </div>
            </div>
            <!-- 快捷入口 -->
            <div class="quick-entry fl">
                <!-- 标题 -->
                <div class="title clf">
                    <span class="point fl"></span>
                    <span class="tit-text fl">快捷入口</span>
                </div>
                <div class="padding-layer">
                    <div class="quick-box">
                        <?php foreach ($quick['list'] ?? [] as $title => $list) {
                            $urls = $quick['url'] ?? [];
                            $btns = $quick['btn'] ?? [];
                            if ($list && is_array($list)) {
                                ?>
                                <div class="quick-list clf">
                                    <div class="quick-nav fl"><?= $title; ?></div>
                                    <div class="quick-content fl">
                                        <?php foreach ($list ?? [] as $id => $name) {
                                            if ($urls) {
                                                $url = Url::buildUrl('../' . $urls[$id] ?? "", [], []);
                                                $authUrl = \common\helpers\Auth::verify('/' . $urls[$id]);//路由权限
                                                $authBtn = \common\helpers\Auth::verify($btns[$id] ?? "");//按钮权限
                                            }
                                            ?>
                                            <?php if ($authUrl && $authBtn) { ?>
                                                <a class="quick-child openContab"
                                                   href="<?= $url ?? 'javascript:;' ?>">
                                                    <div class="inline clf">
                                                        <span class="quick-text fl"><?= $name ?? "" ?></span>
                                                        <div class="more-icon fl">
                                                            <span></span>
                                                            <span></span>
                                                        </div>
                                                    </div>
                                                </a>
                                            <?php } ?>
                                        <?php } ?>
                                    </div>
                                </div>
                            <?php }
                        } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
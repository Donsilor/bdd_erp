<?php

use common\helpers\Url;

$this->title = '首页';
$this->params['breadcrumbs'][] = ['label' => $this->title];
?>
<style type="text/css">
    body, div, ul, li, span, a {
        padding: 0;
        margin: 0;
        box-sizing: border-box;
    }

    a {
        color: inherit;
        text-decoration: none;
    }

    .fl {
        float: left;
    }

    .fr {
        float: right;
    }

    .clf::after {
        display: block;
        content: '';
        height: 0;
        visibility: hidden;
        clear: both;
        opacity: 0;
    }

    .inline {
        height: 100%;
        display: inline-block;
    }

    .scroll {
        overflow-y: scroll;
    }

    .container {
        min-width: 1340px;
        background-color: #fff;
        padding: 30px;
    }

    .bg-empty {
        width: 100%;
        height: 40px;
    }

    .bg-color {
        background-color: #f2f2f2;
    }

    .title {
        position: relative;
        height: 40px;
        padding-left: 20px;
        margin-top: -40px;
    }

    .tit-list {
        width: 50%;
        height: 100%;
    }

    .point {
        width: 10px;
        height: 10px;
        background-color: #169bd5;
        border-radius: 50%;
        margin-top: 6px;
    }

    .tit-text {
        font-size: 24px;
        color: #333;
        margin-left: 14px;
        line-height: 22px;
    }

    .bg-box {
        /* height: 800px; */
        background-color: #fff;
        padding: 20px 3%;
    }

    .pending,
    .quick-entry {
        width: 49%;
    }

    .quick-entry {
        margin-left: 2%;
        height: 403px;
    }

    .padding-layer {
        padding: 20px;
    }

    .pending-head {
        height: 40px;
        background-color: #eb6709;
        padding: 0 20px;
        line-height: 40px;
        font-size: 14px;
        color: #fff;
    }

    .more {
        cursor: pointer;
    }

    .more-icon {
        width: 10px;
        height: 1px;
        margin: 21px 0 0 6px;
        position: relative;
    }

    .more-icon span {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: #fff;
        transform-origin: right;
        transform: rotate(18deg);
    }

    .more-icon span:last-child {
        transform: rotate(-18deg);
    }

    .pending-content {
        height: 361px;
        padding: 20px;
        border: 1px solid #eb6709;
        overflow: scroll;
    }

    .pending-list {
        display: flex;
        font-size: 12px;
    }

    .pending-list .date-box {
        position: relative;
        width: 50px;
    }

    .pending-list .date {
        position: absolute;
        top: 0;
        left: 50%;
        width: 40px;
        height: 20px;
        text-align: center;
        color: #268e3c;
        transform: translate(-50%, 50%)
    }

    .order-box {
        flex-grow: 1;
        margin-left: 10px;
        padding: 10px 0 10px 20px;
        border-left: 1px solid #268e3c;
        position: relative;
        display: flex;
    }

    .order-child {
        display: flex;
        margin-bottom: 10px;
    }

    .order-box .order-child:last-child {
        margin: 0;
    }

    .order-l {
        display: flex;
    }

    .order-date {
        color: #999;
    }

    .order-text {
        margin-left: 10px;
        color: #268e3c;
    }

    .order-state {
        flex: 1;
        text-align: center;
        color: #f00;
    }

    .order-box .point {
        position: absolute;
        top: 0;
        left: 0;
        background-color: #268e3c;
        transform: translate(-50%, -50%);
        margin-top: 0;
    }

    .order-box .line {
        position: absolute;
        top: 0;
        left: 30px;
        height: 1px;
        width: 80%;
        background-color: #fff;
    }

    .order-child.finish .order-date,
    .order-child.finish .order-text,
    .order-child.finish .order-state {
        color: #999;
    }

    .quick-box {
        border: 1px solid #eb6709;
        height: 403px;
        overflow-y: scroll;
    }

    .quick-nav {
        width: 40px;
        height: 100px;
        background-color: #eb6709;
        font-size: 14px;
        color: #fff;
        padding: 30px 4px;
        letter-spacing: 2px;
        border-bottom: 1px solid #fff;
    }

    .quick-content {
        width: calc(100% - 40px);
        height: 100px;
        border-bottom: 1px solid #f2f2f2;
    }

    .quick-list:last-child .quick-nav,
    .quick-list:last-child .quick-content {
        border: none;
    }

    .quick-content {
        display: flex;
        flex-direction: column;
        flex-wrap: wrap;
        align-content: flex-start;
        justify-content: space-between;
        padding: 14px 20px;
    }

    .quick-child {
        width: 110px;
        height: 26px;
        border: 1px solid #359549;
        font-size: 12px;
        color: #359549;
        line-height: 25px;
        text-align: center;
        margin-right: 30px;
    }

    .quick-child .more-icon {
        margin: 12px 0 0 5px;
    }

    .quick-child .more-icon span {
        background-color: #359549;
    }
</style>
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
                                            <?php foreach ($list ?? [] as $p) { ?>
                                                <div class="order-child <?php if ($p['pend_status'] ?? 0) {
                                                    echo 'finish';
                                                } ?>">
                                                    <div class="order-l">
                                                        <div class="order-date"><?= date('Y/m/d H:i', $p['created_at'] ?? 0) ?? "" ?></div>
                                                        <div class="order-text">
                                                            <?= '[' . \common\enums\OperTypeEnum::getValue($p['oper_type'] ?? "") . ']：' ?? "" ?>
                                                            <span><a class="openContab"
                                                                     style="text-decoration:underline;"
                                                                     href="<?= Url::buildUrl('../' . \common\enums\OperTypeEnum::getUrlValue($p['oper_type']), [], ['id']) . '?id=' . $p['oper_id']; ?>"><?= $p['oper_sn'] ?? "" ?></a></span>，需审核请及时处理！
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
                            if ($list && is_array($list)) {
                                ?>
                                <div class="quick-list clf">
                                    <div class="quick-nav fl"><?= $title; ?></div>
                                    <div class="quick-content fl">
                                        <?php foreach ($list ?? [] as $id => $name) {
                                            if ($urls) {
                                                $url = Url::buildUrl('../' . $urls[$id] ?? "", [], []);
                                                $authUrl = \common\helpers\Auth::verify('/' . $urls[$id]);
                                            }
                                            ?>
                                            <?php if ($authUrl) { ?>
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
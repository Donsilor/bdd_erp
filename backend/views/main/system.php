<?php

use common\helpers\Url;

$this->title = '首页';
$this->params['breadcrumbs'][] = ['label' => $this->title];
?>
<?= \common\helpers\Html::cssFile('@web/backend/resources/css/panel.css') ?>
<div class="row">
    <div class="clf">
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
                                    <div class="order-group">
                                        <?php foreach ($list ?? [] as $p) {
                                            $url = Url::buildUrl('../' . \common\enums\OperTypeEnum::getUrlValue($p['oper_type']), [], ['id']) . '?id=' . $p['oper_id'];
                                            ?>
                                            <div class="order-child <?= $p['pend_status'] ? 'finish' : ''; ?>">
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
                                    <div class="point point-start"></div>
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
    <!-- 产品销量 -->
    <div class="sales-volume-wrap">
        <div class="tit-box clf">
            <div class="title clf">
                <span class="point fl"></span>
                <span class="tit-text fl">产品销量</span>
            </div>
        </div>
        <div class="sales-volume">
            <div class="sales-volume-head clf">
                <div class="total fl">产品销量（本月）</div>
                <div class="more clf fr">
                    <span class="fl">more</span>
                    <div class="more-icon fl">
                        <span></span>
                        <span></span>
                    </div>
                </div>
            </div>
            <div class="sales-volume-content clf">
                <div class="sales-volume-list fl">
                    <div class="sales-volume-list-head">
                        <div class="BDD">BDD<span class="color">(总数量：<i>100</i>件)</span></div>
                        <a class="more clf" href="javascript:;">
                            <span class="fl">more</span>
                            <div class="more-icon fl">
                                <span></span>
                                <span></span>
                            </div>
                        </a>
                    </div>
                    <div class="sales-chart">
                        <div class="sales-chart-box">
                            <div class="sales-chart-list">
                                <div class="type">戒指</div>
                                <div class="bar-box">
                                    <div class="bar">35</div>
                                </div>
                                <div class="percent">21%</div>
                            </div>
                            <div class="sales-chart-list">
                                <div class="type">戒指</div>
                                <div class="bar-box">
                                    <div class="bar">35</div>
                                </div>
                                <div class="percent">21%</div>
                            </div>
                            <div class="sales-chart-list">
                                <div class="type">项链</div>
                                <div class="bar-box">
                                    <div class="bar">21</div>
                                </div>
                                <div class="percent">20%</div>
                            </div>
                            <div class="sales-chart-list">
                                <div class="type">吊坠</div>
                                <div class="bar-box">
                                    <div class="bar">35</div>
                                </div>
                                <div class="percent">28%</div>
                            </div>
                            <div class="sales-chart-list">
                                <div class="type">耳饰</div>
                                <div class="bar-box">
                                    <div class="bar">14</div>
                                </div>
                                <div class="percent">21%</div>
                            </div>
                            <div class="sales-chart-list">
                                <div class="type">手链</div>
                                <div class="bar-box">
                                    <div class="bar"></div>
                                </div>
                                <div class="percent"></div>
                            </div>
                            <div class="sales-chart-list">
                                <div class="type">手镯</div>
                                <div class="bar-box">
                                    <div class="bar">14</div>
                                </div>
                                <div class="percent">12%</div>
                            </div>
                            <div class="sales-chart-list">
                                <div class="type">脚链</div>
                                <div class="bar-box">
                                    <div class="bar">32</div>
                                </div>
                                <div class="percent">28%</div>
                            </div>
                            <div class="sales-chart-list">
                                <div class="type">手串</div>
                                <div class="bar-box">
                                    <div class="bar">35</div>
                                </div>
                                <div class="percent">21%</div>
                            </div>
                            <div class="sales-chart-list">
                                <div class="type">总计</div>
                                <div class="bar-box">
                                    <div class="bar">35</div>
                                </div>
                                <div class="percent">21%</div>
                            </div>
                        </div>

                        <div class="line-a"></div>
                        <div class="line-b"></div>
                    </div>
                </div>

                <div class="sales-volume-list fl">
                    <div class="sales-volume-list-head">
                        <div class="BDD">京东<span class="color">(总数量：<i>100</i>件)</span></div>

                        <a class="more clf" href="javascript:;">
                            <span class="fl">more</span>
                            <div class="more-icon fl">
                                <span></span>
                                <span></span>
                            </div>
                        </a>
                    </div>

                    <div class="sales-chart">
                        <div class="sales-chart-box">
                            <div class="sales-chart-list">
                                <div class="type">戒指</div>
                                <div class="bar-box">
                                    <div class="bar">35</div>
                                </div>
                                <div class="percent">21%</div>
                            </div>
                            <div class="sales-chart-list">
                                <div class="type">戒指</div>
                                <div class="bar-box">
                                    <div class="bar">35</div>
                                </div>
                                <div class="percent">21%</div>
                            </div>
                            <div class="sales-chart-list">
                                <div class="type">项链</div>
                                <div class="bar-box">
                                    <div class="bar">21</div>
                                </div>
                                <div class="percent">20%</div>
                            </div>
                            <div class="sales-chart-list">
                                <div class="type">吊坠</div>
                                <div class="bar-box">
                                    <div class="bar">35</div>
                                </div>
                                <div class="percent">28%</div>
                            </div>
                            <div class="sales-chart-list">
                                <div class="type">耳饰</div>
                                <div class="bar-box">
                                    <div class="bar">14</div>
                                </div>
                                <div class="percent">21%</div>
                            </div>
                            <div class="sales-chart-list">
                                <div class="type">手链</div>
                                <div class="bar-box">
                                    <div class="bar"></div>
                                </div>
                                <div class="percent"></div>
                            </div>
                            <div class="sales-chart-list">
                                <div class="type">手镯</div>
                                <div class="bar-box">
                                    <div class="bar">14</div>
                                </div>
                                <div class="percent">12%</div>
                            </div>
                            <div class="sales-chart-list">
                                <div class="type">脚链</div>
                                <div class="bar-box">
                                    <div class="bar">32</div>
                                </div>
                                <div class="percent">28%</div>
                            </div>
                            <div class="sales-chart-list">
                                <div class="type">手串</div>
                                <div class="bar-box">
                                    <div class="bar">35</div>
                                </div>
                                <div class="percent">21%</div>
                            </div>
                            <div class="sales-chart-list">
                                <div class="type">总计</div>
                                <div class="bar-box">
                                    <div class="bar">35</div>
                                </div>
                                <div class="percent">21%</div>
                            </div>
                        </div>

                        <div class="line-a"></div>
                        <div class="line-b"></div>
                    </div>
                </div>

                <div class="sales-volume-list fl">
                    <div class="sales-volume-list-head">
                        <div class="BDD">淘宝<span class="color">(总数量：<i>100</i>件)</span></div>

                        <a class="more clf" href="javascript:;">
                            <span class="fl">more</span>
                            <div class="more-icon fl">
                                <span></span>
                                <span></span>
                            </div>
                        </a>
                    </div>

                    <div class="sales-chart">
                        <div class="sales-chart-box">
                            <div class="sales-chart-list">
                                <div class="type">戒指</div>
                                <div class="bar-box">
                                    <div class="bar">35</div>
                                </div>
                                <div class="percent">21%</div>
                            </div>
                            <div class="sales-chart-list">
                                <div class="type">戒指</div>
                                <div class="bar-box">
                                    <div class="bar">35</div>
                                </div>
                                <div class="percent">21%</div>
                            </div>
                            <div class="sales-chart-list">
                                <div class="type">项链</div>
                                <div class="bar-box">
                                    <div class="bar">21</div>
                                </div>
                                <div class="percent">20%</div>
                            </div>
                            <div class="sales-chart-list">
                                <div class="type">吊坠</div>
                                <div class="bar-box">
                                    <div class="bar">35</div>
                                </div>
                                <div class="percent">28%</div>
                            </div>
                            <div class="sales-chart-list">
                                <div class="type">耳饰</div>
                                <div class="bar-box">
                                    <div class="bar">14</div>
                                </div>
                                <div class="percent">21%</div>
                            </div>
                            <div class="sales-chart-list">
                                <div class="type">手链</div>
                                <div class="bar-box">
                                    <div class="bar"></div>
                                </div>
                                <div class="percent"></div>
                            </div>
                            <div class="sales-chart-list">
                                <div class="type">手镯</div>
                                <div class="bar-box">
                                    <div class="bar">14</div>
                                </div>
                                <div class="percent">12%</div>
                            </div>
                            <div class="sales-chart-list">
                                <div class="type">脚链</div>
                                <div class="bar-box">
                                    <div class="bar">32</div>
                                </div>
                                <div class="percent">28%</div>
                            </div>
                            <div class="sales-chart-list">
                                <div class="type">手串</div>
                                <div class="bar-box">
                                    <div class="bar">35</div>
                                </div>
                                <div class="percent">21%</div>
                            </div>
                            <div class="sales-chart-list">
                                <div class="type">总计</div>
                                <div class="bar-box">
                                    <div class="bar">35</div>
                                </div>
                                <div class="percent">21%</div>
                            </div>
                        </div>

                        <div class="line-a"></div>
                        <div class="line-b"></div>
                    </div>
                </div>

                <div class="sales-volume-list fl">
                    <div class="sales-volume-list-head">
                        <div class="BDD">拼多多<span class="color">(总数量：<i>100</i>件)</span></div>

                        <a class="more clf" href="javascript:;">
                            <span class="fl">more</span>
                            <div class="more-icon fl">
                                <span></span>
                                <span></span>
                            </div>
                        </a>
                    </div>

                    <div class="sales-chart">
                        <div class="sales-chart-box">
                            <div class="sales-chart-list">
                                <div class="type">戒指</div>
                                <div class="bar-box">
                                    <div class="bar">35</div>
                                </div>
                                <div class="percent">21%</div>
                            </div>
                            <div class="sales-chart-list">
                                <div class="type">戒指</div>
                                <div class="bar-box">
                                    <div class="bar">35</div>
                                </div>
                                <div class="percent">21%</div>
                            </div>
                            <div class="sales-chart-list">
                                <div class="type">项链</div>
                                <div class="bar-box">
                                    <div class="bar">21</div>
                                </div>
                                <div class="percent">20%</div>
                            </div>
                            <div class="sales-chart-list">
                                <div class="type">吊坠</div>
                                <div class="bar-box">
                                    <div class="bar">35</div>
                                </div>
                                <div class="percent">28%</div>
                            </div>
                            <div class="sales-chart-list">
                                <div class="type">耳饰</div>
                                <div class="bar-box">
                                    <div class="bar">14</div>
                                </div>
                                <div class="percent">21%</div>
                            </div>
                            <div class="sales-chart-list">
                                <div class="type">手链</div>
                                <div class="bar-box">
                                    <div class="bar"></div>
                                </div>
                                <div class="percent"></div>
                            </div>
                            <div class="sales-chart-list">
                                <div class="type">手镯</div>
                                <div class="bar-box">
                                    <div class="bar">14</div>
                                </div>
                                <div class="percent">12%</div>
                            </div>
                            <div class="sales-chart-list">
                                <div class="type">脚链</div>
                                <div class="bar-box">
                                    <div class="bar">32</div>
                                </div>
                                <div class="percent">28%</div>
                            </div>
                            <div class="sales-chart-list">
                                <div class="type">手串</div>
                                <div class="bar-box">
                                    <div class="bar">35</div>
                                </div>
                                <div class="percent">21%</div>
                            </div>
                            <div class="sales-chart-list">
                                <div class="type">总计</div>
                                <div class="bar-box">
                                    <div class="bar">35</div>
                                </div>
                                <div class="percent">21%</div>
                            </div>
                        </div>

                        <div class="line-a"></div>
                        <div class="line-b"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- 库存汇总 -->
    <div class="inventory-wrap">
        <div class="tit-box clf">
            <div class="title clf">
                <span class="point fl"></span>
                <span class="tit-text fl">库存汇总</span>
            </div>
        </div>

        <div class="inventory">
            <div class="inventory-head clf">
                <div class="total fl">库存汇总</div>
                <div class="more clf fr">
                    <span class="fl">more</span>
                    <div class="more-icon fl">
                        <span></span>
                        <span></span>
                    </div>
                </div>
            </div>

            <div class="inventory-content">
                <div class="flex">
                    <div class="inventory-list clf">
                        <div class="bg-color fl">
                            <div class="type color-blue">库存</div>
                            <div class="num"><span>10000</span><span class="font-12">件</span></div>
                        </div>

                        <div class="detail fl">
                            <div class="tit">货品</div>
                            <ul>
                                <li>
                                    <span class="kind">库存</span>
                                    <span class="kind-num">10000</span>
                                </li>
                                <li>
                                    <span class="kind">待入库</span>
                                    <span class="kind-num">10000</span>
                                </li>
                                <li>
                                    <span class="kind">待出库</span>
                                    <span class="kind-num">10000</span>
                                </li>
                                <li>
                                    <span class="kind">已借贷</span>
                                    <span class="kind-num">10000</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="inventory-list clf">
                        <div class="bg-color fl">
                            <div class="type color-gray">款式</div>
                            <div class="num"><span>20</span><span class="font-12">单</span></div>
                        </div>

                        <div class="detail fl">
                            <div class="tit">款式</div>
                            <ul>
                                <li>
                                    <span class="kind">已审核</span>
                                    <span class="kind-num">10000</span>
                                </li>
                                <li>
                                    <span class="kind">待审核</span>
                                    <span class="kind-num">10000</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="inventory-list clf">
                        <div class="bg-color fl">
                            <div class="type">版式</div>
                            <div class="num"><span>20</span><span class="font-12">单</span></div>
                        </div>

                        <div class="detail fl">
                            <div class="tit">起版</div>
                            <ul>
                                <li>
                                    <span class="kind">已审核</span>
                                    <span class="kind-num">10000</span>
                                </li>
                                <li>
                                    <span class="kind">待审核</span>
                                    <span class="kind-num">10000</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="inventory-list clf">
                        <div class="bg-color fl">
                            <div class="type">裸钻</div>
                            <div class="num"><span>20</span><span class="font-12">单</span></div>
                        </div>

                        <div class="detail fl">
                            <div class="tit">裸钻</div>
                            <ul>
                                <li>
                                    <span class="kind">库存</span>
                                    <span class="kind-num">10000</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="flex">
                    <div class="inventory-list clf">
                        <div class="bg-color fl">
                            <div class="type">金料</div>
                            <div class="num"><span>20</span><span class="font-12">单</span></div>
                        </div>

                        <div class="detail fl">
                            <div class="tit">金料-库存</div>
                            <ul class="width-110">
                                <li>
                                    <span class="kind">AU999-金条</span>
                                    <span class="kind-num">20</span>
                                </li>
                                <li>
                                    <span class="kind">AU999-金料</span>
                                    <span class="kind-num">20</span>
                                </li>
                                <li>
                                    <span class="kind">AU999-银条</span>
                                    <span class="kind-num">20</span>
                                </li>
                                <li>
                                    <span class="kind">AU999-饮料</span>
                                    <span class="kind-num">20</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="inventory-list clf">
                        <div class="bg-color fl">
                            <div class="type">石料</div>
                            <div class="num"><span>20</span><span class="font-12">单</span></div>
                        </div>

                        <div class="detail fl">
                            <div class="tit">石料-库存</div>
                            <ul>
                                <li>
                                    <span class="kind">钻石</span>
                                    <span class="kind-num">100颗</span>
                                    <span class="weigth">20CT</span>
                                </li>
                                <li>
                                    <span class="kind">莫桑石</span>
                                    <span class="kind-num">100颗</span>
                                    <span class="weigth">20CT</span>
                                </li>
                                <li>
                                    <span class="kind">枯石</span>
                                    <span class="kind-num">100颗</span>
                                    <span class="weigth">20CT</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="inventory-list clf">
                        <div class="bg-color fl">
                            <div class="type">配件</div>
                            <div class="num"><span>20</span><span class="font-12">单</span></div>
                        </div>

                        <div class="detail fl">
                            <div class="tit">配件-库存</div>
                            <ul>
                                <li>
                                    <span class="kind">扣环</span>
                                    <span class="kind-num">11件</span>
                                    <span class="weigth">100g</span>
                                </li>
                                <li>
                                    <span class="kind">耳背</span>
                                    <span class="kind-num">11件</span>
                                    <span class="weigth">100g</span>
                                </li>
                                <li>
                                    <span class="kind">扣子</span>
                                    <span class="kind-num">11件</span>
                                    <span class="weigth">100g</span>
                                </li>
                                <li>
                                    <span class="kind">链子</span>
                                    <span class="kind-num">11件</span>
                                    <span class="weigth">100g</span>
                                </li>
                                <li>
                                    <span class="kind">耳棒</span>
                                    <span class="kind-num">11件</span>
                                    <span class="weigth">100g</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="inventory-list clf">
                        <div class="bg-color fl">
                            <div class="type">赠品</div>
                            <div class="num"><span>20</span><span class="font-12">单</span></div>
                        </div>

                        <div class="detail fl">
                            <div class="tit">赠品-库存</div>
                            <ul>
                                <li>
                                    <span class="kind">库存</span>
                                    <span class="kind-num">10000</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
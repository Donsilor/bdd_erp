<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>出货明细表</title>
    <style type="text/css">
        {literal}
        *{margin:0;padding:0;}
        body{font:12px/25px "宋体";}
        .tRight{text-align:right;}
        .tLeft{text-align:left;}
        .wrap{width:1000px;margin:50px auto;}
        h1{font-size:14px; text-align:center;margin-bottom:10px;}
        table.list-ch{border-collapse:collapse;border:none;width:100%;margin-top:10px;}
        table.list-ch td{ border:1px #333 solid;padding:0 2px;}
        table.list-ch thead td{height:35px; line-height:14px; text-align:center; font-weight:bold;}
        {/literal}
    </style>
</head>
<?php
$datetime = new \DateTime;
$now_time = $datetime->format('Y-m-d H:i:s');
$datetime->setTimestamp($model->created_at);
$create_time = $datetime->format('Y-m-d H:i:s');
?>
<body>
<!--startprint-->
<div class="wrap">
    <h1>出货明细表</h1>现在时间&nbsp;<?php echo $now_time; ?>
    <table cellpadding="0" cellspacing="0" border="0" width="100%">
        <tr>
            <td width="65">加工商：</td>
            <td  width="100"  class="tLeft"><?= $model->supplier_id ?  $model->supplier->supplier_name : '' ?></td>
            <td width="65">流水号：</td>
            <td   width="100" class="tLeft"><?= $model->id ?></td>
            <td width="65">出货单号:</td>
            <td  width="100"  class="tLeft"><?= $model->receipt_no ?></td>
            <td width="65">日期：</td>
            <td  width="100" class="tLeft"><?= $create_time?></td>
            <td width="65">件数：</td>
            <td  width="100" ><?= $model->receipt_num ?></td>
        </tr>
        <tr>
            <td>备注：</td>
            <td colspan="8"><?= $model->remark ?></td>
        </tr>
    </table>
    <table cellpadding="0" cellspacing="0" border="0" class="list-ch">
        <thead>
        <tr>
            <td>序号</td>
            <td>客来石<br />信息</td>
            <td>品类</td>
            <td>模号</td>
            <td>售卖方式</td>
            <td>戒托镶口</td>
            <td>手寸</td>
            <td>材质</td>
            <td>毛重</td>
            <td>主成色重(净金重)</td>
            <td>金耗</td>
            <td>金价</td>
            <td>主石信息</td>
            <td>主石单价</td>

            <td>副石</td>
            <td>副石单价</td>
            <td>主副石总重量</td>
            <td>工费</td>
            <td>超石费</td>
            <td>其他<br />工费</td>
            <td>配件<br />成本</td>
            <td>成本</td>
            <td>税费</td>
            <td>含税价</td>
            <td>布产号</td>
            <td>款号</td>
        </tr>
        </thead>
        <tbody>
        <?php foreach($goodsList as $goods_info): ?>
            <tr>
                <td nowrap=true><?= $goods_info['id'] ?></td>
                <td nowrap=true></td>
                <td nowrap=true><?= $goods_info['style_cate_id'] ?></td>
                <td><?= $goods_info['factory_mo'] ?></td>
                <td></td>
                <td><?= $goods_info['xiangkou'] ?></td>
                <td><?= $goods_info['finger'] ?></td>
                <td><?= $goods_info['material'] ?></td>
                <td><?= $goods_info['gross_weight'] ?></td>
                <td><?= $goods_info['gold_weight'] ?></td>
                <td><?= $goods_info['gold_loss'] ?></td>
                <td><?= $goods_info['gold_price'] ?></td>
                <td><?= $goods_info['main_stone'] ?>&nbsp;<?= $goods_info['main_stone_weight'] ?>/<?= $goods_info['main_stone_num'] ?></td>
                <td><?= $goods_info['main_stone_price'] ?></td>

                <td  width="85">
                    <span style="border-bottom:1px #000 solid;display:block;"><?= $goods_info['second_stone1'] ?>&nbsp;<?= $goods_info['second_stone_weight1'] ?>/<?= $goods_info['second_stone_num1'] ?>
                    </span>
                <span style="border-bottom:1px #000 solid;display:block;">
                        <?= $goods_info['second_stone2'] ?>&nbsp;<?= $goods_info['second_stone_weight2'] ?>/<?= $goods_info['second_stone_num2'] ?>
                    </span>
                    <span style="display:block;"><?= $goods_info['second_stone3'] ?>&nbsp;<?= $goods_info['second_stone_weight3'] ?>/<?= $goods_info['second_stone_num3'] ?></span>
            </td>
            <td><span style="border-bottom:1px #000 solid;display:block;"><?= $goods_info['second_stone_price1'] ?></span>
                    <span style="border-bottom:1px #000 solid;display:block;"><?= $goods_info['second_stone_price2'] ?></span>
                <span style="display:block;"><?= $goods_info['second_stone_price3'] ?></span>
                </td>
                <td><?= $goods_info['stone_zhong'] ?></td>
                <td><?= $goods_info['gong_fee'] ?></td>
                <td><?= $goods_info['extra_stone_fee'] ?></td>
                <td><?= $goods_info['other_fee'] ?></td>
                <td><?= $goods_info['parts_fee'] ?></td>
                <td><?= $goods_info['cost_price'] ?></td>
                <td><?= $goods_info['tax_fee'] ?></td>
                <td><?= $goods_info['han_tax_price'] ?></td>
                <td><?= $goods_info['produce_sn'] ?></td>
                <td><?= $goods_info['style_sn'] ?></td>
            </tr>
        <?php endforeach; ?>
        <tr>
            <td>合计</td>
            <td colspan="25" class="tLeft">0</td>
        </tr>
        </tbody>
    </table>
    <table width="100%">
        <tr>
            <td width="33%" valign=top>
                配石统计:<br />
                <table cellpadding="0" cellspacing="0" border="0" class="list-ch" style="width:90%">
                    <thead>
                    <tr>
                        <td>名称</td>
                        <td>石单价</td>
                        <td>数量</td>
                        <td>石重</td>
                        <td>石值</td>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                    </tr>
                    </tbody>
                </table>
            </td>
            <td width="33%" valign=top>
                金料统计:
                <table cellpadding="0" cellspacing="0" border="0" class="list-ch" style="width:90%">
                    <thead>
                    <tr>
                        <td>成色</td>
                        <td>重量</td>
                        <td>平均金价</td>
                        <td>金值</td>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                    </tr>
                    </tbody>
                </table>
            </td>
            <td width="33%" valign=top>
                费用统计
                <table cellpadding="0" cellspacing="0" border="0" class="list-ch" style="width:90%">
                    <thead>
                    <tr>
                        <td>类别</td>
                        <td>金额</td>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>0</td>
                        <td>0</td>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </table>
    <!--endprint-->
    <br/><br/>
    <div style="text-align:center;">

        <button id="print_close" onclick="close_bill();">关闭</button>
        <button id="print_btn" onclick="print_bill();">打&nbsp;印</button>

    </div>
</div>
</body>

<script type="text/javascript">

    function close_bill(){
        window.close();
    }

    function print_bill(){
        bdhtml = window.document.body.innerHTML;
        sprnstr = "<!--startprint-->";
        eprnstr = "<!--endprint-->";
        prnhtml = bdhtml.substr(bdhtml.indexOf(sprnstr) + 17);
        prnhtml = prnhtml.substring(0, prnhtml.indexOf(eprnstr));
        window.document.body.innerHTML = prnhtml;
        window.print();
    }
</script>
</html>

<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel5" xmlns="http://www.w3.org/TR/REC-html40">
<head>
<meta http-equiv=Content-Type content="text/html; charset=utf-8">
    <style>
        body {
            font-family: initial; /*浏览器打印不出div背景颜色*/
            -webkit-print-color-adjust: exact;
        }

        td {
            text-align: left;
            padding: 0 5px;
        }

        .midd {
            height: 10px;
        }

        .msg {
            display: flex;
            text-align: left;
            font-weight: bold;
        }

        .top {
            width: 20%;
        }

        .middle {
            width: 60%;
        }

        .height {
            height: 20px;
        }

        thead tr th, thead tr td, tfoot tr td {
            background-color: #A6A6A6 !important;
        }

        .one, .two, .three {
            line-height: 20px;
        }

        span {
            display: inline-block;
        }

        .template {
            width: 100%;
            text-align: center;
            display: flex;
            justify-content: center;
            padding: 20px 0;
            font-family: 'Times New Roman', Times;
            font-size: 12px;
            color: #000;
        }

        .information {
            width: 100%;
        }

        table {
            table-layout: fixed;
        }

        td {
            /* word-break: break-all;  */
            word-wrap: break-word;
        }

        p {
            margin: 0 !important;
        }

        .address {
            margin: 6px 0;
        }

        /* span{
            font-size: 14px;
        } */
        .total {
            margin: 8px 0;
        }

        .shipping-address {
            line-height: 60px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }

        table, table tr th, table tr td {
            font-size: 10px;
        }

        table tr th {
            /* padding: 10px 20px; */
            text-align: center;
        }

        .foot-info {
            display: flex;
            text-align: left;
            margin-top: 5px;
        }

        .prepared-by {
            width: 22.5%;
        }

        .reviewer {
            width: 20%;
        }

        /* 输入框去掉边框和外边框 */
        input {
            border: none;
            outline: none;
            font-size: 14px;
        }

        /* 字体加粗 */
        .font-bold {
            font-weight: bold;
        }

        /* 文本位置 */
        .algin-center {
            text-align: center;
        }

        .algin-left {
            text-align: left;
        }

        .algin-right {
            text-align: right;
        }

        .img-center {
            justify-content: center;
            align-items: center;
        }

        /* 字体大小 */
        .font-24 {
            font-size: 24px;
        }

        .font-22 {
            font-size: 22px;
        }

        .font-20 {
            font-size: 20px;
        }

        .font-18 {
            font-size: 18px;
        }

        .font-16 {
            font-size: 16px;
        }

        .font-14 {
            font-size: 14px;
        }

        .font-12 {
            font-size: 12px;
        }

        /* 宽度 */
        .width-200 {
            width: 200px;
        }

        .width-190 {
            width: 190px;
        }

        .width-180 {
            width: 180px;
        }

        .width-170 {
            width: 170px;
        }

        .width-160 {
            width: 160px;
        }

        .width-150 {
            width: 150px;
        }

        .width-140 {
            width: 140px;
        }

        .width-125 {
            width: 125px;
        }

        .width-120 {
            width: 120px;
        }

        .width-100 {
            width: 100px;
        }

        .width-97 {
            width: 97px;
        }

        .width-90 {
            width: 90px;
        }

        .width-80 {
            width: 80px;
        }

        .width-65 {
            width: 65px;
        }

        .width-60 {
            width: 60px;
        }

        .width-50 {
            width: 50px;
        }

        .width-55 {
            width: 55px;
        }

        .width-40 {
            width: 40px;
        }

        .width-35 {
            width: 35px;
        }

        .width-30 {
            width: 30px;
        }

        .width-25 {
            width: 20px;
        }

        .width-20 {
            width: 20px;
        }

        /* 行高 */
        .line-height-20 {
            line-height: 20px;
        }

        /* 边距 */
        .margin-top-20 {
            margin-top: 15px;
        }

        .margin-top-55 {
            margin-top: 55px;
        }

        .padding-5 {
            padding: 3px;
        }

        .padding-10 {
            padding: 0px 10px;
        }

        .padding-14 {
            padding: 0px 14px;
        }
        .headerTable, .headerTable td{ border:0px}
        .footerTable, .footerTable td{ border:0px}
    </style>
</head>
<body> 
<table class="headerTable">
    <tr>
        <td class="algin-center" colspan="36">
            <p class="font-18"><b>深圳市恒得利珠宝有限公司</b></p>
            <p class="font-14">货品核价入库单</p>
        </td>
    </tr>
    <tr>
        <td class="algin-left" colspan="6">
                <div class="one">
                    <span>供应商：</span>
                    <span contenteditable="true"><?= $model->supplier->supplier_name ?? "无"; ?></span>
                </div>
        </td>
        <td class="algin-left" colspan="23">
                <div class="two">
                    <span>采购订单号：</span>
                    <span contenteditable="true">无</span>
                </div>
        </td>
        <td class="algin-left" colspan="7">
                <div class="three">
                    <span>金价/g：</span>
                    <span contenteditable="true"><?= $total['gold_price'] ?? '无' ?></span>
                </div>
        </td>
    </tr>
    <tr>
        <td class="algin-left" colspan="6">
                <div class="one">
                    <span>订单类型：</span>
                    <span contenteditable="true">成品采购</span>
                </div>
        </td>
        <td class="algin-left" colspan="23">
                <div class="two">
                    <span>销售渠道：</span>
                    <span contenteditable="true"><?= $total['channel'] ?? "无"; ?></span>
                </div>
        </td>
        <td class="algin-left" colspan="7">
                <div class="three">
                    <span>工厂结算单号：</span>
                    <span contenteditable="true"><?= $model->send_goods_sn ?? "无" ?></span>
                </div>
        </td>
    </tr>
    <tr>
        <td class="algin-left" colspan="6">
                <div class="one">
                    <span>入库单号：</span>
                    <span contenteditable="true"><?= $model->bill_no ?? "无" ?></span>
                </div>
        </td>
        <td class="algin-left" colspan="23">
                <div class="two">
                    <span>入库方法：</span>
                    <span contenteditable="true">无</span>
                </div>
        </td>
        <td class="algin-left" colspan="7">
                <div class="three">
                    <span>结价：</span>
                    <span contenteditable="true">无</span>
                </div>
        </td>       
    </tr>
    <tr>
        <td class="algin-left" colspan="6">
               
        </td>
        <td class="algin-left" colspan="23">
                
        </td>
        <td class="algin-left" colspan="7">
            <div class="three">
                <span>日期：</span>
                <span contenteditable="true"><?= date('Y/m/d', $model->created_at)?></span>
            </div>
        </td>       
    </tr>
</table>
<table border=1 style="border-collapse:collapse;margin-top: 5px">
            <thead>
            <td class="width-25 algin-center font-bold" rowspan="2">序号</td>
            <td class="width-80 algin-center font-bold" rowspan="2">条码号</td>
            <td class="width-60 algin-center font-bold" rowspan="2">款号</td>
            <td class="width-60 algin-center font-bold" rowspan="2">货品名称</td>
            <td class="width-20 algin-center font-bold" rowspan="2">渠道</td>
            <td class="width-30 algin-center font-bold" rowspan="2">材质</td>
            <td class="width-20 algin-center font-bold" rowspan="2">件数</td>
            <td class="width-20 algin-center font-bold" rowspan="2">手寸</td>
            <td class="width-160 algin-center font-bold" colspan="4">金料</td>
            <td class="width-170 algin-center font-bold" colspan="5">主石</td>
            <td class="width-170 algin-center font-bold" colspan="5">副石1</td>
            <td class="width-30 algin-center font-bold" rowspan="2">配件(g)</td>
            <td class="width-30 algin-center font-bold" rowspan="2">配件额</td>
            <td class="width-30 algin-center font-bold" rowspan="2">配件<br/>工费</td>
            <td class="width-30 algin-center font-bold" rowspan="2">工费</td>
            <td class="width-30 algin-center font-bold" rowspan="2">镶石费</td>
            <td class="width-30 algin-center font-bold" rowspan="2">车花片</td>
            <td class="width-30 algin-center font-bold" rowspan="2">分色/分件</td>
            <td class="width-30 algin-center font-bold" rowspan="2">补口费</td>
            <td class="width-30 algin-center font-bold" rowspan="2">版费</td>
<!--            <td class="width-35 algin-center font-bold" rowspan="2">证书号</td>-->
            <td class="width-30 algin-center font-bold" rowspan="2">税额</td>
            <td class="width-80 algin-center font-bold" colspan="2">工厂结算</td>
            <td class="width-50 algin-center font-bold" rowspan="2">成本单价</td>
            <td class="width-55 algin-center font-bold" rowspan="2">总成本<br/>金额</td>
            <tr class="algin-left">
                <td class="algin-center padding-5 font-bold">货重</td>
                <td class="algin-center padding-5 font-bold">净重</td>
                <td class="algin-center padding-5 font-bold">损耗</td>
<!--                <td class="algin-center padding-5 font-bold">含耗重</td>-->
                <td class="algin-center padding-5 font-bold">金料额</td>
                <td class="algin-center padding-5 font-bold">石号</td>
                <td class="algin-center padding-5 font-bold">粒数</td>
                <td class="algin-center padding-5 font-bold">石重</td>
                <td class="algin-center padding-5 font-bold">单价</td>
                <td class="algin-center padding-5 font-bold">金额</td>
                <td class="algin-center padding-5 font-bold">石号</td>
                <td class="algin-center padding-5 font-bold">粒数</td>
                <td class="algin-center padding-5 font-bold">石重</td>
                <td class="algin-center padding-5 font-bold">单价</td>
                <td class="algin-center padding-5 font-bold">金额</td>
                <td class="algin-center padding-5 font-bold">折足<br/>金料</td>
                <td class="algin-center padding-5 font-bold">金额</td>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach ($lists as $key => $val) {
                //$pagesize = 10;
            ?>
                <tr class="algin-left">
                    <td class="algin-center padding-5"><?= $key + 1 ?></td>
                    <td class="algin-center padding-5"><?= $val['goods_id'] ?? "/" ?></td>
                    <td class="algin-center padding-5"><?= $val['style_sn'] ?? "/" ?></td>
                    <td class="algin-center padding-5"><?= $val['goods_name'] ?? "/" ?></td>
                    <td class="algin-center padding-5"><?= $val['channel_code'] ?? "/" ?></td>
                    <td class="algin-center padding-5"><?= $val['material_type'] ?? "/" ?></td>
                    <td class="algin-center padding-5"><?= $val['goods_num'] ?? "0" ?></td>
                    <td class="algin-center padding-5"><?= $val['finger'] ?? "/" ?></td>
                    <!-- 金料-->
                    <td class="algin-center padding-5"><?= floatval($val['suttle_weight']) ?? "0" ?></td>
                    <td class="algin-center padding-5"><?= floatval($val['gold_weight']) ?? "0" ?></td>
                    <td class="algin-center padding-5"><?= floatval($val['gold_loss']) ?? "0" ?></td>
<!--                    <td class="algin-center padding-5">--><?//= $val['lncl_loss_weight'] ?? "0" ?><!--</td>-->
                    <td class="algin-center padding-5"><?= floatval($val['gold_amount']) ?? "0" ?></td>
                    <!-- 主石-->
                    <td class="algin-center padding-5"><?= $val['main_stone_sn'] ?? "/" ?></td>
                    <td class="algin-center padding-5"><?= $val['main_stone_num'] ?? "0" ?></td>
                    <td class="algin-center padding-5"><?= floatval($val['main_stone_weight']) ?? "0" ?></td>
                    <td class="algin-center padding-5"><?= floatval($val['main_stone_price']) ?? "0" ?></td>
                    <td class="algin-center padding-5"><?= floatval($val['main_stone_amount']) ?? "0" ?></td>
                    <!-- 副石1-->
                    <td class="algin-center padding-5"><?= $val['second_stone_sn1'] ?? "/" ?></td>
                    <td class="algin-center padding-5"><?= $val['second_stone_num1'] ?? "0" ?></td>
                    <td class="algin-center padding-5"><?= floatval($val['second_stone_weight1']) ?? "0" ?></td>
                    <td class="algin-center padding-5"><?= floatval($val['second_stone_price1']) ?? "0" ?></td>
                    <td class="algin-center padding-5"><?= floatval($val['second_stone_amount1']) ?? "0" ?></td>
                    <!-- 配件-->
                    <td class="algin-center padding-5"><?= floatval($val['parts_gold_weight']) ?? "0" ?></td>
                    <td class="algin-center padding-5"><?= floatval($val['parts_amount']) ?? "0" ?></td>
                    <td class="algin-center padding-5"><?= floatval($val['parts_fee']) ?? "0" ?></td>
                    <!-- 工费-->
                    <td class="algin-center padding-5"><?= floatval($val['basic_gong_fee']) ?? "0" ?></td>
                    <td class="algin-center padding-5"><?= floatval($val['xianqian_fee']) ?? "0" ?></td>
                    <td class="algin-center padding-5"><?= floatval($val['biaomiangongyi_fee']) ?? "0" ?></td>
                    <td class="algin-center padding-5"><?= floatval($val['fense_fee']) ?? "0" ?></td>
                    <td class="algin-center padding-5"><?= floatval($val['bukou_fee']) ?? "0" ?></td>
                    <td class="algin-center padding-5"><?= floatval($val['templet_fee']) ?? "0" ?></td>
<!--                    <td class="algin-center padding-5">--><?//= $val['cert_id'] ?? "" ?><!--</td>-->
                    <td class="algin-center padding-5"><?= floatval($val['tax_amount']) ?? "0" ?></td>
                    <td class="algin-center padding-5"><?= floatval($val['pure_gold']) ?? "0" ?></td>
                    <td class="algin-center padding-5"><?= floatval($val['factory_cost']) ?? "0" ?></td>
                    <td class="algin-center padding-5"><?= floatval(bcdiv($val['cost_price'], $val['goods_num'],3)) ?></td>
                    <td class="algin-center padding-5"><?= floatval($val['cost_price']) ?? "0" ?></td>
                </tr>
                </tbody>
            <?php
            }
            ?>
            <tfoot>
            <tr>
                <td class="algin-center padding-5" colspan="6">合计</td>
                <td class="algin-center padding-5"><?= $total['goods_num'] ?? 0; ?></td>
                <td class="algin-center padding-5">/</td>
                <td class="algin-center padding-5"><?= floatval($total['suttle_weight']) ?? '0.00'; ?></td>
                <td class="algin-center padding-5"><?= floatval($total['gold_weight']) ?? '0.00'; ?></td>
                <td class="algin-center padding-5">/</td>
<!--                <td class="algin-center padding-5">--><?//= $total['lncl_loss_weight'] ?? '0.00'; ?><!--</td>-->
                <td class="algin-center padding-5"><?= floatval($total['gold_amount']) ?? '0.00'; ?></td>
                <td class="algin-center padding-5">/</td>
                <td class="algin-center padding-5"><?= $total['main_stone_num'] ?? 0; ?></td>
                <td class="algin-center padding-5"><?= floatval($total['main_stone_weight']) ?? '0.00'; ?></td>
                <td class="algin-center padding-5">/</td>
                <td class="algin-center padding-5"><?= floatval($total['main_stone_amount']) ?? '0.00'; ?></td>
                <td class="algin-center padding-5">/</td>
                <td class="algin-center padding-5"><?= $total['second_stone_num1'] ?? 0; ?></td>
                <td class="algin-center padding-5"><?= floatval($total['second_stone_weight1']) ?? '0.00'; ?></td>
                <td class="algin-center padding-5">/</td>
                <td class="algin-center padding-5"><?= floatval($total['second_stone_amount1']) ?? '0.00'; ?></td>
                <td class="algin-center padding-5"><?= floatval($total['parts_gold_weight']) ?? '0.00'; ?></td>
                <td class="algin-center padding-5"><?= floatval($total['parts_amount']) ?? '0.00'; ?></td>
                <td class="algin-center padding-5"><?= floatval($total['parts_fee']) ?? '0.00'; ?></td>
                <td class="algin-center padding-5"><?= floatval($total['basic_gong_fee']) ?? '0.00'; ?></td>
                <td class="algin-center padding-5"><?= floatval($total['xianqian_fee']) ?? '0.00'; ?></td>
                <td class="algin-center padding-5"><?= floatval($total['biaomiangongyi_fee']) ?? '0.00'; ?></td>
                <td class="algin-center padding-5"><?= floatval($total['fense_fee']) ?? '0.00'; ?></td>
                <td class="algin-center padding-5"><?= floatval($total['bukou_fee']) ?? '0.00'; ?></td>
                <td class="algin-center padding-5"><?= floatval($total['templet_fee']) ?? '0.00'; ?></td>
<!--                <td class="algin-center padding-5"></td>-->
                <td class="algin-center padding-5"><?= floatval($total['tax_amount']) ?? '0.00'; ?></td>
                <td class="algin-center padding-5"><?= floatval($total['pure_gold']) ?? '0.00'; ?></td>
                <td class="algin-center padding-5"><?= floatval($total['factory_cost']) ?? '0.00'; ?></td>
                <td class="algin-center padding-5"><?= floatval($total['one_cost_price']) ?? '0.00'; ?></td>
                <td class="algin-center padding-5"><?= floatval($total['cost_price']) ?? '0.00'; ?></td>
            </tr>
            </tfoot>
        </table>
<table class="footerTable" style="padding-top: 10px">
    <tr>
        <td class="algin-left" colspan="6">
            <div class="prepared-by">
                <span>制单人：</span>
                <span contenteditable="true"><?= $model->creator->username ?? ''; ?></span>
            </div>            
        </td>
        <td class="algin-left" colspan="23">
           <div class="reviewer">
                <span>复核人：</span>
                <span contenteditable="true"></span>
            </div>            
        </td>
        <td class="algin-left" colspan="7">
            <div class="review">
                <span>审核人：</span>
                <span contenteditable="true"><?= $model->auditor->username ?? ''; ?></span>
            </div>
        </td>
    </tr>    
</table>
</body>
</html>
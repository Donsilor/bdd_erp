<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>货品出库单</title>
<!--        <link href="/backend/resources/css/print.css" rel="stylesheet">-->
		<script language="javascript">
			function preview(fang) {
				if (fang < 10) {
					bdhtml = window.document.body.innerHTML; //获取当前页的html代码
					sprnstr = "<!--startprint" + fang + "-->"; //设置打印开始区域
					eprnstr = "<!--endprint" + fang + "-->"; //设置打印结束区域
					prnhtml = bdhtml.substring(bdhtml.indexOf(sprnstr) + 18); //从开始代码向后取html
					prnhtml = prnhtml.substring(0, prnhtml.indexOf(eprnstr)); //从结束代码向前取html
					window.document.body.innerHTML = prnhtml;
					window.print();
					window.document.body.innerHTML = bdhtml;
				} else {
					window.print();
				}
			}
		</script>
        <style>
            body{
                font-family:initial; /*浏览器打印不出div背景颜色*/
                -webkit-print-color-adjust: exact;
            }
            .information{
                width: 100%;
            }
            td{
                text-align: left;
                padding:0 5px;
            }
            .midd{
                height: 10px;
            }
            .msg{
                display: flex;
                justify-content: space-between;
                text-align: left;
                font-weight: bold;
            }
            .top{
                width: 83%;
            }
            .bottom{
                width: 17%;
            }
            .height{
                height: 20px;
            }
            thead tr th,thead tr td,tfoot tr td{
                background-color: #808080!important;
            }
            .one,.two,.three{
                line-height: 20px;
            }
            span{
                display: inline-block;
            }
            .template{
                width: 100%;
                text-align: center;
                display: flex;
                justify-content: center;
                padding: 20px 0;
                font-family:'Times New Roman', Times;
                font-size: 14px;
                color:#000;
            }
            .information{
                width: 100%;
            }
            table{
                table-layout: fixed;
            }
            td{
                /* word-break: break-all;  */
                word-wrap:break-word;
            }
            p{
                margin: 0!important;
            }
            .address{
                margin: 6px 0;
            }
            /* span{
                font-size: 14px;
            } */
            .total{
                margin: 8px 0;
            }
            .shipping-address{
                line-height: 60px;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 5px;
            }
            table,table tr th, table tr td {
                border:1px solid #000;
                font-size: 12px;
            }
            table tr th{
                /* padding: 10px 20px; */
                text-align: center;
            }
            .foot-info{
                display: flex;
                justify-content: space-between;
                text-align: left;
                margin-top: 5px;
            }
            .prepared-by{
                width: 83%;
            }
            .reviewer{
                width: 17%;
            }

            /* 输入框去掉边框和外边框 */
            input{
                border:none;
                outline: none;
                font-size: 14px;
            }

            /* 字体加粗 */
            .font-bold{
                font-weight: bold;
            }
            /* 文本位置 */
            .algin-center{
                text-align: center;
            }
            .algin-left{
                text-align: left;
            }
            .algin-right{
                text-align: right;
            }
            .img-center{
                justify-content: center;
                align-items: center;
            }
            /* 字体大小 */
            .font-24{
                font-size: 24px;
            }
            .font-22{
                font-size: 22px;
            }
            .font-20{
                font-size: 20px;
            }
            .font-18{
                font-size: 18px;
            }
            .font-16{
                font-size: 16px;
            }
            .font-14{
                font-size: 14px;
            }
            .font-12{
                font-size: 12px;
            }
            /* 宽度 */
            .width-200{
                width: 200px;
            }
            .width-190{
                width: 190px;
            }
            .width-180{
                width: 180px;
            }
            .width-170{
                width: 170px;
            }
            .width-160{
                width: 160px;
            }
            .width-150{
                width: 150px;
            }
            .width-140{
                width: 140px;
            }
            .width-125{
                width: 125px;
            }
            .width-120{
                width: 120px;
            }
            .width-100{
                width: 100px;
            }
            .width-97{
                width: 97px;
            }
            .width-90{
                width: 90px;
            }
            .width-80{
                width: 80px;
            }
            .width-65{
                width: 65px;
            }
            .width-60{
                width: 60px;
            }
            .width-50{
                width: 50px;
            }
            .width-40{
                width: 40px;
            }
            .width-35{
                width: 35px;
            }
            .width-30{
                width: 30px;
            }
            .width-25{
                width: 20px;
            }
            .width-20{
                width: 20px;
            }
            /* 行高 */
            .line-height-20{
                line-height: 20px;
            }
            /* 边距 */
            .margin-top-20{
                margin-top: 15px;
            }
            .margin-top-55{
                margin-top: 55px;
            }
            .padding-5{
                padding: 3px;
            }
            .padding-10{
                padding:0px 10px;
            }
            .padding-14{
                padding:0px 14px;
            }
        </style>
        <style media="print">
            .Noprint {   DISPLAY:   none;}
            .PageNext {   PAGE-BREAK-AFTER:   always   }
        </style>
	</head>
    <div class="text-center Noprint" style="text-align:right;">
        <!-- 打印按钮 -->
        <button type="button" class="btn btn-info btn-ms" target="_blank" onclick="preview(10)">打印</button>
    </div>
    <body>
    <div class="template">
        <div class="information">
            <p class="font-bold font-18"><?= date('Y', time()) ?>年深圳市恒得利珠宝有限公司</p>
            <p class="font-bold font-16">货品出库单</p>
            <div class="midd"></div>
            <div class="msg">
                <div class="top">
                    <div class="one"><span >销售渠道：</span><span contenteditable="true"><?= $model->saleChannel->name ?? "无"; ?></span></div>
                    <div class="one"><span >出库单号：</span><span contenteditable="true"><?= $model->bill_no ?? "无"; ?></span></div>
                </div>
                <div class="bottom">
                    <div class="three"><span >制单日期：</span><span contenteditable="true"><?= $model->created_at ? date('Y/m/d', $model->created_at) : "无"; ?></span></div>
                    <div class="three"><span >出库日期：</span><span contenteditable="true"><?= $model->audit_time ? date('Y/m/d', $model->audit_time) : "无"; ?></span></div>
                </div>
            </div>
            <table class="table">
                <thead>
                <th class="width-30 algin-center font-bold">序号</th>
                <th class="width-80 algin-center font-bold" >品类</th>
                <th class="width-150 algin-center font-bold" >货品名称</th>
                <th class="width-140 algin-center font-bold" >条码号</th>
                <th class="width-120 algin-center font-bold" >款号</th>
                <th class="width-30 algin-center font-bold" >数量</th>
                <th class="width-50 algin-center font-bold" >尺寸</th>
                <th class="width-65 algin-center font-bold" >总石重(ct)</th>
                <th class="width-65 algin-center font-bold" >总连石重</th>
                <th class="width-65 algin-center font-bold" >标签价</th>
                <th class="width-50 algin-center font-bold" >销售价</th>
                <th class="width-65 algin-center font-bold" >证书号</th>
                <th class="width-65 algin-center font-bold" >备注</th>
                </thead>
                <?php
                foreach ($lists as $key => $val) {
                //$pagesize = 10;
                ?>
                <tbody>
                <tr class="algin-left">
                    <td class="algin-center padding-5"><?= $key + 1 ?></td>
                    <td class="algin-center padding-5"><?= $val['style_cate_name'] ?? "/" ?></td>
                    <td class="algin-center padding-5"><?= $val['goods_name'] ?? "/" ?></td>
                    <td class="algin-center padding-5"><?= $val['goods_id'] ?? "/" ?></td>
                    <td class="algin-center padding-5"><?= $val['style_sn'] ?? "/" ?></td>
                    <td class="algin-center padding-5"><?= $val['goods_num'] ?? "0" ?></td>
                    <td class="algin-center padding-5"><?= $val['product_size'] ?? "/" ?></td>
                    <td class="algin-center padding-5"><?= floatval($val['cart']) ?? "0.00" ?></td>
                    <td class="algin-center padding-5"><?= floatval($val['suttle_weight']) ?? "0.00" ?></td>
                    <td class="algin-center padding-5"><?= floatval($val['market_price']) ?? "0.00" ?></td>
                    <td class="algin-center padding-5" contenteditable="true">无</td>
                    <td class="algin-center padding-5"><?= $val['cert_id'] ?? "/" ?></td>
                    <td class="algin-center padding-5"><?= $val['remark'] ?? "/" ?></td>
                </tr>
                </tbody>
                <?php
                }
                ?>
                <tfoot>
                <tr>
                    <td class="algin-center padding-5" colspan="5">合计</td>
                    <td class="algin-center padding-5"><?= floatval($total['goods_num']) ?? '0.00'; ?></td>
                    <td class="algin-center padding-5">/</td>
                    <td class="algin-center padding-5"><?= floatval($total['cart']) ?? '0.00'; ?></td>
                    <td class="algin-center padding-5"><?= floatval($total['suttle_weight']) ?? '0.00'; ?></td>
                    <td class="algin-center padding-5"><?= floatval($total['market_price']) ?? '0.00'; ?></td>
                    <td class="algin-center padding-5">无</td>
                    <td class="algin-center padding-5">/</td>
                    <td class="algin-center padding-5">/</td>
                </tr>
                </tfoot>
            </table>
            <div class="foot-info">
                <div class="prepared-by font-bold"><span >制单人：</span><span contenteditable="true"><?= $model->creator->username ?? '无'; ?></span></div>
                <div class="reviewer font-bold"><span >收货人：</span><span contenteditable="true"><?= $model->salesman->username ?? '无'; ?></span></div>
            </div>
        </div>
    </div>
    <div class="text-center Noprint" style="text-align:center;">
        <!-- 打印按钮 -->
        <button type="button" class="btn btn-info btn-ms" onclick="preview(10)">打印</button>
    </div>
    </body>
</html>

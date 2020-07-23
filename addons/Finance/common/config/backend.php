<?php

return [

    // ----------------------- 菜单配置 ----------------------- //
    'config' => [
        // 菜单配置
        'menu' => [
            'location' => 'default', // default:系统顶部菜单;addons:应用中心菜单
            'icon' => 'fa fa-puzzle-piece',
        ],
        // 子模块配置
        'modules' => [
        ],
    ],

    // ----------------------- 快捷入口 ----------------------- //

    'cover' => [

    ],

    // ----------------------- 菜单配置 ----------------------- //

    'menu' => [
        [
            'title' => '财务类SZ',
            'route' => 'indexFinance',
            'icon' => 'fa fa-superpowers',
            'child' => [
                [
                    'title' => '银行支付单',
                    'route' => 'bank-payment/index',
                ],
                [
                    'title' => '合同款支付审批单',
                    'route' => 'purchase/index',
                ],
                [
                    'title' => '采购收货单',
                    'route' => 'receipt/index',
                ],
                [
                    'title' => '不良返厂单',
                    'route' => 'defective/index',
                ],
                [
                    'title' => '质检列表',
                    'route' => 'receipt-goods/iqc-index',
                ],
            ],

        ],
    ],

    // ----------------------- 权限配置 ----------------------- //

    'authItem' => [
        [
            'title' => '所有权限',
            'name' => '*',
        ],
    ],
];
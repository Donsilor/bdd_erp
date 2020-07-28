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
                    'route' => 'bank-pay/index',
                ],
                [
                    'title' => '合同款项支付审批单',
                    'route' => 'contract-pay/index',
                ],
                [
                    'title' => '个人因公借款审批单',
                    'route' => 'borrow-pay/index',
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
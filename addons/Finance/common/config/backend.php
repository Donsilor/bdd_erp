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
                'title' => '财务订单',
                'route' => 'IndexOrderFinance',
                'icon' => 'fa fa-superpowers',
                'child' => [
                        [
                                'title' => '订单点款',
                                'route' => 'order-pay/index',
                        ],
                        [
                                'title' => '财务出库单',
                                'route' => 'finance-sale/index',
                        ],
                        [
                                'title' => '财务入库单',
                                'route' => 'finance-entry/index',
                        ],
                        [
                            'title' => '财务销售明细单',
                            'route' => 'sales-detail/index',
                        ],
                ],
                    
        ],
        [
                'title' => '财务审批',
                'route' => 'indexFlowFinance',
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
        [
                'title' => '功能配置',
                'route' => 'indexConfigFinance',
                'icon' => 'fa fa-superpowers',
                'child' => [
                    [
                        'title' => '结账账期管理',
                        'route' => 'accounts-date/index',
                    ]
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
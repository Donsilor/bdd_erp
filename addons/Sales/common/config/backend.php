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
            'title' => '订单管理',
            'route' => 'indexOrder',
            'icon' => 'fa fa-superpowers',
            'child' => [
                [
                    'title' => '订单列表',
                    'route' => 'order/index',
                ],
                [
                    'title' => '待配货订单',
                    'route' => 'distribution/index',
                ],
                [
                    'title' => '订单发货质检',
                    'route' => 'order-fqc/index',
                ],
                [
                    'title' => '质检问题列表',
                    'route' => 'order-fqc-list/index',
                ],
            ],

        ],
        [
            'title' => '客户管理',
            'route' => 'indexCustomer',
            'icon' => 'fa fa-superpowers',
            'child' => [
                [
                    'title' => '客户列表',
                    'route' => 'customer/index',
                ],
            ],

        ],
        [
            'title' => '物流管理',
            'route' => 'indexShipping',
            'icon' => 'fa fa-superpowers',
            'child' => [
                [
                    'title' => '待发货订单',
                    'route' => 'shipping/index',
                ],
                [
                    'title' => '快递单列表',
                    'route' => 'freight/index',
                ],
            ],

        ],
        [
            'title' => '退款管理',
            'route' => 'indexReturn',
            'icon' => 'fa fa-superpowers',
            'child' => [
                [
                    'title' => '退款列表',
                    'route' => 'return/index',
                ],
                [
                    'title' => '主管审核',
                    'route' => 'return/leader',
                ],
                [
                    'title' => '商品部审核',
                    'route' => 'return/storekeeper',
                ],
                [
                    'title' => '财务审核',
                    'route' => 'return/finance',
                ],
            ],

        ],
        [
            'title' => '功能配置',
            'route' => 'indexConfig',
            'icon' => 'fa fa-superpowers',
            'child' => [
                [
                    'title' => '销售渠道',
                    'route' => 'sale-channel/index',
                ],
                [
                    'title' => '客户来源',
                    'route' => 'customer-sources/index',
                ],
                [
                    'title' => '快递公司',
                    'route' => 'express/index',
                ],
                [
                    'title' => '支付方式',
                    'route' => 'payment/index',
                ],
                [
                    'title' => '质检配置',
                    'route' => 'fqc-config/index',
                ],
                [
                    'title' => '货币汇率',
                    'route' => 'currency/index',
                ],
                [
                    'title' => '退款原因',
                    'route' => 'return-config/index',
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
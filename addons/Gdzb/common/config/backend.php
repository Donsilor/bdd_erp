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
                    ],
                    
            ],
            [
                'title' => '供应商',
                'route' => 'indexOrder',
                'icon' => 'fa fa-superpowers',
                'child' => [
                    [
                        'title' => '供应商列表',
                        'route' => 'supplier/index',
                    ],
                ],

            ],
            [
                    'title' => '客户管理',
                    'route' => 'indexOrder',
                    'icon' => 'fa fa-superpowers',
                    'child' => [
                            [
                                    'title' => '客户列表',
                                    'route' => 'customer/index',
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
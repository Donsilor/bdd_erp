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
                    'title' => '生产管理',
                    'route' => 'indexStyle',
                    'icon' => 'fa fa-superpowers',
                    'child' => [
                            [
                                    'title' => '布产列表',
                                    'route' => 'produce/index',
                            ],                            
                    ],
                    
            ],
            [
                    'title' => '功能配置',
                    'route' => 'indexStyleConfig',
                    'icon' => 'fa fa-superpowers',
                    'child' => [
                            [
                                    'title' => '供应商',
                                    'route' => 'supplier/index',
                            ],
                            [
                                    'title' => '跟单人',
                                    'route' => 'follower/index',
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
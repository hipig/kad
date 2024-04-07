<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $menus = [
            [
                'name' => '仪表盘',
                'key' => 'workplace.dashboard',
                'path' => 'workplace/dashboard',
                'icon' => 'icon-dashboard',
            ],
            [
                'name' => '用户管理',
                'key' => 'user',
                'path' => '',
                'icon' => 'icon-user',
                'children' => [
                    [
                        'name' => '用户列表',
                        'key' => 'user.index',
                        'path' => 'user/index',
                    ],
                    [
                        'name' => '用户举报',
                        'key' => 'user.report',
                        'path' => 'user/report',
                    ],
                ]
            ],
            [
                'name' => '聊天管理',
                'key' => 'chat',
                'path' => '',
                'icon' => 'icon-message',
                'children' => [
                    [
                        'name' => '单聊记录',
                        'key' => 'chat-message.index',
                        'path' => 'chat-message/index',
                    ],
                    [
                        'name' => '群聊记录',
                        'key' => 'chat-group.message',
                        'path' => 'chat-group/message',
                    ],
                    [
                        'name' => '群组列表',
                        'key' => 'chat-group.index',
                        'path' => 'chat-group/index',
                    ]
                ]
            ],
            [
                'name' => '动态管理',
                'key' => 'post',
                'path' => '',
                'icon' => 'icon-at',
                'children' => [
                    [
                        'name' => '动态列表',
                        'key' => 'post.index',
                        'path' => 'post/index',
                    ],
                    [
                        'name' => '评论列表',
                        'key' => 'post.comment',
                        'path' => 'post/comment',
                    ],
                    [
                        'name' => '动态举报',
                        'key' => 'post.report',
                        'path' => 'post/report',
                    ]
                ]
            ]
        ];

        Menu::query()->truncate();
        foreach ($menus as $menu) {
            $this->createMenu($menu);
        }
    }

    public function createMenu($menuData, $parent = null)
    {
        $menu = Menu::create(collect($menuData)->except(['children'])->toArray());
        if (!is_null($parent)) {
            $menu->parent()->associate($parent);
        }
        $menu->save();

        if ($menuData['children'] ?? false) {
            foreach ($menuData['children'] as $childMenu) {
                $this->createMenu($childMenu, $menu);
            }
        }
    }

}

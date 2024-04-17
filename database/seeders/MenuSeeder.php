<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\Role;
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
                'type' => Menu::TYPE_MENU,
                'name' => '仪表盘',
                'key' => 'workplace.dashboard',
                'path' => 'workplace/dashboard',
                'icon' => 'icon-dashboard',
            ],
            [
                'type' => Menu::TYPE_MENU,
                'name' => '用户管理',
                'key' => 'user',
                'path' => '',
                'icon' => 'icon-user',
                'children' => [
                    [
                        'type' => Menu::TYPE_MENU,
                        'name' => '用户列表',
                        'key' => 'user.index',
                        'path' => 'user/index',
                    ],
                    [
                        'type' => Menu::TYPE_MENU,
                        'name' => '用户举报',
                        'key' => 'user.report',
                        'path' => 'user/report',
                    ],
                ]
            ],
            [
                'type' => Menu::TYPE_MENU,
                'name' => '聊天管理',
                'key' => 'chat',
                'path' => '',
                'icon' => 'icon-message',
                'children' => [
                    [
                        'type' => Menu::TYPE_MENU,
                        'name' => '单聊记录',
                        'key' => 'chat-message.index',
                        'path' => 'chat-message/index',
                    ],
                    [
                        'type' => Menu::TYPE_MENU,
                        'name' => '群组消息',
                        'key' => 'chat-group.message',
                        'path' => 'chat-group/message',
                    ],
                    [
                        'type' => Menu::TYPE_MENU,
                        'name' => '群组列表',
                        'key' => 'chat-group.index',
                        'path' => 'chat-group/index',
                    ]
                ]
            ],
            [
                'type' => Menu::TYPE_MENU,
                'name' => '动态管理',
                'key' => 'post',
                'path' => '',
                'icon' => 'icon-at',
                'children' => [
                    [
                        'type' => Menu::TYPE_MENU,
                        'name' => '动态列表',
                        'key' => 'post.index',
                        'path' => 'post/index',
                    ],
                    [
                        'type' => Menu::TYPE_MENU,
                        'name' => '评论列表',
                        'key' => 'post.comment',
                        'path' => 'post/comment',
                    ],
                    [
                        'type' => Menu::TYPE_MENU,
                        'name' => '动态举报',
                        'key' => 'post.report',
                        'path' => 'post/report',
                    ]
                ]
            ],
            [
                'type' => Menu::TYPE_MENU,
                'name' => '系统管理',
                'key' => 'system',
                'path' => '',
                'icon' => 'icon-settings',
                'role' => ['admin'],
                'children' => [
                    [
                        'type' => Menu::TYPE_MENU,
                        'name' => '管理员',
                        'key' => 'system.user',
                        'path' => 'system/user',
                        'role' => ['admin'],
                    ],
                    [
                        'type' => Menu::TYPE_MENU,
                        'name' => '角色管理',
                        'key' => 'system.role',
                        'path' => 'system/role',
                        'role' => ['admin'],
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
        $menu = Menu::updateOrCreate(['key' => $menuData['key']], collect($menuData)->except(['children', 'role'])->toArray());
        if (!is_null($parent)) {
            $menu->parent()->associate($parent);
        }
        $menu->save();

        $roleNames = $menuData['role'] ?? [];
        $roleIds = Role::query()->whereIn('name', $roleNames)->pluck('id')->toArray();
        $menu->roles()->sync($roleIds);

        if ($menuData['children'] ?? false) {
            foreach ($menuData['children'] as $childMenu) {
                $this->createMenu($childMenu, $menu);
            }
        }
    }

}

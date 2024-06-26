<?php

namespace App\Console\Commands\Admin;

use App\Models\AdminUser;
use App\Models\Role;
use Illuminate\Console\Command;

class CreateUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create-user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Admin user create';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $username = $this->ask('请输入用户名');
        $name = $this->ask('请输入昵称');
        $password = $this->secret('请输入密码');
        $roleName = $this->ask('请输入角色');

        $user = AdminUser::create([
            'username' => $username,
            'name' => $name,
            'password' => $password
        ]);

        $role = Role::query()->where('name', $roleName)->first();
        if ($role) {
            $role->users()->attach($user);
        }

        $this->info("创建用户 {$name} 成功");
    }
}

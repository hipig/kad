<?php

namespace App\Console\Commands;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:generate-token';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '快速为用户生成 token';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->ask('输入用户ID');

        $user = User::find($userId);

        if (!$user) {
            return $this->error('用户不存在');
        }

        $token = $user->createToken('User Generate Token');
        $model = $token->token;
        $model->expires_at = Carbon::now()->addDays(365);
        $model->save();

        $this->info($token->accessToken);
    }
}

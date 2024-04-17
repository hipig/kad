<?php

namespace App\Exports;

use App\ModelFilters\Admin\UserFilter;
use App\Models\User;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UserExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    public function __construct(
        protected array $requestData = []
    ) {}

    public function query()
    {
        return User::filter($this->requestData, UserFilter::class)->latest();
    }

    public function headings(): array
    {
        return [
            '昵称',
            '用户名',
            '钱包地址',
            '好友数',
            '关注数',
            '粉丝数',
            '群组数',
            '动态数',
            '注册时间',
            '状态',
        ];
    }

    public function map($row): array
    {
        return [
            $row->nickname,
            $row->username,
            $row->wallet_account,
            $row->friend_count,
            $row->following_count,
            $row->follower_count,
            $row->chat_group_count,
            $row->post_count,
            $row->created_at,
            $row->status_text,
        ];
    }

}

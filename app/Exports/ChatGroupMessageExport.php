<?php

namespace App\Exports;

use App\ModelFilters\Admin\ChatGroupMessageFilter;
use App\Models\ChatGroupMessage;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ChatGroupMessageExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    public function __construct(
        protected array $requestData = []
    ) {}

    public function query()
    {
        return ChatGroupMessage::filter($this->requestData, ChatGroupMessageFilter::class)->with(['group', 'user'])->latest();
    }

    public function headings(): array
    {
        return [
            '消息KEY',
            '群组',
            '用户',
            '消息内容',
            '状态',
            '发言时间',
        ];
    }

    public function map($row): array
    {
        return [
            $row->msg_seq,
            $row->group->name,
            $row->user->nickname,
            json_encode($row->body, JSON_UNESCAPED_UNICODE),
            $row->status_text,
            $row->created_at,
        ];
    }

}

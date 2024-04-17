<?php

namespace App\Exports;

use App\ModelFilters\Admin\ChatMessageFilter;
use App\Models\ChatMessage;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ChatMessageExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    public function __construct(
        protected array $requestData = []
    ) {}

    public function query()
    {
        return ChatMessage::filter($this->requestData, ChatMessageFilter::class)->with(['user', 'toUser'])->latest();
    }

    public function headings(): array
    {
        return [
            '消息KEY',
            '发送用户',
            '接收用户',
            '消息内容',
            '状态',
            '发言时间',
        ];
    }

    public function map($row): array
    {
        return [
            $row->msg_seq,
            $row->user->nickname,
            $row->toUser->nickname,
            json_encode($row->body, JSON_UNESCAPED_UNICODE),
            $row->status_text,
            $row->created_at,
        ];
    }

}

<?php

namespace App\Exports;

use App\ModelFilters\Admin\ChatGroupFilter;
use App\Models\ChatGroup;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ChatGroupExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    public function __construct(
        protected array $requestData = []
    ) {}

    public function query()
    {
        return ChatGroup::filter($this->requestData, ChatGroupFilter::class)->with(['owner'])->latest();
    }

    public function headings(): array
    {
        return [
            '群组KEY',
            '群组名称',
            '群成员数量',
            '群组类型',
            '群主',
            '状态',
            '发言时间',
        ];
    }

    public function map($row): array
    {
        return [
            $row->group_key,
            $row->name,
            $row->member_num,
            $row->type_text,
            $row->owner?->nickname,
            $row->status_text,
            $row->created_at,
        ];
    }

}

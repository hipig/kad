<?php

namespace App\Exports;

use App\ModelFilters\Admin\PostCommentFilter;
use App\Models\PostComment;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PostCommentExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    public function __construct(
        protected array $requestData = []
    ) {}

    public function query()
    {
        return PostComment::filter($this->requestData, PostCommentFilter::class)->with(['user', 'post'])->latest();
    }

    public function headings(): array
    {
        return [
            '评论用户',
            '评论内容',
            '动态内容',
            '回复数',
            '点赞数',
            '创建时间',
        ];
    }

    public function map($row): array
    {
        return [
            $row->user->nickname,
            $row->content,
            $row->post?->content,
            $row->comment_count,
            $row->like_count,
            $row->created_at,
        ];
    }

}

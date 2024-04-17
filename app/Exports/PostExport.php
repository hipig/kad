<?php

namespace App\Exports;

use App\ModelFilters\Admin\PostFilter;
use App\Models\Post;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PostExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    public function __construct(
        protected array $requestData = []
    ) {}

    public function query()
    {
        return Post::filter($this->requestData, PostFilter::class)->with(['user', 'images'])->latest();
    }

    public function headings(): array
    {
        return [
            '发布用户',
            '内容',
            '图片',
            '可见状态',
            '评论数',
            '收藏数',
            '点赞数',
            '创建时间',
        ];
    }

    public function map($row): array
    {
        $images = $row->images->pluck('url')->toArray() ?? [];
        return [
            $row->user->nickname,
            $row->content,
            implode(',', $images),
            $row->visible_status_text,
            $row->comment_count,
            $row->collect_count,
            $row->like_count,
            $row->created_at,
        ];
    }

}

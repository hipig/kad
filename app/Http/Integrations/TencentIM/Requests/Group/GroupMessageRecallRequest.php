<?php

namespace App\Http\Integrations\TencentIM\Requests\Group;

use App\Http\Integrations\TencentIM\Requests\BaseRequest;
use Saloon\Enums\Method;
use Saloon\Http\Request;

class GroupMessageRecallRequest extends BaseRequest
{
    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        return '/v4/group_open_http_svc/group_msg_recall';
    }

    protected function defaultBody(): array
    {
        return [
            'GroupId' => $this->groupId,
            'MsgSeqList' => $this->msgSeqList
        ];
    }

    public function __construct(
        public string $groupId,
        public array $msgSeqList
    ){}
}

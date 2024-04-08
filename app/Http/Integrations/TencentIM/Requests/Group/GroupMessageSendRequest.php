<?php

namespace App\Http\Integrations\TencentIM\Requests\Group;

use App\Http\Integrations\TencentIM\Requests\BaseRequest;
use Saloon\Enums\Method;
use Saloon\Http\Request;

class GroupMessageSendRequest extends BaseRequest
{
    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        return '/v4/group_open_http_svc/send_group_msg';
    }

    protected function defaultBody(): array
    {
        return [
            'GroupId' => $this->groupId,
            'MsgBody' => $this->msgBody,
            'Random' => $this->random,
            'OnlineOnlyFlag' => $this->onlineOnlyFlag
        ];
    }

    public function __construct(
        public string $groupId,
        public array $msgBody,
        public int $random,
        public int $onlineOnlyFlag
    ){}
}

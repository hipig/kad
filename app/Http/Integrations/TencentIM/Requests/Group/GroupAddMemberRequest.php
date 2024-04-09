<?php

namespace App\Http\Integrations\TencentIM\Requests\Group;

use App\Http\Integrations\TencentIM\Requests\BaseRequest;
use Saloon\Enums\Method;
use Saloon\Http\Request;

class GroupAddMemberRequest extends BaseRequest
{
    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        return '/v4/group_open_http_svc/add_group_member';
    }

    protected function defaultBody(): array
    {
        return [
            'GroupId' => $this->groupId,
            'MemberList' => $this->memberList,
            'Silence' => $this->silence
        ];
    }

    public function __construct(
        public string $groupId,
        public array $memberList,
        public int $silence
    ){}
}

<?php

namespace App\Http\Integrations\TencentIM\Requests\Group;

use App\Http\Integrations\TencentIM\Requests\BaseRequest;
use Saloon\Enums\Method;
use Saloon\Http\Request;

class GroupUpdateRequest extends BaseRequest
{
    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        return '/v4/group_open_http_svc/modify_group_base_info';
    }

    protected function defaultBody(): array
    {
        return [
            'GroupId' => $this->groupId,
            'Name' => $this->name,
            'Introduction' => $this->introduction,
            'Notification' => $this->notification,
        ];
    }

    public function __construct(
        public string $groupId,
        public string $name,
        public string $introduction,
        public string $notification,
    ){}
}

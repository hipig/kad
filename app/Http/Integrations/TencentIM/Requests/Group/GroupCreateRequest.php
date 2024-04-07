<?php

namespace App\Http\Integrations\TencentIM\Requests\Group;

use App\Http\Integrations\TencentIM\Requests\BaseRequest;
use Saloon\Enums\Method;
use Saloon\Http\Request;

class GroupCreateRequest extends BaseRequest
{
    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        return '/v4/group_open_http_svc/create_group';
    }

    protected function defaultBody(): array
    {
        $body = [
            'Name' => $this->name,
            'Type' => $this->type,
        ];

        if ($this->ownerAccount) {
            $body['Owner_Account'] = $this->ownerAccount;
        }

        return $body;
    }

    public function __construct(
        public string $name,
        public string $type,
        public $ownerAccount
    ){}
}

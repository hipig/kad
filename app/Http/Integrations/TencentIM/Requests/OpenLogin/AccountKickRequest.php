<?php

namespace App\Http\Integrations\TencentIM\Requests\OpenLogin;

use App\Http\Integrations\TencentIM\Requests\BaseRequest;
use Saloon\Enums\Method;
use Saloon\Http\Request;

class AccountKickRequest extends BaseRequest
{
    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        return '/v4/im_open_login_svc/kick';
    }

    protected function defaultBody(): array
    {
        return [
            'UserID' => $this->userId,
        ];
    }

    public function __construct(
        public string $userId,
    ){}
}

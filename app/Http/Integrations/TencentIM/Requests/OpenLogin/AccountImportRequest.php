<?php

namespace App\Http\Integrations\TencentIM\Requests\OpenLogin;

use App\Http\Integrations\TencentIM\Requests\BaseRequest;
use Saloon\Enums\Method;
use Saloon\Http\Request;

class AccountImportRequest extends BaseRequest
{
    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        return '/v4/im_open_login_svc/account_import';
    }

    protected function defaultBody(): array
    {
        return [
            'UserID' => $this->userId,
            'Nick' => $this->nickname,
            'FaceUrl' => $this->avatar,
        ];
    }

    public function __construct(
        public string $userId,
        public string $nickname,
        public string $avatar
    ){}
}

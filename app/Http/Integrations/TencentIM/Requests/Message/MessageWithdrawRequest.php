<?php

namespace App\Http\Integrations\TencentIM\Requests\Message;

use App\Http\Integrations\TencentIM\Requests\BaseRequest;
use Saloon\Enums\Method;
use Saloon\Http\Request;

class MessageWithdrawRequest extends BaseRequest
{
    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        return '/v4/openim/admin_msgwithdraw';
    }

    protected function defaultBody(): array
    {
        return [
            'From_Account' => $this->fromAccount,
            'To_Account' => $this->toAccount,
            'MsgKey' => $this->msgKey
        ];
    }

    public function __construct(
        public string $fromAccount,
        public string $toAccount,
        public string $msgKey
    ){}
}

<?php

namespace App\Http\Integrations\TencentIM\Requests\Message;

use App\Http\Integrations\TencentIM\Requests\BaseRequest;
use Saloon\Enums\Method;
use Saloon\Http\Request;

class MessageSendRequest extends BaseRequest
{
    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        return '/v4/openim/sendmsg';
    }

    protected function defaultBody(): array
    {
        return [
            'From_Account' => $this->fromAccount,
            'To_Account' => $this->toAccount,
            'MsgBody' => $this->msgBody,
            'MsgRandom' => $this->random
        ];
    }

    public function __construct(
        public string $fromAccount,
        public string $toAccount,
        public array $msgBody,
        public int $random
    ){}
}

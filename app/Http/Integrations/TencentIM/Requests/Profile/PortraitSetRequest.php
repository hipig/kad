<?php

namespace App\Http\Integrations\TencentIM\Requests\Profile;

use App\Http\Integrations\TencentIM\Requests\BaseRequest;
use Saloon\Enums\Method;
use Saloon\Http\Request;

class PortraitSetRequest extends BaseRequest
{
    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        return '/v4/profile/portrait_set';
    }

    protected function defaultBody(): array
    {
        return [
            'From_Account' => $this->username,
            'ProfileItem' => $this->items
        ];
    }

    public function __construct(
        public string $username,
        public array $items
    ){}
}

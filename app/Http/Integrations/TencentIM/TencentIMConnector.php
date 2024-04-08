<?php

namespace App\Http\Integrations\TencentIM;

use Saloon\Http\Connector;
use Saloon\Http\Response;
use Saloon\Traits\Plugins\AcceptsJson;
use Saloon\Traits\Plugins\AlwaysThrowOnErrors;

class TencentIMConnector extends Connector
{
    use AcceptsJson, AlwaysThrowOnErrors;

    /**
     * The Base URL of the API
     */
    public function resolveBaseUrl(): string
    {
        return config('im.domain');
    }

    protected function defaultQuery(): array
    {
        return [
            'sdkappid' => config('im.appid'),
            'identifier' => config('im.administrator_userid'),
            'usersig' => config('im.administrator_usersig'),
            'random' => random_int(0, 4294967295),
            'contenttype' => 'json'
        ];
    }

    public function hasRequestFailed(Response $response): ?bool
    {
        return $response->json('ActionStatus') === 'FAIL';
    }
}

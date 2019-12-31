<?php

namespace Apps\Components;

use JsonRPC\Client;

class Api
{
    /** @var string */
    private $url;

    public function __construct(string $url)
    {
        $this->url = $url;
    }

    public function create(string $url) :array
    {
        $client = new Client($this->url);

        $result = $client->execute('url-create', [
            'url' => $url
        ]);

        if (!isset($result['url'])) {

            return [
                'status' => 'error',
                'error' => $result,
            ];
        }

        return [
            'status' => 'success',
            'url' => $result['url'],
        ];
    }
}